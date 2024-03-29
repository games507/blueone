<?php
class Functions extends CApplicationComponent
{
	
    public function getOptionAdmin($option_name='')
	{
		$stmt="SELECT * FROM
		{{option}}
		WHERE
		option_name='".addslashes($option_name)."'
		AND
		customer_id='0'
		LIMIT 0,1
		";
		$connection=Yii::app()->db;
		$rows=$connection->createCommand($stmt)->queryAll(); 		
		if (is_array($rows) && count($rows)>=1){
			return stripslashes($rows[0]['option_value']);
		}
		return '';
	}	

	public function updateOptionAdmin($option_name='',$option_value='')
	{
		$stmt="SELECT * FROM
		{{option}}
		WHERE
		option_name='".addslashes($option_name)."'
		AND
		customer_id='0'
		";
		$connection=Yii::app()->db;
		$rows=$connection->createCommand($stmt)->queryAll(); 		
		
		$params=array(
		'option_name'=> addslashes($option_name),
		'option_value'=> addslashes($option_value)
		);
		$command = Yii::app()->db->createCommand();
		
		if (is_array($rows) && count($rows)>=1){
			$res = $command->update('{{option}}' , $params , 
				                     'option_name=:option_name' , array(':option_name'=> addslashes($option_name) ));
		    if ($res){
		    	return TRUE;
		    } 
		} else {			
			if ($command->insert('{{option}}',$params)){
				return TRUE;
			}
		}
		return FALSE;
	}
	
    public function updateOption($option_name='',$option_value='',$customer_id='')
	{
		$and='';
		if ( !empty($customer_id)){
			$and=" AND customer_id='".$customer_id."' ";
		}
		$stmt="SELECT * FROM
		{{option}}
		WHERE
		option_name='".addslashes($option_name)."'		
		$and
		";
		$connection=Yii::app()->db;
		$rows=$connection->createCommand($stmt)->queryAll(); 		
		
		$params=array(
		'option_name'=> addslashes($option_name),
		'option_value'=> addslashes($option_value)
		);
		if ( !empty($customer_id)){
			$params['customer_id']=$customer_id;
		}
		$command = Yii::app()->db->createCommand();
				
		if (is_array($rows) && count($rows)>=1){			
			$res = $command->update('{{option}}' , $params , 
				                     'option_name=:option_name and customer_id=:customer_id' ,
				                     array(
				                      ':option_name'=> addslashes($option_name),
				                      ':customer_id'=>$customer_id
				                      )
				                     );
		    if ($res){
		    	return TRUE;
		    } 
		} else {			
			if ($command->insert('{{option}}',$params)){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function getOption($option_name='',$customer_id='')
	{
		$and='';
		if ( !empty($customer_id)){
			$and=" AND customer_id='".$customer_id."' ";
		}
		$stmt="SELECT * FROM
		{{option}}
		WHERE
		option_name='".addslashes($option_name)."'
		$and
		LIMIT 0,1
		";
		//dump($stmt);
		$connection=Yii::app()->db;
		$rows=$connection->createCommand($stmt)->queryAll(); 		
		if (is_array($rows) && count($rows)>=1){
			return stripslashes($rows[0]['option_value']);
		}
		return '';
	}	
	
    public function jsLanguageValidator()
    {
    	$js_lang=array(
		  'requiredFields'=>Yii::t("default","You have not answered all required fields"),
		  'groupCheckedTooFewStart'=>Yii::t("default","Please choose at least"),
		  'badEmail'=>Yii::t("default","You have not given a correct e-mail address"),
		);
		return $js_lang;
    }	
    
   public function jsLanguageAdmin()
    {
    	
    	$link="<a href=\"".Yii::app()->request->baseUrl."/merchant/MerchantStatus/"."\">".Yii::t("default","click here to renew membership")."</a>";
    	return array(
    	  "deleteWarning"=>Yii::t("default","You are about to permanently delete the selected items.\n'Cancel' to stop, 'OK' to delete.?"),
    	  "checkRowDelete"=>Yii::t("default","Please check on of the row to delete."),
    	  "removeFeatureImage"=>Yii::t("default","Remove image"),
    	  "removeFiles"=>Yii::t("default","Remove Files"),
    	  "lastTotalSales"=>Yii::t("default","Last 30 days Total Sales"),
    	  "lastItemSales"=>Yii::t("default","Last 30 days Total Sales By Item"),
    	  "NewOrderStatsMsg"=>Yii::t("default","New Order has been placed."),
    	  
    	  'Hour'=>Yii::t("default","Hour"),
    	  'Minute'=>Yii::t("default","Minute"),
    	  'processing'=>Yii::t("default","processing."),
    	  'merchantStats'=>Yii::t("default","Your merchant membership is expired. Please renew your membership.").$link,
    	  "Status"=>Yii::t("default","Status"),
    	  
    	  "tablet_1"=>Yii::t("default","No data available in table"),
    	  "tablet_2"=>Yii::t("default","Showing _START_ to _END_ of _TOTAL_ entries"),
    	  "tablet_3"=>Yii::t("default","Showing 0 to 0 of 0 entries"),
    	  "tablet_4"=>Yii::t("default","(filtered from _MAX_ total entries)"),
    	  "tablet_5"=>Yii::t("default","Show _MENU_ entries"),
    	  "tablet_6"=>Yii::t("default","Loading..."),
    	  "tablet_7"=>Yii::t("default","Processing..."),
    	  "tablet_8"=>Yii::t("default","Search:"),
    	  "tablet_9"=>Yii::t("default","No matching records found"),
    	  "tablet_10"=>Yii::t("default","First"),
    	  "tablet_11"=>Yii::t("default","Last"),
    	  "tablet_12"=>Yii::t("default","Next"),
    	  "tablet_13"=>Yii::t("default","Previous"),
    	  "tablet_14"=>Yii::t("default",": activate to sort column ascending"),
    	  "tablet_15"=>Yii::t("default",": activate to sort column descending"),    	
    	  'read_more'=>t("Read more"),
    	  'read_less'=>t("Read less"),
       );
    	  
    }    
    
    public function prettyDate($date='',$full=false)
    {
    	if ($date=="0000-00-00"){
    		return ;
    	}    
    	if ($date=="0000-00-00 00:00:00"){
    		return ;
    	}
    	if ( !empty($date)){
    		if  ($full==TRUE){
    	         return date('M d,Y G:i:s',strtotime($date));
    		} else return date('M d,Y',strtotime($date));
    	}
    	return false;
    }

	public function FormatDateTime($date='',$time=true)
	{
		if ($date=="0000-00-00"){
    		return ;
    	}    
    	if ($date=="0000-00-00 00:00:00"){
    		return ;
    	}
    	if ( !empty($date)){    		
    		$date_f=Yii::app()->functions->getOptionAdmin("website_date_format");
    		$time_f=Yii::app()->functions->getOptionAdmin("website_time_format");       		
    		if (!empty($date_f)){
    			if ( $time==TRUE){
    			    $date_ouput = date("$date_f $time_f",strtotime($date));	
    			} else $date_ouput = date("$date_f",strtotime($date));	    			
    			return $this->translateDate($date_ouput);
    		} else {
    			if ( $time==TRUE){
    		        $date_ouput= date('M d,Y G:i:s',strtotime($date));	
    			} else $date_ouput= date('M d,Y',strtotime($date));	
    		    return $this->translateDate($date_ouput);
    		}
    	}
    	return false;
	}
	    
    public function dateTranslation()
    {
    	return array(
    	  'January'=>Yii::t("default","January"),
    	  'February'=>Yii::t("default","February"),
    	  'March'=>Yii::t("default","March"),
    	  'April'=>Yii::t("default","April"),
    	  'May'=>Yii::t("default","May"),
    	  'June'=>Yii::t("default","June"),
    	  'July'=>Yii::t("default","July"),
    	  'August'=>Yii::t("default","August"),
    	  'September'=>Yii::t("default","September"),
    	  'October'=>Yii::t("default","October"),
    	  'November'=>Yii::t("default","November"),
    	  'December'=>Yii::t("default","December"),
    	  'Jan'=>Yii::t("default","Jan"),
    	  'Feb'=>Yii::t("default","Feb"),
    	  'Mar'=>Yii::t("default","Mar"),
    	  'Apr'=>Yii::t("default","Apr"),
    	  'May'=>Yii::t("default","May"),
    	  'Jun'=>Yii::t("default","Jun"),
    	  'Jul'=>Yii::t("default","Jul"),
    	  'Aug'=>Yii::t("default","Aug"),
    	  'Sep'=>Yii::t("default","Sep"),
    	  'Oct'=>Yii::t("default","Oct"),
    	  'Nov'=>Yii::t("default","Nov"),
    	  'Dec'=>Yii::t("default","Dec"),
    	  'Sunday'=>t("Sunday"),
    	  'Monday'=>t("Monday"),
    	  'Tuesday'=>t("Tuesday"),
    	  'Wednesday'=>t("Wednesday"),
    	  'Thursday'=>t("Thursday"),
    	  'Friday'=>t("Friday"),
    	  'Saturday'=>t("Saturday"),
    	  'Sun'=>Yii::t("default","Sun"),
    	  'Mon'=>Yii::t("default","Mon"),
    	  'Tue'=>Yii::t("default","Tue"),
    	  'Wed'=>Yii::t("default","Wed"),
    	  'Thu'=>Yii::t("default","Thu"),
    	  'Fri'=>Yii::t("default","Fri"),
    	  'Sat'=>Yii::t("default","Sat"),
    	  'Su'=>Yii::t("default","Su"),
    	  'Mo'=>Yii::t("default","Mo"),
    	  'Tu'=>Yii::t("default","Tu"),
    	  'We'=>Yii::t("default","We"),
    	  'Th'=>Yii::t("default","Th"),
    	  'Fr'=>Yii::t("default","Fr"),
    	  'Sa'=>Yii::t("default","Sa"),
    	  
    	  'day'=>Yii::t("default","day"),
    	  'days'=>Yii::t("default","days"),
    	  'week'=>Yii::t("default","week"),
    	  'weeks'=>Yii::t("default","weeks"),
    	  'month'=>Yii::t("default","month"),
    	  'months'=>Yii::t("default","months"),
    	  'ago'=>Yii::t("default","ago"),
    	  'In'=>Yii::t("default","In"),
    	  'minute'=>Yii::t("default","minute"),
    	  'hour'=>Yii::t("default","hour"),
    	  'yesterday'=>Yii::t("default","yesterday"),
    	  'hours'=>Yii::t("default","hours"),
    	  'mins'=>Yii::t("default","mins"),
    	);
    }
    
    public function translateDate($date='')
    {    	    	
    	$translate=$this->dateTranslation();    	
    	foreach ($translate as $key=>$val) {    		
    		$date=str_replace($key,$val,$date);
    	}
    	return $date;
    }
    
    public function timeFormat($time='',$is_display=false)
    {
    	if(empty($time)){
			return false;
		}
		$time_format=$this->getOptionAdmin("website_time_picker_format");		
		switch ($time_format){
			case "12":
				if ( $is_display==true){
					return date("g:i A", strtotime($time));
				} else return date("G:i", strtotime($time));
				break;
			default:
				if ( $is_display==true){
					return date("G:i", strtotime($time));
				} else return date("G:i", strtotime($time));
				break;	
		}
		return $time;
    }
    
    public function sendEmail($to='',$from='',$subject='',$body='', $customer_id='' , $is_log=true)
    {
    	
    	if(empty($from)){
    		$from=getOptionA('global_sender');
    	}
    	
    	$params=array(
    	  'email_address'=>$to,
    	  'sender'=>$from,
    	  'subject'=>$subject,
    	  'content'=>$body,
    	  'date_created'=>AdminFunctions::dateNow(),
    	  'ip_address'=>$_SERVER['REMOTE_ADDR']
    	);
    	
    	if (!empty($customer_id)){
    		$params['customer_id']=$customer_id;
    	}
    	
    	$email_provider=getOptionA('email_provider');    	
    	if ( $email_provider=="smtp"){
    		    		    		
    		require_once 'phpmailer/PHPMailerAutoload.php';
    		$mail = new PHPMailer;    		
    		$mail->isSMTP(); 
    		//$mail->SMTPDebug = 3;
    		$mail->Host = trim(getOptionA('smtp_host'));
    		$mail->SMTPAuth = true;  
    		$mail->Username = trim(getOptionA('smtp_username')); 
    		$mail->Password = trim(getOptionA('smtp_password'));
    		$mail->SMTPSecure = trim(getOptionA('smtp_secure')) ;    
    		$mail->Port = trim(getOptionA('smtp_port'));
    		    	
    		$mail->setFrom($from);
    		$mail->addAddress($to);   
    		$mail->isHTML(true);   
    		$mail->Subject = $subject;
    		$mail->Body    = $body;
    		
    		if($mail->send()) {    
    			$mail->clearAllRecipients();
    		    $params['status']="send";			
    		    if($is_log){
				   Yii::app()->db->createCommand()->insert("{{email_logs}}",$params);
    		    }
				return true;    					
            }   
            
            $mail->clearAllRecipients();
    		$params['status']="failed" ." " . $mail->ErrorInfo;    		

    		if($is_log){
		       Yii::app()->db->createCommand()->insert("{{email_logs}}",$params);
    		}
    	    return false;
    	} 
    	
    	$headers  = "From: $from\r\n";		
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		
$message =<<<EOF
$body
EOF;
		$headers  = "From: $from\r\n";		
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
				
		if (!empty($to)) {
			if (@mail($to, $subject, $message, $headers)){
				$params['status']="send";
				if($is_log){
				   Yii::app()->db->createCommand()->insert("{{email_logs}}",$params);
				}
				return true;
			}
		}
				
		if($is_log){
		   $params['status']="failed";		
		   Yii::app()->db->createCommand()->insert("{{email_logs}}",$params);
		}
		
    	return false;
    }
    
    public function sendSMS($to='',$message='' , $customer_id='' , $is_log=true)
    {
    	$sms_provier=getOptionA('sms_provier');    
    	
    	$msg=''; $raw='';
    	
    	//dump($sms_provier);
    	
    	switch ($sms_provier) {
    		
    		case "clickatell":
    			try {    			
	    			$api_key=getOptionA('clickatell_apikey');
	    			$use_curl=getOptionA('clickatell_curl');
	    			$use_unicode=getOptionA('clickatell_unicode');	    			
	    			$raw= ClickatellWrapper::sendSMS($api_key,$to,$message,$use_curl,$use_unicode);
	    			$msg="process";
	    		} catch (Exception $e){
	    			$msg=$e->getMessage();
	    			$raw=$e->getMessage();
	    		}       	
    			break;
    			
    		case "twilio":
    			require_once "Twilio.php";		
				$sms_sender_id=Yii::app()->functions->getOptionAdmin('twilio_sender_id');
				$sms_account_id=Yii::app()->functions->getOptionAdmin('twilio_sid');
				$sms_token=Yii::app()->functions->getOptionAdmin('twilio_token');
				
				$twilio=new Twilio;
				$twilio->_debug=false;
				$twilio->sid=$sms_account_id;
				$twilio->auth=$sms_token;
				$twilio->data['From']=$sms_sender_id;
				$twilio->data['To']=$to;
				$twilio->data['Body']=$message;
				if ($resp=$twilio->sendSMS()){
					$raw=$twilio->getSuccessXML();				
					$msg="process";
				} else $msg=$twilio->getError();				
    			break;
    	
    		case "nexmo":
    			$nexmo_sender_id=Yii::app()->functions->getOptionAdmin('nexmo_sender');
	    		$nexmo_key=Yii::app()->functions->getOptionAdmin('nexmo_key');
	    		$nexmo_secret=Yii::app()->functions->getOptionAdmin('nexmo_secret');    		
	    		$nexmo_use_curl=Yii::app()->functions->getOptionAdmin('nexmo_curl');  
	    		    		
	    		$Nexmo=new Nexmo;
	    		$Nexmo->key=$nexmo_key;
	    		$Nexmo->secret=$nexmo_secret;
	    		$Nexmo->sender=$nexmo_sender_id;    		
	    		$Nexmo->to=$to;
	    		$Nexmo->message=$message;
	    		$Nexmo->is_curl=$nexmo_use_curl;	    		
	    		$nexmo_use_unicode=Yii::app()->functions->getOptionAdmin('nexmo_unicode');
	    		if ( $nexmo_use_unicode==1){
	    			$Nexmo->unicode=true;
	    		}	    		
	    		
	    		try {    			
	    			$raw=$Nexmo->sendSMS();
	    			$msg="process";
	    		} catch (Exception $e){
	    			$msg=$e->getMessage();
	    		}       		
    		    break;
    		    
    		case "sms_gateway":
    			try {    			
	    			$raw=SMSGateway::sendSMS($to,$message);
	    			$msg="process";	    			
	    		} catch (Exception $e){
	    			$msg=$e->getMessage();
	    		}       			    		
    		    break;
    		    
    		case "africas":    
    		    try {
    				$raw = africastalkingWrapper::sendSMS(array( 
    				  'to'=>$to,
    				  'message'=>$message,
    				  'from'=>getOptionA('africas_sender'),
    				  'username'=>getOptionA('africas_username'),
    				  'api_key'=>getOptionA('africas_apikey')
    				));
    				$msg ="process";
    			} catch (Exception $e){
    				$msg  = $e->getMessage();
    			}    	
    		    break;
    		    
    		case "msg91":    			
    		       $msg91_route=getOptionA('msg91_route');
	    		   if(empty($msg91_route)){
	    		   	  $msg91_route='default';
	    		   }
	    		   $msg_resp=Msg91::sendSMS(
	    		      getOptionA('msg91_authkey'),
	    		      $to,
	    		      getOptionA('msg91_senderid'),
	    		      $message,
	    		      getOptionA('msg91_unicode'),
	    		      $msg91_route
	    		   );
	    		   if($msg_resp){
	    		   	  $msg="process";
	    		   	  $raw=$msg_resp;
	    		   } else $msg=Msg91::$msg;
    		   break;    
    		    
    		default:
    			$msg='no sms provider';
    			$raw='no sms provider';
    			break;
    	}

    	   	    	
    	if($is_log){    		
    		
    		$params=array(
	    	  'to_number'=>$to,
	    	  'sms_text'=>$message,
	    	  'provider'=>$sms_provier,
	    	  'msg'=>$msg,
	    	  'raw'=>$raw,
	    	  'date_created'=>AdminFunctions::dateNow(),
	    	  'ip_address'=>$_SERVER['REMOTE_ADDR']
	    	);
	    	
	    	if ($customer_id>0){
	    		$params['customer_id']=$customer_id;
	    	}    
	    	    		
    		Yii::app()->db->createCommand()->insert("{{sms_logs}}",$params);
    	}    	
    	
    	return array(
		  'msg'=>$msg,
		  'raw'=>$raw,
		  'sms_provider'=>$sms_provier
		);
    }
    
    public static function dateDifference($start, $end )
    {
        $uts['start']=strtotime( $start );
		$uts['end']=strtotime( $end );
		if( $uts['start']!==-1 && $uts['end']!==-1 )
		{
		if( $uts['end'] >= $uts['start'] )
		{
		$diff    =    $uts['end'] - $uts['start'];
		if( $days=intval((floor($diff/86400))) )
		    $diff = $diff % 86400;
		if( $hours=intval((floor($diff/3600))) )
		    $diff = $diff % 3600;
		if( $minutes=intval((floor($diff/60))) )
		    $diff = $diff % 60;
		$diff    =    intval( $diff );            
		return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
		}
		else
		{			
		return false;
		}
		}
		else
		{			
		return false;
		}
		return( false );
     }        
     
    public function smarty($search='',$value='',$subject='')
    {	
	   return str_replace("[".$search."]",$value,$subject);
    }
     
	
} /*end class*/


function t($message='')
{
	return Yii::t("default",$message);
}

function websiteUrl()
{
	return Yii::app()->getBaseUrl(true);
}

function statusList()
{
	return array(
	 'publish'=>Yii::t("default",'Publish'),
	 'pending'=>Yii::t("default",'Pending for review'),
	 'draft'=>Yii::t("default",'Draft')
	);
}

function dump($data='')
{
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function getOption($customer_id='',$option_name='')
{
	return Yii::app()->functions->getOption($option_name,$customer_id );
}

function updateOption($option_name='',$option_value='',$customer_id='')
{
	return Yii::app()->functions->updateOption($option_name, $option_value, $customer_id);
}

function getOptionA($key='')
{
	return Yii::app()->functions->getOptionAdmin($key);  
}

function updateOptionAdmin($option_name='',$option_value='')
{
	return Yii::app()->functions->updateOptionAdmin($option_name,$option_value);  
}

function sendEmail($to='',$from='',$subject='',$body='', $customer_id='')
{
	return Yii::app()->functions->sendEmail($to,$from,$subject,$body,$customer_id);
}

function sendSMS($to='',$text='')
{
	return Yii::app()->functions->sendSMS($to,$text);
}

function smarty($search='',$value='',$subject='')
{	
   return Yii::app()->functions->smarty($search,$value,$subject);
}

function q($data='')
{
	return Yii::app()->db->quoteValue($data);
}	