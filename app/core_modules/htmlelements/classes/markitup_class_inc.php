<?php

/**
*
* Class to load the JQuery markitup editor
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

    /**
    * Constructor
    */
    public function init()
    {
        //Set the default set to wiki
        $this->set = "default";
    }

    public function setType($value) {
        $this->set = $value;
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

    public function getMarkitUpScript()
    {
        return $this->getJavascriptFile('markitup/jquery.markitup.js','htmlelements')."\n";
    }

    public function getSet()
    {
        return $this->getJavascriptFile('markitup/sets/' . $this->set .'/set.js','htmlelements')."\n";
    }

    public function getMarkItUpCss()
    {
        return "<link rel=\"stylesheet\" type=\"text/css\" href=\""
          . $this->getResourceUri('markitup/skins/markitup/style.css', 'htmlelements')
          . "\" />\n";
    }

    public function getSetCss()
    {
        $styleSet = "markitup/sets/$this->set/style.css";
        return "<link rel=\"stylesheet\" type=\"text/css\" href=\""
          . $this->getResourceUri($styleSet, 'htmlelements')
          . "\" />\n";
    }

    public function getMarkit($what=NULL, $name=NULL)
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