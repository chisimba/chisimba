<?php

/**
 * MongoDB Helper Class
 *
 * Convenience class for interacting with MongoDB
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   mongo
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: mongoops_class_inc.php 19535 2010-10-28 18:22:39Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://www.mongodb.org/
 */

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
 * MongoDB Helper Class
 *
 * Convenience class for interacting with MongoDB.
 *
 * @category  Chisimba
 * @package   mongo
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: mongoops_class_inc.php 19535 2010-10-28 18:22:39Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://www.mongodb.org/
 */
class mongoops extends object
{
    /**
     * The name of the collection to default to.
     *
     * @access private
     * @var    string
     */
    private $collection;

    /**
     * Cache of MongoCollection objects.
     *
     * @access private
     * @var    array
     */
    private $collectionCache;

    /**
     * The name of the database to default to.
     *
     * @access private
     * @var    string
     */
    private $database;

    /**
     * Cache of MongoDB objects.
     *
     * @access private
     * @var    array
     */
    private $databaseCache;

    /**
     * Instance of the Mongo class.
     *
     * @access private
     * @var    object
     */
    private $objMongo;

    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objSysConfig;

    /**
     * Retrieves the MongoCollection object according to the specified collection and database names.
     *
     * @access private
     * @param  string $collection The name of the collection.
     * @param  string $database   The name of the database.
     * @return object The corresponding instance of the MongoCollection class.
     */
    private function getCollection($collection=NULL, $database=NULL)
    {
        // Use the default if the collection name has not been specified.
        if ($collection === NULL) {
            $collection = $this->collection;
        }

        // Use the default if the database name has not been specified.
        if ($database === NULL) {
            $database = $this->database;
        }

        // Retrieve the MongoDB object.
        $objDatabase = $this->getDatabase($database);

        // Retrieve the MongoCollection object from cache or create it.
        if (array_key_exists($collection, $this->collectionCache[$database])) {
            $objCollection = $this->collectionCache[$database][$collection];
        } else {
            $objCollection = $objDatabase->$collection;
            $this->collectionCache[$database][$collection] = $objCollection;
        }

        return $objCollection;
    }

    /**
     * Returns the MongoDB instance associated with the given database name.
     *
     * @access public
     * @param  string $database The database name. If not specified, default is assumed.
     * @return object Corresponding instance of MongoDB.
     */
    private function getDatabase($database=NULL)
    {
        // Use the default if the database name has not been specified.
        if ($database === NULL) {
            $database = $this->database;
        }

        // Retrieve the MongoDB object from cache or create it.
        if (array_key_exists($database, $this->databaseCache)) {
            $objDatabase = $this->databaseCache[$database];
        } else {
            $objDatabase = $this->objMongo->$database;
            $this->databaseCache[$database] = $objDatabase;
            $this->collectionCache[$database] = array();
        }

        return $objDatabase;
    }

    /**
     * Deletes a record from the collection.
     *
     * @access public
     * @param  array   $criteria   The criteria of the records to delete.
     * @param  boolean $justOne    Delete just one record.
     * @param  boolean $safe       Blocks until update has been applied.
     * @param  boolean $fsync      Write the update to disk immediately.
     * @param  string  $collection The name of the collection to run the query on.
     * @param  string  $database   The name of the database containing the collection.
     * @return mixed   Boolean if asynchronous, array if synchronous.
     */
    public function delete(array $criteria, $justOne=FALSE, $safe=FALSE, $fsync=FALSE, $collection=NULL, $database=NULL)
    {
        $options = array('justOne' => $justOne, 'safe' => $safe, 'fsync' => $fsync);

        return $this->getCollection($collection, $database)->remove($criteria, $options);
    }

    /**
     * Runs a query on a collection.
     *
     * @access public
     * @param  array  $query      The query to run.
     * @param  array  $fields     The fields to return.
     * @param  string $collection The name of the collection to run the query on.
     * @param  string $database   The name of the database containing the collection.
     * @return object Instance of the MongoCursor class.
     */
    public function find(array $query=array(), array $fields=array(), $collection=NULL, $database=NULL)
    {
        return $this->getCollection($collection, $database)->find($query, $fields);
    }

    /**
     * Returns a list of the names of the collections inside a database.
     *
     * @access public
     * @param  string $database The name of the database to use.
     * @return array  The names of the collections.
     */
    public function getCollectionNames($database=NULL)
    {
        $collections = $this->getDatabase($database)->listCollections();
        $names = array();

        foreach($collections as $collection) {
            $names[] = (string) $collection;
        }

        return $names;
    }

    /*
     * Initialises some of the object's properties.
     *
     * @access public
     */
    public function init()
    {
        // Objects
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objMongo     = new Mongo($this->objSysConfig->getValue('server', 'mongo'));

        // Arrays
        $this->collectionCache = array();
        $this->databaseCache   = array();

        // Strings
        $this->database = $this->objSysConfig->getValue('database', 'mongo');
    }

    /**
     * Imports a CSV file into a collection.
     *
     * @access public
     * @param  string  $file       The location of the file.
     * @param  string  $collection The collection to use.
     * @param  string  $database   The database containing the collection.
     * @return boolean The results of the import.
     */
    public function importCSV($file, $collection=NULL, $database=NULL)
    {
        $handle = fopen($file, 'r');
        $keys = array_map('strtolower', fgetcsv($handle));
        $success = TRUE;

        while (($record = fgetcsv($handle)) !== FALSE) {
            $data = array_combine($keys, $record);
            $success = $this->insert($data, $collection, $database) && $success;
        }

        fclose($handle);

        return $success;
    }

    /**
     * Inserts data into the collection.
     *
     * @access public
     * @param  array   $data       The data to insert.
     * @param  string  $collection The collection to insert the data into.
     * @param  string  $database   The database containing the collection.
     * @return boolean The results of the insert.
     */
    public function insert(array $data, $collection=NULL, $database=NULL)
    {
        return $this->getCollection($collection, $database)->insert($data);
    }

    /**
     * Sets the name of the default collection.
     *
     * @access public
     * @param  string $collection The name of the default collection.
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * Sets the name of the default database.
     *
     * @access public
     * @param  string $database The name of the default database.
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }
}

?>
