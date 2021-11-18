
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("SMS Settings")?></h5>
    </div>    
  </div> <!--row-->
  
  <form id="frm" method="POST" onsubmit="return false;">
  <?php echo CHtml::hiddenField('action','smsSettings')?>
  
  <div class="row">
     <div class="col s12">
        <?php echo CHtml::radioButton('sms_provier',
	      getOptionA('sms_provier')=="twilio"?true:false
	      ,array(
	        'id'=>"sms_provier",
	        'class'=>"with-gap sms_provier",
	        'value'=>"twilio"
	      ))?>
	      <label for="sms_provier"><?php echo t("Twilio")?></label>
	      
	      <?php echo CHtml::radioButton('sms_provier',
	      getOptionA('sms_provier')=="nexmo"?true:false
	      ,array(
	        'id'=>"sms_provier1",
	        'class'=>"with-gap sms_provier",
	        'value'=>"nexmo"
	      ))?>
	      <label for="sms_provier1"><?php echo t("Nexmo")?></label>
	      
	      <?php echo CHtml::radioButton('sms_provier',
	      getOptionA('sms_provier')=="sms_gateway"?true:false
	      ,array(
	        'id'=>"sms_provier2",
	        'class'=>"with-gap sms_provier",
	        'value'=>"sms_gateway"
	      ))?>
	      <label for="sms_provier2"><?php echo t("SMS Gateway")?></label>
	      
	      <?php echo CHtml::radioButton('sms_provier',
	      getOptionA('sms_provier')=="africas"?true:false
	      ,array(
	        'id'=>"sms_provier3",
	        'class'=>"with-gap sms_provier",
	        'value'=>"africas"
	      ))?>
	      <label for="sms_provier3"><?php echo t("Africas Talking SMS")?></label>
	      
	      
	      <?php echo CHtml::radioButton('sms_provier',
	      getOptionA('sms_provier')=="clickatell"?true:false
	      ,array(
	        'id'=>"sms_provier4",
	        'class'=>"with-gap sms_provier",
	        'value'=>"clickatell"
	      ))?>
	      <label for="sms_provier4"><?php echo t("Clickatell")?></label>
	      
	      
	      <?php echo CHtml::radioButton('sms_provier',
	      getOptionA('sms_provier')=="msg91"?true:false
	      ,array(
	        'id'=>"sms_provier5",
	        'class'=>"with-gap sms_provier",
	        'value'=>"msg91"
	      ))?>
	      <label for="sms_provier5"><?php echo t("msg91")?></label>
	      
     </div>
  </div>
  
  <h5><?php echo t("Twilio")?></h5>
  
  <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('twilio_sender_id',
	       getOptionA('twilio_sender_id')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Sender ID")?></label>
	      </div>  
     </div>
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('twilio_sid',
	       getOptionA('twilio_sid')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Account SID")?></label>
	      </div>  
     </div>
  </div>
  
   <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('twilio_token',
	       getOptionA('twilio_token')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("AUTH Token")?></label>
	      </div>  
     </div>    
  </div>
  
  
  <h5><?php echo t("Nexmo")?></h5>
  
   <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('nexmo_sender',
	       getOptionA('nexmo_sender')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Sender")?></label>
	      </div>  
     </div>
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('nexmo_key',
	       getOptionA('nexmo_key')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Key")?></label>
	      </div>  
     </div>
  </div>
  
   <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('nexmo_secret',
	       getOptionA('nexmo_secret')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Secret")?></label>
	      </div>  
     </div>    
  </div>
  
   <div class="row">
     <div class="col s6">
        <?php echo CHtml::checkBox('nexmo_curl',
	      getOptionA('nexmo_curl')==1?true:false
	      ,array(
	        'id'=>"nexmo_curl",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="nexmo_curl"><?php echo t("Use CURL")?></label>	      
     </div>
     
     <div class="col s6">
         <?php echo CHtml::checkBox('nexmo_unicode',
	      getOptionA('nexmo_unicode')==1?true:false
	      ,array(
	        'id'=>"nexmo_unicode",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="nexmo_unicode"><?php echo t("Use Unicode")?></label>
     </div>
     
  </div>
  
  
  <h5><?php echo t("SMS Gateway")?></h5>
  
   <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('sms_gateway_username',
	       getOptionA('sms_gateway_username')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Username")?></label>
	      </div>  
     </div>
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::passwordField('sms_gateway_password',
	       getOptionA('sms_gateway_password')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Password")?></label>
	      </div>  
     </div>
  </div>
  
  <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('sms_gateway_sender',
	       getOptionA('sms_gateway_sender')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Sender")?></label>
	      </div>  
     </div>     
     
      <div class="col s6">
        <?php echo CHtml::checkBox('sms_user_curl',
	      getOptionA('sms_user_curl')==1?true:false
	      ,array(
	        'id'=>"sms_user_curl",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="sms_user_curl"><?php echo t("Use CURL")?></label>	      
     </div>                 
  </div>
  
  <h5><?php echo t("Africas Talking SMS")?></h5>
  
   <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('africas_username',
	       getOptionA('africas_username')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Username")?></label>
	      </div>  
     </div>
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('africas_apikey',
	       getOptionA('africas_apikey')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("API key")?></label>
	      </div>  
     </div>
  </div>
  
    <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('africas_sender',
	       getOptionA('africas_sender')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Sender / Shortcode")?></label>
	      </div>  
     </div>
    </div>
    
    
    
  <h5><?php echo t("Clickatell")?></h5>
  
   <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('clickatell_apikey',
	       getOptionA('clickatell_apikey')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("API KEY")?></label>
	      </div>  
     </div>
     <div class="col s6">
        <?php echo CHtml::checkBox('clickatell_curl',
	      getOptionA('clickatell_curl')==1?true:false
	      ,array(
	        'id'=>"clickatell_curl",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="clickatell_curl"><?php echo t("Use CURL")?></label>	      
     </div>
  </div>
  
    <div class="row">
       <div class="col s6">
        <?php echo CHtml::checkBox('clickatell_unicode',
	      getOptionA('clickatell_unicode')==1?true:false
	      ,array(
	        'id'=>"clickatell_unicode",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="clickatell_unicode"><?php echo t("Use Unicode")?></label>	      
     </div>
    </div>    
  
    
    
    <!--MSG91-->
      <h5><?php echo t("Msg91")?></h5>
  
   <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('msg91_authkey',
	       getOptionA('msg91_authkey')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Auth Key")?></label>
	      </div>  
     </div>
     <div class="col s6">
         <div class="input-field">	    
	       <?php echo CHtml::textField('msg91_senderid',
	       getOptionA('msg91_senderid')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Sender ID")?></label>
	      </div>  
     </div>
  </div>
  
    <div class="row">     
     <div class="col s6">     
       <div class="input-field">
        <?php 
		  echo CHtml::dropDownList('msg91_route',
		  getOptionA('msg91_route'),array(
		    'default'=>"Default",
		    1=>t("Promotional"),
		    4=>t("Transactional SMS"),
		  ));
        ?>
	    <label><?php echo t("Route")?></label>
       </div>     
     </div>
     
       <div class="col s6">
        <?php echo CHtml::checkBox('msg91_unicode',
	      getOptionA('msg91_unicode')==1?true:false
	      ,array(
	        'id'=>"msg91_unicode",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="msg91_unicode"><?php echo t("Use Unicode")?></label>	      
     </div>
     
    </div> 
    <!--MSG91-->   
    
    
    <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
     
     
     <button type="button" class="btn send-test-sms" style="margin-left:50px;"> 
      <?php echo t("Send TEST SMS")?>
     </button>
     
    </div>
   
   </form>  
  
 </div> <!--card-content-->
</div> <!--card-->