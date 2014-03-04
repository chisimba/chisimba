<?php
class edit extends object {


    /**
     * Initialises classes to be used
     */
    public function init() {
        $this->loadClass('link','htmlelements');
        $this->loadClass('htmltable','htmlelements');
        $this->loadClass('htmlheading','htmlelements');
        $this->objLanguage = $this->getObject("language", "language");
        $this->objUser = $this->getObject("user","security");
    }

    /**
     * Builds the form to display the gifts that are present in the database.
     * The currently logged in user can only view/edit the gifts donated to them.
     * @param array $data
     * @param boolean $archived
     * @return string
     */
    public function getResults($data,$id) {

        $this->objCancelButton = new button('cancel');
        $this->objCancelButton->setValue($this->objLanguage->languageText("mod_addedit_btnCancel","gift"));
        $this->objCancelButton->setOnClick("window.location='".$this->uri(NULL)."';");

        $editHeading = $this->objLanguage->languageText('mod_edit_viewAllGifts','gift');
        $editHeading .= " ".$this->objUser->fullName()."<br><br>";
        $title = new htmlheading($editHeading,1);

        $recentTable = new htmltable();
        $recentTable->cellspacing = 5;
        $recentTable->startRow();
        $recentTable->addHeaderCell($this->objLanguage->languageText('mod_addedit_donor','gift'),'15%');
        $recentTable->addHeaderCell($this->objLanguage->languageText("mod_addedit_receiver","gift"),'15%');
        $recentTable->addHeaderCell($this->objLanguage->languageText("mod_addedit_giftname","gift"),'15%');
        $recentTable->addHeaderCell($this->objLanguage->languageText("mod_addedit_description","gift"),'40%');
        $recentTable->addHeaderCell($this->objLanguage->languageText("mod_addedit_value","gift"),'15%');
        $recentTable->endRow();

        $previousTable = new htmltable();
        $previousTable->cellspacing = 5;
        $previousTable->startRow();
        $previousTable->addHeaderCell($this->objLanguage->languageText('mod_addedit_donor','gift'),'15%');
        $previousTable->addHeaderCell($this->objLanguage->languageText("mod_addedit_receiver","gift"),'15%');
        $previousTable->addHeaderCell($this->objLanguage->languageText("mod_addedit_giftname","gift"),'15%');
        $previousTable->addHeaderCell($this->objLanguage->languageText("mod_addedit_description","gift"),'40%');
        $previousTable->addHeaderCell($this->objLanguage->languageText("mod_addedit_value","gift"),'15%');
        $previousTable->endRow();

        $objIcon = $this->newObject('geticon','htmlelements');
        $objIcon->setIcon('edit');
        $objIcon->title = $this->objLanguage->languageText('mod_linkedit_edit','gift');
        $editIcon = $objIcon->show();

        $recent_Counter = 0;
        $previous_Counter = 0;
        foreach ($data as $info) {
            $donor = $info['donor'];
            $recipient = $info['recipient'];
            $giftname = $info['giftname'];
            $description = $info['description'];
            $value = $info['value'];
            $editLink   = new link($this->uri(array("action"=>"edit","id"=>$info['id'])));
            $editLink->link = $editIcon;

            if($info['id'] != $id) {
                $previousTable->startRow();
                $previousTable->addCell($donor);
                $previousTable->addCell($recipient);
                $previousTable->addCell($giftname);
                $previousTable->addCell($description);
                $previousTable->addCell($value);
                $previousTable->addCell($listed);
                $previousTable->addCell($editLink->show());
                $previousTable->endRow();
                $previous_Counter++;
            }
            else {
                $recentTable->startRow();
                $recentTable->addCell($donor);
                $recentTable->addCell($recipient);
                $recentTable->addCell($giftname);
                $recentTable->addCell($description);
                $recentTable->addCell($value);
                $recentTable->addCell($listed);
                $recentTable->addCell($editLink->show());
                $recentTable->endRow();
                $recent_Counter++;
            }
        }

        if ($previous_Counter == 0) {
            $previousTable->startRow();
            $previousTable->addCell($this->objLanguage->languageText('mod_edit_NoResults','gift'),'','','','','colspan="5"');
            $previousTable->endRow();
        }
        if ($recent_Counter == 0) {
            $recentTable->startRow();
            $recentTable->addCell($this->objLanguage->languageText('mod_edit_NoResults','gift'),'','','','','colspan="5"');
            $recentTable->endRow();
        }

        $previousTable->startRow();
        $previousTable->addCell("<br>".$this->objCancelButton->show());
        $previousTable->endRow();

        $table = $title->show();

        if($id != "") {
            $recentTableHeading = new htmlheading($this->objLanguage->languageText('mod_edit_editRecentDonation','gift'),3);
            $previousTableHeading = new htmlheading($this->objLanguage->languageText('mod_edit_editPreviousDonation','gift'),3);
            $table .= $recentTableHeading->show().$recentTable->show()."<br><br>".$previousTableHeading->show().$previousTable->show();
        }
        else
            $table .= $previousTable->show();
            
        return $table;
    }
}
?>
