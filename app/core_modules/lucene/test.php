<?php
/**
 * @package    ZSearch
 * @subpackage demo
 */

/** Zend_Search_Lucene */
require_once 'resources/Search/Lucene.php';



/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   lucene
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class FileDocument extends Zend_Search_Lucene_Document
{
    /**
     * Object constructor
     *
     * @param  string                       $fileName    
     * @param  boolean                      $storeContent
     * @throws Zend_Search_Lucene_Exception
     */
    public function __construct($fileName, $storeContent = false)
    {
        if (!file_exists($fileName)) {
            throw new Zend_Search_Lucene_Exception("File doesn't exists. Filename: '$fileName'");
        }
        $this->addField(Zend_Search_Lucene_Field::Text('path', $fileName));
        $this->addField(Zend_Search_Lucene_Field::Keyword( 'modified', filemtime($fileName) ));

        $f = fopen($fileName,'rb');
        $byteCount = filesize($fileName);

        $data = '';
        while ( $byteCount > 0 && ($nextBlock = fread($f, $byteCount)) != false ) {
            $data .= $nextBlock;
            $byteCount -= strlen($nextBlock);
        }
        fclose($f);

        if ($storeContent) {
            $this->addField(Zend_Search_Lucene_Field::Text('contents', $data));
        } else {
            $this->addField(Zend_Search_Lucene_Field::UnStored('contents', $data));
        }
    }
}


// Create index
$index = new Zend_Search_Lucene('index', true);
// Uncomment next line if you want to have case sensitive index
// ZSearchAnalyzer::setDefault(new ZSearchTextAnalyzer());
var_dump($index);

$indexSourceDir = '/home/paul/documents/';
$dir = opendir($indexSourceDir);
while ($file = readdir($dir)) {
    if (is_dir($file)) {
       // echo "dir encountered";
    	continue;
    }
    if (strcasecmp(substr($file, strlen($file)-5), '.odp') != 0) {
        //echo $file;
    	continue;
    }

    // Create new Document from a file
    $doc = new FileDocument($indexSourceDir . '/' . $file, true);
    var_dump($index);
    // Add document to the index
    $index->addDocument($doc);

    echo $file . "...\n";
    flush();
}
closedir($dir);


?>