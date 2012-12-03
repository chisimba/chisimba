//This is to help withthe visual effects on Filemanager's Thumbnail view and also the layout
jQuery(document).ready(function(){
   var fileLink = jQuery('.fileLink');
   var fileDetails = jQuery('.filedetails');
   jQuery(fileLink).hover(function(){
       jQuery(fileDetails).css('width',jQuery(this).css('width'))
       jQuery(fileDetails).css('height',jQuery(this).css('height'))
   })
});