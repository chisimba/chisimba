/* 
 * Javascript to support facebook async comments
 *
 * Written by Derek Keats
 * Started on: January 17, 2011, 1:41 pm
 *
 *
 */

  window.fbAsyncInit = function() {
    FB.init({appId: apid, status: true, cookie: true,
             xfbml: true});
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());


/**
 *
 * Put your jQuery code inside this function.
 *
 */
jQuery(function() {
 


});