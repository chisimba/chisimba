/**
 * Enables/Disables text selection, saves selected text
 *   
 * @example jQuery('p').enableTextSelect(); / jQuery('#selectable-area').disableTextSelec();
 * @cat plugin
 * @type jQuery 
 *
 */
jQuery.fn.disableTextSelect = function() {
  return this.each(function() {
    jQuery(this).css({
      '-moz-user-select' : 'none'
    }).bind('selectstart', function() {
      return false;
    });
  });
};

jQuery.fn.enableTextSelect = function() {
  return this.each(function() {
    jQuery(this).css({
      '-moz-user-select':'text'
    }).unbind('selectstart');
  });
};

