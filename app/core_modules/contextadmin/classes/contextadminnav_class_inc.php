<?php

/**
 * Context Admin Navigation
 * 
 * Class to generate a navigation for context admin
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
 * @package   contextadmin
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * Context Admin Navigation
 * 
 * Class to generate a navigation for context admin
 * 
 * @category  Chisimba
 * @package   contextadmin
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class contextadminnav extends object
{
    
    /**
     * Constructor
     */
    public function init()
    {
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     * Method to display the navigation
     */
    public function show()
    {
        $str = '';
        
        $heading = new htmlheading();
        $heading->type = 3;
        $heading->str = ucwords($this->objLanguage->code2Txt('mod_contextadmin_name', 'contextadmin', NULL, '[-context-] Admin'));
        
        $str = $heading->show();
        
        $str .= '<ul id="nav-secondary">';
        
        $mycoursesLink = new link ($this->uri(NULL));
        $mycoursesLink->link = ucwords($this->objLanguage->code2Txt('phrase_mycourses', 'system', NULL, 'My [-contexts-]'));
        
        $str .= '<li>'.$mycoursesLink->show().'</li>';
        
        $objUser = $this->getObject('user', 'security');
        
        if ($objUser->isAdmin() || $objUser->isLecturer()) {
        
            $createCourse = new link ($this->uri(array('action'=>'add')));
            $createCourse->link = ucwords($this->objLanguage->code2Txt('mod_contextadmin_createcontext', 'contextadmin', NULL, 'Create [-context-]'));
            
            $str .= '<li>'.$createCourse->show().'</li>';
        }
        
        $str .= '</ul>';
        
        $heading = new htmlheading();
        $heading->type = 3;
        $heading->str = ucwords($this->objLanguage->code2Txt('mod_contextadmin_searchforcontext', 'contextadmin', NULL, 'Search for [-context-]'));
        
        $str .= '<br />'.$heading->show();
        
        $form = new form ('searchform', $this->uri(array('action'=>'search')));
        $form->method = 'GET';
        
        $module = new hiddeninput('module', $this->getParam('module', 'contextadmin'));
        $action = new hiddeninput('action', 'search');        
        
        $textinput = new textinput ('search', $this->getParam('search'));
        $button = new button ('searchbutton', $this->objLanguage->languageText('word_search', 'system', 'Search'));
        $button->setToSubmit();
        
        $form->addToForm($module->show().$action->show().$textinput->show().'<br />'.$button->show());
        
        $str .= $form->show();
        
        
        
        $heading = new htmlheading();
        $heading->type = 3;
        $heading->str = ucwords($this->objLanguage->code2Txt('phrase_browsecourses', 'system', NULL, 'Browse [-contexts-]'));
        
        $str .= $heading->show();
        
        $str .= $this->getAlphaListingTable();
        
        return $str;
    }
    
    /**
     * Method to generate an alphabetical navigation of courses
     */
    public function getAlphaListingTable()
    {
        $objContext = $this->getObject('dbcontext', 'context');
        $sql = 'SELECT title FROM tbl_context ORDER BY title';
        $results = $objContext->getArray($sql);
        
        $available = array();
        
        if (count($results) > 0) {
            foreach ($results as $title)
            {
                $letter = substr(strtoupper(trim($title['title'])), 0, 1);
                
                @$available[$letter]++;
            }
        }
        
        
        // Character Bounds for A, Z
        $lBound=65;
        $uBound=90;
        
        $perLine = 7;
        $counter = 0;
        
        $numContexts = ucwords($this->objLanguage->code2Txt('mod_contextadmin_numcontexts', 'contextadmin', NULL, '[-num-] [-contexts-]'));
        $oneContext = ucwords($this->objLanguage->code2Txt('mod_contextadmin_onecontext', 'contextadmin', NULL, '1 [-context-]'));
        
        $table = $this->newObject('htmltable', 'htmlelements');
        
        for ($i=$lBound; $i<=$uBound; $i++)
        {
            if ($counter % $perLine == 0) {
                $table->startRow();
            }
            
            if (array_key_exists(chr($i), $available)) {
                $alphaLink = new link ($this->uri(array('action'=>'browseother', 'letter'=>chr($i))));
                $alphaLink->link = chr($i);
                
                if ($available[chr($i)] == 1) {
                    $alphaLink->title = $oneContext;
                    $alphaLink->alt = $oneContext;
                } else {
                    $alphaLink->title = str_replace('[-num-]', $available[chr($i)], $numContexts);
                    $alphaLink->alt = str_replace('[-num-]', $available[chr($i)], $numContexts);
                }
                
                $str = $alphaLink->show();
            } else {
                $str = chr($i);
            }
            
            $table->addCell($str, round(100/$perLine).'%');
            
            
            $counter++;
            
            if ($counter % $perLine == 0) {
                $table->endRow();
            }
        }
        
        //echo $perLine - ($i % $perLine);
        
        if ($counter % $perLine != 0) {
            for ($j = ($perLine - ($counter % $perLine)); $j--; $j >= 0)
            {
                $table->addCell('&nbsp;', round(100/$perLine).'%');
            }
            
            $table->endRow();
        }
        
        $table->extra = 'border="1"';
        
        return $table->show();
    }
    
    
    /**
     * Method to generate an alphabetical navigation of courses
     */
    public function getAlphaListingAjax()
    {
        $objContext = $this->getObject('dbcontext', 'context');
        $sql = 'SELECT title FROM tbl_context ORDER BY title';
        $results = $objContext->getArray($sql);
        
        $available = array();
        
        if (count($results) > 0) {
            foreach ($results as $title)
            {
                $letter = substr(strtoupper(trim($title['title'])), 0, 1);
                
                @$available[$letter]++;
            }
        }
        
        $alphaListing = '';
        $divider = ' | ';
        
        // Character Bounds for A, Z
        $lBound=65;
        $uBound=90;
        
        $numContexts = ucwords($this->objLanguage->code2Txt('mod_contextadmin_numcontexts', 'contextadmin', NULL, '[-num-] [-contexts-]'));
        $oneContext = ucwords($this->objLanguage->code2Txt('mod_contextadmin_onecontext', 'contextadmin', NULL, '1 [-context-]'));
        
        $table = $this->newObject('htmltable', 'htmlelements');
        
        for ($i=$lBound; $i<=$uBound; $i++)
        {
            
            if (array_key_exists(chr($i), $available)) {
                $alphaLink = new link ("javascript:getContexts('".chr($i)."');");
                $alphaLink->link = chr($i);
                
                if ($available[chr($i)] == 1) {
                    $alphaLink->title = $oneContext;
                    $alphaLink->alt = $oneContext;
                } else {
                    $alphaLink->title = str_replace('[-num-]', $available[chr($i)], $numContexts);
                    $alphaLink->alt = str_replace('[-num-]', $available[chr($i)], $numContexts);
                }
                
                $str = $alphaLink->show();
            } else {
                $str = chr($i);
            }
            
            $alphaListing .= $str.$divider;
        }
        
        
        return $alphaListing;
    }
}

?>