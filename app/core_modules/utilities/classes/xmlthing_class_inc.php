<?php
/**
 * XMLWriter extension wrapper class
 * 
 * Class to wrap the xmlwriter extension of php5 for Chisimba
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
 * @package   utilties
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
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


/**
 * Chisimba xmlwriter wrapper object
 * 
 * Provides unified API for the xmlwriter extension of php > 5
 * 
 * @category  Chisimba
 * @package   utilties
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class xmlthing extends object
{
	/**
	 * XML Object
	 *
	 * @var    object
	 * @access public
	 */
	public $xw;
	
	/**
	 * Standard init function
	 *
	 */
	public function init()
	{
		
	}
	
	/**
	 * Method to create the initial document for manipulation
	 *
	 * @param string $version - xml doc version
	 * @param string $encoding - the encoding of the document
	 * @param bool   $indent - whether to indent or not. Default is to indent
	 */
	public function createDoc($version='1.0', $encoding='UTF-8', $indent=TRUE)
	{
		$this->xw = new xmlWriter();
    	$this->xw->openMemory();
   	    $this->xw->startDocument($version,$encoding);
   	    $this->xw->setIndent($indent);
	}
	
	/**
	 * Method to add a DTD to the document
	 *
	 * @param string $type - dtd type (html)
	 * @param string $dtd - the document type definition
	 */
	public function addDTD($type='html', $dtd="-//WAPFORUM//DTD XHTML Mobile 1.0//EN', 'http://www.wapforum.org/DTD/xhtml-mobile10.dtd")
	{
		$this->xw->startDtd($type, $dtd);
    	$this->xw->endDtd();
	}
	
	/**
	 * Method to start an element
	 *
	 * @param string $ele
	 */
	public function startElement($ele='html')
	{
		$this->xw->startElement($ele);
	}
	
	/**
	 * Method to write the element parts
	 *
	 * @param string $name the element name
	 * @param string $value the element value
	 */
	public function writeElement($name, $value)
	{
		$this->xw->writeElement($name, $value);
	}
	
	/**
	 * Method to end an element (closes)
	 *
	 */
	public function endElement()
	{
		$this->xw->endElement();
	}
	
	/**
	 * Method to create an attribute in an element
	 *
	 * @param string $name name of the attribute
	 * @param string $value value
	 */
	public function writeAtrribute($name='xm:lang', $value='en')
	{
		$this->xw->writeAttribute($name, $value);
	}
	
	/**
	 * Method to close off the DTD
	 *
	 */
	public function endDTD()
	{
		$this->xw->endDtd();
	}
	
	/**
	 * dumps the xml as a string
	 *
	 * @return string xml
	 */
	public function dumpXML()
	{
		return $this->xw->outputMemory(TRUE);
	}
	
}
?>