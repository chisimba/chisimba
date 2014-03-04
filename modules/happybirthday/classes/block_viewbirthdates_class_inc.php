<?php
/*
*@Author Emmanuel Natalis
*@University Computing Center
*@Dar es salaam university of Tanzania
*
*
*Happybithday block for viewing  users celebrating their birthdays today
*/
class block_viewbirthdates extends object
{
    public $objLanguage;
    public $blockContent;
    public $objDbhappybirthday;
   function init()
   {
         /*
        *Initialising the language object
        */
        $this->objLanguage=$this->getObject('language','language');
           /*
           *
            *This is the title of the block
            *
            */
        $this->title=$this->objLanguage->languageText('mod_happybirthday_blocktitle','happybirthday');
     
        $this->objDbhappybirthday=$this->getObject('dbhappybirthday','happybirthday');
       
      }
      public function show()
      {
          /*
          *Returning the names of user names
          */
         return $this->objDbhappybirthday->userFullname() ;
         }      
         
   }
?>