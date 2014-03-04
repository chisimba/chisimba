<?php
/**
*
* Personal blocks render classs to help templates
*
* Allows the creation of personal blocks for display on sidebar block areas.
* Requires the blockalicious module to function. Personal blocks allow the
* addition of web widgets in locations such as a blog.
*
*/
/* ----------- data class extends dbTable for tbl_quotes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
*
* Render class for the personalblocks module. This is a view layer
* class that helps render output to templates
*
* @author Derek Keats
*
* @todo insert the contextblock code
*
*/
class pbrender extends dbTable
{
    /**
    *
    * @var string $objConfig String object property for holding the
    * configuration object
    * @access public
    *
    */
    public $objConfig;
    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    * Constructor method to define the table
    */
    public function init() {
        $this->objUser = $this->getObject("user", "security");
        $this->objLanguage = $this->getObject("language", "language");
        $this->loadClass("htmltable", "htmlelements");
        $this->objDb = $this->getObject("dbpersonalblocks", "personalblocks");
 	$this->objWashout = $this->getObject("washout", "utilities");
    }

    /**
    *
    * Show the title from the language item for module title. It shows the title inside
    * a H3 heading using the htmlheading class.
    *
    * @return string The module title in a H3 heading
    * @access public
    *
    */
    public function showTitle()
    {
        $this->loadClass("htmlheading", "htmlelements");
        $h = new htmlheading();
        $h->str = $this->objLanguage->languageText("mod_personalblocks_title", "personalblocks");
    	return $h->show();
    }

    /**
    *
    * Method to try to figure out which user's blocks should be displayed
    * and display accordingly. For example, if you are viewing my blog, you
    * should see my blocks, not your own.
    *
    * @return integer The user id of the person whose blocks should be displayed
    * @access private
    *
    */
    private function findUser()
    {
        $objGuess = $this->getObject('bestguess', 'utilities');
        return $objGuess->guessUserId();
    }

    /**
    *
    * Method to try to figure out which context the user is in for the display
    * of context specific blocks.
    *
    * @return integer The user id of the person whose blocks should be displayed
    * @access private
    *
    */
    public function findContext()
    {
        $contextObject = $this->getObject('dbcontext', 'context');
        $contextCode = $contextObject->getContextCode();
        if (!$contextCode || $contextCode=="") {
            return FALSE;
        } else {
            return $contextCode;
        }
    }

    /**
    *
    * Render the personal blocks for the left hand panel
    *
    * @param boolean $showName If true the block name is shown.
    * @return string The left blocks rendered out
    * @access public
    *
    */
    public function renderLeft($showName=FALSE, $blockType='personal')
    {
        if ($blockType == 'personal') {
            $creatorId = $this->findUser();
            $ar = $this->objDb->getLeftBlocks($creatorId);
        } else {
            $context = $this->findContext();
            $ar = $this->objDb->getLeftBlocksContext($context);
        }

        $ret ="";
        $blockname="";
        if (isset($ar)) {
            if (count($ar) > 0) {
                foreach ($ar as $line) {
                    $blockcontent = $line['blockcontent'];
                    $blockcontent = str_replace('"', '&quot;',$blockcontent);
                    $blockcontent = $this->objWashout->parseText($blockcontent);
                    if ($blockType !== 'personal') {
                        $userId = $line['creatorid'];
                        $blockcontent .= $this->getUserPresence($userId);
                    }
                    if ($showName) {
                        $blockname = $line['blockname'];
                        $blockname = $this->addFeaturBoxHeader($blockname);
                        $blockcontent = $blockname 
                          . '<div class="featureboxcontent">' 
                          . $blockcontent . '</div>';
                    }
                    $ret .= "<div class=\"featurebox\">" 
                      . $blockcontent 
                      . $this->addFeaturBoxBottom()
                      . "</div>";
                }
            } else {
                $ret = $this->emptyBlocks("left");
            }
        } else {
            $ret = $this->emptyBlocks("left");
        }
        return $ret;
    }

    /**
    *
    * Render the personal blocks for the right hand panel
    *
    * @param boolean $showName If true the block name is shown.
    * @return string The right blocks rendered out
    * @access public
    *
    */
    public function renderRight($showName=FALSE, $blockType='personal')
    {
        if ($blockType == 'personal') {
            $creatorId = $this->findUser();
            $ar = $this->objDb->getRightBlocks($creatorId);
        } else {
            $context = $this->findContext();
            $ar = $this->objDb->getRightBlocksContext($context);
        }
        $ret ="";
        $blockname="";
        if (isset($ar)) {
            if (count($ar) > 0) {
                foreach ($ar as $line) {
                    $blockcontent = $line['blockcontent'];
                    $blockcontent = str_replace('"', '&quot;',$blockcontent);
                    $blockcontent = $this->objWashout->parseText($blockcontent);
                    if ($blockType !== 'personal') {
                        $userId = $line['creatorid'];
                        $blockcontent .= $this->getUserPresence($userId);
                    }
                    if ($showName) {
                        $blockname = $line['blockname'];
                        $blockname = $this->addFeaturBoxHeader($blockname);
                        $blockcontent = $blockname 
                          . '<div class="featureboxcontent">' 
                          . $blockcontent . '</div>';
                    }
                    $ret .= "<div class=\"featurebox\">" 
                      . $blockcontent 
                      . $this->addFeaturBoxBottom()
                      . "</div>";
                }
            } else {
                $ret = $this->emptyBlocks("right");
            }
        } else {
            $ret = $this->emptyBlocks("right");
        }
        return $ret;
    }
    
    /**
     * Get userimage and name
     * 
     * @param string $userId The userid to look up
     * @return string Rendered small image and name
     * @access private 
     */
    private function getUserPresence($userId)
    {
        $fullName = $this->objUser->fullName($userId);
        $userImg =  $this->objUser->getSmallUserImage($userId);
        return "<div class='userpresence'>" . $userImg . " " . $fullName . "</div>";
    }
    
    /**
     *
     * Make a featurebox header for the title
     * 
     * @param string $blockname The content of the header
     * @return string The rendered featureblock header
     * @access private
     * 
     */
    private function addFeaturBoxHeader($blockname)
    {
        $ret = "<div class='featureboxtopcontainer'>\n"
          . "<div class='featureboxtopleft'></div>\n"
          . "<div class='featureboxtopborder'></div>\n"
          . "<div class='featureboxtopright'></div>\n"
          . "</div>"
          . "<h5 class='featureboxheader'>$blockname</h5>";
        return $ret;
    }
    
    /**
     *
     * Make a featurebox bottom 
     * 
     * @return string The rendered featureblock bottom
     * @access private
     * 
     */
    private function addFeaturBoxBottom()
    {
        $ret = "<div class='featureboxbottomcontainer'>\n"
          . "<div class='featureboxbottomleft'></div>\n"
          . "<div class='featureboxbottomborder'></div>\n"
          . "<div class='featureboxbottomright'></div>\n"
          . "</div>";
        return $ret;
    }

    /**
    *
    * Render the personal blocks for the middle panel
    *
    * @param boolean $showName If true the block name is shown.
    * @return string The middle blocks rendered out
    * @access public
    *
    */
    public function renderMiddle($showName=FALSE, $blockType='personal')
    {
        if ($blockType == 'personal') {
            $creatorId = $this->findUser();
            $ar = $this->objDb->getMiddleBlocks($creatorId);
        } else {
            $context = $this->findContext();
            $ar = $this->objDb->getMiddleBlocksContext($context);
        }
        $ret ="";
        $blockname="";
        if (isset($ar)) {
            if (count($ar) > 0) {
                foreach ($ar as $line) {
                    $blockcontent = $line['blockcontent'];
                    $blockcontent = str_replace('"', '&quot;',$blockcontent);
                    $blockcontent = $this->objWashout->parseText($blockcontent);
                    if ($blockType !== 'personal') {
                        $userId = $line['creatorid'];
                        $blockcontent .= $this->getUserPresence($userId);
                    }
                    if ($showName) {
                        $blockname = $line['blockname'];
                        $blockname = $this->addFeaturBoxHeader($blockname);
                        $blockcontent = $blockname 
                          . '<div class="featureboxcontent">' 
                          . $blockcontent . '</div>';
                    }
                    $ret .= "<div class=\"featurebox\">" 
                      . $blockcontent 
                      . $this->addFeaturBoxBottom()
                      . "</div>";
                }
            } else {
                $ret = $this->emptyBlocks("middle");
            }
        } else {
            $ret = $this->emptyBlocks("middle");
        }
        return $ret; 
    }

    /**
    *
    * Returns all personal block records formatted in a table for edit,
    * add, delete or view. Records are not paginated because the use is unlikely
    * to have very large numbers of blocks.
    *
    * @return string A table with all records.
    * @access public
    *
    */
    public function showAll()
    {
        $userId = $this->objUser->userId();
        $ar = $this->objDb->getBlocks($userId);
        $objTable = new htmltable("personalblocks");
        $objTable->cellspacing="0";
        $objTable->cellpadding="2";
        $objTable->border=0;
        $objTable->width="100%";
        // Add the table header
        $objTable->startHeaderRow();
        $objTable->addHeaderCell($this->getBlkInfIconGrey(), 18, "bottom", 'left', 'heading');
        $objTable->addHeaderCell($this->objLanguage->languageText(
          'mod_personalblocks_blname','personalblocks'), NULL, "bottom", 'left', 'heading');
        $objTable->addHeaderCell($this->objLanguage->languageText(
          'mod_personalblocks_location','personalblocks'), 80, "bottom", 'left', 'heading');
        $objTable->addHeaderCell($this->objLanguage->languageText(
          'mod_personalblocks_active','personalblocks'), 70, "bottom", 'left', 'heading');
        $objTable->addHeaderCell($this->objLanguage->languageText(
          'mod_personalblocks_blocktype','personalblocks'), 50, "bottom", 'left', 'heading');
        $contextTranslated = ucfirst($this->objLanguage->code2Txt(
          'mod_personalblocks_context', 'personalblocks', NULL, 
          '[-context-] code'));
        $objTable->addHeaderCell($contextTranslated, 70, 
          "bottom", 'left', 'heading');
        $objTable->addHeaderCell($this->objLanguage->languageText(
          'mod_personalblocks_sortorder','personalblocks'), 
          70, "bottom", 'left', 'heading');
        $objTable->addHeaderCell($this->getAddButton(), 70, "bottom", 'left', 'heading');
        $objTable->endHeaderRow();

        if (isset($ar)) {
            if (count($ar) > 0) {
                $rowcount=0;
                foreach ($ar as $line) {
                    $oddOrEven = ($rowcount == 0) ? "odd" : "even";
                    
                    $id = $line['id'];

                    // Insert the name and blockinfo icon
                    $infIco = $this->getBlockInfoIcon();
                    $tableRow[] = $infIco;
                    if(!empty($line['blockname'])){
                        $blockname = $line['blockname'];
                        $tableRow[] = $blockname;
                    } else {
                        $tableRow[] = "&nbsp;";
                        $blockname ="";
                    }
                    if(!empty($line['location'])){
                        $location = $line['location'];
                        // Get location icon from word
                        $locIcon = $this->getLocationIcon($location);
                        $tableRow[]= $locIcon;
                    } else {
                        $tableRow[]= '&nbsp;';
                    }
                    if(!empty($line['active'])){
                        $active = $line['active'];
                        $activeIco = $this->getYesNoIcon($active);
                        $tableRow[] = $activeIco;
                    } else {
                        $active = FALSE;
                        $activeIco = $this->getYesNoIcon($active);
                        $tableRow[] = $activeIco;
                    }
                   if(!empty($line['blocktype'])){
                        $blocktype = $line['blocktype'];
                        // Get blocktype text from code
                        $blocktypeIco = $this->getBlockTypeIcon($blocktype);
                        $tableRow[] = $blocktypeIco;
                    } else {
                        $tableRow[]= '&nbsp;';
                    }

                    if(!empty($line['context'])){
                        $context = $line['context'];
                        // Get context text from code
                        // @todo - add function for that purpose
                        $tableRow[] = $context;
                    } else {
                        $tableRow[]= '&nbsp;';
                    }
                    if(!empty($line['sortorder'])){
                        $sortorder = $line['sortorder'];
                        // Get sortorder text from code
                        // @todo - add function for that purpose
                        $tableRow[] = $sortorder;
                    } else {
                        $tableRow[]= '&nbsp;';
                    }
                    $tableRow[] = $this->getEditButton($id)
                     . " " . $this->getDeleteIcon($id, $blockname);
                    //Add the row to the table for output
                    $objTable->addRow($tableRow, $oddOrEven);
                    // clear out the array
                    $tableRow=array();
                    // Set rowcount for bitwise determination of odd or even
                    $rowcount = ($rowcount == 0) ? 1 : 0;
                }
                $ret = $objTable->show();
            } else {
                $ret = $objTable->show();
            	$ret .= $this->noRecordsMsg();
            }
        } else {
            $ret = $objTable->show();
        	$ret .= $this->noRecordsMsg();
        }
        return $ret;

    }

    /**
    *
    * Returns the message for when no blocks are found for insertion underneath
    * the empty table within the norecorrdsmessage div
    *
    * @return string The norecords message
    * @access private
    *
    */
    private function noRecordsMsg()
    {
    	return "<br /><span class=\"noRecordsMessage\">"
          . $this->objLanguage->languageText("mod_personalblocks_noblocksfound",'personalblocks')
          . "</span>";
    }

    private function emptyBlocks($location)
    {
        $langElem = "mod_personalblocks_no" . $location;
    	return $this->objLanguage->languageText($langElem,'personalblocks');
    }

    /**
    *
    * Return an add button
    * @return string A rendered add button with the correct URL
    * @access private
    *
    */
    private function getAddButton()
    {
        $objGetIcon = $this->newObject('geticon', 'htmlelements');
        $paramAr = array(
          'action' => 'addpblock',
          'mode' => 'add');
        $addButton = $objGetIcon->getAddIcon($this->uri($paramAr, "personalblocks"));
    	return $addButton;
    }

    /**
    *
    * Return an edit button based on the id of the record to edit
    *
    * @param string $id The key of the record to edit
    * @return string A rendered edit button with the correct URL
    * @access private
    *
    */
    private function getEditButton(&$id)
    {
        $objEditIcon = $this->getObject('geticon', 'htmlelements');
        //The URL for the edit link
        $editLink=$this->uri(array('action' => 'editblock',
          'mode' => 'edit',
          'id' =>$id));
        $objEditIcon->alt=$this->objLanguage->languageText("mod_personalblocks_editblock",'personalblocks');
        return $objEditIcon->getEditIcon($editLink);
    }

    /**
    *
    * Return a green checkmark or a red x mark depending on whether
    * it is passed a 0 or a 1
    *
    * @param boolean $flag Either true or false
    * @return string An image tag pointing to the appropriate icon
    * @access private
    *
    */
    private function getYesNoIcon($flag)
    {
        if ($flag==1 || $flag==TRUE) {
            return  $this->getCheckIcon();
        } else {
            return $this->getXIcon();
        }
    }

    /**
    *
    * Return a green checkmark
    *
    * @return string An image tag pointing to the green check icon
    * @access private
    *
    */
    private function getCheckIcon()
    {
        $objChIcon = $this->newObject('geticon', 'htmlelements');
        $objChIcon->alt = $this->objLanguage->languageText("word_yes");
        $objChIcon->setIcon("greentick");
        return $objChIcon->show();
    }

    /**
    *
    * Return a red X mark
    *
    * @return string An image tag pointing to the red X icon
    * @access private
    *
    */
    private function getXIcon()
    {
        $objXIcon = $this->newObject('geticon', 'htmlelements');
        $objXIcon->alt = $this->objLanguage->languageText("word_no");
        $objXIcon->setIcon("redcross");
        return $objXIcon->show();
    }

    /**
    *
    * Return a block info icon
    *
    * @return string An image tag pointing to the block info icon
    * @access private
    *
    */
    private function getBlockInfoIcon()
    {
        $objBlIcon = $this->newObject('geticon', 'htmlelements');
        $objBlIcon->alt = $this->objLanguage->languageText("word_no");
        $objBlIcon->valign = "middle";
        $objBlIcon->setIcon("blockinfo", "png");
        return $objBlIcon->show();
    }

    /**
    *
    * Return a block info icon greyed out for the header
    *
    * @return string An image tag pointing to the block info icon
    * @access private
    *
    */
    private function getBlkInfIconGrey()
    {
        $objBlGIcon = $this->newObject('geticon', 'htmlelements');
        $objBlGIcon->alt = $this->objLanguage->languageText("word_no");
        $objBlGIcon->valign = "middle";
        $objBlGIcon->setIcon("blockinfo_grey", "png");
        return $objBlGIcon->show();
    }

    /**
    *
    * Return a block location icon indicating left, middle, right
    *
    * @return string An image tag for an icon indicating left, middle, right
    * @access private
    *
    */
    private function getLocationIcon($location)
    {
        $objBlLocIcon = $this->newObject('geticon', 'htmlelements');
        $objBlLocIcon->alt = $location;
        $objBlLocIcon->valign = "middle";
        $objBlLocIcon->setIcon("blocklocation_$location", "png");
        return $objBlLocIcon->show();
    }

    /**
    *
    * Return a green checkmark or a red x mark depending on whether
    * it is passed a 0 or a 1
    *
    * @param boolean $flag Either true or false
    * @return string An image tag pointing to the appropriate icon
    * @access private
    *
    */
    private function getBlockTypeIcon($blocktype)
    {
        if ($blocktype=="personal") {
            return  $this->getPersonalIcon();
        } else {
            return $this->getContextIcon();
        }
    }


    /**
    *
    * Return a icon indicating personal block
    *
    * @return string An image tag pointing to the personal.gif icon
    * @access private
    *
    */
    private function getPersonalIcon()
    {
        $objChIcon = $this->newObject('geticon', 'htmlelements');
        $objChIcon->alt = $this->objLanguage->languageText(
          "mod_personalblocks_personal", "personalblocks");
        $objChIcon->setIcon("personal", "png");
        return $objChIcon->show();
    }

    /**
    *
    * Return a icon indicating context block
    *
    * @return string An image tag pointing to the personal.gif icon
    * @access private
    *
    */
    private function getContextIcon()
    {
        $objChIcon = $this->newObject('geticon', 'htmlelements');
        $objChIcon->alt = ucfirst($this->objLanguage->code2Txt(
          'mod_personalblocks_context',
          'personalblocks', NULL,
          '[-context-] code'));
        $objChIcon->setIcon("course", "png");
        return $objChIcon->show();
    }

    /**
    *
    * Return an delete icon
    *
    * @param string $id The key of the record to delete
    * @param string $blockname The name of the block for use in the confirm message
    * @return string A rendered delete icon with the correct URL
    * @access private
    *
    */
    private function getDeleteIcon(&$id, $blockname)
    {
    	$objDelIcon = $this->newObject('geticon', 'htmlelements');
        // The delete icon with link uses confirm delete utility.
        $objDelIcon->setIcon("delete");
        $objDelIcon->alt=$this->objLanguage->languageText("mod_personalblocks_delblock",'personalblocks');
        $delLink = $this->uri(array(
          'action' => 'delete',
          'confirm' => 'yes',
          'id' => $id));
        $objConfirm = $this->getObject('confirm','utilities');
        $rep = array('TITLE' => $blockname);
        $objConfirm->setConfirm($objDelIcon->show(), $delLink,
          $this->objLanguage->code2Txt("mod_personalblocks_confmsg", 
           'personalblocks', $rep));
        return $objConfirm->show();
    }

    /**
    *
    * Render an edit / add form for editing or adding a personal block
    *
    * @return string The rendered form
    * @access public
    *
    */
    public function renderEditAddForm()
    {
        $mode=$this->getParam("mode", "add");
        //Set up the form action
        $paramArray=array(
        'action' => 'save',
        'mode' => $mode);
        $formAction=$this->uri($paramArray);
        //Load the form class
        $this->loadClass('form','htmlelements');
        //Load the textinput class
        $this->loadClass('textinput','htmlelements');
        //Load the textarea class
        $this->loadClass('textarea','htmlelements');
        //Load the label class
        $this->loadClass('label','htmlelements');
        //Create and instance of the form class
        $objForm = new form('personalblock');
        //Set the action for the form to the uri with paramArray
        $objForm->setAction($formAction);
        //Set the displayType to 3 for freeform
        $objForm->displayType=3;
        //See if its edit or add
        $mode = $this->getParam("mode", "add");
        if ($mode=="edit") {
        	$keyvalue=$this->getParam("id", NULL);
            if (!$keyvalue) {
                die($this->objLanguage->languageText("modules_badkey").": ".$keyvalue);
            }
            $ar = $this->objDb->getRow('id', $keyvalue);
            $id = $ar['id'];
            $blockname = $ar['blockname'];
            $blocktype = $ar['blocktype'];
            $context = $ar['context'];
            $location = $ar['location'];
            $sortorder = $ar['sortorder'];
            $active = $ar['active'];
            $blockcontent = $ar['blockcontent'];
        } else {
            $location="left";
            $blockname="";
            $blocktype = "personal";
            $blockcontent="";
            $active="1";
            $sortorder = "";
            $context = NULL;
        }
        //Create an element for the hidden text input
        $objElement = new textinput("id");
        //Set the value to the primary keyid
        if (isset($id)) {
            $objElement->setValue($id);
        }
        //Set the field type to hidden for the primary key
        $objElement->fldType="hidden";
        //Add the hidden PK field to the form
        $objForm->addToForm($objElement->show());

        //Create an element for the input of blockname
        $objElement = new textinput ("blockname");
        $requiredName = $this->objLanguage->languageText("mod_personalblocks_namerequired", "personalblocks");
        //Add a validation rule
        $objForm->addRule('blockname',$requiredName,'required');
        //Set the value of the element to $blockid
        if (isset($blockname)) {
            $objElement->setValue($blockname);
        }
        $ifTable= "<table>\n"
          . "<tr><td>"
          . $this->objLanguage->languageText("mod_personalblocks_blname", "personalblocks")
          . "</td><td>".$objElement->show()."</td></tr>";
                    
        // Add a text area for the block contents.
        $this->loadClass('textarea', 'htmlelements');
        $widgetTxt = new textarea('blockcontent', $blockcontent);
        $requiredContents = $this->objLanguage->languageText("mod_personalblocks_contentsrequired", "personalblocks");
        //---------------$widgetTxt->setId("blockcontentstyle");
        //Add a validation rule
        $objForm->addRule('blockcontent',$requiredContents,'required');
        $ifTable .= "<tr><td valign='top'>"
          . $this->objLanguage->languageText("mod_personalblocks_content", "personalblocks")
          . "</td><td>" . $widgetTxt->show() . "</td></tr>";
        //Make it an expanding textarea
        $aG = $this->getObject("jqexpanding", "jquery");
        $aG->show("input_blockcontent");
        // Add a radio set for choosing location.
        $this->loadClass("radio", "htmlelements");
        $objRadioElement = new radio('location');
        $objRadioElement->addOption('left',  "&nbsp;" . $this->objLanguage->languageText("mod_personalblocks_left", "personalblocks") . "&nbsp;");
        $objRadioElement->addOption('middle', "&nbsp;" . $this->objLanguage->languageText("mod_personalblocks_middle", "personalblocks") . "&nbsp;");
        $objRadioElement->addOption('right', "&nbsp;" . $this->objLanguage->languageText("mod_personalblocks_right", "personalblocks") . "&nbsp;");
        $objRadioElement->setSelected($location);
        $ifTable .= "<tr><td>"
          . $this->objLanguage->languageText("mod_personalblocks_location", "personalblocks")
          . "</td><td>" . $objRadioElement->show() . "</td></tr>";
        // Add a radio set for active / not active.
        $objRadioActive = new radio('active');
        $objRadioActive->addOption('1',  "&nbsp;" . $this->objLanguage->languageText("mod_personalblocks_isactive", "personalblocks") . "&nbsp;");
        $objRadioActive->addOption('0', "&nbsp;" . $this->objLanguage->languageText("mod_personalblocks_inactive", "personalblocks") . "&nbsp;");
        $objRadioActive->setSelected($active);
        $ifTable .= "<tr><td>"
          . $this->objLanguage->languageText("mod_personalblocks_active", "personalblocks")
          . "</td><td>" . $objRadioActive->show() . "</td></tr>";
          
        // Check if they are in a context and have rights to add a block to it.
        // If they are in a context, and have rights to it, this should be visible
        // If they are not in a context, or have no rights to it, this should not be visible
        $objContext = $this->getObject('dbcontext', 'context');
        if($objContext->isInContext()){
            //Check if they have edit/author rights
            $contextCode = $objContext->getContextCode();
            $userId = $this->objUser->userId();
            if ($this->objUser->isContextLecturer($userId, $contextCode) || 
              $this->objUser->isAdmin()) {
            	$curContext = TRUE;
            } else {
            	$curContext = FALSE;
            }
            
        } else {
            $curContext = FALSE;
            $contextCode="";
        }
        $indicator = $this->getBlockTypeIcon($blocktype);
        //If they are not in a context, or have no rights to it then don't show context block
        if (!$curContext) {
            $objBlockType = new textinput("blocktype");
            //Set the field type to hidden for the block type
            $objBlockType->fldType="hidden";
            $objBlockType->setValue($blocktype);
            //Add the hidden blocktype field to the form
            $ifTable .= "<tr><td>"
              . $this->objLanguage->languageText("mod_personalblocks_blocktype", "personalblocks")
              . "</td><td valign=\"middle\">{$indicator}&nbsp;&nbsp;" . $blocktype . $objBlockType->show() . "</td></tr>"; 
        } else {
            // Load the Javascript for setting the context
            $this->loadContextClickJavascript($contextCode);
            // Add a radio for the  block type (Personal, context)
            $contextTranslated = ucfirst($this->objLanguage->code2Txt('mod_personalblocks_context',
              'personalblocks', NULL, '[-context-] code'));
            //The radio button for the default block type of personal
            $radioPersonal = '<input type="radio" value="personal" ' .
                    'name="blocktype" ';
            if ($blocktype == "personal") {
                $radioPersonal.= ' checked="checked" ';
                $visibility="hidden";
            }
            $radioPersonal.= 'onclick="setContextCode(\'personal\');">&nbsp;' 
              . '<label for="personal">'
              . $this->objLanguage->languageText("mod_personalblocks_personal", "personalblocks") 
              . '</label>&nbsp;';
            //The radio button for the context block type
            $radioContext = '<input type="radio" value="context" ' .
                    'name="blocktype" ';
            if ($blocktype == "context") {
                $radioContext .= ' checked="checked" ';
                $visibility="visible";
            }
            $radioContext .= 'onclick="setContextCode(\'context\');">&nbsp;' 
              . '<label for="context">'
              . $contextTranslated . '</label>&nbsp;';
              
            $ifTable .= "<tr><td>" 
              . $this->objLanguage->languageText("mod_personalblocks_blocktype", "personalblocks")
              . "</td><td valign=\"middle\">" 
              . $radioPersonal . "&nbsp;&nbsp;" . $radioContext 
              . "&nbsp;&nbsp;<span id=\"typeIndicator\">{$indicator}</span></td></tr>";
        }

        // Add a textbox for the sort order
        //@todo Make it an ajax thing
        $objElement = new textinput ("sortorder");
        //Set the value of the element to $sortorder
        if (isset($sortorder)) {
            $objElement->setValue($sortorder);
        }
        $ifTable .= "<tr><td>"
          . $this->objLanguage->languageText("mod_personalblocks_sortorder", "personalblocks")
          . "</td><td>".$objElement->show()."</td></tr>";

        //Add a dropdown for the context
        //@ToDo add the dropdown and some JS to make it visible
        $objElement = new textinput ("context");
        //Set the value of the element to the cntext
        if (isset($context)) {
            $objElement->setValue($context);
        }
        // $visibility is used by the Javascript to set the visibility of the context
        if (!$curContext) {
            //Set the field type to hidden for the block type
            $objElement->fldType="hidden";
            $ifTable .= $objElement->show();
        } else {
            $label = ucfirst($this->objLanguage->code2Txt('mod_personalblocks_contextcode',
              'personalblocks', NULL, '[-context-] code'));
            $ifTable .= "<tr><td><span id='contextLabel' style=\"visibility: {$visibility};\">" 
              . $label . "</span></td><td><span id='contextCode' style=\"visibility: {$visibility};\">"
              .$objElement->show()."</span></td></tr>";
        }

        // Create an instance of the button object
        $this->loadClass('button', 'htmlelements');
        // Create a submit button
        $objElement = new button('submit');
        // Set the button type to submit
        $objElement->setToSubmit();
        // Use the language object to add the word save
        $objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
    	// Add the buttons to the form
        $ifTable .= "<tr><td>" . $objElement->show() . "</td><td>&nbsp;</td></tr>";
        $ifTable .= "</table>";
        $objForm->addToForm($ifTable);
        return $objForm->show();
    }

    /**
    * 
    * Create the JS for the block type. It hides and shows the context
    * textbox and its label, as well as setting the value to the current
    * context when the context radio button is clicked, and removing the
    * value when personal block is clicked.
    * 
    * @param string $contextCode The context code of the context that the user is in.
    * @access private
    * @return TRUE
    *  
    */
    private function loadContextClickJavascript($contextCode)
    {
        $ret = '
<script type="text/javascript">
//<![CDATA[
function setContextCode(blocktype){
    var contextCode = document.getElementById(\'contextCode\');
    var contextLabel = document.getElementById(\'contextLabel\');
    var typeIndicator = document.getElementById(\'typeIndicator\');
    if (blocktype == "context") {
        document.forms[\'personalblock\'].context.value=\'' . $contextCode . '\';
        contextCode.style.visibility="visible";
        contextLabel.style.visibility="visible";
        typeIndicator.innerHTML = \'' . $this->getContextIcon() . '\';
    } else {
        document.forms[\'personalblock\'].context.value=\'\';
        contextCode.style.visibility="hidden";
        contextLabel.style.visibility="hidden";
        typeIndicator.innerHTML = \'' . $this->getPersonalIcon() . '\';
    }
}
// ]]>
</script>';
        $this->appendArrayVar('headerParams', $ret);
        
        return TRUE;
    }
    

}
?>