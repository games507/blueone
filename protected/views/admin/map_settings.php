<form id="frm" method="POST" onsubmit="return false;">
<?php echo CHtml::hiddenField('action','saveMapSettings')?>

<div class="card">
 <div class="card-content">
   
   <h5><?php echo t("Map API Keys")?></h5>  
   
    <div class="row">
     <div class="col s6">
      <div class="input-field">
        <?php echo CHtml::dropDownList('map_provider',
        getOptionA('map_provider')
        ,AdminFunctions::mapProviderList())?>
	    <label><?php echo t("Choose Map Provider")?></label>
      </div>
     </div>
     
     <div class="col s6">
        <div class="input-field">
	        <?php echo CHtml::dropDownList('map_default_country',
	        getOptionA('map_default_country')
	        ,(array)$country_list)?>
		    <label><?php echo t("Default Map Country")?></label>
	      </div>
     </div>
     
    </div>
    
    
    <h5><?php echo t("Google")?></h5>
    
    <div class="row">        
     <div class="col s12">
      <div class="input-field">
        <?php echo CHtml::textField('google_api_key',
        getOptionA('google_api_key'))?>
	    <label><?php echo t("Google Api Key")?></label>
      </div>
     </div>         
    </div> 
    
  
    <div class="row">        
     <div class="col s12">
      <div class="input-field">
        <?php echo CHtml::textField('google_geo_api_key',
        getOptionA('google_geo_api_key'))?>
	    <label><?php echo t("Geocoding API Key")?></label>
      </div>
     </div>         
    </div> 
    
     
     <div class="col s12">
        <div class="input-field">
	        <?php echo CHtml::dropDownList('google_distance_method',
	        getOptionA('google_distance_method')
	        ,array(
	          'driving'=>t("driving"),
	          'transit'=>t("transit"),
	          'walking'=>t("walking"),
	          'bicycling'=>t("bicycling"),
	        ))?>
		    <label><?php echo t("Method")?></label>
	      </div>
     </div>
    
    
    <br/>
    <h5><?php echo t("Mapbox")?></h5>
    
    <div class="row">
     <div class="col s12">
      <div class="input-field">
        <?php echo CHtml::textField('mapbox_access_token',
        getOptionA('mapbox_access_token'))?>
	    <label><?php echo t("Mapbox Access Token")?></label>
      </div>
     </div>
    </div> 
    
    <div class="row">
      <div class="col s12">
        <div class="input-field">
	        <?php echo CHtml::dropDownList('mapbox_method',
	        getOptionA('mapbox_method')
	        ,array(
	          'driving'=>t("driving"),
	          'cycling'=>t("cycling"),
	          'walking'=>t("walking"),	          
	        ))?>
		    <label><?php echo t("Method")?></label>
	      </div>
     </div>
    </div>
    
    <div class="row">
     <div class="col s6">
       &nbsp;
        <!--<?php echo CHtml::checkBox('map_use_curl',
	      getOptionA('map_use_curl')==1?true:false
	      ,array(
	        'id'=>"map_use_curl",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	   <label for="map_use_curl"><?php echo t("Enabled Curl")?></label>	-->
     </div>
     
     <div class="col s6">
       <a href="<?php echo Yii::app()->createUrl('/admin/test_map_api')?>" target="_blank" class="btn blue">
         <?php echo t("Test Map API")?>
       </a>
     </div>
     
    </div>
    
    
    
       
   
   <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
    </div>
   
  </div> <!--card content-->
</div> <!--card-->
</form>
