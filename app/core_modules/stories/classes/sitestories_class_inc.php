<?php
/* -------------------- stories class ----------------*/

/**
* Class for providing a service to other modules that want to
* display stories a story
*
* @author Derek Keats
*
*/
class sitestories extends dbTable {

    var $objUser;
    var $objLanguage;
    var $objDbStories;
    var $objH;
    var $objParse;

    /**
    *
    * Constructor method to define the table
    *
    */
    function init()
    {
        parent::init('tbl_stories');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->objDbStories = & $this->getObject('dbstories');
        $this->objH =& $this->getObject('htmlheading', 'htmlelements');
        //Get the smiley parser
        $this->objParse = &$this->getObject('parse4display', 'strings');
    }

    /**
    *
    * Method to fetch a story by story ID
    *
    * @param string $id: The id of the story to return
    *
    */
    function fetchStory($id) {
        $ar=$this->objDbStories->getRow('id', $id);
        $creatorId = $ar['creatorId'];
        $isActive = stripslashes($ar['isActive']);
        $title =stripslashes($ar['title']);
        $abstract = stripslashes($ar['abstract']);
        $mainText = $this->objParse->prepare(stripslashes($ar['mainText']));
        $dateCreated = stripslashes($ar['dateCreated']);
        $expirationDate = stripslashes($ar['expirationDate']);
        $notificationDate = stripslashes($ar['notificationDate']);
        //Add the heading
        $this->objH->type=3;
        $this->objH->str=$title;
        $ret=$this->objH->show();
        //Add the abstract
        $ret.="<p class=\"minute\">".$abstract."</p>";
        //Add the main text
        $ret.="<p>".$mainText;
        if ($this->objUser->isAdmin()) {
            $editArray = array('action' => 'edit',
                    'id' => $id);
            $objGetIcon = $this->newObject('geticon', 'htmlelements');
            $ret .= "&nbsp;" . $objGetIcon->getEditIcon($this->uri($editArray, "stories"));
        }
        $ret .= "</p>";
        //Add the author and date
        $ret.="<p class=\"minute\">".$this->objLanguage->languageText("phrase_postedby",'stories');
        $ret.=" <b>".$this->objUser->fullname($creatorId)."</b> ".$this->objLanguage->languageText("word_on",'stories');
        $ret.=" <b>".$dateCreated."</b></p>";
        //Create an instance of the modulesadmin to check if registered
        $this->objModule=&$this->getObject('modulesadmin','modulelist');
        if ($this->objModule->checkIfRegistered('comment', 'comment')){
            //Create an instance of the comment link
            $objComment = & $this->getObject('commentinterface', 'comment');
            //Set the table name
            $objComment->set('tableName', 'tbl_stories');
            $objComment->set('sourceId', $id);
            $ret .= $objComment->showAll();
        }

        return $ret;

    } #function fetchStory

    /**
    *
    * Method to fetch a story by story category
    *
    * @param string $category The category of the stories to return
    * @param integer $limit The number of stories to return
    * @todo -cstories Implement remove hard coding of en and replace with site default.
    *
    */
    function fetchCategory($category, $limit=NULL, $showAuthor=TRUE, $language=NULL)
    {
        if (!$language) {
            $language = 'en';
        }
        //Set up the where clause to return only the category
        $where=" WHERE category='" . $category
          . "' AND isActive='1' AND language='"
          . $language . "' ORDER BY isSticky DESC, dateCreated DESC ";
        //Get an array of the stories in the requested category
        if ($limit!==NULL) {
            $ar=$this->getMostRecent($where, $limit);
        } else {
            $ar=$this->objDbStories->getAll($where);
        }
        /*Count the number of elements returned, used to
        * determine whether or not to display a horizontal
        * rule after the entry
        */
        $elems=count($ar);
        //Initialize counter
        $count=0;
        //Initialize the return string
        $ret="";
        //Instantiate the classe for checking expiration
        $objExp = & $this->getObject('datetime','utilities');
        //Get an instance of the language code
        $objLcode = & $this->getObject('languagecode', 'language');
        // Get Icon for stickylabel
        $objStIcon = $this->newObject('geticon', 'htmlelements');

        //Create an instance of the modulesadmin to check if registered
        $this->objModule=&$this->getObject('modulesadmin','modulelist');
        if ($this->objModule->checkIfRegistered('comment', 'comment')){
            //Create an instance of the comment link
            $objComment = & $this->getObject('commentinterface', 'comment');
            //Set the table name
            $objComment->set('tableName', 'tbl_stories');
            //Set the module code
            $objComment->set('moduleCode', 'stories');
            //Load the link class
            $this->loadClass('link','htmlelements');
            $comReg=TRUE;
        } else {
            $comReg=FALSE;
        }
        $curModule = $this->getParam('module', NULL);
        //Loop through and build the output string
        foreach ($ar as $line) {
            $count=$count+1;
            $id = $line['id'];
            $creatorId = $line['creatorid'];
            $isActive = stripslashes($line['isactive']);
            $title = stripslashes($line['title']);
            $abstract = stripslashes($line['abstract']);
            $mainText = $this->objParse->prepare(stripslashes($line['maintext']));
            $dateCreated = stripslashes($line['datecreated']);
            $expirationDate = stripslashes($line['expirationdate']);
            $notificationDate = stripslashes($line['notificationdate']);
            $commentCount = $line['commentcount'];

            //Check is sticky and replace with isSticky icon
            $isSticky = $line['issticky'];
            if ($isSticky == 1) {
                $objStIcon->setIcon('sticky_yes');
                $title = $objStIcon->show() . $title;
            }

            //Check if expired, if so change font, add icon, & email owner
            if ( $objExp->hasExpired($expirationDate) ) {
                //put it in an error span
                $mainText = "<span class=\"error\">"
                  . $mainText . "</span>&nbsp;"
                  //add the expired clock icon
                  . $objExp->getExpiredIcon();
                if ($isActive==1) {
                    //Send an email to the owner of the content
                    $objExp->sendExpiredMsg('dbstories', 'stories',
                      $creatorId, $title, $abstract, $id);
                }
            }
            //Add the heading
            $this->objH->type=3;
            $this->objH->str=$title;
            $ret .= $this->objH->show();
            //Add the abstract
            $ret .= "<p class=\"minute\">".$abstract."</p>";
            //Add the main text
            $ret .= "<p>".$mainText;
            if ($this->objUser->isAdmin()) {
                $editArray = array(
                  'action' => 'edit',
                  'id' => $id,
                  'comefrom' => $curModule);
                $objGetIcon = $this->newObject('geticon', 'htmlelements');
                $ret .= "&nbsp;" . $objGetIcon->getEditIcon($this->uri($editArray, "stories"));
            }
            $ret .= "</p>";

            if ($showAuthor) {
                //Add the author and date
                $ret.="<p class=\"minute\">".$this->objLanguage->languageText("phrase_postedby",'stories');
                $ret.=" <b>".$this->objUser->fullname($creatorId)."</b> ".$this->objLanguage->languageText("word_on",'stories');
                $ret.=" <b>".$dateCreated."</b>";
            }

            //Insert a comment link with view comments if the user is logged in
            if ($comReg){
                if ($this->objUser->isLoggedIn()) {
                    $objComment->set('sourceId', $id);
                    $ret .= $objComment->addCommentLink();
                    if ($commentCount>0) {
                        $ccStr = $commentCount . " "
                        . strtolower($this->objLanguage->languageText("word_comments",'stories'));
                        //Set the location
                        $ccLocation = $this->uri(array(
                          'action' => 'viewstory',
                          'id' => $id), 'stories');
                        $ret .= $objComment->addViewLink($ccLocation, $ccStr);
                    }
                }
            }

            //Insert a horizontal rule
            if ($elems>1 && $count != $elems) {
                $ret.="<hr />";
            }
            //Check for translations
            $ar = $this->getTranslations($id);
            if (count($ar) > 0 ) {
                $ret .= "&nbsp;&nbsp;&nbsp;" .
                  $this->objLanguage->languageText("mod_stories_alsoavailable",'stories');
                foreach ($ar as $line) {
                    $lcode = $line['language'];
                    $id = $line['id'];
                    $link = $this->uri(array('action' => 'viewstory',
                      'language' => $lcode,
                      'id' => $id));
                    $language = "<a href=\"" . $link . "\" target=\"_blank\">"
                      . $objLcode->getLanguage($lcode) . "</a>";
                    $ret .= "&nbsp;&nbsp;>>" . $language;
                }
            }
            $ret .= "</p>";
        }
        return $ret;
    } #function fetchCategory


    function getTranslations(&$id)
    {
        $sql = "SELECT id, parentId, language FROM tbl_stories WHERE parentId='" . $id . "'";
        return $this->objDbStories->getArray($sql);
    }

    /**
    *
    * Method to put a dropdown list of categories
    *
    */
    function putCategoryChooser()
    {
        $objCat = & $this->getObject('dbstorycategory', 'storycategoryadmin');
        $ar = $objCat->getAll();
        //Load the form class that I need
        $this->loadClass('form','htmlelements');
        //Instantiate the form class
        $objForm = new form('chCat');
        //Instantiate a dropdown
        $objCatDrd = $this->newObject("dropdown", "htmlelements");
        $objCatDrd->name = 'category_selector';
        $objCatDrd->extra=" onChange=\"Javascript:document.location=document.chCat.category_selector.value;\"";
        //Add the categories
        $objCatDrd->addOption("", $this->objLanguage->languageText("mod_stories_anothercat",'stories'));
        foreach ($ar as $line) {
            $link = $this->uri(array(
              'action' => $this->getParam('action', NULL),
              'storycategory' => $line['category']), $this->getParam('module', '_default'));
            $objCatDrd->addOption($link, $line['title']);
        }
        $objForm->addToForm($objCatDrd->show());
        return $objForm->show();
    }

	/**
    *
	* Method to return the most recent stories
    *
    * @param string $where A SQL where clause made up elsewhere.
    * @param integer $num The number of stories to return
    *
	*/
	function getMostRecent($where, $num=10)
    {
        $numOfStories=$this->objDbStories->getRecordCount($where);
		if ($numOfStories > $num) {
		    $first=$numOfStories - $num;
		} else {
			$first = 0;
		}
        $sql="SELECT * FROM tbl_stories " . $where . " ";
		return $this->objDbStories->getArrayWithLimit($sql, $first, $num);
	} #function getMostRecent

}  #end of class
?>
