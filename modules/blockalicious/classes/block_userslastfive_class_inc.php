<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* The class provides a block to show the last five users to login
* if they are still active
*
* @author Derek Keats <derek@dkeats.com>
*
*/
class block_userslastfive extends object
{
    public $title;
    public $objLanguage;
    
    /**
     * Constructor for the class
     * 
     * @return VOID
     * @access public
     * 
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText(
          "mod_blockalicious_ulftitle", "blockalicious"
        );
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('userslastfive.js',
          'blockalicious')
        );
    }
    
    /**
     * Method to output block
     * 
     * @return string The rendered block
     * @access public
     * 
    */
    public function show()
    {
        $arUsers = $this->getData();
        //var_dump($arUsers);
        $ret = NULL;
        if (!empty ($arUsers)) {
           foreach ($arUsers as $user) {
               $ret .= $user['firstname'] . " " . $user['surname'] . "<br />";
           }
        }
        return '<div id="userslastfive">' . $ret . "</div>";
    }
    
    /**
    *
    * Get an array of the last five online users
    * 
    * @return string array 
    * @access private
    * 
    */
    private function getData()
    {
        $objDb = $this->getObject('loggedinusers', 'security');
        return $objDb->getLastFiveOnlineUsers();
    }
}
?>