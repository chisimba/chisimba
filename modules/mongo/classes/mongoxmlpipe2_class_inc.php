<?php

/**
 * MongoDB Helper Class
 * 
 * Convenience class for interacting with MongoDB
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
 * @package   mongo
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: mongoops_class_inc.php 18998 2010-09-14 12:45:34Z paulscott $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://www.mongodb.org/
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * MongoDB Helper Class
 * 
 * Convenience class for interacting with MongoDB.
 * 
 * @category  Chisimba
 * @package   mongo
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: mongoops_class_inc.php 18998 2010-09-14 12:45:34Z paulscott $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://www.mongodb.org/
 */
class mongoxmlpipe2 extends object
{
    /**
     * The name of the collection to default to.
     *
     * @access private
     * @var    string
     */
    private $collection;

    /**
     * Cache of MongoCollection objects.
     *
     * @access private
     * @var    array
     */
    private $collectionCache;

    /**
     * The name of the database to default to.
     *
     * @access private
     * @var    string
     */
    private $database;

    /**
     * Cache of MongoDB objects.
     *
     * @access private
     * @var    array
     */
    private $databaseCache;

    /**
     * Instance of the Mongo class.
     *
     * @access private
     * @var    object
     */
    private $objMongo;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objSysConfig;

    /**
	 * Handles file pointer resource.
	 * @var resource
	 */
	protected $fp;
 
    /**
	 * Remainder of a last string, that will be returned as a prefix of
	 * a next string.
	 *
	 * @var string
	 */
    protected $remainder = "";
	
    /**
     * regular expression for stripping control characters that aren't valid in
     * xml. See http://www.w3.org/International/questions/qa-controls for more
     * details.
     * 
     * @var string
     */
    public $ccRegex = '/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/S';

    /**
     * XML Writer property (memory based writer doc)
     *
     * @var string
     */
     public $xmlwriter;
     
    /**
     * init document id
     * 
     * @var integer
     */
    public $docId = 0;
	
    /*
     * Initialises some of the object's properties.
     *
     * @access public
     */
    public function init()
    {
        // Objects from other classes.
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

        // Local properties.
        $this->collectionCache = array();
        $this->database        = $this->objSysConfig->getValue('database', 'mongo');
        $this->databaseCache   = array();
        
        // xmlwriter 
        // instantiate an xmlwriter
        $this->xmlwriter = new xmlWriter();
        $this->xmlwriter->openMemory();
        $this->xmlwriter->setIndent(true);
        $this->xmlwriter->startDocument('1.0','UTF-8');
 
        // open global tags
        $this->xmlwriter->startElement('sphinx:docset');
 
        // create a schema definition
        $this->xmlwriter->startElement('sphinx:schema');
        $this->xmlwriter->startElement('sphinx:field');
        $this->xmlwriter->writeAttribute("name", "content");
        $this->xmlwriter->endElement(); // field
        $this->xmlwriter->endElement(); // schema
    }
    
    /**
     * Method to iterate through a directory of text files and index them safely in fulltext
     *
     * @param string $path
     * @return void
     */
    public function docIndexer($path)
    {
        // iterate through files in a given directory
        foreach(new DirectoryIterator($path) as $file) {
	        // skip dots and directories
	        if($file->isDir() || $file->isDot() || (pathinfo($file, PATHINFO_EXTENSION) != "txt")) {
		        continue;
	        }
            // increment a document id
  	        $this->docId++;
 
	        // open xml tags for the document
	        $this->xmlwriter->startElement('sphinx:document');
	        $this->xmlwriter->writeAttribute("id", $this->docId);
	        $this->xmlwriter->startElement("content");
 
    	    // make sure to read the file safely
	        $reader = $this->fileObject($file->getPathname());
 
            // sequentially read file content
            while(($buffer = $this->fread(131072)) !== false) {
                // strip control character
		        $buffer = preg_replace($this->ccRegex, " ", $buffer);

                // pass each buffer into xmlwriter and flush
                $this->xmlwriter->text($buffer);
                print $this->xmlwriter->flush();
            }
            
            unset($this->fp);
            // close xml tags for the document
            $this->xmlwriter->endElement(); // content
            $this->xmlwriter->endElement(); // field
        }
        // finalize global tags
        $this->xmlwriter->endElement();
        print $this->xmlwriter->outputMemory(true);
    }
    
    
    /**
     * Methods for reading utf-8 files safely.
     *
     * Safely means that the reader's fread will return only intact utf-8
     * multibyte symbols. This is useful when you're going to fread() and
     * immediately pass buffers to some code that requires consistent utf-8 content,
     * like xmlwriter::text(), which otherwise will treat strings as corrupted.
     */
    
    /**
	 * Constructs a new file object.
	 *
	 * @param string $file_name The name of the stream to open  
	 * @param string $open_mode The file open mode
	 * @param bool $use_include_path  Whether to search in include paths
	 * @param resource $context A stream context
	 * @throws RuntimeException if the file can not be opened
	 */
	public function fileObject($file_name, $open_mode = 'r', $use_include_path = FALSE, $context = NULL) {
		if(func_num_args() >= 4) {
			$this->fp = fopen($file_name, $open_mode, $use_include_path, $context);
		} else {
			$this->fp = fopen($file_name, $open_mode, $use_include_path);
		}
		if (!$this->fp) {
			throw new customException("Cannot open file $file_name");
		}
	}
	
	/**
	 * An php::fread() function analog, but it won't stop on a middle of
	 * a multibyte utf-8 character.
	 *
	 * @param int $length
	 * @return string|false
	 */
	public function fread($length = 8192) {
		if(feof($this->fp)) {
			if(strlen($this->remainder) > 0) {
				$ret = $this->remainder;
				$this->remainder = "";
				return $ret;
			}
			return false;
		} else {
			$buffer = fread($this->fp, $length);
			if (($spacepos = strrpos($buffer, "")) !== false) {
				$now = $this->remainder.substr($buffer, 0, $spacepos);
				$this->remainder = substr($buffer, $spacepos + 1);
			} else {
				$this->remainder .= $buffer;
			}
		}
	}
	
	
}
?>
