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
 * @author    Paul Scott <pscott209@gmail.com>
 * @copyright 2011 Paul Scott
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
 * @author    Paul Scott <pscott209@gmail.com>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: mongoops_class_inc.php 19535 2010-10-28 18:22:39Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://www.mongodb.org/
 */
class geomongo extends object
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
    private $databaseCache = array();

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

    /*
     * Initialises some of the object's properties.
     *
     * @access public
     */
    public function init()
    {
        // Objects
        $this->objSysConfig    = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objMongo        = new Mongo($this->objSysConfig->getValue('server', 'mongo'));
        $database              = $this->objSysConfig->getValue('database', 'mongo');
        $this->dbname          = $database;
        $this->db              = $this->objMongo->$database;
        $this->collection      = new MongoCollection($this->db, $this->objSysConfig->getValue('collection', 'mongo'));
        $this->objProxy        = $this->getObject('proxyparser', 'utilities');
        
    }
    
    public function getByLonLat($lon, $lat, $limit = 10) {
        $lonlat = array($lon, $lat);
        $cursor = $this->collection->find(Array('loc' => Array('$nearSphere' => $lonlat)))->limit($limit);
        $resultset = $this->jsonCursor($cursor);
        return json_encode($resultset);
    }
    
    public function getByPlacename($placename, $limit = NULL) {
        $cursor = $this->collection->find(Array('name' => array('$regex' => $placename)))->limit($limit);
        $resultset = $this->jsonCursor($cursor);
        return json_encode($resultset);
    }
    
    public function getAllByCountryCode($cc, $limit = NULL) {
        $cursor = $this->collection->find(Array('countrycode' => array('$regex' => $cc)))->limit($limit);
        $resultset = $this->jsonCursor($cursor);
        return json_encode($resultset);
    }
    
    public function getRadiusMiles($lon, $lat, $radius = 3) {
        $radiusOfEarth = 3956; //avg radius of earth in miles
        $cursor = $this->collection->find(
            array('loc' =>
                array('$within' =>
                    array('$centerSphere' =>
                        array(
                            array(floatval($lon), floatval($lat)), $radius/$radiusOfEarth
                        )
                    )
                )
            )
        );
        $resultset = $this->jsonCursor($cursor);
        return json_encode($resultset);
    }
    
    public function getRadiusKm($lon, $lat, $radius = 5) {
        $radiusOfEarth = 6378.1; //avg radius of earth in km
        $cursor = $this->collection->find(
            array('loc' =>
                array('$within' =>
                    array('$centerSphere' =>
                        array(
                            array(floatval($lon), floatval($lat)), $radius/$radiusOfEarth
                        )
                    )
                )
            )
        );
        
        $resultset = $this->jsonCursor($cursor);
        return json_encode($resultset);
    }
    
    public function mongoWikipedia($objWikipedia) {
        $wikipedia = new stdClass();
        $result = 0;
        foreach($objWikipedia->articles as $art) {
            $id = $result;
            $lon = $art->lng;
            $lat = $art->lat;
            $type = $art->type;
            $title = $art->title;
            $url = $art->url;
            $distance = $art->distance;
            $data = array('id' => $id, 'lon' => $lon, 'lat' => $lat, 'type' => $type, 'title' => $title, 'url' => $url, 'distance' => $distance);
            $wikipedia->$id = $data;
            $result++;
        }
        
        return json_encode($wikipedia);
    }
    
    public function mongoFlickr($objFlickr) {
        var_dump($objFlickr);
    }
    
    public function getElevation($latitude, $longitude) {
        // get the elevation
    	$url = "http://maps.googleapis.com/maps/api/elevation/json?locations=$latitude,$longitude&sensor=false";
    	$proxyArr = $this->objProxy->getProxy();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
			curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'] . ":" . $proxyArr['proxy_port']);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'] . ":" . $proxyArr['proxy_pass']);
		}
		$elejson = curl_exec($ch);
		$ele = json_decode($elejson);
		if($ele->status == "OK") {
			$res = $ele->results;
			$res = $res[0];
			$elevation = $res->elevation;
			$resolution = $res->resolution;
		}
		else {
			$elevation = 0;
			$resolution = 0;
		}
		return array("elevation" => $elevation, "resolution" => $resolution);
    }
    
    public function upsertRecord($insertarr, $mode = "forceupdate") {
    	$this->objOps          = $this->getObject('geoops', 'geo');
    	if($mode == "forceupdate") {
    	    $ele = $this->getElevation($insertarr['latitude'], $insertarr['longitude']);
		    $elevation = $ele['elevation'];
		    // get wikipedia info url(s)
		    $wiki = $this->objOps->getWikipedia($insertarr['longitude'], $insertarr['latitude'], $radius=50);
		    $wiki = $wiki->articles;
		    if(!empty($wiki)) {
		        if(ucwords($insertarr['name']) == ucwords($wiki[0]->title)) {
			        $wikipedia = $wiki[0]->url;
		        }
		    }
		    else {
			    $wikipedia = "";
		    }
    	}
    	else {
    		$wikipedia = "";
    		$elevation = 0;
    	}
    	$cursor = $this->collection->update(array("name" => ucwords($insertarr['name'])), 
    	                                          array("loc" => array($insertarr['longitude'], $insertarr['latitude']), 
    	                                          "name" => array(ucwords($insertarr['name'])), 
    	                                          "longitude" => array(floatval($insertarr['longitude'])),
    	                                          "latitude" => array(floatval($insertarr['latitude'])),
    	                                          "type" => array($insertarr['type']),
    	                                          "wikipedia" => array($wikipedia),
    	                                          "elevation" => array(floatval($elevation)),
    	                                          //"countrycode" => array($insertarr['countrycode']),
    	                                          //"timezone" => array($insertarr['timezone']),
    	                                          "alternatenames" => array(ucwords($insertarr['alternatenames'])), 
    	                                          "asciiname" => array(ucwords($insertarr['name'])),
    	                                          // "population" => array(floatval($insertarr['population'])),
    	                                          ), array("upsert" => true));
        return $cursor; // boolean!
    }
    
    public function getRecordCount() {
    	$cursor = $this->collection->count();
    	return $cursor;
    }
    
    public function getDistinct($key) {
    	
    	$cursor = $this->db->command(array("distinct" => $this->objSysConfig->getValue('collection', 'mongo'), "key" => $key));
    	return $cursor;
    }
    
    public function isWithinPolygon($lon, $lat, $polygon, $limit = 10) {
        $cursor = $this->collection->find(
            array('loc' =>
                array('$within' =>
                    array('$box' => $polygon
                        //array(
                        //    array(floatval($lon), floatval($lat))
                        //)
                    )
                )
            )
        );
        $resultset = $this->jsonCursor($cursor);
        return json_encode($resultset);
    }
    
    private function jsonCursor($cursor) {
        $ret = new StdClass();
        $resultno = 1;
        foreach ($cursor as $obj) {
            $ret->$resultno = array('location' => $obj['loc'], 
                                    'elevation' => $obj['elevation'][0], 
                                    'name' => $obj['name'][0],
                                    'type' => $obj['type'][0],
                                    'wikipedia' => $obj['wikipedia'][0], 
                                    //'countrycode' => $obj['countrycode'][0], 
                                    'longitude' => $obj['longitude'][0], 
                                    'latitude' => $obj['latitude'][0],
                                    //'timezone' => $obj['timezone'][0], 
                                    'alternatenames' => $obj['alternatenames'][0], 
                                    //'asciiname' => $obj['asciiname'][0],
                                    //'population' => $obj['population'][0]
            ); 
            $resultno++;
        }
        
        return $ret;
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
        $this->dbname = $database;
    }
    
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
            $database = $this->dbname;
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
            $database = $this->dbname;
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
}

?>
