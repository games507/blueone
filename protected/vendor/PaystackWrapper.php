<?php
class PaystackWrapper
{
	public static function getCredentials()
	{
		$secret_key = ''; $mode = '';
		$mode = getOptionA('psk_mode');		
		switch ($mode) {
			case "sandbox":	
			    $secret_key = getOptionA('psk_sandbox_secret_key');
				break;
				
		    case "live":
		    	$secret_key = getOptionA('psk_live_secret_key');
			    break;
			    
			default:
				break;
		}
		if(!empty($secret_key)){
			return array(
			 'mode'=>$mode,
			 'secret_key'=>$secret_key
			);
		}
		return false;
	}
	
	public static function pay($params=array())
	{
		if($credentials=self::getCredentials()){
			$secret_key = $credentials['secret_key'];
			require 'paystack_src/autoload.php';
		    $paystack = new Yabacon\Paystack($secret_key);
		    
		    try {
		    	
				$tranx = $paystack->transaction->initialize($params);
					  				  
				$redirect_url = $tranx->data->authorization_url;				
				return $redirect_url;
					  
			} catch(\Yabacon\Paystack\Exception\ApiException $e){
				throw new Exception( $e->getMessage() );
			}		
		} else throw new Exception( t("credentials not available") );
	}
	
	public static function verifyPayment($reference='')
	{
		if(empty($reference)){
			throw new Exception( t("invalid reference number") );
		}
		if($credentials=self::getCredentials()){
			$secret_key = $credentials['secret_key'];
			require 'paystack_src/autoload.php';				
		    $paystack = new Yabacon\Paystack($secret_key);
		    
		    try {
			
		    	$tranx = $paystack->transaction->verify([
	              'reference'=>$reference,
	            ]);
                            
	            if ('success' === $tranx->data->status){
	            	return array(
	            	 'transaction_id'=>$tranx->data->id,
	            	 'response'=>json_encode($tranx)
	            	);
	            } 
	                            		    
		    } catch(\Yabacon\Paystack\Exception\ApiException $e){
		    	throw new Exception( $e->getMessage() );
		    }
		} else throw new Exception( t("credentials not available") );
	}
	
} /*end class*/