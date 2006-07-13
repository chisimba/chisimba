<?php
ini_set("max_execution_time", 3600);

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check



/**
 * Indexer class extends object
 */

class indexer extends Zend_Search_Lucene_Document
{

	public $document;
	public $index;
	public $indexPath;

	public function init()
	{

	}

	/**
     * Pseudo Constructor. Creates our indexable document and adds all
     * necessary fields to it using the passed stuff from the filesys or dbTable derived classes
     */
     public function doIndex(&$doc)
     {
        //instantiate the lucene engine
        $this->index = new Zend_Search_Lucene($this->indexPath, true);
        //hook up the document parser
        $this->document = new Zend_Search_Lucene_Document();
		//change directory to the index path
        chdir($this->indexPath);
		foreach (glob("*") as $filename) {

			//echo "indexing" . "  " . $filename . "<br><br>";
			//fake the document
			$docBody = file_get_contents($filename);

			//set the properties that we want to use in our index
    	    //url
     		$this->document->addField(Zend_Search_Lucene_Field::UnIndexed('url', $doc->generateUrl($filename)));
        	//createdBy
     		//$this->document->addField(Zend_Search_Lucene_Field::UnIndexed('createdBy', $doc->getProperty('createdBy', $filename)));
        	//document teaser
     		//$this->document->addField(Zend_Search_Lucene_Field::UnIndexed('teaser', $doc->getProperty('teaser', $filename)));
        	//doc title
     		$this->document->addField(Zend_Search_Lucene_Field::Text('title', $filename)); //$doc->getProperty('title', $filename)));
        	//doc author
     		//$this->document->addField(Zend_Search_Lucene_Field::Text('author', $doc->getProperty('author', $filename)));
        	//document body
        	//NOTE: this is not actually put into the index, so as to keep the index nice and small
        	//      only a reference is inserted to the index.
     		$this->document->addField(Zend_Search_Lucene_Field::Text('contents', $doc->getProperty('body', $filename))); //Change to Unstored
     		//what else do we need here???

     		//add the document to the index
     		$this->index->addDocument($this->document);
		}//end foreach
		//commit the index to disc
		$this->index->commit();
		//print_r($this->index->getFieldNames());
     }

}
?>