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


class lucene extends controller
{
	/**
	 * indexPath variable - to hold the path that we are currently indexing
	 *
	 * @var mixed
	 */
	public $indexPath;

	/**
	 * Instantiated index object
	 *
	 * @var object
	 */
	public $index;

	/**
	 * Instantiated document object
	 *
	 * @var object
	 */
	public $doc;

	/**
	 * Configuration object
	 *
	 * @var object
	 */
	public $objConfig;

	/**
	 * Instantiated search object
	 *
	 * @var object
	 */
	public $search;

/**
	* This is a method to determine if the user has to be logged in or not
    */
     public function requiresLogin() // overides that in parent class
     {
        return FALSE;

     }


	/**
	 * Constructor - public init function
	 * This is the standard initialisation method for the framework
	 *
	 * @param void
	 * @return void
	 */
	public function init()
	{
		// instantiate objects
        try{
			//the language object
        	$this->objLanguage = $this->getObject('language','language');
        	//the config object
        	$this->objConfig = $this->getObject('altconfig','config');
        	//the lucene document object
        	$this->doc = $this->getObject('doc');
        	//lucene indexing object
        	$this->index = $this->getObject('indexer');
        }
        //catch any exceptions that may have occured and pass them to the error handler
        catch (customException $e){
        	//output the standard error page
       		echo customException::cleanUp($e);
       		//kill the script to suppress any further errors
        	exit();
        }

	}

	/**
	* The Dispatch  methed that the framework needs to evoke the controller
	*
	* @param void
	* @return mixed template
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
	            	//do the indexing - note this indexes an ENTIRE tree, not a single doc
					$this->index->doIndex($this->doc);
	            	break;

	            case 'search':
	            	//move this to a new module. This is only for testing now...
	            	$query = $this->getParam('query');
	            	$this->search = new Zend_Search_Lucene($this->objConfig->getcontentBasePath().'/chisimbaIndex');
	            	echo "Searching " . $this->search->count() . " Documents <br><br>";
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
    				
	            case 'searchresults':
	            	$query = $this->getParam('query');
	            	$objLucene = & $this->newObject('results');
	            	$searchResults = $objLucene->show($query);
					// echo $searchResults; die();
					$searchResults = str_replace('&','&amp;', $searchResults);
					$this->setVarByRef('searchResults', $searchResults);
					return 'searchresults_tpl.php';
	        }
		}
		catch (customException $e){
       		echo customException::cleanUp($e);
        	exit();
        }
	}
}
?>