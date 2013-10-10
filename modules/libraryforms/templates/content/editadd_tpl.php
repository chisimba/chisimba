<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
// end security check

/**
 *
 * libraryforms
 *
 * libraryforms allows students or distant user to request books online
 *
 * @category  Chisimba
 * @package   libraryforms
 * @author    Brenda Mayinga brendamayinga@ymail.com
 */
//$objEditForm = $this->getObject('editform', 'libraryforms');
$objBookThesis = $this->getObject('bookthesis', 'libraryforms');
$objFeedbk = $this->getObject('feedbk', 'libraryforms');
$objILLperiodical = $this->getObject('illperiodical', 'libraryforms');
$objILLperiodical = $this->getObject('illperiodical', 'libraryforms');
$tab = $this->newObject('tabbedbox', 'htmlelements');
$table = $this->newObject('htmltable', 'htmlelements');
$tabcontent = $this->newObject('tabcontent', 'htmlelements');
$objTable = $this->newObject('htmltable', 'htmlelements');
$this->loadClass('form', 'htmlelements');
//$objForm = new form('myform', $this->uri(array('action' => 'valform'), 'htmlelements'));
$this->objUser = $this->getObject('User', 'security');


$category = 'user';

$tab->tabbedbox();
$tab->addTabLabel($this->objLanguage->languageText("category_resource_four", "libraryforms"));
$tab->addBoxContent($objFeedbk->show());

$tabcontent->addTab('FeedbackForm', $tab->show());

$tab->tabbedbox();
$tab->addTabLabel($this->objLanguage->languageText("category_resource_two", "libraryforms"));
$tab->addBoxContent($objBookThesis->show());

$tabcontent->addTab('Book / Thesis only Form', $tab->show());

$tab->tabbedbox();
$tab->addTabLabel($this->objLanguage->languageText("category_resource_three", "libraryforms"));
$tab->addBoxContent($objILLperiodical->show());

$tabcontent->addTab('Periodical Request Form', $tab->show());


$tabcontent->width = '90%';

echo '<br/><center>' . $tabcontent->show() . '</center>';
?>



