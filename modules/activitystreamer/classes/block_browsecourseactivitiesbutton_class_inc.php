<?php

/**
 * activitystreamer blocks
 * 
 * Chisimba Activity Streamer blocks class
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
 * @package   activitystreamer
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright 2009 Paul Mungai
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */


// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


/**
 * Context blocks
 * 
 * Chisimba Context blocks class
 * 
 * @category  Chisimba
 * @package   activitystreamer
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright 2009 Paul Mungai
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */
class block_browsecourseactivitiesbutton extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
        try {
            $this->objLanguage =  $this->getObject('language', 'language');
            $this->objUser =  $this->getObject('user', 'security');
            $this->title = ucwords($this->objLanguage->code2Txt('mod_activitystreamer_courseupdatesbtn', 'activitystreamer', NULL, 'Course Updates button'));
            
            $this->loadClass('checkbox', 'htmlelements');
        } catch (customException $e) {
            customException::cleanUp();
        }
    }
    
    /**
    * Standard block show method. It uses the renderform
    * class to render the login box
    */
    public function show()
    {
        //$objTab = $this->newObject('jqtabs', 'htmlelements');

        $objUtils = $this->getObject('activityutilities', 'activitystreamer');
        $objSysConfig  = $this->getObject('altconfig','config');
        $this->appendArrayVar('headerParams', '
        	<script type="text/javascript">
        		var uri = "'.str_replace('&amp;','&',$this->uri(array('action' => 'jsoncourseactivities', 'module' => 'activitystreamer'))).'"; 
        		var baseuri = "'.$objSysConfig->getsiteRoot().'index.php"; </script>');
        
        //Ext stuff
        $ext =$this->getJavaScriptFile('ext-3.0-rc2/adapter/ext/ext-base.js', 'ext');
        $ext .=$this->getJavaScriptFile('ext-3.0-rc2/ext-all.js', 'ext');
        $ext .=$this->getJavaScriptFile('searchcourseactivitiesbutton.js', 'activitystreamer');
        $ext .=$this->getJavaScriptFile('ext-3.0-rc2/examples/shared/examples.js', 'ext');
       
        $ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css', 'ext').'" type="text/css" />';
        $ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/examples/grid/grid-example.css', 'ext').'" type="text/css" />';
        $ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/examples/shared/examples.css', 'ext').'" type="text/css" />';
        $this->appendArrayVar('headerParams', $ext);
        
	//Div to render content
        $str = '<input type="button" id="course-activities-btn" value="Latest Course Updates" /><br /><br />
        <div id="course-activities-view" class="x-hidden">
         <div class="x-window-header">'.$this->title.'</div>
          <div id="course-tabs">
          <!-- Auto create tab 1 -->
           <div class="course-tab_1" title="'.$this->title.'" id="course-activity-topic-grid">
           </div>
          <!-- Auto create tab 2 -->
          <!--<div class="x-tab" title="Tab 2">
            <p>... Tab 2!</p>
          </div>-->
         </div>
       </div>';

        return $str;

    }
}
?>
