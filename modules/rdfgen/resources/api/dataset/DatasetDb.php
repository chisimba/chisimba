<?php
require_once RDFAPI_INCLUDE_DIR . 'dataset/Dataset.php';
require_once RDFAPI_INCLUDE_DIR . 'model/DbModel.php';
require_once RDFAPI_INCLUDE_DIR . 'dataset/IteratorFindQuadsDb.php';
// ----------------------------------------------------------------------------------
// Class: DatasetDb
// ----------------------------------------------------------------------------------

/**
* Persistent implementation of a Dataset in a database.
* A RDF dataset is a collection of named RDF graphs.
*
* @version  $Id$
* @author Daniel Westphal (http://www.d-westphal.de)
* @author Chris Bizer <chris@bizer.de>
*
* @package 	dataset
* @access	public
**/
require_once(RDFAPI_INCLUDE_DIR.PACKAGE_DBASE);

class DatasetDb extends Dataset
{

	/**
	* Reference to databse connection.
	*
	* @var		resource dbConnection
	* @access	private
	*/
	var $dbConnection;

	/**
	* Reference to the dbStore Object.
	*
	* @var		$dbStore dbStore
	* @access	private
	*/
	var $dbStore;


	/**
	* Name of the Dataset
	*
	* @var		string
	* @access	private
	*/
	var $setName;


	/**
    * Constructor
    * You can supply a Dataset name.
    *
    * @param  ADODBConnection
    * @param  DbStore
    * @param  string
	* @access	public
    */
	function DatasetDb(&$dbConnection,&$dbStore,$datasetName)
	{
		$this->dbConnection=& $dbConnection;
		$this->dbStore=&$dbStore;
		$this->setName= $datasetName;
		$this->_initialize();
	}

	/**
    * Initialize
    * Read all needed data into the set.
    *
    *
	* @access	private
    */
	function _initialize()
	{
		$recordSet =& $this->dbConnection->execute("SELECT defaultModelUri
                                         FROM datasets where datasetName='".$this->setName."'");

   		$this->defaultGraph=& $this->dbStore->getModel($recordSet->fields[0]);
	}



//	=== Graph level methods ========================

	/**
    * Sets the Dataset name. Return true on success, false otherwise.
    *
    * @param  string
	* @access	public
    */
	function setDatasetName($datasetName)
	{
		if ($this->dbStore->datasetExists($datasetName))
			return false;

		$this->dbConnection->execute("UPDATE datasets SET datasetName='".$datasetName."'
                                      where datasetName='".$this->setName."'");

		$this->dbConnection->execute("UPDATE dataset_model SET datasetName='".$datasetName."'
                                      where datasetName='".$this->setName."'");
		$this->setName=$datasetName;
		return true;
	}

	/**
    * Returns the Datasets name.
    *
    * @return string
	* @access	public
    */
	function getDatasetName()
	{
		return $this->setName;
	}

	/**
	 * Adds a NamedGraph to the set.
	 *
	 * @param NamedGraphDb
	 */
	function addNamedGraph(&$graph)
	{
		$graphNameURI=$graph->getGraphName();
		$this->removeNamedGraph($graphNameURI);
		$this->dbConnection->execute('INSERT INTO dataset_model VALUES('
		  . $this->dbConnection->qstr($this->setName) . ','
		  . $this->dbConnection->qstr($graph->modelID) . ','
		  . $this->dbConnection->qstr($graphNameURI) .')');
	}


	/**
	 * Overwrites the existting default graph.
	 *
	 * @param DbModel
	 */
	function setDefaultGraph(&$graph)
	{
		$this->dbConnection->execute('UPDATE datasets SET defaultModelUri ='
		  . $this->dbConnection->qstr($graph->modelURI) . '  WHERE datasetName ='
		  . $this->dbConnection->qstr($this->setName));
	}

	/**
	 * Returns a reference to the defaultGraph.
	 *
	 * @return NamedGraphDb
	 */
	function & getDefaultGraph()
	{
		$defaultGraphURI = $this->dbConnection->GetOne("SELECT defaultModelUri FROM datasets WHERE datasetName ='".$this->setName."'");
		return ($this->dbStore->getNamedGraphDb($defaultGraphURI,'http://rdfapi-php/dataset_defaultGraph_'.$this->setName));
	}

	/**
	 * Returns true, if a defaultGraph exists. False otherwise.
	 *
	 * @return boolean
	 */
	function hasDefaultGraph()
	{
		return true;
	}

	/**
	 * Removes a NamedGraph from the set. Nothing happens
	 * if no graph with that name is contained in the set.
	 *
	 * @param string
	 */
	function removeNamedGraph($graphName)
	{
		$this->dbConnection->execute('DELETE FROM dataset_model WHERE datasetName="'
		  . $this->dbConnection->qstr($this->setName) . '"  AND graphURI ="'
		  . $this->dbConnection->qstr($graphName) . '"');
	}

	/**
	 * Tells wether the Dataset contains a NamedGraph.
	 *
	 * @param  string
	 * @return boolean
	 */
	function containsNamedGraph($graphName)
	{
		$count= $this->dbConnection->GetOne('SELECT count(*) FROM dataset_model WHERE datasetName="'.$this->setName.'"  AND graphURI ="'.$graphName.'"');
		return ($count>0);
	}

	/**
	 * Returns the NamedGraph with a specific name from the Dataset.
	 * Changes to the graph will be reflected in the set.
	 *
	 * @param string
	 * @return NamedGraphDb or null
	 */
	function &getNamedGraph($graphName)
	{
		if(!$this->containsNamedGraph($graphName))
			return null;

		$modelVars =& $this->dbConnection->execute("SELECT models.modelURI, models.modelID, models.baseURI
	                                            	FROM models, dataset_model
	                                            	WHERE dataset_model.graphURI ='" .$graphName ."' AND dataset_model.modelId= models.modelID");

		return new NamedGraphDb($this->dbConnection, $modelVars->fields[0],
                         		$modelVars->fields[1], $graphName ,$modelVars->fields[2]);
	}

	/**
	 * Returns the names of the namedGraphs in this set as strings in an array.
	 *
	 * @return Array
	 */
	function listGraphNames()
	{
		$recordSet =& $this->dbConnection->execute("SELECT graphURI FROM dataset_model WHERE datasetName ='".$this->setName."'");

		$return=array();
		while (!$recordSet->EOF)
		{
		  $return[] = $recordSet->fields[0];
		  $recordSet->moveNext();
		}
		return $return;
	}

	/**
	 * Creates a new NamedGraph and adds it to the set. An existing graph with the same name will be replaced. But the old namedGraph remains in the database.
	 *
	 * @param  string
	 * @param  string
	 * @return NamedGraphDb
	 */
	function &createGraph($graphName,$baseURI = null)
	{
		$graph =& $this->dbStore->getNewNamedGraphDb(uniqid('http://rdfapi-php/namedGraph_'),$graphName,$baseURI);
		$this->addNamedGraph($graph);

		return $graph;
	}

	/**
	 * Deletes all NamedGraphs from the set.
	 */
	function clear()
	{
		$this->dbConnection->execute("DELETE FROM dataset_model WHERE datasetName ='".$this->setName."'");
	}

	/**
	 * Returns the number of NamedGraphs in the set. Empty graphs are counted.
	 *
	 * @return int
	 */
	function countGraphs()
	{
		return ($this->dbConnection->GetOne("SELECT count(*) FROM dataset_model WHERE datasetName ='".$this->setName."'"));
	}

	/**
	 * Returns an iterator over all {@link NamedGraph}s in the set.
	 *
	 * @return IteratorAllGraphsDb
	 */
	function &listNamedGraphs()
	{
		$recordSet =& $this->dbConnection->execute("SELECT graphURI FROM dataset_model WHERE datasetName ='".$this->setName."'");
		$it = new IteratorAllGraphsDb($recordSet, $this);
		return $it;
	}

	/**
	 * Tells wether the set contains any NamedGraphs.
	 *
	 * @return boolean
	 */
	function isEmpty()
	{
		return ($this->countGraphs()==0);
	}

	/**
	 * Add all named graphs of the other dataset to this dataset.
	 *
	 * @param Dataset
	 */
	function addAll($otherDataset)
	{
		for($iterator = $otherDataset->listNamedGraphs(); $iterator->valid(); $iterator->next())
		{
			$this->addNamedGraph($iterator->current());
 		};

 		if ($otherDataset->hasDefaultGraph())
 		{
 			$this->defaultGraph = $this->defaultGraph->unite($otherDataset->getDefaultGraph());
 		}
	}

//	=== Quad level methods ========================

	/**
	 * Adds a quad to the Dataset. The argument must not contain any
	 * wildcards. If the quad is already present, nothing happens. A new
	 * named graph will automatically be created if necessary.
	 *
	 * @param Quad
	 */
	function addQuad(&$quad)
	{
		$graphName=$quad->getGraphName();
		$graphName=$graphName->getLabel();

		$graph=& $this->getNamedGraph($graphName);

		if ($graph===null)
			$graph=& $this->createGraph($graphName);

		$statement=$quad->getStatement();
		$graph->add($statement);
	}

	/**
	 * Tells wether the Dataset contains a quad or
	 * quads matching a pattern.
	 *
	 * @param Resource
	 * @param Resource
	 * @param Resource
	 * @param Resource
	 * @return boolean
	 */
	function containsQuad($graphName,$subject,$predicate,$object)
	{
		// static part of the sql statement
		$sql = "SELECT count(*)
          		FROM statements, dataset_model
           		WHERE datasetName ='".$this->setName."' AND statements.modelID=dataset_model.modelId ";

		if($graphName!=null)
		{
			$sql.= " AND graphURI ='".$graphName->getLabel()."'";
		}

		// dynamic part of the sql statement
		$sql .= DbModel::_createDynSqlPart_SPO($subject, $predicate, $object);

		return (($this->dbConnection->GetOne($sql))>0);
	}

	/**
	 * Deletes a Quad from the RDF dataset.
	 *
	 * @param Quad
	 */
	function removeQuad($quad)
	{
		$graphName=$quad->getGraphName();$graphName=$graphName->getLabel();
		//find namedGraph IDs
		$graphID = $this->dbConnection->GetOne("SELECT modelId FROM dataset_model WHERE graphURI ='$graphName'");

		// static part of the sql statement
		$sql = "DELETE FROM statements WHERE modelID = $graphID";

		// dynamic part of the sql statement
		$sql .= DbModel::_createDynSqlPart_SPO($quad->getSubject(), $quad->getPredicate(), $quad->getObject());

		// execute the query
		if($graphID)
			$recordSet =& $this->dbConnection->execute($sql);
	}

	/**
	 * Counts the Quads in the RDF dataset. Identical Triples in
	 * different NamedGraphs are counted individually.
	 *
	 * @return int
	 */
	function countQuads()
	{
		$sql = "SELECT count(*)
          		FROM statements, dataset_model
           		WHERE datasetName ='".$this->setName."' AND statements.modelID=dataset_model.modelId ";

		return ((int)$this->dbConnection->GetOne($sql));
	}

	/**
	 * Finds Statements that match a quad pattern. The argument may contain
	 * wildcards.
	 *
	 * @param Resource or null
	 * @param Resource or null
	 * @param Resource or null
	 * @param Resource or null
	 * @return IteratorFindQuadsDb
	 */
	function &findInNamedGraphs($graphName,$subject,$predicate,$object,$returnAsTriples =false )
	{
		// static part of the sql statement
		$sql = "SELECT subject, predicate, object, l_language, l_datatype, subject_is, object_is, dataset_model.graphURI
          		FROM statements, dataset_model
           		WHERE datasetName ='".$this->setName."' AND statements.modelID=dataset_model.modelId ";

		if($graphName!=null)
		{
			$sql.= " AND graphURI ='".$graphName->getLabel()."'";
		}

		// dynamic part of the sql statement
		$sql .= DbModel::_createDynSqlPart_SPO($subject, $predicate, $object);

		// execute the query
		$recordSet =& $this->dbConnection->execute($sql);


		$it = new IteratorFindQuadsDb($recordSet, $this, $returnAsTriples);
		return $it;
	}

	/**
	 * Finds Statements that match a pattern in the default Graph. The argument may contain
	 * wildcards.
	 *
	 * @param Resource or null
	 * @param Resource or null
	 * @param Resource or null
	 * @return IteratorFindQuadsDb
	 */
	function &findInDefaultGraph($subject,$predicate,$object)
	{
		$defaultGraphID = (int)$this->dbConnection->GetOne("SELECT models.modelID FROM datasets, models WHERE datasets.datasetName ='".$this->setName."' AND datasets.defaultModelUri = models.modelURI");
		// static part of the sql statement
		$sql = "SELECT subject, predicate, object, l_language, l_datatype, subject_is, object_is
          		FROM statements
           		WHERE modelID ='$defaultGraphID'";

		// dynamic part of the sql statement
		$sql .= DbModel::_createDynSqlPart_SPO($subject, $predicate, $object);

		// execute the query
		$recordSet =& $this->dbConnection->execute($sql);

		$it = new IteratorFindQuadsDb($recordSet, $this, true);
		return $it;
	}
}
?>