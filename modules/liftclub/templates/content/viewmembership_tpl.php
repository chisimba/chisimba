<?php
// check if the site signup user string is set, if so, use it to populate the fields
if (isset($userstring)) {
    $userstring = base64_decode($userstring);
    $userstring = explode(',', $userstring);
} else {
    $userstring = NULL;
}
$this->loadClass('form', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');
$this->appendArrayVar('headerParams', '
    <script type="text/javascript">
        
        // Flag Variable - Update message or not
        var doUpdateMessage = false;
        
        // Var Current Entered Code
        var currentCode;
        
        // Action to be taken once page has loaded
        jQuery(document).ready(function(){
           // use this to reset several forms at once
           getActions();           
        });
        function getActions(){
            jQuery("#button_submitmessage").bind(\'click\', function() {
                sitepath = jQuery("#input_sitepath").val();
                recipentid = jQuery("#input_favusrid").attr(\'value\');
                messagetitle = jQuery("#input_msgtitle").attr(\'value\');
                messagebody = jQuery("#input_msgbody").attr(\'value\');
                sendmessage(recipentid, messagetitle, messagebody);
            });        
            jQuery("#input_addtofav").bind(\'change\', function() {
                sitepath = jQuery("#input_sitepath").val();
                checkCode(jQuery("#input_favusrid").attr(\'value\'));
            });
        }       
        function checkCode(code)
        {            
            // Messages can be updated
            doUpdateMessage = true;
            
            // If code is null
            if (code == null) {
                // Remove existing stuff
                jQuery("#favmessage").html("Unfortunately there is a problem with that user");
                jQuery("#favmessage").removeClass("error");
                jQuery("#favmessage").removeClass("success");
                doUpdateMessage = false;
                                
            // Else Need to do Ajax Call
            } else {
                
                // Check that existing code is not in use
                if (currentCode != code) {
                    
                    // Set message to checking
                    jQuery("#favmessage").removeClass("success");
                    jQuery("#favmessage").html("<span id=\"favusercheck\">' . addslashes($objIcon->show()) . ' Processing ...</span>");
                                     
                    // Set current Code
                    currentCode = code;
                    
                    // DO Ajax 
                    jQuery.ajax({
                        type: "GET", 
                        url: sitepath, 
                        data: "module=liftclub&action=addfavourite&favusrid="+code, 
                        success: function(msg){                        
                            // Check if messages can be updated and code remains the same
                            if (doUpdateMessage == true && currentCode == code) {
                                
                                // IF code exists
                                if (msg == "ok") {
                                    jQuery("#favmessage").html("Added to your favourites successfully!");
                                    jQuery("#favmessage2").html(" ");
                                    jQuery("#favmessage").addClass("success");
                                    jQuery("#favmessage").removeClass("error");
                                    jQuery("#sendmessage").html("");
                                    jQuery("#sendmessage1").addClass("error");
                                    jQuery("#sendmessage1").html("Kindly refresh page to send a message!");
                                    jQuery("#sendmessage2").html("");
                                    jQuery("#sendmessage3").html("");
                                    jQuery("#sendmessage4").html("");
                                } else if (msg == "exists") {
                                    jQuery("#favmessage").html("Already part of your favourites!");
                                    jQuery("#favmessage2").html(" ");
                                    jQuery("#favmessage").addClass("success");
                                    jQuery("#favmessage").removeClass("error");
                                    jQuery("#sendmessage").html("");
                                    jQuery("#sendmessage1").addClass("error");
                                    jQuery("#sendmessage1").html("Kindly refresh page to send a message!");
                                    jQuery("#sendmessage2").html("");
                                    jQuery("#sendmessage3").html("");
                                    jQuery("#sendmessage4").html("");
                                } else if (msg == "notlogged") {
                                    jQuery("#favmessage").addClass("error");
                                    jQuery("#favmessage").html("kindly log in to be able to add lift to favourites!");
                                    jQuery("#favmessage2").html(" ");
                                    jQuery("#sendmessage").html("");
                                    jQuery("#sendmessage1").addClass("error");
                                    jQuery("#sendmessage1").html("Sending a message requires login!");
                                    jQuery("#sendmessage2").html("");
                                    jQuery("#sendmessage3").html("");
                                    jQuery("#sendmessage4").html("");                                    
                                // Else
                                } else {
                                    jQuery("#favmessage").html("Unexpected error occured!");
                                    jQuery("#favmessage").addClass("error");                                    
                                }
                                
                            }
                        }
                    });
                }
            }
        }
        // Function to send message
        function sendmessage(code, messagetitle, messagebody)
        {
            // Messages can be updated
            doUpdateMessage = true;
            
            // If code is null
            if (code == null) {
                // Remove existing stuff
                jQuery("#erroronsendmessage").html("Unfortunately there is a problem with that user");
                jQuery("#erroronsendmessage").removeClass("error");
                jQuery("#erroronsendmessage").removeClass("success");
                doUpdateMessage = false;
                                
            // Else Need to do Ajax Call
            } else if (messagetitle == null) {
                // Remove existing stuff
                jQuery("#erroronsendmessage").html("Title cannot be null");
                jQuery("#erroronsendmessage").removeClass("error");
                jQuery("#erroronsendmessage").removeClass("success");
                doUpdateMessage = false;            
            } else if (messagebody == null) {
                // Remove existing stuff
                jQuery("#erroronsendmessage").html("Title cannot be null");
                jQuery("#erroronsendmessage").removeClass("error");
                jQuery("#erroronsendmessage").removeClass("success");
                doUpdateMessage = false;            
            } else {
                
                // Check that existing code is not in use
                if (currentCode != code) {
                    
                    // Set message to checking
                    jQuery("#erroronsendmessage").removeClass("success");
                    jQuery("#erroronsendmessage").html("<span id=\"favusercheck\">' . addslashes($objIcon->show()) . ' Sending ...</span>");
                                     
                    // Set current Code
                    currentCode = code;
                    
                    // DO Ajax
                    jQuery.ajax({
                        type: "GET", 
                        url: sitepath, 
                        data: "module=liftclub&action=sendmessage&favusrid="+code+"&msgtitle="+messagetitle+"&msgbody="+messagebody, 
                        success: function(msg){                        
                            // Check if messages can be updated and code remains the same
                            if (doUpdateMessage == true && currentCode == code) {
                                
                                // IF code exists
                                if (msg == "ok") {
                                    jQuery("#erroronsendmessage").html("");
                                    jQuery("#sendmessage").html("");
                                    jQuery("#sendmessage1").html("Message sent successfully!");

                                    jQuery("#sendmessage2").html("");
                                    jQuery("#sendmessage3").html("");
                                    jQuery("#sendmessage4").html("");
                                    jQuery("#favmessage2").html("");
                                    jQuery("#erroronsendmessage").addClass("success");
                                    jQuery("#sendmessage1").addClass("success");
                                    jQuery("#erroronsendmessage").removeClass("error");
                                    jQuery("#input_msgtitle").val("");
                                    jQuery("#input_msgbody").val("");
                                } else if (msg == "notlogged") {
                                    jQuery("#erroronsendmessage").addClass("error");
                                    jQuery("#erroronsendmessage").html("kindly log in to be able to send the message!");
                                // Else
                                } else {
                                    jQuery("#erroronsendmessage").html("Unexpected error occured!");
                                    jQuery("#erroronsendmessage").addClass("error");
                                }
                                
                            }
                        }
                    });
                }
            }
        }
    </script>');
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText("mod_liftclub_viewdetails", 'liftclub', "View Member Details");
echo '<div style="padding:10px;">' . $header->show();
$required = '<span class="warning"> * ' . $this->objLanguage->languageText('word_required', 'system', 'Required') . '</span>';
$form = new form('viewdetails', $this->uri(array(
    'action' => 'liftclubhome',
    'id' => $id,
    'originid' => $originid,
    'destinyid' => $destinyid,
    'detailsid' => $detailsid
)));
$messages = array();
//Get userid
$thisuserid = $this->objUser->userId();
//Add to favourite if logged in
if (!empty($thisuserid)) {
    $addfav = new checkbox('addtofav', null, false);
    $favUsrId = new textinput('favusrid', null, 'hidden');
    $favUsrId->value = $this->getParam('liftuserid');
    $sysSiteRoot = $this->objConfig->getsiteRoot() . "index.php";
    $sitepathtitle = new textinput('sitepath', $sysSiteRoot, "hidden", 10);
    $table = $this->newObject('htmltable', 'htmlelements');
    if ($isFavourite == 1) {
        $table->startRow();
        $table->addCell("<br /><div id='favmessage2'><b>" . $this->objLanguage->languageText('mod_liftclub_addfavourite', 'liftclub', "Add to favourite") . "? " . $addfav->show() . " </b></div>" . $favUsrId->show() . $sitepathtitle->show() , 150, 'top', 'right');
        $table->addCell("<br /><div id='favmessage'> </div>", NULL, 'top', 'left');
        $table->endRow();
    } else {
        $table->startRow();
        $table->addCell("<br /><div id='favmessage2'><b> </b></div>" . $favUsrId->show() . $sitepathtitle->show() , 150, 'top', 'right');
        $table->addCell("<br /><div id='favmessage'> </div>", NULL, 'top', 'left');
        $table->endRow();
    }
    $form->addToForm($table->show());
    $form->addToForm('<br />');
    //Send a message
    $table = $this->newObject('htmltable', 'htmlelements');
    $messageTitle = new textinput('msgtitle', NULL, NULL);
    $messageBody = new textarea($name = 'msgbody', $value = '', $rows = 4, $cols = 50);
    $titleLabel = new label($this->objLanguage->languageText('mod_liftclub_messagetitle', 'liftclub', 'Title'));
    $bodyLabel = new label($this->objLanguage->languageText('mod_liftclub_messagebody', 'liftclub', 'Body'));
    $table->startRow();
    $table->addCell("<b><div id='sendmessage'>" . $titleLabel->show() . ": </div></b>", 150, NULL, 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell("<b><div id='sendmessage1'>" . $messageTitle->show() . "</div></b>");
    $table->endRow();
    $table->startRow();
    $table->addCell("<b><div id='sendmessage2'>" . $bodyLabel->show() . ": </div></b>", 150, 'top', 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell("<div id='sendmessage3'>" . $messageBody->show() . "</div>");
    $table->endRow();
    $button = new button('submitmessage', 'Send Message');
    $button->setId('button_submitmessage');
    $table->startRow();
    $table->addCell("&nbsp;", 150, 'top', 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell("<div id='sendmessage4'>" . $button->show() . "</div>");
    $table->endRow();
    $table->startRow();
    $table->addCell("&nbsp;", 150, 'top', 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell("<div id='erroronsendmessage'> </div>");
    $table->endRow();
    $fieldset = $this->newObject('fieldset', 'htmlelements');
    $fieldset->legend = $this->objLanguage->languageText('mod_liftclub_sendmessage', 'liftclub', 'Send a Message') . "?";
    $fieldset->contents = $table->show();
    $form->addToForm($fieldset->show());
    $form->addToForm('<br />');
}
//Add user info
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$usernameLabel = new label($this->objLanguage->languageText('word_username', 'system'));
$table->addCell("<b>" . $usernameLabel->show() . ": </b>", 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($register_username);
//, NULL, NULL, NULL, NULL, 'colspan="2"');
$table->endRow();
$table->startRow();
$needLabel = new label($this->objLanguage->languageText('phrase_iwanto', 'liftclub', 'I want to'));
$table->addCell("<b>" . $needLabel->show() . ": </b>", 150, 'top', 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($userneed . " - " . $needtype);
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_accountdetails', 'liftclub', 'Account Details');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add from (home or trip origin) details
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$streetnameLabel = new label($this->objLanguage->languageText('mod_liftclub_streetname', 'liftclub', "Street Name"));
$table->addCell("<b>" . $streetnameLabel->show() . ": </b>", 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($street_name);
$table->endRow();
$table->startRow();
$suburbLabel = new label($this->objLanguage->languageText('mod_liftclub_suburb', 'liftclub', "Suburb"));
$table->addCell("<b>" . $suburbLabel->show() . ": </b>", 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($suburborigin);
$table->endRow();
$table->startRow();
$citytownLabel = new label($this->objLanguage->languageText('mod_liftclub_citytown', 'liftclub', "City/Town"));
if ($citytownorigin !== null) {
    $townname = $this->objDBCities->listSingle($citytownorigin);
}
$table->addCell("<b>" . $citytownLabel->show() . ": </b>", 150, 'top', 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($townname[0]["city"]);
$table->endRow();
$provinceLabel = new label($this->objLanguage->languageText('mod_liftclub_province', 'liftclub', "Province"));
$table->startRow();
$table->addCell("<b>" . $provinceLabel->show() . ": </b>", 150, 'bottom', 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($province, NULL, 'bottom', 'left');
$table->endRow();
$table->startRow();
$neighbourLabel = new label($this->objLanguage->languageText('mod_liftclub_neighbour', 'liftclub', "Neighbour"));
$table->addCell("<b>" . $neighbourLabel->show() . " :</b>", 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($neighbourorigin);
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_from', 'liftclub', 'From (Home or Trip Origin)');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add to (home or trip destination) details
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$institutionLabel = new label($this->objLanguage->languageText("mod_liftclub_institution", "liftclub", "Institution"));
$table->addCell("<b>" . $institutionLabel->show() . " :</b>", 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($destinstitution);
$table->endRow();
$table->startRow();
$streetnameLabel2 = new label($this->objLanguage->languageText('mod_liftclub_streetname', 'liftclub', "Street Name"));
$table->addCell("<b>" . $streetnameLabel2->show() . "</b>", 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($deststreetname);
$table->endRow();
$table->startRow();
$suburbLabel2 = new label($this->objLanguage->languageText('mod_liftclub_suburb', 'liftclub', "Suburb"));
$table->addCell("<b>" . $suburbLabel2->show() . "</b>", 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($destsuburb);
$table->endRow();
$table->startRow();
$citytownLabel2 = new label($this->objLanguage->languageText('mod_liftclub_citytown', 'liftclub', "City/Town"));
if ($destcity !== null) {
    $townname2 = $this->objDBCities->listSingle($destcity);
}
$table->addCell("<b>" . $citytownLabel2->show() . " :</b>", 150, 'top', 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($townname2[0]["city"]);
$table->endRow();
$provinceLabel2 = new label($this->objLanguage->languageText('mod_liftclub_province', 'liftclub', "Province"));
$table->startRow();
$table->addCell("<b>" . $provinceLabel2->show() . " :</b>", 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($destprovince);
$table->endRow();
$table->startRow();
$neighbourLabel2 = new label($this->objLanguage->languageText('mod_liftclub_neighbour', 'liftclub', "Neighbour"));
$table->addCell("<b>" . $neighbourLabel2->show() . "</b>", 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($destneighbour);
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_to', 'liftclub', 'To (Home or Trip Destination)');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add Trip details
$table = $this->newObject('htmltable', 'htmlelements');
if ($this->getSession('needtype') == 'Trip') {
    $table->startRow();
    $table->addCell($this->objLanguage->languageText('mod_liftclub_daterequired', 'liftclub', "Date Required") , 150, 'top', 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell($tripdaterequired, null, 'bottom', 'left');
    $table->endRow();
    $table->addCell('&nbsp;', 150, 'top', 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell('&nbsp;');
    $table->endRow();
} else {
    $table->startRow();
    $traveltimes = new textinput('traveltimes');
    $traveltimesLabel = new label($this->objLanguage->languageText('mod_liftclub_traveltimes', 'liftclub', "Travel Times") . '&nbsp;', 'input_traveltimes');
    $traveltimes->value = $triptimes;
    if ($mode == 'addfixup') {
        $traveltimes->value = $this->getParam('traveltimes');
        if ($this->getParam('traveltimes') == '') {
            $messages[] = $this->objLanguage->languageText('entertraveltimes', 'liftclub', 'Please Specify the Travel Times');
        }
    }
    $table->addCell("<b>" . $traveltimesLabel->show() . " :</b>", 150, NULL, 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell($triptimes);
    $table->endRow();
    $table->startRow();
    $monday = new checkbox('monday', null, false);
    if ($daymon == 'Y') $monday = new checkbox('monday', null, true);
    $mondayLabel = new label($this->objLanguage->languageText('mod_liftclub_monday', 'liftclub', "Monday") . '&nbsp;', 'input_monday');
    $tuesday = new checkbox('tuesday', null, false);
    if ($daytues == 'Y') $tuesday = new checkbox('tuesday', null, true);
    $tuesdayLabel = new label($this->objLanguage->languageText('mod_liftclub_tuesday', 'liftclub', "Tuesday") . '&nbsp;', 'input_tuesday');
    $wednesday = new checkbox('wednesday', null, false);
    if ($daywednes == 'Y') $wednesday = new checkbox('wednesday', null, true);
    $wednesdayLabel = new label($this->objLanguage->languageText('mod_liftclub_wednesday', 'liftclub', "Wednesday") . '&nbsp;', 'input_wednesday');
    $thursday = new checkbox('thursday', null, false);
    if ($daythurs == 'Y') $thursday = new checkbox('thursday', null, true);
    $thursdayLabel = new label($this->objLanguage->languageText('mod_liftclub_thursday', 'liftclub', "Thursday") . '&nbsp;', 'input_thursday');
    $friday = new checkbox('friday', null, false);
    if ($dayfri == 'Y') $friday = new checkbox('friday', null, true);
    $fridayLabel = new label($this->objLanguage->languageText('mod_liftclub_friday', 'liftclub', "Friday") . '&nbsp;', 'input_friday');
    $saturday = new checkbox('saturday', null, false);
    if ($daysatur == 'Y') $saturday = new checkbox('saturday', null, true);
    $saturdayLabel = new label($this->objLanguage->languageText('mod_liftclub_saturday', 'liftclub', "Saturday") . '&nbsp;', 'input_saturday');
    $sunday = new checkbox('sunday', null, false);
    if ($daysun == 'Y') $sunday = new checkbox('sunday', null, true);
    $sundayLabel = new label($this->objLanguage->languageText('mod_liftclub_sunday', 'liftclub', "Sunday") . '&nbsp;', 'input_sunday');
    $table->addCell("<b>" . $this->objLanguage->languageText('mod_liftclub_days', 'liftclub', "Days") . " :</b>", 150, NULL, 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell("<i>" . $monday->show() . $mondayLabel->show() . " " . $tuesday->show() . $tuesdayLabel->show() . " " . $wednesday->show() . $wednesdayLabel->show() . " " . $thursday->show() . $thursdayLabel->show() . " " . $friday->show() . $fridayLabel->show() . " " . $saturday->show() . $saturdayLabel->show() . " " . $sunday->show() . $sundayLabel->show() . " " . "</i>");
    $table->endRow();
    $table->startRow();
    $table->addCell("<b>" . $this->objLanguage->languageText('mod_liftclub_daysvary', 'liftclub', 'Days may vary') . '&nbsp;:</b>', 150, NULL, 'right');
    $table->addCell('&nbsp;', 5);
    if ($varydays == 'Y') {
        $table->addCell($this->objLanguage->languageText('word_yes', 'system'));
    } else {
        $table->addCell($this->objLanguage->languageText('word_no', 'system'));
    }
    $table->endRow();
}
$table->startRow();
$table->addCell("<b>" . $this->objLanguage->languageText('mod_liftclub_smoke', 'liftclub', 'Allow smoking?') . '&nbsp;:</b>', 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
if ($tripsmoke == 'Y') {
    $table->addCell($this->objLanguage->languageText('word_yes', 'system'));
} else {
    $table->addCell($this->objLanguage->languageText('word_no', 'system'));
}
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_tripdetails', 'liftclub', 'Trip Details');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add additional Information
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$additionalinfoLabel = new label($this->objLanguage->languageText('mod_liftclub_additionalinfo', 'liftclub', "Additional Information") . '&nbsp;');
$table->addCell("<b>" . $additionalinfoLabel->show() . " :</b>", 150, "top", 'right');
$table->addCell('&nbsp;', 5);
$table->addCell("<p>" . $tripadditionalinfo . "</p>");
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('mod_liftclub_additionalinfo', 'liftclub', 'Additional Information');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
echo $form->show();
echo '</div>';
?>
