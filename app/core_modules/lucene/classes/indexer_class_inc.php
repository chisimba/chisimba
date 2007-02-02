<?php
//do some ini manipulations to cater for very large document trees
ini_set("max_execution_time", 3600);
//set the memory limit to infinite
ini_set("memory_limit", -1);

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Indexer class extends object
 * The indexer object deals with filesystem based document trees
 * It will take a document stored on disc and add it to the index
 *
 * @author Paul Scott
 * @package lucene
 * @copyright AVOIR UWC
 */
class indexer extends Zend_Search_Lucene_Document
{

	/**
	 * The document object
	 *
	 * @var object
	 */
	public $document;

	/**
	 * The index object
	 *
	 * @var object
	 */
	public $index;

	/**
	 * The path that we are indexing
	 *
	 * @var string
	 */
	public $indexPath;

	/**
	 * Standard initialisation method
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{

	}

	/**
     * Creates our indexable document and adds all
     * necessary fields to it using the passed stuff from the filesystem.
     * This method will index an entire tree, recursively globbing through all documents
     * NOTE: This method should be used very sparingly!!!
     *
     * @param $doc - object of the document class passed by reference
     * @return void
     */
     public function doIndex(&$doc)
     {
     	//echo $this->indexPath; die();
        //check if an index exists
     	if(file_exists($this->indexPath.'chisimbaIndex/segments'))
        {
        	chmod($this->indexPath.'chisimbaIndex', 0777);
        	//we build onto the previous index
        	$this->index = new Zend_Search_Lucene($this->indexPath.'chisimbaIndex');

        }
        else {
        	//instantiate the lucene engine and create a new index
        	mkdir($this->indexPath.'chisimbaIndex');
        	chmod($this->indexPath.'chisimbaIndex', 0777);
        	$this->index = new Zend_Search_Lucene($this->indexPath.'chisimbaIndex', true);

        }
        //hook up the document parser
        $this->document = new Zend_Search_Lucene_Document();
		//change directory to the index path
        chdir($this->indexPath);
        $files = $this->globr($this->indexPath, "*");
        if(empty($files))
        {
        	exit;
        }

		foreach ($files /*glob("*")*/ as $filename) {
			echo "indexing" . "  " . $filename . "<br><br>";

			//set the properties that we want to use in our index
   		    //url
   			$this->document->addField(Zend_Search_Lucene_Field::UnIndexed('url', $doc->generateUrl($filename)));

       		//createdBy
   			//$this->document->addField(Zend_Search_Lucene_Field::UnIndexed('createdBy', $doc->getProperty('createdBy', $filename)));
       		//document teaser
     		$this->document->addField(Zend_Search_Lucene_Field::UnIndexed('date', $doc->getProperty('date', $filename)));

        	//doc title
     		$this->document->addField(Zend_Search_Lucene_Field::Text('title', basename($filename))); //$doc->getProperty('title', $filename)));

        	//doc author
     		//$this->document->addField(Zend_Search_Lucene_Field::Text('author', $doc->getProperty('author', $filename)));
        	//document body
        	//NOTE: this is not actually put into the index, so as to keep the index nice and small
        	//      only a reference is inserted to the index.
        	if(is_file($filename))
            {
     		 $this->document->addField(Zend_Search_Lucene_Field::Unstored('contents', $doc->getProperty('body', $filename)));
            }
     		 //what else do we need here???

     		//add the document to the index
     		$this->index->addDocument($this->document);
		}//end foreach
		//commit the index to disc
		$this->index->commit();
     }

    	/**
         * Recursive version of glob
         *
         * @return array containing all pattern-matched files.
         *
         * @param string $sDir      Directory to start with.
         * @param string $sPattern  Pattern to glob for.
         * @param int $nFlags      Flags sent to glob.
         */
        private function globr($sDir, $sPattern, $nFlags = NULL)
        {
                //chdir($sDir);
        		$sDir = str_replace('\\','/',$sDir);
        		$sDir = escapeshellcmd($sDir);
                //echo $sDir;
                // Get the list of all matching files currently in the
                // directory.

                $aFiles = glob($sDir.$sPattern, $nFlags);

                // Then get a list of all directories in this directory, and
                // run ourselves on the resulting array.  This is the
                // recursion step, which will not execute if there are no
                // directories.

                foreach (@glob("$sDir/*", GLOB_ONLYDIR) as $sSubDir)
                {
                   // if(is_file($sSubDir))
                    //{
                        $aSubFiles = $this->globr($sSubDir, $sPattern, $nFlags);
                        $aFiles = array_merge($aFiles, $aSubFiles);
                    //}
                }

                // The array we return contains the files we found, and the
                // files all of our children found.

                return $aFiles;
        }//end function


}
?>