<?php

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
 * Indexer class extends object
 * The indexer to allow developers to add documents to the search index
 *
 * @author    Tohir Solomons
 * @package   lucene
 * @copyright AVOIR UWC
 */
class indexdata extends object
{

	/**
	 * Name of the Index
	 *
	 * @var string $indexName Name of the Search Index
	 */
	public $indexName = 'searchindex';
	
	/**
	 * The index object
	 *
	 * @var object
	 */
	public $index;

	/**
	 * Standard initialisation method
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		$this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject('user', 'security');
	}
	
	/**
	 * Method to add a record to the search index
	 *
	 * @param string $docId Unique Document Id - Should be in the format of {module}_{type}_{id}
	 * @param string $docDate Date and Time document has been created or should become available
	 * @param string $url URL to the page
	 * @param string $title Title of the Document
	 * @param string $contents Contents of the Document
	 * @param string $teaser Short Overview of Contents - Appears in Search Results
	 * @param string $module Module Item is coming from
	 * @param string $license License applicable to the content
	 * @param string $context Context content is being created in, duplicate if used in various contexts
	 * @param string $workgroup Workgroup content is being created in, duplicate if used in various workgroups
	 * @param string $permissions Permissions to be checked before being displayed in Search Results. module|action, which could then be used as $this->isValid('action', 'module')
	 * $param array $extra Any extra fields that might be peculiar to a module, in the format of $item=>$value,
	 *
	 * The last one is useful for categories, eg. forum. $extra = array('forum'=>'id') allows one to do a search in a particular forum
	 */
	public function luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, $license=NULL, $context=NULL, $workgroup=NULL, $permissions=NULL, $extra=NULL)
    {
		
        // Remove Index if it exists
		$this->removeIndex($docId);
		
        // Get Indexer Object
		$this->index = $this->checkIndexPath();
		
        // Setup Lucene Document
        $document = new Zend_Search_Lucene_Document();
		
        // Set the properties that we want to use in our index
		
        // Document
        $document->addField(Zend_Search_Lucene_Field::Keyword('docid', $docId));
		
        // Date
        $document->addField(Zend_Search_Lucene_Field::Keyword('date', $docDate));
		
        // URL
		$document->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));
		
        // Title
		$document->addField(Zend_Search_Lucene_Field::Text('title', $title));
		
        // Contents
		$document->addField(Zend_Search_Lucene_Field::UnStored('contents', strtolower(strip_tags($title.' '.$contents))));
		
		// Teaser
		$document->addField(Zend_Search_Lucene_Field::Text('teaser', $teaser));
		
		// Module
		$document->addField(Zend_Search_Lucene_Field::Keyword('module', $module));
		
		// UserId
		$document->addField(Zend_Search_Lucene_Field::Keyword('userId', $userId));
		
		// License
		$document->addField(Zend_Search_Lucene_Field::Keyword('license', $license));
		
		// Context
		$document->addField(Zend_Search_Lucene_Field::Keyword('context', $context));
		
		// Workgroup
		$document->addField(Zend_Search_Lucene_Field::Keyword('workgroup', $workgroup));
		
		// Permissions
		$document->addField(Zend_Search_Lucene_Field::UnIndexed('permissions', $permissions));
		
        // Add Extra items into arrray
		if (is_array($extra)) {
            
            // Todo: check that fields dont clash with existing
			foreach ($extra as $item->$value)
			{
				$document->addField(Zend_Search_Lucene_Field::Keyword($item, $value));
			}
		}
		
		
        // Add the document to the index
        $this->index->addDocument($document);
		
        //optimize the thing
        $this->index->optimize();
    }
	
    /**
     * Method to remove a document from the search index
     * @param string $docId Unique Document Id - Should be in the format of {module}_{type}_{id}
     */
	public function removeIndex($docId)
	{
		$this->index = $this->checkIndexPath();
		
		// Do a Search in the Doc ID field
		$query = Zend_Search_Lucene_Search_QueryParser::parse('docid:'.$docId);
		
		$hits = $this->index->find($query);
		
		foreach ($hits as $hit) {
			//echo '<li>'.$hit->title.' '.$hit->docid.'</li>';
			$this->index->delete($hit->id);
		}
		
		// Do a Search for the Doc ID field
		$pathTerm  = new Zend_Search_Lucene_Index_Term($docId, 'docid');
		$pathQuery = new Zend_Search_Lucene_Search_Query_Term($pathTerm);
		
		$hits = $this->index->find($pathQuery);
		
		foreach ($hits as $hit) {
			//echo '<li>'.$hit->title.' '.$hit->docid.'</li>';
			$this->index->delete($hit->id);
		}
        
	}
	
    /**
     * Method to get the indexing object
     * This checks whether the index has been created already or not, and recreates them.
     *
     * @return object
     */
	public function checkIndexPath()
	{
		
		//if (!$this->index instanceof Zend_Search_Lucene) {
			// Generate Path to Index
			$indexPath = $this->objConfig->getcontentBasePath().'searchindexes/'.$this->indexName;
			
			// Check if index exists
			if (file_exists($indexPath . '/segments')) {
				
				// Set directory as writeable
				@chmod($indexPath, 0777);
				
				// Open index so that we build onto it
				$this->index = new Zend_Search_Lucene($indexPath);
				
			} else {
				
				// Create Index Directory
				$objMkDir = $this->getObject('mkdir', 'files');
				$objMkDir->mkdirs($indexPath);
				
				// Set directory as writeable
				@chmod($indexPath, 0777);
				
				// Create Inded
				$this->index = new Zend_Search_Lucene($indexPath, true);
			}
			
		//}
		
		return $this->index;
		
		
	}
	
    /**
     * Testing Area
     *
     */
	public function tohirTest()
	{
		$index = $this->checkIndexPath();
		
		//$pathTerm  = new Zend_Search_Lucene_Index_Term('*op*', 'content');
		//$pathQuery = new Zend_Search_Lucene_Search_Query_Wildcard($pathTerm);
		
		//$userQuery = Zend_Search_Lucene_Search_QueryParser::parse('optimizatio');
		
		//$query = Zend_Search_Lucene_Search_QueryParser::parse('wdwq');
		$query = Zend_Search_Lucene_Search_QueryParser::parse('module:faq');
		
		$hits = $index->find($query);
		//$hits = $index->find('contents:optimizatio');
		
		echo '<ol>';
		foreach ($hits as $hit)
		{
			echo '<li>'.$hit->title.' '.$hit->docid.'</li>';
		}
		echo '</ol>';
	}



}
?>