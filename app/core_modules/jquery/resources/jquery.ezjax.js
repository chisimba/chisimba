/*
 * 
 * jQuery EZJax v. 1.0.1
 * http://www.thecreativeoutfit.com
 * 
 * 
 */

jQuery.fn.ezjax = function(o) {
  
  obj = jQuery(this).attr('class'); 
  
  // Defaults
  var o = jQuery.extend( {
    container: '#ezjax_content',
	initial: null,
	effect: null,
	speed: 'normal',
	easing: null,
	bind: '.'+obj
  },o);
  
  // Load initial
  
	if(o.initial!=null){
		jQuery(o.container).load(o.initial,function(){
		bind();
	});
	}
  
  // Re-bind for any links internal to the content
  
	function bind(){
		jQuery(o.container+' '+o.bind).ezjax({
			container: o.container,
			initial: null,
			effect: o.effect,
			speed: o.speed,
			easing: o.easing
		});
	}
  
  // Main functionality
  
  return this.each(function() {
  
  	jQuery(this).click(function(){
		var url = jQuery(this).attr('href');
		
		// Check for transition effect
		
		if (o.effect != null) {
			
			// Run effect
			switch(o.effect){
				
				// Slide
				case 'slide':
				jQuery(o.container).animate({height:"0px"}, o.speed, function(){
					jQuery(o.container).css('overflow','hidden'); // Fix glitchies
					jQuery(o.container).load(url, function(){
						jQuery(o.container).animate({
							height: jQuery(o.container).children().height() + 20
						},o.speed,o.easing,function(){
							jQuery(o.container).css('overflow','visible'); // Undo glitchy fix
						});
						bind();
					});
				});
				break;
				
				// Fade
				case 'fade':
				jQuery(o.container).animate({ opacity: 0.0 }, o.speed, function(){
					jQuery(o.container).load(url, function(){
						jQuery(o.container).animate({ opacity: 1.0 }, o.speed);
						bind();
					});
				});
				break;
			}
		
		}
		else {
			// Standard load (no effect)
			jQuery(o.container).load(url,function(){
				bind();
			});
		}
		
		// Keeps the href from firing
		return false;
		
	});
  
  });
  
};