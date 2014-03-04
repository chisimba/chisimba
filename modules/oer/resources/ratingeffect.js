		jQuery(function(){
			jQuery("#rat").children().not(":radio").hide();
			
			// Create stars
			jQuery("#rat").stars({
				// starWidth: 28, // only needed in "split" mode
				cancelShow: false,
				callback: function(ui, type, value)
				{
					// Hide Stars while AJAX connection is active
					jQuery("#rat").hide();
					jQuery("#loader").show();
					// Send request to the server using POST method
					/* NOTE: 
						The same PHP script is used for the FORM submission when Javascript is not available.
						The only difference in script execution is the returned value. 
						For AJAX call we expect an JSON object to be returned. 
						The JSON object contains additional data we can use to update other elements on the page.
						To distinguish the AJAX request in PHP script, check if the jQuery_SERVER['HTTP_X_REQUESTED_WITH'] header variable is set.
						
					*/ 
					jQuery.post("?module=oer&action=rateproduct", {rate: value}, function(db)
					{
						// Select stars to match "Average" value
						ui.select(Math.round(db.avg));                                                
						
						// Update other text controls...
						jQuery("#avg").text(db.avg);
						jQuery("#votes").text(totalvotestext+": "+db.votes);
						
						// Show Stars
						jQuery("#loader").hide();
						jQuery("#rat").show();

					}, "json");
				}
			});
		});
