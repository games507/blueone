<?php
class Api_servicesController extends CController
{	
	public $data;
	public $code=2;
	public $msg='';
	public $details='';
	
	public function __construct()
	{
		$this->data=$_GET;
		
		$website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		 
	    if (!empty($website_timezone)){
	 	   Yii::app()->timeZone=$website_timezone;
	    }		 
	    	    
	}
	
	public function beforeAction($action)
	{
		$api_services_key=getOptionA('api_services_key');
		if(!empty($api_services_key)){
		    $keys = isset($this->data['keys'])?$this->data['keys']:'';
			if($api_services_key!=$keys){
				$this->msg=t("api services key is not valid");
				$this->output();
				Yii::app()->end();
			}
		} else {
			$this->msg=t("api services key is empty in your settings");
			$this->output();
			Yii::app()->end();
		}
		return true;
	}
	
	private function output()
    {
       if (!isset($this->data['debug'])){        	
       	  header('Access-Control-Allow-Origin: *');
          header('Content-type: application/json');
       } 
       
	   $resp=array(
	     'code'=>$this->code,
	     'msg'=>$this->msg,
	     'details'=>$this->details,
	     'request'=>json_encode($this->data)		  
	   );		   
	   if (isset($this->data['debug'])){
	   	   dump($resp);
	   }
	   
	   if (!isset($_GET['callback'])){
  	   	   $_GET['callback']='';
	   }    
	   
	   echo CJSON::encode($resp);	   
	   Yii::app()->end();
    }		
	
	public function actioninsert_task()
	{
		$map_provider = MapsWrapper::getMapProvider();		
		$provider = isset($map_provider['provider'])?$map_provider['provider']:'';		
		MapsWrapper::init($map_provider);
		
		$validator = new Validator;
		$req = array(
		  'merchant_token'=>t("Merchant token is required"),
		  'trans_type'=>t("Transaction type is required"),
		  'email_address'=>t("Email address is required"),
		  'delivery_date'=>t("Delivery date is required"),
		  'delivery_address'=>t("Delivery address is required"),		  
		  'customer_name'=>t("Customer name is required")
		);
		
		$email = array(
		  'email_address'=>t("Invalid email address")
		);
		$validator->email($email,$this->data);
		
		if(isset($this->data['delivery_date'])){
			$delivery_date=$this->data['delivery_date'];
			$delivery_date=explode("-",$delivery_date);
			if(count($delivery_date)!=3){
				$validator->msg[]=t("Invalid delivery date");
			}
		}
		
		if(isset($this->data['trans_type'])){
			$trans_type = isset($this->data['trans_type'])?$this->data['trans_type']:'';			
			$allowed_type = Driver::transactionType();			
			if(!in_array($trans_type,$allowed_type)){
				$validator->msg[]=t("Invalid transaction type");
			}			
		}
		
		$validator->required($req,$this->data);
		if ($validator->validate()){
			$merchant_token = trim($this->data['merchant_token']);			
			if($merchant_info = FrontFunctions::getCustomerByToken($merchant_token)){
				
				$customer_id = $merchant_info['customer_id'];
								
				if(!Driver::planCheckCAnAddTask( $customer_id , $merchant_info['plan_id'] )){
					$this->msg=t("You cannot add more task you account is restrict to add new task");
					$this->output();
				}
								
				$params=array(
				  'customer_id'=>$customer_id,
				  'task_description'=> isset($this->data['task_description']) ? trim($this->data['task_description']) :"",
				  'trans_type'=>isset($this->data['trans_type']) ? trim($this->data['trans_type']) : "",
				  'contact_number'=> isset($this->data['contact_number']) ? trim($this->data['contact_number']) : "",
				  'email_address'=> isset($this->data['email_address'])?  trim($this->data['email_address']) : "",
				  'customer_name'=> isset($this->data['customer_name'])? trim($this->data['customer_name']) : "",
				  'delivery_date'=> isset($this->data['delivery_date']) ? trim($this->data['delivery_date']) : "",
				  'delivery_address'=> isset($this->data['delivery_address']) ? trim($this->data['delivery_address']) : "",
				  'date_created'=>AdminFunctions::dateNow(),
				  'task_token'=>Driver::generateTaskToken()
				);
				if(isset($this->data['task_lat']) && isset($this->data['task_lng'])){
					$params['task_lat']=trim($this->data['task_lat']);
					$params['task_lng']=trim($this->data['task_lng']);
				} else {					
					try {					
					 	$delivery_address = isset($this->data['delivery_address'])?$this->data['delivery_address']:'';
						$resp_loc = MapsWrapper::geoCodeAdress($delivery_address);						
				    	$params['task_lat']=$resp_loc['lat'];
					    $params['task_lng']=$resp_loc['long'];
				    	MapsWrapper::saveLogs($provider,'geoCodeAdress',$resp_loc);
					} catch (Exception $e) {
						MapsWrapper::saveLogs($provider,'geoCodeAdress', $e->getMessage() );
					}
				}			
				
				if(isset($this->data['team_id'])){
					$params['team_id'] = (integer) $this->data['team_id'];
				}
				if(isset($this->data['driver_id'])){
					$params['driver_id'] = (integer) $this->data['driver_id'];
				}
				if(isset($this->data['dropoff_contact_name'])){
					$params['dropoff_contact_name'] = (string) $this->data['dropoff_contact_name'];
				}
				if(isset($this->data['dropoff_contact_number'])){
					$params['dropoff_contact_number'] = (string) $this->data['dropoff_contact_number'];
				}
				if(isset($this->data['drop_address'])){
					$params['drop_address'] = (string) $this->data['drop_address'];
				}
				if(isset($this->data['dropoff_task_lat'])){
					$params['dropoff_task_lat'] = (string) $this->data['dropoff_task_lat'];
				}
				if(isset($this->data['dropoff_task_lng'])){
					$params['dropoff_task_lng'] = (string) $this->data['dropoff_task_lng'];
				}
				if(isset($this->data['status'])){
					$params['status'] = (string) $this->data['status'];
				}							
												
				if(Yii::app()->db->createCommand()->insert("{{driver_task}}",$params)){	
					$this->code = 1;
					$this->msg=t("Successful");
					$this->details=array(
					  'task_token'=>$params['task_token']
					);
				} else $this->msg = t("Cannot insert records please try again later");
								
			} else $this->msg = t("Invalid merchant token");
		} else $this->msg = $validator->getError();
		$this->output();
	}
	
	public function actioninsert_contact()
	{		
		$validator = new Validator;
		$req = array(
		  'merchant_token'=>t("Merchant token is required"),
		  'name'=>t("Name is required"),
		  'email'=>t("Email address is required"),
		  'phone'=>t("Phone is required"),
		  'address'=>t("Delivery is required"),		  
		  'status'=>t("Status is required")
		);
		
		$email = array(
		  'email'=>t("Invalid email address")
		);
		$validator->email($email,$this->data);
		
		$status_list = AdminFunctions::customerStatus();
	    if(isset($this->data['status'])){
	    	if(!empty($this->data['status'])){
		    	if(!array_key_exists($this->data['status'],$status_list)){
		    		$validator->msg[]=t("Invalid status");
		    	}
	    	}
	    }
		
		$validator->required($req,$this->data);
		if ($validator->validate()){
			$merchant_token = trim($this->data['merchant_token']);			
			if($merchant_info = FrontFunctions::getCustomerByToken($merchant_token)){
				$customer_id = $merchant_info['customer_id'];
				
				$resp = Yii::app()->db->createCommand()
		          ->select('fullname')
		          ->from('{{contacts}}')   
		          ->where("customer_id=:customer_id AND email=:email",array(
		            ':customer_id'=>$customer_id,
		            ':email'=>$this->data['email'],		            
		          ))	          
		          ->limit(1)
		          ->queryRow();	

		         if(!$resp){
					$params = array(
					  'customer_id'=>$customer_id,
					  'fullname'=> isset($this->data['name']) ?  trim($this->data['name']) : "",
					  'email'=> isset($this->data['email']) ? trim($this->data['email']) : "",
					  'phone'=> isset($this->data['phone'])?  trim($this->data['phone']) : "",
					  'address'=>isset($this->data['address']) ?  trim($this->data['address']) : "",
					  'status'=>isset($this->data['status']) ? trim($this->data['status']) : "",
					  'date_created'=>AdminFunctions::dateNow(),
					  'ip_address'=>$_SERVER['REMOTE_ADDR']
					);					
					
					$map_provider = MapsWrapper::getMapProvider();		
		            $provider = isset($map_provider['provider'])?$map_provider['provider']:'';		
	    	    
										
					try {			
						MapsWrapper::init($map_provider);		
						$res_location = MapsWrapper::geoCodeAdress($params['address']);	
						$params['addresss_lat']=$res_location['lat'];
					    $params['addresss_lng']=$res_location['long'];
					    MapsWrapper::saveLogs($provider,'geoCodeAdress',$res_location);
					} catch (Exception $e) {
						MapsWrapper::saveLogs($provider,'geoCodeAdress', $e->getMessage() );
					}
															
					if(Yii::app()->db->createCommand()->insert("{{contacts}}",$params)){	
						$contact_id = Yii::app()->db->getLastInsertID();
						$this->code = 1;
						$this->msg=t("Successful");
						$this->details=array(
						  'contact_id'=>$contact_id
						);
					} else $this->msg = t("Cannot insert records please try again later");
				    
		         } else $this->msg = t("Email address already exist");
				
			} else $this->msg = t("Invalid merchant token");
		} else $this->msg = $validator->getError();
		
		$this->output();
	}
	
}
/*end class*/