<?php
/**
 *
 * User Interface code for Slate
 *
 * User Interface code for slate. This class builds the user interface
 * elements of the slate module add/edit block.
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
 * @package   slate
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
 * User Interface code for Slate
 *
 * User Interface code for slate. This class builds the user interface
 * elements of the slate module add/edit block.
*
* @package   slate
* @author    Derek Keats derek@dkeats.com
*
*/
class slateui extends object
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
    * Constructor for the slate UI object
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
            $this->page = NULL;
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
    private function insertAddIcon($mode, $labelText=NULL)
    {
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $link =  $this->uri(
           array("action" => "edit", "mode" => "add"),
           'slate');
        $addlink = new link($link);
        $objIcon->setIcon('add');
        $addlink->link = $objIcon->show();
        if ($mode == 'add') {
            $showCss = "style='visibility:hidden'";
        } else {
            $showCss = " style='visibility:show' ";
        }
        return "&nbsp; <span class='conditional_add' "
          . $showCss . ">" . $labelText . $addlink->show()
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
              'mod_slate_heading_edit', 'slate', "Edit page");
        } else {
            $h = $this->objLanguage->languageText(
              'mod_slate_heading_new','slate', "New page");
        }
        // Setup and show heading.
        $header = new htmlHeading();
        $header->str = $h . $this->insertAddIcon($this->mode);
        $header->type = 2;
        return $header->show();
    }

    /**
     *
     * Show the pages that are available within existing slates.
     *
     * @return string The rendered links
     * @access public
     *
     */
    public function showPages()
    {
        $this->appendArrayVar('headerParams',
        $this->getJavaScriptFile('slate.js',
          'slate'));
        $objData = $this->getObject("dbslatepages", "slate");
        $arData = $objData->getPages();
        $this->loadClass('htmltable','htmlelements');
        $this->loadClass('link','htmlelements');
        //----------$this->loadClass('button','htmlelements');
        // Setup table for display layout.
        $table = $this->newObject('htmltable', 'htmlelements');
        // Edit & delete icons
        $edIcon = $this->newObject('geticon', 'htmlelements');
        $edIcon->setIcon('edit');
        $delIcon = $this->newObject('geticon', 'htmlelements');
        $delIcon->setIcon('delete');
        $d = $this->objLanguage->languageText(
              'mod_slate_defaultpage','slate', "The default slate page, it cannot be deleted.");
        $l = $this->objLanguage->languageText(
              'mod_slate_default','slate', "DEFAULT");
        $st = $this->objLanguage->languageText(
              'mod_slate_defaulttitle','slate', "Default slate");
        $linkUrl = $this->uri(array(),'slate');
        $al = new link($linkUrl);
        $al->link = $st;
        $al->cssClass = "slate_page_link";
        $table->startRow();
        $table->addCell($al->show());
        $table->addCell($l);
        $table->addCell($d);
        if ($this->objUser->isAdmin()) {
            $table->addCell("");
            $table->addCell("");
        }
        unset($d, $al, $l, $st);
        foreach ($arData as $linkItem) {
            $id = $linkItem['id'];
            $linkPage = $linkItem['page'];
            $linkUrl = $this->uri(array('page' => $linkPage), 'slate');
            $pageTitle = $linkItem['title'];
            $pageDesc = $linkItem['description'];
            $activeLink = new link($linkUrl);
            $activeLink->cssClass = "slate_page_link";
            $activeLink->link = $pageTitle;

            $description =  "<div class='slate_desc'>" . $pageDesc . "</div>";
            $table->startRow(NULL, "ROW_" . $id);
            $table->addCell($activeLink->show());
            $table->addCell($linkPage);
            $table->addCell($description);
            unset($activeLink);
            if ($this->objUser->isAdmin()) {
                $edUrl = $this->uri(array(
                    'action' => 'edit',
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
        if ($this->objUser->isAdmin()) {
            $ret = '<div class="slate_addlink">' . $this->insertAddIcon('showadd', "Add a page ") . "</div>";
        } else {
            $ret = NULL;
        }
        return $ret . $table->show() . $ret;
    }

    /**
     *
     * Show the page navigation for display in a narrow block
     *
     * @return string  The rendered page navigation
     * @access public
     *
     */
    public function showPageNav()
    {
        $objData = $this->getObject("dbslatepages", "slate");
        $arData = $objData->getPages();
        $this->loadClass('link','htmlelements');
        $ret = "";
        foreach ($arData as $linkItem) {
            $id = $linkItem['id'];
            $linkPage = $linkItem['page'];
            $linkUrl = $this->uri(array('page' => $linkPage), 'slate');
            $pageTitle = $linkItem['title'];
            $pageDesc = $linkItem['description'];
            $activeLink = new link($linkUrl);
            $activeLink->cssClass = "slate_page_link";
            $activeLink->link = $pageTitle;
            $ret .= $activeLink->show() . "<br />";
        }
        if ($this->objUser->isAdmin()) {
            $ret .= $this->insertAddIcon('showadd', "Add a page ");
        }
        return $ret;
    }


    /**
     *
     * Show the form for adding / editing a slate page
     *
     * @return string The formatted form
     * @access public
     *
     */
    public function showPageEditForm()
    {
        $this->appendArrayVar('headerParams',
        $this->getJavaScriptFile('editor.js',
          'slate'));
        // Serialize language items to Javascript.
        $arrayVars = array();
        $arrayVars['status_success'] = "mod_slate_status_success";
        $arrayVars['status_fail'] = "mod_slate_status_fail";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'slate');
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
        $page = $this->page;
        $title = $this->title;
        $description = $this->description;
        // Table for the form.
        $table = $this->newObject('htmltable', 'htmlelements');

        // Input for the title.
        $label = $this->objLanguage->languageText(
          'mod_slate_pagetitle', 'slate', "Page title");
        $table->startRow();
        $table->addCell($label);
        $textinput = new textinput('title');
        $textinput->size = 60;
        $textinput->setValue($title);
         $textinput->cssClass = 'required';
        $table->addCell($textinput->show());
        $table->endRow();

        // Input for the page.

        $label = $this->objLanguage->languageText(
          'mod_slate_page', 'slate', "Page number for link");
        $table->startRow();
        $table->addCell($label);
        if ($this->mode == 'edit' ) {
            $textinput = new hiddeninput('page');
            $extra = $page;
        } else {
            $textinput = new textinput('page');
            $textinput->size = 60;
            $textinput->cssClass = 'required';
            $extra = NULL;
        }
        $textinput->setValue($page);
        $table->addCell($textinput->show() . $extra);
        $table->endRow();

        // Input for the description
        $label = $this->objLanguage->languageText(
          'mod_slate_description', 'slate', "Page description");
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
        $button = new button('savePage', $buttonTitle);
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
        // A route back to manage slate.
        $back = $this->uri(array('action'=>'manage'), 'slate');
        $this->loadClass('link','htmlelements');
        $ln = new link($back);
        $ln->cssClass = "slate_page_link";
        $label = $this->objLanguage->languageText(
          'mod_slate_manage', 'slate', "Manage slates");
        $ln->link = $label;
        $ret = $ln->show() . "<br />";

         // Createform, add fields to it and display.
        $formData = new form('slatepageEditor', NULL);
        $formData->addToForm(
            $this->makeHeading()
          . $table->show()
          . $hiddenFields
          . $ret . $msgArea);
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
        $objDb = $this->getObject('dbslatepages', 'slate');
        $arData = $objDb->getPageById($id);
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