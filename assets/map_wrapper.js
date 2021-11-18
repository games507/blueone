var map;
var map_marker = [];
var map_bounds = [];

initMap = function(){
	$provider = map_provider;	
	
	switch($provider){
		case "mapbox":

		  map = L.map( map_div ,{ 
			scrollWheelZoom:true,
			zoomControl:true,
		 }).setView([map_lat,map_lng], 5 );
	 
		  L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token='+map_token, {		    
		    maxZoom: 18,
		    id: 'mapbox/streets-v11',	    
		  }).addTo(map);	 
		
		break;
		
		default:		  
		  map = new GMaps({
			  div: '.'+ map_div,
			  lat: map_lat,
			  lng: map_lng	,
			  zoom: 5,			  
		  }); 
		break;
	}
	
};