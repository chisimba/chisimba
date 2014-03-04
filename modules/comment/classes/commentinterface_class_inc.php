<?php
/* ----------- interface class for comment------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
*
* Class to generate interface elements for comments
*
* @author Derek Keats
* @package comment
*
*/
class commentinterface extends object
{

    /**
    * @var string $tableName The name of the table to add comments to
    */
    public $tableName;

    /**
    * @var string $sourceId The id (pk) field in the table to identify the row
    */
    public $sourceId;

    /**
    * @var string $moduleCode The module that owns the table
    */
    public $moduleCode;

    /**
    * @var object $objLanguage String to hold the instance of the language object
    */
    public $objLanguage;

    /**
    * @var object $objUser String to hold instance of the user object
    */
    public $objUser;

    /**
    * @var object $objDb String to hold instance of the dbcomment database object
    */
    public $objDb;
    /**
    * @var boolean $suppressView True|False whether to suppress the view icon
    */
    public $suppressView;

    //Added 2006/09/11 Serge Meunier - For approval of comments
    public $useApproval;


    public $objDT;

    /**
    * Constructor method to define the table
    */
   public function init() {
        $this->objUser =  $this->getObject('user', 'security');
        $this->objLanguage =  $this->getObject('language', 'language');
        $this->objDb =  $this->getObject('dbcomment');
        $this->objDateFunctions = $this->getObject('dateandtime','utilities');
        $this->useApproval = FALSE;

        // Get an instance of the decisiontable object.
        $this->objDT = $this->getObject( 'decisiontable','decisiontable' );
        // Create the decision table for the current module
        $this->objDT->create('comment');
        // Collect information from the database.
        $this->objDT->retrieve('comment');

    }

    /**
    *
    * Method to set parameters
    *
    * @param string $param The parameter to set
    * @param string $value The value of the parameter to be set
    * @return bool TRUE
    *
    */
    public function set($param, $value)
    {
        $this->$param = $value;
        return TRUE;
    }

    /**
    *
    * Method to get a parameter value
    *
    * @param string $param The parameter for which to get a value
    * @return The value of the the parameter
    *
    */
    public function get($param)
    {
       return $this->$param;
    }

    /**
    *
    * Method to set whether or not to use approval of comment
    * @param bool approval: Boolean flag to determine if to use approval of comments or not
    *
    */
    public function useApproval($approval)
    {
        $this->useApproval = $approval;
    }
    /**
    *
    * Method to render an input form for adding a comment
    * @return string: String containing the generated code
    *
    */
    public function renderInput()
    {
        $tableName = $this->getParam('tableName', NULL);
        $sourceId = $this->getParam('sourceId', NULL);
        $moduleCode = $this->getParam('moduleCode', NULL);
        $type = $this->getParam('type', NULL);

        $formAction = $this->uri(array(
          'action'=>'save',
          'mode'=>'add',
          'tableName'=>$tableName,
          'moduleCode' => $moduleCode,
          'sourceId'=>$sourceId));
        //Load the form class
        $this->loadClass('form','htmlelements');
        //Load the textarea class
        $this->loadClass('textarea','htmlelements');

        //Added 2006/07/20 Serge Meunier for hidden field for type
        //Load the textinput class
        $this->loadClass('textinput','htmlelements');

        //Create and instance of the form class
        $objForm = new form('commentform');
        //Set the action for the form to the uri with paramArray
        $objForm->setAction($formAction);
        //Set the displayType to 3 for freeform
        $objForm->displayType=3;

        //Title should it be used???
        $wTitle="<h1>" . $this->objLanguage->languageText("mod_comment_title".'comment') . "</h1>";

        //Create an element for the input of comment
        $objElement = new textarea ("comment");
        //Set the value of the element to $comment
        if (isset($comment)) {
            $objElement->setContent($comment);
        }
        $objElement->cols=57;
        $objElement->rows=12;

        //Add the $comment element to the form
        $objForm->addToForm("&nbsp;&nbsp;"
          . $objElement->show() . "<br /><br />\n");

        //Added 2006/09/11 Serge Meunier - To add approval status to comments
        $objApproved = new textinput("approved", '0',  "hidden", NULL);
        $objForm->addToform($objApproved->show());


        //------------
        //Modified 2006/07/18 Serge Meunier - Only displays dropdown if no type is specified
        if (!is_null($type)){
            //Create a hidden text field for the comment type
            $objCat = new textinput("type", $type,  "hidden", NULL);
            $objForm->addToForm($objCat->show());
        } else {
            //Get the types
            $objCat = & $this->getObject('dbcommenttype', 'commenttypeadmin');
            $tar = $objCat->getAll();

            //Create a dropdown for the comment type selector
            $this->loadClass('dropdown', 'htmlelements');
            $objCat = new dropdown();
            //$objCat = $this->newObject("dropdown", "htmlelements");
            $objCat->name = 'type';

            //Added 2006/07/18 by Serge Meunier for client side validation
            $objCat->SetId('input_type');

            $objCat->addOption("", $this->objLanguage->languageText("mod_comment_selecttype",'comment'));
            $objCat->addFromDB($tar, 'title', 'type');
            $objForm->addToForm("&nbsp;&nbsp;" . $objCat->show() . "\n");

            //Added 2006/07/18 by Serge Meunier for client side validation
            $objForm->addRule('type', $this->objLanguage->languageText("mod_comment_typeval",'comment'), 'required');
        }
        //-------------

        $objForm->addRule('comment', $this->objLanguage->languageText("mod_comment_commentval",'comment'), 'required');

        // Create an instance of the button object
        $this->loadClass('button', 'htmlelements');
        // Create a submit button
        $objElement = new button('submit');
        // Set the button type to submit
        $objElement->setToSubmit();
        // Use the language object to add the word save
        $objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
        // Add the button to the form
        $objForm->addToForm('&nbsp;&nbsp;&nbsp;'.$objElement->show());
        return $this->putInFieldset($objForm->show(),
          $this->objLanguage->languageText("mod_comment_addlabel",'comment'));
    }

    /**
    *
    * Method to render an input form for editing a comment
    * @author Serge Meunier
    * @return string: String containing the generated code
    * Added 2006/07/21
    *
    */
    public function renderInputEdit()
    {
        $tableName = $this->getParam('tableName', NULL);
        $sourceId = $this->getParam('sourceid', NULL);
        $moduleCode = $this->getParam('moduleCode', NULL);
        $id = $this->getParam('id', NULL);
        $userId = $this->getParam('userId', NULL);
        if (is_null($userId)){
            $this->objUser->userId();
        }
        if (is_null($id)){
            return $this->renderInput();
        }else{
            $formAction = $this->uri(array(
              'action'=>'editsave',
              'mode'=>'edit',
              'tableName'=>$tableName,
              'moduleCode' => $moduleCode,
              'sourceId'=>$sourceId,
              'id'=>$id));
            $commentar = $this->objDb->getRow('id', $id);

            //Load the form class
            $this->loadClass('form','htmlelements');
            //Load the textarea class
            $this->loadClass('textarea','htmlelements');

            //Load the textinput class
            $this->loadClass('textinput','htmlelements');

            //Create and instance of the form class
            $objForm = new form('commentform');
            //Set the action for the form to the uri with paramArray
            $objForm->setAction($formAction);
            //Set the displayType to 3 for freeform
            $objForm->displayType=3;

            //Added 2006/09/11 Serge Meunier - To add approval status to comments
            $objApproved = new textinput("approved", $commentar['approved'],  "hidden", NULL);
            $objForm->addToform($objApproved->show());

            //Title should it be used???
            $wTitle="<h1>" . $this->objLanguage->languageText('mod_comment_title','comment') . "</h1>";

            //Create an element for the input of comment
            $objElement = new textarea ("comment");
            //Set the value of the element to $comment
            if (isset($comment)) {
                $objElement->setContent($comment);
            }
            $objElement->cols=57;
            $objElement->rows=12;
           // $objElement->setContent($commentar['comment']);

            //Add the $comment element to the form
            $objForm->addToForm("&nbsp;&nbsp;"
              . $objElement->show() . "<br /><br />\n");

            //Get the types
            $objCat = & $this->getObject('dbcommenttype', 'commenttypeadmin');
            $tar = $objCat->getAll();

            //Create a dropdown for the comment type selector
            $objCat = $this->newObject("dropdown", "htmlelements");
            $objCat->name = 'type';

            $objCat->SetId('input_type');

            $objCat->addOption("", $this->objLanguage->languageText("mod_comment_selecttype",'comment'));
            $objCat->addFromDB($tar, 'title', 'type', $commentar['type']);

            $objForm->addToForm("&nbsp;&nbsp;" . $objCat->show() . "\n");

            $objForm->addRule('type', $this->objLanguage->languageText('mod_comment_typeval','comment'), 'required');

            $objForm->addRule('comment', $this->objLanguage->languageText('mod_comment_commentval','comment'), 'required');

            // Create an instance of the button object
            $this->loadClass('button', 'htmlelements');
            // Create a submit button
            $objElement = new button('submit');
            // Set the button type to submit
            $objElement->setToSubmit();
            // Use the language object to add the word save
            $objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
            // Add the button to the form
            $objForm->addToForm('&nbsp;&nbsp;&nbsp;'.$objElement->show());
            return $this->putInFieldset($objForm->show(),
              $this->objLanguage->languageText('mod_comment_editlabel','comment'));
        }
    }

    /**
    * Method to render the output of all unapproved / approved comments for moderation
    * All comments are connected to a specific table and not a specific record
    *
    * @author Megan Watson added 02/10/2006
    * @param string $userId: The user id of the currently logged in user
    * @param string $moduleCode: The code of the module calling the function
    * @return string html
    */
    public function showForModerator($userId = null, $moduleCode = null)
    {
        $tableName = $this->get('tableName');

        $ar = $this->objDb->getCommentsByTableName($tableName);

        return $this->displayAllComments($tableName, $ar, $userId, $moduleCode);
    }

    /**
    *
    * Method to render an output of all comments for a table/record
    * @author Serge Meunier modified 2006/07/20
    * @param string $userId: The user id of the currently logged in user
    * @param string $moduleCode: The code of the module calling the function
    * @return array | NULL The array containing the comments, or else NULL on failure
    *
    */
    public function showAll($userId = null, $moduleCode = null)
    {
        $tableName = $this->get('tableName');
        $sourceId = $this->get('sourceId');

        $ar = $this->objDb->getComment($tableName, $sourceId);

        return $this->displayAllComments($tableName, $ar, $userId, $moduleCode);
    }

    /**
    * Method to render the output of the comment data
    * @author Megan Watson modified 02/10/2006
    * @param string $tableName The table attached to the record
    * @param string $sourceId The record being commented on
    * @param array $ar The comment data
    * @param string $userId: The user id of the currently logged in user
    * @param string $moduleCode: The code of the module calling the function
    * @return array | NULL The array containing the comments, or else NULL on failure
    */
    public function displayAllComments($tableName, $ar, $userId = null, $moduleCode = null)
    {
        if (count($ar) > 0) {
            //Loop Through the comments & display them
            $rowcount=0;
            $ret = "&nbsp;&nbsp;<b>" . $this->objLanguage->languageText("word_comments") . "</b>";
            foreach ($ar as $line) {
                if (($this->useApproval == FALSE) || (($this->useApproval == TRUE) && ($line['approved'] == 1)) || ($this->objDT->isValid('approve'))){
                    $oddOrEven=($rowcount==0) ? "odd" : "even";
                    $outstr="";
                    //Create the content layer
                    $this->blt = &$this->newObject('layer', 'htmlelements');
                    //Set the background colour to the odd or even colour
                    $this->blt->id = "blog-comment-".$oddOrEven;
                    $outstr .= $line['commenttext'];
                    $type = $line['type'];
                    //Add the author and date
                    $outstr .= "<p class=\"minute\">"
                      .$this->objLanguage->languageText("phrase_postedby")
                      . " <b>".$this->objUser->fullName($line['creatorid'])."</b> "
                      .$this->objLanguage->languageText("word_on")
                      . " <b>" . $line['datecreated'] . "</b>";

                    //---------------------
                    //Added 2006/07/20 Serge Meunier - Added icons to distinguish comment types on form
                    $objCat = & $this->getObject('dbcommenttype', 'commenttypeadmin');
                    $where = "WHERE type = '" . $type . "'";
                    $tar = $objCat->getAll($where);

                    $iconname = "comment" . $type;
                    $iconext = "gif";
                    $objGetIcon = $this->newObject('geticon', 'htmlelements');
                    $objGetIcon->setIcon($iconname, $iconext);
                    if (count($tar) > 0){
                        $objGetIcon->alt = $tar[0]['title'];
                    }else{
                        $objGetIcon->alt = $type;
                    }
                    $outstr .= "&nbsp;" . $objGetIcon->show();

                    //--------------------
                    //Added 2006/09/11 serge Meunier To allow approval of comments
                    if (($this->objDT->isValid('approve')) && ($this->useApproval == TRUE)){
                        if ($line['approved'] == 0){
                            $approveLink=$this->uri(array(
                              'action'=>'approve',
                              'id'=>$line['id'],
                              'approved'=>'1'), 'comment');

                            $outstr .= "&nbsp;" . $this->addCommentApproveLink($approveLink, FALSE);
                        }else{
                            $approveLink=$this->uri(array(
                              'action'=>'approve',
                              'id'=>$line['id'],
                              'approved'=>'0'), 'comment');

                            $outstr .= "&nbsp;" . $this->addCommentApproveLink($approveLink, TRUE);
                        }
                    }
                    //-------------------

                    //If allowed, show edit and delete icons
                    $difference = $this->objDateFunctions->dateDifference($line['datecreated'], date("Y-m-d H:i:s"));
                    $timediff = ($difference['d'] * 1440) + ($difference['h'] * 60) + $difference['m'];

                    if ($this->objUser->isAdmin()  || (($this->objUser->userId() == $line['creatorid']) && ($timediff < 30))){
                        $objEditIcon = $this->newObject('geticon', 'htmlelements');
                        $objDeleteIcon = $this->newObject('geticon', 'htmlelements');

                        //The URL for the edit link

                        $editLink=$this->uri(array(
                           'action'=>'edit',
                          'mode'=>'edit',
                          'tableName'=>$tableName,
                          'moduleCode' => $moduleCode,
                          'sourceid'=>$line['sourceid'],
                          'id'=>$line['id'],
                          'userId'=>$userId), 'comment');

                        $outstr .= "&nbsp;" . $this->addCommentEditLink($editLink);
                        $deleteArray = $this->uri(array('action'=>'delete',
                          'id' => $line['id'],
                          'tableName'=>$tableName,
                          'moduleCode' => $moduleCode,
                          'sourceid'=>$line['sourceid']),'comment');
                        $outstr .= "&nbsp;" . $this->addCommentDeleteLink($deleteArray);
                        //$objDeleteIcon->getDeleteIconWithConfirm('', $deleteArray, 'comment');
                    }

                    $outstr .= "</p>";
                    //-------------------


                    //Add the output to the layer
                    $this->blt->addToStr($outstr);
                    $ret .= "<div style=\"padding-left: 20px; padding-right: 20px;
                      padding-bottom: 2px;\">" . $this->blt->show() . "</div>";
                    $rowcount=($rowcount==0) ? 1 : 0;
                }
            }
            return $ret . "<br />";
        } else {
            return NULL;
        }
    }

    /**
    *
    * Method to render an output of most recent comments for a table/record
    *
    * @param string $userId: The user id of the currently logged in user
    * @param string $moduleCode: The code of the module calling the function
    * @param int $count: The number of records to return
    * @param int $offset: The position from which to return records (0 returns 1st record onwards, etc)
    * @return array | NULL The array containing the comments, or else NULL on failure
    * @author Serge Meunier
    * Added 2006/07/18
    *
    */
    public function showMostRecentComment($userId = NULL, $moduleCode = null, $count = 10000000, $offset = 0)
    {
        $tableName = $this->get('tableName');
        $sourceId = $this->get('sourceId');

        $ar = $this->objDb->getMostRecentComment($tableName, $sourceId, $count, $offset);
        if (count($ar) > 0) {
            //Loop Through the comments & display them
            $rowcount=0;
            $ret = "&nbsp;&nbsp;<b>" . $this->objLanguage->languageText("word_comments") . "</b>";
            foreach ($ar as $line) {
                if (($this->useApproval == FALSE) || (($this->useApproval == TRUE) && ($line['approved'] == 1)) || ($this->objDT->isValid('approve'))){
                    $oddOrEven=($rowcount==0) ? "odd" : "even";
                    $outstr="";
                    //Create the content layer
                    $this->blt = &$this->newObject('layer', 'htmlelements');
                    //Set the background colour to the odd or even colour
                    $this->blt->id = "blog-comment-".$oddOrEven;
                    $outstr .= $line['commenttext'];
                    $type = $line['type'];

                    //Add the author and date
                    $outstr .= "<p class=\"minute\">"
                      .$this->objLanguage->languageText("phrase_postedby")
                      . " <b>".$this->objUser->fullName($line['creatorid'])."</b> "
                      .$this->objLanguage->languageText("word_on")
                      . " <b>" . $line['dateCreated'] . "</b>";

                    //---------------------
                    //Added 2006/07/20 Serge Meunier - Added icons to distinguish comment types on form
                    $objCat = & $this->getObject('dbcommenttype', 'commenttypeadmin');
                    $where = "WHERE type = '" . $type . "'";
                    $tar = $objCat->getAll($where);

                    $iconname = "comment" . $type;
                    $iconext = "gif";
                    $objGetIcon = $this->newObject('geticon', 'htmlelements');
                    $objGetIcon->setIcon($iconname, $iconext);
                    if (count($tar) > 0){
                        $objGetIcon->alt = $tar[0]['title'];
                    }else{
                        $objGetIcon->alt = $type;
                    }
                    $outstr .= "&nbsp;" . $objGetIcon->show();

                    //--------------------
                    //Added 2006/09/11 serge Meunier To allow approval of comments
                    if (($this->objDT->isValid('approve')) && ($this->useApproval == TRUE)){
                        if ($line['approved'] == 0){
                            $approveLink=$this->uri(array(
                              'action'=>'approve',
                              'id'=>$line['id'],
                              'approved'=>'1'), 'comment');

                            $outstr .= "&nbsp;" . $this->addCommentApproveLink($approveLink, FALSE);
                        }else{
                            $approveLink=$this->uri(array(
                              'action'=>'approve',
                              'id'=>$line['id'],
                              'approved'=>'0'), 'comment');

                            $outstr .= "&nbsp;" . $this->addCommentApproveLink($approveLink, TRUE);
                        }
                    }
                    //-------------------

                    //If allowed, show edit and delete icons
                    $difference = $this->objDateFunctions->dateDifference($line['dateCreated'], date("Y-m-d H:i:s"));
                    $timediff = ($difference['d'] * 1440) + ($difference['h'] * 60) + $difference['m'];
                    if ($this->objUser->isAdmin()  || (($this->objUser->userId() == $line['creatorid']) && ($timediff < 30))){
                        $objEditIcon = $this->newObject('geticon', 'htmlelements');
                        $objDeleteIcon = $this->newObject('geticon', 'htmlelements');


                        //The URL for the edit link

                        $editLink=$this->uri(array(
                          'action'=>'edit',
                          'mode'=>'edit',
                          'tableName'=>$tableName,
                          'moduleCode' => $moduleCode,
                          'sourceId'=>$sourceId,
                          'id'=>$line['id'],
                          'userId'=>$userId), 'comment');

                        $outstr .= "&nbsp;" . $this->addCommentEditLink($editLink);
                        $deleteArray = array('action'=>'delete',
                          'id' => $line['id'],
                          'tableName'=>$tableName,
                          'moduleCode' => $moduleCode,
                          'sourceId'=>$sourceId);
                        $outstr .= "&nbsp;" . $objDeleteIcon->getDeleteIconWithConfirm('', $deleteArray, 'comment');
                    }

                    $outstr .= "</p>";
                    //-------------------


                    //Add the output to the layer
                    $this->blt->addToStr($outstr);
                    $ret .= "<div style=\"padding-left: 20px; padding-right: 20px;
                      padding-bottom: 2px;\">" . $this->blt->show() . "</div>";
                    $rowcount=($rowcount==0) ? 1 : 0;
                }
            }
            return $ret . "<br />";
        } else {
            return NULL;
        }
    }

    /**
    *
    * Method to render an output of comments for a table/record/type
    *
    * @param string $userId: The user id of the currently logged in user
    * @param string $moduleCode: The code of the module calling the function
    * @param string $type The comment type of the records to return
    * @param int $count: The number of records to return
    * @param int $offset: The position from which to return records (0 returns 1st record onwards, etc)
    * @return array | NULL The array of comments, or else NULL
    * @author Serge Meunier
    * Added 2006/07/18
    *
    */
     public function showCommentByType($userId = null, $moduleCode = null, $type, $count = 10000000, $offset = 0)
    {
        $tableName = $this->get('tableName');
        $sourceId = $this->get('sourceId');

        $ar = $this->objDb->getCommentByType($tableName, $sourceId, $type, $count, $offset);
        if (count($ar) > 0) {
            //Loop Through the comments & display them
            $rowcount=0;
            $ret = "&nbsp;&nbsp;<b>" . $this->objLanguage->languageText("word_comments") . "</b>";
            foreach ($ar as $line) {
                if (($this->useApproval == FALSE) || (($this->useApproval == TRUE) && ($line['approved'] == 1)) || ($this->objDT->isValid('approve'))){
                    $oddOrEven=($rowcount==0) ? "odd" : "even";
                    $outstr="";
                    //Create the content layer
                    $this->blt = &$this->newObject('layer', 'htmlelements');
                    //Set the background colour to the odd or even colour
                    $this->blt->id = "blog-comment-".$oddOrEven;
                    $outstr .= $line['commenttext'];
                    $type = $line['type'];
                    //Add the author and date
                    $outstr .= "<p class=\"minute\">"
                      .$this->objLanguage->languageText("phrase_postedby")
                      . " <b>".$this->objUser->fullName($line['creatorid'])."</b> "
                      .$this->objLanguage->languageText("word_on")
                      . " <b>" . $line['dateCreated'] . "</b>";

                    //---------------------
                    //Added 2006/07/20 Serge Meunier - Added icons to distinguish comment types on form
                    $objCat = & $this->getObject('dbcommenttype', 'commenttypeadmin');
                    $where = "WHERE type = '" . $type . "'";
                    $tar = $objCat->getAll($where);

                    $iconname = "comment" . $type;
                    $iconext = "gif";
                    $objGetIcon = $this->newObject('geticon', 'htmlelements');
                    $objGetIcon->setIcon($iconname, $iconext);
                    if (count($tar) > 0){
                        $objGetIcon->alt = $tar[0]['title'];
                    }else{
                        $objGetIcon->alt = $type;
                    }
                    $outstr .= "&nbsp;" . $objGetIcon->show();

                    //--------------------
                    //Added 2006/09/11 serge Meunier To allow approval of comments
                    if (($this->objDT->isValid('approve')) && ($this->useApproval == TRUE)){
                        if ($line['approved'] == 0){
                            $approveLink=$this->uri(array(
                              'action'=>'approve',
                              'id'=>$line['id'],
                              'approved'=>'1'), 'comment');

                            $outstr .= "&nbsp;" . $this->addCommentApproveLink($approveLink, FALSE);
                        }else{
                            $approveLink=$this->uri(array(
                              'action'=>'approve',
                              'id'=>$line['id'],
                              'approved'=>'0'), 'comment');

                            $outstr .= "&nbsp;" . $this->addCommentApproveLink($approveLink, TRUE);
                        }
                    }
                    //-------------------
                    //If allowed, show edit and delete icons
                    $difference = $this->objDateFunctions->dateDifference($line['dateCreated'], date("Y-m-d H:i:s"));
                    $timediff = ($difference['d'] * 1440) + ($difference['h'] * 60) + $difference['m'];
                    if ($this->objUser->isAdmin()  || (($this->objUser->userId() == $line['creatorid']) && ($timediff < 30))){
                        $objEditIcon = $this->newObject('geticon', 'htmlelements');
                        $objDeleteIcon = $this->newObject('geticon', 'htmlelements');


                        //The URL for the edit link

                        $editLink=$this->uri(array(
                          'action'=>'edit',
                          'mode'=>'edit',
                          'tableName'=>$tableName,
                          'moduleCode' => $moduleCode,
                          'sourceId'=>$sourceId,
                          'id'=>$line['id'],
                          'userId'=>$userId), 'comment');

                        $outstr .= "&nbsp;" . $this->addCommentEditLink($editLink);
                        $deleteArray = array('action'=>'delete',
                          'id' => $line['id'],
                          'tableName'=>$tableName,
                          'moduleCode' => $moduleCode,
                          'sourceId'=>$sourceId);
                        $outstr .= "&nbsp;" . $this->addCommentDeleteLink($deleteArray);
                        //$objDeleteIcon->getDeleteIconWithConfirm('', $deleteArray, 'comment');
                    }

                    $outstr .= "</p>";
                    //-------------------



                    //Add the output to the layer
                    $this->blt->addToStr($outstr);
                    $ret .= "<div style=\"padding-left: 20px; padding-right: 20px;
                       padding-bottom: 2px;\">" . $this->blt->show() . "</div>";
                    $rowcount=($rowcount==0) ? 1 : 0;
                }
            }
            return $ret . "<br />";
        } else {
            return NULL;
        }
    }

    /**
    *
    * Method to add a comment link with an icon
    * @param string $type: The specified comment type, NULL to allow all comment types
    * @return string: Generated code for the comment link
    *
    */
    public function addCommentLink($type = NULL)
    {
        $tableName = $this->get('tableName');
        $sourceId = $this->get('sourceId');
        $moduleCode = $this->get('moduleCode');
        //Only put the link if they are logged in
        if ($this->objUser->isLoggedIn()) {
            // Create an instance of icon object
            $objGetIcon = $this->newObject('geticon', 'htmlelements');
            // The add comment icon with link
            $objGetIcon->setIcon("comment");
            $objGetIcon->alt=$this->objLanguage->languageText('mod_comment_add','comment');
            $objGetIcon->align = "middle";
            //Get the popup window HTML element
            $this->objPop = &$this->getObject('windowpop', 'htmlelements');
            //Set the location

            //----------
            //modified 2006/07/20 Serge Meunier to add a specified type
            if (is_null($type)){
            $location = $this->uri(array(
              'action' => 'add',
              'tableName' => $tableName,
              'sourceId' => $sourceId,
              'moduleCode'=>$moduleCode), 'comment');
            } else {
            $location = $this->uri(array(
              'action' => 'add',
              'tableName' => $tableName,
              'sourceId' => $sourceId,
              'moduleCode'=>$moduleCode,
              'type'=>$type), 'comment');
            }
            //----------

            $this->objPop->set('location', $location);
            $this->objPop->set('linktext', $objGetIcon->show());
            $this->objPop->set('width','400');
            $this->objPop->set('height','300');
            $this->objPop->set('left','200');
            $this->objPop->set('top','200');
            $this->objPop->putJs();
            return "&nbsp;&nbsp;" . $this->objPop->show();
        } else {
            return FALSE;
        }
    }

     /**
    *
    * Method to put a string inside a fieldset
    *
    * @param string $str The string for the content
    * @param string $label THe legend for the fieldset
    * @return string: Code for the fieldset;
    *
    */
    public function putInFieldset(& $str, $label)
    {
        //Create an instance of the fieldset object
        $objFieldset = & $this->getObject('fieldset', 'htmlelements');
        $objFieldset->legend=$label;
        $objFieldset->legendalign='LEFT';
        $objFieldset->width="77%";
        $objFieldset->contents = $str;
        return $objFieldset->show();
    }

    /**
    *
    * Method to add a view link as a standard HTML link
    * without a popup.
    *
    * @param string $link The link that will open when clicked
    * @param string $linkStr The text to be included in the link (can also be an image)
    * @param bool $suppressLink Whether or not tot suppress the Link
    * @return string: The code for a view link
    *
    */
    public function addViewLink($link, $linkStr, $suppressLink=FALSE)
    {
        // Create an instance of icon object
        $objGetIcon = $this->newObject('geticon', 'htmlelements');
        // The add comment icon with link
        $objGetIcon->setIcon("comment_view");
        $objGetIcon->alt=$this->objLanguage->languageText('mod_comment_view','comment');
        $objGetIcon->align = "middle";
        $linkStr = $linkStr ;
        if ($suppressLink == FALSE) {
            $this->viewLink = $this->newObject('link', 'htmlelements');
            $this->viewLink->href = $link;
            $this->viewLink->link = $objGetIcon->show();
            $linkStr .= $this->viewLink->show();
        }
        return $linkStr;
    }


    /**
    *
    * Method to add a popup window link as a standard HTML link
    *
    * @param string $link The link that will open when clicked
    * @param string $linkStr The text to be included in the link (can also be an image)
    * @param bool $suppressLink Whether or not tot suppress the Link
    * @return string: The code for a popup link
    *
    */
    public function addPopupLink($link, $linkStr, $suppressLink=FALSE)
    {
        //Instantiate the window popup object
        $objPop=& $this->getObject('windowpop','htmlelements');

        // Create an instance of icon object
        $objGetIcon = $this->newObject('geticon', 'htmlelements');
        // The add comment icon with link
        $objGetIcon->setIcon("comment_view");
        $objGetIcon->alt=$this->objLanguage->languageText('mod_comment_view','comment');
        $objGetIcon->align = "middle";
        $linkStr = $linkStr ;
        if ($suppressLink == FALSE) {
            //Setup the popup window
            $objPop->set('window_name','comment');
            $objPop->set('location',$link);
            $objPop->set('linktext', $objGetIcon->show());
            $objPop->set('width','600');
            $objPop->set('height','700');
            $objPop->set('left','300');
            $objPop->set('top','300');
            $objPop->set('scrollbars', 'yes');
            $linkStr .=  $objPop->show();
        }
        return $linkStr;
    }

    /**
    *
    * Method to add a popup window link as a standard HTML link to edit a comment
    *
    * @param string $link The link that will open when clicked
    * @return string: The code for a comment link for editing
    * @author Serge Meunier
    * Added 2006/07/21
    *
    */
    public function addCommentEditLink($link)
    {
        //Only put the link if they are logged in
        if ($this->objUser->isLoggedIn()) {
            // Create an instance of icon object
            $objGetIcon = $this->newObject('geticon', 'htmlelements');
            // The add comment icon with link
            $objGetIcon->setIcon("edit");
            $objGetIcon->alt=$this->objLanguage->languageText("word_edit");
            $objGetIcon->align = "middle";
            //Get the popup window HTML element
            $this->objPop = &$this->getObject('windowpop', 'htmlelements');
            //Set the location

            $this->objPop->set('location', $link);
            $this->objPop->set('linktext', $objGetIcon->show());
            $this->objPop->set('width','400');
            $this->objPop->set('height','300');
            $this->objPop->set('left','200');
            $this->objPop->set('top','200');
            $this->objPop->putJs();
            return "&nbsp;&nbsp;" . $this->objPop->show();
        } else {
            return FALSE;
        }
    }

/**
    *
    * Method to add a popup window link as a standard HTML link to edit a comment
    *
    * @param string $link The link that will open when clicked
    * @return string: The code for a comment link for editing
    * @author Serge Meunier
    * Added 2006/07/21
    *
    */
    public function addCommentDeleteLink($link)
    {
        //Only put the link if they are logged in
        if ($this->objUser->isLoggedIn()) {
            // Create an instance of icon object
            $objGetIcon = $this->newObject('geticon', 'htmlelements');
            // The add comment icon with link
            $objGetIcon->setIcon("delete");
            $objGetIcon->alt=$this->objLanguage->languageText("word_delete");
            $objGetIcon->align = "middle";
            //Get the popup window HTML element
            $this->objPop = &$this->getObject('windowpop', 'htmlelements');
            //Set the location

            $this->objPop->set('location', $link);
            $this->objPop->set('linktext', $objGetIcon->show());
            $this->objPop->set('width','400');
            $this->objPop->set('height','300');
            $this->objPop->set('left','200');
            $this->objPop->set('top','200');
            $this->objPop->putJs();
            return "&nbsp;&nbsp;" . $this->objPop->show();
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Method to add a popup window link as a standard HTML link to approve a comment
    *
    * @param string $link The link that will open when clicked
    * @param bool $status The approval status
    * @return string: The code for a comment link for editing
    * @author Serge Meunier
    * Added 2006/07/21
    *
    */
    public function addCommentApproveLink($link, $status = FALSE)
    {
        //Only put the link if they are logged in
        if ($this->objUser->isLoggedIn()) {
            // Create an instance of icon object
            $objGetIcon = $this->newObject('geticon', 'htmlelements');
            if ($status == FALSE){
                $objGetIcon->setIcon("approve");
                $objGetIcon->alt=$this->objLanguage->languageText("word_approve");
            }else{
                $objGetIcon->setIcon("disapprove");
                $objGetIcon->alt=$this->objLanguage->languageText("word_disapprove");
            }
            $objGetIcon->align = "middle";
            //Get the popup window HTML element
            $this->objPop = &$this->getObject('windowpop', 'htmlelements');
            //Set the location

            $this->objPop->set('location', $link);
            $this->objPop->set('linktext', $objGetIcon->show());
            $this->objPop->set('width','400');
            $this->objPop->set('height','300');
            $this->objPop->set('left','200');
            $this->objPop->set('top','200');
            $this->objPop->putJs();
            return "&nbsp;&nbsp;" . $this->objPop->show();
        } else {
            return FALSE;
        }
    }
    /**
    *
    * Method to get number of approved comments for a table
    *
    * @param string $tableName The name of the table that the comment applies to
    * @param string $sourceId The id field in the source table
    * @return int : The number of approved comments
    *
    */
    public function getVisibleCommentCount($tableName, $sourceId, $sourceModule)
    {
        if ($this->objDT->isValid('approve')){
            $moderator = TRUE;
        }else{
            $moderator = FALSE;
        }
        return $this->objDb->getApprovedCount($tableName, $sourceId, $sourceModule, $moderator);
    }
} #end of class
?>
