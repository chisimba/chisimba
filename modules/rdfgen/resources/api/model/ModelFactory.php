<?php
require_once RDFAPI_INCLUDE_DIR . 'model/DbStore.php';

// ----------------------------------------------------------------------------------
// Class: ModelFactory
// ----------------------------------------------------------------------------------


/**
* ModelFactory is a static class which provides methods for creating different
* types of RAP models. RAP models have to be created trough a ModelFactory
* instead of creating them directly with the 'new' operator because of RAP's
* dynamic code inclusion mechanism.
*
* @version  $Id: ModelFactory.php 524 2007-08-14 11:12:45Z kobasoft $
* @author Daniel Westphal <mail at d-westphal.de>
* @author Richard Cyganiak <richard@cyganiak.de>
*
*
* @package 	model
* @access	public
**/
class ModelFactory
{
	/**
	* Returns a MemModel.
	* You can supply a base URI
	*
	* @param   string  $baseURI
	* @return	object	MemModel
	* @access	public
	*/
	function & getDefaultModel($baseURI = null)
	{
		return ModelFactory::getMemModel($baseURI);
	}

	/**
	* Returns a NamedGraphSetMem.
    * You can supply a GraphSet name.
    *
    * @param string $graphSetId
    * @param string $uri
	* @access	public
    */
	function & getDatasetMem($graphSetId = null)
	{
        require_once RDFAPI_INCLUDE_DIR . 'dataset/DatasetMem.php';
		$m = new DatasetMem($graphSetId);
		return $m;
	}

	/**
	* Returns a MemModel.
	* You can supply a base URI
	*
	* @param   string  $baseURI
	* @return	object	MemModel
	* @access	public
	*/
	function & getMemModel($baseURI = null)
	{
        require_once RDFAPI_INCLUDE_DIR . 'model/MemModel.php';
		$m = new MemModel($baseURI);
		return $m;
	}

	/**
	* Returns a DbModel with the database connection
	* defined in constants.php.
	* You can supply a base URI. If a model with the given base
	* URI exists in the DbStore, it'll be opened.
	* If not, a new model will be created.
	*
	* @param   string  $baseURI
	* @return	object	DbModel
	* @access	public
	*/
	function & getDefaultDbModel($baseURI = null)
	{
		$dbStore = ModelFactory::getDbStore();
		$m = ModelFactory::getDbModel($dbStore,$baseURI);
		return $m;
	}

	/**
	* Returns a new DbModel using the database connection
	* supplied by $dbStore.
	* You can supply a base URI. If a model with the given base
	* URI exists in the DbStore, it'll be opened.
	* If not, a new model will be created.
	*
	* @param   object	DbStore  $dbStore
	* @param   string  $baseURI
	* @return	object	DbModel
	* @access	public
	*/
	function & getDbModel($dbStore, $baseURI = null)
	{
		if ($dbStore->modelExists($baseURI)) {
			return $dbStore->getModel($baseURI);
        }

		return $dbStore->getNewModel($baseURI);
	}

	/**
	* Returns a database connection with the given parameters.
	* Paramters, which are not defined are taken from the constants.php
	*
	* @param   string   $dbDriver
	* @param   string   $host
	* @param   string   $dbName
	* @param   string   $user
	* @param   string   $password
	* @return	object	DbStore
	* @access	public
	*/
	function & getDbStore($dbDriver=ADODB_DB_DRIVER, $host=ADODB_DB_HOST, $dbName=ADODB_DB_NAME,
                   		$user=ADODB_DB_USER, $password=ADODB_DB_PASSWORD)
	{
		$dbs = new DbStore($dbDriver, $host, $dbName,$user, $password);
		return $dbs;
	}

	/**
	* Returns a InfModelF.
	* (MemModel with forward chaining inference engine)
	* Configurations can be done in constants.php
	* You can supply a base URI
	*
	* @param   string  $baseURI
	* @return	object	MemModel
	* @access	public
	*/
	function & getInfModelF($baseURI = null)
	{
        require_once RDFAPI_INCLUDE_DIR . 'infModel/InfModelF.php';
		$mod = new InfModelF($baseURI);
		return $mod;
	}

	/**
	* Returns a InfModelB.
	* (MemModel with backward chaining inference engine)
	* Configurations can be done in constants.php
	* You can supply a base URI
	*
	* @param   string  $baseURI
	* @return	object	MemModel
	* @access	public
	*/
	function & getInfModelB($baseURI = null)
	{
        require_once RDFAPI_INCLUDE_DIR . 'infModel/InfModelB.php';
		$mod = new InfModelB($baseURI);
		return $mod;
	}

	/**
	* Returns a ResModel.
	* $modelType has to be one of the following constants:
	* MEMMODEL,DBMODEL,INFMODELF,INFMODELB to create a resmodel with a new
	* model from defined type.
	* You can supply a base URI
	*
	* @param   constant  $modelType
	* @param   string  $baseURI
	* @return	object	ResModel
	* @access	public
	*/
	function & getResModel($modelType, $baseURI = null)
	{
		switch ($modelType) {
			case DBMODEL:
				$baseModel = ModelFactory::getDefaultDbModel($baseURI);
				break;

			case INFMODELF:
				$baseModel = ModelFactory::getInfModelF($baseURI);
				break;

			case INFMODELB:
				$baseModel = ModelFactory::getInfModelB($baseURI);
				break;

			default:
				$baseModel = ModelFactory::getMemModel($baseURI);
				break;
		}
		return ModelFactory::getResModelForBaseModel($baseModel);
	}

	/**
	* Creates a ResModel that wraps an existing base model.
	*
	* @param   	object  Model	$baseModel
	* @return	object	ResModel
	* @access	public
	*/
    function &getResModelForBaseModel(&$baseModel) {
        require_once RDFAPI_INCLUDE_DIR . 'resModel/ResModel.php';
        $mod = new ResModel($baseModel);
        return $mod;
    }

	/**
	* Returns an OntModel.
	* $modelType has to be one of the following constants:
	* MEMMODEL, DBMODEL, INFMODELF, INFMODELB to create a OntModel
	* with a new model from defined type.
	* $vocabulary defines the ontology language. Currently only
	* RDFS_VOCABULARY is supported. You can supply a model base URI.
	*
	* @param   	constant  	$modelType
	* @param   	constant  	$vocabulary
	* @param   	string  	$baseURI
	* @return	object		OntModel
	* @access	public
	*/
	function & getOntModel($modelType,$vocabulary, $baseURI = null)
	{
		switch ($modelType)
		{
			case DBMODEL:
				$baseModel = ModelFactory::getDefaultDbModel($baseURI);
				break;

			case INFMODELF:
				$baseModel = ModelFactory::getInfModelF($baseURI);
				break;

			case INFMODELB:
				$baseModel = ModelFactory::getInfModelB($baseURI);
				break;

			default:
				$baseModel = ModelFactory::getMemModel($baseURI);;
		}

        $mod = ModelFactory::getOntModelForBaseModel($baseModel, $vocabulary);
        return $mod;
	}

	/**
	* Creates an OntModel that wraps an existing base model.
	* $vocabulary defines the ontology language. Currently only
	* RDFS_VOCABULARY is supported.
	*
	* @param   	object  Model	$baseModel
	* @param   	constant  	$vocabulary
	* @return	object		OntModel
	* @access	public
	*/
	function &getOntModelForBaseModel(&$baseModel, $vocabulary)
	{
        require_once RDFAPI_INCLUDE_DIR . 'ontModel/OntModel.php';

		switch ($vocabulary)
		{
			case RDFS_VOCABULARY:
				require_once(RDFAPI_INCLUDE_DIR.'ontModel/'.RDFS_VOCABULARY);
				$vocab_object = new RdfsVocabulary();
				break;
			default:
                trigger_error("Unknown vocabulary constant '$vocabulary'; only RDFS_VOCABULARY is supported", E_USER_WARNING);
                $vocab_object = null;
				break;
		}
		$mod = new OntModel($baseModel, $vocab_object);
		return $mod;
	}



	/**
	* Creates a SparqlClient.
	*
	* @param   	String  $server	Link to a SPARQL endpoint.
	* @return	SparqlClient the SparqlClient object.
	* @access	public
	*/
	function & getSparqlClient($server){
		$cl = new SparqlClient($server);
		return $cl;
	}
}
?>