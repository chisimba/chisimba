<?php

/**
* Context Forms
* 
* This class generates commonly used forms related to the context module
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
* Context Forms
* 
* This class generates commonly used forms related to the context module
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
class contextforms extends object
{
    /**
    * @var string $formAction : Action URL to point forms to
    */
    public $formAction = 'updatecontext';
    
    /**
    * @var string $formModule : Module URL to point forms to
    */
    public $formModule = 'context';
    
    /**
     * Constructor
     */
    public function init()
    {
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     * Method to generate an edit context settings form
     * @param array $context Current Context Settings
     * @return str
     */
    public function editContextForm($context)
    {
        $header = new htmlheading();
        $header->type = 1;
        $header->str = $this->objLanguage->languageText('word_edit', 'system', 'Edit').': '.$context['title'];
        
        $str = $header->show();
        
        $title = new textinput('title');
        $title->size = 50;
        
        if ($context != NULL) {
            $title->value = htmlentities($context['title']);
        }
        
        
        $titleLabel = new label ($this->objLanguage->languageText('word_title', 'system', 'Title'), 'input_title');
        
        $status = new dropdown ('status');
        //$status->setBreakSpace('<br />');
        $status->addOption('Published', $this->objLanguage->languageText('word_published', 'system', 'Published'));
        $status->addOption('Unpublished', $this->objLanguage->languageText('word_unpublished', 'system', 'Unpublished'));
        
        if ($context != NULL) {
            $status->setSelected($context['status']);
        }
        
        $access= new radio ('access');
        $access->setBreakSpace('<br />');
        $access->addOption('Public', '<strong>'.$this->objLanguage->languageText('word_public', 'system', 'Public').'</strong> - <span class="caption">'.ucfirst($this->objLanguage->code2Txt('mod_context_publiccontextdescription', 'context', NULL, '[-context-] can be accessed by all users, including anonymous users')).'</span>');
        $access->addOption('Open', '<strong>'.$this->objLanguage->languageText('word_open', 'system', 'Open').'</strong> - <span class="caption">'.ucfirst($this->objLanguage->code2Txt('mod_context_opencontextdescription', 'context', NULL, '[-context-] can be accessed by all users that are logged in')).'</span>');
        $access->addOption('Private', '<strong>'.$this->objLanguage->languageText('word_private', 'system', 'Private').'</strong> - <span class="caption">'.$this->objLanguage->code2Txt('mod_context_privatecontextdescription', 'context', NULL, 'Only [-context-] members can enter the [-context-]').'<span class="caption">');
        
        if ($context != NULL) {
            $access->setSelected($context['access']);
        } else {
            $access->setSelected('Public');
        }
        
        
        $table = $this->newObject('htmltable', 'htmlelements');
        
        if ($context != NULL) {
            $table->startRow();
            $table->addCell(ucwords($this->objLanguage->code2Txt('mod_context_contextcode', 'context', NULL, '[-context-] Code')).':', 100);
            $table->addCell('<strong>'.$context['contextcode'].'</strong>');
            $table->endRow();
        } else {
            $table->startRow();
            $table->addCell($codeLabel->show(), 100);
            $table->addCell($code->show().' <span id="contextcodemessage"></span>');
            $table->endRow();
        }
        
        
        $table->startRow();
        $table->addCell($titleLabel->show().':');
        $table->addCell($title->show());
        $table->endRow();
        
        $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell('&nbsp;');
        $table->endRow();
        
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('word_status', 'system', 'Status').':');
        $table->addCell($status->show());
        $table->endRow();
        
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('word_access', 'system', 'Access').':');
        $table->addCell($access->show());
        $table->endRow();
        
        
        
        $objSelectImage = $this->getObject('selectimage', 'filemanager');
        $objSelectImage->context = TRUE;
        
        $table2 = $this->newObject('htmltable', 'htmlelements');
        $table2->startRow();
        $table2->addCell($table->show(), 600, NULL, NULL, NULL, 'colspan="2"');
        $table2->addCell($objSelectImage->show());
        $table2->endRow();
        
        $table2->startRow();
        $table2->addCell('&nbsp;');
        $table2->addCell('&nbsp;');
        $table2->addCell('&nbsp;');
        $table2->endRow();
        
        $table2->startRow();
        $table2->addCell(ucwords($this->objLanguage->code2Txt('mod_context_aboutcontext', 'context', NULL, 'About [-context-]')).':', 100);
        
        $htmlEditor = $this->newObject('htmlarea', 'htmlelements');
        $htmlEditor->name = 'about';
        $htmlEditor->toolbarSet = 'simple';
        
        if ($context != NULL) {
            $htmlEditor->value = $context['about'];
        }
        
        $table2->addCell($htmlEditor->show(), NULL, NULL, NULL, NULL, 'colspan="2"');
        $table2->endRow();
        
        $table2->startRow();
        $table2->addCell('&nbsp;');
        $table2->addCell('&nbsp;');
        $table2->addCell('&nbsp;');
        $table2->endRow();
        
        
        $table2->startRow();
        $table2->addCell('&nbsp;', 100);
        
        if ($context == NULL) {
            $button = new button ('savecontext', $formButton);
        } else {
            $button = new button ('savecontext', ucwords($this->objLanguage->code2Txt('mod_context_updatecontext', 'context', NULL, 'Update [-context-]')));
        }
        $button->setToSubmit();
        
        $table2->addCell($button->show(), NULL, NULL, NULL, NULL, 'colspan="2"');
        $table2->endRow();
        
        $form = new form ('createcontext', $this->uri(array('action'=>$this->formAction), $this->formModule));
        
        $form->addToForm($table2->show());
        
        if ($context != NULL) {
            $hiddenInput = new hiddeninput('contextcode', $context['contextcode']);
            $form->addToForm($hiddenInput->show());
        }
        
        $form->addRule('title', $this->objLanguage->code2Txt('mod_context_entertitleofcontext', 'context', NULL, 'Please enter the title of your [-context-]'),'required');
        
        $str .= $form->show();
        
        return $str;
    }


}
?>