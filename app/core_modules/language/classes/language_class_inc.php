<?php
/* -------------------- LANGUAGE CLASS ----------------*/
/*Description of file
  *This is a Language class for kewlNextGen
  *@author Prince Mbekwa, Paul Scott
  *@copyright (c) 200-2004 University of the Western Cape
  *@Version 0.9
  *@author  Prince Mbekwa,Derek Keats <dkeats@uwc.ac.za>
*/
/**
*Description of the class
* Language class for KEWL.NextGen. Provides language translation methods,
* the main one being to call the language translation for a particular
* language code.
*
*/
class language extends dbTable {
    var $elements;
    var $objNewForm;
    var $objDropdown;
    var $objButtons;
    var $objConfig;

    /**
    * Constructor method for the language class
    */
    function init()
    {
        parent::init("tbl_" . $this->currentLanguage());
        $this->objConfig = &$this->getObject('config','config');
        $this->objNewForm = &$this->newObject('form', 'htmlelements');
        $this->objDropdown = &$this->newObject('dropdown', 'htmlelements');
        $this->objButtons = &$this->getObject('button', 'htmlelements');
    }

    /**
    * Method to return the language text when passed the language
    * text code. It looks up the language text for the current
    * language in the database.
    *
    * @param string $itemName : The language code for the item to be
    * looked up
    */
    function languageText($itemName, $default = false)
    {
        switch ($itemName) {
            case "mod_context_context":
                return $this->_getContextWord('context');
                break;
            case "mod_context_contexts":
                return $this->_getContextWord('context', TRUE);
                break;
            case "mod_context_author":
                return $this->_getContextWord('author');
                break;
            case "mod_context_authors":
                return $this->_getContextWord('author', TRUE);
                break;
	   case "mod_context_organisation":
                return $this->_getContextWord('organisation');
                break;
            case "mod_context_organisations":
                return $this->_getContextWord('organisation', TRUE);
                break;
            case "mod_context_readonly":
                return $this->_getContextWord('readonly');
                break;
            case "mod_context_readonlys":
                return $this->_getContextWord('readonly', TRUE);
                break;
            case "mod_context_workgroup":
                return $this->_getContextWord('workgroup');
                break;
            case "mod_context_workgroups":
                return $this->_getContextWord('workgroup', TRUE);
                break;
            case "mod_context_story":
                return $this->_getContextWord('story');
                break;
            case "mod_context_stories":
                return $this->_getContextWord('stories', TRUE);
                break;
            default:
                $line = $this->getRow('code', $itemName);
                if ($line['content'] != null) {
                    return trim($line['content']);
                } else {
                    if ($default != false) {
                        return $default;
                    } else if ($itemName == 'error_languageitemmissing') { // test to prevent recursive loop
                            return "This language item is missing";
                    } else {
                        return ($this->languageText('error_languageitemmissing') . ":" . $itemName);
                    }
                }
                break;
        }
    }

    /**
    * Method to return a language element with a [-STRING-] tag replaced
    * with some text. This allows you to create a translatable element
    * with pseudotags, as we had in KEWL.
    *
    * @example TEXT: mod_mymodule_greet|Time and user sensitive greeting
    * |Hello [-FULLNAME-], Good [-GREETINGTIME-], I hope you are fine this great [-DAYOFTHEWEEK-].
    *
    * This allows this to appear in text as Hello Derek Keats,
    * Good evening, I hope you are fine this great Friday.
    *
    * Call it as follows:
    *    $str="mod_mymodule_greet";
    *    $arrOfRep= array(
    *       'FULLNAME'=> $userFullname,
    *       'GREETINGTIME'=> $time,
    *       'DAYOFTHEWEEK'=> $DOW );
    *    $formTitle=$this->objLanguage->code2Txt($str, $arrOfRep );
    *
    * Note that it is not case sensitive, and you can add as many elements as you like.
    *
    * @param string $str the language text code.
    * @param array $arrOfRep An associative array of [-TAG-], replacement pairs
    * @return string $ret The array parsed
    *
    * @author Jonathan Abrahams, Derek Keats
    */
    function code2Txt($str, $arrOfRep=NULL)
    {
        $ret=$this->languageText($str);
        // Process abstracted tags
        $ret = preg_replace($this->_match('context'), $this->_getContextWord('context'), $ret);
        $ret = preg_replace($this->_match('contexts'), $this->_getContextWord('context', TRUE), $ret);
        $ret = preg_replace($this->_match('author'), $this->_getContextWord('author'), $ret);
        $ret = preg_replace($this->_match('authors'), $this->_getContextWord('author', TRUE), $ret);
	$ret = preg_replace($this->_match('organisation'), $this->_getContextWord('organisation'), $ret);
        $ret = preg_replace($this->_match('organisations'), $this->_getContextWord('organisation', TRUE), $ret);
        $ret = preg_replace($this->_match('readonly'), $this->_getContextWord('readonly'), $ret);
        $ret = preg_replace($this->_match('readonlys'), $this->_getContextWord('readonly', TRUE), $ret);
        $ret = preg_replace($this->_match('workgroup'), $this->_getContextWord('workgroup'), $ret);
        $ret = preg_replace($this->_match('workgroups'), $this->_getContextWord('workgroup', TRUE), $ret);
        $ret = preg_replace($this->_match('story'), $this->_getContextWord('story'), $ret);
        $ret = preg_replace($this->_match('stories'), $this->_getContextWord('story', TRUE), $ret);
        // Process other tags
        if( $arrOfRep!=NULL ) {
            foreach( $arrOfRep as $tag=>$rep ) {
                $ret = preg_replace($this->_match($tag), $rep, $ret);
            }
        }
        return $ret;
    }

    /**
    * Method to return Language list
    */
    function languagelist()
    {
        $sql = "Select languageName from tbl_languagelist";
        return $this->getArray($sql);
    }

    /**
    * Method to render Language Chooser on the
    * index page.
    * Construct a form and populate it with all available
    * language translations for selection
    */
    function putlanguageChooser()
    {
        $ret = $this->languageText("phrase_languagelist") . ":<br>\n";
        $script = $_SERVER['PHP_SELF'];
        $ret = $objNewForm = new form('languageCheck', $script);
        $ret = $objDropdown = new dropdown('Languages');
        $results = $this->languagelist();
        foreach ($results as $list) {
            foreach($list as $key) {
                $objDropdown->addOption($key, $key);
            }
        }
        $ret = $objNewForm->addToForm($ret = $this->languageText("phrase_languagelist") . ":<br>\n");
        $ret .= $objDropdown->show();
        $ret .= $button = $this->objButtons->button('go', $this->languageText("word_go", "[Go]"), 'submit');
        $ret .= $button = $this->objButtons->setToSubmit();
        $ret = $objNewForm->addToForm($ret .= $button = $this->objButtons->show());
        $ret = $objNewForm->show();
        return $ret;
    }

    /**
    * Method to return the default language.
    */
    function currentLanguage()
    {
        if (isset($_POST['Languages'])) {
            $_SESSION["language"] = $_POST['Languages'];
            $var = $_POST['Languages'];
        } else {
            if (isset($_SESSION["language"])) {
                $var = $_SESSION["language"];
            } else {
                $this->objConfig = &$this->getObject('config','config');
                $var = $this->objConfig->defaultLanguage();
            }
        }
        return $var;
    }

    /**************************** PRIVATE METHODS *******************************/

    /**
    * Method to create code2Txt match expression.
    * @access private
    * @param String The tag
    * @return String The regular expression with tag
    */
    function _match( $tag )
    {
        return "/\[\-".$tag."\-\]/isU";
    }

    /**
    *
    *  Method to return the word to substitute for context
    *
    */
    function _getContextWord($type, $plural=FALSE)
    {
        $systemType = $this->objConfig->getValue("SYSTEM_TYPE", "contextabstract");
        switch ($type) {
            case "context":
                if ($plural==TRUE) {
                    $langKey = "mod_contextabstract_contexts_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_contexts_default");
                } else {
                    $langKey = "mod_contextabstract_context_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_context_default");
                }
                return $this->languageText($langKey, $default);
                break;
            case "author":
                if ($plural==TRUE) {
                    $langKey = "mod_contextabstract_authors_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_authors_default");
                } else {
                    $langKey = "mod_contextabstract_author_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_author_default");
                }
                return $this->languageText($langKey, $default);
                break;

            case "organisation":
                if ($plural==TRUE) {
                    $langKey = "mod_contextabstract_organisations_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_organisations_default");
                } else {
                    $langKey = "mod_contextabstract_organisation_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_organisation_default");
                }
                return $this->languageText($langKey, $default);
                break;

            case "readonly":
                if ($plural==TRUE) {
                    $langKey = "mod_contextabstract_readonlys_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_readonlys_default");
                } else {
                    $langKey = "mod_contextabstract_readonly_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_readonly_default");
                }
                return $this->languageText($langKey, $default);
                break;

            case "workgroup":
                if ($plural==TRUE) {
                    $langKey = "mod_contextabstract_workgroups_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_workgroups_default");
                } else {
                    $langKey = "mod_contextabstract_workgroup_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_workgroup_default");
                }
                return $this->languageText($langKey, $default);
                break;

            case "story":
                if ($plural==TRUE) {
                    $langKey = "mod_contextabstract_stories_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_stories_default");
                } else {
                    $langKey = "mod_contextabstract_story_" . $systemType;
                    $default = $this->languageText("mod_contextabstract_story_default");
                }
                return $this->languageText($langKey, $default);
                break;

            default:
                return $this->languageText("mod_contextabstract_unrecabstr" . ": "
                  . $type, "Unrecognized abstraction type: " . $type);
                break;
        }


    }
} #end of class

?>
