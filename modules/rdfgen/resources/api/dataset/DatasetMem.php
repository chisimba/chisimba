<?php
require_once RDFAPI_INCLUDE_DIR . 'dataset/Dataset.php';
require_once RDFAPI_INCLUDE_DIR . 'dataset/NamedGraphMem.php';
require_once RDFAPI_INCLUDE_DIR . 'model/MemModel.php';
// ----------------------------------------------------------------------------------
// Class: DatasetMem
// ----------------------------------------------------------------------------------

/**
* In-memory implementation of a RDF dataset.
* A RDF dataset set is a collection of named RDF graphs.
*
* @version  $Id$
* @author Daniel Westphal (http://www.d-westphal.de)
* @author Chris Bizer <chris@bizer.de>
*
* @package 	dataset
* @access	public
**/

class DatasetMem extends Dataset
{

	/**
	* Reference to a Memmodel that holds the default graph.
	*
	* @var		resource Memmodel
	* @access	private
	*/
	var $defaultGraph;


	/**
	* Name of the DatasetMem.
	*
	* @var		string
	* @access	private
	*/
	var $setName;

	/**
	* List of all NamedGraphs.
	*
	* @var		array
	* @access	private
	*/
	var $graphs=array();

	/**
    * Constructor.
    * You can supply a Dataset name.
    *
    * @param string
	* @access	public
    */
	function DatasetMem($datasetName = null)
	{
		$this->setDatasetName($datasetName);
		$this->defaultGraph=new MemModel();
	}

//	=== Graph level methods ========================

	/**
    * Sets the Datasets name.
    *
    * @param  string
	* @access	public
    */
	function setDatasetName($datasetName)
	{
		$this->setName=$datasetName;
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
	 * Adds a NamedGraph to the set. Will replace a NamedGraph with the same name that is already in the set.
	 *
	 * @param NamedGraphMem
	 */
	function addNamedGraph(&$graph)
	{
		$graphNameURI=$graph->getGraphName();
		$this->graphs[$graphNameURI]=&$graph;
	}

	/**
	 * Overwrites the existting default graph.
	 *
	 * @param MemModel
	 */
	function setDefaultGraph(&$graph)
	{
		$this->defaultGraph=&$graph;
	}

	/**
	 * Returns a reference to the defaultGraph
	 * @return Model
	*/
	function &getDefaultGraph()
	{
		return $this->defaultGraph;
	}

	/**
	 * Returns true, if an defaultGraph exists. False otherwise	.
	 *
	 * @return boolean
	*/
	function hasDefaultGraph()
	{
		return $this->defaultGraph != null;
	}

	/**
	 * Removes a NamedGraph from the set. Nothing happens
	 * if no graph with that name is contained in the set.
	 *
	 * @param string
	 */
	function removeNamedGraph($graphName)
	{
		unset($this->graphs[$graphName]);
	}

	/**
	 * Tells wether the Dataset contains a NamedGraph.
	 *
	 * @param  string
	 * @return boolean
	 */
	function containsNamedGraph($graphName)
	{
		return isset($this->graphs[$graphName]) === true;
	}

	/**
	 * Returns the NamedGraph with a specific name from the Dataset.
	 * Changes to the graph will be reflected in the set.
	 *
	 * @param string
	 * @return NamedGraphMem or NULL
	 */
	function &getNamedGraph($graphName)
	{
		if (!isset($this->graphs[$graphName])) return NULL;
		return $this->graphs[$graphName];
	}

	/**
	 * Returns the names of the namedGraphs in this set as strings in an array.
	 *
	 * @return Array
	 */
	function listGraphNames()
	{
		return array_keys($this->graphs);
	}

	/**
	 * Creates a new NamedGraph and adds it to the set. An existing
	 * graph with the same name will be replaced.The name of the NamedGraph to be created ; must be an URI
	 *
	 * @param  string
	 * @param  string
	 * @return NamedGraphMem
	 */
	function &createGraph($graphName,$baseURI = null)
	{
		$this->graphs[$graphName]=new NamedGraphMem($graphName,$baseURI);
		return $this->getNamedGraph($graphName);
	}

	/**
	 * Deletes all NamedGraphs from the set.
	 */
	function clear()
	{
		$this->graphs = array();
	}

	/**
	 * Returns the number of NamedGraphs in the set. Empty graphs
	 * are counted.
	 *
	 * @return int
	 */
	function countGraphs()
	{
		return count($this->graphs);
	}

	/**
	 * Returns the NamedGraph with a specific offset in the dataset.
	 * Changes to the graph will be reflected in the set.
	 *
	 * @param int
	 * @return NamedGraphMem or null
	 * @access	private
	 */
	function &getGraphWithOffset($offset)
	{
		$i=0;
		foreach ($this->graphs as $graph)
		{
			if (($i++)==$offset)
				return $graph;
		}
		$n = null;
		return $n;
	}

	/**
	 * Returns an iterator over all {@link NamedGraph}s in the set.
	 *
	 * @return IteratorAllGraphsMem
	 */
	function &listNamedGraphs()
	{
        require_once RDFAPI_INCLUDE_DIR . 'dataset/IteratorAllGraphsMem.php';
        $m = new IteratorAllGraphsMem($this);
        return $m;
	}

	/**
	 * Tells wether the set contains any NamedGraphs.
	 *
	 * @return boolean
	 */
	function isEmpty()
	{
		return $this->countGraphs() == 0;
	}

	/**
	 * Adds all named graphs of the other dataset to this dataset.
	 *
	 * @param Dataset
	 */
	function addAll($otherDataset)
	{
		for($iterator = $otherDataset->listNamedGraphs(); $iterator->valid(); $iterator->next())
		{
			$current=$iterator->current();
			$this->graphs[$current->getGraphName()]=$current;
 		}

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
		if ($this->containsNamedGraph($graphName->getLabel())===false)
			$this->createGraph($graphName->getLabel());

		$this->graphs[$graphName->getLabel()]->add($quad->getStatement());
	}


	/**
	 * Tells wether the Dataset contains a quad or
	 * quads matching a pattern.
	 *
	 * @param Resource $graphName
	 * @param Resource $subject
	 * @param Resource $predicate
	 * @param Resource $object
	 * @return boolean
	 */
	function containsQuad($graphName,$subject,$predicate,$object)
	{
		if($graphName!=null)
		{
			if ($this->containsNamedGraph($graphName->getLabel())!==true)
				return false;

			return ($this->graphs[$graphName->getLabel()]->findFirstMatchingStatement($subject,$predicate,$object)!=null);
		}

		foreach ($this->graphs as $graph)
		{
			if ($graph->findFirstMatchingStatement($subject,$predicate,$object)!=null)
				return true;
		};

		return false;
	}

	/**
	 * Deletes a Quad from the RDF dataset.
	 *
	 * @param Quad
	 */
	function removeQuad($quad)
	{
			$graphName=$quad->getGraphName();

			if($graphName!=null)
			{
				if ($this->containsNamedGraph($graphName->getLabel())!==true)
					return;

				return ($this->graphs[$graphName->getLabel()]->remove($quad->getStatement())!=null);

			}
			foreach ($this->graphs as $graph)
			{
				$graph->remove($quad->getStatement());
			};
	}

	/**
	 * Counts the Quads in the RDF dataset. Identical Triples in
	 * different NamedGraphs are counted individually.
	 *
	 * @return int
	 */
	function countQuads()
	{
		$count=0;
		foreach ($this->graphs as $graph)
		{
			$count+=$graph->size();
		}
		return $count;
	}

	/**
	 * Finds Statements that match a quad pattern. The argument may contain
	 * wildcards.
	 *
	 * @param Resource or Null
	 * @param Resourceor Null
	 * @param Resource or Null
	 * @param Resource or Null
	 * @return Iterator
	 */
	function &findInNamedGraphs($graph,$subject,$predicate,$object,$returnAsTriples = false)
	{

		if ($graph!=null)
		{
			$findGraph=&$this->getNamedGraph($graph->getLabel());
			if($findGraph==null)
				$findGraph=new MemModel();

			return $findGraph->iterFind($subject,$predicate,$object);
		}


        require_once RDFAPI_INCLUDE_DIR . 'dataset/IteratorFindQuadsMem.php';
        $m = new IteratorFindQuadsMem($subject,$predicate,$object,$this->listNamedGraphs(),$returnAsTriples);
        return $m;
	}

	/**
	 * Finds Statements that match a pattern in the default Graph. The argument may contain
	 * wildcards.
	 *
	 * @param Resource or Null
	 * @param Resource or Null
	 * @param Resource or Null
	 * @return Iterator
	 */
	function &findInDefaultGraph($subject,$predicate,$object)
	{
		return $this->defaultGraph->iterFind($subject,$predicate,$object);
	}
}
?>