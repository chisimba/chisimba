<?
    /**
    * Template for Self-registration
    * Much of this code is as I found it.
    * The actual HTML elements have mostly been replaced with calls to the htmlelments classes
    * The code uses several instances of htmltable, as the page consists of several tables one after the other.
    */

    $objLanguage=& $this->getObject('language', 'language');
    if (isset($this->message)){
        print "<h2>".$objLanguage->languageText('word_problem')." : ".$objLanguage->languageText($this->message)."</h2>\n";
    }

    $this->loadclass('textinput','htmlelements');
    /* method to act as a 'wrapper' for textelement class
    * @author James Scoble
    * @param $name string
    * @param $type string
    * @param $value  string
    * @returns string
    */
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

?>
<head>
<script Language="JavaScript">
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
        alert('<?php echo $objLanguage->languageText('instruction_entergenerateid'); ?>');
        theForm.userId.value = "";
        theForm.userId.focus();
        return (false);
    }
  if (theForm.userId.value.length < 5)
    {
        alert('<?php echo $objLanguage->languageText('instruction_enteratleast5digits'); ?>');
        theForm.userId.value = "";
        theForm.userId.focus();
        return (false);
    }
  if (theForm.userId.value.length > 15)
    {
        alert('<?php echo $objLanguage->languageText('instruction_enteratmost9digits'); ?>');
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
        alert('<?php echo str_replace('chapter number','userId',$objLanguage->languageText('alert_enteronlydigit')); ?>');
        theForm.userId.value = "";
        theForm.userId.focus();
        return (false);
    }
  if (theForm.title.selectedIndex == 0) {
        alert('<?php echo $objLanguage->languageText('alert_titlenotvalidoption'); ?>');
        theForm.title.value = "";
        theForm.title.focus();
        return (false);
    }
  if (theForm.firstname.value == "") {
        alert('<?php echo $objLanguage->languageText('instruction_enterfirstname'); ?>');
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
        alert('<?php echo $objLanguage->languageText('instruction_entersurname'); ?>');
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
        alert('<?php echo $objLanguage->languageText('instruction_entervalidusername'); ?>');
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
        alert('<?php echo $objLanguage->languageText('instruction_entervalidemail'); ?>');
        theForm.email.value = "";
        theForm.email.focus();
        return (false);
    }
  if (!isEmail(theForm.email.value)) {
    alert('<?php echo $objLanguage->languageText('instruction_entervalidemail'); ?>');
    theForm.email.value = "";
    theForm.email.focus();
    return(false);
  }
  if (theForm.email.value != theForm.email2.value) {
        alert('<?php echo $objLanguage->languageText('error_emailnotsame'); ?>');
        theForm.email.value = "";
        theForm.email.focus();
        return (false);
    }
theForm.B1.disabled=true;
return (true);
}

//-->
</Script>
</head>

<?
    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='700';
    $objTblclass->attributes=" border=0";
    $objTblclass->cellspacing='0';
    $objTblclass->cellpadding='0';

    $formtags=$this->newObject('formtags','htmlelements');
?>
<form method="post" action="<?php echo $this->uri(array('module'=>'useradmin','action'=>'submitregister'));  ?>"
    name="Form1" onsubmit="return Validator(this)" ID="Form1">
<?
    // create an instance of the help object
    $objHelp =& $this->getObject('help', 'help');
    $helpIcon = '&nbsp;'.$objHelp->show('register', 'useradmin');

    //Create an instance of the fieldset object
    $objFieldset = $this->getObject('fieldset', 'htmlelements');
    $objFieldset->legend=$objLanguage->languageText("heading_registeryourself").$helpIcon;
    $objFieldset->legendalign='CENTER';
    $objFieldset->width="50%";
    $objFieldset->align='CENTER';

    //Headings and explanation messages

    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText("heading_registeryourself"),"", NULL, NULL, NULL," colspan=4 class='heading'");
    $objTblclass->endRow();

    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText("message_selfregister"),"", NULL, NULL, NULL," class='even' colspan=4");
    $objTblclass->endRow();

    // A message is output that uses the short name of the institution
    $row=array($objLanguage->languageText("step1"), str_replace("[--INSTITUTIONNAME--]",$this->objConfig->getinstitutionShortName(),$objLanguage->languageText("heading_ifyouatinstitute")), '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', str_replace("[--INSTITUTIONNAME--]",$this->objConfig->institutionShortName(),$objLanguage->languageText("heading_ifguest")));
    $objTblclass->addRow($row,'odd');

    // UserId - can be autogenerated using JavaScript
    $onclick="document.Form1.userId.value=Math.round(Math.random()*1000)+'".date('ydi')."';";
    $row=array('',textinput('userId','text','','Text1'),'',"<a onclick=\"".$onclick."\" class='pseudobutton' >".$objLanguage->languageText("hyperlink_generaterandomnumber")."</a>" );
    $objTblclass->addRow($row,'odd');

    $row=array('',$objLanguage->languageText("message_usestudentnum").
    "<br>".$objLanguage->languageText("message_usestaffnum"),'',$objLanguage->languageText("message_willfillinnumber") );
    $objTblclass->addRow($row,'odd');

    // Add to the Fieldset object to be output at the end of the file
    $objFieldset->contents =$objTblclass->show()."<br />\n";
    ?><?
    // title
    $this->loadclass('dropdown','htmlelements');
    $objDrop2= new dropdown('title');
    $titles=array("title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
    $objDrop2->addOption('',$objLanguage->languageText('option_selectatitle'));
    foreach ($titles as $row)
    {
        $row=$objLanguage->languageText($row);
        $objDrop2->addOption($row,$row);
    }

    $objDrop->extra="ID=\"Select1\"";


    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='700';
    $objTblclass->attributes=" border=0";
    $objTblclass->cellspacing='0';
    $objTblclass->cellpadding='2';


    // First name and surname fields
    $row=array();
    $row[]=$objLanguage->languageText("step2");
    $row[]=$objLanguage->languageText("word_title");
    $row[]=$objDrop2->show();
    $row[]=$objLanguage->languageText("phrase_firstname");
    $row[]=textinput('firstname','text','','Text3');
        //<!-- <input name="firstname" type="text" size="23" ID="Text3" maxlength="50"></td> -->
    $row[]=$objLanguage->languageText("word_surname");
    $row[]=textinput('surname','text','','Text2');
    $objTblclass->addRow($row,'even');

    // country
    $objCountries=&$this->getObject('countries','utilities');
    $objDrop4=new dropdown('country');
    $objDrop4->addFromDB($objCountries->getAll(' order by name'), "printable_name", "iso",'ZA');
    $row=array('&nbsp;','&nbsp;','&nbsp;','&nbsp;','&nbsp;',$objLanguage->languageText('word_country'),$objDrop4->show());
    $objTblclass->addRow($row,'even');

    // Add to the Fieldset object to be output at the end of the file
    $objFieldset->contents.=$objTblclass->show();
    $objFieldset->contents.="\n&nbsp;\n";

    //   &nbsp;


    // Username
    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='700';
    $objTblclass->attributes=" border=0";
    $objTblclass->cellspacing='0';
    $objTblclass->cellpadding='0';

    $row=array(
        $objLanguage->languageText("step3"),
        $objLanguage->languageText("word_username"),
        textinput('username','text','','Text4')
        );

     $objTblclass->addRow($row,'odd');

     $objTblclass->startRow();
     $objTblclass->addCell('',"", NULL, NULL, NULL,"class='odd'");
     $objTblclass->addCell($objLanguage->languageText("warning_pleasenote")."&nbsp;".$objLanguage->languageText("warning_usernamenospaces"),"", NULL, NULL, NULL, "colspan=2 class='odd'");
     $objTblclass->endRow();

    // Add to the Fieldset object to be output at the end of the file
    $objFieldset->contents.=$objTblclass->show()."&nbsp\n";

  //?>&nbsp;<?
    //Email Address
    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='700';
    $objTblclass->attributes=" border=0";
    $objTblclass->cellspacing='0';
    $objTblclass->cellpadding='0';

    $row=array(
        $objLanguage->languageText("step4"),
        $objLanguage->languageText("Pagetext_emailaddress"),
        textinput('email','text','','Text5')
        );
       //<!--- <input name="email" type="text"size="47" ID="Text5" maxlength="100"></td> -->
    $objTblclass->addRow($row,'even');

    $row=array(
        '&nbsp;',
        '&nbsp;<b>'.$objLanguage->languageText("label_confirmemail").'</b>',
        textinput('email2','text','','Text6')
        );
       //<!---<input name="email2" type="text" size="47" ID="Text6" maxlength="100"></td>-->>
    $objTblclass->addRow($row,'even');

    // Add to the Fieldset object to be output at the end of the file
    $objFieldset->contents.=$objTblclass->show();


    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='700';
    $objTblclass->attributes=" border=0";
    $objTblclass->cellspacing='0';
    $objTblclass->cellpadding='0';
    $row=array(
        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
        $objLanguage->languageText("message_confirmemailmessage")
        );
    $objTblclass->addRow($row,'even');

    // Add to the Fieldset object to be output at the end of the file
    $objFieldset->contents.=$objTblclass->show()."&nbsp\n";

    //$systemType = $this->objConfig->getValue("SYSTEM_TYPE", "contextabstract");
    //if ($systemType=='alumni'){
    //    $objAlumni=&$this->getObject('alumniusers','alumni');
    //    $objFieldset->contents.=$objAlumni->showRegisterFields()."&nbsp\n";
    //}

    // Create the Button object for the submit button
    $objButton=$this->getObject('button','htmlelements');
    $objButton->button('B1',$objLanguage->languageText('mod_useradmin_register1','Register me now'));
    $objButton->setToSubmit();

    // Table for the step6 part of the form - the register button
    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='700';
    $objTblclass->attributes=" border=0";
    $objTblclass->cellspacing='0';
    $objTblclass->cellpadding='0';
    $row=array(
        $objLanguage->languageText("phrase_finalstep","Final Step"),'&nbsp;',$objButton->show());
       // '<input type="submit" class="button"  value="'.$objLanguage->languageText('mod_useradmin_register1','Register me now').'" name="B1" ID="Submit1">');
        //&nbsp;<input type="reset" class="button"  value="Reset" name="B2"  ID="Button1">
    $objTblclass->addRow($row,'odd');

    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText("message_whenclickregister"),"", NULL, 'center', NULL, 'class=odd colspan="3"');
    $objTblclass->endRow();

    // Add to the Fieldset object to be output at the end of the file
    $objFieldset->contents.=$objTblclass->show();

    print $objFieldset->show();

    print $formtags->closeForm();
?>
