<?php
/**
* @package Award
*/
//header("Content-type: image/png");
$graph = $this->getObject('inflationgraph','award');
$graph->setLabels($this->indexFacet->getIndexName($this->getParam('indexId')),
					$this->objLanguage->languageText('word_date'),
					$this->objLanguage->languageText('word_inflation'),
					$this->objLanguage->languageText('phrase_anualised'));
$graph->addData($arrData);
$graph->width = $this->getParam('width',450);
$graph->height = $this->getParam('height',300);
$graph->show();
?>