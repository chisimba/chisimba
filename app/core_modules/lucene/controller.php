<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for the Lucene implementation of Chisimba
 *
 * @author Paul Scott
 * @copyright AVOIR UWC
 * @access public
 * @package lucene
 */
require_once 'resources/Exception.php';
require_once 'resources/Search/Exception.php';
require_once 'resources/Search/Lucene.php';

require_once 'resources/Search/Lucene/Document.php';
require_once 'resources/Search/Lucene/Exception.php';
require_once 'resources/Search/Lucene/Field.php';

require_once 'resources/Search/Lucene/Storage/Directory.php';
require_once 'resources/Search/Lucene/Storage/File.php';

require_once 'resources/Search/Lucene/Storage/Directory/Filesystem.php';
require_once 'resources/Search/Lucene/Storage/File/Filesystem.php';

//analysis
require_once 'resources/Search/Lucene/Analysis/Analyzer.php';
require_once 'resources/Search/Lucene/Analysis/Token.php';
require_once 'resources/Search/Lucene/Analysis/TokenFilter.php';
require_once 'resources/Search/Lucene/Analysis/Analyzer/Common.php';
require_once 'resources/Search/Lucene/Analysis/Analyzer/Common/Text.php';
require_once 'resources/Search/Lucene/Analysis/Analyzer/Common/Text/CaseInsensitive.php';
require_once 'resources/Search/Lucene/Analysis/TokenFilter/LowerCase.php';

//index
require_once 'resources/Search/Lucene/Index/FieldInfo.php';
require_once 'resources/Search/Lucene/Index/SegmentInfo.php';
require_once 'resources/Search/Lucene/Index/SegmentWriter.php';
require_once 'resources/Search/Lucene/Index/Term.php';
require_once 'resources/Search/Lucene/Index/TermInfo.php';
require_once 'resources/Search/Lucene/Index/Writer.php';

//Search
require_once 'resources/Search/Lucene/Search/Query.php';
require_once 'resources/Search/Lucene/Search/QueryHit.php';
require_once 'resources/Search/Lucene/Search/QueryParser.php';
require_once 'resources/Search/Lucene/Search/QueryToken.php';
require_once 'resources/Search/Lucene/Search/QueryTokenizer.php';
require_once 'resources/Search/Lucene/Search/Similarity.php';
require_once 'resources/Search/Lucene/Search/Weight.php';

//Searc/Query
require_once 'resources/Search/Lucene/Search/Query/MultiTerm.php';
require_once 'resources/Search/Lucene/Search/Query/Phrase.php';
require_once 'resources/Search/Lucene/Search/Query/Term.php';

//Search/Similarity
require_once 'resources/Search/Lucene/Search/Similarity/Default.php';

//Search/Weight
require_once 'resources/Search/Lucene/Search/Weight/MultiTerm.php';
require_once 'resources/Search/Lucene/Search/Weight/Phrase.php';
require_once 'resources/Search/Lucene/Search/Weight/Term.php';

class lucene extends controller
{
	//public $indexer;
	public $indexPath;
	public $index;
	public $doc;
	public $objConfig;
	public $search;

	/**
	 * Constructor
	 */
	public function init()
	{
		// instantiate objects
        try{
			//the language object
        	$this->objLanguage = $this->getObject('language','language');
        	$this->objConfig = $this->getObject('altconfig','config');
        	$this->doc = $this->getObject('doc');
        	$this->index = $this->getObject('indexer');


        }
        catch (customException $e){
       		echo customException::cleanUp($e);
        	exit();
        }

	}

	/**
	* The Dispatch  methed that the framework needs to evoke the controller
	*/
	public function dispatch()
	{
		try{

			$action = $this->getParam('action');
	        switch ($action){
	            case null:
	            case 'index':
	            	//set the path to index
	            	$this->index->indexPath = $this->objConfig->getcontentBasePath();
	            	$this->indexPath = $this->index->indexPath;
	            	//do the indexing
					$this->index->doIndex($this->doc);
	            	break;

	            case 'search':
	            	//move this to a new module. This is only for testing now...
	            	$query = $this->getParam('query');
	            	$this->search = new Zend_Search_Lucene($this->objConfig->getcontentBasePath());
	            	//var_dump($this->search->terms());
	            	//clean the query
	            	$query = trim($query);

	            	if (strlen($query) > 0) {
        				$hits = $this->search->find($query);
       					//print_r($hits);
        				$numHits = count($hits);
    				}
    				echo "Found $numHits Results for Query $query <br><br>";
    				foreach($hits as $hit)
    				{
    					echo "Title " . $hit->title . " at URL " . "<a href=$hit->url>$hit->url</a> " . "with relevance score of " . $hit->score . "<br><br><hr>";
    				}





	        }
		}
		catch (customException $e){
       		echo customException::cleanUp($e);
        	exit();
        }
	}
}
?>