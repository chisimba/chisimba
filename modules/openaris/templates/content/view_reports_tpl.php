<?php
/**
 * ahis View reports Template
 *
 * Template for viewing reports
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
 * @package   ahis
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: view_reports_tpl.php 13754 2009-06-25 18:36:14Z rosina $
 * @link      http://avoir.uwc.ac.za
 */
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

$msg = '<br />';
if(isset($output)) {
        $objMsg = $this->getObject('timeoutmessage','htmlelements');
        $objMsg->setMessage($this->objLanguage->languageText('mod_ahis_promptyear', 'openaris'));
        $msg = $objMsg->show()."<br />";
}

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $this->objLanguage->languageText('mod_ahis_viewspreadreports', 'openaris');
$objHeading->type = 2;

$gisHeading = $this->newObject('htmlheading','htmlelements');
$gisHeading->str = $this->objLanguage->languageText('mod_ahis_viewgisreports', 'openaris');
$gisHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('form','htmlelements');

$sButton = new button('enter', $this->objLanguage->languageText('mod_ahis_runreport', 'openaris'));
$sButton->setToSubmit();
$sButton->setCSS('create_reportButton');
$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript:clear_viewReports();");
$cButton->setCSS('clearButton');
$backUri = $this->uri(array('action'=>'home'));
$bButton = new button('back', $this->objLanguage->languageText('word_clear'), "javascript: document.location='$backUri'");
$bButton->setCSS('cancelButton');
$yearBox = new textinput('year', $year, 'text', 5);

$monthDrop = new dropdown('month');
for ($i=1; $i<=12; $i++) {
    $date = strtotime("01-$i-01");
    $monthDrop->addOption(date('m', $date), date('F', $date));
}
$monthDrop->setSelected($month);
//$outputDrop = new dropdown('outputType');
//$outputDrop->addOption(1, 'CSV');
//$outputDrop->setSelected(1);
//$outputDrop->addOption(2, $this->objLanguage->languageText('phrase_onscreen'));
//$outputDrop->addOption(3, 'PDF');
//$outputDrop->setSelected($outputType);
$outputDrop = new textinput('outputType', '1', 'hidden');

$reportDrop = new dropdown('reportType');
$reportDrop->addFromDB($reportTypes, 'name', 'id');
$reportDrop->setSelected($reportType);

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;
//$objTable->cssClass = 'min50';

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";
$objTable->startRow();
$objTable->addCell($objHeading->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_selectmonthyear', 'openaris').":$tab");
$objTable->addCell($monthDrop->show()." ".$yearBox->show());
//$objTable->addCell($this->objLanguage->languageText('word_year').": ");
//$objTable->addCell($yearBox->show());
$objTable->endRow();
$objTable->startRow();
//$objTable->addCell($this->objLanguage->languageText('phrase_outputtype').": ");
//$objTable->addCell($outputDrop->show());
$objTable->addCell($this->objLanguage->languageText('phrase_report').": $tab");
$objTable->addCell($reportDrop->show().$outputDrop->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('');
$objTable->addCell("&nbsp;".$sButton->show()."&nbsp; &nbsp;".$cButton->show()."&nbsp &nbsp;".$bButton->show());
//$objTable->addCell('');
//$objTable->addCell();
$objTable->addCell('');
$objTable->endRow();

$objForm = new form('reportForm', $this->uri(array('action' => 'view_reports')));
$objForm->addToForm($objTable->show());
$objForm->addRule('year', $this->objLanguage->languageText('mod_ahis_valyear', 'openaris'), 'numeric');
$objForm->addRule(array('name'=>'year','maxnumber'=>date('Y')), $this->objLanguage->languageText('mod_ahis_valyear', 'openaris'), 'maxnumber');

$gisDrop = new dropdown('report');
//$gisDrop->addOption(1, 'Active Sureveillance');
$gisDrop->addOption(2, 'Passive Surveillance');
$gisDrop->setSelected(2);

$spacer = "&nbsp;&nbsp;";
$gisTable = $this->newObject('htmltable', 'htmlelements');
$gisTable->cellspacing = 3;
$gisTable->width = NULL;
$gisTable->cssClass = 'borderleft';
$gisTable->startRow();
$gisTable->addCell($gisHeading->show());
$gisTable->endRow();
$gisTable->startRow();
$gisTable->addCell($spacer.$this->objLanguage->languageText('mod_ahis_reporttype','openaris').": ");
$gisTable->endRow();
$gisTable->startRow();
$gisTable->addCell($spacer.$gisDrop->show());
$gisTable->endRow();
$gisTable->startRow();
$gisTable->addCell("$spacer&nbsp;".$sButton->show()."&nbsp; &nbsp;&nbsp &nbsp;".$bButton->show());
$gisTable->endRow();

$gisForm = new form('gisForm', $this->uri(array('action' => 'gis_reports')));
$gisForm->addToForm($gisTable->show());

$bigTable = $this->newObject('htmltable', 'htmlelements');
$bigTable->width = NULL;
$bigTable->cellspacing = 8;
$bigTable->cellpadding = 8;
$bigTable->startRow();
$bigTable->addCell($objForm->show());
$bigTable->addCell($gisForm->show());
$bigTable->endRow();

if (isset($enter) && $enter) {

    $report = $this->objViewReport->generateReport($year, $month, $reportType);
} else {
    $report = "";
}

$scriptUri = $this->getResourceURI('util.js');
$this->appendArrayVar("headerParams", "<script type='text/javascript' src='$scriptUri'></script>");

echo $msg.$bigTable->show().$report;