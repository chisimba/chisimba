<?php
/**
* @package AWARD Wage Reports
*/

header("Content-type: image/png");
$graph = $this->getObject('agcgraph','award');
$graph->addMultipleDatasets($arrData);
$graph->setType('line');
$graph->showKey(true);
$graph->width = $this->getParam('width',700);
$graph->height = $this->getParam('height',500);
//$graph->title = $title;
//$graph->yLabel = $YLabel;
//$graph->xLabel = $this->objLanguage->languageText('word_year');
$graph->show();
?>