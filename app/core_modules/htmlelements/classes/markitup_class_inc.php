<?php

/**
*
* Class to load the JQuery markitup editor
*
* markItUp! is a JavaScript plugin built on the jQuery library.
* It allows you to turn any textarea into a markup editor. Html,
* Textile, Wiki Syntax, Markdown, BBcode or even your own Markup
* system can be easily implemented.
*
* Its website is http://markitup.jaysalvat.com/home/
*
* @category  Chisimba
* @author  Derek Keats
* @package htmlelements
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id: markitup_class_inc.php 3661 2008-03-15 15:10:37Z dkeats $
* @link      http://avoir.uwc.ac.za
*/

class markitup extends object
{
    /**
    *
    * The set is the type of editor to load, defaults to wiki
    * since this was primarily written for the wiki
    *
    * @access public
    *
    */
    public $set;


    public $validTypes = array('chiki','html', 'default');
    public $objLanguage;

    /**
    * Constructor
    */
    public function init()
    {
        //Set the default set to wiki
        $this->set = "default";
        $this->objLanguage = $this->getObject('language','language');
    }

    public function setType($value)
    {
        if (in_array($value, $this->validTypes)) {
            $this->set = $value;
        } else {
            $msg=$this->objLanguage->languageText('mod_htmlements_error_markitup_illegalset', 'htmlelements')
              . ": " . $value;
            throw new customException($msg);
        }

    }


    /**
    * Method to load the markitup JavaScript
    *
    * @return string markitup JavaScript
    * @access public
    *
    */
    public function show($what=NULL, $name=NULL)
    {
        // Load Markitup
        $ret = $this->getMarkitUpScript()
          . $this->getSet()
          . $this->getMarkItUpCss()
          . $this->getSetCss()
          . $this->getMarkit($what, $name);
        return $ret;
    }

    /**
    *
    * Method to return the markit up javascript for adding to
    * the page header
    *
    * @access private
    * @return string A string contiaing the SCRIPT tags
    *
    */
    private function getMarkitUpScript()
    {
        return $this->getJavascriptFile('markitup/jquery.markitup.js','htmlelements')."\n";
    }
    /**
    *
    * Method to return the markitup set javascript for adding to
    * the page header
    *
    * @access private
    * @return string A string contiaing the SCRIPT tags
    *
    */
    private function getSet()
    {
        return $this->getJavascriptFile('markitup/sets/' . $this->set .'/set.js','htmlelements')."\n";
    }

    /**
    *
    * Method to return the markit up CSS for the set for adding to
    * the page header
    *
    * @access private
    * @return string A string contiaing the set CSS STYLE tags
    *
    */
    private function getMarkItUpCss()
    {
        return "<link rel=\"stylesheet\" type=\"text/css\" href=\""
          . $this->getResourceUri('markitup/skins/markitup/style.css', 'htmlelements')
          . "\" />\n";
    }

    /**
    *
    * Method to return the markit up CSS for adding to
    * the page header
    *
    * @access private
    * @return string A string contiaing the CSS STYLE tags
    *
    */
    private function getSetCss()
    {
        $styleSet = "markitup/sets/$this->set/style.css";
        return "<link rel=\"stylesheet\" type=\"text/css\" href=\""
          . $this->getResourceUri($styleSet, 'htmlelements')
          . "\" />\n";
    }

    /**
    *
    * Method to return the markitup script for turning on the
    * markup editor for the actual TEXTAREA for adding to
    * the page header
    *
    * @access private
    * @return string A string contiaing the marketup script
    *
    */
    private function getMarkit($what=NULL, $name=NULL)
    {
        switch ($what) {
            case "all":
               $what = NULL;
               break;
            case "class":
               $what = ".";
               break;
            case "id":
            default:
               $what="#";
               break;
        }
        if ($name==NULL) {
            $name = "markItUp";
        }

        return '<script type="text/javascript" >
          jQuery(document).ready(function() {
          jQuery("' . $what . $name . '").markItUp(mySettings);
          });
          </script>';
    }
}

?>