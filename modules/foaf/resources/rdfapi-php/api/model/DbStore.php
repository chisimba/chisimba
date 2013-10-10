<?php
require_once RDFAPI_INCLUDE_DIR . 'constants.php';
require_once RDFAPI_INCLUDE_DIR . 'util/Object_rap.php';

// ----------------------------------------------------------------------------------
// Class: DbStore
// ----------------------------------------------------------------------------------

/**
 * DbStore is a persistent store of RDF data using relational database technology.
 * DbStore uses ADOdb Library for PHP V3.60 (http://php.weblogs.com/ADODB),
 * which allows to connect to multiple databases in a portable manner.
 * This class also provides methods for creating tables for MsAccess, MySQL, and MS SQL Server.
 * If you want to use other databases, you will have to create tables by yourself
 * according to the abstract database schema described in the API documentation.
 *
 * You can activate debug mode by defining ADODB_DEBUG_MODE to "1".
 *
 *
 * @version  $Id: DbStore.php 7228 2007-09-27 06:24:51Z kudakwashe $
 * @author   Radoslaw Oldakowski <radol@gmx.de>
 * @author   Daniel Westphal (http://www.d-westphal.de)
 *
 * @package model
 * @access	public
 */


class DbStore extends Object_rap{

/**
 * Database connection object
 *
 * @var     object ADOConnection
 * @access	private
 */
 var $dbConn;

    /**
    *   SparqlParser so we can re-use it
    *   @var Parser
    */
    var $queryParser = null;


/**
 * Constructor:
 * Set the database connection with the given parameters.
 *
 * @param   string   $dbDriver
 * @param   string   $host
 * @param   string   $dbName
 * @param   string   $user
 * @param   string   $password
 * @access	public
 */
 function DbStore ($dbDriver=ADODB_DB_DRIVER, $host=ADODB_DB_HOST, $dbName=ADODB_DB_NAME,
                   $user=ADODB_DB_USER, $password=ADODB_DB_PASSWORD) {

   // include DBase Package
   require_once(RDFAPI_INCLUDE_DIR.PACKAGE_DBASE);

   // create a new connection object
   $this->dbConn =& ADONewConnection($dbDriver);

   //activate the ADOdb DEBUG mode
   if (ADODB_DEBUG_MODE == '1')
        $this->dbConn->debug = true;

   // connect to database
   $r = $this->dbConn->connect($host, $user, $password, $dbName);
   if ($r !== true) {
      throw new Exception('Could not connect to database');
   }

   // optimized for speed
   $this->dbConn->setFetchMode(ADODB_FETCH_NUM);
   //$ADODB_COUNTRECS = FALSE;
 }


/**
 * Create tables and indexes for the given database type.
 * Currently supported: MsAccess and MySQL.
 * If you want to use other databases, you will have to create tables by yourself
 * according to the abstract <a href="database_schema.html">database schema</a>
 * described in the API documentation.
 *
 * @param   string  $databaseType
 * @throws	PhpError
 * @access	public
 */
 function createTables($databaseType) {

   if (!strcasecmp($databaseType, 'MsAccess'))
   $this->_createTables_MsAccess();
   elseif (!strcasecmp($databaseType, 'MySQL'))
       $this->_createTables_MySql();
   elseif (!strcasecmp($databaseType, 'MSSQL'))
   		$this->_createTables_mssql();
   else {
       $errmsg = RDFAPI_ERROR . "(class: DbStore; method: createTables('$databaseType')):
                                  Currently only MsAcces, MySQL and MSSQL supported.";
       trigger_error($errmsg, E_USER_ERROR);
   }
 }


/**
 * List all DbModels stored in the database.
 *
 * @return  array
 * @throws	SqlError
 * @access	public
 */
 function listModels() {

   $recordSet =& $this->dbConn->execute("SELECT modelURI, baseURI
                                         FROM models");
   if (!$recordSet)
      echo $this->dbConn->errorMsg();
   else {
      $models = array();
      $i=0;
      while (!$recordSet->EOF) {

          $models[$i]['modelURI'] = $recordSet->fields[0];
          $models[$i]['baseURI'] = $recordSet->fields[1];

          ++$i;
          $recordSet->moveNext();
      }
      return $models;
   }
 }


/**
 * Check if the DbModel with the given modelURI is already stored in the database
 *
 * @param   string   $modelURI
 * @return  boolean
 * @throws	SqlError
 * @access	public
 */
 function modelExists($modelURI) {

   $res =& $this->dbConn->execute("SELECT COUNT(*) FROM models
                                   WHERE modelURI = '" .$modelURI ."'");
   if (!$res)
      echo $this->dbConn->errorMsg();
   else {
      if (!$res->fields[0]) {
          $res->Close();
         return FALSE;
      } else {
          $res->Close();
          return TRUE;
      }
   }
 }



    /**
    * Returns the database connection object
    *
    * @return ADOdb Database object
    * @access public
    */
    function &getDbConn()
    {
        return $this->dbConn;
    }


/**
 * Create a new instance of DbModel with the given $modelURI and
 * load the corresponding values of modelID and baseURI from the database.
 * Return FALSE if the DbModel does not exist.
 *
 * @param   string   $modelURI
 * @return  object DbModel
 * @access	public
 */
 function getModel($modelURI) {

   if (!$this->modelExists($modelURI))
      return FALSE;
   else {
      $modelVars =& $this->dbConn->execute("SELECT modelURI, modelID, baseURI
                                            FROM models
                                            WHERE modelURI='" .$modelURI ."'");

      return new DbModel($this->dbConn, $modelVars->fields[0],
                         $modelVars->fields[1], $modelVars->fields[2]);
   }
 }


/**
 * Create a new instance of DbModel with the given $modelURI
 * and insert the DbModel variables into the database.
 * Return FALSE if there is already a model with the given URI.
 *
 * @param   string   $modelURI
 * @param   string   $baseURI
 * @return  object DbModel
 * @throws  SqlError
 * @access	public
 */
 function getNewModel($modelURI, $baseURI=NULL) {

   if ($this->modelExists($modelURI))
      return FALSE;
   else {
      $modelID = $this->_createUniqueModelID();

      $rs =& $this->dbConn->execute("INSERT INTO models
                                            (modelID, modelURI, baseURI)
                                            VALUES ('" .$modelID ."',
                                                    '" .$modelURI ."',
                                                    '" .$baseURI ."')");
      if (!$rs)
         $this->dbConn->errorMsg();
      else
         return new DbModel($this->dbConn, $modelURI, $modelID, $baseURI);
   }
 }


/**
 * Store a MemModel or another DbModel from a different DbStore in the database.
 * Return FALSE if there is already a model with modelURI matching the modelURI
 * of the given model.
 *
 * @param   object Model  &$model
 * @param   string $modelURI
 * @return  boolean
 * @access	public
 */
 function putModel(&$model, $modelURI=NULL) {

   if (!$modelURI) {
      if (is_a($model, 'MemModel'))
         $modelURI = 'DbModel-' .$this->_createUniqueModelID();
      else
         $modelURI = $model->modelURI;
   }else
      if ($this->modelExists($modelURI))
         return FALSE;


   $newDbModel = $this->getNewModel($modelURI, $model->getBaseURI());
   $newDbModel->addModel($model);
 }


/**
 * Close the DbStore.
 * !!! Warning: If you close the DbStore all active instances of DbModel from this
 * !!!          DbStore will lose their database connection !!!
 *
 * @access	public
 */
 function close() {

   $this->dbConn->close();
   unset($this);
 }


// =============================================================================
// **************************** private methods ********************************
// =============================================================================


/**
 * Create a unique ID for the DbModel to be insert into the models table.
 * This method was implemented because some databases do not support auto-increment.
 *
 * @return  integer
 * @access	private
 */
 function _createUniqueModelID() {

   $maxModelID =& $this->dbConn->GetOne('SELECT MAX(modelID) FROM models');
   return ++$maxModelID;
 }

 /**
 * Create a unique ID for the dataset to be insert into the datasets table.
 * This method was implemented because some databases do not support auto-increment.
 *
 * @return  integer
 * @access	private
 */
 function _createUniqueDatasetID() {

   $maxDatasetID =& $this->dbConn->GetOne('SELECT MAX(datasetId) FROM datasets');
   return ++$maxDatasetID;
 }


/**
 * Create tables and indexes for MsAccess database
 *
 * @throws  SqlError
 * @access	private
 */
 function _createTables_MsAccess() {

   $this->dbConn->startTrans();

   $this->dbConn->execute('CREATE TABLE models
                           (modelID long primary key,
                            modelURI varchar not null,
                            baseURI varchar)');

   $this->dbConn->execute('CREATE UNIQUE INDEX m_modURI_idx ON models (modelURI)');

   $this->dbConn->execute('CREATE TABLE statements
                           (modelID long,
                            subject varchar,
                            predicate varchar,
                            object Memo,
                            l_language varchar,
                            l_datatype varchar,
                            subject_is varchar(1),
                            object_is varchar(1),
                            primary key (modelID, subject, predicate, object,
                                         l_language, l_datatype))');

   $this->dbConn->execute('CREATE INDEX s_mod_idx ON statements (modelID)');
   $this->dbConn->execute('CREATE INDEX s_sub_idx ON statements (subject)');
   $this->dbConn->execute('CREATE INDEX s_pred_idx ON statements (predicate)');
   $this->dbConn->execute('CREATE INDEX s_obj_idx ON statements (object)');

     $this->dbConn->execute('CREATE TABLE namespaces
                           (modelID long,
                            namespace varchar,
                            prefix varchar,
                            primary key (modelID, namespace, prefix))');

   $this->dbConn->execute('CREATE INDEX n_name_idx ON namespaces (namespace)');
   $this->dbConn->execute('CREATE INDEX n_pref_idx ON namespaces (prefix)');

     $this->dbConn->execute("CREATE TABLE datasets
                           (datasetName varchar,
  							defaultModelUri varchar,
							primary key (datasetName))");

   $this->dbConn->execute('CREATE INDEX nGS_idx1 ON datasets (datasetName)');


   $this->dbConn->execute("CREATE TABLE `dataset_model` (
  							datasetName varchar,
  							modelId long,
							graphURI varchar,
							PRIMARY KEY  (modelId,datasetName))");


   if (!$this->dbConn->completeTrans())
      echo $this->dbConn->errorMsg();
 }


/**
 * Create tables and indexes for MySQL database
 *
 * @throws  SqlError
 * @access	private
 */
 function _createTables_MySql() {

   $this->dbConn->startTrans();

   $this->dbConn->execute("CREATE TABLE models
                           (modelID bigint NOT NULL,
                            modelURI varchar(255) NOT NULL,
                            baseURI varchar(255) DEFAULT '',
                            primary key (modelID))");

   $this->dbConn->execute('CREATE UNIQUE INDEX m_modURI_idx ON models (modelURI)');

   $this->dbConn->execute("CREATE TABLE statements
                           (modelID bigint NOT NULL,
                            subject varchar(255) NOT NULL,
                            predicate varchar(255) NOT NULL,
                            object text,
                            l_language varchar(255) DEFAULT '',
                            l_datatype varchar(255) DEFAULT '',
                            subject_is varchar(1) NOT NULL,
                            object_is varchar(1) NOT NULL)");

   $this->dbConn->execute("CREATE TABLE namespaces
                           (modelID bigint NOT NULL,
                            namespace varchar(255) NOT NULL,
                            prefix varchar(255) NOT NULL,
   							primary key (modelID,namespace))");

  $this->dbConn->execute("CREATE TABLE `dataset_model` (
  							`datasetName` varchar(255) NOT NULL default '0',
  							`modelId` bigint(20) NOT NULL default '0',
							`graphURI` varchar(255) NOT NULL default '',
							 PRIMARY KEY  (`modelId`,`datasetName`))");

  $this->dbConn->execute("CREATE TABLE `datasets` (
  							`datasetName` varchar(255) NOT NULL default '',
						    `defaultModelUri` varchar(255) NOT NULL default '0',
						     PRIMARY KEY  (`datasetName`),
						     KEY `datasetName` (`datasetName`))");

   $this->dbConn->execute('CREATE INDEX s_mod_idx ON statements (modelID)');
   $this->dbConn->execute('CREATE INDEX n_mod_idx ON namespaces (modelID)');

   $this->dbConn->execute('CREATE INDEX s_sub_pred_idx ON statements
                          (subject(200),predicate(200))');

   $this->dbConn->execute('CREATE INDEX s_sub_idx ON statements (subject(200))');
   $this->dbConn->execute('CREATE INDEX s_pred_idx ON statements (predicate(200))');
   $this->dbConn->execute('CREATE INDEX s_obj_idx ON statements (object(250))');

   $this->dbConn->execute('CREATE FULLTEXT INDEX s_obj_ftidx ON statements (object)');

   if (!$this->dbConn->completeTrans())
      echo $this->dbConn->errorMsg();
 }

 /**
 * Create tables and indexes for MSSQL database
 *
 * @throws  SqlError
 * @access	private
 */
 function _createTables_mssql(){

   $this->dbConn->startTrans();

   $this->dbConn->execute("CREATE TABLE [dbo].[models] (
							[modelID] [int] NOT NULL ,
							[modelURI] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
							[baseURI] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
							) ON [PRIMARY]");

   $this->dbConn->execute("CREATE TABLE [dbo].[statements] (
							[modelID] [int] NOT NULL ,
							[subject] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
							[predicate] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
							[object] [text] COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
							[l_language] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
							[l_datatype] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
							[subject_is] [nchar] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
							[object_is] [nchar] (1) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
							) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]");


    $this->dbConn->execute("CREATE TABLE [dbo].[namespaces] (
							[modelID] [int] NOT NULL ,
							[namespace] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL ,
							[prefix] [nvarchar] (200) COLLATE SQL_Latin1_General_CP1_CI_AS NULL ,
							) ON [PRIMARY]");

   $this->dbConn->execute("ALTER TABLE [dbo].[models] WITH NOCHECK ADD
							CONSTRAINT [PK_models] PRIMARY KEY  CLUSTERED
							(
								[modelID]
							)  ON [PRIMARY] ");
   $this->dbConn->execute("ALTER TABLE [dbo].[namespaces] WITH NOCHECK ADD
							CONSTRAINT [PK_namespaces] PRIMARY KEY  CLUSTERED
							(
								[modelID],[namespace]
							)  ON [PRIMARY] ");

   $this->dbConn->execute("CREATE  INDEX [joint index on subject and predicate] ON [dbo].[statements]([subject], [predicate]) ON [PRIMARY]");


   if (!$this->dbConn->completeTrans())
   	echo $this->dbConn->errorMsg();

 }


  /**
  * Checks if tables are setup for RAP
  *
  * @param   string  $databaseType
  * @throws SqlError
  * @access public
  **/
 function isSetup($databaseType="MySQL") {
   if ($databaseType=="MySQL")
     return $this->_isSetup_MySql();
    if ($databaseType=="MSSQL")
     return $this->_isSetup_MSSQL();
   else {
   	if ($databaseType=='MsAccess'){
   		return $this->_isSetup_MsAccess();
   	}else{
     $errmsg=RDFAPI_ERROR."(class: DbStore; method isSetup('$databaseType')):\nCurrently only MySQL, MsAccess and MSSQL are supported!";
     trigger_error($errmsg, E_USER_ERROR);}
   }
 }

 /**
  * Checks if tables are setup for RAP (MySql)
  *
  * @throws SqlError
  * @access private
  **/
 function _isSetup_MySql() {
   $recordSet =& $this->dbConn->execute("SHOW TABLES");
   if (!$recordSet)
     echo $this->dbConn->errorMsg();
   else {
     $tables = array();
     while (!$recordSet->EOF) {

       $tables[]= $recordSet->fields[0];

       if(isset($i)){++$i;}
       $recordSet->moveNext();
     }
     if (in_array("models",$tables) && in_array("statements",$tables)&& in_array("namespaces",$tables)) return true;
   }
   return false;

 }


  /**
  * Checks if tables are setup for RAP (MsAccess)
  *
  * @throws SqlError
  * @access private
  **/
 function _isSetup_MsAccess() {
   	$tables =& $this->dbConn->MetaTables();
   	if (!$tables)
     echo $this->dbConn->errorMsg();
   if (count($tables)==0){
     return false;}
   else {
   		if (in_array("models",$tables) && in_array("statements",$tables) && in_array("namespaces",$tables)){ return true;
   		}else{return false;}
   }
 }

  /**
  * Checks if tables are setup for RAP (MSSQL)
  *
  * @throws SqlError
  * @access private
  **/
 function _isSetup_MSSQL() {
   	$tables =& $this->dbConn->MetaTables();
   	if (!$tables)
     echo $this->dbConn->errorMsg();
   if (count($tables)==0){
     return false;}
   else {
   		if (in_array("models",$tables) && in_array("statements",$tables) && in_array("namespaces",$tables)){ return true;
   		}else{return false;}
   }
 }


 /**
 * Create a new instance of DatasetDb with the given $datasetName
 * and insert the DatasetDb variables into the database.
 * Return FALSE if there is already a model with the given URI.
 *
 * @param   $datasetName string
 * @return  object DatasetDB
 * @throws  SqlError
 * @access	public
 */
 function & getNewDatasetDb($datasetName)
 {

 	require_once(RDFAPI_INCLUDE_DIR . PACKAGE_DATASET);

   if ($this->datasetExists($datasetName))
      return FALSE;
   else
   {
   		$defaultModelUri=uniqid('http://rdfapi-php/dataset_defaultmodel_');
   		$defaultModel=$this->getNewModel($defaultModelUri);

      	$rs =& $this->dbConn->execute("INSERT INTO datasets
                                            VALUES ('" .$datasetName ."',
                                                    '" .$defaultModelUri."')");

      if (!$rs)
         $this->dbConn->errorMsg();
      else
		$return=new DatasetDb($this->dbConn, $this, $datasetName);
   		return ($return);
   }
 }

 /**
 * Check if the Dataset with the given $datasetName is already stored in the database
 *
 * @param   $datasetName string
 * @return  boolean
 * @throws	SqlError
 * @access	public
 */
function datasetExists($datasetName) {

   $res =& $this->dbConn->execute("SELECT COUNT(*) FROM datasets
                                   WHERE datasetName = '" .$datasetName ."'");
   if (!$res)
      echo $this->dbConn->errorMsg();
   else {
      if (!$res->fields[0])
         return FALSE;
      return TRUE;
   }
 }


 /**
 * Create a new instance of DatasetDb with the given $datasetName and
 * load the corresponding values from the database.
 * Return FALSE if the DbModel does not exist.
 *
 * @param   $datasetId string
 * @return  object DatasetDb
 * @access	public
 */
 function &getDatasetDb($datasetName) {
    require_once(RDFAPI_INCLUDE_DIR . PACKAGE_DATASET);

    if (!$this->datasetExists($datasetName)) {
        return FALSE;
    } else {
        $return = new DatasetDb($this->dbConn, $this, $datasetName);
        return ($return);
    }
 }

 /**
 * Create a new instance of namedGraphDb with the given $modelURI and graphName and
 * load the corresponding values of modelID and baseURI from the database.
 * Return FALSE if the DbModel does not exist.
 *
 * @param   $modelURI string
 * @param   $graphName string
 * @return  object NamedGraphMem
 * @access	public
 */
 function getNamedGraphDb($modelURI, $graphName)
 {
	require_once(RDFAPI_INCLUDE_DIR . PACKAGE_DATASET);

   if (!$this->modelExists($modelURI))
      return FALSE;
   else {
      $modelVars =& $this->dbConn->execute("SELECT modelURI, modelID, baseURI
                                            FROM models
                                            WHERE modelURI='" .$modelURI ."'");

      return new NamedGraphDb($this->dbConn, $modelVars->fields[0],
                         $modelVars->fields[1], $graphName ,$modelVars->fields[2]);
   }
 }

 /**
 * Create a new instance of namedGraphDb with the given $modelURI and graphName
 * and insert the DbModel variables into the database (not the graphName. This
 * is only stored persistently, when added to dataset).
 * Return FALSE if there is already a model with the given URI.
 *
 * @param   $modelURI string
 * @param  	$graphName string
 * @param   $baseURI string
 * @return  object namedGraphDb
 * @throws  SqlError
 * @access	public
 */
 function getNewNamedGraphDb($modelURI, $graphName, $baseURI=NULL) {

   if ($this->modelExists($modelURI))
      return FALSE;
   else {
      $modelID = $this->_createUniqueModelID();

      $rs =& $this->dbConn->execute("INSERT INTO models
                                            (modelID, modelURI, baseURI)
                                            VALUES ('" .$modelID ."',
                                                    '" .$modelURI ."',
                                                    '" .$baseURI ."')");
      if (!$rs)
         $this->dbConn->errorMsg();
      else
         return new NamedGraphDb($this->dbConn, $modelURI, $modelID, $graphName, $baseURI);
   }
 }

 /**
 * Removes the graph with all statements from the database.
 * Warning: A single namedGraph can be added to several datasets. So it'll be
 * removed from all datasets.
 *
 * @param   $modelURI string
 * @return  boolean
 * @throws  SqlError
 * @access	public
 */
 function removeNamedGraphDb($modelURI)
 {
	if (!$this->modelExists($modelURI))
		return FALSE;

	$modelID = $this->dbConn->GetOne("SELECT modelID FROM models WHERE modelURI='".$modelURI."'");

	$this->dbConn->execute("DELETE FROM models WHERE modelID=".$modelID);
	$this->dbConn->execute("DELETE FROM dataset_model WHERE modelId=".$modelID);
	$this->dbConn->execute("DELETE FROM statements WHERE modelID=".$modelID);

	return true;
 }

 

    /**
    * Performs a SPARQL query against a model. The model is converted to
    * an RDF Dataset. The result can be retrived in SPARQL Query Results XML Format or
    * as an array containing the variables an their bindings.
    *
    * @param  string $query       Sparql query string
    * @param mixed $arModelIds    Array of modelIDs, or NULL to use all models
    * @param  string $resultform  Result form ('xml' for SPARQL Query Results XML Format)
    * @return string/array
    */
    function sparqlQuery($query, $arModelIds = null, $resultform = false)
    {
        $engine = $this->_prepareSparql($arModelIds);
        return $engine->queryModel(
            null,
            $this->_parseSparqlQuery($query),
            $resultform
        );
    }//function sparqlQuery($query,$resultform = false)



    /**
    *   Prepares everything for SparqlEngine-usage
    *   Loads the files, creates instances for SparqlEngine and
    *   Dataset...
    *
    *   @return SparqlEngineDb
    */
    function _prepareSparql($arModelIds)
    {
        require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb.php';
        return new SparqlEngineDb($this, $arModelIds);
    }//function _prepareSparql()



    /**
    *   Parses an query and returns the parsed form.
    *   If the query is not a string but a Query object,
    *   it will just be returned.
    *
    *   @param $query mixed String or Query object
    *   @return Query query object
    *   @throws Exception If $query is no string and no Query object
    */
    function _parseSparqlQuery($query)
    {
        if ($this->queryParser === null) {
            require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlParser.php';
            $this->queryParser = new SparqlParser();
        }
        return $this->queryParser->parse($query);
    }//function _parseSparqlQuery($query)

} // end: Class DbStore
?>