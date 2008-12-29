<?php
/**
 * Tabber class
 * 
 * HTML control class to create multiple tabbed boxes using the layers class.
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
 * @package   htmlelements
 * @author Charl Mert <charl.mert@gmail.com>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id: tabber_class_inc.php 10308 2008-08-26 12:18:36Z tohir $
 * @link      http://avoir.uwc.ac.za
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
/**
* HTML control class to create facebook like dialogs.
* 
* @abstract 
* @package boxy
* @category HTML Controls
* @copyright 2007, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Charl Mert
* @example
*/
class jqboxy extends object 
{

    /**
    * @var $innerHtml The contents of the dialog
    * @access private
    */
    private $innerHtml;

    /**
    * @var $title The title of the dialog
    * @access private
    */
    private $title;

    /**
    * Helper Constuctor
    * 
    * @access public
    * @return void
    */    
    public function init()
    {
        //Need to find a better way to do this, perhaps adding parameter support 
        //for object instanciation to the core enigine_class
        $theme = $this->getVar('jquery_boxy_theme', 'default');

        $jQuery = $this->newObject('jquery', 'htmlelements');
        $jQuery->loadBoxPlugin('0.1.3', $theme);
        $jQuery->loadLiveQueryPlugin();
    }
        
    /**
    * Method that sets the innerHtml of the dialog
    * 
    * @access public
    * @param string $html The html that will be rendered in the dialog
    * @return void
    */
    function setHtml($html){
        $this->innerHtml = $html;
        return true;
    }

    /**
    * Method that uses AJAX to set the innerHtml of the dialog
    *
    * @access public
    * @param string $html The html that will be rendered in the dialog
    * @return void
    */
    function loadAjaxHtml($url, $options){
        $this->innerHtml = $html;
        return true;
    }


    /**
    * Method that sets the innerHtml of the dialog
    * 
    * @access public
    * @param string $title The title of the dialog box
    * @return void
    */
    function setTitle($title){
        $this->title = $title;
        return true;
    }

   /**
    * Method that binds an input element to the window
    * The event will trigger the boxy dialog to show
    * 
    * @access public
    * @param string event : Can hold the following values.
    *
    * These can be any of the valid jquery events:
    *
    * blur, change, click, dblclick, error, focus, keydown, keypress, keyup, 
    * mousedown, mousemove, mouseout, mouseout, resize, scroll, select, submit etc...
    *
    * See http://docs.jquery.com/Events for an extensive list of events. * @return void
    */    
    function bindElement($id = '', $event = 'click'){

        $innerHtml = '"'.addslashes($this->innerHtml).'"';

$script = <<<BOXBIND
    <script type="text/javascript" >
        jQuery(document).ready(function(){
            jQuery('#$id').livequery('click',function(){
                var dialog = new Boxy({$innerHtml}, {title: "{$this->title}"});
            });
        });
    </script>
BOXBIND;
        $this->appendArrayVar('headerParams', $script);
        return true;
    }

   /**
    * Method that binds an input element to the window
    * The event will trigger the boxy dialog to show
    * 
    * @access public
    * @param string event
    * 
    * Allows to bind script inline (after the element is created, not in the <head> but in the <body>)
    */    
    function bindElementInline($id = '', $event = 'click'){

        $innerHtml = '"'.addslashes($this->innerHtml).'"';

$script = <<<BOXBIND
    <script type="text/javascript" >
        jQuery(document).ready(function(){
            jQuery('#$id').livequery('$event',function(){
                var dialog = new Boxy({$innerHtml}, {title: "{$this->title}", closeText: ''});
            });
        });
    </script>
BOXBIND;
        echo $innerHtml;
        return true;
    }

    
    /**
    * Method to attach an elemen'ts click event to the dialog (to trigger the dialog)
    *
    * @access public
    * @return bool
    */
    public function attachClickEvent($id)
    {
        $this->bindElement($id, 'click');   
        return true;
    }

    /**
    * Method to attach an elemen'ts click event to the dialog (to trigger the dialog)
    *
    * @access public
    * @return bool
    */
    public function attachClickEventInline($id)
    {
        $this->bindElementInline($id, 'click');   
        return true;
    }

    /**
    * Method to show the dialog (loads the dialog and waits for trigger event before displaying)
    * 
    * @access public
    * @return $str string
    */
    public function show(){
	

    }    

}
?>
