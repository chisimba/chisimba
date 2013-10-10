<?php
// ----------------------------------------------------------------------------------
// Class: ResModel
// ----------------------------------------------------------------------------------

/**
* A ResModel provides an resource centric view on an underlying RDF model.
* ResModels show information not as statements but as resources with
* properties, similar to Jena models. ResModels may create Resources [URI
* nodes and bnodes]. Creating a Resource does not make the Resource visible to
* the model; Resources are only "in" Models if Statements about them are added
* to the Model. Similarly the only way to "remove" a Resource from a Model is
* to remove all the Statements that mention it.
* 
* When a Resource or Literal is created by a Model, the Model is free to re-use an existing 
* Resource or Literal object with the correct values, or it may create a fresh one.
*
* @version  $Id: ResModel.php 562 2008-02-29 15:30:18Z cax $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	resModel
* @access	public
**/
 
class ResModel
{
	
	/**
	* Holds a reference to the assoiated memmodel/dbmodel/infmodel
	* @var		ResResource
	* @access	private
	*/
	var $model;

	
	/**
    * Constructor
	* You have to supply a memmodel/dbmodel/infmodel to save the statements.
    *
    * @param object model $model 
	* @access	public
    */	
	function ResModel(& $model)
	{
		if (!is_a($model,'Model'))
			trigger_error(RDFAPI_ERROR . '(class: ResourceLayer; method: ResourceLayer): 
				$model has to be object of class Model', E_USER_ERROR);	
		
		$this->model =& $model;			
	}
	
	/**
	* Create a new resource associated with this model. 
	* If the uri string isn't set, this creates a bnode. 
	* Otherwise it creates a URI node. 
	* A URI resource is .equals() to any other URI Resource with the same URI 
	* (even in a different model - be warned).
	* 
	* This method may return an existing Resource with the correct URI and model, 
	* or it may construct a fresh one, as it sees fit.
	*
	* Operations on the result Resource may change this model. 
	*
   	* @param	string	$uri
   	* @return	object ResResource 
   	* @access	public
   	*/
	function createResource($uri = null)
	{
		$resResource = new ResResource($uri);
		//associate the resource with this model, and get a unique identifier
		//if it is bnode.
		$resResource->setAssociatedModel($this);
		
		return $resResource;
	} 
	
	/**
	* Create a new Property associated with this model. 
	* This method may return an existing property with the correct URI and model, 
	* or it may construct a fresh one, as it sees fit.
	*
	* Subsequent operations on the returned property may modify this model. 
	*  
	*
   	* @param	string	$uri
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function createProperty($uri = null)
	{
		$resProperty = new ResProperty($uri);
		$resProperty->setAssociatedModel($this);
			
		return $resProperty;
	}
	
	/**
	* Create an untyped literal from a String value with a specified language.
	*
	* If you want to type this literal, you have to set a datatype before
	* adding it to the model.
	*  
	*
   	* @param	string	$label
   	* @param	string	$languageTag
   	* @return	object ResLiteral 
   	* @access	public
   	*/	
	function createLiteral($label,$languageTag = null)
	{
		$resLiteral = new ResLiteral($label,$languageTag);
		$resLiteral->setAssociatedModel($this);
		
		return $resLiteral;
	}
	
	/**
	* General method to search for triples.
	* NULL input for any parameter will match anything.
	* Example:  $result = $m->find( NULL, NULL, $node );
	* Finds all Statements with $node as object.
	* Returns an array of statements with ResResources.
	*
	* @param	object ResResource	$subject
	* @param	object ResResource	$predicate
	* @param	object ResResource	$object
	* @return	array
	* @access	public
	* @throws	PhpError
	*/
	function find($subject,$predicate, $object)
	{
		$result=array();
		//convert ResResources to Resources and Blanknodes
		$resmodel=$this->model->find(	$this->_resNode2Node($subject),
										$this->_resNode2Node($predicate),
										$this->_resNode2Node($object)
									 );
		//convert Resources, Blanknodes to ResResources							 
		foreach ($resmodel->triples as $statement) 
		{
			$result[]=new Statement($this->_node2ResNode($statement->getSubject()),
									$this->_node2ResNode($statement->getPredicate(),true),
									$this->_node2ResNode($statement->getObject())
								    );
		};
		return $result;
		
	}
	
	/**
	* Searches for triples and returns the first matching statement.
	* NULL input for any parameter will match anything.
	* Example:  $result = $m->findFirstMatchingStatement( NULL, NULL, $node );
	* Returns the first statement with ResResources of the Model where the object equals $node.
	* Returns an NULL if nothing is found.
	* You can define an offset to search.
	*
	* @param	object Node	$subject
	* @param	object Node	$predicate
	* @param	object Node	$object
	* @param	integer $offset
	* @return	object Statement      
	* @access	public
	*/
	function findFirstMatchingStatement($subject,$predicate,$object,$offset = 0)
	{
	
		$statement = $this->model->findFirstMatchingStatement(	$this->_resNode2Node($subject),
																$this->_resNode2Node($predicate),
																$this->_resNode2Node($object),
																$offset
															  );
		if ($statement!==null)
		{											
			return new Statement(	$this->_node2ResNode($statement->getSubject()),
									$this->_node2ResNode($statement->getPredicate(),true),
									$this->_node2ResNode($statement->getObject())
							    );
		} else 
		{
			return null;
		}
	}
	
	/**
	* Adds a new triple to the Model without checking if the statement is already in the Model.
	* So if you want a duplicate free Model use the addWithoutDuplicates() function (which is slower then add())
	* Expects a statements with ResResources(ResLiterals)
	*
	* @param	object Statement	$statement
	* @access	public
	* @throws	PhpError 
	*/
	function add($statement)
	{
		return $this->model->add(new Statement(	$this->_resNode2Node($statement->getSubject()),
												$this->_resNode2Node($statement->getPredicate()),
												$this->_resNode2Node($statement->getObject()))
											   );		
	}
	/**
	* Checks if a new statement is already in the Model and adds the statement, if it is not in the Model.
	* addWithoutDuplicates() is significantly slower then add(). 
	* Retruns TRUE if the statement is added.
	* FALSE otherwise.
	* Expects a statements with ResResources(ResLiterals)
	*
	* @param	object Statement	$statement
	* @return   boolean
	* @access	public
	* @throws	PhpError 
	*/
	function addWithoutDuplicates($statement)
	{
		return $this->model->addWithoutDuplicates(new Statement($this->_resNode2Node($statement->getSubject()),
																$this->_resNode2Node($statement->getPredicate()),
																$this->_resNode2Node($statement->getObject()))
															    );	
	}
	
	/**
	* Tests if the Model contains the given statement.
	* TRUE if the statement belongs to the model;
	* FALSE otherwise.
	* Expects a statement of ResResources(ResLiterals)
	*
	* @param	object Statement	$statement
	* @return	boolean
	* @access	public
	*/
	function contains(& $statement)
	{
		
		return $this->model->contains(new Statement($this->_resNode2Node($statement->getSubject()),
													$this->_resNode2Node($statement->getPredicate()),
													$this->_resNode2Node($statement->getObject()))
													);
	}
	
	/**
	* Determine if all of the statements in a model are also contained in this model.
	* True if all of the statements in $model are also contained in this model and false otherwise.
	*
	* @param	object Model	&$model
	* @return	boolean
	* @access	public
	*/
	function containsAll(& $model)
	{
		if (is_a($model,'ResModel'))
			return $this->model->containsAll($model->getModel());
		
		return $this->model->containsAll($model);
	}
	
	/**
	* Determine if any of the statements in a model are also contained in this model.
	* True if any of the statements in $model are also contained in this model and false otherwise.
	*
	* @param	object Model	&$model
	* @return	boolean
	* @access	public
	*/	
	function containsAny(& $model)
	{
		if (is_a($model,'ResModel'))
			return $this->model->containsAny($model->getModel());
		return $this->model->containsAny($model);
	}
	
	/**
	* Determine if the node (ResResource / ResLiteral) $node appears in any statement of this model.
	*
	* @param	object Node	&$node
	* @return	boolean
	* @access	public
	*/	
	function containsResource(& $node)
	{
		if ($this->findFirstMatchingStatement($node,null,null) === null)
			if ($this->findFirstMatchingStatement(null,$node,null) === null)
				if ($this->findFirstMatchingStatement(null,null,$node) === null)
					return false;
					
		return true;
	}
	
	/**
	* Create a literal from a String value with the $dtype Datatype 
	* An existing literal of the right value may be returned, or a fresh one created. 
	*
	* @param	string	$value
	* @param	string 	$dtype
	* @return 	object ResLiteral
	* @access	public
	*/
	function createTypedLiteral($value,$dtype)
	{
		$resLiteral = new ResLiteral($value);
		$resLiteral->setDatatype($dtype);
		$resLiteral->setAssociatedModel($this);
		
		return $resLiteral;
	}
	
	/**
	* Checks if two models are equal.
	* Two models are equal if and only if the two RDF graphs they represent are isomorphic.
	* 
	* Warning: This method doesn't work correct with models where the same blank node has different 
	* identifiers in the two models. We will correct this in a future version.
	*
	* @access	public 
	* @param	object	model &$that
	* @throws    phpErrpr
	* @return	boolean 
	*/		
	function equals(& $that)
	{
		if (is_a($that,'ResModel'))
			return $this->model->equals($that->getModel());
		return $this->model->equals($that);	
	}
	
	/** 
	* Returns a new model that is the subtraction of another model from this model.
	*
	* @param	object Model $model
	* @return	object MemModel
	* @access	public
	* @throws phpErrpr
	*/ 
	function subtract($model)
	{
		if (is_a($model,'ResModel'))
			return $this->model->subtract($model->getModel());
		return $this->model->subtract($model);
	}
	
	/** 
	* Answer a statement find(s, p, null) with ResResources(ResLiterals) from this model. 
	* If none exist, return null; if several exist, pick one arbitrarily.
	*
	* @param	object ResResource $subject
	* @param	object ResResource $property
	* @return	object Statement
	* @access	public
	* @throws phpErrpr
	*/ 
	function getProperty($subject,$property)
	{
	
		$statement= $this->model->findFirstMatchingStatement(	$this->_resNode2Node($subject),
																$this->_resNode2Node($property),
																null
															);
		if ($statement === null)
			return null;
																
		return new Statement($this->_node2ResNode($statement->getSubject()),
									$this->_node2ResNode($statement->getPredicate(),true),
									$this->_node2ResNode($statement->getObject())
							);
													
	}
	
	/**
	* Checks if MemModel is empty
	*
	* @return	boolean
	* @access	public
	*/
	function isEmpty()
	{
		return $this->model->isEmpty();
	}
	
	/** 
	* Returns a ResIterator with all objects in a model.
	*
	* @return	object ResIterator
	* @access	public
	* @throws phpErrpr
	*/
	function listObjects()
	{
		return $this->listObjectsOfProperty(null);
	}
	
	/** 
	* Returns a ResIterator with all objects with a given property and property value.
	*
	* @param	object ResResource	$property
	* @param	object ResResource	$value
	* @return	object ResIterator
	* @access	public
	*/
	function listObjectsOfProperty($property, $value = null)
	{
		return new ResIterator(null,$property,$value,'o',$this);
	}

	
	/** 
	* Returns a ResIterator with all subjects in a model.
	*
	* @return	object ResIterator
	* @access	public
	* @throws phpErrpr
	*/
	function listSubjects()
	{
		return $this->listSubjectsWithProperty(null);
	}
	
	/** 
	* Returns a ResIterator with all subjects with a given property and property value.
	*
	* @param	object ResResource	$property
	* @param	object ResResource	$value
	* @return	object ResIterator
	* @access	public
	* @throws phpErrpr
	*/
	function listSubjectsWithProperty($property,$value = null)
	{
		return new ResIterator(null,$property,$value,'s',$this);
	}
	
	/**
	* Removes the statement of ResResources(ResTriples) from the MemModel. 
	* TRUE if the statement is removed.
	* FALSE otherwise.
	*
	* @param	object Statement	$statement
	* @return   boolean
	* @access	public
	* @throws	PhpError
	*/	
	function remove($statement)
	{
		return $this->model->remove(new Statement(	$this->_resNode2Node($statement->getSubject()),
													$this->_resNode2Node($statement->getPredicate()),
													$this->_resNode2Node($statement->getObject())
												  ));
	}
	
	/**
	* Number of statements in the MemModel
	*
	* @return	integer
	* @access	public
	*/
	function size()
	{
		return $this->model->size();
	}
	
	/** 
	* Returns a new Model that is the set-union of the model with another model.
	* Duplicate statements are removed. If you want to allow duplicates, use addModel() which is much faster.
	*
	* The result of taking the set-union of two or more RDF graphs (i.e. sets of triples) 
	* is another graph, which we will call the merge of the graphs. 
	* Each of the original graphs is a subgraph of the merged graph. Notice that when forming 
	* a merged graph, two occurrences of a given uriref or literal as nodes in two different 
	* graphs become a single node in the union graph (since by definition they are the same 
	* uriref or literal) but blank nodes are not 'merged' in this way; and arcs are of course 
	* never merged. In particular, this means that every blank node in a merged graph can be 
	* identified as coming from one particular graph in the original set of graphs.
	* 
	* Notice that one does not, in general, obtain the merge of a set of graphs by concatenating 
	* their corresponding N-triples documents and constructing the graph described by the merged 
	* document, since if some of the documents use the same node identifiers, the merged document 
	* will describe a graph in which some of the blank nodes have been 'accidentally' merged. 
	* To merge Ntriples documents it is necessary to check if the same nodeID is used in two or 
	* more documents, and to replace it with a distinct nodeID in each of them, before merging the 
	* documents. (Not implemented yet !!!!!!!!!!!)
	*
	* @param	object Model	$model
	* @return	object MemModel
	* @access	public
	* @throws phpErrpr
	*
	*/
	function & unite(& $model)
	{
		if (is_a($model,'ResModel'))
			return $this->model->unite($model->getModel());
		return $this->model->unite($model);
	}
	
	/** 
	* Adds another model to this MemModel.
	* Duplicate statements are not removed. 
	* If you don't want duplicates, use unite().
	* If any statement of the model to be added to this model contains a blankNode 
	* with an identifier already existing in this model, a new blankNode is generated.
	*
	* @param	object Model	$model 
	* @access	public
	* @throws phpErrpr
	*
	*/
	function addModel(&$model)  
	{
		if (is_a($model,'ResModel'))
			return $this->model->addModel($model->getModel());
		return $this->model->addModel($model);
	}
	
	/**
	* Create a new RDF Container from type rdf:Alt 
	* This method may return an existing container with the correct URI and model, 
	* or it may construct a fresh one, as it sees fit.
	*
	* Subsequent operations on the returned Container may modify this model. 
	*  
	*
   	* @param	string	$uri
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function createAlt($uri = null)
	{
		$resAlt = new ResAlt($uri);
		$resAlt->setAssociatedModel($this);
			
		return $resAlt;		
	}
	
	/**
	* Create a new RDF Container from type rdf:Bag 
	* This method may return an existing container with the correct URI and model, 
	* or it may construct a fresh one, as it sees fit.
	*
	* Subsequent operations on the returned Container may modify this model. 
	*  
	*
   	* @param	string	$uri
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function createBag($uri = null)
	{
		$resBag = new ResBag($uri);
		$resBag->setAssociatedModel($this);
			
		return $resBag;
	}
	
	/**
	* Create a new RDF Container from type rdf:Seq 
	* This method may return an existing container with the correct URI and model, 
	* or it may construct a fresh one, as it sees fit.
	*
	* Subsequent operations on the returned Container may modify this model. 
	*  
	*
   	* @param	string	$uri
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function createSeq($uri = null)
	{
		$resSeq = new ResSeq($uri);
		$resSeq->setAssociatedModel($this);
			
		return $resSeq;
	}	
	
	/**
	* Create a new RDF Collection from type rdf:List 
	* This method may return an existing container with the correct URI and model, 
	* or it may construct a fresh one, as it sees fit.
	*
	* Subsequent operations on the returned Container may modify this model. 
	*  
	*
   	* @param	string	$uri
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function createList($uri = null)
	{
		$resList = new ResList($uri);
		$resList->setAssociatedModel($this);
			
		return $resList;	
	}
	
	/**
	* Returns a reference to the underlying model (Mem/DB/InfModel) that contains the statements 
	*  
	*
   	* @return	object Model 
   	* @access	public
   	*/	
	function & getModel()
	{
		return  $this->model;
	}
	
	
	/**
	* Internal method, that returns a resource URI that is unique for the Model.
	* URIs are generated using the base_uri of the Model, the prefix and a unique number.
	* If no prefix is defined, the bNode prefix, defined in constants.php, is used.
	*
	* @param	string	$prefix
	* @return	string
	* @access	private
	*/
	function getUniqueResourceURI($bnodePrefix)
	{
		return $this->model->getUniqueResourceURI($bnodePrefix);
	}
	
	/**
	* Load a model from a file containing RDF, N3 or N-Triples.
	* This function recognizes the suffix of the filename (.n3 or .rdf) and
	* calls a suitable parser, if no $type is given as string ("rdf" "n3" "nt");
	* If the model is not empty, the contents of the file is added to this DbModel.
	*
	* @param 	string 	$filename
	* @param 	string 	$type
	* @param   boolean $stream
	* @access	public
	*/
	function load($filename, $type = NULL, $stream=false)
	{
		$this->model->load($filename, $type, $stream);
	}
	
	/**
	* Return current baseURI.
	*
	* @return  string
	* @access	public
	*/
	function getBaseURI()  
	{
		return $this->model->getBaseURI();
	}

	/**
	* Saves the RDF,N3 or N-Triple serialization of the MemModel to a file.
	* You can decide to which format the model should be serialized by using a
	* corresponding suffix-string as $type parameter. If no $type parameter
	* is placed this method will serialize the model to XML/RDF format.
	* Returns FALSE if the MemModel couldn't be saved to the file.
	*
	* @access	public 
	* @param 	string 	$filename
	* @param 	string 	$type
	* @throws   PhpError
	* @return	boolean   
	*/  
	function saveAs($filename, $type ='rdf') 
	{
		return $this->model->saveAs($filename, $type ='rdf');
	}
	
	/**
	* Writes the RDF serialization of the MemModel as HTML table.
	*
	* @access	public 
	*/  
	function writeAsHTMLTable()
	{
		$this->model->writeAsHtmlTable();
	}
	
	/** 
	* Returns a new model containing all the statements which are in both this model and another.
	*
	* @param	object Model	$model
	* @return	object MemModel
	* @access	public
	* @throws phpErrpr
	*/ 
	function & intersect(& $model)
	{
		if (is_a($model,'ResModel'))
			return $this->model->intersect($model->getModel());
		return $this->model->intersect($model);
	}
	
	/** 
	* converts a Resource,Blanknode,Literal into a ResResource, ResProperty, or ResLiteral
	*
	* @param	object Node	$node
	* @param	boolean		$isProperty
	* @return	object ResResource / ResProperty / ResLiteral
	* @access	private
	* @throws phpErrpr
	*/ 
	function _node2ResNode($node, $isProperty = false)
	{
		if (is_a($node,'Literal'))
		{
			$return= new ResLiteral($node->getLabel(),$node->getLanguage());
			$return->setDatatype($node->getDatatype());
			$return->setAssociatedModel($this);

			return $return;
		}
		if (is_a($node,'Resource'))
		{
			if ($isProperty)
			{
				$res= new ResProperty($node->getLabel());
			} else 
			{
				$res= new ResResource($node->getLabel());
			}
			$res->setAssociatedModel($this);
			if (is_a($node,'Blanknode'))
				$res->setIsAnon(true);	
		
			return $res;
		}
	}
	
	/** 
	* converts a ResResource, ResProperty, or ResLiteral into a Resource, Blanknode, or Literal
	*
	* @param	object ResNode	$resNode
	* @return	object Node
	* @access	private
	* @throws phpErrpr
	*/ 
	function _resNode2Node($resNode)
	{
		if (is_a($resNode,'ResResource'))
		{
			if ($resNode->getIsAnon())	
			{
				$return=new BlankNode($resNode->getURI());
			} else 
			{
				$return=new Resource($resNode->getURI());	
			}	
		return $return;	
		}
		
		if (is_a($resNode,'ResLiteral'))
		{
			$literal=new Literal($resNode->getLabel(),$resNode->getLanguage());
			if ($resNode->getDatatype() != null)
				$literal->setDatatype($resNode->getDatatype());
			return $literal;
		}
	}
	
	/**
	* Set a base URI for the MemModel.
	* Affects creating of new resources and serialization syntax.
	* If the URI doesn't end with # : or /, then a # is added to the URI. 
	* @param	string	$uri
	* @access	public
	*/
	function setBaseURI($uri) 
	{
		$this->model->setBaseURI($uri);
	}
	
	/**
	* Writes the RDF serialization of the MemModel as HTML table.
	*
	* @access	public 
	* @return	string 
	*/  
	function writeRdfToString() 
	{
		return $this->model->writeRdfToString();	
	}
	
	/**
	* Perform an RDQL query on this MemModel.
	* This method returns an associative array of variable bindings.
	* The values of the query variables can either be RAP's objects (instances of Node)
	* if $returnNodes set to TRUE, or their string serialization.
	*
	* @access	public
	* @param string $queryString
	* @param boolean $returnNodes
	* @return  array   [][?VARNAME] = object Node  (if $returnNodes = TRUE)
	*      OR  array   [][?VARNAME] = string
	*
	*/
	function & rdqlQuery($queryString, $returnNodes = TRUE) 
	{
		$ret = $this->model->rdqlQuery($queryString, $returnNodes);
		return $ret;
	}
	
	/**
	* Perform an RDQL query on this MemModel.
	* This method returns an RdqlResultIterator of variable bindings.
	* The values of the query variables can either be RAP's objects (instances of Node)
	* if $returnNodes set to TRUE, or their string serialization.
	*
	* @access	public
	* @param string $queryString
	* @param boolean $returnNodes
	* @return  object RdqlResultIterator = with values as object Node  (if $returnNodes = TRUE)
	*      OR  object RdqlResultIterator = with values as strings if (if $returnNodes = FALSE)
	*
	*/
	function rdqlQueryAsIterator($queryString, $returnNodes = TRUE) 
	{
		return $this->model->rdqlQueryAsIterator($queryString, $returnNodes);
	}
	
	
	/**
	* Returns the models namespaces.
	*
	* @author   Tobias Gauﬂ <tobias.gauss@web.de>
	* @access   public
	* @return   Array
	*/
	function getParsedNamespaces(){
		return $this->model->getParsedNamespaces();
	}



	/**
	* Adds the namespaces to the model. This method is called by 
	* the parser. !!!! addParsedNamespaces() not overwrites manual
	* added namespaces in the model !!!!
	*
	* @author   Tobias Gauﬂ <tobias.gauss@web.de>
	* @access   public
	* @param    Array $newNs
	*/
	function addParsedNamespaces($newNs){
		$this->model->addParsedNamespaces($newNs);
	}


	/**
	* Adds a namespace and prefix to the model.
	*
	* @author   Tobias Gauﬂ <tobias.gauss@web.de>
	* @access   public
	* @param    String $prefix, String $nmsp
	*/
	function addNamespace($prefix, $namespace){
		$this->model->addNamespace($prefix, $namespace);
	}
	
	/**
	* removes a single namespace from the model
	*
	* @author   Tobias Gauﬂ <tobias.gauss@web.de>
	* @access   public
	* @param    String $nmsp
	*/
	function removeNamespace($nmsp){
		return $this->model->removeNamespace($nmsp);
	}

	
	
	
	
	
}

?>