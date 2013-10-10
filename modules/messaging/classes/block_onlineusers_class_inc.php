<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The class to display a list of online users in a block
*
* @author Kevin Cyster
*/
class block_onlineusers extends object
{
    /*
    * @var object $objIcon: The geticon class in the htmlelements module
    * @access private
    */
    private $objIcon;

   /*
    * @var object $objUser: The user class in the security module
    * @access private
    */
    private $objUser;

    /*
    * @var string $userId: The user id of the current user
    * @access private
    */
    private $userId;

   /*
    * @var object $objLanguage: The language class in the language module
    * @access private
    */
    private $objLanguage;

    /*
    * @var string $title: The title of the block
    * @access public
    */
    public $title;

     /**
    * Constructor for the class
    * 
    * @access public
    * @return
    */
    public function init()
    {
        // load html element classes
        $this->loadClass('layer', 'htmlelements');
        $this->loadClass('windowpop','htmlelements');
        $this->objIcon = $this->getObject('geticon', 'htmlelements');

        // system classes
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->objLanguage = $this->getObject('language', 'language');
        
        // language elements
        $title = $this->objLanguage->languageText('mod_messaging_userschatting', 'messaging');
        $label = $this->objLanguage->languageText('mod_messaging_userlist', 'messaging');  
        $help = $this->objLanguage->languageText('mod_messaging_help', 'messaging');
        $listLabel = $this->objLanguage->languageText('mod_messaging_list', 'messaging');
              
        // get data
        $isModerator = $this->getSession('is_moderator');       
        
        // help icon
        $this->objIcon->setIcon('help_small');
        $this->objIcon->align = 'top';
        $this->objIcon->title = $help;
        $this->objIcon->extra = 'style="cursor: help;" onclick="javascript:
            var el_Div = $(\'usersHelpDiv\');
            jsShowHelp(el_Div);"';
        $helpIcon = $this->objIcon->show();
        
        // help layer
        $objLayer = new layer();
        $objLayer->id = 'usersHelpDiv';
        $objLayer->display = 'none';
        if($isModerator){
            $objLayer->addToStr('<font size="1">'.$label.$listLabel.'</font>');
        }else{
            $objLayer->addToStr('<font size="1">'.$label.'</font>');
        }
        $helpLayer = $objLayer->show();
        
        // title
        $this->title = $title.'&nbsp;'.$helpIcon.$helpLayer;                
    }

    /**
    * Method to output a block with online users
    *
    * @access public
    * @return string $str: The output string
    */
    public function show()
	{
        // display layer for online user list
        $objLayer = new layer();
        $objLayer->id = 'usersListDiv';
        $userLayer = $objLayer->show();
        $str = $userLayer;
        return $str;
    }
}
?>