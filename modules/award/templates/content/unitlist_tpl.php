<?

$objHeading = $this->newObject('htmlheading','htmlelements');
$objHeading->str = $this->objLanguage->languageText('mod_lrs_sampleheading','award');

$wageType = $this->objDbPayPeriodType->getRow('id',$wageTypeId);
		
switch ($aggregate) {
	case 'max':
		$aggregate_wording = $this->objLanguage->languageText('mod_lrspostlogin_maxavgs', 'award');
		$avewage = $this->objDbWages->getAverageMaxWages($sicId,$socId,null,'all',$wageTypeId,$agreeId,$year,'all');
		break;
	case 'med':
		$aggregate_wording = $this->objLanguage->languageText('mod_lrspostlogin_medavgs', 'award');
		$avewage = $this->objDbWages->getMedianWages($sicId,$socId,null,'all',$wageTypeId,$agreeId,$year,'all');
		break;
	case 'min':
	default:
		$aggregate_wording = $this->objLanguage->languageText('mod_lrspostlogin_minavgs', 'award');
		$avewage = $this->objDbWages->getAverageMinWages($sicId,$socId,null,'all',$wageTypeId,$agreeId,$year,'all');
}

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->addHeader(array($this->objLanguage->languageText('mod_lrspostlogin_agreename','award'),
							$this->objLanguage->languageText('word_occupation'),
							"$aggregate_wording {$wageType['name']} ".$this->objLanguage->languageText('phrase_wagerate'))
					 ,null,'align=left');
$class = 'odd';
$currency = $this->objConfig->getValue('CURRENCY_ABREVIATION','award');

$wageLink = $this->newObject('link','htmlelements');
foreach ($avewage['units'] as $unitId) {
	$agreements = $this->objAgree->getAll("WHERE unitid = '$unitId' AND YEAR(implementation) <= $year
							AND YEAR(DATE_ADD(implementation,INTERVAL length MONTH)) >= $year
							ORDER BY name");
	foreach ($agreements as $agree) {
		switch ($aggregate) {
			case 'max':
				$wage = $this->objAgree->getMaxWageFromAgree($agree['id'],$wageTypeId);
				break;
			case 'med':
				$wage = $this->objAgree->getMedWageFromAgree($agree['id'],$wageTypeId);
				break;
			case 'min':
			default:
				$wage = $this->objAgree->getMinWageFromAgree($agree['id'],$wageTypeId);
		}
		$class = ($class == 'odd')? 'even' : 'odd';
		if ($this->objUser->isAdmin()) {
			$wageLink->link = $agree['name'];
			$wageLink->link($this->uri(array('action'=>'wage','wageId'=>$wage['id'], 'agreeId'=>$agree['id'])));
			$name = $wageLink->show();
		} else {
			$name = $agree['name'];
		}
		$objTable->startRow($class);
		$objTable->addCell($name);
		$objTable->addCell($wage['soc']);
		$objTable->addCell($currency.number_format($wage['rate'],2),null,null,'center');
		$objTable->endRow();
		
	}
}
echo $objHeading->show().$objTable->show();

?>