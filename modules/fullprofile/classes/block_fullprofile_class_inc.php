<?php

class block_fullprofile extends object {

    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objDisplay = $this->getObject('fpdisplay', 'fullprofile');
        $this->objDbFullprofile = $this->getObject('dbfullprofile', 'fullprofile');

        //Load the htmlelements classess
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');

        $this->title = "Full profile";
    }

    function show()
    {
        $html = "";
        //Create the form
        $form = new form('searchuser',$this->uri(array('action'=>'search')));
        //$form->displayType = 4;

        //Create the search text input
        $searchInput = new textinput('searchterm');
        $searchInput->size = 12;

        $button = new button ('search', $this->objLanguage->languageText('word_search', 'system', 'Search'));
        $button->cssId = 'searchbutton';
        $button->setToSubmit();

        $searchHtml = $searchInput->show().'&nbsp;'.$button->show();

        $form->addToForm($searchHtml);

        //Create list of current users friends to view
        $userid = $this->objUser->userId();
        $friends = $this->objDbFullprofile->getRandFriends($userid);

        //Create title for friends section
        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '4';
        $title->str = $this->objLanguage->languageText('mod_fullprofile_friends', 'fullprofile');

        $objTable = $this->newObject('htmltable', 'htmlelements');

        $objTable->startRow();
        $objTable->addCell($title->show(), null, 'top', null, null, 'colspan="2"', '0');
        $objTable->endRow();

        if(is_array($friends) && count($friends)>0){
            $count = 0;
            foreach($friends as $friend){
                $fuserid = $friend['fuserid'];

                $flink = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$fuserid)).'">'.$this->objUser->getSmallUserImage($fuserid).'<br />'.$this->objUser->fullname($fuserid).'</a>';
               if($count == 0){
                    $objTable->startRow();
                    $objTable->addCell($flink, NULL, 'center', 'center');
                    $count++;
                } else if($count == 2){
                     $objTable->addCell($flink, NULL, 'center', 'center');
                     $objTable->endRow();
                     $count = 0;
                } else {
                     $objTable->addCell($flink, NULL, 'center', 'center');
                     $count++;
                }

            }
        } else {
            $objTable->startRow();
            $objTable->addCell('<span class="subdued">User has not added any friends</span>', null, 'top', null, null, 'colspan="2"', '0');
            $objTable->endRow();
        }

        //Create link to view current users profile
        $myProfileLink = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$this->objUser->userId())).'">'.$this->objLanguage->languageText('mod_fullprofile_viewmyprofile', 'fullprofile').'</a>';

        $html .= $form->show();

        $html .= $objTable->show();

        $html .= '<p>&nbsp;</p>';
        
        $html .= $myProfileLink;
        
        return $html;
    }

}
?>
