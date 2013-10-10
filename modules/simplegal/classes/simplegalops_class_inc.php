<?php
/**
 *
 * simplegal helper class
 *
 * PHP version 5.1.0+
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   simplegal
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * simplegal helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package simplegal
 *
 */
class simplegalops extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;
    
    /**
     * @var array $data Object property for holding the data
     *
     * @access public
     */
    public $data = array();

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        $this->formatUI();
        $this->exposureJS();
    }
    
    public function formatData($data) {
        $str = NULL;
        $str .= '&nbsp;<br /><div class="panel"><ul id="images">';
        foreach($data as $pic) {
            $str .= '<li><a href="'.$pic['post_content'].'_z.jpg"><img src="'.$pic['post_content'].'_t.jpg" title="'.$pic['post_title'].'" /></a></li>';
        }
        $str .= '<div id="controls"></div>
				<div class="clear"></div>
			</div>
			<div id="exposure"></div>			
			<div class="clear"></div>
			<div id="slideshow"></div>		
		</div>';       
        return $str;
    }
    
    public function formatUI() {
        $js = NULL;
        $js .= '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>';
        $js .= $this->getJavascriptFile('jquery.exposure.js?v=0.9', 'simplegal');
        $this->appendArrayVar('headerParams', $js);
        //return $js;
    }
    
    private function exposureJS() {
        $js  = '<script type="text/javascript">';
		$js .=	"$(function(){
				$('#images').exposure({controlsTarget : '#controls',
					controls : { prevNext : true, pageNumbers : true, firstLast : false },
					visiblePages : 2,
					slideshowControlsTarget : '#slideshow',
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
						
						// Fade out the previous image.
						$('.exposureWrapper > .exposureLastImage').stop().fadeOut(500, function() {
							$(this).remove();
						});
						
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
							thumb.parents('li').siblings().children('img.selected').stop().fadeTo(200, 0.3, function() { $(this).removeClass('selected'); });			
							thumb.fadeTo('fast', 1).addClass('selected');
						}
					},
					onPageChanged : function() {
						$('.exposureThumbs li.current').hide().stop().fadeIn('fast');
					}
				});
			});
		</script>";
		$this->appendArrayVar('headerParams', $js);
		//return $js;
    }
}
?>
