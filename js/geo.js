var bpgeoGeocoder;

function buddyPressGeoOnload() {
	bpgeoGeocoder = new google.maps.Geocoder();
	
	if ( jQuery( "div.field_latitude" ).length > 0 ) {
		jQuery( "#profile-group-edit-submit").click( 
			function() { 
				var l = jQuery( "div.field_city-state input" ).attr( "value" );
				buddyPressGeoDetermineLatLatFromAdddress( l, function( lat, lon ) { 				        				 
					jQuery( "div.field_latitude input" ).attr( "value", lat );
					jQuery( "div.field_longitude input" ).attr( "value", lon );
					
					jQuery( "#profile-edit-form").submit();
				});
				
				return false;
			});
	}
	
	if ( jQuery( "#geo-submit" ).length > 0 ) {
		jQuery( "#geo-submit" ).click(
			function() {
				var dest = jQuery( "form#geo-form" ).attr( "method" );
				var loc = jQuery( "input#geo-search-field" ).attr( "value" );
				var distance = jQuery( "select#geo-distance" ).attr( "value" );
				
				buddyPressGeoDetermineLatLatFromAdddress( loc, function( lat, lon ) {
					var newLocation = bpGeoLocation + "/?type=near&lat=" + lat + "&lon=" + lon + "&friendly=" + loc + "&within=" + distance;
					window.location = newLocation;
				} );
				
				return false;
			}
		);		
	}
	
	if ( jQuery( 'a#bp-geo-rebuild' ).length > 0 ) {
		jQuery( 'a#bp-geo-rebuild' ).click( function() { 
			buddyPressGeoRebuild( 0, '', 0, 0, 0 );
			return false;
		});
	}
}

function buddyPressGeoRebuild( num, lastLoc, lastUser, lastLat, lastLon ) {
	var someUrl = "/?bp_geo_index=" + num + "&bp_geo_last_loc=" + lastLoc + "&bp_geo_last_lat=" + lastLat + "&bp_geo_last_lon=" + lastLon + "&bp_geo_last_user=" + lastUser + "&_ajax_nonce=" + bpGeoNonce;
	jQuery.get( someUrl, function( result ) {
		var jsonResult = eval( "(" + result + ")" );
				
		if ( jsonResult.doneStatus == 0 ) {
			jQuery( 'p#ajax-location').hide()
			jQuery( "p#ajax-done").fadeIn();
			
			return;
		}
		
		jQuery( '#bp-geo-num' ).hide().html( jsonResult.userNumPlusOne ).fadeIn( 100 );
		jQuery( 'p#ajax-location').show().find( "em" ).html( jsonResult.userLocation );
					
		buddyPressGeoDetermineLatLatFromAdddress( jsonResult.userLocation, function( lat, lon ) {
			buddyPressGeoRebuild( jsonResult.nextUser, jsonResult.userLocation, jsonResult.userNum, lat, lon );
		});
	} );
}

function buddyPressGeoDetermineLatLatFromAdddress( address, callback ) {
	if (bpgeoGeocoder) {
      bpgeoGeocoder.geocode( { 'address': address }, 
      	function(results, status) {
        		if (status == google.maps.GeocoderStatus.OK) {
        				 var l = String(results[0].geometry.location);
        				 l = l.substring( 1, l.length - 1 );
        			  	 var latlngStr = l.split(", ",2);
					    var lat = latlngStr[0];
					    var lng = latlngStr[1];
					    
					    callback( lat, lng );
				}	
      	}
      );
	}		
}

jQuery( document ).ready( function() { buddyPressGeoOnload(); } );

