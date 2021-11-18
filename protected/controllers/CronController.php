<?php
class CronController extends CController
{
	public function init()
	{			
		 // set website timezone
		 $website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");	 		 
		 if (!empty($website_timezone)){		 	
		 	Yii::app()->timeZone=$website_timezone;
		 }		 				 
	}
	
	public function actionIndex()
	{		
		
	}
	
	public function actionprocesspush()
	{
		dump("cron start..");
		define('LOCK_SUFFIX', CHANNEL_ID.'_processpush');		
		if(($pid = cronHelper::lock()) !== FALSE):

		$stmt="
		SELECT a.*,
		
		(
		 select option_value
		 from {{option}}
		 where		 		 
		 option_name = 'services_json'
		 limit 0,1
		) as services_account_json,
		
		(
		 select option_value
		 from {{option}}
		 where		 		 
		 option_name = 'customer_timezone'
		 AND
		 customer_id = a.customer_id
		 limit 0,1
		) as timezone,
		
		b.app_version
		
		FROM {{driver_pushlog}} a
		LEFT JOIN {{driver}} b
		ON
		a.driver_id = b.driver_id
		
		WHERE a.status='pending'
		ORDER BY a.date_created ASC
		LIMIT 0,20
		";		
		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			 $file = AdminFunctions::uploadPath()."/".$res[0]['services_account_json'];				 
			 foreach ($res as $val) {
			 	
			  $timezone = isset($val['timezone'])?$val['timezone']:'';
			  if(!empty($timezone)){
			  	  Yii::app()->timeZone=$timezone;
			  }
			  
		   	  $process_status=''; $json_response='';
			  $process_date = AdminFunctions::dateNow();				
			  $device_id = trim($val['device_id']);		
		   	  
		   	     try {		    		
	    			$json_response = FcmWrapper::ServiceAccount($file,CHANNEL_ID.'_fcm')
					->setTarget($device_id)
					->setTitle($val['push_title'])
					->setBody($val['push_message'])
					->setChannel(CHANNEL_ID)
					->setSound(CHANNEL_SOUNDNAME)
					->setAppleSound(CHANNEL_SOUNDFILE)
					->setBadge(1)
					->setForeground("true")
					->prepare()
					->send();						
					$process_status = 'process';
	    		} catch (Exception $e) {
	    			$process_status = 'failed';
					$json_response = $e->getMessage();						
				}			
								
				if(!empty($process_status)){
		   	  	   $process_status=substr( strip_tags($process_status) ,0,255);
		   	    } else $process_status='failed';	
		   	    
		   	    if(is_array($json_response) && count($json_response)>=1){
		   	    	$json_response = json_encode($json_response);
		   	    } 
		   	    
		   	    $params = array(
				  'status'=>$process_status,
				  'date_process'=>$process_date,
				  'json_response'=>$json_response
				);		
								
				Yii::app()->db->createCommand()->update("{{driver_pushlog}}",$params,
		  	    'push_id=:push_id',
			  	    array(
			  	      ':push_id'=>(integer)$val['push_id']
			  	    )
		  	    );
		   	  
		   } //end foreach
		}
		
		cronHelper::unlock();
		endif;	
		dump("cron end..");
	}
	
    public function actionAutoAssign()
    {
        
    	dump("cron start..");
        define('LOCK_SUFFIX', CHANNEL_ID.'_autoassign');        
        if(($pid = cronHelper::lock()) !== FALSE):
        
		$distance_exp=3959;  $radius=3000;			
		$driver_assign_request_expire = 10;
		
		$timezone = Yii::app()->timeZone;
		$date_now=date('Y-m-d'); 

		$stmt="SELECT a.*,
		b.enabled_auto_assign,
		b.include_offline_driver,
		b.autoassign_notify_email,
		b.request_expire,
		b.auto_assign_type,
		b.assign_request_expire,
		b.driver_assign_radius
		
		 FROM
		{{driver_task}} a
		LEFT JOIN {{customer}} b
        ON
        a.customer_id=b.customer_id
        
		WHERE 1
		
		AND a.status IN ('unassigned')  
		AND a.auto_assign_type=''
		AND a.delivery_date like '$date_now%'	
		AND b.enabled_auto_assign='1'	
		ORDER BY task_id ASC
		LIMIT 0,100
		";
				
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {
				
				Driver::setCustomerTimezone( $val['customer_id'] );    	
				
				
				if($val['driver_assign_radius']>0){
					$radius=$val['driver_assign_radius'];
				}
				
				$notify_email=$val['autoassign_notify_email'];
				
				$lat=$val['task_lat'];
				$lng=$val['task_lng'];
				$task_id=$val['task_id'];		
								
				$and='';
				$todays_date=date('Y-m-d');			
		        $time_now = time() - 600;
		        
		        $limit="LIMIT 0,100";
		        
		        $assignment_status="waiting for driver acknowledgement";
		        
		        if ( $val['include_offline_driver']=="" || $val['include_offline_driver']=="0"){
		        	$and.=" AND a.on_duty ='1' ";
                    $and.=" AND a.last_online >='$time_now' ";
                    $and.=" AND a.last_login like '".$todays_date."%'";
		        }
							
		        if ( $val['auto_assign_type']=="one_by_one"){
		        			        			        
					$and.=" AND a.driver_id NOT IN (
					  select driver_id
					  from
					  {{driver_assignment}}
					  where
					  driver_id=a.driver_id
					  and
					  task_id=".Driver::q($task_id)."
					) ";
										
					$stmt2="
					SELECT a.driver_id, a.customer_id,
					 a.first_name,a.last_name,a.location_lat,a.location_lng,
					a.on_duty, a.last_online, a.last_login
					, 
					( $distance_exp * acos( cos( radians($lat) ) * cos( radians( location_lat ) ) 
			        * cos( radians( location_lng ) - radians($lng) ) 
			        + sin( radians($lat) ) * sin( radians( location_lat ) ) ) ) 
			        AS distance
			        FROM {{driver}} a
			        HAVING distance < $radius
					AND a.customer_id=".Driver::q($val['customer_id'])." 
					$and
					ORDER BY distance ASC
					$limit
					";
					
		        } else {
		        			        	
		        	$and.=" AND a.driver_id NOT IN (
					  select driver_id
					  from
					  {{driver_assignment}}
					  where
					  driver_id=a.driver_id
					  and
					  task_id=".Driver::q($task_id)."					  
					) ";
					
					$stmt2="SELECT a.* FROM {{driver}} a		
					WHERE 1
					AND customer_id=".Driver::q($val['customer_id'])." 
					$and			
					";			
		        }
		        		        
		        if($res2 = Yii::app()->db->createCommand($stmt2)->queryAll()){		
		        	
		        	$x=0;
		        	 	        	
		        	foreach ($res2 as $val2) {
		        		
		        		$driver_assign_request_expire = (integer) getOption( $val2['customer_id'] ,"driver_assign_request_expire");
		        		if($driver_assign_request_expire<=0){
		        			$driver_assign_request_expire = 10;
		        		}
		        		
		        		$customer_timezone =  getOption( $val2['customer_id'] ,"customer_timezone");
		        		if(!empty($customer_timezone)){
		        			$timezone = $customer_timezone;
		        		}
		        		
		        		if($x==0){							
							$request_interval =  -$driver_assign_request_expire;
						} else {
							$request_interval =  $x*$driver_assign_request_expire;
						}
		        			        		
		        		$params=array(
						  'auto_assign_type'=>$val['auto_assign_type'],
						  'task_id'=>$val['task_id'],
						  'driver_id'=>$val2['driver_id'],
						  'first_name'=>$val2['first_name'],
						  'last_name'=>$val2['last_name'],
						  'date_created'=>AdminFunctions::dateNow(),
						  'ip_address'=>$_SERVER['REMOTE_ADDR'],
						  'customer_id'=>isset($val2['customer_id'])?$val2['customer_id']:0,
						  'request_interval'=>$request_interval,
						  'timezone'=>$timezone
						);
												
						Yii::app()->db->createCommand()->insert("{{driver_assignment}}",$params);			
						
						$x++;
		        	}
		        } else {
		        	// unable to assign
		        	$assignment_status = "unable to auto assign";
		        	if (!empty($val['autoassign_notify_email'])){
		        		$email_enabled=getOption($val['customer_id'],'FAILED_AUTO_ASSIGN_EMAIL');
		        		if($email_enabled){
		        		   $tpl=getOption($val['customer_id'],'FAILED_AUTO_ASSIGN_EMAIL_TPL');
						   $tpl=Driver::smarty('TaskID',$task_id,$tpl);
						   $tpl=Driver::smarty('CompanyName',getOptionA('website_title'),$tpl);
						   						   
						   sendEmail($notify_email,"","Unable to auto assign Task $task_id",$tpl);
		        		}
		        	}
		        }
		        
		        $less="-1";
			    if($val['assign_request_expire']>0){
				   $less="-".$val['assign_request_expire'];
			    }
		        
			    $params_task=array(
				 'auto_assign_type'=>$val['auto_assign_type'],				 
				 'assign_started'=>AdminFunctions::dateNow(),
				 'assignment_status'=> $assignment_status
				);
				
								
				Yii::app()->db->createCommand()->update("{{driver_task}}",$params_task,
		  	    'task_id=:task_id',
			  	    array(
			  	      ':task_id'=> (integer) $task_id
			  	    )
		  	    );

			    
			} /*end foreach*/
		} else {
			if (isset($_GET['debug'])){echo 'no record to process';}
		}
				
		cronHelper::unlock();
        endif;	
        dump("cron end..");		
    }		
    
    public function actionprocessautoassign_all()
    {
    	dump("cron start..");
        define('LOCK_SUFFIX', CHANNEL_ID.'_processautoassign_all');        
        if(($pid = cronHelper::lock()) !== FALSE):
        
        $stmt="
		SELECT 
		a.assignment_id,
		a.auto_assign_type,
		a.task_id,
		a.driver_id,
		concat(a.first_name,' ',a.last_name) as driver_name,
		a.timezone,
		
		b.customer_name,
		b.delivery_address,
		b.delivery_date,		
		
		c.customer_id,
		c.enabled_push,				
		c.device_platform,
		c.device_id,
		c.phone as driver_phone,
		c.email as driver_email
		
		FROM
		{{driver_assignment}} a
		LEFT JOIN {{driver_task}} b
		ON
		a.task_id = b.task_id
		
		LEFT JOIN {{driver}} c
		ON
		a.driver_id = c.driver_id
		
		WHERE a.status='pending'
		AND a.auto_assign_type='send_to_all'	
		LIMIT 0,50		
		";
        
        if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
        	foreach ($res as $val) {	

        		$timezone = isset($val['timezone'])?$val['timezone']:'';
        		   		
        		if(!empty($timezone)){
        			Yii::app()->timeZone=$timezone;
        		}
        		       		
				Driver::sendDriverNotification('ASSIGN_TASK',$val);	
				
				$params = array(
				 'status'=>"process",
				 'date_process'=>AdminFunctions::dateNow()
				);
				
				Yii::app()->db->createCommand()->update("{{driver_assignment}}",$params,
		  	    'assignment_id=:assignment_id',
			  	    array(
			  	      ':assignment_id'=>(integer)$val['assignment_id']
			  	    )
		  	    );	
			}
        }
        
        cronHelper::unlock();
        endif;	
        dump("cron end..");		
    }

    public function actionprocessautoassign_onebyone()
    {
    	dump("cron start..");
        define('LOCK_SUFFIX', CHANNEL_ID.'_processautoassign_onebyone');        
        if(($pid = cronHelper::lock()) !== FALSE):
        
        $stmt="
        SELECT timezone FROM {{driver_assignment}}
        WHERE status = 'pending'
        GROUP BY timezone
        ORDER BY assignment_id ASC
        LIMIT 0,2
        ";        
        if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
        	foreach ($res as $val) {     
        		$timezone = isset($val['timezone'])?$val['timezone']:'';
        		if(!empty($val['timezone'])){        		   
        		    Yii::app()->timeZone = $val['timezone'];        		    
        		    $now = new DateTime();
					$mins = $now->getOffset() / 60;
					$sgn = ($mins < 0 ? -1 : 1);
					$mins = abs($mins);
					$hrs = floor($mins / 60);
					$mins -= $hrs * 60;
					$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);			
					Yii::app()->db->createCommand("SET time_zone='$offset';")->query();        		    					
        		}
        		$this->one_by_one($timezone);
        	}
        }
        
        cronHelper::unlock();
        endif;	
        dump("cron end..");		
    }   
    
    private function one_by_one($timezone='')
    {
    	if(!empty($timezone)){
    		Yii::app()->timeZone=$timezone;
    	}
    	
    	$stmt="
        SELECT 
		a.assignment_id,
		a.auto_assign_type,
		a.task_id,
		a.driver_id,
		concat(a.first_name,' ',a.last_name) as driver_name,
		a.date_created,
		
		b.customer_name,
		b.delivery_address,
		b.delivery_date,		
		
		c.enabled_push,	
		b.customer_id,
		c.device_platform,
		c.device_id,
		c.phone as driver_phone,
		c.email as driver_email
				
		FROM
		{{driver_assignment}} a
		LEFT JOIN {{driver_task}} b
		ON
		a.task_id = b.task_id
		
		LEFT JOIN {{driver}} c
		ON
		a.driver_id = c.driver_id
		
		WHERE a.status='pending'
		AND a.auto_assign_type='one_by_one'			
		AND a.timezone = ".q($timezone)."		
		AND NOW() - INTERVAL a.request_interval MINUTE >= a.date_created
		LIMIT 0,50
		";    	
    	if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
    		foreach ($res as $val) {
    			
    			Driver::sendDriverNotification('ASSIGN_TASK',$val);	
    			
    			Yii::app()->db->createCommand()->update("{{driver_assignment}}",array(
        		 'status'=>"process",
        		 'date_process'=>AdminFunctions::dateNow(),
        		 'ip_address'=>$_SERVER['REMOTE_ADDR']
        		),
		  	    'assignment_id=:assignment_id',
			  	    array(
			  	      ':assignment_id'=> (integer) $val['assignment_id']
			  	    )
		  	    );
		  	    
		  	    $assigment_status = Yii::t("default","waiting for [driver_name] acknowledgement",array(
		  	      '[driver_name]'=>isset($val['driver_name'])?$val['driver_name']:''
		  	    ));
		  	    
		  	    Yii::app()->db->createCommand()->update("{{driver_task}}",array(
		  	      'assignment_status'=>$assigment_status
		  	    ),
		  	    'task_id=:task_id',
			  	    array(
			  	      ':task_id'=> (integer) $val['task_id']
			  	    )
		  	    );

    			
    		}
    	}
    }
    
    public function actionCheckAutoAssign()
    {
    	dump("cron start..");
        define('LOCK_SUFFIX', CHANNEL_ID.'_checkautoassign');        
        if(($pid = cronHelper::lock()) !== FALSE):
            		
		$stmt="SELECT a.*,
		b.enabled_auto_assign,
		b.include_offline_driver,
		b.autoassign_notify_email,
		b.request_expire,
		b.auto_assign_type,
		b.assign_request_expire,
		b.auto_retry_assigment
		
		 FROM
		{{driver_task}} a
		
		LEFT JOIN {{customer}} b
        ON
        a.customer_id=b.customer_id
        
		WHERE 1
		AND a.status IN ('unassigned') 	
		AND a.auto_assign_type IN ('one_by_one','send_to_all')	
		AND a.assignment_status NOT IN ('','unable to auto assign')
		AND a.task_id NOT IN (
		  select task_id from {{driver_assignment}}
		  where
		  task_id=a.task_id
		  and
		  status='pending'  
		)
		ORDER BY a.task_id ASC
		LIMIT 0,10
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {
							
				$customer_id = isset($val['customer_id'])?(integer)$val['customer_id']:0;				
				Driver::setCustomerTimezone($customer_id);
				
				$task_id=$val['task_id'];
				$assign_type=$val['auto_assign_type'];
				$assign_started=date("Y-m-d g:i:s a",strtotime($val['assign_started']));
				$request_expire = (integer) $val['request_expire'];
				$date_now=date('Y-m-d g:i:s a');
				$notify_email=$val['autoassign_notify_email'];

			
				if($request_expire<=0){
			        $request_expire=10;
				}
												
				$time_diff=Yii::app()->functions->dateDifference($assign_started,$date_now);
				
				if (is_array($time_diff) && count($time_diff)>=1){
					
					if ( $time_diff['hours']>0 || $time_diff['minutes']>=$request_expire){
											    
				        Yii::app()->db->createCommand()->update("{{driver_task}}",array(
				          'assignment_status'=>"unable to auto assign"
				        ),
				  	    'task_id=:task_id',
					  	    array(
					  	      ':task_id'=>$task_id
					  	    )
				  	    );
								    					    	
				    	if ( $res2 = Driver::getUnAssignedDriver3($task_id)){				    		
				    		foreach ($res2 as $val2) {	
				    		   				    		   				    			
				    		   $assigment_id=$val2['assignment_id'];
							   $params_driver=array(
							      'task_status'=>'unable to auto assign',							      
                                  'date_process'=>AdminFunctions::dateNow()
                               );
							   					   	       					   	       
						       Yii::app()->db->createCommand()->update("{{driver_assignment}}",$params_driver,
						  	    'assignment_id=:assignment_id',
							  	    array(
							  	      ':assignment_id'=>$assigment_id
							  	    )
						  	    );
				    				    		   
				    		   $task_info=Driver::getTaskByDriverNTask($val2['task_id'], $val2['driver_id'] );
				    		   Driver::sendDriverNotification('CANCEL_TASK',$task_info);
				    		}
				    	} 
				    	
				    	if(!empty($notify_email)){
				    						    		
				    		$email_enabled=getOption($val['customer_id'],'FAILED_AUTO_ASSIGN_EMAIL');
				    						    		
				    		if($email_enabled){
							   $tpl=getOption($val['customer_id'],'FAILED_AUTO_ASSIGN_EMAIL_TPL');
							   $tpl=Driver::smarty('TaskID',$task_id,$tpl);
							   $tpl=Driver::smarty('CompanyName',getOptionA('website_title'),$tpl);
							   							   
				    		   sendEmail($notify_email,"","Unable to auto assign Task $task_id",$tpl);
				    		}
				    	}   	
				    					    	
				    	/*retry auto assign*/
				    	if ( $val['auto_retry_assigment']==1){
				    		Driver::retryAutoAssign($task_id);
				    	}
				    		
					}
				}
			} /*end foreach*/
		} 
		
		cronHelper::unlock();
        endif;	
        dump("cron end..");
        
    }
    
    public function actionCheckCustomerExpiry()
    {    	
    	
		dump("cron start..");
		define('LOCK_SUFFIX', CHANNEL_ID.'_checkcustomexpiry');        
		if(($pid = cronHelper::lock()) !== FALSE):

    	$date_now=date("Y-m-d");
    	$stmt="UPDATE 
    	{{customer}}
    	SET status='expired'
    	WHERE 
    	plan_expiration<".Driver::q($date_now)."
    	";    	
    	Yii::app()->db->createCommand($stmt)->query();
    	
    	cronHelper::unlock();
		endif;	
		dump("cron end..");				

    }
    
    public function actionProcessBroadcast()
    {    	
    	
		dump("cron start..");
		define('LOCK_SUFFIX', CHANNEL_ID.'_processbroadcast');        
		if(($pid = cronHelper::lock()) !== FALSE):
		
		$stmt="
		SELECT a.*,
		
		(
		 select option_value
		 from {{option}}
		 where		 		 
		 option_name = 'services_json'
		 limit 0,1
		) as services_account_json

		FROM {{push_broadcast}}	a
		WHERE a.status='pending'		
		ORDER BY broadcast_id ASC		
		LIMIT 0,10
		";
		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			$file = AdminFunctions::uploadPath()."/".$res[0]['services_account_json'];
			foreach ($res as $val) {				
				$process_status=''; $json_response='';
				$process_date = AdminFunctions::dateNow();
								
				$topic_id = $val['customer_id'];
				$topics = CHANNEL_TOPIC_ALERT.$topic_id;
								
				try {		    		
	    			$json_response = FcmWrapper::ServiceAccount($file,CHANNEL_ID.'_fcm')
					->setTarget($topics)
					->setTitle($val['push_title'])
					->setBody($val['push_message'])
					->setChannel(CHANNEL_ID)
					->setSound(CHANNEL_SOUNDNAME)
					->setAppleSound(CHANNEL_SOUNDFILE)
					->setBadge(1)
					->setForeground("true")
					->prepare()
					->send();						
					$process_status = 'process';
	    		} catch (Exception $e) {
	    			$process_status = 'failed';
					$json_response = $e->getMessage();						
				}			
								
				if(!empty($process_status)){
		   	  	   $process_status=substr( strip_tags($process_status) ,0,255);
		   	    } else $process_status='failed';	
		   	    
		   	    if(is_array($json_response) && count($json_response)>=1){
		   	    	$json_response = json_encode($json_response);
		   	    } 
		   	    
		   	    $params = array(
				  'status'=>$process_status,
				  'date_process'=>$process_date,
				  'json_response'=>$json_response
				);		
								
				Yii::app()->db->createCommand()->update("{{push_broadcast}}",$params,
		  	    'broadcast_id=:broadcast_id',
			  	    array(
			  	      ':broadcast_id'=>(integer)$val['broadcast_id']
			  	    )
		  	    );
				  
			} //end foreach
		}
		
		cronHelper::unlock();
		endif;	
		dump("cron end..");				
    }
    
    public function actionClearAgentTracking()
    {
    	
    	dump("cron start..");
		define('LOCK_SUFFIX', CHANNEL_ID.'_cleartracking');        
		if(($pid = cronHelper::lock()) !== FALSE):
		
    	$date=date("Y-m-d 23:59:00",strtotime("-7 days"));    	
    	$stmt="
    	DELETE FROM
    	{{driver_track_location}}
    	WHERE 
    	date_created <=".Driver::q($date)."
    	";    	
    	Yii::app()->db->createCommand($stmt)->query();
    	    	
		cronHelper::unlock();
		endif;	
		dump("cron end..");				

    }
    
    public function actionprocess_sms()
    {    	
		dump("cron start..");
		define('LOCK_SUFFIX', CHANNEL_ID.'_process_sms');        
		if(($pid = cronHelper::lock()) !== FALSE):
		
		$stmt="
		SELECT * FROM {{sms_logs}}
		WHERE provider = ''
		AND msg = ''		
		ORDER BY id ASC
		LIMIT 0,50
		";
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {
								
				$resp = Yii::app()->functions->sendSMS($val['to_number'],$val['sms_text'],'',false);
								
		        Yii::app()->db->createCommand()->update("{{sms_logs}}",array(
		          'msg'=>isset($resp['msg'])?$resp['msg']:'process',
		          'raw'=>isset($resp['raw'])?$resp['raw']:'process',
		          'provider'=>isset($resp['sms_provider'])?$resp['sms_provider']:'none',
		        ),
		  	    'id=:id',
			  	    array(
			  	      ':id'=>$val['id']
			  	    )
		  	    );
				
			}
		}
		
		cronHelper::unlock();
		endif;	
		dump("cron end..");				
    }
    
    public function actionprocess_email()
    {    	
		dump("cron start..");
		define('LOCK_SUFFIX', CHANNEL_ID.'_process_email');        
		if(($pid = cronHelper::lock()) !== FALSE):
		
		$from=getOptionA('global_sender');
		
		$stmt="
		SELECT * FROM {{email_logs}}
		WHERE status = ''
		ORDER BY id ASC
		LIMIT 0,50
		";
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			foreach ($res as $val) {
			    $resp = Yii::app()->functions->sendEmail(
			      $val['email_address'],
			      $from,
			      $val['subject'],
			      $val['content'],
			      $val['customer_id'],
			      false
			    );
			    				   
		        Yii::app()->db->createCommand()->update("{{email_logs}}",array(
		          'status'=>$resp==true?"process":"failed"
		        ),
		  	    'id=:id',
			  	    array(
			  	      ':id'=>$val['id']
			  	    )
		  	    );
		  	    			    
			}
		}
		
		cronHelper::unlock();
		endif;	
		dump("cron end..");				
    }
    
    public function actionRunAll()
    {    	    	
    	Driver::consumeUrl(Driver::getHostURL().Yii::app()->createUrl("cron/processpush"));
    	Driver::consumeUrl(Driver::getHostURL().Yii::app()->createUrl("cron/processbroadcast"));
    	Driver::consumeUrl(Driver::getHostURL().Yii::app()->createUrl("cron/process_email"));
    	Driver::consumeUrl(Driver::getHostURL().Yii::app()->createUrl("cron/process_sms"));
    	
    	Driver::consumeUrl(Driver::getHostURL().Yii::app()->createUrl("cron/autoassign"));
    	Driver::consumeUrl(Driver::getHostURL().Yii::app()->createUrl("cron/processautoassign_all"));
    	Driver::consumeUrl(Driver::getHostURL().Yii::app()->createUrl("cron/processautoassign_onebyone"));
    	Driver::consumeUrl(Driver::getHostURL().Yii::app()->createUrl("cron/checkautoassign"));      	
    }
		
} /*end class*/