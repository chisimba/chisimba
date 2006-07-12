<?php
ini_set("max_execution_time", 3600);

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

require_once '../resources/Search/Lucene.php';

/**
 * Indexer class extends object
 */

class indexer extends Zend_Search_Lucene_Document
{

	public $document;
	public $index;

	public function __construct()
	{
		//instantiate the lucene engine
        $this->index = new Zend_Search_Lucene($indexPath, true);
        //hook up the document parser
        $this->document = new Zend_Search_Lucene_Document();
	}

	/**
     * Pseudo Constructor. Creates our indexable document and adds all
     * necessary fields to it using the passed stuff from the filesys or dbTable derived classes
     */
     public function doIndex(&$doc)
     {
        //set the properties that we want to use in our index
        //url
     	$this->document->addField(Zend_Search_Lucene_Field::UnIndexed('url', $this->doc->generateUrl()));
        //createdBy
     	$this->document->addField(Zend_Search_Lucene_Field::UnIndexed('createdBy', $this->doc->getProperty('createdBy')));
        //document teaser
     	$this->document->addField(Zend_Search_Lucene_Field::UnIndexed('teaser', $this->doc->getProperty('teaser')));
        //doc title
     	$this->document->addField(Zend_Search_Lucene_Field::Text('title', $this->doc->getProperty('title')));
        //doc author
     	$this->document->addField(Zend_Search_Lucene_Field::Text('author', $this->doc->getProperty('author')));
        //document body
        //NOTE: this is not actually put into the index, so as to keep the index nice and small
        //      only a reference is inserted to the index.
     	$this->document->addField(Zend_Search_Lucene_Field::UnStored('contents', $this->doc->getProperty('body')));
     	//what else do we need here???

     }

}
?>