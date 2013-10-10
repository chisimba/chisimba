<?php
/**
 *
 * _SHORTDESCRIPTION
 *
 * _LONGDESCRIPTION
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
 * @category  Chisimba
 * @package   _MODULECODE
 * @author    _AUTHORNAME _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: db_MODULECODE.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
*  
*
* @author _AUTHORNAME
* @package _MODULECODE
*
*/
class filtertemplate extends object
{
    public $objConfig;
    public $objLanguage;

    /**
    *
    * Intialiser for the _MODULECODE controller
    * @access public
    *
    */
    public function init()
    {
        //Set the parent table here
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    public function getXmlRendered($filter)
    {
        if ($filter) {
            $objFilterXml = $this->getObject('filterxml', 'filtergen');
            if ($xml = $objFilterXml->getFilterXml($filter)) {
                $str = "<h3>" . $xml->name . "</h3>" 
                  . $xml->description 
                  . "<br /><b>" 
                  . $this->objLanguage->languageText('mod_filtergen_typeoffilter','filtergen') 
                  . "</b>: "
                  . $xml->type 
                  . "<br /><b>" 
                  . $this->objLanguage->languageText('mod_filtergen_format','filtergen')  
                  . "</b>:";
                foreach ($xml->formats->format as $format) {
                    $str .= "<br />&nbsp;&nbsp;" . $format;
                }
                $str .= "<br /><b>" 
                  . $this->objLanguage->languageText('mod_filtergen_example','filtergen') 
                  . "</b>: ";
                foreach ($xml->examples->example as $e) {
                    $str .= "<br />&nbsp;&nbsp;" . $e;
                }
                $paramCounts = $xml->num_of_parameters;
                $str .= "<br /><b>" 
                  . $this->objLanguage->languageText('mod_filtergen_numparams','filtergen') 
                  . "</b>: " . $paramCounts;
                if ($paramCounts > 0) {
                    $str .= "<br /><b>" 
                    . $this->objLanguage->languageText('mod_filtergen_params','filtergen') 
                    . "</b>: ";
                    foreach ($xml->params->param as $param) {
                        //param->name and param->value
                        $str .= "<br />&nbsp;&nbsp;" . $param->name
                          . " (" . $param->value . ")";
                    }
                }
                $str .= "<br /><b>" 
                  . $this->objLanguage->languageText('mod_filtergen_before','filtergen') 
                  . "</b>:"
                  . $xml->before ."<br /><b>" 
                  . $this->objLanguage->languageText('mod_filtergen_after','filtergen') 
                  . "</b>:"
                  . $xml->after;
            } else {
                $str = '<span class="error">' 
                  . $this->objLanguage->languageText('mod_filtergen_cfdlmissing','filtergen')
                  . '</span>';
            }
        } else {
            $str = '<span class="error">' 
              . $this->objLanguage->languageText('mod_filtergen_cfdlmissing','filtergen')
              . '</span>';
        }
        return $str;
    }

    public function makeForm($filter)
    {
        $objFilterXml = $this->getObject('filterxml', 'filtergen');
        if ($xml = $objFilterXml->getFilterXml($filter)) {
            //Load the form object
            $this->loadClass('form', 'htmlelements');
            //Load the button object
            $this->loadClass('button', 'htmlelements');
            //Load the textinput object
            $this->loadClass('textinput', 'htmlelements');
            //Create a form
            $objForm = new form('filter');
            $numParams = intval($xml->num_of_parameters);
            $objForm->action = $this->uri(array(
              'action' => 'getfilter',
               'filter' => $filter,
               'numparams' => $numParams), "filtergen");
            $name = "<h3>" .$xml->name . "</h3>";
            $objForm->addToForm($name);
            $desc = " " . $xml->description . " ";
            $objForm->addToForm($desc);
            // Chikis are special case
            if ($filter == "chiki") {
                // Put the chiki stuff here.
                die("chiki");
            } else {
                if ($numParams == 0) {
                    $textInput = $this->getFormSimple($xml, $filter);
                    $objForm->addToForm("<br />" . $textInput);
                } else {
                    die("PARAMS > 0");
                }
            }
            
            
            
            //Create a submit button
            $objElement = new button('submit');
            // Set the button type to submit
            $objElement->setToSubmit();
            // Use the language object to add the word save
            $objElement->setValue(' '.$this->objLanguage->languageText("mod_filtergen_insertfilter", "filtergen").' ');
            $objForm->addToForm("<br />" . $objElement->show());
            //Send back the form
            return $objForm->show();
        }
    }
    
    public function getFormSimple($xml, $filter)
    {
        $objElement = new textinput("filter_text");
        $objElement->size=70;
        return $objElement->show();
    }
    
    
}
?>