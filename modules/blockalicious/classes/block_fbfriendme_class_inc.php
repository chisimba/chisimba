<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Facebook friend me widget
*
* @author Derek Keats
*
*/
class block_fbfriendme extends object
{
    public $title;
    public $blockType;
    public $faceBookId;

    /**
    * Constructor for the class
    */
    public function init()
    {
        //Instantiate the language object
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText("mod_blockalicious_fbfriendme", "blockalicious");
        //Userparams is where the facebook id is stored
        $objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
        //This enables the thing to work as a blog plugin
        $uid = $this->getParam('userid', FALSE);
        if ($uid) {
            $un = $this->objUser->userName($uid);
        } else {
            $un = $this->objUser->userName();
        }
        $objUserParams->setUid($un);
        $objUserParams->readConfig();
        $this->faceBookId = $objUserParams->getValue("facebookid");
        $this->blockType = "none";
    }

    /**
    * Method to output block
    */
    public function show()
	{
        return $this->getWidget();
    }

    private function getWidget()
    {
        $icon = $this->getResourceUri("fb-friend-icon.gif", "blockalicious");
        $icon = "<img src=\"$icon\" alt=\"$this->title\" border=\"0\" style=\"vertical-align:middle\">";
        $objLink = $this->getObject("link", "htmlelements");
        $objLink->href = "http://www.facebook.com/addfriend.php?id=$this->faceBookId";
        $objLink->title = $this->title;
        $objLink->link = $icon . " <span class=\"minute\">$this->title</span>";
        return $objLink->show() . '<br />';
    }
}
?>