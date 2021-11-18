
<div class="card">
 <div class="card-content">
 
  <h4><?php echo t("Geocoding results")?>:</h4>
  <?php if(!empty($message)):?>
     <p class="text-danger"><?php echo $message?></p>
  <?php else :?>
    <?php dump($resp);?>
  <?php endif;?>
  
  <h4><?php echo t("Map results")?>:</h4>
  
  <?php if($provider=="google"):?>
  <p class="grey lighten-5"><?php echo t("If you cannot see the map or the map says This page can't load Google Maps correctly below it means your api is not working, enabled the api Google Maps JavaScript API")?></p>
  <?php else :?>
  
  <p class="grey lighten-5"><?php echo t("If you cannot see the map below please check your mapbox token")?></p>
  
  <?php endif;?>
  
  <div class="map" id="map"></div>
 
 </div>
</div> 