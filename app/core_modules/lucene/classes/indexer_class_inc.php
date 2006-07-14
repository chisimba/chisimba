<?php
ini_set("max_execution_time", 3600);
//ini_set("memory_limit", "700MB");

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
        if(file_exists($this->indexPath.'/chisimbaIndex'))
        {
        	//we build onto the previous index
        	$this->index = new Zend_Search_Lucene($this->indexPath.'/chisimbaIndex');
        }
        else {
        	//instantiate the lucene engine
        	$this->index = new Zend_Search_Lucene($this->indexPath.'/chisimbaIndex', true);
        }
        //hook up the document parser
        $this->document = new Zend_Search_Lucene_Document();
		//change directory to the index path
        chdir($this->indexPath);
        $files = $this->globr($this->indexPath, "*");
		foreach ($files /*glob("*")*/ as $filename) {
			echo "indexing" . "  " . $filename . "<br><br>";

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
     		$this->document->addField(Zend_Search_Lucene_Field::Text('title', basename($filename))); //$doc->getProperty('title', $filename)));
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
        		$sDir = escapeshellcmd($sDir);
                //echo $sDir;
                // Get the list of all matching files currently in the
                // directory.

                $aFiles = glob("$sDir/$sPattern", $nFlags);

                // Then get a list of all directories in this directory, and
                // run ourselves on the resulting array.  This is the
                // recursion step, which will not execute if there are no
                // directories.

                foreach (@glob("$sDir/*", GLOB_ONLYDIR) as $sSubDir)
                {
                        $aSubFiles = $this->globr($sSubDir, $sPattern, $nFlags);
                        $aFiles = array_merge($aFiles, $aSubFiles);
                }

                // The array we return contains the files we found, and the
                // files all of our children found.

                return $aFiles;
        }//end function


}
?>