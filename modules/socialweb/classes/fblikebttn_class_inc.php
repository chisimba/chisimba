<?php

/**
 * 
 * Official Facebook Like Button Generator
 * 
 * Generates the necessary HTML to include the official Facebook Like button.
 * OpenGraph debugger https://developers.facebook.com/tools/debug
 * 
 * PHP version 5
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
 * @package   twitter
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: fblikebttn_class_inc.php 24861 2012-12-16 13:30:54Z dkeats $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://twitter.com/goodies/tweetbutton
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * 
 * Official Facebook Like Button Generator
 * 
 * Generates the necessary HTML to include the official Facebook Like button.
 * 
 * @category  Chisimba
 * @package   socialweb
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2012 Kenga SOlutions
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: fblikebttn_class_inc.php 24861 2012-12-16 13:30:54Z dkeats $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   https://developers.google.com/+/plugins/+1button/
 */
class fblikebttn extends object
{
    /**
     *
     * Property to hold whether we should display the facebook like 
     * button or not
     * 
     * @var boolean TRUE|FALSE
     * @access public
     * 
     */
    public $fbReady;
    
    /**
     * 
     * Constructor for the facebook like button. It adds the necessary
     * script to the page header to load the FB SDK.
     * 
     * @access public
     * @return void
     * 
     */
    public function init()
    {
        // Get the facebook ID from the facebook module
        $this->objConfig=$this->newObject('dbsysconfig','sysconfig');
        $appId = $this->objConfig->getValue('facebook_apid', 'socialweb');
        $appAdmin = $this->objConfig->getValue('facebook_adminuser', 'socialweb');
        $this->setVar('fb_app_id', $appId);
        $this->setVar('fb_app_id', $appAdmin);
        $this->appId=$appId;
        if ($appId !== 'replaceme') {
            $this->fbReady=TRUE;
        } else {
            $this->fbReady=FALSE;
        }
    }
    
    /**
     * 
     * Render the script to activate the buttons
     * 
     * @return string The rendered script
     * @access public
     * 
     */
    public function activateButtons()
    {
        if ($this->fbReady==TRUE) {
            $script = '
    <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({appId: \'' . $this->appId . '\', status: true, cookie: true, xfbml: true});
            };
            (function() {
                var e = document.createElement(\'script\'); 
                e.async = true;
                e.src = document.location.protocol + \'//connect.facebook.net/en_US/all.js\';
                document.getElementById(\'fb-root\').appendChild(e);
            }());
            </script>';
            return $script;
        } else { 
            return NULL;
        }
    }
    
    /**
     * Generates the necessary HTML to include the official FB LIke button.
     * 
     * <div class="fb-like" data-href="http://localhost/scratch/index.php?
     * module=simpleblog&amp;by=id&amp;id=gen10Srv19Nme23_77963_1347792858" 
     * data-send="true" data-width="450" data-show-faces="true"></div>
     *
     * @access public
     * @param  string $uri     The URI to post. Defaults to the current page.
     * @return string The generated HTML.
     */
    public function getButton($uri=FALSE)
    {
        if ($this->fbReady == TRUE) {
            // Create the HTML document.
            $doc = new DOMDocument('UTF-8');
            $buttonWrapper = $doc->createElement('div');
            $buttonWrapper->setAttribute('class', 'fblikebutton');
            //$br = $doc->createElement('br');
            //$br->setAttribute('style', 'clear:both;');
            // Create the link.
            $div = $doc->createElement('div');
            $div->setAttribute('class', 'fb-like');
            $div->setAttribute('data-send', 'true');
            $div->setAttribute('data-width', '450');
            $div->setAttribute('data-show-faces', 'true');
            if ($uri) {
                $div->setAttribute('data-href', $uri);
            }
            $buttonWrapper->appendChild($div);
            //$doc->appendChild($br);
            $doc->appendChild($buttonWrapper);
            //$doc->appendChild($br);
            return $doc->saveHTML();
        } else {
            return NULL;
        }

    }
}
?>