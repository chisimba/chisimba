<?php

/**
 * Context blocks
 * 
 * Chisimba Context blocks class
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
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
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
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class block_browsecontext extends object
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
			$this->title = ucwords($this->objLanguage->code2Txt('mod_context_browseallcontexts', 'context', NULL, 'Browse All [-contexts-]'));
            //$this->title = ucWords($this->objLanguage->code2Txt("mod_context_contexts",'context'));            
            $this->blockType = 'none';
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
				
        $objTab = $this->newObject('tabpane', 'htmlelements');
        $objUtils = $this->getObject('utilities', 'context');
        $objSysConfig  = $this->getObject('altconfig','config');
        $objNav = $this->getObject('contextadminnav', 'contextadmin');
        $str = $this->objLanguage->languageText('word_browse', 'glossary', 'Browse').': '.$objNav->getAlphaListingAjax();
        $str2 = '<div id="browseusercontextcontent"></div>';
        $str .= '<div id="browsecontextcontent"></div>';
        $str3 = '<div id="browseallcontextcontent"></div>';
		$isAdmin = ($this->objUser->isAdmin()) ? "true" : "false";
        $str .= $this->getJavaScriptFile('contextbrowser.js');        
        
        $this->appendArrayVar('headerParams', '
        	<script type="text/javascript">
        	var pageSize = 500;
        	var lang = new Array();
        	lang["mycontext"] =   "'.ucWords($this->objLanguage->code2Txt('phrase_mycourses', 'system', NULL, 'My [-contexts-]')).'";
        	lang["contexts"] =   "'.ucWords($this->objLanguage->code2Txt('wordcontext', 'system', NULL, '[-contexts-]')).'";
        	lang["context"] =   "'.ucWords($this->objLanguage->code2Txt('wordcontext', 'system', NULL, '[-context-]')).'";
        	lang["othercontext"] =   "'.ucWords($this->objLanguage->code2Txt('phrase_othercourses', 'system', NULL, 'Other [-contexts-]')).'";
        	lang["searchcontext"] =   "'.ucWords($this->objLanguage->code2Txt('phrase_allcourses', 'system', NULL, 'Search [-contexts-]')).'";
        	lang["contextcode"] =   "'.ucWords($this->objLanguage->code2Txt('mod_context_contextcode', 'system', NULL, '[-contexts-] Code')).'";
        	lang["lecturers"] =   "'.ucWords($this->objLanguage->code2Txt('word_lecturers', 'system', NULL, '[-authors-]')).'";
        	
			var uri = "'.str_replace('&amp;','&',$this->uri(array('module' => 'context', 'action' => 'jsonlistcontext'))).'"; 
        	var usercontexturi = "'.str_replace('&amp;','&',$this->uri(array('module' => 'context', 'action' => 'jsonusercontexts'))).'"; 
			var othercontexturi = "'.str_replace('&amp;','&',$this->uri(array('module' => 'context', 'action' => 'jsonusercontexts'))).'"; 
        		var baseuri = "'.$this->uri().'index.php";
        		var isAdmin = '.$isAdmin.';
        		contextPrivateMessage="'.$this->objLanguage->code2Txt('mod_context_privatecontextexplanation', 'context', NULL, 'This is a closed [-context-] only accessible to members').'"; </script>');
		
		//Ext stuff		
		$objExtJS = $this->getObject('extjs','htmlelements');
		$objExtJS->show();
		
		$ext =$this->getJavaScriptFile('Ext.ux.grid.Search.js', 'context');		
		$ext .=$this->getJavaScriptFile('usercontextslist.js', 'context');
		$ext .=$this->getJavaScriptFile('othercontexts.js', 'context');
		$ext .=$this->getJavaScriptFile('search.js', 'context');		
        $ext .=$this->getJavaScriptFile('extcontexbrowser.js', 'context');
      
        $this->appendArrayVar('headerParams', $ext);       
		
		return '<div id="contextbrowser"></div>				
				<p>&nbsp;</p>';
        
    }
}
?>
