<?php

class xmlthing extends object
{
	public $xw;
	
	public function init()
	{
		
	}
	
	public function createDoc()
	{
		$this->xw = new xmlWriter();
    	$this->xw->openMemory();
   	    $this->xw->startDocument('1.0','UTF-8');
   	    $this->xw->setIndent(TRUE);
	}
	
	public function addDTD($type='html', $dtd="-//WAPFORUM//DTD XHTML Mobile 1.0//EN', 'http://www.wapforum.org/DTD/xhtml-mobile10.dtd")
	{
		$this->xw->startDtd($type, $dtd);
    	$this->xw->endDtd();
	}
	
	public function startElement($ele='html')
	{
		$this->xw->startElement($ele);
	}
	
	public function writeElement($name, $value)
	{
		$this->xw->writeElement($name, $value);
	}
	
	public function endElement()
	{
		$this->xw->endElement();
	}
	
	public function writeAtrribute($name='xm:lang', $value='en')
	{
		$this->xw->writeAttribute($name, $value);
	}
	
	public function endDTD()
	{
		$this->xw->endDtd();
	}
	
	public function dumpXML()
	{
		return $this->xw->outputMemory(true);
	}
	
}