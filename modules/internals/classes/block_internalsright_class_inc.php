<?php

// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_internalsright_class_inc
 *
 * @author monwabisi
 */
class block_internalsright extends Object {

    //put your code here

    var $objLanguage;
    var $objUser;
    var $objAltConfig;
    var $objDBleaves;
    var $objBlockMiddle;

    /**
     * 
     */
    public function init() {
        $this->objBlockMiddle = $this->getObject('block_internalsmiddle', 'internals');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->title = $this->objLanguage->languageText('word_internals_title', 'system');
        $this->objDBleaves = $this->getObject('dbinternals', 'internals');
    }

    public function addLeaveForm() {
        
    }

    public function show() {
        //the user ID
        $userID = $this->objUser->getUserId($this->objUser->userName());
        $objForm = $this->getObject('form', 'htmlelements');
        $requests = $this->objDBleaves->getLeaveRequests();
        $pending = 0;
        $rejected = 0;
        $approved = 0;
        foreach ($requests as $value) {
            if ($userID == $value['userid']) {
                if($value['status'] == 'pending'){
                    $pending++;
                }
                if($value['status'] == 'approved'){
                    $approved++;
                }
                if($value['status'] == 'rejected'){
                    $rejected ++;
                }
                $objForm->addToForm(str_replace('@rejected',$rejected,$this->objLanguage->languageText('phrase_rejected','system')).'<br/>'.str_replace('@approved',$approved,$this->objLanguage->languageText('phrase_approved','system')).'<br/>'.str_replace('@pending',$pending,$this->objLanguage->languageText('phrase_pending','system')) );
            }
        }
        return $objForm->show();
    }

}

?>
