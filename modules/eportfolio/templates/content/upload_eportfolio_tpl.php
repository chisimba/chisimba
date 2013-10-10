<?php
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');
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
                
                jQuery("#contextcodemessage").html("This folder is reserved. You cannot extract scorm in the ROOT folder.");
                jQuery("#contextcodemessage").addClass("error");
                jQuery("#input_contextcode").addClass("inputerror");
                jQuery("#contextcodemessage").removeClass("success");
                doUpdateMessage = false;
                
            // Else Need to do Ajax Call
            } else {
            
                
                
                // Check that existing code is not in use
                if (currentCode != code) {
                    
                    // Set message to checking
                    jQuery("#contextcodemessage").removeClass("success");
                    jQuery("#contextcodemessage").html("<span id=\"contextcodecheck\">' . addslashes($objIcon->show()) . ' Checking ...</span>");
                    
                    
                    // Set current Code
                    currentCode = code;
                    
                    // DO Ajax
                    jQuery.ajax({
                        type: "GET", 
                        url: "index.php", 
                        data: "module=eportfolio&action=checkfolder&code="+code, 
                        success: function(msg){                        
                            // Check if messages can be updated and code remains the same
                            if (doUpdateMessage == true && currentCode == code) {
                                var result;
                                ifok = 1;
                                // IF code exists
                                if (msg == ifok ) {
                                    jQuery("#contextcodemessage").html("Good! It complies to IMS Global eportfolio Specification");
                                    jQuery("#contextcodemessage2").html("");
                                    jQuery("#contextcodemessage").addClass("success");
                                    jQuery("#contextcodemessage").removeClass("error");
                                    jQuery("#input_parentfolder").removeClass("inputerror");
                                    jQuery("#savebutton").removeAttr("disabled");

                                // Else
                                } else {
                                    jQuery("#contextcodemessage").html("The folder contents do NOT comply to IMS Global eportfolio Specification");
                                    jQuery("#contextcodemessage2").html("Button is Disabled");
                                    jQuery("#contextcodemessage2").addClass("error");
                                    jQuery("#contextcodemessage").addClass("error");
                                    jQuery("#input_parentfolder").addClass("inputerror");
                                    jQuery("#contextcodemessage").removeClass("success");
                                    jQuery("#savebutton").attr("disabled", "disabled");                                    
                                }
                                
                            }
                        }
                    });
                }
            }
        }
    </script>');
echo '<h1>' . $this->objLanguage->languageText('mod_eportfolio_import', 'eportfolio') . ' ' . $this->objLanguage->languageText('mod_eportfolio_wordEportfolio', 'eportfolio') . '</h1>';
$formaction = 'uploadeportfolio';
//echo '<p>Todo: Allow User to place order of chapter</p>';
$form = new form('uploadeportfolio', $this->uri(array(
    'action' => $formaction
)));
//Empty string
$contextCodeMessage3 = "";
$table = $this->newObject('htmltable', 'htmlelements');
$table->width = '100%';
$table->attributes = " align='left' border='0'";
$table->cellspacing = '2';
$table->cellpadding = '5';
//spacer
$table->startRow();
$table->addCell("&nbsp;");
$table->addCell("&nbsp;");
$table->endRow();
$label = new label($this->objLanguage->languageText('mod_eportfolio_selectFolder', 'eportfolio') , 'input_parentfolder');
$usrFolders = $this->objFolders->getTreedropdown(Null);
$table->startRow();
$table->addCell($label->show() . " ", 190, 'top', 'left');
//$table->addCell($htmlArea->show());
$table->addCell($usrFolders . ' <span id="contextcodemessage">' . $contextCodeMessage3 . '</span>', Null, 'top', 'left');
$table->endRow();
//spacer
$table->startRow();
$table->addCell("&nbsp;");
$table->addCell("&nbsp;");
$table->endRow();
$button = new button('submitbutton', $this->objLanguage->languageText('mod_eportfolio_import', 'eportfolio'));
$button->cssId = 'savebutton';
$button->setToSubmit();
$table->startRow();
$table->addCell($button->show());
$table->addCell("&nbsp;");
$table->endRow();
$table->startRow();
$table->addCell(' <span id="contextcodemessage2">' . $contextCodeMessage3 . '</span>');
$table->addCell("&nbsp;");
$table->endRow();
$form->addToForm($table->show());
//$form->addToForm($button->show() . ' <span id="contextcodemessage2">' . $contextCodeMessage . '</span>');
echo $form->show();
?>
