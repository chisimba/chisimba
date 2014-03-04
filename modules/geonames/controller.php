<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
ini_set("max_execution_time", -1);
ini_set("memory_limit", -1);
// end security check
class geonames extends controller
{
    public $objLog;
    public $objLanguage;
    public $objUser;
    public $objGeoOps;
    public $objDbGeo;
    
    
    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
        	$this->objGeoOps = $this->getObject('geoops');
        	$this->objDbGeo = $this->getObject('dbgeonames');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->objConfig = $this->getObject('altconfig', 'config');
            
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
            	return 'main_tpl.php';
            	break;
            	
            case 'uploaddatafile':
            	$userid = $this->objUser->userId();
            	$file = $this->getParam('zipfile');
            	//file id is returned, so lets go and get the actual file for parsing...
            	$geozip = $this->objGeoOps->unpackPdfs($file);
            	//$dataArr = file($geozip);
            	//print_r($dataArr);
            	// rename the file to a csv
            	$records = $this->objGeoOps->parseCSV($geozip);
            	/*foreach($records as $entry)
            	{
            		@$insarr = array('userid' => $userid, 'geonameid' => $entry[0], 'name' => $entry[1], 'asciiname' => $entry[2], 'alternatenames' => $entry[3], 
            						'latitude' => $entry[4], 'longitude' => $entry[5], 'featureclass' => $entry[6], 'featurecode' => $entry[7], 
            						'countrycode' => $entry[8], 'cc2' => $entry[9], 'admin1code' => $entry[10], 'admin2code' => $entry[11], 
            						'population' => $entry[12], 'elevation' => $entry[13], 'gtopo30' => $entry[14], 'timezoneid' => $entry[15], 
            						'moddate' => $entry[16]
            						);
            		$this->objDbGeo->insertRecord($insarr);
            	}*/
            	$message = $this->objLanguage->languageText("mod_geonames_uploaddone", "geonames");
            	$this->setVarByRef('message', $message);
            	return 'main_tpl.php';
            	break;
            	
            case 'viewaskml':
            	// grab the database and send out as kml
            	$kml = $this->getObject('kmlgen','simplemap');
            	// get all entries
            	$entries = $this->objDbGeo->grabAllRecords();
            	$doc = $kml->overlay('my map','a test map');
            	foreach($entries as $row)
            	{
            		if(empty($row['elevation']))
            		{
            			$el = 0;
            		}
            		else {
            			$el = $row['elevation'];
            		}
            		$doc .= $kml->generateSimplePlacemarker($row['name'], $row['name'], $row['longitude'],$row['latitude'],$el);
            	}
            	$doc .= $kml->simplePlaceSuffix();
            	//echo $doc;
            	$filename = $this->objConfig->getcontentbasePath().'test.kml';
            	touch($filename);
            	chmod($filename, 0777);
				$somecontent = $doc;

				// Let's make sure the file exists and is writable first.
				if (is_writable($filename)) {

					// In our example we're opening $filename in append mode.
					// The file pointer is at the bottom of the file hence
					// that's where $somecontent will go when we fwrite() it.
					if (!$handle = fopen($filename, 'w')) {
						echo "Cannot open file ($filename)";
						exit;
					}

					// Write $somecontent to our opened file.
					if (fwrite($handle, $somecontent) === FALSE) {
						echo "Cannot write to file ($filename)";
						exit;
					}

					echo "Success, wrote kml to file ($filename)";

					fclose($handle);

				} else {
					echo "The file $filename is not writable";
				}
				break;
            case 'search':
                return 'search.php';

        }
    }

}
?>