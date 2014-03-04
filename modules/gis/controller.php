<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check
class gis extends controller
{
	public $objLog;
	public $objLanguage;
	public $objGisOps;
	public $objPostGis;
	public $objGisUtils;
	public $objMapserverOps;
	public $objSysConfig;

	/**
     * Constructor method to instantiate objects and get variables
     */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->objGisOps = $this->getObject('gisops');
			$this->objGisUtils = $this->getObject('gisutils');
			$this->objMapserverOps = $this->getObject('mapserverops');
			$this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
			//Get the activity logger class
			$this->objLog = $this->newObject('logactivity', 'logger');
			//Log this module call
			$this->objLog->log();
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
	public function dispatch($action = Null)
	{
		switch ($action) {
			default:
				//return the upload form
				return 'upload_tpl.php';
				break;

			case 'showmap':
				$this->requiresLogin();
				$layers = $this->getParam('layers');
				$size = $this->getParam('mapsize');
				$extent = $this->getParam('mapext');

				
				$mapfile = $this->objSysConfig->getValue('mapfile', 'gis'); 
				$layers = $this->objSysConfig->getValue('default_layers', 'gis'); //'mrctest2+mrctest1';//type in layers name here o display in mapserever
				$mapservcgi = $this->objSysConfig->getValue('mapserv_binary', 'gis'); //'/cgi-bin/mapserv.exe';  //'/cgi-bin/mapserv';
				//copy and paste out of mapfile-fullextent	or get extent from gis app    MaxX   MinY  MinX   Max Y
				$fullextent = $this->objSysConfig->getValue('extents', 'gis');
				$fullextent = explode(", ", $fullextent);
				//$fullextent = array(-47.1234, -38.4304, 73.1755, 40.9487);
				$fullextent = array (floatval($fullextent[0]), floatval($fullextent[1]), floatval($fullextent[2]), floatval($fullextent[3]));
				//var_dump($fullextent); die();
				// bounds  maxX    minX   minY
				$bounds = floatval($fullextent[0]).", ". floatval($fullextent[2]).", ".floatval($fullextent[1]);
				//$bounds = '-47.1234, 73.1755, -38.4304';
				//var_dump($bounds); die();
				
				
				$size = array(800, 800);
				
				$this->objMapserverOps->initMapserver($mapfile, $fullextent);
				//$themap = $this->objMapserverOps->saveMapImage();
				$themap = $this->objMapserverOps->drawMapMsCross($size, $bounds, $layers);
				$this->setVarByRef('mapfile', $mapfile);
				$this->setVarByRef('layers', $layers);
				$this->setVarByRef('mapservcgi', $mapservcgi);
				$this->setVarByRef('bounds', $bounds);
				//$this->setVarByRef('themap', $themap);
				return 'showmap_tpl.php';
				break;
				
			case 'addgeom':
				$this->objPostGis = $this->getObject('dbpostgis');
				$this->objGisOps = $this->getObject('gisops');
				$this->objPostGis->addGeomToGeonames(27700);
				//$this->objPostGis->createGeomFromPoints();
				break;

			case 'uploaddatafile':
				$this->objPostGis = $this->getObject('dbpostgis', 'gis');
				$test = $this->objPostGis->isPostGIS();
				if($test[0]['present'] == 't')
				{
					$filename = $this->getParam('shpzip');
					$objFile = $this->getObject('dbfile', 'filemanager');
					$fpath = $objFile->getFullFilePath($this->getParam('shpzip'));
					$fname = $objFile->getFileName($this->getParam('shpzip'));
					$fpath = str_replace($fname, '', $fpath);
					$destpath = $fpath.'/shapes/';
					if(!file_exists($fpath))
					{
						mkdir($destpath, 0777);
					}
					$this->objGisUtils->unPackFilesFromZip($fpath.$fname, $destpath);
					$this->objPostGis->shp2pgsql($destpath);
					chdir($destpath);
					foreach(glob("*.{shp,dbf,shx}", GLOB_BRACE) as $delfiles)
					{
						unlink($delfiles);
						//echo $delfiles;
					}
				}
				else {
					throw new customException($this->objLanguage->languageText("mod_gis_nopostgis", "gis"));
				}

				$this->nextAction('');
				break;
		}
	}
	
		/**
    * Overide the login object in the parent class
    *
    * @param  void  
    * @return bool  
    * @access public
    */
	public function requiresLogin()
	{
        return FALSE;
	}
}
?>