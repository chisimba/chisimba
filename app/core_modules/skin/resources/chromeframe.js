jQuery(function() {
 if (jQuery.browser.msie) {
  jQuery.getScript("https://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js", function() {
   CFInstall.check({mode: "overlay", destination: "http://www.charlvn.com"});
  });
 }
});
