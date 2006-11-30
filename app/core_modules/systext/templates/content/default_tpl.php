<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package systext
*/

/**
* Default template for the systext module
* Author Kevin Cyster
* */

// set up body parameter
    if($mode == 'addsystem' or $mode == 'editsystem'){
        $this -> setVar('bodyParams', " onload = \"{document.form.systemtype.focus();document.form.systemtype.select();}\"");
    }elseif($mode == 'addtext'){
        $this -> setVar('bodyParams', " onload = \"{document.form.text.focus();document.form.text.select();}\"");
    }elseif($mode == 'edittext' and $canDelete != 'N'){
        $this -> setVar('bodyParams', " onload = \"{document.form.text.focus();document.form.text.select();}\"");
    }

// set up html elements
    $objHeader =& $this -> newObject('htmlheading', 'htmlelements');
    $objTable =& $this -> newObject('htmltable', 'htmlelements');
    $objText =& $this -> newObject('textinput', 'htmlelements');
    $objLink =& $this -> newObject('link', 'htmlelements');
    $objButton =& $this -> newObject('button', 'htmlelements');
    $objForm =& $this -> newObject('form', 'htmlelements');
    $objIcon =& $this -> newObject('geticon', 'htmlelements');

// set up language items
    $header = $this -> objLanguage -> languageText('mod_systext_name','systext');
    $systemLabel = $this -> objLanguage -> languageText('mod_systext_system','systext');
    $textLabel = $this -> objLanguage -> languageText('mod_systext_text','systext');
    $nosystemLabel = $this -> objLanguage -> languageText('mod_systext_nosystem','systext');
    $notextLabel = $this -> objLanguage -> languageText('mod_systext_notext','systext');
    $addsystemLabel = $this -> objLanguage -> languageText('mod_systext_addsystem','systext');
    $newsystemLabel = $this -> objLanguage -> languageText('mod_systext_newsystem','systext');
    $addtextLabel = $this -> objLanguage -> languageText('mod_systext_addtext','systext');
    $newtextLabel = $this -> objLanguage -> languageText('mod_systext_newtext','systext');
    $saveLabel = $this -> objLanguage -> languageText('mod_systext_save','systext');
    $cancelLabel = $this -> objLanguage -> languageText('mod_systext_cancel','systext');
    $deleteLabel = $this -> objLanguage -> languageText('mod_systext_delete','systext');
    $deleteConfirm = $this -> objLanguage -> languageText('mod_systext_deleteconfirm','systext');
    $exitLabel = $this -> objLanguage -> languageText('mod_systext_exit','systext');

// set up heading
    $objHeader -> str = $header;
    $objHeader -> type = 1;
    $str = $objHeader -> show();
    echo $str;

// set up add icons
    $objIcon -> title = $addsystemLabel;
    $addsystemIcon = $objIcon -> getAddIcon($this -> uri(array('mode' => 'addsystem')));

    $objIcon -> title = $addtextLabel;
    $addtextIcon = $objIcon -> getAddIcon($this -> uri(array('mode' => 'addtext')));

// set up hidden textinput for mode
    $objText = new textinput('mode', $mode);
    $objText -> fldType = 'hidden';
    $modeHiddenText = $objText -> show();

// set up hidden textinput for systemId
    $objText = new textinput('systemId', $systemId);
    $objText -> fldType = 'hidden';
    $systemHiddenText = $objText -> show();

// set up hidden textinput for textId
    $objText = new textinput('textId', $textId);
    $objText -> fldType = 'hidden';
    $textHiddenText = $objText -> show();

// set up hidden textinput for canDelete
    $objText = new textinput('candelete', $canDelete);
    $objText -> fldType = 'hidden';
    $candeleteHiddenText = $objText -> show();

// set up textinput for new system type
    $objText = new textinput('systemtype', $newsystemLabel);
    $objText -> extra = ' MAXLENGTH="15"';
    $objText -> size = '15';
    $newsystemText = $objText -> show();

// set up textinput for new text item
    $objText = new textinput('text', $newtextLabel);
    $objText -> extra = ' MAXLENGTH="50"';
    $objText -> size = '15';
    $newtextText = $objText -> show();

// set up textinput for abstracts
    $objText = new textinput('abstract[]');
    $objText -> extra = ' MAXLENGTH="50"';
    $objText -> size = '15';
    $abstractText = $objText -> show();

// set up hidden textinput for delete
    $objText = new textinput('deleted', '');
    $objText -> fldType = 'hidden';
    $deleteHiddenText = $objText -> show();

// set up save, cancel and delete buttons
    $objButton = new button('save', $saveLabel);
    $objButton -> setToSubmit();
    $saveButton = $objButton -> show();

    $objButton = new button('cancel', $cancelLabel);
    $objButton -> setToSubmit();
    $cancelButton = $objButton -> show();

    $objButton = new button('deleteButton', $deleteLabel);
    $objButton -> extra = ' onClick="if( confirm(\''.$deleteConfirm.'\') ) {document.form.deleted.value = \'Delete\' ;document.form.submit();};"';
    $deleteButton = $objButton -> show();

// set up heading colspan
    if($mode == 'addsystem'){
        $colspan = 'colspan="' . (count($arrSystemTypes) + 1) .'"';
    }else{
        $colspan = 'colspan="' . (count($arrSystemTypes)).'"';
    }

// set up heading rowspan
    if($mode == 'addtext'){
        $rowspan = 'rowspan="' . (count($arrTextItems) + 1).'"';
    }else{
        $rowspan = 'rowspan="' . (count($arrTextItems)).'"';
    }

// set up table
    $objTable -> cellspacing = '2';
    $objTable -> cellpadding = '2';
    $objTable -> border = '1';

// set up table data
    $objTable -> startRow();
    $objTable -> addCell($modeHiddenText, '', '', '', 'heading', 'colspan="2"  rowspan="2"');
    $objTable -> addCell($systemLabel . " " . $addsystemIcon, '', '', 'center', 'heading', $colspan);
    $objTable -> endRow();

// set up system type headings
    $objTable -> startRow();
    //var_dump($arrSystemTypes);die();
    foreach($arrSystemTypes as $key => $systemType){
        if($systemType['id'] == '1@init'){
            $str = $systemType['systemtype']; // default system can not be edited
        }else{
            if($mode == 'editsystem' and $systemId == $systemType['id']){ // set up input box for editing
                $objText = new textinput('systemtype', $systemType['systemtype']);
                $objText -> extra = ' MAXLENGTH="15"';
                $objText -> size = '15';
                $text = $objText -> show();
                $str = $systemHiddenText . $text . "<br/>" . $saveButton . " " . $cancelButton . " " . $deleteButton . $deleteHiddenText;
            }else{ // set up links
                $objLink = new link($this -> uri(array('mode' => 'editsystem', 'systemId' => $systemType['id']), 'systext'));
                $objLink -> link = $systemType['systemtype'];
                $link = $objLink -> show();
                $str = $link;
            }
        }
        $objTable -> addCell($str, '', '', 'center', 'heading', '');
    }
    if($mode == 'addsystem'){ // set up input box for adding a new system type
        $objTable -> addCell($newsystemText . "<br/>" . $saveButton . " " . $cancelButton, '', '', 'center', 'heading', '');
    }
    $objTable -> endRow();

// set up text item headings
    $i = 0;
    foreach($arrTextItems as $textItem){
        $class = (($i++ % 2) == 0)? 'even':'odd';
        $objTable -> startRow();
        if($i == 1){
            $objTable -> addCell($textLabel . " " . $addtextIcon, '', '', '', 'heading', $rowspan);
        }
        if($mode == 'edittext' and $textId == $textItem['id'] and $canDelete == 'N'){
            // set up input box for editing
            $objLink = new link($this -> uri(array('mode' => 'edittext', 'textId' => $textItem['id'], 'candelete' => 'N'), 'systext'));
            $objLink -> link = $textItem['textinfo'];
            $link = $objLink -> show();
            $str = $candeleteHiddenText . $textHiddenText . $link . "<br/>" . $saveButton . " " . $cancelButton;
        }elseif($mode == 'edittext' and $textId == $textItem['id'] and $canDelete != 'N'){
            $objText = new textinput('text', $textItem['textinfo']);
            $objText -> extra = ' MAXLENGTH="15"';
            $objText -> size = '15';
            $text = $objText -> show();
            $str = $candeleteHiddenText . $textHiddenText . $text . "<br/>" . $saveButton . " " . $cancelButton . " " . $deleteButton . $deleteHiddenText;
        }else{
            // set up links
            if($textItem['candelete'] == 'N'){
                $objLink = new link($this -> uri(array('mode' => 'edittext', 'textId' => $textItem['id'], 'candelete' => 'N'), 'systext'));
            }else{
                $objLink = new link($this -> uri(array('mode' => 'edittext', 'textId' => $textItem['id']), 'systext'));
            }
            $objLink -> link = $textItem['textinfo'];
            $link = $objLink -> show();
            $str = $link;
        }
        $objTable -> addCell($str, '', '', '', 'heading', '');

        // set up text abstracts
        foreach($arrSystemTypes as $systemType){
            $arrAbstractItem = $this -> facet -> getAbstractText($systemType['id'], $textItem['id']);
            $abstractId = $arrAbstractItem[0]['id'];
            if($mode == 'editsystem' and $systemId == $systemType['id']){
                $objText = new textinput('abstract[' . $abstractId . "-" . $systemType['id'] . "-" . $textItem['id'] . ']', $arrAbstractItem[0]['abstract']);
                $objText -> extra = ' MAXLENGTH="50"';
                $objText -> size = '15';
                $abstractText = $objText -> show();
                $objTable -> addCell($abstractText, '', '', 'center', $class, '');
            }elseif($mode == 'edittext' and $textId == $textItem['id']){
                // default can not be edited
                if($abstractId != 'init1' and $abstractId != 'init_2' and $abstractId != 'init_3' and $abstractId != 'init_4' and $abstractId != 'init_5' and $abstractId != 'init_6' and $abstractId != 'init_7' and $abstractId != 'init_8' and $abstractId != 'init_9' and $abstractId != 'init_10' and $abstractId != 'init_11' and $abstractId != 'init_12'){
                    $objText = new textinput('abstract[' . $abstractId . "-" . $systemType['id'] . "-" . $textItem['id'] . ']', $arrAbstractItem[0]['abstract']);
                    $objText -> extra = ' MAXLENGTH="50"';
                    $objText -> size = '15';
                    $abstractText = $objText -> show();
                    $objTable -> addCell($abstractText, '', '', 'center', $class, '');
                }else{
                    if(empty($arrAbstractItem)){
                        $objTable -> addCell("-", '', '', 'center', $class, '');
                    }else{
                        $objTable -> addCell($arrAbstractItem[0]['abstract'], '', '', 'center', $class, '');
                    }
                }
            }else{
                if(empty($arrAbstractItem)){
                    $objTable -> addCell("-", '', '', 'center', $class, '');
                }else{
                    $objTable -> addCell($arrAbstractItem[0]['abstract'], '', '', 'center', $class, '');
                }
            }
        }
        if($mode == 'addsystem'){ // set up input boxes for adding abstracts for a new system type
            $objTable -> addCell($abstractText, '', '', 'center', $class, '');
        }
        $objTable -> endRow();
    }
    if($mode == 'addtext'){ // set up input box for adding a new text item
        $class = (($i++ % 2) == 0)? 'even':'odd';
        $objTable -> startRow();
        $objTable -> addCell($newtextText . "<br/>" . $saveButton . " " . $cancelButton, '', '', '', 'heading', '');
        foreach($arrSystemTypes as $systemType){ // set up input boxes for adding abstracts for a new text item
            $objTable -> addCell($abstractText, '', '', 'center', $class, '');
        }
        $objTable -> endRow();
    }
    $str = $objTable -> show();

    // set up form
    $objForm = new form('form', $this -> uri(array('action' => 'submit')));
    $objForm -> addToForm($str);
    $str = $objForm -> show();
    echo $str;

// set up exit link
    $objLink = new link($this -> uri(array(), '_default'));
    $objLink -> link = $exitLabel;
    $exitLink = $objLink -> show();

    echo "<br/>" . $exitLink;
?>