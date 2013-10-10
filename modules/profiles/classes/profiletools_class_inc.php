<?php
/**
* profiletools class extends object
* @package profiletools
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Profiletools class provide utility functions for the profiles module
*
* @author Megan Watson
* @copyright (c) 2007 University of the Western Cape
* @version 0.1
*/

class profiletools extends object
{
    /**
    * Constructor method
    */
    public function init()
    {
        try{
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objLangCode = $this->getObject('languagecode', 'language');
            $this->objCountries = $this->getObject('countries', 'utilities');
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->userId();
            
            $this->featureBox = $this->newObject('featurebox', 'navigation');
            $this->editor = $this->newObject('htmlarea', 'htmlelements');
            
            $this->loadClass('htmlheading', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('radio', 'htmlelements');
            $this->loadClass('link', 'htmlelements');    
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
    }

    /**
    * Method to get the contents for the right column
    *
    * @access public
    * @return string html
    */
    public function getRightSide()
    {        
        $str = $this->showSearch();
        
        $lnViewList = $this->objLanguage->languageText('phrase_viewprofilelist');
        $objLink = new link($this->uri(''));
        $objLink->link = $lnViewList;
        $str .= '<p>'.$objLink->show().'</p>';

        return $str;
    }
    
    
    /**
    * Method to get the contents for the right column
    *
    * @access public
    * @return string html
    */
    public function getViewRightSide($data)
    {        
        $str = $this->showDetails($data);

        $lnViewList = $this->objLanguage->languageText('phrase_viewprofilelist');
        $objLink = new link($this->uri(''));
        $objLink->link = $lnViewList;
        $str .= '<p>'.$objLink->show().'</p>';

        return $str;
    }

    
    /**
    * Method to create a search block for searching profiles
    *
    * @access private
    * @return string html
    */
    private function showSearch()
    {
        $head = $this->objLanguage->languageText('word_search');
        $lbFirstname = $this->objLanguage->languageText('phrase_firstname');
        $lbSurname = $this->objLanguage->languageText('word_surname');
        $lbUsername = $this->objLanguage->languageText('word_username');
        
        $objInput = new textinput('user');
        $str = $objInput->show();
        
        $objRadio = new radio('type');
        $objRadio->addOption('firstname', '&nbsp;&nbsp;'.$lbFirstname);
        $objRadio->addOption('surname', '&nbsp;&nbsp;'.$lbSurname);
        $objRadio->addOption('username', '&nbsp;&nbsp;'.$lbUsername);
        $objRadio->setSelected('surname');
        $objRadio->setBreakSpace('<br />');
        $str .= '<p>'.$objRadio->show().'</p>';
        
        $objButton = new button('search', $head);
        $objButton->setToSubmit();
        $str .= '<p>'.$objButton->show().'</p>';
        
        $objForm = new form('searchusers', $this->uri(array('action' => 'dosearch')));
        $objForm->addToForm($str);
        
        return $this->featureBox->show($head, $objForm->show());
    }
    
    /**
    * Method to create a block displaying the users details
    * 
    * @access public
    * @return string html
    */
    public function showDetails($data)
    {
        $lbEmail = $this->objLanguage->languageText('phrase_emailaddress');
        $lbCell = $this->objLanguage->languageText('phrase_cellnumber');
        $lbCountry = $this->objLanguage->languageText('word_country');
        $lbGender = $this->objLanguage->languageText('word_gender');
        $lbFemale = $this->objLanguage->languageText('word_female');
        $lbMale = $this->objLanguage->languageText('word_male');
        
        $str = $this->objUser->getUserImage($data['userid']);
        
        $name = $data['firstname'].' '.$data['surname'];
        $gender = ($data['sex'] == 'M') ? $lbMale : $lbFemale;
        $country = $this->objLangCode->getName($data['country']);
        
        $str .= '<p>'.$lbGender.': '.$gender;
        $str .= '<br />'.$lbEmail.': '.$data['emailaddress'];
        $str .= !empty($data['cellnumber']) ? '<br />'.$lbCell.': '.$data['cellnumber'] : '';
        $str .= '<br /><br />'.$lbCountry.': '.$country.'&nbsp;&nbsp;'.$this->objCountries->getCountryFlag($data['country']);
        $str .= '</p>';
        
//        $str .= $this->objCountries->getCountryFlag($data['country']);
        
        return $this->featureBox->show($name, $str);
    }
    
    /**
    * Method to display a form for creating or editing a profile
    *
    * @access public
    * @param array $data the profile data
    * @return string html
    */
    public function editProfile($data = NULL)
    {
        $head = $this->objLanguage->languageText('mod_profiles_createprofile', 'profiles');
        $head2 = $this->objLanguage->languageText('mod_profiles_editprofile', 'profiles');
        $btnSave = $this->objLanguage->languageText('word_save');
        $btnCancel = $this->objLanguage->languageText('word_cancel');
        
        $objHead = new htmlheading();
        $objHead->str = (empty($data)) ? $head : $head2;
        $objHead->type = 1;
        $str = $objHead->show();
        
        $profile = isset($data['profile']) ? $data['profile'] : '';
        $this->editor->init('profile', $profile);
        $this->editor->setDefaultToolBarSetWithoutSave();
        $formStr = $this->editor->showFCKEditor();
        
        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show();
        
        $objButton = new button('cancel', $btnCancel);
        $objButton->setToSubmit();
        $formStr .= '&nbsp;&nbsp;&nbsp;'.$objButton->show().'</p>';
        
        if(isset($data['prid']) && !empty($data['prid'])){
            $objInput = new textinput('id', $data['prid'], 'hidden');
            $formStr .= $objInput->show();
        }
        
        $objForm = new form('saveprofile', $this->uri(array('action' => 'saveprofile')));
        $objForm->addToForm($formStr.'<br />');
        $str .= $objForm->show();
        
        return $str;
    }
    
    /**
    * Method to display a profile
    *
    * @access public
    * @param array $data The profile data
    * @return string html
    */
    public function viewProfile($data)
    {
//        $lnViewList = $this->objLanguage->languageText('phrase_viewprofilelist');
        
        $name = $data['firstname'].' '.$data['surname'];
        $str = $data['profile'];
        
//        $objLink = new link($this->uri(''));
//        $objLink->link = $lnViewList;
//        $str .= '<p>'.$objLink->show().'</p>';
        
        return $this->featureBox->showContent($name, $str);
    }
    
    /**
    * Method to display the list of profiles
    *
    * @access public
    * @param array $data the list of profiles
    * @return string html
    */
    public function listProfiles($data = NULL)
    {   
        $head = $this->objLanguage->languageText('word_profiles');
        $lbView = $this->objLanguage->languageText('phrase_viewprofile');
        $hdTitle = $this->objLanguage->languageText('word_title');
        $hdName = $this->objLanguage->languageText('word_name');
        $hdCountry = $this->objLanguage->languageText('word_country');
        $lbNoProfiles = $this->objLanguage->languageText('mod_profiles_noprofilesavailable', 'profiles');
        $lbCreate = $this->objLanguage->languageText('mod_profiles_createprofile', 'profiles');
        $lbEdit = $this->objLanguage->languageText('mod_profiles_editprofile', 'profiles');
        
        $objHead = new htmlheading();
        $objHead->str = $head;
        $objHead->type = 1;
        $str = $objHead->show();
        
        //echo '<pre>'; print_r($data); echo '</pre>';
        
        $create = TRUE;
        if(!empty($data)){
            $objTable = new htmltable();
            $objTable->cellspacing = '2';
            $objTable->cellpadding = '5';
            $class = 'even';
            
            $hdArr = array();
            $hdArr[] = '';
            $hdArr[] = $hdTitle;
            $hdArr[] = $hdName;
            $hdArr[] = $hdCountry;
            $hdArr[] = '';
            
            $objTable->addHeader($hdArr);
            
            foreach($data as $item){
                $class = ($class == 'even') ? 'odd' : 'even';
                $objTable->row_attributes = " onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className=''; \" class ='{$class}'";
                
                if($item['userid'] == $this->userId){
                    $create = FALSE;
                }
                
                $row = array();
                // get the user image (thumbnail)
                $image = $this->objUser->getSmallUserImage($item['userid']);
                
                // link to view profile
                $links = '';
                if(!empty($item['profile'])){
                    $objLink = new link($this->uri(array('action' => 'viewprofile', 'userid' => $item['userid'])));
                    $objLink->link = $lbView;
                    $links = $objLink->show();
                }
                
                $row[] = $image;
                $row[] = $item['title'];
                $row[] = $item['firstname'].' '.$item['surname'];
                $row[] = $this->objCountries->getCountryFlag($item['country']).'&nbsp;&nbsp;'.$this->objLangCode->getName($item['country']);
                $row[] = $links;
                
                $objTable->addRow($row, $class);
            }
            $str .= $objTable->show();
        }else{
            $str .= '<p class="noRecordsMessage">'.$lbNoProfiles.'</p>';
        }
        
        $objLink = new link($this->uri(array('action' => 'editprofile')));
        if($create){
            $objLink->link = $lbCreate;
        }else{
            $objLink->link = $lbEdit;
        }
        $str .= '<p>'.$objLink->show().'</p>';
        
        return $str;
    }
}
?>