<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_postdelete_class_inc
 *
 * @author monwabisi
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_postdelete extends object {

    var $objLanguage;
    var $objPost;

    //put your code here
    function init() {
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->title = "Delete post/reply";
        $this->objPost = $this->getObject('dbpost', 'discussion');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    function biuldForm() {
        $id = $this->getParam('id');
        // Get Post Info
        $post = $this->objPost->getPostWithText($id);
        $postDisplay = $this->objPost->displayPost($post);
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
        $elements = $objHighlightLabels->show();
        $elements .='<h1>' . $this->objLanguage->languageText('mod_discussion_moderatepost', 'discussion') . ': <em>' . $post['post_title'] . '</em></h1>';
        $elements .=$postDisplay;
        if ($post['postright'] - $post['postleft'] > 1) {
            $replies = $this->objPost->buildChildTree($post['post_id']);
        }
        if ($post['postleft'] == 1) {

            $objIcon->setIcon('warning');
            $message = '<p class="warning">' . $objIcon->show() . ' Warning </p> <p>This is the first/message post in this topic, and is the topic itself. If you wish to delete this topic, please go to the moderation options for this topic.</p>';
            $link = new link($this->uri(array('action' => 'moderatetopic', 'id' => $post['topic_id'])));
            $link->link = 'Moderate Topic';
            $message .= '<p>' . $link->show() . '</p>';
            echo $message;
        } else {

            $form = new form('deletepost', $this->uri(array('action' => 'moderatepostdeleteconfirm')));
            $hiddenId = new hiddeninput('id', $post['post_id']);
            $form->addToForm($hiddenId->show());

            $form->addToForm('<h3><strong>' . $this->objLanguage->languageText('mod_discussion_confirmdeletepost', 'discussion') . '</strong></h3>');

            $radio = new radio('confirmdelete');
            $radio->addOption('N', '<strong>' . $this->objLanguage->languageText('word_no') . '</strong> - ' . $this->objLanguage->languageText('mod_discussion_donotdeletepost', 'discussion'));
            $radio->addOption('Y', '<strong>' . $this->objLanguage->languageText('word_yes') . '</strong> - ' . $this->objLanguage->languageText('mod_discussion_deletepost', 'discussion'));
            $radio->setSelected('N');
            $radio->setBreakspace(' <br /><br />  ');

            $form->addToForm($radio->show());

            if ($post['postright'] - $post['postleft'] > 1) {
                $form->addToForm('<div class="discussionTangentIndent"><span class="warning">' . $this->objLanguage->languageText('word_warning') . '</span>: ' . $this->objLanguage->languageText('mod_discussion_warndeletepostreplies', 'discussion') . '<br /><br />' . $replies . '</div>');
            }

            $button = new button('save', $this->objLanguage->languageText('mod_discussion_confirmdelete', 'discussion'));
            $button->cssClass = 'delete';
            $button->setToSubmit();
            $form->addToForm('<p>' . $button->show() . '</p>');

            return $form->show();
        }
    }

    function show() {
        return $this->biuldForm();
    }

}

?>
