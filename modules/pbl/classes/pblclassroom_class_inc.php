<?php
/**
* Class pblClassroom extends object.
* @author Fernando Martinez
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package pbl
* @version 1
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class to set up a virtual PBL Classroom
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 1
 */

class pblClassroom extends object
{
    public $entry;
    private $dbclassroom, $dbchat;
    private $dbloggedin;

    /**
    * Method to construct the class
    */
    public function init()
    {
        $this->dbclassroom = &$this->getObject('dbclassroom');
        $this->dbloggedin = &$this->getObject('dbloggedin');
        $this->dbcontent = &$this->getObject('dbcontent');
        $this->dbchat = &$this->getObject('dbchat');
        
        $this->objLanguage = &$this->getObject('language', 'language');
        
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
    }

    /**
    * Constructor method.
    */
    public function classroom()
    {
    }

    /**
    * Method to get the first scene in the case for display.
    *
    * @return
    */
    public function start()
    {
        $this->entry = $this->dbclassroom->getEntryPoint();
        $this->setSession('active', $this->entry);
    }

    /**
    * Method to generate html img tags for each image instruction found in a scene.
    *
    * @param string $str The scene to be parsed for images
    * @return
    */
    public function renderImgs(&$str)
    {
        while (($tagStart = strpos($str, '~img')) !== FALSE) {
            $tagEnd = strpos($str, ")", $tagStart);
            if ($tagEnd === FALSE){
                break;
            }
            $length = $tagEnd - $tagStart + 1;
            $tag = substr($str, $tagStart + 5, $length-6);
            $imgTag = "<img src='" . $tag . "' />";
            $str = substr_replace($str, $imgTag, $tagStart, $length);
        }
    }

    /**
    * Method to render a question with a set of choices.
    *
    * @param string $str The scene containing the question
    * @return
    */
    public function renderChoice(&$str)
    {
        $html = "";
        while (($tagStart = strpos($str, '~choice')) !== FALSE) {
            $tagEnd = strpos($str, ")", $tagStart);
            if ($tagEnd === FALSE){
                break;
            }
            $choiceStr = substr($str, $tagStart + 8, $tagEnd - $tagStart-8);
            $this->setSession('choicestr', $choiceStr);
            $html = substr($str, 0, strlen($str) - ($tagEnd - $tagStart + 1));
            $str = $html;
        }
    }

    /**
    * Method to render a multiple choice question using checkboxes for the answer.
    *
    * @param string $str The scene containing the multiple choice question
    * @return
    */
    public function renderMcq(&$str)
    {
        $lbOk = $this->objLanguage->languageText('word_ok');
        $html = '';
        $captionEnd = 0;
        while (($tagStart = strpos($str, '~mcq')) !== FALSE) {
            $tagEnd = strpos($str, ')', $tagStart);
            if ($tagEnd === FALSE){
                break;
            }
            $choiceEnd = strpos($str, ":", $tagStart);
            if ($choiceEnd === FALSE){
                break;
            }
            $length = $captionEnd - $tagStart + 1;
            $caption = substr($str, 0, $tagStart-1);
            $choices = explode(",", substr($str, $tagStart + 5, $choiceEnd - $tagStart-5));
            $ansstr = substr($str, $choiceEnd + 3, $tagEnd - $choiceEnd-3);
            $nChoices = count($choices);
            $this->setSession('nchoices', $nChoices);
            $this->setSession('ok', $ansstr);
            $html .= '<strong>' . $caption . '</strong>';//<br /><form action=' . $this->uri(array('action' => 'evaluatemcq')) . " method='post'>";
            $i = 0;
            $formStr = '';
            foreach($choices as $choice) {
                $i++;
                //$sel = "<input type='checkbox' name='mcq$i' value='1'>" . $choice . "<br />";
                
                $objCheck = new checkbox("mcq{$i}");
                $objCheck->setValue('1');
                $sel = $objCheck->show().$choice.'<br />';
                $formStr .= $sel;
            }
            //$html .= "<br /><input type='submit' value='Ok'></form><br /><br />";
            $objButton = new button('ok', $lbOk);
            $objButton->setIconClass("next");
            $objButton->setToSubmit();
            $formStr .= '<p>'.$objButton->show().'</p>';
            
            $objForm = new form('evaluate', $this->uri(array('action' => 'evaluatemcq')));
            $objForm->addToForm($formStr);
            $html .= '<br />'.$objForm->show().'<br />';
            
            $str = $html;
        }
    }

    /**
    * Method to get the scene information in the case for display to the user.
    * Images contained within the scene are displayed.
    *
    * @return string $str The display scene
    */
    public function writeBoard()
    {
        // Get active scene from table classroom
        $id = $this->dbclassroom->getActiveSceneId();
        if(empty($id)){
            return '';
        }
        
        // if empty: get entry point into case
        if (empty($id)){
            $id = $this->dbclassroom->getEntryPoint();
        }
        // get scene from database
        $str = $this->dbclassroom->getSceneUI('id', $id);

        $this->renderImgs($str);
        return $str;
    }

    /**
    * Method to get the task information for display to the user.
    * Method renders a multiple choice question or choice string for display.
    *
    * @return string $pblsect The task display
    */
    public function writeTask()
    {
        // Get active scene from table classroom
        $active = $this->dbclassroom->getActiveSceneId();

        // if empty: get entry point into case
        if (empty($active)){
            $active = $this->dbclassroom->getEntryPoint();
        }

        $id = $this->dbclassroom->getNextTaskId($active);
        $str = $this->dbclassroom->getSceneUI('id', $id);

        $pblsect = $str;
        $this->renderMcq($pblsect);
        $this->renderChoice($pblsect);
        return $pblsect;
    }

    /**
    * Method to write a form to save & retrieve a users notes / learning issues / hypothesis from the database.
    *
    * @param string $option Notes or LIs or Hypothesis
    * @return string $objForm The form for display
    */
    public function writeNotes($option)
    {
        $sesScribe = $this->getSession('scribe');
        $sesPblUserId = $this->getSession('pbl_user_id');

        $true = TRUE;
        $readonly = '';
        if (strtolower($option) == 'notes') {
            $contents = $this->dbloggedin->retrieveNotes();
            $action = "editnotes";
        } else {
            $contents = $this->dbcontent->retrieveNotes(strtolower($option));
            $action = "editcontent";
            if ($sesScribe != $sesPblUserId) {
                $true = FALSE;
                $readonly = 'readonly="readonly"';
            }
        }
        $content = "";
        if ($contents) {
            foreach($contents as $row)
            $content = $row[strtolower($option)];
        }
        $objForm = new form($option, $this->uri(array('action' => $action, 'option' => $option)));
        $objForm->setDisplayType('3');
        $objForm->extra = "style=\"margin-bottom:20px;\" ";
        
        $objText = new textarea('content', $content, '10');
        $objText->extra = " wrap='soft' $readonly style=\"width: 97%;\" ";
        $objForm->addToForm($objText->show());
        
        $objInput = new textinput('option', $option, 'hidden');        
        $objForm->addToForm($objInput->show());
        
        if ($true) {
            $objButton = new button('save', $this->objLanguage->languageText('word_save'));
            $objButton->setIconClass("save");
            $objButton->setToSubmit();
            $save = '<br />'.$objButton->show().'&nbsp;&nbsp;';
            $objButton = new button('erase', $this->objLanguage->languageText('word_erase'));
            $objButton->setIconClass("erase");
            $objButton->setToSubmit();
            $save .= $objButton->show().'<br />';
            $objForm->addToForm($save);
        }
        return $objForm->show();
    }
}

?>