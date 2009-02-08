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
* HTML control class to create multiple tabbed boxes using the jQuery Core UI's Tabbing functionality.
* The style sheet resides in core_modules/htmlelements/resources/jquery/api/ui/css/flora.tabs.css
* 
* 
* @abstract 
* @package tabber
* @category HTML Controls
* @copyright 2007, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Charl Mert
* @example
*/
class jquerytabs extends object 
{
    
    /**
    * @var $tabs array :  Array that holds all the tabs
    * @access private
    */
    private $tabs = array();

    /**
    * @var string $setSelected: The tab to shown as default (0, 1, 2 etc.)
    * @access public
    */
    public $setSelected = 0;

    /**
    * @var string $tabId: The tab id
    * @access public
    */
    public $tabId;
   
    /**
    * @var array $extra: This array will contain extra attributes for any tab or tab content area
    * @access public
    */
    public $extra = array();

    /**
    * @var string $width: This can be used to set the overall width of the tab container
    * @access public
    */
    public $width = '100%';

    /**
    * Constuctor
    * 
    * @access public
    * @return void
    */    
    public function init()
    {
        $jQuery = $this->newObject('jquery', 'htmlelements');
        $jQuery->loadUITabbing();

        $this->tabId = 'jQueryTab_'.rand(1,10);
        $this->setSelected = 0;

         $script = '
            <script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery("#'.$this->tabId.' > ul").tabs({
                    selected: '.$this->setSelected.'
                });
            
            });
            </script>
            ';

        $this->appendArrayVar('headerParams', $script);
        $this->tabs = array();
        $this->isNested = FALSE;
    }
        
    /**
    * Method that adds a tab
    * 
    * @access public
    * @param array $tab : Can hold the following values
    * name string
    * content string
    * extra string
    * @return void
    */    
    function addTab($name = NULL, $content = NULL){

        $tab['name'] = $name;
        $tab['content'] = $content;
        array_push($this->tabs,$tab);

    }

   /**
    * Method that removes a tab
    * 
    * @access public
    * @param array $tab : Can hold the following values
    * name string
    * content string
    * extra string
    * @return void
    */    
    function removeTab($name = NULL){

        $tmpTabs = array();
        foreach ($this->tabs as $key => $value){
            if ($this->tabs[$key]['name'] != $name){
                array_push($tmpTabs, $value);
            }
        }
        $this->tabs = $tmpTabs;

    }
    
    /**
    * Method to get a list of current tabs
    *
    * @access public
    * @return array $tabArray: The tabs for an instance of the tabber object
    */
    public function getTabs()
    {
        $tabArray = $this->tabs;
        return $tabArray;
    }

    /**
    * Method to show the tabs
    * 
    * @access public
    * @return $str string
    */
    public function show(){
    
        if(isset($this->tabs) && is_array($this->tabs)){            
            $str = '<div id="'.$this->tabId.'" class="flora">'."\n";
            $str .= '<ul>'."\n";

            $counter = 0;
            foreach ($this->tabs as $key => $value){
                 $str .= "\n".'<li><a href="#jqtabid'.$counter.'"><span>'.$this->tabs[$key]['name'].'</span></a></li>'."\n";
                $counter++;
            }    

            $str .= '</ul>'."\n";

            $counter = 0;
            foreach ($this->tabs as $key => $value){
                 $str .= '<div id = "jqtabid'.$counter.'" >';
                          $str .= $this->tabs[$key]['content'];
                $str .= '</div>'."\n";
                $counter++;
             }

           $str .= '</div>';

            return $str;
        }
        return FALSE;

    }    

}
?>
