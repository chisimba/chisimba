/* 
	imageScale plugin
	Description: Resizes images to a specifid size and overlays them with a hover 
				 tooltip containing link to the original full size image.
	Original resize plugin code from: http://ditio.net/2010/01/02/jquery-image-resize-plugin/
	Modified by: Harshal Joshi (harshalj@live.com)
        Modified for Chisimba by Derek Keats (derek@dkeats.com)
	
*/

(function ($) {
        // Check if an object has an attibute defined
        $.fn.hasAttr = function(name) {
            return this.attr(name) !== undefined;
        };

        // The image scaling methods
	$.fn.imageScale = function (options) {

		var settings = $.extend({
			scale: 1,
			maxWidth: null,
			maxHeight: null
		}, options);

		if ($("#ImageScaledDiv").length == 0) { // Inject hover div if does not exist in DOM. 
			jQuery(document.body).append('<div id="ImageScaledDiv">This image has been scaled to fit. <br />' +
				'<a id="ImageScaledLink"  href="" target="_blank">Click here to see the original image.</a></div>');
		}

		var hiddenDiv = $("#ImageScaledDiv");

		/* Style for hover div */
		hiddenDiv.css({ 'position': 'absolute', 'display': 'none', 'margin': '0px', 'background': 'white',
			'border': 'solid black 1px', 'padding': '5px', 'vertical-align': 'middle', 'display': 'none'
		});

		return this.each(function () {

			if (this.tagName.toLowerCase() != "img") {
				// Only images can be resized
				return $(this);
			}

                        // Check for existing width / height & remove them if
                        //     the image needs resizing -- Added by DWK
                        if(jQuery(this).hasAttr('width')) {
                            var tmpWidth = jQuery(this).attr('width');
                            if (settings.maxWidth != null) {
                                if (settings.maxWidth < tmpWidth) {
                                    //alert('Width: ' + tmpWidth+", should be: "+settings.maxWidth);
                                    jQuery(this).removeAttr('width');
                                    jQuery(this).removeAttr('height');
                                    jQuery(this).css("width",null);
                                    jQuery(this).css("height",null);
                                }
                            }
                            
                        }

			var width = this.naturalWidth;
			var height = this.naturalHeight;


			if (!width || !height) {
				// IE fix
				var img = document.createElement('img');
				img.src = this.src;

				width = img.width;
				height = img.height;
			}

			if (settings.scale != 1) {
				width = width * settings.scale;
				height = height * settings.scale;
			}

			var pWidth = 1;
			if (settings.maxWidth != null) {
				pWidth = width / settings.maxWidth;
			}
			var pHeight = 1;
			if (settings.maxHeight != null) {
				pHeight = height / settings.maxHeight;
			}
			var reduce = 1;

			if (pWidth < pHeight) {
				reduce = pHeight;
			} else {
				reduce = pWidth;
			}

			if (reduce < 1) {
				reduce = 1;
			}

			var newWidth = width / reduce;
			var newHeight = height / reduce;

			/*
			Image is being scaled, show the hidden div on top of the image with a link to view the original image.
			*/
			if (reduce > 1) {


				$(this).hover(function (e) {
					//get the position of the placeholder element
					var pos = $(this).offset();
					var width = $(this).width();

					$(hiddenDiv).css({ "left": (pos.left) + "px", "top": (pos.top) + "px" });
					$(hiddenDiv).show();

					var imageUrl = $(this).attr('src');
					$('#ImageScaledLink').attr('href', imageUrl);

				},
				function (e) {
					var link = $('#ImageScaledLink');

					if (hiddenDiv[0] != e.relatedTarget && link[0] != e.relatedTarget)
						$(hiddenDiv).hide();
				});

				$(hiddenDiv).mouseout(function (e) {
					var mouseX = e.pageX;
					var mouseY = e.pageY;
					var imageTop = $(this).offset().top;
					var imageLeft = $(this).offset().left;

					if (mouseX <= imageLeft || mouseY <= imageTop)
						$(hiddenDiv).hide();
				});
			}

			return $(this)
				.attr("width", newWidth)
				.attr("height", newHeight);

		});
	}
})(jQuery);

