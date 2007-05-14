<?php
// ----------------------------------------------------------------------------------
// Class: RDF_Store_MDB
// ----------------------------------------------------------------------------------
/**
 * Store_MDB is a persistent store of RDF data using relational database technology.
 * Store_MDB uses the MDB Library for PHP (http://pear.php.net/MDB),
 * which allows to connect to multiple databases in a portable manner.
 *
 * @version V0.7
 * @author Radoslaw Oldakowski <radol@gmx.de>
 * @package model
 * @access public
 */

class RDF_Store_MDB extends RDF_Object
{
    /**
     * Database connection object
     *
     * @var object ADOConnection
     * @access private
     */
    var $dbConn;

    var $database;

    /**
     * Set the database connection with the given parameters.
     *
     * @param string $dsn
     * @param string $options
     * @access public
     */
    function RDF_Store_MDB($dsn, $options = null)
    {
        require_once 'MDB.php';
        // create a new connection object
        $this->dbConn =& MDB::connect($dsn, $options);
    }

    /**
     * Create tables and indexes for the given database type.
     *
     * @throws PhpError
     * @access public
     */
    function createTables()
    {
        MDB::loadFile('Manager');
        $manager =& new MDB_Manager;
        $err = $manager->connect($this->dbConn);
        if(MDB::isError($err)) {
            return $err;
        }
        $filename = dirname(__FILE__).'/database_schema.xml';
        $err = $manager->updateDatabase($filename, $filename.'.old', array('database' => $this->dbConn->database_name));
        if(MDB::isError($err)) {
            return $err;
        }
        $dsn = $this->dbConn->getDSN();
        if (isset($dsn['phptype']) && $dsn['phptype'] == 'mysql') {
            $this->dbConn->query('CREATE INDEX s_mod_idx ON statements (modelID)');
            $sql = 'CREATE INDEX s_sub_pred_idx ON statements (subject(200),predicate(200))';
            $this->dbConn->query($sql);
            $this->dbConn->query('CREATE INDEX s_obj_idx ON statements (object(250))');
        }
        return true;
    }

    /**
     * List all Model_MDBs stored in the database.
     *
     * @return array
     * @throws SqlError
     * @access public
     */
    function listModels()
    {
        $sql = 'SELECT modelURI, baseURI FROM models';
        $result = $this->dbConn->queryAll($sql);
        if (MDB::isError($result)) {
            return $result;
        }
        for ($i=0,$j=count($result);$i<$j;++$i) {
            $models[$i]['modelURI'] = $result[$i][0];
            $models[$i]['baseURI'] = $result[$i][1];
        }
        return $models;
    }

    /**
     * Check if the Model_MDB with the given modelURI is already stored in the database
     *
     * @param string $modelURI
     * @return boolean
     * @throws SqlError
     * @access public
     */
    function modelExists($modelURI)
    {
        $sql = 'SELECT COUNT(*)
            FROM models
            WHERE modelURI = ' . $this->dbConn->getValue('text', $modelURI);
        $result = $this->dbConn->queryOne($sql);
        if (MDB::isError($result)) {
            return $result;
        }
        return (bool)$result;
    }

    /**
     * Create a new instance of Model_MDB with the given $modelURI and
     * load the corresponding values of modelID and baseURI from the database.
     * Return FALSE if the Model_MDB does not exist.
     *
     * @param string $modelURI
     * @return object Model_MDB
     * @access public
     */
    function getModel($modelURI)
    {
        if (!$this->modelExists($modelURI)) {
            return false;
        } else {
            $sql = 'SELECT modelURI, modelID, baseURI FROM models
                WHERE modelURI=' . $this->dbConn->getValue('text', $modelURI);
            $modelVars = $this->dbConn->queryRow($sql);

            return new RDF_Model_MDB($this->dbConn, $modelVars[0],
                $modelVars[1], $modelVars[2]);
        }
    }

    /**
     * Create a new instance of Model_MDB with the given $modelURI
     * and insert the Model_MDB variables into the database.
     * Return FALSE if there is already a model with the given URI.
     *
     * @param string $modelURI
     * @param string $baseURI
     * @return object Model_MDB
     * @throws SqlError
     * @access public
     */
    function getNewModel($modelURI, $baseURI = null)
    {
        if ($this->modelExists($modelURI)) {
            return false;
        }

        $this->dbConn->autoCommit(false);

        $modelID = $this->_createUniqueModelID();

        $sql = 'INSERT INTO models VALUES (' .
            $this->dbConn->getValue('text', $modelID) .',' .
            $this->dbConn->getValue('text', $modelURI) .',' .
            $this->dbConn->getValue('text', $baseURI) .')';
        $result = &$this->dbConn->query($sql);

        $this->dbConn->autoCommit(true);

        if (MDB::isError($result)) {
            return $result;
        }

        return new RDF_Model_MDB($this->dbConn, $modelURI, $modelID, $baseURI);
    }

    /**
     * Store a Model_Memory or another Model_MDB from a different Store_MDB in the database.
     * Return FALSE if there is already a model with modelURI matching the modelURI
     * of the given model.
     *
     * @param object Model  &$model
     * @param string $modelURI
     * @return boolean
     * @access public
     */
    function putModel(&$model, $modelURI = null)
    {
        if (!$modelURI) {
            if (is_a($model, 'RDF_Model_Memory')) {
                $modelURI = 'Model_MDB-' . $this->_createUniqueModelID();
            } else {
                $modelURI = $model->modelURI;
            }
        } else {
            if ($this->modelExists($modelURI)) {
                return false;
            }
        }

        $newmodel = $this->getNewModel($modelURI, $model->getBaseURI());
        $newmodel->addModel($model);
    }

    /**
     * Close the Store_MDB.
     * !!! Warning: If you close the Store_MDB all active instances of Model_MDB from this
     * !!!          Store_MDB will lose their database connection !!!
     *
     * @access public
     */
    function close()
    {
        $this->dbConn->disconnect();
        $this = null;
    }
    // =============================================================================
    // **************************** private methods ********************************
    // =============================================================================
    /**
     * Create a unique ID for the Model_MDB to be insert into the models table.
     * This method was implemented because some databases do not support auto-increment.
     *
     * @return integer
     * @access private
     */
    function _createUniqueModelID()
    {
        $sql = 'SELECT MAX(modelID) FROM models';
        $maxModelID = $this->dbConn->queryOne($sql);
        ++$maxModelID;
        return $maxModelID;
    }
} // end: Class Store_MDB
?>