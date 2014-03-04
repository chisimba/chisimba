<?php

/**
 * Triplestore user interface
 * 
 * Provides elements of the user interface for working with data stored in
 * the Chisimba framework triplestore. A triplestore is a purpose-built database
 * for the storage and retrieval of Resource Description Framework (RDF) metadata.
 * A triplestore is optimized for the storage and retrieval of many short
 * statements called triples, in the form of subject-predicate-object. This
 * class creates interfaces for editing and adding triples.
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
 * @category  chisimba
 * @package   triplestore
 * @author    Derek Keats <derek.keats@wits.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: triplesubject_class_inc.php 16737 2010-02-07 13:51:44Z charlvn $
 * @link      http://avoir.uwc.ac.za/
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
// end security check

/**
 *
 * Triplestore user interface
 *
 * Provides elements of the user interface for working with data stored in
 * the Chisimba framework triplestore. A triplestore is a purpose-built database
 * for the storage and retrieval of Resource Description Framework (RDF) metadata.
 * A triplestore is optimized for the storage and retrieval of many short
 * statements called triples, in the form of subject-predicate-object. This
 * class creates interfaces for editing and adding triples.
 *
 * 
 * @category  chisimba
 * @package   triplestore
 * @author    Derek Keats <derek.keats@wits.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: triplesubject_class_inc.php 16737 2010-02-07 13:51:44Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * 
 */
class tripleui extends object
{
    /**
     * Instance of the dbtriplestore class of the triplestore module.
     *
     * @access protected
     * @var    object
     */
    protected $objTriplestore;

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;

    /**
     * Initialise the instance of the triplesubject class.
     *
     * @access public
     */
    public function init()
    {
        $this->objTriplestore = $this->getObject('dbtriplestore', 'triplestore');
        //Load the form class
        $this->loadClass('form','htmlelements');
        //Load the textinput class
        $this->loadClass('textinput','htmlelements');
        //Load the label class
        $this->loadClass('label','htmlelements');
        // Load the button object
        $this->loadClass('button', 'htmlelements');
        // Load the language object
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
    }

    public function buildEditForm($mode, $id="", $subject="", $predicate="", $tripobject="")
    {
        $subLabel = $this->getLabelSubject();
        $predLabel = $this->getLabelPredicate();
        $objectLabel = $this->getLabelObject();
        $subject = $this->getInputSubject($subject);
        $predicate = $this->getInputPredicate($predicate);
        $tripObject = $this->getInputObject($tripobject);
        $formAction = $this->getFormAction($mode);
        if ($id !== "") {
            $idFld = $this->getHiddenId($id);
        } else {
            $idFld=NULL;
        }
        $table = "<table><tr><td>$subLabel</td><td>$subject</td></tr>"
          . "<tr><td>$predLabel</td><td>$predicate</td></tr>"
          . "<tr><td>$objectLabel</td><td>$tripObject</td></tr>"
          . "</table>";



        // Create and instance of the form class.
        $objForm = new form('triplestore');
        // Set the form action.
        $objForm->setAction($formAction);
        $objForm->addToForm($table);
        $objForm->addToForm($this->getSaveButton());
        $ret = "<div class='standard_form'>"
          . $objForm->show()
          . "</div>";
        return $ret;
        
    }

    /**
     * Get the input element for the subject of the triple
     *
     * @return string a formatted input box with label
     * @access public
     *
     */
    public function getInputSubject($subject="")
    {
        //Create an element for the input of subject
        $objElement = new textinput ("subject");
        //Set the value of the element to $subject
        $objElement->setValue($subject);
        // Render the subject element.
        return $objElement->show();
    }

    /**
     * Get the label for the subject of the triple
     *
     * @return string a formatted label
     * @access public
     *
     */
    public function getLabelSubject()
    {
        // Create label for the input of subject.
        $subLabel = new label($this->objLanguage->languageText("mod_triplestore_subject",'triplestore'), "label_subject");
        // Render the label for the subject element.
        return $subLabel->show();
    }

    /**
     * Get the label for the predicate of the triple
     *
     * @return string a formatted input box with label
     * @access public
     *
     */
    public function getInputPredicate($predicate="")
    {
        //Create an element for the input of subject
        $objElement = new textinput ("predicate");
        //Set the value of the element to $predicate
        $objElement->setValue($predicate);
        // Render the subject element.
        return $objElement->show();
    }

    /**
     * Get the label for the predicate of the triple
     *
     * @return string a formatted label
     * @access public
     *
     */
    public function getLabelPredicate()
    {
        // Create label for the input of subject.
        $subLabel = new label($this->objLanguage->languageText("mod_triplestore_predicate",'triplestore'), "label_predicate");
        // Render the label for the subject element.
        return $subLabel->show();
    }

    /**
     * Get the input element for the object of the triple. Note that we
     * have to use tripobject for the object because object is a reserved
     * word.
     *
     * @return string a formatted input box with label
     * @access public
     *
     */
    public function getInputObject($tripobject="")
    {
        //Create an element for the input of tripobject
        $objElement = new textinput ("tripobject");
        //Set the value of the element to $tripobject
        $objElement->setValue($tripobject);
        // Render the tripobject element.
        return $objElement->show();
    }

    /**
     * Get the label for the object of the triple
     *
     * @return string a formatted label
     * @access public
     *
     */
    public function getLabelObject()
    {
        // Create label for the input of subject.
        $subLabel = new label($this->objLanguage->languageText("mod_triplestore_object",'triplestore'), "label_object");
        // Render the label for the subject element.
        return $subLabel->show();
    }

    /**
     * Get a hidden field for the primary key during edit
     *
     * @return string The rendered hidden field
     * @access public
     *
     */
    public function getHiddenId($id)
    {
        //Create an element for the hidden text input
        $objElement = new textinput("id");
        //Set the value to the primary keyid
        $objElement->setValue($id);
        //Set the field type to hidden for the primary key
        $objElement->fldType="hidden";
        // render the hidden element
        return $objElement->show();
    }

     /**
     * Get a save button
     *
     * @return string The rendered save button
     * @access public
     *
     */
    public function getSaveButton()
    {
        // Create a submit button
        $objElement = new button('submit');
        $objElement->setIconClass("save");
        // Set the button type to submit
        $objElement->setToSubmit();
        // Use the language object to add the word save
        $objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
        return $objElement->show();
    }

    /**
    * Get the form action
    *
    * @return string The rendered form action
    * @access public
    *
    */
    public function getFormAction($mode=FALSE)
    {
        if (!$mode) {
            $mode = $this->getParam("mode", "add");
        }
        $paramArray=array(
          'action'=>'save',
          'mode'=>$mode);
        return $this->uri($paramArray);
    }

    /**
    * Get the triples that reference my username
    *
    * @return string The rendered form action
    * @access public
    *
    */
    public function getMyTriples($page, $pageSize)
    {
        $myUserId = $this->objUser->userId();
        $myUserName = $this->objUser->userName($myUserId);
        // Retrieve all the triples out of the triplestore.
        $triples = $this->objTriplestore->getTriplesPaginated($myUserName, $page, $pageSize);
        // Multiling this .........................................................................................
        $sub = "subject";
        $pred = "predicate";
        $obj = "object";
        $dt = "date";
        $ret = "<br /><div class='ingrid'>\n\n<table  id=\"table1\">\n";
        if ($page == 1) {
            $ret .= "<thead>\n\n<tr><th>$sub</th><th>$pred</th><th>$obj</th><th>$dt</th>\n\n</tr>\n</thead>\n";
        }
        $ret .= "<tbody>\n";
        foreach ($triples as $triple) {
            $id = $triple['id'];
            $ret .= "<tr><td>" . $triple['subject']
              ."</td><td><span class='edit' id='predicate|$id'>" . $triple['predicate']
              . "</span></td><td><span class='edit' id='subject|$id'>" . $triple['object']
              . "</span></td><td>" . $triple['date'] . "</td></tr>\n";
        }
        $ret .= "</tbody>\n</table>\n\n</div>";
        // Now make it all editable
        $objTh = & $this->getObject('jqeditablehelper', 'jqeditable');
        $objTh->loadJs();
        $arrayParams =  array('indicator' => 'Saving...',
            'tooltip' => 'Click to edit...');
        $targetUrl = $this->uri(array('action' => 'saveinline'), 'triplestore');
        $targetUrl = str_replace('&amp;', '&', $targetUrl);
        $objTh->buildReadyFunction($arrayParams, $targetUrl);
        $objTh->loadReadyFunction();
        return $ret;
    }
}
?>