<?php
// security check - must be included in all scripts
if (! $GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}

class studentforms extends object {

    public function init() {
        $this->loadClass ( 'form', 'htmlelements' );
        $this->tabBox = $this->newObject ( 'tabbedbox', 'htmlelements' );
        $this->tabPanel = $this->newObject ( 'tabcontent', 'htmlelements' );
        $this->loadClass ( 'textinput', 'htmlelements' );
        $this->loadClass ( 'button', 'htmlelements' );
        $this->objLanguage = $this->getObject ( 'language', 'language' );

    }

    public function studentInput($data = array()) {
        $textfields = array ('surname', 'firstname', 'middlename', 'birthdate');
        $inputObjects = array ();
        $inputNames = array ();
        foreach ( $textfields as $line ) {
            $newItem = new textinput ( 'sis_' . $line );
            $newItem->size = 30;
            $newItem->extra = ' maxlength="50"';
            if (isset ( $data [$line] )) {
                $newItem->value = $data [$line];
            }
            $inputObjects [$line] = $newItem;
            $inputNames [$line] = $this->objLanguage->languageText ( 'mod_sis_' . $line, 'sis' );
        }

        $this->loadClass ( 'radio', 'htmlelements' );
        $gender = new radio ( 'sis_gender' );
        $gender->addOption ( 'male', $this->objLanguage->languageText ( 'mod_sis_male', 'sis' ) );
        $gender->addOption ( 'female', $this->objLanguage->languageText ( 'mod_sis_female', 'sis' ) );
        if (isset ( $data ['gender'] )) {
            $gender->setSelected ( $data ['gender'] );
        }
        $inputNames ['gender'] = $this->objLanguage->languageText ( 'mod_sis_gender', 'sis' );
        $inputObjects ['gender'] = $gender;

        $table = $this->newObject ( 'htmltable', 'htmlelements' );
        $table->startRow ();
        $table->addCell ( $inputNames ['surname'] );
        $table->addCell ( $inputNames ['firstname'] );
        $table->addCell ( $inputNames ['middlename'] );
        $table->endRow ();

        $table->startRow ();
        $table->addCell ( $inputObjects ['surname']->show () );
        $table->addCell ( $inputObjects ['firstname']->show () );
        $table->addCell ( $inputObjects ['middlename']->show () );
        $table->endRow ();

        $table->startRow ();
        $table->addCell ( $inputNames ['birthdate'] );
        $table->addCell ( $inputNames ['gender'] );
        $table->addCell ( $inputNames ['ethnicity'] );
        $table->endRow ();

        $table->startRow ();
        $table->addCell ( $inputObjects ['birthdate']->show () );
        $table->addCell ( $inputObjects ['gender']->show () );
        $table->addCell ( $inputObjects ['ethnicity']->show () );
        $table->endRow ();

        $formObj = new form ( );
        $formObj->addToForm ( $table->show () );

        $button = new button ( );
        $html = $formObj->show ();

        return $html;
    }

    public function userinfo($data = array()) {
        // code to go here


    }

}

?>
