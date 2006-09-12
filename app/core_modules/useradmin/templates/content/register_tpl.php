<script Language="JavaScript" type="text/javascript">
<!--
function invalidString(s,type){
  if (type == "name" || type == "surname"){
    invalidChar = "@,#,$,%,^,&,*,+,?,<,>,~,`,1,2,3,4,5,6,7,8,9,0";
    count = 23
  }
  else {
    invalidChar = "@,#,$,%,^,&,*,+,?,<,>,~,`,";
    count = 13
  }
  invalidArray = invalidChar.split(",");
  for (i = 0; i < count; i++)  {
    if (s.indexOf(invalidArray[i]) != -1)
    {
     return (false);
    }
  }
  return (true);
}

function isEmail(str) {
  // are regular expressions supported?
  var supported = 0;
  if (window.RegExp) {
    var tempStr = "a";
    var tempReg = new RegExp(tempStr);
    if (tempReg.test(tempStr)) supported = 1;
  }
  if (!supported)
    return (str.indexOf(".") > 2) && (str.indexOf("@") > 0);
  var r1 = new RegExp("(@.*@)|(\\.\\.)|(@\\.)|(^\\.)");
  var r2 = new RegExp("^.+\\@(\\[?)[a-zA-Z0-9\\-\\.]+\\.([a-zA-Z]{2,3}|[0-9]{1,3})(\\]?)$");
  return (!r1.test(str) && r2.test(str));
}

function ClearForm() {
  document.Form1.userId.value = " ";
  document.Form1.title.selectedIndex = 0;
  document.Form1.surname.value = " ";
  document.Form1.firstname.value = " ";
  document.Form1.username.value = " ";
  document.Form1.email.value = " ";
  document.Form1.email2.value = " ";
  document.Form1.department.selectedIndex = 0;
}

function Validator(theForm) {
  if (theForm.userId.value == "")
    {
        alert('<?php echo $objLanguage->languageText('instruction_entergenerateid','useradmin'); ?>');
        theForm.userId.value = "";
        theForm.userId.focus();
        return (false);
    }
  if (theForm.userId.value.length < 5)
    {
        alert('<?php echo $objLanguage->languageText('instruction_enteratleast5digits','useradmin'); ?>');
        theForm.userId.value = "";
        theForm.userId.focus();
        return (false);
    }
  if (theForm.userId.value.length > 15)
    {
        alert('<?php echo $objLanguage->languageText('instruction_enteratmost9digits','useradmin'); ?>');
        theForm.userId.value = ""
        theForm.userId.focus();
        return (false);
    }
  var checkOK = "0123456789-";
  var checkStr = theForm.userId.value;
  var allValid = true;
  var decPoints = 0;
  var allNum = "";
  for (i = 0;  i < checkStr.length;  i++) {
        ch = checkStr.charAt(i);
        for (j = 0;  j < checkOK.length;  j++)
            if (ch == checkOK.charAt(j))
            break;
            if (j == checkOK.length)
            {
                allValid = false;
                break;
            }
            allNum += ch;
        }
  if (!allValid) {
        alert('<?php echo str_replace('chapter number','userId',$objLanguage->languageText('alert_enteronlydigit','useradmin')); ?>');
        theForm.userId.value = "";
        theForm.userId.focus();
        return (false);
    }
  if (theForm.title.selectedIndex == 0) {
        alert('<?php echo $objLanguage->languageText('alert_titlenotvalidoption','useradmin'); ?>');
        theForm.title.value = "";
        theForm.title.focus();
        return (false);
    }
  if (theForm.firstname.value == "") {
        alert('<?php echo $objLanguage->languageText('instruction_enterfirstname','useradmin'); ?>');
        theForm.firstname.value = "";
        theForm.firstname.focus();
        return (false);
    }
  if (invalidString(theForm.firstname.value,"name") == false) {
        alert('Please make sure there are no illegal characters in the Name field.');
        theForm.firstname.value = "";
        theForm.firstname.focus();
        return (false);
    }
  if (theForm.surname.value == "") {
        alert('<?php echo $objLanguage->languageText('instruction_entersurname','useradmin'); ?>');
        theForm.surname.value = "";
        theForm.surname.focus();
        return (false);
    }
  if (invalidString(theForm.surname.value,"surname") == false) {
        alert('Please make sure there are no illegal characters in the Surname field.');
        theForm.surname.value = "";
        theForm.surname.focus();
        return (false);
    }

  if (theForm.username.value == "") {
        alert('<?php echo $objLanguage->languageText('instruction_entervalidusername','useradmin'); ?>');
        theForm.username.value = "";
        theForm.username.focus();
        return (false);
    }
  if (invalidString(theForm.username.value,"username") == false) {
        alert('Please make sure there are no illegal characters in the Username field.');
        theForm.username.value = "";
        theForm.username.focus();
        return (false);
    }
  if (theForm.email.value == "")
    {
        alert('<?php echo $objLanguage->languageText('instruction_entervalidemail','useradmin'); ?>');
        theForm.email.value = "";
        theForm.email.focus();
        return (false);
    }
  if (!isEmail(theForm.email.value)) {
    alert('<?php echo $objLanguage->languageText('instruction_entervalidemail','useradmin'); ?>');
    theForm.email.value = "";
    theForm.email.focus();
    return(false);
  }
  if (theForm.email.value != theForm.email2.value) {
        alert('<?php echo $objLanguage->languageText('error_emailnotsame','useradmin'); ?>');
        theForm.email.value = "";
        theForm.email.focus();
        return (false);
  }
  theForm.registermenow.disabled=true;
  return (true);
}
//-->
</script>
<?
    $this->loadclass('textinput','htmlelements');
    $this->loadclass('dropdown','htmlelements');
    $objFeatureBox = $this->newObject('featurebox', 'navigation');

    function textinput($name,$type,$value=NULL,$extra=NULL)
    {
        if (isset($_REQUEST[$name])){
            $value=$_REQUEST[$name];
        }
        $field=new textinput($name,$value);
        $field->fldType=$type;
    	$field->extra=$extra;
        return $field->show();
    }

    $objLanguage =& $this->getObject('language', 'language');

    if (isset($this->message)){
        echo
			"<h2>"
			.$objLanguage->languageText('word_problem','useradmin')
			." : "
			.$this->message
			."</h2>";
    }

    //$formtags=$this->newObject('formtags','htmlelements');

?>
	<form
		id="Form1"
    	name="Form1"
		method="post"
		action="<?php echo $this->uri(array('action'=>'registerapply'),'useradmin');  ?>"
		onsubmit="return Validator(this);"
	>
<?php

    $objHelp =& $this->getObject('help', 'help');
    $helpIcon = $objHelp->show('register', 'useradmin');

    $objFieldset = $this->getObject('fieldsetex', 'htmlelements');
    //$objFieldset->setLegend("<h1>".$objLanguage->languageText("heading_registeryourself",'useradmin').$helpIcon."</h1>");
    $objFieldset->align='CENTER';
    $objFieldset->legendalign='CENTER';
    $objFieldset->width="50%";

	//$objFieldset->addLabel($objLanguage->languageText("heading_registeryourself",'useradmin'));
	$objFieldset->addLabel($objLanguage->languageText("message_selfregister",'useradmin'));
	//echo $objLanguage->languageText("step1",'useradmin');
	// UserId
    $objFieldset->addLabelledField('User ID',textinput('userId','text',''/*,'Text1'*/));
	$objFieldset->addLabelledField('', str_replace(
		"[--INSTITUTIONNAME--]",
		'UWC',//$this->objConfig->institutionShortName(),
		$objLanguage->languageText("heading_ifyouatinstitute",'useradmin')
	));
	$objFieldset->addLabelledField('', $objLanguage->languageText("message_usestudentnum",'useradmin'));
	$objFieldset->addLabelledField('', $objLanguage->languageText("message_usestaffnum",'useradmin'));
	$objFieldset->addLabelledField('', str_replace(
		"[--INSTITUTIONNAME--]",
		'UWC',//$this->objConfig->institutionShortName(),
		$objLanguage->languageText("heading_ifguest",'useradmin')
	));
	$objFieldset->addLabelledField('', $objLanguage->languageText("message_willfillinnumber",'useradmin'));
    $objFieldset->addLabelledField('', "<a onclick=\"document.Form1.userId.value=Math.round(Math.random()*1000)+'".date('ydi')."';\" class='pseudobutton' >".$objLanguage->languageText("hyperlink_generaterandomnumber",'useradmin')."</a>");
	// Title
    $objDropdown = new dropdown('title');
    $objDrop->extra='id="Select1"';
    $objDropdown->addOption('',$objLanguage->languageText('option_selectatitle', 'useradmin'));
    $titles=array("title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
    foreach ($titles as $title)
    {
        $_title = $objLanguage->languageText($title);
        $objDropdown->addOption($_title,$_title);
    }
    $objFieldset->addLabelledField($objLanguage->languageText("word_title"), $objDropdown->show());
	// First Name
    $objFieldset->addLabelledField($objLanguage->languageText("phrase_firstname"), textinput('firstname','text',''/*,'Text3'*/));
	// Surname
    $objFieldset->addLabelledField($objLanguage->languageText("word_surname"), textinput('surname','text',''/*,'Text2'*/));
    // Country
    $objCountries=&$this->getObject('languagecode','language');

	$objFieldset->addLabelledField($objLanguage->languageText('word_country'), $objCountries->country());
	// Username
    $objFieldset->addLabelledField($objLanguage->languageText("word_username"), textinput('username','text',''/*,'Text4'*/));
    // Email
	$objFieldset->addLabelledField($objLanguage->languageText("Pagetext_emailaddress",'useradmin'), textinput('email','text',''/*,'Text5'*/));
	$objFieldset->addLabelledField($objLanguage->languageText("label_confirmemail",'useradmin'), textinput('email2','text',''/*,'Text6'*/));
	$objFieldset->addLabelledField('', $objLanguage->languageText("message_confirmemailmessage",'useradmin'));
    // Submit button
    $objButton=$this->getObject('button','htmlelements');
    $objButton->button('registermenow',$objLanguage->languageText('mod_useradmin_register1','useradmin'));
    $objButton->setToSubmit();
	$objFieldset->addLabelledField('', $objButton->show());
    echo $objFeatureBox->show($objLanguage->languageText("heading_registeryourself",'useradmin'),$objFieldset->show());
?>
	</form>
