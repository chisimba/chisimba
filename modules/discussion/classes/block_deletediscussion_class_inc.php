<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_deletediscussion_class_inc
 *
 * @author monwabisi
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_deletediscussion extends object {

        //put your code here
        var $objLnaguage;
        var $objDiscussion;

        function init() {
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('radio', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('label', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                $this->loadClass('hiddeninput', 'htmlelements');
                $this->title = "Delete block";
                $this->objLanguage = $this->getObject('language', 'language');
                $this->objDiscussion = $this->getObject('dbdiscussion','discussion');
        }

        function buildForm() {
                $id = $this->getParam('id');
                $discussion = $this->objDiscussion->getDiscussion($id);
                $Header = & $this->getObject('htmlheading', 'htmlelements');
                $Header->type = 1;
                $Header->str = $this->objLanguage->languageText('mod_discussion_deletediscussion', 'discussion') . ': ' . $discussion['discussion_name'];
//                $Header->show();
//                $Header->show() . '<p><strong>' . $this->objLanguage->languageText('mod_discussion_discussiondescription', 'discussion') . '</strong>: ' . $discussion['discussion_description'] . '</p>';


                $return = "";
                if ($discussion['defaultdiscussion'] == 'Y') {
                        $return =  '<p class="error">' . $this->objLanguage->code2Txt('mod_discussion_defaultdiscussioncannotbedeleted', 'discussion') . '</p>';
                        $return .='<p>' . $this->objLanguage->languageText('mod_discussion_createanotherdiscussionfirst', 'discussion') . '</p>';

                        $returnLink = new link($this->uri(array('action' => 'administration')));
                        $returnLink->link = $this->objLanguage->languageText('mod_discussion_returntodiscussionadministration', 'discussion');

                        $return .= '<p>' . $returnLink->show() . '</p>';
                } else {


                        // First Cell - Deleting the Discussion
                        $firstCell = '<p><strong>' . $this->objLanguage->languageText('mod_discussion_optiononedeletediscussion', 'discussion') . '</strong></p>';

                        $firstCell .= '<p class="warning"><strong>' . $this->objLanguage->languageText('mod_discussion_warningphrase', 'discussion') . ':</strong> ' . $this->objLanguage->languageText('mod_discussion_entirediscussiondeleted', 'discussion') . '</p>';

                        // [[ JOC OK
                        $firstCell .= '<p>' . $this->objLanguage->languageText('mod_discussion_confirmdeletediscussion', 'discussion') . '</p>';

                        $form1 = new form('deletediscussion', $this->uri(array('action' => 'deletediscussionconfirm')));
                        $hiddenInput = new hiddeninput('id', $discussion['id']);
                        $form1->addToForm($hiddenInput->show());


                        $button = new button('deletediscussion');
                        $button->value = $this->objLanguage->languageText('mod_discussion_confirmdeletediscussionbutton', 'discussion');
                        $button->cssClass = 'delete';
                        $button->setToSubmit();

                        $button2 = new button('cancel', $this->objLanguage->languageText('word_cancel'), "window.location='" . $this->uri(array('action' => 'administration')) . "';");
                        $button2->cssClass = 'cancel';
                        //$button2->value = ;
                        // fix up Cancel

                        $form1->addToForm($button->show() . ' &nbsp; ' . $button2->show());

                        $firstCell .= $form1->show();

                        // Second Cell - Making it Invisible
                        $secondCell = '<p><strong>' . $this->objLanguage->languageText('mod_discussion_optiontwomakediscussioninvisible', 'discussion') . '</strong></p>';

                        $secondCell .= '<p>' . $this->objLanguage->languageText('mod_discussion_preservesdiscussioncontent', 'discussion') . '</p>';

                        $form2 = new form('makeinvisible', $this->uri(array('action' => 'changevisibilityconfirm', 'discussion')));
                        $radio = new radio('visible');
                        $radio->addOption('Y', $this->objLanguage->languageText('mod_discussion_makediscussionvisible', 'discussion'));
                        $radio->addOption('N', $this->objLanguage->languageText('mod_discussion_hidediscussion', 'discussion'));
                        $radio->setBreakSpace(' / ');

                        $radio->setSelected($discussion['discussion_visible']);

                        $form2->addToForm('<p>' . $radio->show() . '</p>');

                        $button = new button('changevisibility');
                        $button->value = $this->objLanguage->languageText('mod_discussion_updatediscussionvisibility', 'discussion');
                        $button->setToSubmit();

                        $form2->addToForm('<p>' . $button->show() . '</p>');

                        $hiddenInput = new hiddeninput('id', $discussion['id']);
                        $form2->addToForm($hiddenInput->show());

                        $secondCell .= $form2->show();

                        $table = $this->newObject('htmltable', 'htmlelements');
                        $table->cellpadding = 5;

                        $table->startRow();
                        $table->addCell($firstCell, '50%');
                        $table->addCell($secondCell, '50%');
                        $table->endRow();

                        $return .= $table->show();


                        $returnLink = new link($this->uri(array('action' => 'administration')));
                        $returnLink->link = $this->objLanguage->languageText('mod_discussion_returntodiscussionadministration', 'discussion');

                        $return .= '<p align="center">' . $returnLink->show() . '</p>';
                }
                return $return;
        }

        function show() {
                return $this->buildForm();
        }

}

?>
