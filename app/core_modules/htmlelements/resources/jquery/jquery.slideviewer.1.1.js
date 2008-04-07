jQuery(function(){
   jQuery("div.svw").prepend("<img src='http://www.gcmingati.net/wordpress/wp-content/uploads/svwloader.gif' class='ldrgif' alt='loading...'/ >"); 
});
var j = 0;
jQuery.fn.slideView = function(settings) {
	  settings = jQuery.extend({
     easeFunc: "easeInOutExpo", /* <-- easing function names changed in jquery.easing.1.2.js */
     easeTime: 750,
     toolTip: false
  }, settings);
	return this.each(function(){
		var container = jQuery(this);
		container.find("img.ldrgif").remove(); // removes the preloader gif
		container.removeClass("svw").addClass("stripViewer");		
		var pictWidth = container.find("li").find("img").width();
		var pictHeight = container.find("li").find("img").height();
		var pictEls = container.find("li").size();
		var stripViewerWidth = pictWidth*pictEls;
		container.find("ul").css("width" , stripViewerWidth); //assegnamo la larghezza alla lista UL	
		container.css("width" , pictWidth);
		container.css("height" , pictHeight);
		container.each(function(i) {
			jQuery(this).after("<div class='stripTransmitter' id='stripTransmitter" + j + "'><ul><\/ul><\/div>");
			jQuery(this).find("li").each(function(n) {
						jQuery("div#stripTransmitter" + j + " ul").append("<li><a title='" + jQuery(this).find("img").attr("alt") + "' href='#'>"+(n+1)+"<\/a><\/li>");												
				});
			jQuery("div#stripTransmitter" + j + " a").each(function(z) {
				jQuery(this).bind("click", function(){
				jQuery(this).addClass("current").parent().parent().find("a").not(jQuery(this)).removeClass("current"); // wow!
				var cnt = - (pictWidth*z);
				jQuery(this).parent().parent().parent().prev().find("ul").animate({ left: cnt}, settings.easeTime, settings.easeFunc);
				return false;
				   });
				});
			jQuery("div#stripTransmitter" + j).css("width" , pictWidth);
			jQuery("div#stripTransmitter" + j + " a:eq(0)").addClass("current");
			if(settings.toolTip){
			container.next(".stripTransmitter ul").find("a").Tooltip({
				track: true,
				delay: 0,
				showURL: false,
				showBody: false
				});
			}
			});
		j++;
  });	
};