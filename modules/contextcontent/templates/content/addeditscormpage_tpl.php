<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');

//Array to contain language items for JS
$arrLang = array();
$arrLang['reservedfolder'] = $this->objLanguage->languageText('mod_scorm_reservedfolder','scorm');
$arrLang['containsscorm'] = $this->objLanguage->languageText('mod_scorm_containsscorm','scorm');
$arrLang['buttondisabled'] = $this->objLanguage->languageText('mod_scorm_buttondisabled','scorm');

//AJAX to check if selected folder contains scorm

$this->appendArrayVar('headerParams', '
    <script type="text/javascript">
        
        // Flag Variable - Update message or not
        var doUpdateMessage = false;
        
        // Var Current Entered Code
        var currentCode;
        
        // Action to be taken once page has loaded
        jQuery(document).ready(function(){
            jQuery("#input_parentfolder").bind(\'change\', function() {
                checkCode(jQuery("#input_parentfolder").attr(\'value\'));
            });
        });
        
        // Function to check whether context code is taken
        function checkCode(code)
        {
            // Messages can be updated
            doUpdateMessage = true;
            
            // If code is null
            if (code == null) {
                // Remove existing stuff
                jQuery("#contextcodemessage").html("");
                jQuery("#contextcodemessage").removeClass("error");
                jQuery("#input_contextcode").removeClass("inputerror");
                jQuery("#contextcodemessage").removeClass("success");
                doUpdateMessage = false;
                
            // If code is root - Reserved. Saves Ajax Call
            } else if (code.toLowerCase() == "root") {
                
                currentCode = code;
                
                jQuery("#contextcodemessage").html("'.$arrLang['reservedfolder'].'");
                jQuery("#contextcodemessage").addClass("error");
                jQuery("#input_contextcode").addClass("inputerror");
                jQuery("#contextcodemessage").removeClass("success");
                jQuery("#contextcodemessage2").html("'.$arrLang['buttondisabled'].'");   
                jQuery("#contextcodemessage2").addClass("error");
                jQuery("#submitbutton").attr("disabled", "disabled");                               
                doUpdateMessage = false;
                
            // Else Need to do Ajax Call
            } else {
            
                
                
                // Check that existing code is not in use
                if (currentCode != code) {
                    
                    // Set message to checking
                    jQuery("#contextcodemessage").removeClass("success");
                    jQuery("#contextcodemessage").html("<span id=\"contextcodecheck\">'.addslashes($objIcon->show()).' Checking ...</span>");
                    
                    
                    // Set current Code
                    currentCode = code;
                    
                    // DO Ajax
                    jQuery.ajax({
                        type: "GET", 
                        url: "index.php", 
                        data: "module=scorm&action=checkfolder&code="+code, 
                        success: function(msg){                        
                            // Check if messages can be updated and code remains the same
                            if (doUpdateMessage == true && currentCode == code) {
                                
                                // IF code exists
                                if (msg == "ok") {
                                    jQuery("#contextcodemessage2").html("");                                
                                    jQuery("#contextcodemessage").html("'.$arrLang['containsscorm'].'");
                                    jQuery("#contextcodemessage").addClass("success");
                                    jQuery("#contextcodemessage2").removeClass("error");                                    
                                    jQuery("#contextcodemessage").removeClass("error");
                                    jQuery("#input_parentfolder").removeClass("inputerror");
                                    jQuery("#submitbutton").removeAttr("disabled");
                                } else if(msg == "notok") {
                                    jQuery("#contextcodemessage2").html("'.$arrLang['buttondisabled'].'");
                                    jQuery("#contextcodemessage2").addClass("error");
                                    jQuery("#contextcodemessage").html("'.$arrLang['doesntcontainscorm'].'");
                                    jQuery("#contextcodemessage").addClass("error");
                                    jQuery("#input_parentfolder").addClass("inputerror");
                                    jQuery("#contextcodemessage").removeClass("success");
                                    jQuery("#submitbutton").attr("disabled", "disabled");                                    
                                } else {
                                    jQuery("#contextcodemessage2").html("'.$arrLang['buttondisabled'].'");
                                    jQuery("#contextcodemessage2").addClass("error");
                                    jQuery("#contextcodemessage").html("'.$arrLang['unknownerror'].'");
                                    jQuery("#contextcodemessage").addClass("error");
                                    jQuery("#input_parentfolder").addClass("inputerror");
                                    jQuery("#contextcodemessage").removeClass("success");
                                    jQuery("#submitbutton").attr("disabled", "disabled");
                                }                                
                            }
                        }
                    });
                }
            }
        }
    </script>');

if ($mode == 'edit') {
    $formaction = 'updatescormpage';
    echo '<h1>'.$this->objLanguage->languageText('mod_contextcontent_editcontextpages','contextcontent').': - '.$page['pagetitle'].'</h1>';
} else {
    echo '<h1>'.$this->objLanguage->languageText('mod_contextcontent_addnewcontextpages','contextcontent').' - '.$this->objContext->getTitle().'</h1>';
    $formaction = 'savescormpage';
}
$form = new form ('savescormpage', $this->uri(array(
    'action'=>$formaction,
    'chapter'=>$chapter,
    'currentchapter'=>$chapter,
    'tree'=>$tree
)));
$table = $this->newObject('htmltable', 'htmlelements');
$label = new label ($this->objLanguage->languageText('mod_contextcontent_parent','contextcontent'), 'input_parentnode');

$table->startRow();
$table->addCell($label->show());
$table->addCell($tree);
$table->endRow();
$table->startRow();
$table->addCell('');
$table->endRow();
$title = new textinput('menutitle');
$form->addRule('page', 'Title is required','required');
$title->size = 60;

if ($mode == 'edit') {
    $title->value = $page['pagetitle'];
}

$label = new label ($this->objLanguage->languageText('mod_scorm_scormtitle','scorm'), 'input_page');
$table->startRow();
$table->addCell($label->show(), 150);
$table->addCell($title->show());
$table->endRow();
//spacer
$table->startRow();
$table->addCell("&nbsp;");
$table->addCell("&nbsp;");
$table->endRow();


if ($mode == 'edit') {
// name of dropdown = 'parentfolder'
    $usrFolders = $this->objFolders->getTreedropdown($page['pagecontent']);
//    $htmlArea->value = $page['introduction'];
} else {
    $usrFolders = $this->objFolders->getTreedropdown(Null);
}
$label = new label ($this->objLanguage->languageText('mod_scorm_selectscormfolder','scorm'), 'input_parentfolder');
$table->startRow();
$table->addCell($label->show());
//$table->addCell($htmlArea->show());
$table->addCell($usrFolders.' <pre id="contextcodemessage">'.$contextCodeMessage.'</pre>');
$table->endRow();
//spacer
$table->startRow();
$table->addCell("&nbsp;");
$table->addCell("&nbsp;");
$table->endRow();


$radio = new radio ('visibility');
$radio->addOption('Y', ' '.$this->objLanguage->languageText('word_yes','system', 'Yes'));
$radio->addOption('N', ' '.$this->objLanguage->languageText('word_no','system', 'No'));
$radio->addOption('I', ' '.$this->objLanguage->languageText('mod_contextcontent_onlyshowintroduction','contextcontent'));

if ($mode == 'edit') {
    $radio->setSelected($page['visibility']);
} else {
    $radio->setSelected('Y');
}
$radio->setBreakSpace(' &nbsp; ');

$table->startRow();
$table->addCell($this->objLanguage->code2Txt('mod_contextcontent_visibletostudents','contextcontent'));
$table->addCell($radio->show());
$table->endRow();
//spacer
$table->startRow();
$table->addCell("&nbsp;");
$table->addCell("&nbsp;");
$table->endRow();

$form->addToForm($table->show());


$hiddeninput = new hiddeninput('scorm', 'Y');
$form->addToForm($hiddeninput->show());


$button = new button('submitbutton', $this->objLanguage->languageText('mod_contextcontent_addcontextpages','contextcontent'));
$button->cssId = 'submitbutton';
$button->setToSubmit();
$form->addToForm($button->show().' <pre id="contextcodemessage2">'.$contextCodeMessage.'</pre>');

if ($mode == 'edit') {
    $hiddeninput = new hiddeninput('id', $id);
    $form->addToForm($hiddeninput->show());

    $hiddeninput = new hiddeninput('pagecontentid', $page['id']);
    $form->addToForm($hiddeninput->show());

    $hiddeninput = new hiddeninput('contextpageid', $page['contextpageid']);
    $form->addToForm($hiddeninput->show());

}

echo $form->show();

?>
