<script type="text/javascript">
//<![CDATA[
function init () {
    $('input_redraw').onclick = function () {
        redraw();
    }
}

function redraw () {
 var url = 'index.php';
 var pars = 'module=security&action=generatenewcaptcha';
 var myAjax = new Ajax.Request(url,{method:'get',parameters:pars, onComplete:showResponse});
}

function showLoad () {
 $('load').style.display = 'block';
}

function showResponse (originalRequest) {
 var newData = originalRequest.responseText;
 $('captchaDiv').innerHTML = newData;
}
//]]>
</script>
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
$this->appendArrayVar('headerParams', '
	<script type="text/javascript">
		var uri = "' . str_replace('&amp;', '&', $this->uri(array(
    'module' => 'liftclub',
    'action' => 'jsongetcities'
))) . '"; 
 </script>');
//Ext stuff
$ext = '<link rel="stylesheet" href="' . $this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css', 'htmlelements') . '" type="text/css" />';
$ext.= $this->getJavaScriptFile('ext-3.0-rc2/adapter/ext/ext-base.js', 'htmlelements');
$ext.= $this->getJavaScriptFile('ext-3.0-rc2/ext-all.js', 'htmlelements');
$ext.= $this->getJavaScriptFile('extjsgetcity.js', 'liftclub');
$ext.= $this->getJavaScriptFile('extjsgetcityb.js', 'liftclub');
$ext.= $this->getJavaScriptFile('forum-search.js', 'rimfhe');
$ext.= '<link rel="stylesheet" href="' . $this->getResourceUri('combos.css', 'liftclub') . '"type="text/css" />';
$ext.= $this->getJavaScriptFile('ext-3.0-rc2/examples/shared/examples.js', 'htmlelements');
$this->appendArrayVar('headerParams', $ext);
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText("mod_liftclub_tripinfo", 'liftclub', "Add/Modify Trip Info");
echo '<div style="padding:10px;">' . $header->show();
$required = '<span class="warning"> * ' . $this->objLanguage->languageText('word_required', 'system', 'Required') . '</span>';
$str = $this->objLanguage->languageText('mod_liftclub_firstneedtoregister', 'liftclub', 'In order to be able to access [[SITENAME]], you first need to register');
$str = str_replace('[[SITENAME]]', $this->objConfig->getSitename() , $str);
echo '<p>' . $str . '<br />';
echo $this->objLanguage->languageText('mod_liftclub_pleaseenterdetails', 'liftclub', 'Please enter your details, email address and desired user name in the form below.') . '</p>';
$id = $this->getParam('id');
$originid = $this->getParam('originid');
$destinyid = $this->getParam('destinyid');
$detailsid = $this->getParam('detailsid');
$needtype = $this->getParam('needtype');
$userneed = $this->getParam('userneed');
$form = new form('register', $this->uri(array(
    'action' => 'updateregister',
    'id' => $id,
    'originid' => $originid,
    'destinyid' => $destinyid,
    'detailsid' => $detailsid,
    'userneed' => $userneed,
    'needtype' => $needtype
)));
$messages = array();
$table = $this->newObject('htmltable', 'htmlelements');
//Add from (home or trip origin) details
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$streetname = new textinput('street_name');
$streetname->extra = "maxlength=350";
$streetnameLabel = new label($this->objLanguage->languageText('mod_liftclub_streetname', 'liftclub', "Street Name") . '&nbsp;', 'input_street_name');
if ($mode == 'addfixup') {
    $streetname->value = $this->getParam('street_name');
    if ($this->getParam('street_name') == '') {
        $messages[] = $this->objLanguage->languageText('enterstreetname', 'system', 'Please enter Street Name');
    }
} else {
    $streetname->value = $street_name;
}
$table->addCell($streetnameLabel->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($streetname->show() . $required);
$table->endRow();
$table->startRow();
$suburb = new textinput('suburb');
$suburb->extra = "maxlength=350";
$suburbLabel = new label($this->objLanguage->languageText('mod_liftclub_suburb', 'liftclub', "Suburb") . '&nbsp;', 'input_suburb');
if ($mode == 'addfixup') {
    $suburb->value = $this->getParam('suburb');
    if ($this->getParam('suburb') == '') {
        $messages[] = $this->objLanguage->languageText('entersuburb', 'system', 'Please enter Suburb');
    }
} else {
    $suburb->value = $suburborigin;
}
$table->addCell($suburbLabel->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($suburb->show() . $required);
$table->endRow();
$table->startRow();
$citytown = new textinput('citytown', null, 'hidden');
$citytown->size = 10;
$citytownb = new textinput('citytownb');
$citytownb->size = 40;
$citytowna = new textinput('citytowna');
$citytowna->size = 41;
$citytowna->extra = 'disabled = "true"';
$citytownLabel = new label($this->objLanguage->languageText('mod_liftclub_citytown', 'liftclub', "City/Town") . '&nbsp;', 'input_citytownb');
if ($mode == 'addfixup') {
    $citytown->value = $this->getParam('citytown');
    $townname = $this->objDBCities->listSingle($this->getParam('citytown'));
    $citytowna->value = $townname[0]["city"];
    if ($this->getParam('citytown') == '' || strlen($this->getParam('citytown')) < 1) {
        $messages[] = $this->objLanguage->languageText('entercitytown', 'system', 'Please enter City/Town');
    }
} elseif ($citytownorigin !== null) {
    $townname = $this->objDBCities->listSingle($citytownorigin);
    $citytown->value = $citytownorigin;
    $citytowna->value = $townname[0]["city"];
}
$table->addCell($citytownLabel->show() , 150, 'top', 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($citytownb->show() . $citytowna->show() . $citytown->show() . $required);
$table->endRow();
$provinceDropdown = new dropdown('province');
$provinceLabel = new label($this->objLanguage->languageText('mod_liftclub_province', 'liftclub', "Province") . '&nbsp;', 'input_province');
$provinces = array(
    "province_easterncape",
    "province_freestate",
    "province_guateng",
    "province_kwazulunatal",
    "province_mpumalanga",
    "province_limpopoprovince",
    "province_northwestprovince",
    "province_notherncape",
    "province_westerncape"
);
foreach($provinces as $myprovince) {
    $_province = trim($this->objLanguage->languageText($myprovince, 'liftclub'));
    $provinceDropdown->addOption($_province, $_province);
}
if ($mode == 'addfixup') {
    $provinceDropdown->setSelected($this->getParam('province'));
} else {
    if ($province !== null) $provinceDropdown->setSelected($province);
}
$table->startRow();
$table->addCell($provinceLabel->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($provinceDropdown->show());
$table->endRow();
$table->startRow();
$neighbour = new textinput('neighbour');
$neighbour->extra = "maxlength=350";
$neighbourLabel = new label($this->objLanguage->languageText('mod_liftclub_neighbour', 'liftclub', "Neighbour") . '&nbsp;', 'input_neighbour');
if ($mode == 'addfixup') {
    $neighbour->value = $this->getParam('neighbour');
} else {
    $neighbour->value = $neighbourorigin;
}
$table->addCell($neighbourLabel->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($neighbour->show());
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_from', 'liftclub', 'From (Home or Trip Origin)');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add to (home or trip destination) details
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$institution = new textinput('institution');
$institution->extra = "maxlength=350";
$institutionLabel = new label($this->objLanguage->languageText("mod_liftclub_institution", "liftclub", "Institution") . '&nbsp;:', 'input_institution');
if ($mode == 'addfixup') {
    $institution->value = $this->getParam('institution');
} else {
    $institution->value = $destinstitution;
}
$table->addCell($institutionLabel->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($institution->show());
$table->endRow();
$table->startRow();
$streetname2 = new textinput('street_name2');
$streetname2->extra = "maxlength=350";
$streetnameLabel2 = new label($this->objLanguage->languageText('mod_liftclub_streetname', 'liftclub', "Street Name") . '&nbsp;', 'input_street_name2');
if ($mode == 'addfixup') {
    $streetname2->value = $this->getParam('street_name2');
    if ($this->getParam('street_name2') == '') {
        $messages[] = $this->objLanguage->languageText('enterstreetname', 'system', 'Please enter Street Name');
    }
} else {
    $streetname2->value = $deststreetname;
}
$table->addCell($streetnameLabel2->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($streetname2->show() . $required);
$table->endRow();
$table->startRow();
$suburb2 = new textinput('suburb2');
$suburb2->extra = "maxlength=350";
$suburbLabel2 = new label($this->objLanguage->languageText('mod_liftclub_suburb', 'liftclub', "Suburb") . '&nbsp;', 'input_suburb2');
if ($mode == 'addfixup') {
    $suburb2->value = $this->getParam('suburb2');
    if ($this->getParam('suburb2') == '') {
        $messages[] = $this->objLanguage->languageText('entersuburb', 'system', 'Please enter Suburb');
    }
} else {
    $suburb2->value = $destsuburb;
}
$table->addCell($suburbLabel2->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($suburb2->show() . $required);
$table->endRow();
$table->startRow();
$citytown2 = new textinput('citytown2', null, 'hidden');
$citytown2->extra = "maxlength=350";
$citytown2a = new textinput('citytown2a');
$citytown2a->size = 41;
$citytown2a->extra = 'maxlength=350 disabled = "true"';
$citytown2b = new textinput('citytown2b');
$citytown2b->size = 40;
$citytown2b->extra = 'maxlength=350';
$citytownLabel2 = new label($this->objLanguage->languageText('mod_liftclub_citytown', 'liftclub', "City/Town") . '&nbsp;', 'input_citytown2a');
if ($mode == 'addfixup') {
    $citytown2->value = $this->getParam('citytown2');
    $townname2 = $this->objDBCities->listSingle($this->getParam('citytown2'));
    $citytown2a->value = $townname2[0]["city"];
    if ($this->getParam('citytown2') == '') {
        $messages[] = $this->objLanguage->languageText('entercitytown', 'system', 'Please enter City/Town');
    }
} elseif ($destcity !== null) {
    $citytown2->value = $destcity;
    $townname2 = $this->objDBCities->listSingle($destcity);
    $citytown2a->value = $townname2[0]["city"];
}
$table->addCell($citytownLabel2->show() , 150, 'top', 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($citytown2b->show() . $citytown2a->show() . $citytown2->show() . $required);
$table->endRow();
$provinceDropdown2 = new dropdown('province2');
$provinceLabel2 = new label($this->objLanguage->languageText('mod_liftclub_province', 'liftclub', "Province") . '&nbsp;', 'input_province2');
foreach($provinces as $myprovince) {
    $_province = trim($this->objLanguage->languageText($myprovince, 'liftclub'));
    $provinceDropdown2->addOption($_province, $_province);
}
if ($mode == 'addfixup') {
    $provinceDropdown2->setSelected($this->getParam('province2'));
} else {
    $provinceDropdown2->setSelected($destprovince);
}
$table->startRow();
$table->addCell($provinceLabel2->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($provinceDropdown2->show());
$table->endRow();
$table->startRow();
$neighbour2 = new textinput('neighbour2');
$neighbour2->extra = "maxlength=350";
$neighbourLabel2 = new label($this->objLanguage->languageText('mod_liftclub_neighbour', 'liftclub', "Neighbour") . '&nbsp;', 'input_neighbour2');
if ($mode == 'addfixup') {
    $neighbour2->value = $this->getParam('neighbour2');
} else {
    $neighbour2->value = $destneighbour;
}
$table->addCell($neighbourLabel2->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($neighbour2->show());
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_to', 'liftclub', 'To (Home or Trip Destination)');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add Trip details
$table = $this->newObject('htmltable', 'htmlelements');
if ($userneed == 'Trip') {
    $table->startRow();
    $dateRequired = $this->newObject('datepicker', 'htmlelements');
    $dateRequired->setName('daterequired');
    $dateRequired->setDateFormat('YYYY-MM-DD');
    if ($tripdaterequired !== null) $dateRequired->setDefaultDate($tripdaterequired);
    if ($mode == 'addfixup') {
        $dateRequired->setDefaultDate($this->getParam('daterequired'));
        if ($this->getParam('daterequired') == '') {
            $messages[] = $this->objLanguage->languageText('enterdaterequired', 'liftclub', 'Please Specify the Date Required');
        }
    }
    $table->addCell($this->objLanguage->languageText('mod_liftclub_daterequired', 'liftclub', "Date Required") , 150, 'top', 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell($dateRequired->show() . $required, null, 'bottom', 'left');
    $table->endRow();
    $table->addCell('&nbsp;', 150, 'top', 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell('&nbsp;');
    $table->endRow();
} else {
    $table->startRow();
    //$traveltimes = new textinput('traveltimes');
    if (!empty($triptimes)) {
        $triptimesA = explode(":", $triptimes);
        if (count($triptimesA) == 2) {
            $hval = $triptimesA[0];
            $triptimesB = explode(" ", $triptimesA[1]);
            $mval = $triptimesB[0];
            $pval = $triptimesB[1];
        } else {
            $hval = "";
            $mval = "";
            $pval = "";
        }
    } else {
        $hval = "";
        $mval = "";
        $pval = "";
    }
    $hourMin = $this->objLiftSearch->hourmin($hval, $mval, $pval);
    $traveltimesLabel = new label($this->objLanguage->languageText('mod_liftclub_traveltimes', 'liftclub', "Travel Times") . '&nbsp;', 'input_traveltimes');
    //$traveltimes->value = $triptimes;
    if ($mode == 'addfixup') {
        $hval = $this->getParam('hour');
        $mval = $this->getParam('minute');
        $pval = $this->getParam('pm');
        $hourMin = $this->objLiftSearch->hourmin($hval, $mval, $pval);
    }
    $table->addCell($traveltimesLabel->show() , 150, NULL, 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell($hourMin . $required);
    $table->endRow();
    $table->startRow();
    if ($mode == 'addfixup') {
        if ($this->getParam('monday') == 'Y') {
            $monday = new checkbox('monday', null, true);
        } else {
            $monday = new checkbox('monday', null, false);
        }
        $monday->SetValue('Y');
    } else {
        $monday = new checkbox('monday', null, false);
        if ($daymon == 'Y') $monday = new checkbox('monday', null, true);
        $monday->SetValue('Y');
    }
    $mondayLabel = new label($this->objLanguage->languageText('mod_liftclub_monday', 'liftclub', "Monday") . '&nbsp;', 'input_monday');
    if ($mode == 'addfixup') {
        if ($this->getParam('tuesday') == 'Y') {
            $tuesday = new checkbox('tuesday', null, true);
        } else {
            $tuesday = new checkbox('tuesday', null, false);
        }
        $tuesday->SetValue('Y');
    } else {
        $tuesday = new checkbox('tuesday', null, false);
        if ($daytues == 'Y') $tuesday = new checkbox('tuesday', null, true);
        $tuesday->SetValue('Y');
    }
    $tuesdayLabel = new label($this->objLanguage->languageText('mod_liftclub_tuesday', 'liftclub', "Tuesday") . '&nbsp;', 'input_tuesday');
    if ($mode == 'addfixup') {
        if ($this->getParam('wednesday') == 'Y') {
            $wednesday = new checkbox('wednesday', null, true);
        } else {
            $wednesday = new checkbox('wednesday', null, false);
        }
        $wednesday->SetValue('Y');
    } else {
        $wednesday = new checkbox('wednesday', null, false);
        if ($daywednes == 'Y') $wednesday = new checkbox('wednesday', null, true);
        $wednesday->SetValue('Y');
    }
    $wednesdayLabel = new label($this->objLanguage->languageText('mod_liftclub_wednesday', 'liftclub', "Wednesday") . '&nbsp;', 'input_wednesday');
    if ($mode == 'addfixup') {
        if ($this->getParam('thursday') == 'Y') {
            $thursday = new checkbox('thursday', null, true);
        } else {
            $thursday = new checkbox('thursday', null, false);
        }
        $thursday->SetValue('Y');
    } else {
        $thursday = new checkbox('thursday', null, false);
        if ($daythurs == 'Y') $thursday = new checkbox('thursday', null, true);
        $thursday->SetValue('Y');
    }
    $thursdayLabel = new label($this->objLanguage->languageText('mod_liftclub_thursday', 'liftclub', "Thursday") . '&nbsp;', 'input_thursday');
    if ($mode == 'addfixup') {
        if ($this->getParam('friday') == 'Y') {
            $friday = new checkbox('friday', null, true);
        } else {
            $friday = new checkbox('friday', null, false);
        }
        $friday->SetValue('Y');
    } else {
        $friday = new checkbox('friday', null, false);
        if ($dayfri == 'Y') $friday = new checkbox('friday', null, true);
        $friday->SetValue('Y');
    }
    $fridayLabel = new label($this->objLanguage->languageText('mod_liftclub_friday', 'liftclub', "Friday") . '&nbsp;', 'input_friday');
    if ($mode == 'addfixup') {
        if ($this->getParam('saturday') == 'Y') {
            $saturday = new checkbox('saturday', null, true);
        } else {
            $saturday = new checkbox('saturday', null, false);
        }
        $saturday->SetValue('Y');
    } else {
        $saturday = new checkbox('saturday', null, false);
        if ($daysatur == 'Y') $saturday = new checkbox('saturday', null, true);
        $saturday->SetValue('Y');
    }
    $saturdayLabel = new label($this->objLanguage->languageText('mod_liftclub_saturday', 'liftclub', "Saturday") . '&nbsp;', 'input_saturday');
    if ($mode == 'addfixup') {
        if ($this->getParam('sunday') == 'Y') {
            $sunday = new checkbox('sunday', null, true);
        } else {
            $sunday = new checkbox('sunday', null, false);
        }
        $sunday->SetValue('Y');
    } else {
        $sunday = new checkbox('sunday', null, false);
        if ($daysun == 'Y') $sunday = new checkbox('sunday', null, true);
        $sunday->SetValue('Y');
    }
    $sundayLabel = new label($this->objLanguage->languageText('mod_liftclub_sunday', 'liftclub', "Sunday") . '&nbsp;', 'input_sunday');
    $table->addCell($this->objLanguage->languageText('mod_liftclub_days', 'liftclub', "Days") , 150, NULL, 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell("<i>" . $monday->show() . $mondayLabel->show() . " " . $tuesday->show() . $tuesdayLabel->show() . " " . $wednesday->show() . $wednesdayLabel->show() . " " . $thursday->show() . $thursdayLabel->show() . " " . $friday->show() . $fridayLabel->show() . " " . $saturday->show() . $saturdayLabel->show() . " " . $sunday->show() . $sundayLabel->show() . " " . "</i>" . $required);
    $table->endRow();
    $daysvaryRadio = new radio('daysvary');
    $daysvaryRadio->addOption('Y', $this->objLanguage->languageText('word_yes', 'system'));
    $daysvaryRadio->addOption('N', $this->objLanguage->languageText('word_no', 'system'));
    $daysvaryRadio->setBreakSpace(' &nbsp; ');
    // && empty($this->getParam('tuesday')) && empty($this->getParam('wednesday')) && empty($this->getParam('thursday')) && empty($this->getParam('friday')) && empty($this->getParam('saturday')) && empty($this->getParam('sunday'))
    if ($mode == 'addfixup') {
        if ($this->getParam('monday') == '' && $this->getParam('tuesday') == '' && $this->getParam('wednesday') == '' && $this->getParam('thursday') == "" && $this->getParam('friday') == "" && $this->getParam('saturday') == "" && $this->getParam('sunday') == "") {
            $messages[] = $this->objLanguage->languageText('enteroneday', 'liftclub', 'Please Specify at least one day of the week');
        }
        $daysvaryRadio->setSelected($this->getParam('daysvary'));
    } else {
        $daysvaryRadio->setSelected('N');
        if ($varydays !== null) $daysvaryRadio->setSelected($varydays);
    }
    $table->startRow();
    $table->addCell($this->objLanguage->languageText('mod_liftclub_daysvary', 'liftclub', 'Days may vary') . '&nbsp;', 150, NULL, 'right');
    $table->addCell('&nbsp;', 5);
    $table->addCell($daysvaryRadio->show());
    $table->endRow();
}
$smokeRadio = new radio('smoke');
$smokeRadio->addOption('Y', $this->objLanguage->languageText('word_yes', 'system'));
$smokeRadio->addOption('N', $this->objLanguage->languageText('word_no', 'system'));
$smokeRadio->setBreakSpace(' &nbsp; ');
if ($mode == 'addfixup') {
    $smokeRadio->setSelected($this->getParam('smoke'));
} else {
    $smokeRadio->setSelected('N');
    if ($tripsmoke !== null) $smokeRadio->setSelected($tripsmoke);
}
$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_liftclub_smoke', 'liftclub', 'Allow smoking?') . '&nbsp;', 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($smokeRadio->show());
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_tripdetails', 'liftclub', 'Trip Details');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add additional Information
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$additionalinfo = new textarea('additionalinfo');
$additionalinfoLabel = new label($this->objLanguage->languageText('mod_liftclub_additionalinfo', 'liftclub', "Additional Information") . '&nbsp;', 'input_additionalinfo');
if ($mode == 'addfixup') {
    $additionalinfo->value = $this->getParam('additionalinfo');
} else {
    $additionalinfo->value = $tripadditionalinfo;
}
$table->addCell($additionalinfoLabel->show() , 150, "top", 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($additionalinfo->show());
$table->endRow();
$acceptoffersRadio = new radio('acceptoffers');
$acceptoffersRadio->addOption('Y', $this->objLanguage->languageText('word_yes', 'system'));
$acceptoffersRadio->addOption('N', $this->objLanguage->languageText('word_no', 'system'));
$acceptoffersRadio->setBreakSpace(' &nbsp; ');
if ($mode == 'addfixup') {
    $acceptoffersRadio->setSelected($this->getParam('acceptoffers'));
} else {
    $acceptoffersRadio->setSelected('N');
    if ($tripacceptoffers == 'Y') $acceptoffersRadio->setSelected('Y');
}
$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_liftclub_acceptoffers', 'liftclub', 'Could we send you special offers and newsletters?') . '&nbsp;', 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($acceptoffersRadio->show() , "", "bottom");
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('mod_liftclub_additionalinfo', 'liftclub', 'Additional Information');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add notifications
$table = $this->newObject('htmltable', 'htmlelements');
$notificationsRadio = new radio('notifications');
$notificationsRadio->addOption('Y', $this->objLanguage->languageText('word_yes', 'system'));
$notificationsRadio->addOption('N', $this->objLanguage->languageText('word_no', 'system'));
$notificationsRadio->setBreakSpace(' &nbsp; ');
if ($mode == 'addfixup') {
    $notificationsRadio->setSelected($this->getParam('notifications'));
} else {
    $notificationsRadio->setSelected('N');
    if ($tripemailnotifications == 'Y') $notificationsRadio->setSelected($tripemailnotifications);
}
$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_liftclub_notifications', 'liftclub', 'Receive notifications via email when another member sends you a message?') . '&nbsp;', 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($notificationsRadio->show() , "", "bottom");
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('mod_liftclub_accountsettings', 'liftclub', 'Account Settings');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add additional Information
$table = $this->newObject('htmltable', 'htmlelements');
$safetyprivterms = new checkbox('safetyterms', Null, false);
if ($mode == 'addfixup') {
    $safetyprivterms->value = $this->getParam('safetyterms');
    if ($this->getParam('safetyterms') == '') {
        $messages[] = $this->objLanguage->languageText('entersafetyprivterms', 'system', 'Kindly read the Phrase Safety and Privacy Terms and accept by checking the required field if you agree');
    }
}
$table->startRow();
$table->addCell('&nbsp;&nbsp;&nbsp;'. $safetyprivterms->show() . '&nbsp;&nbsp;&nbsp;' . $this->objLanguage->languageText('mod_liftclub_iread', 'liftclub', 'I have read and agree with your') . '&nbsp;' . $this->objLanguage->languageText('mod_liftclub_safeprivterms', 'liftclub', 'Safety and Privacy Terms') . '&nbsp;' . $this->objLanguage->languageText('mod_liftclub_ofuse', 'liftclub', 'of use agreement') . '&nbsp;' .  $required, 150, Null, 'left', Null, 'colspan=2');
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('mod_liftclub_safeprivterms', 'liftclub', 'Safety and Privacy Terms');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add form captcha
$objCaptcha = $this->getObject('captcha', 'utilities');
$captcha = new textinput('request_captcha');
$captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request') , 'input_request_captcha');
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = 'Verify Image';
$fieldset->contents = stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.')) . '<br /><div id="captchaDiv">' . $objCaptcha->show() . '</div>' . $captcha->show() . $required . '  <a href="javascript:redraw();">' . $this->objLanguage->languageText('word_redraw', 'security', 'Redraw') . '</a>';
$form->addToForm($fieldset->show());
$button = new button('submitform', 'Complete Registration');
$button->setToSubmit();
$form->addToForm('<p align="center"><br />' . $button->show() . '</p><br/ ><br/ >');
if ($mode == 'addfixup') {
    foreach($problems as $problem) {
        $messages[] = $this->explainProblemsInfo($problem);
    }
}
if ($mode == 'addfixup' && count($messages) > 0) {
    echo '<ul><li><span class="error">' . $this->objLanguage->languageText('mod_userdetails_infonotsavedduetoerrors', 'userdetails') . '</span>';
    echo '<ul>';
    foreach($messages as $message) {
        if ($message != '') {
            echo '<li class="error">' . $message . '</li>';
        }
    }
    echo '</ul></li></ul>';
}
echo $form->show();
echo '</div>';
?>
