<?php
/**
 * Google+ Card UI elements file.
 *
 * This file controls the Google+ Card UI elements.
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
 * @version    $Id: blogui_class_inc.php 20147 2010-12-31 12:30:20Z dkeats $
 * @package    googleplus
 * @subpackage googleplusui
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * class to control Google+ Card ui elements
 *
 * This class controls the Google+ Card UI elements. 
 *
 * @category  Chisimba
 * @package   googleplus
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2006-2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class googleplusui extends object
{

    /**
     * user object
     *
     * @var    object
     * @access public
     */
    public $objUser;
    
    /**
     * Google plus ID
     *
     * @var integer
     * @access public
     */
     public $plusId;
     
    /**
     * Google plus user data
     *
     * @var string
     * @access public
     */
     public $plusdata;
    
    /**
     * Standard init function
     *
     * Initialises and constructs the object via the framework
     *
     * @return void
     * @access public
     */
    public function init()
    {
        // user class
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objLanguage = $this->getObject('language', 'language');
        require_once($this->getResourcePath('googleCard.php'));
    }
    
    public function setupCard($plusId) {
        $this->plusId = $plusId;
        $plus = new googleCard($this->plusId);
        // enable caching (off by default)
        $plus->cache_data = 0;
        // do the scrape and set a prop for later
        $this->plusdata = $plus->googleCard();
    }
    
    public function show() {
        if (isset($this->plusdata) && !empty($this->plusdata['name']) && !empty($this->plusdata['count']) && !empty($this->plusdata['img']))
        {
            $ret = '
            <div id="plus_card">
		        <div id="plus_card_image">
			        <a href="'.$this->plusdata['url'].'"> <img src="'.$this->plusdata['img'].'" width="80" height="80" /></a>
		        </div>
		        <div id="plus_card_name">
			        <a href="'.$this->plusdata['url'].'">'.$this->plusdata['name'].'</a>
		        </div>
		        <span id="plus_card_add">
			        <a href="'.$this->plusdata['url'].'">Add to circles</a>
		        </span>
		        <div id="plus_card_count">
			        <p>In '.$this->plusdata['count'].' people\'s circles</p>
		        </div>
	        </div>';
	        
	        return $ret;
        }
        else {
            return NULL;
        }
    }
    
}
?>  
