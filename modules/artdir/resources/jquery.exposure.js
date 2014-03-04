/*
* Exposure (http://http://exposure.blogocracy.org/)
* Copyright (c) 2010 Kristoffer Jelbring
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/
(function($) {	
/**
* @name Exposure
* @author Kristoffer Jelbring (kris@blogocracy.org)
* @version 0.9.1
*
* @type jQuery
* @cat plugins/Media
*
* @desc Turn a simple HTML list into a rich and smart photo viewer that handles very large amounts of photos.
*
* @example $('#images').exposure({options});
*
* @options
*	target:	(selector string) Where to insert the image being displayed. Defaults to '#exposure'. If no target is found, one will be created.
*	showThumbs:	(boolean) Display thumbnails or not. Defaults to true. 
*	showControls: (boolean) Display paging controls or not. Defaults to true, but will be set to false if missing controlsTarget or if carouselControl is set to true.
*	imageControls: (boolean) Switch paging controls to use images instead of pages. Defaults to false.
*	controls: (object) Display only certain paging controls. All controls default to true. Usage example: controls : { prevNext : true, pageNumbers : true, firstLast : false }
*	carouselControls: (boolean) Enable carousel type controls instead of the classic paging type controls. Defaults to false, but will be set to false if showThumbs is also set to false.
*	enableSlideshow: (boolean) Enable slideshow. Defaults to true.
*	slideshowControlsTarget: (selector string) Where to insert the slideshow controls. Defaults to null.
*	autostartSlideshow: (boolean) Automatically start the slideshow when the gallery is loaded. Defaults to false.
*	slideshowDelay: (number) Delay for each slide in the slideshow (in milliseconds). Defauts to 3000.
*	onSlideshowPlayed: (function) Callback funcation that is called when the slideshow is played.
*	onSlideshowPaused: (function) Callback funcation that is called when the slideshow is paused.
*	showCaptions: (boolean) Display captions or not. Captions are added by setting a title attribute on the items in the list.
*	showExtraData: (boolean) Display extra image data or not. This data is added by inserting inner HTML to the items in the list.
*	dataTarget: (selector string) Where to insert captions and extra image data. Defaults to null, in which case the data container will appended to the main Exposure target.
*	controlsTarget: (selector string) Where to insert the paging controls. Defaults to null.
*	onThumb: (function) Callback function that is called when a thumbnail is displayed.
*	onImage: (function) Callback function that is called when an image is displayed. Defaults to removing the previous image.
*	onCarousel: (function) Callback function that is called right before the image carousel is updated.
*	onNext: (function) Callback function that is called when nextImage is called.
*	onPrev: (function) Callback function that is called when prevImage is called.
*	onPageChanged: (function) Callback function that is called when goToPage is called. Is not called when carouselControls is set. Defaults to showing all thumbnails on the current page.
*	onPagingLink: (function) Callback function that is called when a new paging link has been added. Defaults to returning the link.
*	separatePageBrowsing: (boolean) Enable separate page browsing (change page without changing the image being viewed). Defaults to false.
*	loop: (boolean) Start over when last image is reached.
*	onEndOfLoop: (function) Callback function that is called when the last image is reached and loop option is set to false.
*	viewFirstImage: (boolean) Enable automatic showing of the first image in the gallery when the gallery is loaded. Defaults to true.
*	pageSize: (number) Maximum number of images (thumbnails) per page. Defaults to 5.
*	visiblePages: (number) Maxium number of pages visible in paging.
*	preloadBuffer: (number) Maximum number of images to keep in load queue at any given time. Defaults to 3.
*	keyboardNavigation: (boolean) Enable keyboard navigation. Defaults to true.
*	clickingNavigation: (boolean) Enable browsing by clicking the image being shown. Defaults to true.
*	fixedContainerSize: (boolean) Enable a fixed size target element (set the size using CSS) instead of one that adapts to the size of the current image. Defaults to false.
*	maxWidth: (number) Maximum image width in the gallery (larger images will be downscaled). Defaults to null.
*	maxHeight: (number) Maximum image height in the gallery (larger images will be downscaled). Defaults to null.
*	stretchToMaxSize: (boolean) Stretch all images to maxWidth and maxHeight. Defaults to false.
*	fullScreen: (boolean) Stretch all images to be viewn in full screen. Defaults to false.
*	onEnterFullScreen: (function) Callback function that is called when entering full screen mode. Defaults to showing background mask.
*	onExitFullScreen: (function) Callback function that is called when exiting full screen mode. Defaults to hiding target and background mask.
*	showThumbToolTip: (boolean) Display captions as thumbnail tooltips or not. Defaults to true.
*	onEmpty: (function) Called when the gallery is empty. Defaults to removing controls and targets and to hiding the list element that the plugin is called on.
*	onInit: (function) Called when the gallery has been initialized.
*	allowDuplicates: (boolean) Allow the same image to be added more than once. Defaults to true.
*	jsonSource: (JSON data string/URL to JSON data/JSON object) Load additional images from an external source using JSON. Defaults to null.
*/
var $$ = $.fn.exposure = function($args) {
	
	var v = "0.9.1";
	var i;

	var $defaults = {
		target : '#exposure',
		showThumbs : true,
		showControls : true,
		imageControls : false,
		controls : {
				prevNext : true,
				firstLast : true,
				pageNumbers : true				
		},
		carouselControls: false,
		enableSlideshow : true,
		slideshowControlsTarget : null,
		autostartSlideshow : false,
		slideshowDelay : 3000,
		onSlideshowPlayed : function() {},
		onSlideshowPaused : function() {},
		showCaptions : true,
		showExtraData : true,
		dataTarget : null,
		controlsTarget : null,
		onThumb : function(thumb) {},
		onImage : function(image, imageData, thumb) {
			$('.exposureWrapper > .exposureLastImage').remove();
		},
		onCarousel : function(firstImage, lastImage) {},
		onNext : function() {},
		onPrev : function() {},
		onPageChanged : function() {
			$('.exposureThumbs li.current').show().each(function(i) {
				var imageHeight = $(this).find('img').height();
				if (imageHeight > 0) {
					$(this).height(imageHeight);	
				}
			});
		},
		onPagingLink : function(link) {
			return link;
		},
		separatePageBrowsing : false,
		loop : true,
		onEndOfLoop : function() {},
		pageSize : 5,
		viewFirstImage : true,
		visiblePages : 5,
		preloadBuffer : 3,
		keyboardNavigation : true,
		clickingNavigation : true,
		fixedContainerSize : false,
		maxWidth : null,
		maxHeight : null,
		stretchToMaxSize : false,
		fullScreen : false,
		onEnterFullScreen : function(mask) {
			mask.show();	
		},
		onExitFullScreen : function(target, mask) {
			target.hide();
            mask.hide();		
		},
		showThumbToolTip : true,
		onEmpty : function() {
			$('.exposureThumbs').hide();
			$($.exposure.target).remove();
			if ($.exposure.showControls) {
				$($.exposure.controlsTarget).remove();				
			}
			if ($.exposure.slideshowControlsTarget) {
				$($.exposure.slideshowControlsTarget).remove();
			}	
		},
		onInit : function() {},
		allowDuplicates : true,
		jsonSource : null
	};

	var opts = $.extend($defaults, $args);
	for (i in opts) {
		if ($$.defined($defaults[i])) {
			$.exposure[i] = opts[i];
		}
	}
	
	if (!$($.exposure.target).length) {
		// The target element is missing so it needs to be created.
		$('<div id="exposure"></div>').insertBefore($(this));	
	}
	
	var wrapper = $('<div class="exposureWrapper"></div>');
	var target = $($.exposure.target).addClass('exposureTarget').append(wrapper);
	
	if ($.exposure.showCaption || $.exposure.showExtraData) {
		// Determine which image data to display (caption and/or additional data).
		var dataElementsHtml = '';
		if ($.exposure.showCaptions) {
			dataElementsHtml += '<div class="caption"></div>';
		}
		if ($.exposure.showExtraData) {
			dataElementsHtml += '<div class="extra"></div>';
		}
		
		// Append image data container.
		var dataElements = $(dataElementsHtml);
		if (dataElements.length) {
			if ($.exposure.dataTarget && $($.exposure.dataTarget).length) {
				$($.exposure.dataTarget).addClass('exposureData').append(dataElements);
			} else {
				$.exposure.dataTarget = null;
				target.append($('<div class="exposureData"></div>').append(dataElements));
			}
		}
	}
	
	// Don't use carousel controls if not showing thumbs.
    if (!$.exposure.showThumbs) {
        $.exposure.carouselControls = false;
    }
	
	// Don't show paging controls if using carousel controls, or if there is no controls target or if all individual controls have been turned off.
	if ($.exposure.carouselControls || !$.exposure.controlsTarget || (!$.exposure.controls.prevNext && !$.exposure.controls.firstLast && !$.exposure.controls.pageNumbers)) {
		$.exposure.showControls = false;
	}
	
	// Render controls.
	if ($.exposure.showControls) {
		$($.exposure.controlsTarget).addClass('exposureControls').each(function() {
			if ($.exposure.controls.firstLast) { $(this).append($('<a class="exposureFirstPage" href="javascript:void(0);">' + $.exposure.texts.first + '</a>').click($.exposure.first)); }
			if ($.exposure.controls.prevNext) { $(this).append($('<a class="exposurePrevPage" href="javascript:void(0);">' + $.exposure.texts.previous + '</a>').click($.exposure.prev)); }
			if ($.exposure.controls.pageNumbers) { $(this).append($('<div class="exposurePaging"></div>')); }
			if ($.exposure.controls.prevNext) { $(this).append($('<a class="exposureNextPage" href="javascript:void(0);">' + $.exposure.texts.next + '</a>').click($.exposure.next)); }
			if ($.exposure.controls.firstLast) { $(this).append($('<a class="exposureLastPage" href="javascript:void(0);">' + $.exposure.texts.last + '</a>').click($.exposure.last)); }
		});
	}
	
	// Only render slideshow controls if there is a slideshow controls target.
	if ($.exposure.enableSlideshow && $.exposure.slideshowControlsTarget) {
		$($.exposure.slideshowControlsTarget).addClass('exposureSlideshowControls').each(function() {
			$(this).append($('<a class="exposurePlaySlideshow" href="javascript:void(0);">' + $.exposure.texts.play + '</a>').click($.exposure.playSlideshow));
			$(this).append($('<a class="exposurePauseSlideshow" href="javascript:void(0);">' + $.exposure.texts.pause + '</a>').hide().click($.exposure.pauseSlideshow));
		});
	}
	
	// Bind keys for navigation (using Hotkeys Plugin).
	if ($.exposure.keyboardNavigation) {
		$(document).bind('keyup', 'left', $.exposure.prevImage);
		$(document).bind('keyup', 'right', $.exposure.nextImage);
		$(document).bind('keyup', 'ctrl+left', $.exposure.prevPage);
		$(document).bind('keyup', 'ctrl+right', $.exposure.nextPage);
		$(document).bind('keyup', 'up', $.exposure.lastImage);
		$(document).bind('keyup', 'down', $.exposure.firstImage);
		$(document).bind('keyup', 'ctrl+up', $.exposure.lastPage);
		$(document).bind('keyup', 'ctrl+down', $.exposure.firstPage);
		if ($.exposure.enableSlideshow) {
			$(document).bind('keyup', 'space', $.exposure.toggleSlideshow);
		}
	}
	
	if ($.exposure.fullScreen) {
		$(window).resize($.exposure.fitToWindow);
		$('<div class="exposureMask"></div>').click($.exposure.exitFullScreen).insertAfter(target);
		if ($.exposure.keyboardNavigation) {
			$(document).bind('keyup', 'esc', $.exposure.exitFullScreen);
		}
	}
	
	var jsonImages = null;
	
	if ($.exposure.jsonSource) {
		if ($$.object($.exposure.jsonSource)) {
			jsonImages = $.exposure.jsonSource;
		} else if ($.exposure.jsonSource.length) {
			if ($$.startsWith($.exposure.jsonSource, "http://") || $$.startsWith($.exposure.jsonSource, "https://")) {
				// Fetch JSON images using AJAX from specified URL source.
				jsonImages = $.ajax({url : $.exposure.jsonSource,
					type : 'GET', 
					async : false
				}).responseText;
			} else {
				jsonImages = $.exposure.jsonSource;	
			}
		}
	}
	
	// Return "this" to maintain chainability.
	return this.addClass('exposureThumbs').each(function() {
		var i;
		if (jsonImages) {
			var images = $$.object(jsonImages) ? jsonImages : $.parseJSON(jsonImages);
			if (images && images.data) {
				// Append images fetched from JSON source to the list of images.
				for (i in images.data) {
					var photo = images.data[i];
					if (photo.source && photo.source.length) {
						var item = $('<li></li>');
						var link = $('<a></a>').attr('href', photo.source);
						if (photo.thumb_source && photo.thumb_source.length) {
							var thumb = $('<img />').attr('src', photo.thumb_source);
							if (photo.caption && photo.caption.length) {
								thumb.attr('title', photo.caption);
							}
							link.append(thumb);
						} else if (photo.caption && photo.caption.length) {
							link.attr('title', photo.caption);
						}
						item.append(link);					
						if (photo.extra_data && photo.extra_data.length) {
							item.append($(photo.extra_data));
						}
						$(this).append(item);	
					}
				}
			}
		}
		
		var foundImage = false;
		var foundThumb = false;
		
		if ($(this).children('li').length) {	
			var selectedIndex = null;
				
			$(this).show().children('li').each(function() {
				foundImage = true;
				
				// The a tag contains all the needed information about the image.
				var a = $(this).find('a');
				if (a.length) {
					// Use only the first matching link.
					a = $(a[0]);
							
					var src = a.attr('href');
					var img = a.find('img');
					
					// Get caption and thumbnail source from either nested img tag or from rel attribute.
					var thumbSrc = img.length ? img.attr('src') : a.attr('rel');		
					var caption = img.length ? img.attr('title') : a.attr('title');
					
					var isSelected = a.hasClass('selected') && !selectedIndex;
										
					// Remove link and extract additional image data.
					a.remove();		
					var thumbData = $(this).html();
					
					if (thumbSrc) {
						foundThumb = true;
					}
					
					// All information extracted, remove original list entry.
					$(this).remove();
					
					// Add image to list of images.
					var imageIndex = $$.newImage(src, thumbSrc, caption, thumbData);
					
					if (imageIndex > -1) {
						if (isSelected) {
							selectedIndex = imageIndex;
						}
						
						if ($$.loadQueue.length < $.exposure.preloadBuffer) {
							// Preload buffer hasn't been filled yet, add image to load queue.				
							$$.addToLoadQueue(imageIndex);
						}
					}
				} else {
					// Just remove this empty entry.
					$(this).remove();
				}
			});
			
			if (!$.exposure.showThumbs) {
				// Thumbnails are turned off, change page size to 1.
				$.exposure.pageSize = 1;
				
				// Remove the thumbnails container.
				$('.exposureThumbs').remove();
			}
			
			if (foundImage) {
				// Start preloading the first image.
				$$.preloadNextInQueue();
				
				$$.createPaging();
				
				if (selectedIndex) {
					$.exposure.goToPage($.exposure.pageNumberForImage(selectedIndex));
					$.exposure.viewImage(selectedIndex);
				} else {				
					// View the first page (and the first image).
					$.exposure.goToPage(1);
				}
				
				if ($.exposure.enableSlideshow && $.exposure.autostartSlideshow) {
					$.exposure.playSlideshow();
				}
			} else {
				$.exposure.onEmpty();	
			}
		} else {
			$.exposure.onEmpty();
		}
		
		$.exposure.onInit();
		
		$$.initialized = true;
	});
};

// Private functions and properties. These are only for internal use.

/**
* Check if a variable is defined.
*
* @param v Variable to check.
*/
$$.defined = function(v) {
	return typeof v !== 'undefined';
};

/**
* Check if a variable is an object.
*
* @param v Variable to check.
*/
$$.object = function(v) {
	return typeof v === 'object';	
};

/**
* Check if a string starts with another string.
*
* @param s1 String to check.
* @param s2 String to look for.
*/
$$.startsWith = function(s1, s2) {
	if (s1 && s2) {
		return s1.match("^"+s2) === s2;
	}
	return false;
};

/**
* Calculate the differance in outerwidth and width of an element.
*
* @param el The element to check.
* @returns Width differance.
*/
$$.widthDiff = function(el) {
	return el ? el.outerWidth(true)-el.width() : 0;
}; 

/**
* Calculate the differance in outerHeight and height of an element.
*
* @param el The element to check.
* @returns Height differance.
*/
$$.heightDiff = function(el) {
	return el ? el.outerHeight(true)-el.height() : 0;
};

/**
* Value object representing an image in the viewer.
*
* @param src Source to the full size image.
* @param thumb Source to thumbnail version of the image.
* @param caption Image caption.
* @param data Extra image data.
*/
$$.Image = function(src, thumb, caption, data) {
	this.src = src;
	this.thumb = thumb;
	this.caption = caption;
	this.data = data;
	this.loaded = false;
};

/**
* All the images in the viewer. Holds an array of Image objects that are filled up when the plugin is loaded.
*/
$$.images = [];

/**
* All the image sources that's been previously added to the viewer.
*/
$$.sources = {};

/**
* Create a new Image object and add it to images array.
*
* @param src Source to the full size image.
* @param thumb Source to thumbnail version of the image.
* @param caption Image caption.
* @param data Extra image data.
* @returns Index of the new image.
*/
$$.newImage = function(src, thumb, caption, data) {
	var alreadyAdded = $$.defined($$.sources[src]);
	if (alreadyAdded && !$.exposure.allowDuplicates) {
		return -1;
	}
	var image = new $$.Image(src, thumb, caption, data);
	var imageIndex = $$.images.push(image) - 1;
	if (!alreadyAdded) {
		$$.sources[src] = imageIndex;
	}
	return imageIndex;
};

/**
* Initialization flag.
*/
$$.initialized = false;
		
/**
* Index of the image currently being viewed.
*/
$$.current = -1;

/**
* Deselect the image currently being viewed.
*/
$$.deselectCurrentImage = function() {
	$$.current = -1;
	$('.exposureThumbs li.active').removeClass('active');
};
		
/**
* The load queue, holds an array of indices of images to load.
*/
$$.loadQueue = [];
		
/**
* Add an image to the load queue.
*
* @param index Index of image to add.
*/
$$.addToLoadQueue = function(index) {
	if (!$$.loaded(index) && !$$.queued(index)) {
		$$.loadQueue.push(index);
	}
};
		
/**
* Check if a specific image exists in the load queue.
*
* @param index Index of image to check.
*/
$$.queued = function(index) {
	return $.inArray(index, $$.loadQueue) > -1;	
};
		
/**
* Check if a specific image has been loaded.
*
* @param index Index of image to check.
*/
$$.loaded = function(index) {
	var image = $.exposure.getImage(index);
	if (image !== null) {
		return image.loaded;
	}
	return false;
};
		
/**
* Find the next, not already loaded image, in the load queue. This function is recursive and will continue until
* an image is found, or until the queue is empty.
*/
$$.nextInLoadQueue = function() {
	var i;
	if ($$.loadQueue.length > 0) {
		var next = $$.loadQueue.shift();
		if ($$.loaded(next)) {				
			// Image already loaded, remove from load queue.
			i = $.inArray(index, $$.loadQueue);
			$$.loadQueue.splice(i, 1);
			
			// Find next in queue.
			return $$.nextInLoadQueue();
		}
		return next;
	}
	return null;
};
		
/**
* Preload the next image in the load queue.
*/	
$$.preloadNextInQueue = function() {
	if ($$.loadQueue.length > 0) {				
		var nextIndex = $$.nextInLoadQueue();
		if (nextIndex !== null) {
			$$.loadImage(nextIndex, $$.preloadNextInQueue);
		}
	}
};

/**
* Load a specific page.
*
* @param page Number of the page to load.
* @param imageToView Index of the image to view (defaults to viewing first image on page if this parameter isn't set).
*/
$$.loadPage = function(page, imageToView) {
	if ($$.validPage(page)) {
		
		// Calculate first and last images on this page.
		var last = page * $.exposure.pageSize;
		var first = last - $.exposure.pageSize;
		
		if (last > $$.images.length) {
			last = $$.images.length;
		}

		$$.pageTransition = true;
		
		$$.viewThumbs(first, last-1);			
		
		if (!$.exposure.separatePageBrowsing) {
			if (imageToView) {
				// Moving backwards, set the last image on the page as active.
				$.exposure.viewImage(imageToView);
			} else {
				if (page > 1 || ((page === 1 && $.exposure.viewFirstImage) || $$.initialized)) {
					// Set the first image on this page as active.			
					$.exposure.viewImage(first);
				}
			}
		}
		
		$$.pageTransition = false;
	}
};

/**
* Views thumbnails for a specific set of images (and creates them if needed).
*
* @param first Index of the first image to view.
* @param last Index of the last image to view.
*/
$$.viewThumbs = function(first, last) {
	var i;
	if ($.exposure.showThumbs) {
	
		// Go through images in set.
		for (i = first; i <= last; i++) {
			$$.viewThumb(i, i === first, i === last, true);
	    }
	    if (!$.exposure.carouselControls && $$.currentPage < $.exposure.numberOfPages()) {
			// Preload next page of thumbnails.
		    var firstNext = last+1;
		    var lastNext = last+$.exposure.pageSize;
		    if (lastNext >= $$.images.length) {
				lastNext = $$.images.length-1;
			}
		    
		    for (i = firstNext; i <= lastNext; i++) {
				var container = $$.viewThumb(i, i === firstNext, i === lastNext, false);
				if (container && container.length) {
					container.hide();
				}
		    }
	    }
	}
};

/**
* View thumbnail for a specific image (and create it if needed).
*
* @param index Index of the image to view.
* @param first If the image is the first on the page.
* @param last If the image is the last on the page.
* @param current If the image is a part of the current page.
*/
$$.viewThumb = function(index, first, last, currentPage) {
	// Make sure image index is in scope.
    if (index < 0) {
        index = $$.images.length + index;
    } else if (index >= $$.images.length) {
        index = index - $$.images.length;
    }

    var image = $$.images[index];
    // Find thumbnail container.
    var container = $.exposure.getThumb(index).parent();
    if (!container.length) {
        // Create a thumbnail if one doesn't already exist.
        container = $$.createThumbForImage(image, index);
        
        // Add page number as rel attribute.
        container.attr('rel', $.exposure.pageNumberForImage(index));
    }
    if (container.length) {
        // Append in the end of the container in order to save the ordering of the images.
        container.parent().append(container);

        if (first) {
            // Decorate thumbnail container for first image on page.
            container.addClass('first');
        } else {
            container.removeClass('first');
        }
        if (last) {
            // Decorate thumbnail container for last image on page.
            container.addClass('last');
        } else {
            container.removeClass('last');
        }
        
        if (currentPage) {
			if ($.exposure.carouselControls) {
				container.show();
			} else {
				container.addClass('current');	
			}
        }
    }
    
    return container;	
};
		
/**
* Load a specific image.
*
* @param index Index of image to load.
* @param onload Image onload callback function.
*/
$$.loadImage = function(index, onload) {
	var image = $.exposure.getImage(index);		
	var img = $('<img />').addClass('exposureImage');
	var i;
	if (image !== null) {
		image.loaded = true;
		if ($$.queued(index)) {
			// Since image already has been loaded, remove it from the load queue.
			i = $.inArray(index, $$.loadQueue);
			$$.loadQueue.splice(i, 1);
		}
		if (typeof onload === 'function') {
			img.load(onload);
		}
		img.attr('src', image.src);
	}
	return img;		
};

/**
* Create a thumbnail for a specific image.
*
* @param image Image object for the image.
* @param image Index of the image.
*/
$$.createThumbForImage = function(image, index) {
	if ($.exposure.showThumbs) {
		var thumb = $.exposure.getThumb(index);

		if (thumb === null || !thumb.length) {						
			// Create thumbnail container.
			var container = $('<li></li>');
			$('.exposureThumbs').append(container);
			
			// Create thumbnail img element.
			thumb = $('<img />');
			
			if (image.thumb) {
				thumb.attr('src', image.thumb);
			} else {
				// Create a thumbnail from the original image.
				thumb.attr('src', image.src);
				
				// Downscale the new thumbnail.
				var imageWidth = Math.ceil(thumb.width() / thumb.height() * container.height());
				var imageHeight = Math.ceil(thumb.height() / thumb.width() * container.width());		
				if (imageWidth < imageHeight) {
					thumb.css({height: 'auto', maxWidth: container.width()});
				} else {
					thumb.css({width: 'auto', maxHeight: container.height()});
				}
			}
			
			container.append(thumb.css('display', 'block'));					
			
			// Add image index and caption as attributes.
			thumb.attr('rel', index);
			if (image.caption && $.exposure.showThumbToolTip) {
				thumb.attr('title', image.caption);
			}
			
			// Save extra image data in thumbnail data.
			thumb.data('data', image.data);
			
			thumb.click(function() {
				// When a thumbnail is clicked, view full version of that image.
				$.exposure.viewImage(Number($(this).attr('rel')));
			});
			
			thumb.load(function() {
				// Set the height of the thumbnail container to the height of the thumbnail.
				var imageHeight = $(this).height();
				if (imageHeight > 0) {
					$(this).parent().height(imageHeight);
				}		
			});
			
			$.exposure.onThumb(thumb);
			
			return container;
		}
	}
	return null;
};

/**
* Number of the page currently being viewed.
*/
$$.currentPage = 1;
		
/**
* Check if a specific page number is a valid page number.
*
* @param page Page number to check.
*/
$$.validPage = function(page) {
	return page > 0 && page <= $.exposure.numberOfPages();
};

/**
* Create paging links.
*/
$$.createPaging = function() {	
	var i;
	if ($.exposure.showControls && $.exposure.controls.pageNumbers) {	
		// Create paging links.
		var stop = $.exposure.imageControls ? $.exposure.numberOfImages() : $.exposure.numberOfPages();
		$('.exposurePaging').each(function() {
			for (i = 1; i <= stop; i++) {
				$(this).append($$.newPagingLink(i));
			}
		});
	}	
};

/**
* Update paging links.
*/
$$.updatePaging = function(newActivePage) {
	if ($.exposure.showControls && $.exposure.controls.pageNumbers) {
		var current = $.exposure.imageControls ? $$.current+1 : $$.currentPage;
		$('.exposurePaging span.active').each(function() { 
			$(this).replaceWith($$.newPagingLink(current)); 
		});
		$('.exposurePaging a[rel="' + newActivePage + '"]').each(function() { 
			$(this).replaceWith($('<span>' + newActivePage + '</span>').addClass('active')); 
		});
		var pageCount = $.exposure.imageControls ? $.exposure.numberOfImages() : $.exposure.numberOfPages();
		if ($.exposure.visiblePages > 0 && pageCount > $.exposure.visiblePages) {
			var firstVisiblePage = newActivePage;						
			var lastVisiblePage = $.exposure.visiblePages;
			var flooredVisiblePages = Math.floor($.exposure.visiblePages/2);
			if (newActivePage <= flooredVisiblePages) {
				firstVisiblePage = 1;							
			} else if (newActivePage > (pageCount - flooredVisiblePages)) {
				lastVisiblePage = pageCount;
				firstVisiblePage = lastVisiblePage - $.exposure.visiblePages + 1;
			} else { 
				firstVisiblePage -= flooredVisiblePages;
				lastVisiblePage = firstVisiblePage + $.exposure.visiblePages - 1;
			}
			$('.exposurePaging').each(function() {	
				$(this).children().each(function(i) {
					var currentPage = i+1;
					if (currentPage >= firstVisiblePage && currentPage <= lastVisiblePage) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			});
		}	
	}
};

/**
* Create a new paging link for a specific page.
*
* @param page Index of the image/number of the page (depending on the imageControls setting) to create the link for.
*/
$$.newPagingLink = function(index) {
	return $.exposure.onPagingLink($('<a href="javascript:void(0);" rel="' + index + '">' + index + '</a>').click(function() {
		// View the image/page defined in the rel attribute of the link.
		var rel = Number($(this).attr('rel'));
		if ($.exposure.imageControls) {
			$.exposure.viewImage(rel-1);
		} else {
			$.exposure.goToPage(rel);
		}
	}));
};

/**
* Page transition state.
*/
$$.pageTransition = false;
				
/**
* Slideshow playing state.
*/
$$.playingSlideshow = false;
		
/**
* Holds the timer for the slideshow.
*/
$$.slideshowTimer = null;
		
/**
* Slideshow transition state.
*/
$$.slideshowTransition = false;
		
/**
* Recursive function that runs nextImage() after given delay. Don't use this directly, use playSlideshow() instead.
*/		
$$.slideshow = function() {
	$$.slideshowTimer = setTimeout(function() { 
		$$.slideshowTransition = true;
		$.exposure.nextImage(); 
		$$.slideshowTransition = false;
		$$.slideshow(); 
	}, $.exposure.slideshowDelay);
};

/**
* Full screen state.
*/
$$.infullScreen = false;

/**
* Calculate actual max width (subtract padding, margin and borders of image and container),
*/
$$.actualMaxWidth = function(image, target) {
	return $.exposure.maxWidth ? $.exposure.maxWidth-($$.widthDiff(image)+$$.widthDiff(target)) : 0;
};

/**
* Calculate actual max height (subtract padding, margin and borders of image and container),
*/
$$.actualMaxHeight = function(image, target) {
	return $.exposure.maxHeight ? $.exposure.maxHeight-($$.heightDiff(image)+$$.heightDiff(target)) : 0;
};

/**
* Fix image to max size.
*/
$$.fitToMaxSize = function(image) {
	var target = $('.exposureTarget');
	if ($.exposure.stretchToMaxSize) {
		if ($.exposure.maxWidth) {
			// Stretch to maxWidth.
			image.width($$.actualMaxWidth(image, target));
		}
		if ($.exposure.maxHeight) {
			// Stretch to maxHeight.
			image.height($$.actualMaxHeight(image, target));	
		}							
	} else {
		if (image.width() > image.height()) {
			// Landscape format image, fit to width first.
			$$.fitToMaxWidth(image, target);
			$$.fitToMaxHeight(image, target);
		} else if (image.height() > image.width()) {
			// Portrait format image, fit to height first.
			$$.fitToMaxHeight(image, target);
			$$.fitToMaxWidth(image, target);
		} else {
			// Square format image.
			var actualMaxHeight = $$.actualMaxHeight(image, target);
			var smallest = $$.actualMaxWidth(image, target);
			if (!smallest || (actualMaxHeight && smallest && actualMaxHeight < smallest)) {
				smallest = actualMaxHeight;
			}
			if (smallest && image.width() > smallest) {
				image.width(smallest);
				image.height(smallest);
			}
		}
	}	
};

/**
* Center main image in window.
*/
$$.centerImageInWindow = function(image) {
	var target = $('.exposureTarget');
	target.width(image.width()).height(image.height());			
	target.css({
		'top' :  ($(window).height()-target.outerHeight(true))/2, 
		'left' : ($(window).width()-target.outerWidth(true))/2
	});
	
	$('.exposureLastImage').each(function() {
		$(this).css({
			'top' :  (target.height()-$(this).height())/2, 
			'left' : (target.width()-$(this).width())/2
		});
	});
};

/**
* Fit image to maxWidth.
*/
$$.fitToMaxWidth = function(image, target) {
	var actualMaxWidth = $$.actualMaxWidth(image, target);
	if (actualMaxWidth && image.width() > actualMaxWidth) {
		// Calculate new height to maintain aspect ratio.								
		var newHeight = Math.round(actualMaxWidth * image.height()/image.width());
		image.height(newHeight);
		// Shrink to maxWidth.	
		image.width(actualMaxWidth);										
	}
};

/**
* Fit image to maxHeight.
*/
$$.fitToMaxHeight = function(image, target) {	
	var actualMaxHeight = $$.actualMaxHeight(image, target);
	if (actualMaxHeight && image.height() > actualMaxHeight) {
		// Calculate new width to maintain aspect ratio.								
		var newWidth = Math.round(actualMaxHeight * image.width()/image.height());
		image.width(newWidth);
		// Shrink to maxHeight.
		image.height(actualMaxHeight);
	}
};

/**
* Resize target container element to fit image.
*/
$$.resizeContainer = function(img) {
	// Resize image according to maxWidth and maxHeight settings.
	$$.fitToMaxSize(img);
	
	// Resize target element to fit image.
	if (!$.exposure.fixedContainerSize) {
		$('.exposureTarget').show().width(img.width()).height(img.height());
	}
};

// Extend with public functions. These can be called from your gallery using $.exposure.nameOfFunction().
$.extend({exposure : {
		/**
		* Calculate the page number of a specific image.
		*
		* @param index Index of image to get page number for.
		*/
		pageNumberForImage : function(index) {
			return Math.ceil((index + 1) / $.exposure.pageSize);
		},
		
		/**
		* Calculate the total number of pages using the set page size.
		*/
		numberOfPages : function() {
			// Calculate the page number for the last image.
			return $.exposure.pageNumberForImage($$.images.length-1);
		},
		
		/**
		* Check if the the page currently being viewed is the first page.
		*/
		atFirstPage : function() {
			return $$.currentPage === 1;
		},
		
		/**
		* Check if the the page currently being viewed is the last page.
		*/
		atLastPage : function() {
			return $$.currentPage === $.exposure.numberOfPages();
		},
		
		/**
		* Check if an image is the first image on its page.
		*
		* @param index Index of image to check. Will default to image currently being viewed if not set. 
		*/
		firstImageOnPage : function(index) {
			if (!index) {
				index = $$.current;
			}
			return $.exposure.pageSize === 1 || (index % $.exposure.pageSize === 0);
		},
		
		/**
		* Check if the an image is the last image on its page.
		*
		* @param index Index of image to check. Will default to image currently being viewed if not set. 
		*/
		lastImageOnPage : function(index) {
			if (!index) {
				index = $$.current;
			}
			var imageCount = $$.images.length;
			if ($.exposure.pageSize === 1 || imageCount === 1) {
				return true;	
			}
			if (index > 0) {
				var currentPageSize = $.exposure.pageSize;
				var currentPage = $.exposure.pageNumberForImage(index);
				if (currentPage === $.exposure.numberOfPages()) {
					// Calculate the size of the last page as it may differ from the set page size.
					var newPageSize = imageCount % $.exposure.pageSize;
					if (newPageSize > 0) {
						currentPageSize = newPageSize;
					}
				}
				
				var imageIndex = index;
				if (currentPage > 1) {
					imageIndex -= (currentPage-1) * $.exposure.pageSize;
				}
				
				// Check if the current image is the last image of the current page.				
				return (imageIndex+1) % currentPageSize === 0;
			}
			return false;
		},
		
		/**
		* Get the number of the current page.
		*/
		currentPage : function() {
			return $$.currentPage;
		},
		
		/**
		* Get the number of images.
		*/
		numberOfImages : function() {
			return $$.images.length;	
		},
		
		/**
		* Check if the image currently being viewed is the first image.
		*/
		atFirstImage : function() {
			return $$.current === 0; 
		},
		
		/**
		* Check if the image currently being viewed is the last image.
		*/
		atLastImage : function() {
			return $$.current === $.exposure.numberOfImages()-1;
		},
		
		/**
		* Get a spefic image object from the images array.
		*
		* @param index Index of image to get.
		*/
		getImage : function(index) {
			if (index !== null && index > -1 && index < $$.images.length) {
				return $$.images[index];
			}
			return null;
		},
		
		/**
		* Get the index of the image with the specified image source.
		*
		* @param src Source of the image to get index for.
		*/
		indexOfImage : function(src) {
			if (src && $$.defined($$.sources[src])) {
				return $$.sources[src];
			}
			return -1;
		},
		
		/**
		* Get the index of the current image.
		*/
		currentImage : function() {
			return $$.current;
		},

		/**
		* Dynamically add an image to the gallery. 
		*
		* @param src Source to the full size image.
		* @param thumb Source to thumbnail version of the image.
		* @param caption Image caption.
		* @param data Extra image data.
		*/		
		addImage : function(src, thumb, caption, data) {
			var pageCount = $.exposure.numberOfPages();				
			var index = $$.newImage(src, thumb, caption, data);
			if (index > -1) {
				var pageNumber = $.exposure.pageNumberForImage(index);
				var containers = $('.exposureThumbs li[rel="'+ pageNumber + '"]');
				if (containers.length) {
					containers.removeClass('last');
				}
				
				// Recreate paging if a new page needs to be added.
				var newPageAdded = pageNumber > pageCount;
				if (newPageAdded) {
					// Make sure paging container is empty.
					$('.exposurePaging').empty();
					
					$$.createPaging();
				}
				
				if (newPageAdded || pageNumber === $$.currentPage) {
					// Reload the current page.
					$.exposure.goToPage($$.currentPage);	
				}
			}
		},
		
		/**
		* Dynamically remove a specific image from the gallery.
		*
		* @param index Index of image to remove.
		*/
		removeImage : function(index) {
			if ($$.images.length === 1) {
				$.exposure.removeAllImages();
			} else {
				if ($.exposure.enableSlideshow) {
					$.exposure.pauseSlideshow();	
				}
				
				var oldPageCount = $.exposure.numberOfPages();
				
				// Remove the image from the list of images.
				$$.images.splice(index, 1);
				
				// Remove the image from the loadQueue.
				var queueIndex = $.inArray(index, $$.loadQueue);
				if (queueIndex > -1) {
					$$.loadQueue.splice(queueIndex, 1);
				}
			
				// Remove thumbnail and container.
				var container = $.exposure.getThumb(index).parent();
				container.remove();
			
				// Update thumbnail containers.
				$('.exposureThumbs > li').each(function(i) {
					if (i >= index) {
						// Update page number in rel attribute.
						var newRel = $.exposure.pageNumberForImage(i);
						$(this).attr('rel', newRel);
						
						// Update index number in rel attribute of image.
						$(this).find('img').attr('rel', i);
						
						// Update first/last classes
						if ($.exposure.firstImageOnPage(i)) {
							$(this).addClass('first');	
						} else {
							$(this).removeClass('first');	
						}
						if ($.exposure.lastImageOnPage(i)) {
							$(this).addClass('last');
						} else {
							$(this).removeClass('last');
						}
						
						if ($$.currentPage === newRel) {
							$(this).show();
						} else {
							$(this).hide();	
						}
					}
				});
				
				// Recreate paging links.
				var pageRemoved = $.exposure.numberOfPages < oldPageCount;
				if (pageRemoved) {
					// Make sure paging container is empty.
					$('.exposurePaging').empty();
					
					$$.createPaging();
				}
				
				if ($$.current === index) {
					// Skip to next image if the deleting image was the currently viewed image.
					$$.current = -1;
					var nextIndex = index;
					if (index === $.exposure.numberOfImages()) {
						nextIndex = 0;	
					}
					$.exposure.viewImage(nextIndex);
				} 
			}
		},
		
		/**
		* Removes all images from the gallery. Usable when dynamically rebuilding the gallery from scratch.
		*/
		removeAllImages : function() {
			$$.images = [];
			$$.sources = {};
			$$.loadQueue = [];
			if ($.exposure.enableSlideshow) {
				$.exposure.pauseSlideshow();	
			}
			$('.exposureThumbs').empty();
			$('.exposurePaging').empty();
			$.exposure.viewImage(0);
		},
		
		/**
		* Get the thumbnail img element for a specific image.
		*
		* @param index Index of image to find thumbnail for.
		*/
		getThumb : function(index) {
			return $('.exposureThumbs img[rel="'+index+'"]');
		},
		
		/**
		* Get the index of the next image.
		*/
		getNextImage : function() {
			if ($$.current === $$.images.length-1) {
				// Is at last image, return first image.
				if ($.exposure.loop) {
					return 0;
				} else {
					// Loop ended callback.
					$.exposure.onEndOfLoop();	
				}					
			} else {
				// Return next image.
				return $$.current+1;
			}
			return null;
		},
		
		/**
		* Get the index of the previous image.
		*/
		getPrevImage : function() {
			if ($$.current === 0) {
				// Is at first image, return last image.
				if ($.exposure.loop) {
					return $$.images.length-1;
				}
			} else {					
				// Return previous image. 
				return $$.current-1;
			}
			return null;
		},
	
		/**
		* View a specific page.
		*
		* @param page Number of the page to view.
		* @param imageToView Index of the image to view (defaults to viewing first image on page if this parameter isn't set).
		*/
		goToPage : function(page, imageToView) {
			if ($$.validPage(page)) {
				// Hide all thumbnail containers.
				$('.exposureThumbs li').removeClass('current').hide();
				
				$$.loadPage(page, imageToView);
				
				if (!$.exposure.imageControls) {
					$$.updatePaging(page);
				}
				
				$$.currentPage = page;
				
				if ($.exposure.showControls) {			
					if ($.exposure.atFirstPage()) {
						// Disable first page button.
						if ($.exposure.controls.firstLast) {
							$('.exposureFirstPage').addClass('disabled');
						}
						
						// Hide previous page button.
						if (!$.exposure.loop && $.exposure.controls.prevNext) {
							$('.exposurePrevPage').hide();
						}
					} else {
						// Enable first page button.
						if ($.exposure.controls.firstLast) {
							$('.exposureFirstPage').removeClass('disabled');
						}
						
						// Show previous page button.
						if (!$.exposure.loop && $.exposure.controls.prevNext) {
							$('.exposurePrevPage').show();
						}
					}
					if ($.exposure.atLastPage()) {
						// Disable last page button.
						if ($.exposure.controls.firstLast) {
							$('.exposureLastPage').addClass('disabled');
						}
						
						// Hide next page button.
						if (!$.exposure.loop && $.exposure.controls.prevNext) {
							$('.exposureNextPage').hide();
						}
					} else {
						// Enable last page button.
						if ($.exposure.controls.firstLast) {
							$('.exposureLastPage').removeClass('disabled');
						}
						
						// Show next page button.
						if (!$.exposure.loop && $.exposure.controls.prevNext) {			
							$('.exposureNextPage').show();
						}
					}
				}
				
				// Page changed callback.
				if (!$.exposure.carouselControls) {
					$.exposure.onPageChanged();
				}
			}
		},
		
		/**
		* View the first page.
		*/
		firstPage : function() {
			if (!$.exposure.atFirstPage()) {
				$.exposure.goToPage(1);
			}	
		},
		
		/**
		* View the last page.
		*/
		lastPage : function() {
			if (!$.exposure.atLastPage()) {
				$.exposure.goToPage($.exposure.numberOfPages());
			}	
		},
		
		/**
		* View the previous page.
		*/
		prevPage : function() {
			if (!$.exposure.atFirstPage()) {
				// Go to previous page.
				$.exposure.goToPage($$.currentPage-1);
			} else if ($.exposure.loop) {
				// At first page, go to last page.
				$.exposure.goToPage($.exposure.numberOfPages());
			}	
		},
		
		/**
		* View the next page.
		*/
		nextPage : function() {
			if (!$.exposure.atLastPage()) {
				// Go to next page.
				$.exposure.goToPage($$.currentPage+1);
			} else if ($.exposure.loop) {
				// At last page, go back to first page.
				$.exposure.goToPage(1);
			}	
		},
		
		/**
		* View a specific image.
		*
		* @param Index of image to view.
		*/
		viewImage : function(index) {
			if ($$.current !== index) {
				if ($.exposure.enableSlideshow && !$$.slideshowTransition) {
					$.exposure.pauseSlideshow();
				}
				var wrapper = $('.exposureWrapper');
				var validImage = false;	
				var image = $$.images[index];
				if (image) {
					var src = image.src;
					var caption = image.caption;
					var extraImageData = image.data;
									
					if (src) {
						validImage = true;
						
						var hasThumb = $.exposure.showThumbs;
						var thumb = null;
						if ($.exposure.showThumbs) {
							thumb = $('.exposureThumbs img[rel="' + index + '"]');
							hasThumb = thumb && thumb.length;
							
							// Light up active thumbnail.
							if (hasThumb) {
								thumb.parent().siblings().removeClass('active');
								thumb.parent().addClass('active');
							} else {
								$('.exposureThumbs li.active').removeClass('active');
							}
						}
						
						// Show loading animation.
						wrapper.parent().removeClass('exposureLoaded');
						if ($$.loaded(index)) {
							// Hide loading animation if image already loaded.				
							wrapper.parent().addClass('exposureLoaded');
						}
					
						var img = $$.loadImage(index, function() {
							var lastImage = wrapper.find('.exposureImage');
							if (lastImage.length) {
								lastImage.removeClass('exposureCurrentImage');
								lastImage.addClass('exposureLastImage');
							}
							
							$(this).addClass('exposureCurrentImage');
							
							wrapper.append($(this));
							
							// Enable browsing by clicking on the image.
							if ($.exposure.clickingNavigation) {
								$(this).click($.exposure.nextImage);
							}
							
							if (!$(this).width() || !$(this).height()) {
								// Workaround for bug caused by AdBlock plugin for Chrome and Safari: 
								// http://code.google.com/p/adblockforchrome/issues/detail?id=3701
								var i = $(this);
								var delay = setInterval(function() {
									$$.resizeContainer(i);								
									clearTimeout(delay);
								}, 2);
							} else {	
								$$.resizeContainer($(this));
							}
							
							// Image is supposed to be viewed in full screen.
							if ($.exposure.fullScreen && !$$.infullScreen) {
								$.exposure.onEnterFullScreen($('.exposureMask'));
								$$.infullScreen = true;
							}
							
							// Add caption and additional image data.							
							var imageDataContainer = $.exposure.dataTarget ? $($.exposure.dataTarget) : wrapper.siblings('.exposureData');
							if (imageDataContainer.length) {
								if ($.exposure.showCaptions) {
									// Add caption to image data container.
									var captionContainer = imageDataContainer.find('.caption');
									if (captionContainer.length) {
										// Remove current caption from container.
										captionContainer.empty();
										if (!caption && hasThumb) {
											// Extract caption from thumbnail.
											caption	= thumb.attr('title');
										}
									}
									captionContainer.html(caption);
								}
								
								if ($.exposure.showExtraData) {
									// Add extra image data to image data container.
									var extraImageDataContainer = imageDataContainer.find('.extra');
									if (extraImageDataContainer.length) {
										// Remove current data from container.
										extraImageDataContainer.empty();
										if (!extraImageData && hasThumb) {
											// Extract data from thumbnail.
											extraImageData = thumb.data('data');
										}
										extraImageDataContainer.html(extraImageData);
									}
								}
							}
							
							// Image loaded callback.
							$.exposure.onImage($(this), imageDataContainer, thumb);
	
							// Preload next image.					
							$$.preloadNextInQueue();
						});						
					}
				}
				if (!validImage) {
					wrapper.siblings().andSelf().empty();
					$('.exposureThumbs li.active').removeClass('active');	
				}
				
				if ($.exposure.imageControls) {
					var page = $.exposure.pageNumberForImage(index);
					if ($$.currentPage !== page && !$$.pageTransition) {
						$.exposure.goToPage(page, index);	
					}
					$$.updatePaging(index+1);
				}
				
				$$.current = index;
				
				// If using carousel controls make sure to properly update the thumbnails.
				if ($.exposure.carouselControls && $$.images.length > $.exposure.pageSize) {
	                var firstVisibleImage = index;
	                var lastVisibleImage = $.exposure.pageSize-1;
	                var flooredVisibleImages = Math.floor($.exposure.pageSize/2);
	
	                if (!$.exposure.loop && index < flooredVisibleImages) {
	                    firstVisibleImage = 0;
	                } else if (!$.exposure.loop && index >= ($$.images.length - flooredVisibleImages)) {
	                    lastVisibleImage = $$.images.length-1;
	                    firstVisibleImage = lastVisibleImage - $.exposure.pageSize;
	                } else {
	                    firstVisibleImage -= flooredVisibleImages;
	                    lastVisibleImage = firstVisibleImage + $.exposure.pageSize-1;
	                }
	                
	                $.exposure.onCarousel(firstVisibleImage, lastVisibleImage);
	            
					$('.exposureThumbs li').removeClass('current').hide();
	                $$.viewThumbs(firstVisibleImage, lastVisibleImage);
	                $$.currentPage = $.exposure.pageNumberForImage(index);
	            }
			}
		},
		
		/**
		* View first image.
		*/
		firstImage : function() {
			if (!$.exposure.atFirstImage()) {
				if ($.exposure.separatePageBrowsing || $.exposure.atFirstPage()) {
					$.exposure.viewImage(0);
				} else {
					$.exposure.goToPage(1);	
				}
			}
		},
		
		/**
		* View next image.
		*/
		nextImage : function() {
			if (!$.exposure.separatePageBrowsing && $.exposure.lastImageOnPage()) {
				if ($.exposure.atLastPage() && $.exposure.loop) {
					// At the last page, go back to first page.
					$.exposure.goToPage(1);
				} else {
					// Go to the next page.
					$.exposure.goToPage($$.currentPage+1);
				}
				// Next image callback.
				$.exposure.onNext();
			} else {
				var next = $.exposure.getNextImage();
				if (next !== null) {
					// Select next image.
					$.exposure.viewImage(next);
					// Next image callback.
					$.exposure.onNext();	
				}
			}
			var nextNext = $.exposure.getNextImage();
			if (nextNext !== null) {
				// Add second next image to load queue.
				$$.addToLoadQueue(nextNext);
			}
		},
		
		/**
		* View previous image.
		*/
		prevImage : function() {
			if (!$.exposure.separatePageBrowsing && $.exposure.firstImageOnPage()) {
				if ($.exposure.atFirstPage() && $.exposure.loop) {
					// At the first page, go to the last page.
					$.exposure.goToPage($.exposure.numberOfPages(), $.exposure.numberOfImages()-1);
				} else {
					// Go to the previous page.	
					var page = $$.currentPage-1;
					$.exposure.goToPage(page, page * $.exposure.pageSize - 1);
				}
				// Previous image callback.
				$.exposure.onPrev();
			} else {
				var prev = $.exposure.getPrevImage();
				if (prev !== null) {
					// Select next image.
					$.exposure.viewImage(prev);
					// Previous image callback.
					$.exposure.onPrev();
				}
			}
			var prevPrev = $.exposure.getPrevImage();
			if (prevPrev !== null) {
				// Add second previous image to load queue.
				$$.addToLoadQueue(prevPrev);
			}
		},
		
		/**
		* View last image.
		*/
		lastImage : function() {
			if (!$.exposure.atLastImage()) {
				if ($.exposure.separatePageBrowsing || $.exposure.atLastPage()) {
					$.exposure.viewImage($.exposure.numberOfImages()-1);
				} else {
					$.exposure.goToPage($.exposure.numberOfPages(), $.exposure.numberOfImages()-1);	
				}
			}
		},
		
		/**
		* Play the slideshow.
		*/		
		playSlideshow : function() {
			if (!$$.playingSlideshow) {
				if ($.exposure.slideshowControlsTarget) {
					$('.exposurePlaySlideshow').hide();
					$('.exposurePauseSlideshow').show();
				}
				$$.slideshow();
				$$.playingSlideshow = true;		
			}
			$.exposure.onSlideshowPlayed();			
		},
		
		/**
		* Pause the slideshow.
		*/
		pauseSlideshow : function() {
			if ($$.playingSlideshow) {
				if ($.exposure.slideshowControlsTarget) {
					$('.exposurePlaySlideshow').show();
					$('.exposurePauseSlideshow').hide();
				}
				$$.playingSlideshow = false;
				if ($$.slideshowTimer) {
					clearTimeout($$.slideshowTimer);
				}
				$.exposure.onSlideshowPaused();
			}
		},
		
		/**
		* Toggle (play/pause)
		*/
		toggleSlideshow : function() {
			if ($$.playingSlideshow) {
				$.exposure.pauseSlideshow();
			} else {
				$.exposure.playSlideshow();
			}
		},
		
		/**
		* Go to first image/page depending on imageControls setting.
		*/
		first : function() {
			if ($.exposure.imageControls) {
				$.exposure.firstImage();	
			} else {
				$.exposure.firstPage();
			}
		},
		
		/**
		* Go to previous image/page depending on imageControls setting.
		*/
		prev : function() {
			if ($.exposure.imageControls) {
				$.exposure.prevImage();	
			} else {
				$.exposure.prevPage();
			}
		},
		
		/**
		* Go to next image/page depending on imageControls setting.
		*/
		next : function() {
			if ($.exposure.imageControls) {
				$.exposure.nextImage();	
			} else {
				$.exposure.nextPage();
			}
		},
		
		/**
		* Go to last image/page depending on imageControls setting.
		*/
		last : function() {
			if ($.exposure.imageControls) {
				$.exposure.lastImage();	
			} else {
				$.exposure.lastPage();
			}
		},
		
		/**
		* Leave full screen mode.
		*/
		exitFullScreen : function() {
			if ($$.infullScreen) {
				$.exposure.pauseSlideshow();
				$$.deselectCurrentImage();
				$.exposure.onExitFullScreen($('.exposureTarget'), $('.exposureMask'));
				$$.infullScreen = false;
			}
		},
		
		/**
		* Fit images to window (used in full screen mode).
		*/
		fitToWindow : function() {
			$.exposure.maxWidth = $(window).width();
			$.exposure.maxHeight = $(window).height();
			var image = $('.exposureCurrentImage').width('auto').height('auto');
			$$.fitToMaxSize(image);
			
			if (!image.width() || !image.height()) {
				// Workaround for bug caused by AdBlock plugin for Chrome and Safari: 
				// http://code.google.com/p/adblockforchrome/issues/detail?id=3701
				var delay = setInterval(function() {
					$$.centerImageInWindow(image);
					clearTimeout(delay);
				}, 2);
			} else {	
				$$.centerImageInWindow(image);
			}
		},
		
		/**
		* Default texts. Use the localization files to override these.
		*/
		texts : {
			first : 'First',
			previous : 'Prev',
			next : 'Next',
			last : 'Last',
			play : 'Play slideshow',
			pause : 'Pause slideshow'
		}
	}			
});
})(jQuery);

/*
* jQuery Hotkeys Plugin
* Copyright 2010, John Resig
* Dual licensed under the MIT or GPL Version 2 licenses.
*
* Based upon the plugin by Tzury Bar Yochay:
* http://github.com/tzuryby/hotkeys
*
* Original idea by:
* Binny V A, http://www.openjs.com/scripts/events/keyboard_shortcuts/
*/
(function(jQuery){jQuery.hotkeys={version:"0.8",specialKeys:{8:"backspace",9:"tab",13:"return",16:"shift",17:"ctrl",18:"alt",19:"pause",20:"capslock",27:"esc",32:"space",33:"pageup",34:"pagedown",35:"end",36:"home",37:"left",38:"up",39:"right",40:"down",45:"insert",46:"del",96:"0",97:"1",98:"2",99:"3",100:"4",101:"5",102:"6",103:"7",104:"8",105:"9",106:"*",107:"+",109:"-",110:".",111:"/",112:"f1",113:"f2",114:"f3",115:"f4",116:"f5",117:"f6",118:"f7",119:"f8",120:"f9",121:"f10",122:"f11",123:"f12",144:"numlock",145:"scroll",191:"/",224:"meta"},shiftNums:{"`":"~","1":"!","2":"@","3":"#","4":"$","5":"%","6":"^","7":"&","8":"*","9":"(","0":")","-":"_","=":"+",";":": ","'":"\"",",":"<",".":">","/":"?","\\":"|"}};function keyHandler(handleObj){if(typeof handleObj.data!=="string"){return}var origHandler=handleObj.handler,keys=handleObj.data.toLowerCase().split(" ");handleObj.handler=function(event){if(this!==event.target&&(/textarea|select/i.test(event.target.nodeName)||event.target.type==="text")){return}var special=event.type!=="keypress"&&jQuery.hotkeys.specialKeys[event.which],character=String.fromCharCode(event.which).toLowerCase(),key,modif="",possible={};if(event.altKey&&special!=="alt"){modif+="alt+"}if(event.ctrlKey&&special!=="ctrl"){modif+="ctrl+"}if(event.metaKey&&!event.ctrlKey&&special!=="meta"){modif+="meta+"}if(event.shiftKey&&special!=="shift"){modif+="shift+"}if(special){possible[modif+special]=true}else{possible[modif+character]=true;possible[modif+jQuery.hotkeys.shiftNums[character]]=true;if(modif==="shift+"){possible[jQuery.hotkeys.shiftNums[character]]=true}}for(var i=0,l=keys.length;i<l;i++){if(possible[keys[i]]){return origHandler.apply(this,arguments)}}}}jQuery.each(["keydown","keyup","keypress"],function(){jQuery.event.special[this]={add:keyHandler}})})(jQuery);
