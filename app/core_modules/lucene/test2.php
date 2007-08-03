<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP versions 4 and 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   lucene
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
ini_set("max_execution_time", 3600);

/**
 * Description for require_once
 */
require_once 'resources/Search/Lucene.php';

$indexPath = '/var/www/phpman';
$index = new Zend_Search_Lucene($indexPath, true);

$doc = new Zend_Search_Lucene_Document();

//$data = Zend_Search_Lucene_Field::Text('title', $docTitle);
chdir($indexPath);
foreach (glob("*.html") as $filename) {

	echo "indexing" . "  " . $filename . "<br><br>";
	//fake the document
	$docBody = file_get_contents($filename);
	//echo $docBody;
	//die();
	//get the doc properties...
	//$doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $docUrl));
	//$doc->addField(Zend_Search_Lucene_Field::UnIndexed('created', $docCreated));
	//$doc->addField(Zend_Search_Lucene_Field::UnIndexed('teaser', $docTeaser));
	$doc->addField(Zend_Search_Lucene_Field::Text('title', $filename));
	//$doc->addField(Zend_Search_Lucene_Field::Text('author', $docAuthor));
	$doc->addField(Zend_Search_Lucene_Field::Text('contents', $docBody));

	//commit the doc to the index
	$index->addDocument($doc);
}
//write the index to disc
$index->commit();
print_r($index->getFieldNames());
?>