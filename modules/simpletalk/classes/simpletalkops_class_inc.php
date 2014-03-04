<?php
/**
 *
 * Basic ops class for simpletalk
 *
 * Basic ops class for simpletalk, a simple module to allow a user to Submit a 
 * conference talk and have it accepted or rejected. 
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
 * @package   simpletalk
 * @author    Derek Keats derek@dkeats.com
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
 * Basic ops class for simpletalk
 *
 * Basic ops class for simpletalk, a simple module to allow a user to Submit a 
 * conference talk and have it accepted or rejected.
*
* @package   simpletalk
* @author    Derek Keats derek@dkeats.com
*
*/
class simpletalkops extends object
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
    * Intialiser for the simpletalk database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Get an instance of the languate object.
        $this->objLanguage = $this->getObject('language', 'language');
        // Instantiate the user object.
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     *
     * Get the text of the init_overview that we have in the sample database.
     *
     * @param string $mode The mode, either edit or add
     * @return string The text of the init_overview
     * @access public
     *
     */
    public function showForm($mode=FALSE)
    {
        // Set edit or add as the mode.
        if (!$mode) {
            $mode = $this->getParam('mode', 'add');
        }
        
        // Load required classes from htmlelements.
        $this->loadClass('form','htmlelements');
        $this->loadClass('textinput','htmlelements');
        $this->loadClass('textarea','htmlelements');
        $this->loadClass('dropdown','htmlelements');
        
        // Create the form for the talk submission.
        $paramArray=array(
            'action'=>'save',
            'mode' => $mode);
        $formAction = $this->uri($paramArray, 'simpletalk');
        $formAction =  str_replace("&amp;", "&", $formAction);
        $objForm = new form('simpletalk');
        $objForm->setAction($formAction);
        $objForm->displayType=3;
        
        // Set default values.
        $title = NULL;
        $authors = NULL;
        $duration = NULL;
        $track = NULL;
        $abstract = NULL;
        $requirements = NULL;
        if ($this->objUser->isLoggedIn()) {
            $emailAdr = $this->objUser->email();
        } else {
            $emailAdr = NULL;
        }
        
        
        // Check for edit and load values
        if ($mode == 'edit') { 
            if ($this->objUser->isLoggedIn()) {
            $userId = $this->objUser->userId();
                $id = $this->getParam('id', FALSE);
                if ($id) {
                    $objDb = $this->getObject('dbsimpletalk', 'simpletalk');
                    $abstractItem = $objDb->getAbstractForEdit($id);
                    if ($abstractItem) {
                        $title = $abstractItem['title'];
                        $authors = $abstractItem['authors'];
                        $duration = $abstractItem['duration'];
                        $track = $abstractItem['track'];
                        $abstract = $abstractItem['abstract'];
                        $requirements = $abstractItem['requirements'];
                        $emailAdr = $abstractItem['emailadr'];

                        // Add the hidden id to the form for save
                        $this->loadClass ('hiddeninput', 'htmlelements');
                        $objHidden =  new hiddeninput ( 'id', $id);
                        $objForm->addToForm($objHidden->show());
                    }
                }
            }
        }
        
        // Add the title to the form.
        $objTitle = new textinput('title', $title);
        $objTitle->id='simpletalk_title';
        $titleLabel = $this->objLanguage->languageText("mod_simpletalk_title",
            "simpletalk", "Title of your proposed talk");
        $titleShow = $titleLabel . ":<br />" . $objTitle->show() . "<br />";
        $objForm->addToForm($titleShow);
        
        // Add the authors to the form.
        $objAuth = new textinput('authors', $authors);
        $objAuth->id='simpletalk_authors';
        $authLabel = $this->objLanguage->languageText("mod_simpletalk_authors",
            "simpletalk", "List of authors, in the format SURNAME, INITIAL; SURNAME, INITIAL");
        $authShow = $authLabel . ":<br />" . $objAuth->show() . "<br />";
        $objForm->addToForm($authShow);
        
        // Add the email address to the form.
        $objEm = new textinput('emailadr', $emailAdr);
        $objEm->id='simpletalk_emailadr';
        $emLabel = $this->objLanguage->languageText("mod_simpletalk_em_sm",
            "simpletalk", "Email address");
        $emShow = $emLabel . ":<br />" . $objEm->show() . "<br />";
        $objForm->addToForm($emShow);
        
        // Add the talk type dropdown to the form.
        $shortTalk = $this->objLanguage->languageText("mod_simpletalk_short",
            "simpletalk", "Short talk (10-25 minutes)");
        $longTalk = $this->objLanguage->languageText("mod_simpletalk_long",
            "simpletalk", "Full talk (45 minutes)");
        $objDd = new dropdown('duration');
        $dbDd = $this->getObject('dbdurations', 'simpletalk');
        $rsDd = $dbDd->getDurations();
        foreach ($rsDd as $item) {
            $objDd->addOption($item['duration'], $item['duration_label']);
        }
        if ($mode == 'edit') {
            $objDd->setSelected($duration);
        }
        $ddLabel = $this->objLanguage->languageText("mod_simpletalk_duration",
            "simpletalk", "Talk duration");
        $ddShow = $ddLabel . ":<br />" . $objDd->show() . "<br />";
        $objForm->addToForm($ddShow);
        
        // Add the talk track dropdown to the form.
        $objDd = new dropdown('track');
        $dbDd = $this->getObject('dbtracks', 'simpletalk');
        $rsDd = $dbDd->getTracks();
        foreach ($rsDd as $item) {
            $objDd->addOption($item['track'], $item['track_label']);
        }
        if ($mode == 'edit') {
            $objDd->setSelected($track);
        }
        $ddLabel = $this->objLanguage->languageText("mod_simpletalk_track",
            "simpletalk", "Talk track");
        $ddShow = $ddLabel . ":<br />" . $objDd->show() . "<br />";
        $objForm->addToForm($ddShow);
        
        // Add the abstract to the form.
        $abstractBx = new textarea('abstract');
        $abstractLabel = $this->objLanguage->languageText("mod_simpletalk_abstract",
            "simpletalk", "Talk abstract");
        $abstractBx->value=$abstract;
        $absShow = $abstractLabel . ":<br />" . $abstractBx->show() . "<br />";
        $objForm->addToForm($absShow);
        
        // Add the special requirements to the form.
        $req = new textarea('requirements');
        $req->value = $requirements;
        $reqLabel = $this->objLanguage->languageText("mod_simpletalk_requirements",
            "simpletalk", "Indicate any special requirements you may have for your presentation");
        $reqShow = $reqLabel . ":<br />" . $req->show() . "<br />";
        $objForm->addToForm($reqShow);
        
        // Add a save button.
        $objButton = $this->newObject('button', 'htmlelements');
        $objButton->button('saveo',$this->objLanguage->languageText('word_save'));
        $objButton->sexyButtons=TRUE;
        $objButton->setToSubmit();
        $objForm->addToForm($objButton->show());
        
        // Sent back the form in a wrapper div.
        return '<div class="simpletalk_wrap">' . $objForm->show() . '</div>';
    }
    
    /**
     * 
     * Show all submitted abstracts. This is for a small conference or event,
     * so there is no pagination.
     * 
     * @return string List of abstracts or permission message.
     * @access public
     * 
     * 
     */
    public function showAllAbstracts()
    {
        if ($this->checkManagementRights()) {
            return $this->listAllAbstracts();
        } else {
            if ($this->objUser->isLoggedIn()) {
                // Return abstracts submitted by the user if any.
                return $this->listMyAbstracts($this->objUser->userId());
            } else {
                // Tell them they are not logged in
                return $this->objLanguage->languageText("mod_simpletalk_noright",
                    "simpletalk", "You are not logged in.");
            }

        }
    }
    
    /**
     * 
     * List all the abstracts
     * 
     * @return string Formatted table output
     * @access private
     * 
     */
    private function listAllAbstracts()
    {
        $objDb = $this->getObject('dbsimpletalk', 'simpletalk');
        $abstractAr = $objDb->getAbstracts();
        return $this->renderAbstracts($abstractAr);
    }
    
    /**
     * 
     * List abstracts by a given user
     * 
     * @return string Formatted table output
     * @access private
     * 
     */
    private function listMyAbstracts($userId)
    {
        $objDb = $this->getObject('dbsimpletalk', 'simpletalk');
        $whereClause = " WHERE tbl_simpletalk_abstracts.userId = '{$userId}' ";
        $abstractAr = $objDb->getAbstracts($whereClause);
        return $this->renderAbstracts($abstractAr);
    }
    
    /**
     * 
     * Render the array of abstracts for display
     * 
     * @param string Array $abstractAr an array of abstracts from the data
     * 
     * @return type string Formatted abstracts
     * @Access private
     */
    private function renderAbstracts($abstractAr)
    {
        $doc = new DOMDocument('UTF-8');
        $tbl = $doc->createElement('table');
        //Initialize the odd/even counter.
        $rowcount = 0;
        // The edit icon
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $objIcon->setIcon('edit', 'png');
        $edIcon = $objIcon->show();
        // Human date functions
        $objDd = $this->getObject('translatedatedifference', 'utilities');
        foreach ($abstractAr as $abstract) {
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
            
            // The title.
            $label = $this->objLanguage->languageText("mod_simpletalk_tl_sh",
            "simpletalk", "Title");
            $tr = $doc->createElement('tr');
            $tr->setAttribute('class', $oddOrEven);
            $td = $doc->createElement('td');
            $td->appendChild($doc->createTextNode($label));
            $tr->appendChild($td);
            $td = $doc->createElement('td');
            $h3 = $doc->createElement('h3');
            $title = $abstract['title'];
            $h3->appendChild($doc->createTextNode($title));
            $td->appendChild($h3);
            $tr->appendChild($td);
            $tbl->appendChild($tr);
             
            // The authors.
            $label = $this->objLanguage->languageText("mod_simpletalk_au_sh",
            "simpletalk", "Author(s)");
            $tr = $doc->createElement('tr');
            $tr->setAttribute('class', $oddOrEven);
            $td = $doc->createElement('td');
            $td->appendChild($doc->createTextNode($label));
            $tr->appendChild($td);
            $td = $doc->createElement('td');
            $authors = $abstract['authors'];
            $td->appendChild($doc->createTextNode($authors));
            $tr->appendChild($td);
            $tbl->appendChild($tr);
            
            // The email address.
            $label = $this->objLanguage->languageText("mod_simpletalk_em_sh",
            "simpletalk", "Email address");
            $tr = $doc->createElement('tr');
            $tr->setAttribute('class', $oddOrEven);
            $td = $doc->createElement('td');
            $td->appendChild($doc->createTextNode($label));
            $tr->appendChild($td);
            $td = $doc->createElement('td');
            $authors = $abstract['emailadr'];
            $td->appendChild($doc->createTextNode($authors));
            $tr->appendChild($td);
            $tbl->appendChild($tr);
            
            // The submitter.
            $label = $this->objLanguage->languageText("mod_simpletalk_sb_sh",
            "simpletalk", "Submitted by");
            $tr = $doc->createElement('tr');
            $tr->setAttribute('class', $oddOrEven);
            $td = $doc->createElement('td');
            $td->appendChild($doc->createTextNode($label));
            $tr->appendChild($td);
            $td = $doc->createElement('td');
            $subber = $abstract['userid'];
            $subBy = $this->objUser->fullName($subber);
            $subEm = $this->objUser->email($subber);
            $subBy = $subBy . "(" . $subEm . ")";
            $td->appendChild($doc->createTextNode($subBy));
            $tr->appendChild($td);
            $tbl->appendChild($tr);
            
            // The submit date.
            $label = $this->objLanguage->languageText("mod_simpletalk_dt_sh",
            "simpletalk", "Date submitted");
            $tr = $doc->createElement('tr');
            $tr->setAttribute('class', $oddOrEven);
            $td = $doc->createElement('td');
            $td->appendChild($doc->createTextNode($label));
            $tr->appendChild($td);
            $td = $doc->createElement('td');
            $dt = $abstract['datecreated'];
            $hDate = $objDd->getDifference($dt);
            $td->appendChild($doc->createTextNode($dt . " (" . $hDate . ")"));
            $tr->appendChild($td);
            $tbl->appendChild($tr);
            
            // The talk duration.
            $label = $this->objLanguage->languageText("mod_simpletalk_du_sh",
            "simpletalk", "Duration");
            $tr = $doc->createElement('tr');
            $tr->setAttribute('class', $oddOrEven);
            $td = $doc->createElement('td');
            $td->appendChild($doc->createTextNode($label));
            $tr->appendChild($td);
            $td = $doc->createElement('td');
            $duration = $abstract['duration_label'];
            $td->appendChild($doc->createTextNode($duration));
            $tr->appendChild($td);
            $tbl->appendChild($tr);
            
            // The talk track.
            $label = $this->objLanguage->languageText("mod_simpletalk_tr_sh",
            "simpletalk", "Track");
            $tr = $doc->createElement('tr');
            $tr->setAttribute('class', $oddOrEven);
            $td = $doc->createElement('td');
            $td->appendChild($doc->createTextNode($label));
            $tr->appendChild($td);
            $td = $doc->createElement('td');
            $track = $abstract['track_label'];
            $td->appendChild($doc->createTextNode($track));
            $tr->appendChild($td);
            $tbl->appendChild($tr);
            
            // The abstract.
            $label = $this->objLanguage->languageText("mod_simpletalk_ab_sh",
            "simpletalk", "Abstract");
            $tr = $doc->createElement('tr');
            $tr->setAttribute('class', $oddOrEven);
            $td = $doc->createElement('td');
            $td->setAttribute('valign', 'top');
            $td->appendChild($doc->createTextNode($label));
            $tr->appendChild($td);
            $td = $doc->createElement('td');
            $abstractTxt = $abstract['abstract'];
            $td->appendChild($doc->createTextNode($abstractTxt));
            $tr->appendChild($td);
            $tbl->appendChild($tr);
            
            // The requirements.
            $label = $this->objLanguage->languageText("mod_simpletalk_rq_sh",
            "simpletalk", "Requirements");
            $tr = $doc->createElement('tr');
            $tr->setAttribute('class', $oddOrEven);
            $td = $doc->createElement('td');
            $td->setAttribute('valign', 'top');
            $td->appendChild($doc->createTextNode($label));
            $tr->appendChild($td);
            $td = $doc->createElement('td');
            $requirements = $abstract['requirements'];
            $td->appendChild($doc->createTextNode($requirements));
            $tr->appendChild($td);
            $tbl->appendChild($tr);
            
            // Optional edit / delete link
            $ownerId = $abstract['userid'];
            if ($this->checkManagementRights() ||
                $this->objUser->userId() == $ownerId) {
                $tr = $doc->createElement('tr');
                $tr->setAttribute('class', $oddOrEven);
                $td = $doc->createElement('td');
                $td->setAttribute('colspan', '2');
                $id = $abstract['id'];
                $editUrl = $this->uri(
                    array(
                        'mode' => 'edit',
                        'id' => $id
                    ), 'simpletalk');
                $editUrl = str_replace("&amp;", "&", $editUrl);
                $a = $doc->createElement('a');
                $a->setAttribute('href', $editUrl);
                $frag = $doc->createDocumentFragment();
                $frag->appendXML($edIcon);
                $a->appendChild($frag);

                $td->appendChild($a);
                $tr->appendChild($td);
                $tbl->appendChild($tr);
            }

            // Set rowcount for bitwise determination of odd or even
            $rowcount = ($rowcount == 0) ? 1 : 0;
        }
        $wrapper = $doc->createElement('div');
        $wrapper->setAttribute('class', 'simpletalk_wrap');
        $wrapper->appendChild($tbl);
        $doc->appendChild($wrapper);
        return $doc->saveHTML();
        
    }
    
    
    /**
     *
     * Check if a user should be able to manage abstracts based on
     * is admin or membership of the SimpleTalkReviewers group
     * 
     * @return boolean If they have rights or not 
     * @access public
     */
    public function checkManagementRights()
    {
        $ret=FALSE;
        $userId = $this->objUser->userId();
        // Admins can make blogs
        $objGa = $this->getObject('gamodel','groupadmin');
        $groupId = $objGa->getId("SimpleTalkReviewers");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $edGroup = $objGroupOps->isGroupMember($groupId, $userId);     
        if ($this->objUser->isLoggedIn()) {
            if ($this->objUser->isAdmin() || $edGroup ) {
                $ret = TRUE;
            }
        }
        return $ret;
    }
}
?>