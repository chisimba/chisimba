<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');

// Set columns to 2
$cssLayout->setNumColumns(2);
$leftMenu = NULL;

$rightSideColumn = NULL;

$leftCol = NULL;
$middleColumn = NULL;

$leftCol .= $objSideBar->show();

$this->loadClass('href', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->objUser = $this->getObject('user', 'security');

$cform = new form('addserver', $this->uri(array(
'action' => 'addserver'
)));

//add rules
$cform->addRule('ip', $this->objLanguage->languageText("mod_cache_phrase_ipreq", "cache") , 'required');
$cform->addRule('port', $this->objLanguage->languageText("mod_cache_phrase_portreq", "cache") , 'required');
// $cform->addRule('port', $this->objLanguage->languageText("mod_cache_phrase_portnumreq", "cache") , 'numeric');

//start a fieldset
$cfieldset = $this->getObject('fieldset', 'htmlelements');
$cadd = $this->newObject('htmltable', 'htmlelements');
$cadd->cellpadding = 3;

//IP textfield
$cadd->startRow();
$ciplabel = new label($this->objLanguage->languageText('mod_cache_ip', 'cache') . ':', 'input_ip');
$ip = new textinput('ip');
$ip->extra = ' style="width:50%;" ';
if (isset($cache['ip'])) {
	$ip->setValue(htmlentities($cache['ip'], ENT_QUOTES));
}
$cadd->addCell($ciplabel->show());
$cadd->addCell($ip->show());
$cadd->endRow();

//Port
$cadd->startRow();
$plabel = new label($this->objLanguage->languageText('mod_cache_port', 'cache') . ':', 'input_port');
$port = new textinput('port');
$port->extra = ' style="width:50%;" ';
if (isset($cache['port'])) {
	$port->setValue($cache['port']);
}
$cadd->addCell($plabel->show());
$cadd->addCell($port->show());
$cadd->endRow();

//end off the form and add the buttons
$this->objCButton = new button($this->objLanguage->languageText('word_save', 'system'));
$this->objCButton->setValue($this->objLanguage->languageText('word_save', 'system'));
$this->objCButton->setToSubmit();
$cfieldset->addContent($cadd->show());
$cform->addToForm($cfieldset->show());
$cform->addToForm($this->objCButton->show());
$cform = $cform->show();

$this->objIcon = $this->getObject('geticon', 'htmlelements');
if(class_exists('Memcache')){
	$memcache = new Memcache;
} else {
	$arrRep = array('MCLINK'=>'<br/> <a href="http://php.net/memcache">http://php.net/memcache</a><br />');
	echo $this->objLanguage->code2Txt('mod_cache_error', 'cache', $arrRep).'<br/><a href="javascript:javascript:history.go(-1)">'.$this->objLanguage->languageText('mod_cache_back', 'cache').'</a>';
	exit();
}	

// now the table of existing servers...
$tbl = $this->newObject('htmltable', 'htmlelements');
$tbl->cellpadding = 3;
$tbl->width = "100%";
$tbl->align = "center";
//set up the header row
$tbl->startHeaderRow();
$tbl->addHeaderCell($this->objLanguage->languageText("mod_cache_ip", "cache"));
$tbl->addHeaderCell($this->objLanguage->languageText("mod_cache_port", "cache"));
$tbl->addHeaderCell($this->objLanguage->languageText("mod_cache_status", "cache"));
$tbl->addHeaderCell($this->objLanguage->languageText("mod_cache_stats", "cache"));
$tbl->addHeaderCell('');
$tbl->endHeaderRow();

if(!isset($cache))
{
	$cache = array();
}
foreach($cache as $servers)
{
	// grab an edit and delete icon
	$edIcon = $this->objIcon->getEditIcon($this->uri(array(
	'action' => 'editserver',
	'id' => $servers['ip'].":".$servers['port'],
	'module' => 'cache'
	)));
	$delIcon = $this->objIcon->getDeleteIconWithConfirm($servers['ip'], array(
	'module' => 'cache',
	'action' => 'deleteserver',
	'id' => $servers['ip'].":".$servers['port']
	) , 'cache');
	
	$yesIcon = $this->objIcon->setIcon('greentick');
	$yesIcon = $this->objIcon->show();
	$noIcon = $this->objIcon->setIcon('redcross');
	$noIcon = $this->objIcon->show();
	$memcache->addServer($servers['ip'], $servers['port']);
	if($memcache->getServerStatus($servers['ip'], $servers['port']) != 0)
	{
		$status = $yesIcon;
	}
	else {
		$status = $noIcon;
	}
	
	$extStatsLink = new href($this->uri(array('action' => 'displaystats', 'id' => $servers['ip'].":".$servers['port']), 'cache'), $this->objLanguage->languageText("mod_cache_viewstats", "cache"));
	
	$tbl->startRow();
	$tbl->addCell($servers['ip']);
	$tbl->addCell($servers['port']);
	// get a status icon and show it here
	$tbl->addCell($status);
	// get a stats icon
	$tbl->addCell($extStatsLink->show());
	// edit/delete option...
	$tbl->addCell(''); //$edIcon.$delIcon);
	$tbl->endRow();
}

//print_r($cache);

$middleColumn .= $cform.$tbl->show();

if(isset($stats))
{
	// print_r($stats);
	$stbl = $this->newObject('htmltable', 'htmlelements');
	$stbl->cellpadding = 3;
	$stbl->width = "100%";
	$stbl->align = "center";
	//set up the header row
	$stbl->startHeaderRow();
	$stbl->addHeaderCell($machine[0]);
	$stbl->addHeaderCell($machine[1]);
	$stbl->endHeaderRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_processid", "cache"));
	$stbl->addCell($stats['pid']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_uptime", "cache"));
	$timeconv = $this->getObject('duration', 'utilities');
	$stbl->addCell($timeconv->toString($stats['uptime']));
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_time", "cache"));
	$stbl->addCell(date('r', $stats['time']));
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_version", "cache"));
	$stbl->addCell($stats['version']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_pointersize", "cache"));
	$stbl->addCell($stats['pointer_size']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_rusage_user", "cache"));
	$stbl->addCell($stats['rusage_user']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_rusage_system", "cache"));
	$stbl->addCell($stats['rusage_system']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_curritems", "cache"));
	$stbl->addCell($stats['curr_items']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_totitems", "cache"));
	$stbl->addCell($stats['total_items']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_bytes", "cache"));
	$stbl->addCell($stats['bytes']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_currconns", "cache"));
	$stbl->addCell($stats['curr_connections']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_totconns", "cache"));
	$stbl->addCell($stats['total_connections']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_connstructs", "cache"));
	$stbl->addCell($stats['connection_structures']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_cmd_get", "cache"));
	$stbl->addCell($stats['cmd_get']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_cmd_set", "cache"));
	$stbl->addCell($stats['cmd_set']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_gethits", "cache"));
	$stbl->addCell($stats['get_hits']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_getmisses", "cache"));
	$stbl->addCell($stats['get_misses']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_evictions", "cache"));
	$stbl->addCell($stats['evictions']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_bytesread", "cache"));
	$stbl->addCell($stats['bytes_read']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_byteswritten", "cache"));
	$stbl->addCell($stats['bytes_written']);
	$stbl->endRow();
	
	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_limit_maxbytes", "cache"));
	$stbl->addCell($stats['limit_maxbytes']);
	$stbl->endRow();

	$stbl->startRow();
	$stbl->addCell($this->objLanguage->languageText("mod_cache_threads", "cache"));
	$stbl->addCell($stats['threads']);
	$stbl->endRow();
	
	$middleColumn .= $stbl->show();
}

//$extendedStats = $memcache->getExtendedStats();
//print_r($extendedStats);
// server pool stats

$middleColumn .= '[IFRAME]'.$this->objConfig->getSiteRoot().$this->objConfig->getModuleUri().'cache/resources/apc.php[/IFRAME]';

$this->objWasher = $this->getObject('washout', 'utilities');
$middleColumn = $this->objWasher->parseText($middleColumn);
$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());

echo $cssLayout->show();
?>
