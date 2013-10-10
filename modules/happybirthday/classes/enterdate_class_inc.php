<?php
class enterdate extends object
{
/*
* @author Emmanuel Natalis
* @Software developer university of dar es salaam
* @copyright (c) 2008 GNU GPL
* @package happyBirthDay
* @version 1
*/

//Variable declerations
public $fullName;
public $Objfname;
public $siteroot; //This variale will assign the site root path
public $objrootpath;//Variable for storing the object for site root path
 public $objLanguage;//Variable to store the object of the language module
public $msg1;
public $msg2;
public $msg3;
public $welcomeMsg; //Variable for the welcome message
function init()
{
 //Initializing the language object   
             $this->objLanguage=$this->getObject('language','language');

 //Instantiate the language object
$this->objLanguage = $this->getObject('language', 'language');
//Instantiate the user object
$this->Objfname = $this->getObject('user','security');
// Assigning the user's fullname
$this->fullName=$this->Objfname->fullname();
//Getting the site root path
          $this->objrootpath=$this->getObject('altconfig','config');
        //getting the root path
         $this->siteroot=$this->objrootpath->getSiteRoot();
//Capturing values
 $this->msg1=$this->objLanguage->languageText('mod_happybirthday_enter1','happybirthday');
 $this->msg2=$this->objLanguage->languageText('mod_happybirthday_enter3','happybirthday');
 $this->msg3=$this->objLanguage->languageText('mod_happybirthday_selectdate','happybirthday');

         $this->welcomeMsg=$this->welcome." <i><b>".$this->fullName."!</b></i><br>".$this->objLanguage->languageText('mod_happybirthday_welcomeMsg','happybirthday');
}
//
 function displayMsg()
{
 $msg="<br>".$this->msg1." <b><i> ".$this->fullName." </i></b>,<br>".$this->msg2."<br><br>";
return $msg;
}

private function loadElements()
{
 //Load the form class
$this->loadClass('form','htmlelements');
 //Load the label class
$this->loadClass('label', 'htmlelements');

 
}

private function buildForm()
{
     $this->loadElements();
    //Create the form
   //making the form action
 $formAct=$this->siteroot."?module=happybirthday&page=enterdate";
     $objForm = new form('comments',$formAct);
     //create label
        $titleLabel = new label($this->msg3,"title");
        $objForm->addToForm($titleLabel->show() . "<br />");

        //
        $this->cdate=$this->getObject('datepicker','htmlelements');
        $objForm->addToForm($this->cdate->show() . "<br />");

                 //----------SUBMIT BUTTON--------------
        //Create a button for submitting the form
        $objButton = new button('save');
        // Set the button type to submit
        $objButton->setToSubmit();
        
        // with the word save
        $objButton->setValue(' Save ');
        $objForm->addToForm($objButton->show());

        return $objForm->show();
    }
//Method to get the forms action

   
   public function show()
    {
        return $this->buildForm();
     }
//function to display happybirthday main menu
private function _display_main_menu($welcome)
{
//assigning a $greet variable
$greet=$welcome;
   //Creating an instance of the language class
$this->objLangu=$this->getObject('language','language');
//creating an instane of the altconfig class
$this->objSite=new altconfig();
$this->siteRoot=$this->objSite->getSiteRoot();
//setting variables from the language items
$this->enter=$this->objLangu->languageText('mod_happybirthday_enter','happybirthday');
$this->remove=$this->objLangu->languageText('mod_happybirthday_remove','happybirthday');
$this->view=$this->objLangu->languageText('mod_happybirthday_view','happybirthday');
$this->select=$this->objLangu->languageText('mod_happybirthday_select','happybirthday');
echo "$greet<br><br>";
echo "<b>$this->select </b><BR>";
echo "1. <a href='$this->siteRoot?module=happybirthday&page=edata'>".$this->enter."</a><br>";
echo "2. <a href='javascript:if(confirm(\"Are sure you want to remove your birthdate?\")){ window.location.href=\"$this->siteRoot?module=happybirthday&page=rdata\"; }'>".$this->remove."</a><br>";
//echo "2. <a href='$this->siteRoot?module=happybirthday&page=rdata'>".$this->remove."</a><br>";
echo "3. <a href='$this->siteRoot?module=happybirthday&page=view_users'>".$this->view."</a><br>";

}
//function to display the main menu at the first page of the happybirthday module
public function display_main_menu()
{
 $welcome=$this->welcomeMsg;
 echo "<table style=\"text-align: left; width: 1040px; height: 44px;\"
 border=\"0\ cellpadding=\"2\" cellspacing=\"2\">
  <tbody>
    <tr>
      <td style=\"text-align: center; width: 201px;\"></td>
      <td
 style=\"width: 532px; text-align: justify; vertical-align: top;);\">";
//calling a function to display module menus
 $this->_display_main_menu($welcome);
 echo "</td>
      <td style=\"width: 141px;\"></td>
     </tr>
  </tbody>
</table>";
}
}
?>
