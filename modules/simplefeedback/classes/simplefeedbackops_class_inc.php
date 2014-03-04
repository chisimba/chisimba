<?php
/**
 *
 * Ops class for Simple feedback
 *
 * Operations class for the Simple Feedback Module. Presents the user interface.
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
 * @package   simplefeedback
 * @author    Derek Keats derekkeats@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
 * Database access for Simple feedback
 *
 * Database access for Simple feedback. This is a database model class
 * that provides data access to the default module table - tbl_simplefeedback_text.
*
* @package   simplefeedback
* @author    Derek Keats derekkeats@gmail.com
*
*/
class simplefeedbackops extends object
{
    
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;
    /**
     *
     * @var string Object $objUser String for the user object
     * @access public
     *
     */
    public $objUser;

    /**
    *
    * Intialiser for the simplefeedback ops class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Get an instance of the languate object
        $this->objLanguage = $this->getObject('language', 'language');
        // Instantiate the user object.
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     *
     * Get the text of the init_overview that we have in the sample database.
     *
     * @return string The text of the init_overview
     * @access public
     *
     */
    public function showForm($surveyId)
    {
        // Load required classes from htmlelements
        $this->loadClass('form','htmlelements');
        $this->loadClass('textinput','htmlelements');
        $this->loadClass('textarea','htmlelements');
        $this->loadClass ('hiddeninput', 'htmlelements');
        
        // Initialise the return string
        $ret = "";
        
        // Get the survey information
        $objSv = $this->getObject('dbsfsurveys', 'simplefeedback');
        $res = $objSv->getSurveyInfo($surveyId);
        $sTitle = $res['title'];
        $sDesc = $res['description'];
        // Create the overview as two nested divs using the DOM
        $doc = new DOMDocument('UTF-8');
        $fbInfo = $doc->createElement('div');
        $fbInfo->setAttribute('class', 'fb_info');
        $fbTitle = $doc->createElement('div');
        $fbTitle->setAttribute('class', 'fb_title');
        $fbTitle->appendChild($doc->createTextNode($sTitle));
        $fbDesc = $doc->createElement('div');
        $fbDesc->setAttribute('class', 'fb_description');
        $fbDesc->appendChild($doc->createTextNode($sDesc));
        $fbInfo->appendChild($fbTitle);
        $fbInfo->appendChild($fbDesc);
        $doc->appendChild($fbInfo);
        $info = $doc->saveHTML();
        
        // Create the form for the feedback
        $paramArray=array(
            'action'=>'save');
        $formAction=$this->uri($paramArray, 'simplefeedback');
        $objForm = new form('simplefeedback');
        $objForm->setAction($formAction);
        $objForm->displayType=3;
        
        // Add the info to the form
        $objForm->addToForm($info);
        
        // The hidden input field for survey id.
        $objHidden =  new hiddeninput ( 'surveyid', $surveyId );
        $objForm->addToForm($objHidden->show());
        
        // Populate username and email if they are logged in.
        if ($this->objUser->isLoggedIn()) {
            $name = $this->objUser->fullName();
            $email = $this->objUser->email();
        } else {
            $name = NULL;
            $email = NULL;
        }
        $objTi = new textinput('fullname', $name);
        $objTi->id='username';
        $titleLabel = $this->objLanguage->languageText("mod_simplefeedback_username",
            "simplefeedback", "Your name");
        $postTi = $titleLabel . ":<br />" . $objTi->show() . "<br />";
        $objEm = new textinput('email', $email);
        $objEm->id='email';
        $titleLabel = $this->objLanguage->languageText("mod_simplefeedback_email",
            "simplefeedback", "Your email address");
        $postEm = $titleLabel . ":<br />" . $objEm->show() . "<br />";
        
        // Add username and email to the form.
        $objForm->addToForm($postTi);
        $objForm->addToForm($postEm);
        
        // Get the questions for the survey.
        $qDb = $this->getObject('dbsfquestions', 'simplefeedback');
        $arQ = $qDb->getSurvey($surveyId);
        foreach ($arQ as $question) {
            $qNo = $question['questionno'];
            $ans = new textarea('question_' . $qNo);
            $q = $question['question'];
            $objForm->addToForm($qNo . ". " . $q . "<br />" . $ans->show() . "<br />");
        }
        
        //Add a save button
        $objButton = $this->newObject('button', 'htmlelements');
        $objButton->setIconClass("save");
        $objButton->button('save',$this->objLanguage->languageText('word_save'));
        $objButton->setToSubmit();
        $objForm->addToForm($objButton->show());
        return $objForm->show();
    }
    
    /**
     * 
     * Render all the responses to the survey
     * 
     * @param string $surveyId The surveyid of the responses to return
     * @return string The rendered results
     * @access public
     * 
     */
    public function showResults($surveyId)
    {
        $qDb = $this->getObject('dbsfanswers', 'simplefeedback');
        $arA = $qDb->getAnswers($surveyId);
        $doc = new DOMDocument('UTF-8');
        $table = $doc->createElement('table');
        $class = "odd";
        foreach ($arA as $response) {
            // Create a table row
            $tr = $doc->createElement('tr');
            
            // Add a cell to the row
            $fn = $response['fullname'];
            $td = $doc->createElement('td');
            $td->setAttribute('class', $class);
            $td->appendChild($doc->createTextNode($fn));
            $tr->appendChild($td);
            
            // Add a cell to the row
            $qno = $response['questionno'];
            $td = $doc->createElement('td');
            $td->setAttribute('class', $class);
            $td->appendChild($doc->createTextNode($qno));
            $tr->appendChild($td);
            
            // Add a cell to the row
            $q = $response['question'];
            $td = $doc->createElement('td');
            $td->setAttribute('class', $class);
            $td->appendChild($doc->createTextNode($q));
            $tr->appendChild($td);
            
            // Add a cell to the row
            $ans = $response['answer'];
            $td = $doc->createElement('td');
            $td->setAttribute('class', $class);
            $td->appendChild($doc->createTextNode($ans));
            $tr->appendChild($td);
            
            // Add the row to the table
            $table->appendChild($tr);
            
            // Convoluted odd/even
            if ($class == "odd") { 
                $class = "even";
            } else {
                $class = "odd";
            }
        }
        $doc->appendChild($table);
        return $doc->saveHTML();
    }
    
    public function showForms()
    {
        $qDb = $this->getObject('dbsfsurveys', 'simplefeedback');
        $arS = $qDb->getAll();
        $class = "odd";
        $table = $doc->createElement('table');
        foreach ($arS as $survey) {
            // Create a table row
            $tr = $doc->createElement('tr');
            
            // Add a cell to the row
            $title = $survey['title'];
            $td = $doc->createElement('td');
            $td->setAttribute('class', $class);
            $td->appendChild($doc->createTextNode($title));
            $tr->appendChild($td);
            
            // Add a cell to the row
            $userId = $survey['userid'];
            $owner = $this->objUser->fullName($userid);
            $td = $doc->createElement('td');
            $td->setAttribute('class', $class);
            $td->appendChild($doc->createTextNode($owner));
            $tr->appendChild($td);
            
            // Add the row to the table
            $table->appendChild($tr);
            
            // Convoluted odd/even
            if ($class == "odd") { 
                $class = "even";
            } else {
                $class = "odd";
            }
        }
        $doc->appendChild($table);
        return $doc->saveHTML();
    }

}
?>