
<div class="card">
 <div class="card-content">
 
 <form id="frm" method="POST" onsubmit="return false;">
 <?php echo CHtml::hiddenField('action','saveMobileSettings')?>
 
  <h5><?php echo t("Mobile API URL")?></h5>  
   <p class="rounded" style="background:#009688;color:#fff;display:table;padding:3px 5px;"><?php echo Yii::app()->getBaseUrl(true)."/api"?></p>   
   
   <h5 class="top30"><?php echo t("Mobile API Key")?></h5>  
   
    <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('mobile_api_key',
	       getOptionA('mobile_api_key')
	       ,array(
	         'class'=>"validate",	        
	         ))?>
	       <label><?php echo t("Your Mobile API key")?></label>
	      </div>      
	      <p class="grey lighten-5"><?php echo t("This fields is optional if you want to secure api fill this fields")?></p>
     </div>
   </div>
   
   
    <div class="row">
     <div class="col s12">

      <div class="input-field">	    
	       <?php echo CHtml::textField('fcm_server_key',
	       getOptionA('fcm_server_key')
	       ,array(
	         'class'=>"validate",	        
	         ))?>
	       <label><?php echo t("Server Key")?></label>
	      </div>          
     
     </div>
   </div>
   
   <div class="row">
   
     <div class="col s6">
         <a id="upload_services_json" class="waves-effect blue lighten-1 btn"><?php echo t("Service accounts private key")?></a>
         
         <div id="progressBar"></div>
         <div id="progressOuter"></div>
         <div id="msgBox"></div>         
         
         <br/>
         <p>
	     <a href="https://youtu.be/D4pfWT_2rKA" target="_blank">
	      <?php echo t("How to get your Service accounts private key")?>
	     </a>
	     </p>     
         
     </div>  
        
     
     
      <div class="col s6">
            
       <div class="json_preview">         
            <?php if(!empty($json)):?>    
             <?php echo $json;?>      
             <?php echo CHtml::hiddenField('services_json',$json)?>
            <?php endif?>
       </div>       
       
       <?php if(!empty($json)):?>       
        <p class="json_remove_preview">
         <a class="json_remove" href="javascript:;"><?php echo t("Click here")?> </a> <?php echo t("to remove")?>
        </p>
       <?php endif;?>
         
     </div>
     
   </div>
   

 <br/>
   
    <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
    </div>
 
 </form>
 
 </div> <!--content-->
</div> <!--card-->