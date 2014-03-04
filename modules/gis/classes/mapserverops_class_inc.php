<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * Class to handle mapserver elements
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @author Paul Scott
 * @copyright GNU/GPL, AVOIR
 * @package gis
 * @access public
 */
class mapserverops extends object
{
	public $objConfig;
	public $objMapserver;
	public $image;
	public $image_url;

	/**
         * Standard init function called by the constructor call of Object
         *
         * @param void
         * @return void
         * @access public
         */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->loadClass('href', 'htmlelements');
			$this->objConfig = $this->getObject('altconfig', 'config');
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/**
         * Mapserver init function. 
         *
         * Public method to create the mapserver instance and make it available as a class property
         * @access public
         * @param string - mapfile
         * @return object - mapserver object
         */
	public function initMapserver($mapfile, $fullextent)
	{
		$this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
		$dll = $this->objSysConfig->getValue('mapscript_dll', 'gis');
		if($dll === 'TRUE')
		{
			dl('php_mapscript.dll');
		}

		$this->objMapserver = ms_newMapObj($mapfile);
		$this->objMapserver->setExtent($fullextent[0], $fullextent[1], $fullextent[2], $fullextent[3]);
	}

	public function saveMapImage()
	{
		$this->image = $this->objMapserver->draw();
		$this->image_url = $this->image->saveWebImage();
		return $this->image_url;
	}

	/*public function addLayer($name, $type, $attributes = array())
	{
	$layer = $this->objMapserver->ms_newShapeObj($type);
	foreach($attributes as $key => $attribute)
	{
	$layer->set($key[$value]);
	}
	return TRuE;
	}*/

	public function drawMapMsCross($size=array(800,800), $extent=array(-180,-90,180,90), $layers='')
	{
		// We create the map object based on the mapfile received as parameter
		$this->objMapserver->setSize($size[0], $size[1]);
		$layers = $this->addLayer('clouds');
		$layerslist=$layers;
		for ($layer = 0; $layer < $this->objMapserver->numlayers; $layer++) {
			$lay = $this->objMapserver->getLayer($layer);
			$lay->set("status",MS_ON);
			/*var_dump($lay);
			if ((strpos($layerslist, ($this->objMapserver->getLayer($layer)->name)) !== false) || 
			(($this->objMapserver->getLayer($layer)->group != "") && 
			(strpos($layerslist, ($this->objMapserver->getLayer($layer)->group)) !== false)))
			{
				// if the name property of actual $lay object is in $layerslist
				// or the group property is in $layerslist then the layer was requested
				//so we set the status ON... otherwise we set the stat to OFF
				echo "switching on: ".$lay->name;
				$lay->set("status",MS_ON);
			}
			else {
				echo "switching off: ".$lay->name;
				$lay->set("status",MS_OFF);
			} */
		}

		// The next lines are the same as previous mapscript
		$image = $this->objMapserver->draw();
		//echo $this->getResourcePath('maps/', 'gis'); die();
		if(!file_exists($this->getResourcePath('maps/map.png', 'gis')))
		{
			@mkdir($this->getResourcePath('maps', 'gis'));
			@chmod($this->getResourcePath('maps', 'gis'), 0777);
		}
		$imgfile = 'map.png';
		$imagename = $image->saveWebImage($imgfile);
		//echo $imagename; die();
		//var_dump($image->saveWebImage()); die();
		//$retimage = ImageCreateFromPng($imagename);
		//return $retimage;
	}

	public function addLayer($name)
	{
		/* Create a data layer and associate it with the map.
		$newLayer = ms_newLayerObj($this->objMapserver);
		$newLayer->set( "name", $name);
		$newLayer->set( "type", MS_LAYER_LINE);
		$newLayer->set( "status", MS_ON);
		$newLayer->set( "connection", 'user=www-data dbname=mrc host=localhost port=5432');
		$newLayer->set( "data","the_geom from Roads");

		// Create a class object to set feature drawing styles.
		$mapClass = ms_newClassObj($newLayer);
		// Create a style object defining how to draw features
		$layerStyle = ms_newStyleObj($mapClass);
		$layerStyle->color->setRGB(250,0,0);
		$layerStyle->outlinecolor->setRGB(255,255,255);
		//$layerStyle->set( "symbolname", "circle");
		//$layerStyle->set( "size", "10") */

		// Create a map symbol, used as a brush pattern
		// for drawing map features (lines, points, etc.)
		$nSymbolId = ms_newSymbolObj($this->objMapserver, "circle");
		$oSymbol = $this->objMapserver->getsymbolobjectbyid($nSymbolId);
		$oSymbol->set("type", MS_SYMBOL_ELLIPSE);
		$oSymbol->set("filled", MS_TRUE);
		$aPoints[0] = 1;
		$aPoints[1] = 1;
		$oSymbol->setpoints($aPoints);

		// Create another layer to hold point locations
		$oLayerPoints = ms_newLayerObj($this->objMapserver);
		$oLayerPoints->set( "name", "custom_points");
		$oLayerPoints->set( "type", MS_LAYER_POINT);
		$oLayerPoints->set( "status", MS_ON);
		// Open file with coordinates and label text (x,y,label)
		$fPointList = array("18.35,-19.06,Angie", "21.40,-31.03,Ray");
		// For each line in the text file
		foreach ($fPointList as $sPointItem)
		{
			$aPointArray = explode(",",$sPointItem);
			//print_r($aPointArray);
			// :TRICKY: Although we are creating points
			// we are required to use a line object (newLineObj)
			// with only one point. I call it a CoordList object
			// for simplicity since we aren't really drawing a line.
			$oCoordList = ms_newLineObj();
			$oPointShape = ms_newShapeObj(MS_SHAPE_POINT);
			$oCoordList->addXY($aPointArray[0],$aPointArray[1]);
			$oPointShape->add($oCoordList);
			$oPointShape->set( "text", chop($aPointArray[2]));
			$oLayerPoints->addFeature($oPointShape);
		}
		// Create a class object to set feature drawing styles.
		$oMapClass = ms_newClassObj($oLayerPoints);
		// Create a style object defining how to draw features
		$oPointStyle = ms_newStyleObj($oMapClass);
		$oPointStyle->color->setRGB(250,0,0);
		$oPointStyle->outlinecolor->setRGB(255,255,255);
		$oPointStyle->set( "symbolname", "circle");
		$oPointStyle->set( "size", 10);
		
		// Create label settings for drawing text labels
		$oMapClass->label->set( "position", MS_AUTO);
		$oMapClass->label->color->setRGB(250,0,0);
		$oMapClass->label->outlinecolor->setRGB(255,255,255);
	}
}
?>