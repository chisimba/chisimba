<?php
/**
 * SAHRIS Collections manager controller class
 *
 * Class to control the Collections manager module
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
 * @category  chisimba
 * @package   sahriscollectionsman
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
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
 * SAHRIS Collections manager controller class
 *
 * Class to control the Collections manager module.
 *
 * @category  Chisimba
 * @package   sahriscollectionsman
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class sahriscollectionsman extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;
    public $objExif;
    public $objMarc;
    public $objRdf;
    public $objMongodb;
    public $objCollOps;

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            // $this->requiresLogin();
            $this->objLanguage   = $this->getObject ( 'language', 'language' );
            $this->objConfig     = $this->getObject('altconfig', 'config');
            $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser       = $this->getObject('user', 'security');
            $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
            $this->objRdf        = $this->getObject ('rdf', 'rdfgen');
            $this->objCollOps    = $this->getObject('sahriscollectionsops');
            $this->objDbColl     = $this->getObject('dbsahriscollections');
            
            //Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();

			// Define the paths we will be needing
			define ( "RDFAPI_INCLUDE_DIR", $this->getResourcePath ('api/', 'rdfgen'));
			include (RDFAPI_INCLUDE_DIR . "RdfAPI.php");

            if($this->objModuleCat->checkIfRegistered('activitystreamer'))
            {
                $this->objActStream = $this->getObject('activityops','activitystreamer');
                $this->eventDispatcher->addObserver(array($this->objActStream, 'postmade' ));
                $this->eventsEnabled = TRUE;
            } else {
                $this->eventsEnabled = FALSE;
            }
        }
        catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            case NULL:
            default:
                $this->nextAction('viewsites');
                break;

            case 'addform' :
                // check that there is a collection to add to first...
                $cnames = $this->objDbColl->getCollectionNames();
                if(!empty($cnames)) {
                    $form = $this->objCollOps->addRecordForm();   
                }
                else {
                    $form = $this->objLanguage->languageText("mod_sahriscollectionsman_nocolldefined", "sahriscollectionsman");
                }
                $this->setVarByRef('form', $form);
                return 'add_tpl.php';
                break;
                
            case 'collform' :
                $form = $this->objCollOps->addCollectionForm();
                $this->setVarByRef('form', $form);
                return 'addcoll_tpl.php';
                break;

            case 'createcollection' :
                $collname = $this->getParam('cn');
                $comment = $this->getParam('comment');
                $insarr = array('userid' => $this->objUser->userId(), 'collname' => $collname, 'comment' => $comment);
                $this->objDbColl->insertCollection($insarr);
                $this->nextAction('');
                break;
                
            case 'viewcollection' :
                $siteid = $this->getParam('siteid');
                $colls = $this->objDbColl->getCollectionsBySiteId($siteid);
                $collect = $this->objCollOps->formatCollections($colls);
                $this->setVarByRef('collect', $collect);
                return 'sitecollections_tpl.php';
                break;

            case 'addrec' :
                $sitename = $this->getParam('sitename');
                $collection = $this->getParam('collection');
                $objtype = $this->getParam('objtype');
                $objloc = $this->getParam('objloc');
                $objstatus = $this->getParam('objstatus');
                // $username = $this->objUser->userName($this->objUser->userId());
                $accno = $this->getParam('ano');
                $coll = $this->getParam('coll');
                $title = $this->getParam('title');
                $desc = $this->getParam('desc');
                // $datecreated = $this->getParam('datecreated');
                $media = $this->getParam('media');
                $comment = $this->getParam('comment');
                // parse the site name and optionally create a new one if needs be
                $sid = $this->objDbColl->getSiteByName($sitename);
                if($sid == NULL) {
                    $siteabbr = metaphone($sitename, 3);
                    $siteins = array('userid' => $this->objUser->userId(), 'sitename' => $sitename, 'siteabbr' => $siteabbr, 
                                     'sitemanager' => NULL, 'sitecontact' => NULL, 'lat' => NULL, 'lon' => NULL, 'comment' => NULL);
                    $sid = $this->objDbColl->addSiteData($siteins);
                }
                    
                $sitedet = $this->objDbColl->getSiteDetails($sid);
                $siteaccabbr = $sitedet[0]['siteabbr'];
                $sitecount = $this->objDbColl->countItemsInSite($sid);
                  
                $siteacc = $siteaccabbr.$sitecount;
                    
                // get the collection id from name
                $collid = $this->objDbColl->getCollByName($collection);
                if($collid == NULL) {
                    // create a collection as it doesn't exist
                    $insarr = array('userid' => $this->objUser->userId(), 'collname' => $collection, 'comment' => NULL, 
                                    'sitename' => $sitename, 'siteid' => $sid);
                    $collid = $this->objDbColl->insertCollection($insarr);
                }
                    
                $insarr = array('userid' => $this->objUser->userId(), 'siteid' => $sid, 'siteacc' => $siteacc,
                                'accno' => $accno, 'objtype' => $objtype, 'collection' => $collid, 
                                'title' => $title, 'description' => $description, 'media' => $media, 'comment' => $comment, 'location' => $objloc, 
                                'status' => $objstatus);
                $res = $this->objDbColl->insertRecord($insarr);
                $this->nextAction('');
                break;

            case 'getrecord' :
                $acno = $this->getParam('acno');
                $coll = $this->getParam('coll');
                $res = $this->objDbColl->getSingleRecord($acno, $coll);
                $this->setVarByRef('res', $res);
                return 'viewsingle_tpl.php';
                break;
                
            case 'deleterecord' :
                $recordid = $this->getParam('recordid');
                $collid = $this->getParam('collectionid');
                $this->objDbColl->deleterecord($recordid);
               
                $this->nextAction(array('module' => 'sahriscollectionsman', 'action' => 'viewrecords', 'collid' => $collid), 'sahriscollectionsman');
                break;
                
            case 'editrecord' :
                $recordid = $this->getParam('recordid');
                $collid = $this->getParam('collectionid');
                $data = $this->objDbColl->getSingleRecordById($recordid);
                $this->setVarByRef('data', $data);
                return 'editrecord_tpl.php';
                break;
                
            case 'recedit' :
                $recordid = $this->getParam('recordid');
                $sitename = $this->getParam('sitename');
                $gensite = $this->getParam('gensite');
                $username = $this->objUser->userName();
                $collectionname = $this->getParam('collectionname');
                $objname = $this->getParam('objname');
                $objtype = $this->getParam('objtype');
                $accno = $this->getParam('accno');
                $acqmethod = $this->getParam('acqmeth');
                $acqdate = $this->getParam('acqdate');
                $acqsrc  = $this->getParam('acqsrc');
                $origmedia = $this->getParam('origmedia');
                $commname = $this->getParam('commname');
                $localname = $this->getParam('locname');
                $classname = $this->getParam('classname');
                $catbyform = $this->getParam('catbyform');
                $catbytech = $this->getParam('catbytech');
                $material = $this->getParam('material');
                $technique = $this->getParam('technique');
                $dimensions = $this->getParam('dimensions');
                $normalloc = $this->getParam('normalloc');
                $currloc = $this->getParam('currloc');
                $reason = $this->getParam('reason');
                $remover = $this->getParam('remover');
                $physdesc = $this->getParam('physdesc');
                $distfeat = $this->getParam('distfeat');
                $currcond = $this->getParam('currcond');
                $conservemeth = $this->getParam('conservemeth');
                $conservedate = $this->getParam('conservedate');
                $conservator = $this->getParam('conservator');
                $histcomments = $this->getParam('histcomments');
                $maker = $this->getParam('maker');
                $prodplace = $this->getParam('prodplace');
                $prodperiod = $this->getParam('prodperiod');
                $histuser = $this->getParam('histuser');
                $placeofuse = $this->getParam('placeofuse');
                $periodofuse = $this->getParam('periodofuse');
                $provenance = $this->getParam('provenance');
                $collector = $this->getParam('collector');
                $collectdate = $this->getParam('collectdate');
                $collmethod = $this->getParam('collmethod');
                $collnumber = $this->getParam('collnumber');
                $pubref = $this->getParam('pubref');
                $siteid = $this->getParam('siteid');
                $collectionid = $this->getParam('collectionid');
                $media = $this->getParam('media');
                
                // parse the site name and optionally create a new one if needs be
                $sid = $siteid;
            
            
                    
                // get the collection id from name
                $collid = $collectionid;
            
                // and now the data     
                $insarr = array(
                'userid' => $this->objUser->userId($username),
                'collectionname' => $collectionname,
                'objname' => $objname,
                'objtype' => $objtype,
                'accno' => $accno,
                'acqmethod' => $acqmethod,
                'acqdate' => $acqdate,
                'acqsrc' => $acqsrc,
                // 'origmedia' => $origmedia,
                'commname' => $commname,
                'localname' => $localname,
                'classname' => $classname,
                'catbyform' => $catbyform,
                'catbytech' => $catbytech,
                'material' => $material,
                'technique' => $technique,
                'dimensions' => $dimensions,
                'normalloc' => $normalloc,
                'currloc' => $currloc,
                'reason' => $reason,
                'remover' => $remover,
                'physdesc' => $physdesc,
                'distfeat' => $distfeat,
                'currcond' => $currcond,
                'conservemeth' => $conservemeth,
                'conservedate' => $conservedate,
                'conservator' => $conservator,
                'histcomments' => $histcomments,
                'maker' => $maker, 
                'prodplace' => $prodplace,
                'prodperiod' => $prodperiod,
                'histuser' => $histuser,
                'placeofuse' => $placeofuse,
                'periodofuse' => $periodofuse,
                'provenance' => $provenance,
                'collector' => $collector,
                'collectdate' => $collectdate,
                'collmethod' => $collmethod,
                'collnumber' => $collnumber,
                'pubref' => $pubref,
                /*'gensite' => $gensite,
                'media64' => $media64,
                'filename' => $filename,
                'username' => $username,*/
                'media' => $media,
                'collectionid' => $collid,
                );
            
                $res = $this->objDbColl->updateRecord($recordid, $insarr);
                $this->nextAction('');
                break;
               
                
            case 'viewsingle' :
                $id = $this->getParam('id');
                $res = $this->objDbColl->getSingleRecordById($id);
                $this->setVarByRef('res', $res);
                return 'viewsingle_tpl.php';
                break;
                
            case 'viewrecords' :
                $collid = $this->getParam('collid');
                $count = $this->objDbColl->getCollRecordCount($collid);
                $pages = ceil ( $count / 1 );
                $this->setVarByRef ( 'pages', $pages );
                $this->setVarByRef('collid', $collid);
                header("Content-Type: text/html;charset=utf-8");
                return 'collrecords_tpl.php';
                break;
                
            case 'viewrecsajax' :
                $page = intval ( $this->getParam ( 'page', 0 ) );
                $collid = $this->getParam('collid');
                if ($page < 0) {
                    $page = 0;
                }
                $start = $page * 1;
                $msgs = $this->objDbColl->getRange($collid, $start, 1);
                $this->setVarByRef ( 'msgs', $msgs );
                header("Content-Type: text/html;charset=utf-8");
                return 'viewrecs_ajax_tpl.php';
                break;

            case 'search':
                $query = $this->getParam('q', NULL);
                if($query == NULL) {
                    $res = NULL;
                    return 'search_tpl.php';
                }
                else {
                    $res = $this->objDbColl->searchItems($query);
                    $this->setVarByRef('res', $res);
                    return 'search_tpl.php';
                }
                break;
                
            case 'searchform' :
                $form = $this->objCollOps->searchForm();
                $this->setVarByRef('form', $form);
                return 'search_tpl.php';
                break;
                
            case 'uploadcsv' :
                $uploadform = $this->objCollOps->uploadCsvForm();
                $this->setVarByRef('uploadform', $uploadform);
                return 'uploadcsv_tpl.php';
                break;
                
            case 'importcsv' :
                $csv = $this->getParam('csv');
                $objFile = $this->getObject('dbfile', 'filemanager');
                $file = $objFile->getFullFilePath($csv);
                $collarr = $this->objCollOps->parseCSV($file);
                $this->objCollOps->processCsvData($collarr);
                $this->nextAction('');
                break;
                
            case 'viewsites' :
                $sites = $this->objDbColl->getAllSites();
                $details = $this->objCollOps->formatSites($sites);
                $this->setVarByRef('details', $details);
                return 'sitelist_tpl.php';
                break;
                
            case 'sitesreport' :
                $sites = $this->objDbColl->getAllSites();
                $details = $this->objCollOps->sitesReport($sites);
                $this->setVarByRef('details', $details);
                return 'sitesreport_tpl.php';
                break;
                
            case 'objectreport' :
                ini_set('max_execution_time', -1);
                $data = $this->objCollOps->archive();
                $graph = $this->objCollOps->graphObjects($data);
                $this->setVarByRef('graph', $graph);
                return 'objectsreport_tpl.php';
                break;
                
            case 'editsite' :
                $siteid = $this->getParam('siteid');
                $siteform = $this->objCollOps->editSiteForm($siteid);
                $this->setVarByRef('siteform', $siteform);
                return 'site_tpl.php';
                break;
                
            case 'updatesitedata' :
                $id = $this->getParam('id');
                $geotags = $this->getParam('geotag');
                $locarr = explode(",", $geotags);
                $lat = trim($locarr[0]);
                $lon = trim($locarr[1]);
                $sn = $this->getParam('sn');
                $siteabbr = $this->getParam('sa'); //metaphone($sn, 3);
                $sm = $this->getParam('sm');
                $sc = $this->getParam('sc');
                $scom = $this->getParam('scom');
                
                $updatearr = array('userid' => $this->objUser->userId(), 'sitename' => $sn, 'sitemanager' => $sm, 
                                   'comment' => $scom, 'lat' => $lat, 'lon' => $lon, 'siteabbr' => $siteabbr, 'sitecontact' => $sc);
                $this->objDbColl->updateSiteInfo($updatearr, $id);
                $this->nextAction('viewsites');
                break;

            
        }
    }

}
?>
