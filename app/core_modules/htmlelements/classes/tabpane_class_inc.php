<?php
/**
 * Tabpane class 
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
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
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
* HTML control class to create multiple tabbed boxes using the layers class.
* The style sheet class is >box<.
* 
* 
* @abstract 
* @package   tabs
* @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @author    Prince Mbekwa
* @example  
*            $objElement =new tabpane(100,500);

*            $objElement->addTab(array('name'=>'Second','url'=>'http://localhost','content' => $check.$radio.$calendar));
*            $objElement->addTab(array('name'=>'First','url'=>'http://localhost','content' => $form));
*            $objElement->addTab(array('name'=>'Third','url'=>'http://localhost','content' => $tab,'height' => '300','width' => '600'));
*            YOU CAN specify the type of css to go with your tabs.
*            
*            There are three types of look and feel you can choose from
*            1.luna-tab-style-sheet
*            2.winclassic-tab-style-sheet
*            3.webfx-tab-style-sheet : This one is default
*            
* @example  
*            $objElement->addTab(array('name'=>'Second','url'=>'http://localhost','content' => $check.$radio.$calendar),'luna-tab-style-sheet');
*            $objElement->addTab(array('name'=>'First','url'=>'http://localhost','content' => $form,'nested' => true),'luna-tab-style-sheet');
*            
*/
class tabpane extends object 
{
    
    /**
     * Holds Tab array
     * @var $tabs array :  Array that holds all the tabs
    */
    var $tabs = array();

    /**
     * Height Adjustment
     * @var $height int :  the height all the tabs
    */
    var $height;

    /**
     * Width Adjustment
     * @var $width int : with of all the tabs
    */
    var $width=10;
    /**
     * Holds constructed tabs ready for output
     *
     * @var string
     */
    var $constructedTabs=NULL;
    /**
     * Tabpane counter
     *
     * @var int
     */
    var $tabpane =0;
    
    
    /**
     * Description for var
     * @var    unknown
     * @access public 
     */
    var $topTabName=null;
    /**
    * Constuctor
    */
    
    function init()
    {
        $script = '<script language="JavaScript" src="core_modules/htmlelements/resources/tabpane.js"></script>';
        $this->appendArrayVar('headerParams',$script);
        $this->tabpane = 0;
        $this->tabs = array();
        }
        
    /**
    * Method that addes a tab
    * @param $properties array  : Can hold the following values
    *                           name string
    *                           content string
    *                           url string
    * @param $css        string :holds the css type to use and these are
    *                           webfx-tab-style-sheet
    *                           winclassic-tab-style-sheet
    *                           luna-tab-style-sheet
    */    
    function addTab($properties=NULL,$css='webfx-tab-style-sheet'){
        if (is_array($properties)) {
            $link =null;
            if (isset($properties['name'])) {                
                $this->tabs[$properties['name']]['name']=$properties['name'];
                if(isset($properties['content']))
                    $this->tabs[$properties['name']]['content']=$properties['content'];
                if(isset($properties['url']))
                    $this->tabs[$properties['name']]['url']=$properties['url'];
                if(isset($properties['width']))
                    $this->tabs[$properties['name']]['width']=$properties['width'];                      
                if(isset($properties['height']))
                    $this->tabs[$properties['name']]['heigth']=$properties['height'];
                if ($css =='luna-tab-style-sheet')
                    $link = '<link id="luna-tab-style-sheet" type="text/css" rel="stylesheet" href="core_modules/htmlelements/resources/css/luna/tab.css" />';
                    $this->appendArrayVar('headerParams', $link);
                if ($css =='winclassic-tab-style-sheet')
                    $link = '<link id="winclassic-tab-style-sheet" type="text/css" rel="stylesheet" href="core_modules/htmlelements/resources/css/tab.winclassic.css" />';
                    $this->appendArrayVar('headerParams', $link);
                if ($css =='webfx-tab-style-sheet')
                    $link = '<link id="webfx-tab-style-sheet" type="text/css" rel="stylesheet" href="core_modules/htmlelements/resources/css/tab.webfx.css" />';
                    $this->appendArrayVar('headerParams', $link);                
            }            
        }        
    }
    /**
     * Method that builds for Display of Nested or un-nested tabs
     * 
     */
    
    function _buildTabs(){
        //get the javascript
        $script ='<script language="JavaScript" src="core_modules/htmlelements/resources/tabpane.js"></script>';
        $this->appendArrayVar('headerParams',$script);
        $this->constructedTabs=null;
        $cnt=0;
        $this->tabpane ++;
        //start the big div box
        $this->constructedTabs.="<div class=\"tab-page\" id=\"tabPane$this->tabpane\">";
        $this->constructedTabs .="<script type=\"text/javascript\">tp$this->tabpane = new WebFXTabPane( document.getElementById( \"tabPane$this->tabpane\" ) );</script>";
        foreach($this->tabs as $tab){
            $cnt++;
            $this->constructedTabs.="<div class=\"tab-page\" id=\"tabPage$cnt\">";
            $this->constructedTabs.="<h2 class=\"tab\">";
            $this->constructedTabs.=$tab['name']."</h2>";
            $this->constructedTabs.="<script type=\"text/javascript\">tp$this->tabpane.addTabPage( document.getElementById( \"tabPage$cnt\" ) );</script>";
            $this->constructedTabs.=$tab['content'];
            $this->constructedTabs.= "</div>\n";
        }

        $this->constructedTabs.="</div>";
        $this->constructedTabs .="<script type=\"text/javascript\">setupAllTabs();</script>";
        return $this->constructedTabs;
    }
    
    /**
    * Method to show the tabs
    * @return $str string
    */
    function show(){
        if (isset($this->tabs)){
            $this->_buildTabs();
            return $this->constructedTabs;
        }else{
            return $this->constructedTabs;
        }
    }    
}
?>
