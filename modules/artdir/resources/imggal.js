
			$(function(){
				var exitedFullScreen = false;
				
				$('#images').exposure({showControls : false,
					fullScreen : true,
					onExitFullScreen : function(target, mask) {
						target.hide();
			           	mask.hide();
			           	fadeOutSelectedThumb();
			           	exitedFullScreen = true;
					},
					pageSize : 5,
					viewFirstImage : false,
					onThumb : function(thumb) {
						var li = thumb.parents('li');				
						var fadeTo = li.hasClass('active') ? 1 : 0.3;
						
						thumb.css({display : 'none', opacity : fadeTo}).stop().fadeIn(200);
						
						thumb.hover(function() { 
							thumb.fadeTo('fast',1); 
						}, function() { 
							li.not('.active').children('img').fadeTo('fast', 0.3); 
						});
					},
					onImage : function(image, imageData, thumb) {
						// Check if wrapper is hovered.
						var hovered = $('.exposureWrapper').hasClass('exposureHover');
						
						if (exitedFullScreen) {
							// Remove the previous image.
							$('.exposureWrapper > .exposureLastImage').remove();
						} else {
							// Fade out and remove the previous image.	
							$('.exposureWrapper > .exposureLastImage').stop().fadeOut(500, function() {
								$(this).remove();
							});
						}
						
						exitedFullScreen = false;
						
						// Resize and reposition image to fit center of window.
						$.exposure.fitToWindow();
						
						// Fade in the current image.
						image.hide().stop().fadeIn(1000);
						
						var hasCaption = function() {
							var caption = imageData.find('.caption').html();
							var extra = imageData.find('.extra').html();
							return (caption && caption.length > 0) || (extra && extra.length > 0);
						}
						
						var showImageData = function() {
							imageData.stop().show().animate({bottom:0+'px'},{queue:false,duration:160});
						}
						var hoverOver = function() {
							$('.exposureWrapper').addClass('exposureHover');
							// Show image data as an overlay when image is hovered.
							var hasCpt = hasCaption();
							
							if (hasCpt) {
								showImageData.call();
							}
						};
						
						var hideImageData = function() {
							var imageDataBottom = -imageData.outerHeight();
							imageData.stop().show().animate({bottom:imageDataBottom+'px'},{queue:false,duration:160});
						}
						var hoverOut = function() { 
							$('.exposureWrapper').removeClass('exposureHover');
							// Hide image data on hover exit.
							if (hasCaption()) {
								hideImageData.call();
							}
						};
						
						$('.exposureWrapper').hover(hoverOver,hoverOut);
						imageData.hover(hoverOver,hoverOut);
												
						if (hovered) {
							if (hasCaption()) {
								showImageData.call();
							} else {
								hideImageData.call();	
							}	
						}
		
						if ($.exposure.showThumbs && thumb && thumb.length) {
							fadeOutSelectedThumb();		
							thumb.fadeTo('fast', 1).addClass('selected');
						}
					}
				});
				
				function fadeOutSelectedThumb() {
					$('.exposureThumbs img.selected').stop().fadeTo(200, 0.3, function() { $(this).removeClass('selected'); });
				}
			});
