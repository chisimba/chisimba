<?php
// security check
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
/**
*
* Controller class for Happybirth day  module. This is a module developed by the free and open source innovation unit of the university of dar es salaam. *It is a module which can enable a user of the website to enter his birthdate and the system will display the names of the users at the prelogin module *whose birthdates follows that particular day. This is the first version of this module, we expect to expand it furthur by adding some of the *functionalities.
*
* @author Emmanuel Natalis
* @Software developer university of dar es salaam
* @copyright (c) 2008 GNU GPL
* @package happyBirthDay
* @version 1
*
*
*Beggining of the controller class
*/
class happybirthday extends controller
{
    /**
    *
    * 
    *
    */
    //Variable decreration                                                               
   
    public $welcomeMsg; //This is the variable which will be assigned the welcome message once the module is started. This variable will be assigned a value at the controller constructor (init function)
    public $fullName; //This variable will be assigned a user's fullname. This also will be done at the init function
    public $siteroot; //This variale will assign the site root path
    public $objrootpath;//Variable for storing the object for site root path
    public $page;//Script to determine which page to be loaded
    public $dateselected;
    public $Objdbtable;//Variable to store a dbtable object
    public $username;//variable to store the username
    public $objLanguage;//Variable to store the object of the language module
    public $welcome;
    /**
    *
    
    
    *
    */
    public function init()
    {    //Initializing the language object   
             $this->objLanguage=$this->getObject('language','language');
         //getting which page to be loaded
          $this->page=$_GET['page'];
         //Getting the site root path
          $this->objrootpath=$this->getObject('altconfig','config');
        //getting the root path
         $this->siteroot=$this->objrootpath->getSiteRoot();
        // Getting the user object. security is the name of the module and user is the name of the class whose object is gonna be created
           $objUser = & $this->getObject('user', 'security');
         //We call a fullname function to get the user's fullname
        $this->fullName = $objUser->fullname();
         //Getting the username
         $this->username=$objUser->userName();
         //Setting the fullname into array
          //assigning the welcome variable
          $this->welcome=$this->objLanguage->languageText('mod_happybirthday_welcome','happybirthday');
         
        //assigning the welcome message to the  variable
        $this->welcomeMsg=$this->welcome." <i><b>".$this->fullName."!</b></i><br>".$this->objLanguage->languageText('mod_happybirthday_welcomeMsg','happybirthday');

        
    }
    /**
    *
    * Standard controller dispatch method. The dispatch method calls any
    * methods involving logic and hands of the results to the template for
    * display.
    *
    */
    public function dispatch($action=Null)
    {
        
        if($this->page=='edata')
{
 return 'enter_date_tpl.php';
} else 
   if($this->page=='enterdate')
 {
   //Retriving the selected date
   $this->dateselected=$this->getParam('calendardate');
    //passing it to the template file
 
    $this->setVar('dat',$this->dateselected);
   
  return "enter_date_tpl.php";
 }
   else
  if($this->page=='rdata')
  {
   $this->setVar('remove', $this->page);
   return "enter_date_tpl.php";
  }
  else
 if($this->page=='view_users')
  { 
       
       //This function registers the view_users variable
       $this->setVar('view_users', $this->page);
       // here below we return the name of the file to excuted first
       
        return "enter_date_tpl.php";
  } else
   {
       //This function registers the welcomeMsg variable
       $this->setVar('greet', $this->welcomeMsg);
       //we return the name of the file to excuted first
       //script to determine which page to be called
        return "welcome_tpl.php";
      }
    }

   public function requiresLogin($action) 
        {
           
                return TRUE;
           
        }   
}
?>