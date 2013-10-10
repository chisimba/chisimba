<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//echo "The Showdown has just begun gunz.";
//load class
$this->loadclass('link', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');

$homeWelcome = $this->objHome->homePage();

// get the links on the left



$heading = new htmlheading($this->objLanguage->languageText('mod_registration_heading', 'elsitraining'), 1);
$body = $this->objLanguage->languageText('mod_homeWelcome_body', 'elsitraining');
$notice = $this->objLanguage->languageText('mod_homeWelcome_warning', 'elsitraining');





/*if (count($gifts) > 0) {
    foreach ($gifts as $gift) {
        $table->startRow($class);
        $table->addCell($deleted . $viewDetailsLink->show() . $edit );
        $table->addCell($gift['gift_type']);
        $table->addCell($gift['description']);
        $table->addCell($gift["donor"]);
        $value = $this->objGift->formatMoney($gift['value'], TRUE);
        $table->addCell("R" . $value . '&nbsp;&nbsp;', NULL, null, "right");
        $table->addCell($this->objUser->fullname($gift["recipient"]));

        $table->addCell($deleted . $gift["date_recieved"], null, NULL, "center");
        $table->endRow();
    }
}
echo '<br/>';
echo $table->show();
*/

?>
