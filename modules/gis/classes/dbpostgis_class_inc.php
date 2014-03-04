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
 * Data access (db model) Class for postgis in the gis module
 *
 * This is a database model class for the gis module. All database transactions will go through
 * this class. This class is derived from the top level dbTable superclass in the framework core.
 *
 * @author     Paul Scott
 * @filesource
 * @copyright  AVOIR
 * @package    blog
 * @category   chisimba
 * @access     public
 */

class dbpostgis extends dbTable
{
	/**
	 * @var $shp2pgsql path to shp2pgsql binary
	 */
	private $shp2pgsql_path = "/usr/bin/shp2pgsql";
	/**
	 * @var $pgsql_path path to psql
	 */
	private $psql_path = "/usr/bin/psql -d ";
	/**
	 * @var $database the current database
	 */
	public $database = NULL;
	/**
	 * @var $schema the database schema unused so far...
	 */
	public $schema = NULL;

	public $sysConfig;

	public function init()
	{
		// create the connection so as to suppress any issues as well as set the class up
		parent::init('tbl_users');
		// get the configuration value for the postgis db that we want to use
		$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
		$this->database = $this->sysConfig->getValue('postgis_db', 'gis');
		// default to something, or show an error...
		if(!isset($this->database))
		{
			$this->database = 'wms';
		}
		// actually switch the dbTable class to the postGIS db, and reinit the darn thing
		$this->setDatabaseTo($this->database);
		// the language object
		$this->objLanguage = $this->getObject("language", "language");
	}

	/**
	 * Method to get information about the postgis db
	 * 
	 * @author Jean David Techer
	 * @author Paul Scott
	 */
	public function isPostGIS()
	{
		$query = "SELECT COUNT(*)>0 AS present FROM pg_proc
        		 WHERE proname='postgis_full_version'::text";

		$rec = $this->getArray($query);
		if($rec != 0)
		{
			return $rec;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Method to create sql statements from shapefiles (ESRI)
	 * and then insert the data into PostGIS DB
	 * 
	 * @author Paul Scott
	 * @param string $filename
	 * @param string $dbname
	 * @return bool true on success
	 * @access private
	 */
	private function _create_sql($filename, $debug = TRUE, $log = FALSE)
	{
		//create the tablename from the filename (replace the .shp with shp)
		$tablename1 = str_replace('.','',$filename);
		//Remove last three chars from table name ('shp' is discarded)
		$tablename = substr($tablename1,0,-3);
		//Give the log file a name by tacking .log onto the table name
		$logfile = $tablename.".log";
		//set up the command and database
		$command = $this->shp2pgsql_path .  " " . $filename . " " .$tablename ." | ".
		$this->psql_path . " " . $this->database . " 2>&1";

		//execute the shell script that does all the real work (thanks refractions.net)
		$ret = shell_exec($command);

		//Is debugging turned on?
		if($debug == TRUE)
		{
			//echo "Debug Message: " . $ret;
			if($ret == "Object: command not found")
			{
				$debugmsg = $this->objLanguage->languageText("mod_gis_installpostgis", "gis"); //"shp2pgsql not found: Please install PostGIS from <a href=http://postgis.refractions.net>Refractions Research</a>";
			}
		}

		//log the creation of the sql to a file. This defaults to false
		if($log == TRUE)
		{
			//open a file handle for binary safe write -
			//this creates a logfile, just an extra
			if($handle= @fopen($logfile,'w'))
			{
				//Write the log data to a file
				fwrite($handle, $ret);
				//close the file handle
				fclose($handle);
			}
			else {
				$debugmsg = $this->objLanguage->languageText("mod_gis_logfilepermdenied", "gis"); //"Cannot open logfile - Permission Denied";
			}
		}
		if(!empty($debugmsg))
		{
			return $debugmsg;
		}
		else {
			return TRUE;
		}
	}

	/**
	 * Method to search for shapefiles in a directory and then insert them into postgis
	 * @param string $path
	 * @return true on success or error msg on failure
	 * @access public
	 */
	public function shp2pgsql($path)
	{
		if(!file_exists($path))
		{
			return FALSE;
		}
		else {
			chdir($path);
			$fileArray = glob("*.shp");
			if(empty($fileArray))
			{
				return FALSE;
			}
			else {
				//loop through all the shapefiles and perform the function on them
				foreach ($fileArray as $filename)
				{
					//do the sql-ize and insert the data, also write a log
					$creation = $this->_create_sql($filename);
				}//end foreach
				return $creation;
			}
		}
	}

	/**
	 * Method to search for shapefiles in a directory and then insert them into postgis
	 * @param string $path
	 * @return true on success or error msg on failure
	 * @access public
	 */
	public function shp2pgsqlFile($path, $filename)
	{
		if(!file_exists($path.$filename))
		{
			return FALSE; //$debugmsg;
		}
		else {
			chdir($path);
			$creation = $this->_create_sql($filename);
		}
		return $creation;
	}

	/**
	 * Method to return the extents of the geometry 
	 * @param string $dbname
	 * @param string $tablename
	 * @return array $extents
	 */
	public function get_extents($tablename)
	{
		$query = "SELECT EXTENT(the_geom) from $tablename";
		$res = $this->getArray($query);
		if ($res) {
			$extents = $res[0];
			$extents = str_replace("BOX3D","",$extents);
			$extents = str_replace("(","",$extents);
			$extents = str_replace(")","",$extents);
			$extents = str_replace("0,","",$extents);
			$extents = "EXTENT"." ".$extents;
			//return an array of the object
			return $res[0]['extent'];
		}
		else{
			return FALSE;
		}
	}

	public function vac($table)
	{
		$query = "VACUUM ANALYZE $table";
		$res = $this->_execute($query);
		return $res;
	}

	public function pointFromLatLon($lat, $lon, $table, $srid = 27700)
	{
		$sql = 'INSERT INTO '.$table.' ("id",coords)
  ( SELECT "id", SetSRID(MakePoint(x_coord, y_coord), 27700)
    from flat_tbl );';

	}

	public function addGeomToGeonames($srid, $tablename = 'tbl_geonames', $column = 'the_geom', $type = 'POINT', $dim = 3)
	{

		$query = "SELECT addGeometryColumn('$this->database', '$tablename', '$column', $srid, '$type', $dim)";
		return $this->_execute($query);
	}

	public function createGeomFromPoints($tablename = 'tbl_geonames', $column = 'the_geom', $lon = 27.8666667, $lat = -26.1666667, $srid = 27700)
	{
		$query = "update $tablename set $column = setsrid(makepoint($lon, $lat), $srid)";
		return $this->_execute($query);
	}


}
?>