<?php
/**
 *
 * A fieldset class using DOM extension
 *
 * This file contains the fieldset class which is used to generate
 * HTML fieldset elements for forms. It was modified after the original
 * HTMLelements fieldset class by Nguni Phakela as part of the Chisimba
 * hackathon 2010 12 02. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmlfieldset', 'htmldom')
 *
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * A fieldset class using DOM extension
 *
 * @category  Chisimba
 * @package   htmldom
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @author    Nguni Phakela <nonkululeko.phakela@wits.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: htmlfieldset_class_inc.php 16438 2010-01-22 15:38:42Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @example:
 *       $objField = $this->newObject("htmlfieldset", "htmldom");
         $objField->setValue("legend", "TRUE");
         $objField->setValue("legendText", "This is a nice legend");
         $objField->setValue("cssId", "myId");
         $objField->setValue("cssClass", "myClass");
         $str = $objField->show();
 */
class htmlfieldset extends object
{
    /**
     * Holds the CSS id for the fieldset, and is set using $this->setValue()
     *
     * @var string $name
     * @access private
     *
     */
    private $cssId;

    /**
     * Holds the CSS class for the fieldset, and is set using $this->setValue().
     *
     * @var string $name
     * @access private
     *
     */
    private $cssClass;

    /**
     *
     * Object to hold the dom document
     *
     * @var string Object $objDom
     * @access private
     */
    private $objDom;

    /**
    *@var $legend The heading of the frameset
    */
    private $legend;

    /*
     * @var $legendText string used for text inside the legend
     *
     */
    private $legendText;

    /**
    *
    * Intialiser for the htmldom fieldset object
    *
    * @access public
    * @return void
    *
    */
    public function init()
    {
        // Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
        $this->legend = FALSE;
    }

    /**
     *
     * Standard show function to render the fieldset using the DOM document
     * object
     *
     * @param string $caption The fieldset caption
     * @return string
     * @access public
     *
     */
    public function show($caption=null) {
        $fieldset = $this->objDom->createElement('fieldset');

        if($this->legend) {
            $this->legend = $this->objDom->createElement('legend');
            if($this->legendText) {
                $text = $this->objDom->createTextNode($this->legendText);
                $this->legend->appendChild($text);
            }
            $fieldset->appendChild($this->legend);
        }
        if($this->cssId) {
            $fieldset->setAttribute('id',$this->cssId);
        }
        if ($this->cssClass) {
            $fieldset->setAttribute('class',$this->cssClass);
        }
        
        $fieldset = $this->objDom->appendChild($fieldset);
        $ret = $this->objDom->saveHTML();
        
        return $ret;
    }

    /**
     *
     * A standard setter. The following params may be set here
     * $cssClass - A CSS class to use in the fieldset
     * $cssId - A CSS id to use in the fieldset
     * $legendText - The text that is in the legend of the fieldset
     *
     * @param string $param The name of the parameter to set
     * @param string $value The value to set the parameter to
     * @access public
     */
    public function setValue($param, $value) {
        $this->$param = $value;
    }

    /**
     * A standard fetcher. The following params may be fetched here
     * $cssClass - A CSS class to use in the fieldset
     * $cssId - A CSS id to use in the fieldset
     * $legendText - The text that is in the legend of the fieldset
     *
     * @param string $param The name of the parameter to set
     * @access public
     */
    public function getValue($param) {
        return $this->$param;
    }
}
?>