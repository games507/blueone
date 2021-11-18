<?php
require_once 'africastalking/vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;

class africastalkingWrapper
{
	
	public static function sendSMS($data=array())
	{				
		if(!isset($data['username'])){
			throw new Exception( t("invalid username") );
		}
		if(!isset($data['api_key'])){
			throw new Exception( t("invalid api key") );
		}
		if(!isset($data['to'])){
			throw new Exception( t("Invalid Phone Number") );
		}
		if(!isset($data['from'])){
			throw new Exception( t("Invalid Sender / Shortcode") );
		}
		if(!isset($data['message'])){
			throw new Exception( t("Invalid text message") );
		}
				
		$AT  = new AfricasTalking($data['username'], $data['api_key']);
		$sms = $AT->sms();
		$resp = $sms->send(array(
		  'to'=>trim($data['to']),
		  'from'=>trim($data['from']),
		  'message'=>trim($data['message']),
		));
		/*dump("<h2>response</h2>");
		dump($resp);*/
		if(is_array($resp) && count((array)$resp)>=1){
			if(isset($resp['status'])){
				foreach ($resp['data']->SMSMessageData->Recipients as $val) {									
					switch ($val->statusCode) {
						case 100:
						case 101:	
						case 102:			
						    return $val->messageId;
							break;
							
						case 403:	
						   throw new Exception( t("Invalid Phone Number") );
						   break;
						   
						case 404:	
						   throw new Exception( t("Unsupported Number Type") );
						   break;  
						   
						case 405:	
						   throw new Exception( t("Insufficient Balance") );
						   break;     
						   
						case 406:	
						   throw new Exception( t("User In Blacklist") );
						   break;        
						   
						case 407:	
						   throw new Exception( t("Could Not Route") );
						   break;         
						   
						case 500:	
						   throw new Exception( t("Internal Server Error") );
						   break;              
						  
						case 501:	
						   throw new Exception( t("Gateway Error") );
						   break;           
						   
						case 502:	
						   throw new Exception( t("Rejected By Gateway") );
						   break;                
					
						default:
							throw new Exception( $val->status );
							break;
					}
				}
			} else throw new Exception( t("invalid status ")." $resp" );
		} else throw new Exception( t("invalid response from api")." $resp" );
	}
	
}
/*end class*/