<?php

/**
* Display Context
* 
* Class to render context lists
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
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2008 Tohir Solomons
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id$
* @link      http://avoir.uwc.ac.za
* @see       core
*/
/* -------------------- dbTable class ----------------*/
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
* Display Context
* 
* Class to render context lists
* 
* @category  Chisimba
* @package   context
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2008 Tohir Solomons
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   Release: @package_version@
* @link      http://avoir.uwc.ac.za
* @see       core
*/
class displaycontext extends object
{
    /**
    * @var object $objUser : The user Object
    */
    public $objUser;
    
    /**
     * @var string $module Module to go to when editing/deleting a course
     */
    public $module='contextadmin';
    
    /**
     * @var $noImage - Image to display when no course image is available
     */
    private $noImage;
    
    
    /**
    *Initialize by send the table name to be accessed
    */
    public function init()
    {
        $this->objContextImage = $this->getObject('contextimage');
        $this->objUser = $this->getObject('user', 'security');
        $this->objUserContext = $this->getObject('usercontext');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
        
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $objIcon->setIcon('imagepreview');
        
        $this->noImage = $objIcon->show();
        
        
    }
    
    /**
     * Method to display the information of a context in a block
     * @param array $context
     * @param boolean $showEditDeleteLinks - Show Edit Links if applicable. THis function will determine it
     * @param boolean $includeFeatureBox - Display in a feature box or not
     * @return string
     */
    public function formatContextDisplayBlock($context, $showEditDeleteLinks=TRUE, $includeFeatureBox=TRUE)
    {
        $canEdit = FALSE;
        
        $link = new link ($this->uri(array('action'=>'joincontext', 'contextcode'=>$context['contextcode'])));
        $link->link = $context['title'];
        
        // Add Permissions
        
        
        
        
        
        
        $str = '';
        
        // Get Context Image
        $contextImage = $this->objContextImage->getContextImage($context['contextcode']);
        
        // Show if it has an image
        if ($contextImage == FALSE) {
            $contextImage = $this->noImage;
        } else {
            $contextImage = '<img src="'.$contextImage.'" />';
        }
        
        $contextImageLink = new link ($this->uri(array('action'=>'joincontext', 'contextcode'=>$context['contextcode'])));
        $contextImageLink->link = $contextImage;
        
        $str = '';
        
        $str .= '<p><strong>'.ucwords($this->objLanguage->code2Txt('mod_context_contextcode', 'system', NULL, '[-context-] Code')).'</strong>: '.$context['contextcode'].'</p>';
        
        //$str .= $context['about'];
        
        $lecturers = $this->objUserContext->getContextLecturers($context['contextcode']);
        if (count($lecturers) > 0) {
            $str .= '<p><strong>'.ucwords($this->objLanguage->code2Txt('word_lecturers', 'system', NULL, '[-authors-]')).'</strong>: ';
            $divider = '';
            
            foreach ($lecturers as $lecturer)
            {
                $str .= $divider.$lecturer['firstname'].' '.$lecturer['surname'];
                $divider = ', ';
                
                if ($this->objUser->userId() == $lecturer['userid']) {
                    $canEdit = TRUE;
                }
            }
        }
        
        switch (strtolower($context['access']))
        {
            case 'public': $access = $this->objLanguage->code2Txt('mod_context_publiccontextexplanation', 'context', NULL, 'This is an open [-context-] that any user may enter'); break;
            case 'open': $access = $this->objLanguage->code2Txt('mod_context_opencontextexplanation', 'context', NULL, 'This is an open [-context-] that any logged-in user may enter'); break;
            default: $access = $this->objLanguage->code2Txt('mod_context_privatecontextexplanation', 'context', NULL, 'This is a closed [-context-] only accessible to members'); break;            
        }
        
        $str .= '<p><strong>'.ucwords($this->objLanguage->languageText('mod_context_accessettings', 'context', 'Access Settings')).'</strong>: '.$context['access'].' - '.$access.'</p>';
        //$str .= '<p><strong>Contact</strong>: '.$access.'</p>';      
        
        //$content = $contextImage.'<div style="float: left;">'.$str.'</div>';
        //$content .= '<br clear="both" />';
        
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $table->addCell($contextImageLink->show(), 120);
        $table->addCell($str);
        
        //$content = $table->show();
        $content = $str;
        
        $title = '';
        
        if ($this->objUser->isAdmin()) {
            $canEdit = TRUE;
        }
        
        if ($showEditDeleteLinks && $canEdit) {
            $objIcon = $this->newObject('geticon', 'htmlelements');
            $objIcon->setIcon('edit');
            
            $editLink = new link ($this->uri(array('action'=>'edit', 'contextcode'=>$context['contextcode']), $this->module));
            $editLink->link = $objIcon->show();
            
            $editOptions = ' '.$editLink->show();
            
            $objIcon->setIcon('delete');
            
            $deleteLink = new link ($this->uri(array('action'=>'delete', 'contextcode'=>$context['contextcode']), $this->module));
            $deleteLink->link = $objIcon->show();
            
            $editOptions .= ' '.$deleteLink->show();
            
            $title = '<div style="float: right">'.$editOptions.'</div>';
        }
        
        $title .= $link->show();
        
        if ($includeFeatureBox) {
            $objFeatureBox = $this->newObject('featurebox', 'navigation');
            return $objFeatureBox->show($title, $content);
        } else {
            $header = new htmlHeading();
            $header->type = 3;
            $header->str = $title;
            return $header->show().$content;
        }
        
    }

}
?>