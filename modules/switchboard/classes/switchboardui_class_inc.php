<?php
/**
 *
 * User Interface code for Switchboard
 *
 * User Interface code for Switchboard. This class builds the user interface
 * elements of the switchboard module default block.
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
 * @package   switchboard
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * User Interface code for Switchboard
 *
 * User Interface code for Switchboard. This class builds the user interface
 * elements of the switchboard module default block.
*
* @package   switchboard
* @author    Derek Keats derek@dkeats.com
*
*/
class switchboardui extends object
{
    
    /**
    * 
    * @var string $objLanguage String object property for holding the 
    * language object
    * @access public
    * 
    */
    public $objLanguage;
    public $objUser;

    /**
    *
    * Constructor for the switchboard UI object
    * 
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->mode = $this->getParam('mode', 'add');
        if ($this->mode == 'edit') {
            $id = $this->getParam('id', NULL);
            $this->loadData($id);
        } else {
            $this->iconurl = NULL;
            $this->link = NULL;
            $this->title = NULL;
            $this->description = NULL;
        }
    }
    
  
    /**
     *
     * Insert an add icon for use by javacript. It will be visitble
     * when you are in edit mode, but invisible when you are in add 
     * mode. After a save, it will be toggled to visible.
     * 
     * @param string $mode The edit|add mode
     * @return string The rendered icon
     * @access private
     *  
     */
    private function insertAddIcon($mode)
    {
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $link =  $this->uri(
           array("action" => "linkedit"),
           'switchboard');
        $addlink = new link($link);
        $objIcon->setIcon('add');
        
        $addlink->link = $objIcon->show();
        if ($mode == 'add') { 
            $showCss = "style='visibility:hidden'";
        } else {
            $showCss = " style='visibility:show' ";
        }
        return "&nbsp; <span class='conditional_add' " 
          . $showCss . ">" . $addlink->show() 
          . "</span>";
    }

    /**
     *
     * Make a heading for the form
     *
     * @return string The text of the heading
     * @access private
     *
     */
    private function makeHeading()
    {
        $this->loadClass('htmlheading','htmlelements');
        $this->loadClass('link','htmlelements');
        // Get heading based on whether it is edit or add.
        if ($this->mode == 'edit') {
            $h = $this->objLanguage->languageText(
              'mod_switchboard_heading_edit', 'switchboard', "Edit link");
        } else {
            $h = $this->objLanguage->languageText(
              'mod_switchboard_heading_new','switchboard', "New link");
        }
        // Setup and show heading.
        $header = new htmlHeading();
        $header->str = $h . $this->insertAddIcon($this->mode);
        $header->type = 2;
        return $header->show();
    }
    
    /**
     *
     * Show the links that make up the switchboard.
     * 
     * @return string The rendered links
     * @access public
     *  
     */
    public function showLinks()
    {
        $objData = $this->getObject("dbswitchboardlinks", "switchboard");
        $arData = $objData->getLinks();
        $this->loadClass('htmltable','htmlelements');
        $this->loadClass('link','htmlelements');
        $this->loadClass('button','htmlelements');
        // Setup table for display layout.
        $table = $this->newObject('htmltable', 'htmlelements');
        // Edit & delete icons
        $edIcon = $this->newObject('geticon', 'htmlelements');
        $edIcon->setIcon('edit');
        $delIcon = $this->newObject('geticon', 'htmlelements');
        $delIcon->setIcon('delete');
        foreach ($arData as $linkItem) {
            $id = $linkItem['id'];
            $title = $linkItem['title'];
            $linkUrl = $linkItem['link'];
            $iconUrl =  $linkItem['iconurl'];
            $activeLink = new link($linkUrl);
            $activeLink->cssClass = "switchboard_icon_link";
            $activeLink->link = "<img src='$iconUrl' class='switchboard_icon' />";

            $description =  "<div class='sb_desc'>" . $linkItem['description'] . "</div>";
            $table->startRow(NULL, "ROW_" . $id);
            $table->addCell($activeLink->show());
            $table->addCell($title . '<br />' . $description);
            if ($this->objUser->isAdmin()) {
                $edUrl = $this->uri(array(
                    'action' => 'linkedit',
                    'mode' => 'edit',
                    'id' => $id
                    )
                );
                $delUrl = 'javascript:void(0);';
                $edLink = new link($edUrl);
                $edLink->link = $edIcon->show();
                $delLink = new link($delUrl);
                $delLink->cssId = $id;
                $delLink->cssClass = "dellink";
                $delLink->link = $delIcon->show();
                $table->addCell($edLink->show());
                $table->addCell($delLink->show());
            }
            $table->endRow();
        }
        return $table->show();
    }

    /**
     * 
     * Show the form for adding / editing a link
     *
     * @return string The formatted form
     * @access public
     * 
     */
    public function showLinkEditForm()
    {
        // Serialize language items to Javascript.
        $arrayVars['status_success'] = "mod_switchboard_status_success";
        $arrayVars['status_fail'] = "mod_switchboard_status_fail";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'switchboard');
        // Load the jquery validate plugin.
        $this->appendArrayVar('headerParams',
        $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js',
          'jquery'));
        // Load the HTML elements needed for the form.
        $this->loadClass('htmltable','htmlelements');
        $this->loadClass('textinput','htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $mode = $this->mode;
        $id = $this->getParam('id', NULL);
        $iconurl = $this->iconurl;
        $link = $this->link;
        $title = $this->title;
        $description = $this->description;
        // Table for the form.
        $table = $this->newObject('htmltable', 'htmlelements');
        
        // Input for the title.
        $label = $this->objLanguage->languageText(
          'mod_switchboard_linktitle', 'switchboard', "Link title");
        $table->startRow();
        $table->addCell($label);
        $textinput = new textinput('title');
        $textinput->size = 60;
        $textinput->setValue($title);
         $textinput->cssClass = 'required';
        $table->addCell($textinput->show());
        $table->endRow();
        
        // Input for the title.
        $label = $this->objLanguage->languageText(
          'mod_switchboard_linkurl', 'switchboard', "URL for link");
        $table->startRow();
        $table->addCell($label);
        $textinput = new textinput('link');
        $textinput->size = 60;
        $textinput->setValue($link);
         $textinput->cssClass = 'required';
        $table->addCell($textinput->show());
        $table->endRow();
        
        // Input for the title.
        $label = $this->objLanguage->languageText(
          'mod_switchboard_iconurl', 'switchboard', "Icon URL");
        $table->startRow();
        $table->addCell($label);
        $textinput = new textinput('iconurl');
        $textinput->size = 60;
        $textinput->setValue($iconurl);
         $textinput->cssClass = 'required';
        $table->addCell($textinput->show());
        $table->endRow();
        
        // Input for the description
        $label = $this->objLanguage->languageText(
          'mod_switchboard_description', 'switchboard', "Link description");
        $table->startRow();
        $table->addCell($label);
        $textinput = new textinput('description');
        $textinput->size = 60;
        $textinput->setValue($description);
         $textinput->cssClass = 'required';
        $table->addCell($textinput->show());
        $table->endRow();
        
        // Save button
        $table->startRow();
        $table->addCell("&nbsp;");
        $buttonTitle = $this->objLanguage->languageText('word_save');
        $button = new button('submitLink', $buttonTitle);
        $button->setToSubmit();
        $table->addCell($button->show());
        $table->endRow();
        
        // Insert a message area for Ajax result to display.
        $msgArea = "<br /><div id='save_results' class='ajax_results'></div>";
        
        // Add hidden fields for use by JS
        $hiddenFields = "\n\n";
        $hidMode = new hiddeninput('mode');
        $hidMode->cssId = "mode";
        $hidMode->value = $mode;
        $hiddenFields .= $hidMode->show() . "\n";
        $hidId = new hiddeninput('id');
        $hidId->cssId = "id";
        $hidId->value = $id;
        $hiddenFields .= $hidId->show() . "\n\n";
        
         // Createform, add fields to it and display.
        $formData = new form('linkEditor', NULL);
        $formData->addToForm(
            $this->makeHeading()
          . $table->show()
          . $hiddenFields
          . $msgArea);
        return $formData->show();
    }
    
    /**
     *
     * For editing, load the data according to the ID provided. It
     * loads the data into object properties.
     *
     * @param string $id The id of the record to load
     * @return boolean TRUE|FALSE
     * @access private
     *
     */
    private function loadData($id)
    {
        $objDb = $this->getObject('dbswitchboardlinks', 'switchboard');
        $arData = $objDb->getLinkById($id);
        if (!empty($arData)) {
            foreach ($arData[0] as $key=>$value) {
                $this->$key =  $value;
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
}
?>