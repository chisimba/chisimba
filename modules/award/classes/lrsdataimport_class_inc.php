<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
die( "You cannot view this page directly" );
}
/**
* The lrs data import class will provide methods to import data from the existing database
*
* @copyright (c) 2007, AVOIR (http://avoir.uwc.ac.za)
* @license GNU/GPL
* @package lrs
* @version $Id: lrsdataimport_class_inc.php,v 1.12 2007/12/05 14:08:24 nic Exp $
* @author Nic Appleby
*/

class lrsdataimport extends dbTable
{

    /**
    * The KINKY user management object from securtiy module
    *
    * @var object objUser
    * @access private
    */
    var $_objUser;

    /**
     * The Database configuration object
     *
     * @var object
     * @access private
     */
    var $_objDBConfig;

    /**
     * The DB object to connect to the Award database to import from
     *
     * @access private
     * @var string $_awardDBName
     */
    var $_awardDB;

    /**
     * Variable to store latest error message, c style
     *
     * @access private
     * @var string $_error
     */
    var $_error;

    /**
    * Method to initialize the lrs import object
    *
    * @access public
    */
    function init()
    {
        $this->_objUser = $this->getObject('user','security');
        $this->_objDBConfig = $this->getObject('dbconfig','config');
        $this->_objDBAgree = $this->getObject('dblrsagree','lrsagreements');
        $this->_objDBWages = $this->getObject('dblrswage','lrswages');
        $this->_error = '';
    }

    /**
     * Method to return the last error that occurred, c style
     *
     * @access public
     * @return string
     */
    function getError() {
        return $this->_error;
    }
    
    function log($str) {
        $fp = fopen('import.log','ab');
        fwrite($fp,"$str\n");
        fclose($fp);
    }

    
    function doImport($dbName) {
        set_time_limit(0);
        $this->connectAward($dbName);
        if ($this->checkDatabase() === false) {
        	$this->_error = "Cannot connect to database '$dbName'";
            return FALSE;
        }
        if (!$this->databaseEmpty()) {
        	$this->_error = "Database '$dbName' is not empty";
            return FALSE;
        }
        $this->log(date('Y-m-d h:i:s').": -----==IMPORT STARTED==-----\n\n");
        if (!$this->importUnits()) {
        	return FALSE;
        } else {	
        	$this->log(date('Y-m-d h:i:s').": done units");
        	$this->importAgreements();
        	$this->log(date('Y-m-d h:i:s').": done agreements");
        	$this->importIndexes();
        	$this->log(date('Y-m-d h:i:s').": done indexes");
    		$this->importIndexValues();
			$this->log(date('Y-m-d h:i:s').": done index values");
            $this->importRegions();
        	$this->log(date('Y-m-d h:i:s').": done regions");
        	$this->importDistricts();
        	$this->log(date('Y-m-d h:i:s').": done districts");
        	// sic major divs done by default now
        	//$this->importSicMajorDivs();
    		//$this->log(date('Y-m-d h:i:s').": done sic major divs");
        	$this->importOrgArea();
        	$this->log(date('Y-m-d h:i:s').": done org areas");
        	$this->importWages();
        	$this->log(date('Y-m-d h:i:s').": done wages");
        	//$this->importOrgType();
        	//$this->log(date('Y-m-d h:i:s').": done org type");
        	$this->importOrgParty();
        	$this->log(date('Y-m-d h:i:s').": done org party");
        	//$this->importSocOld();
        	//$this->log(date('Y-m-d h:i:s').": done soc old");
        	//$this->importSocMajorGroup();
        	//$this->log(date('Y-m-d h:i:s').": done soc major group");
        	//$this->importGrades();
        	//$this->log(date('Y-m-d h:i:s').": done grades");
        	//$this->importJobCodes();
        	//$this->log(date('Y-m-d h:i:s').": done job codes");
        	//$this->importSocSubMajorGroup();
        	//$this->log(date('Y-m-d h:i:s').": done soc sub major group");
        	//$this->importSocMinorGroup();
        	//$this->log(date('Y-m-d h:i:s').": done soc minor group");
        	//$this->importSocUnitGroup();
        	//$this->log(date('Y-m-d h:i:s').": done soc unit group");
        	//$this->importSocName();
        	//$this->log(date('Y-m-d h:i:s').": done soc name");
        	$this->importWageSocName();
        	$this->log(date('Y-m-d h:i:s').": done wage soc name");
        	$this->importOrgBranch();
        	$this->log(date('Y-m-d h:i:s').": done org branch");
        	$this->importOrgUnitBranch();
        	$this->log(date('Y-m-d h:i:s').": done org unit branch");
        	//$this->importSicDiv();
        	//$this->log(date('Y-m-d h:i:s').": done sic div");
        	//$this->importSicMajorGroup();
        	//$this->log(date('Y-m-d h:i:s').": done sic major group");
        	//$this->importSicGroup();
        	//$this->log(date('Y-m-d h:i:s').": done sic group");
        	//$this->importSicSubGroup();
        	//$this->log(date('Y-m-d h:i:s').": done sic sub group");
        	//$this->importSicOld();
        	$this->importBargUnitSic();
        	$this->log(date('Y-m-d h:i:s').": done barg unit sic");
        	$this->importAgreeAllowance();
        	$this->log(date('Y-m-d h:i:s').": done allowance");
        	$this->importAgreeChildcare();
        	$this->log(date('Y-m-d h:i:s').": done childcare");
        	$this->importAgreeHour();
        	$this->log(date('Y-m-d h:i:s').": done hours");
        	$this->importAgreeLeave();
			$this->log(date('Y-m-d h:i:s').": done leave");
        	$this->log(date('Y-m-d h:i:s').": -----==IMPORT COMPLETE==-----\n");
        }
        return TRUE;
    }

    /**
     * Method to connect to a new table in the KINKY database
     *
     * @access private
     * @param string $tableName The name of the table to connect to
     * @param object $objDB The DB connection object of the database to connect to
     */
    function _changeTable($tableName,$objDB = null) {
        (!isset($objDB))? parent::init($tableName) : parent::init($tableName,FALSE,&$objDB);
    }

    /**
     * Method to create a connection to the Award database
     *
     * @param string $awardName
     */
    function connectAward($awardName) {
        $dsn = $this->_objDBConfig->dbDriver()."://".$this->_objDBConfig->dbUser().":";
        $dsn .= $this->_objDBConfig->dbPassword()."@".$this->_objDBConfig->dbServer()."/";
        $dsn .= $awardName;
        $this->_awardDB = &DB::connect($dsn);
        if (PEAR::isError($this->_awardDB)) {
            die("error connecting to Award Database $awardName via DSN: $dsn");
        }
    }

    /**
     * Method to check if the connected database is an AWARD database
     *
     * @return PEAR Result|false
     */
    function checkDatabase() {
        $this->_changeTable('agree', &$this->_awardDB);
        $result = $this->query('SELECT * FROM agree');
        return $result;
    }
    
    /**
     * Method to check if the connected database import has been done yet
     *
     * @return PEAR Result|false
     */
    function databaseEmpty() {
        $this->_changeTable('tbl_award_agree');
        return ($this->getRecordCount() == 0)? true : false;
    }

    /**
     * Method to import the barg_units table from an award database to org_unit
     * in the KINKY LRS structure
     *
     * @return TRUE|FALSE
     */
    function importUnits() {
        $this->_changeTable('barg_unit',&$this->_awardDB);
        $units = $this->getAll('ORDER BY barg_unit_id ASC');
        $this->_changeTable('tbl_award_unit');
        $success = true;
        $this->_error = "Failed to insert row(s):<br />";
        foreach ($units as $unit) {
            $newUnit['id'] = $unit['barg_unit_id'];
            $newUnit['name'] = $unit['barg_unit_name'];
            $newUnit['active'] = '1';
            if (!$this->insert($newUnit)) {
                $this->_error .= "id: {$newUnit['id']} name: {$newUnit['name']}<br />";
                $success = false;
            }
        }
        return $success;
    }

    /**
    * Method to import agreements and intelligently guess their types
    *
    * @access public
    */
    function importAgreements() {
        $this->_changeTable('agree',&$this->_awardDB);
        $agreements = $this->getAll("ORDER BY agree_id ASC");
        $this->_changeTable('tbl_award_agree');
        $success = true;
        $this->_error = "Failed to insert row(s):<br />";
        foreach ($agreements as $agree) {
            $newAgree['id'] = $agree['agree_id'];
            //$newAgree['pay_period_typeId'] = ($agree['pay_period_type_id'] == 0)? 1 : $agree['pay_period_type_id'];
            //check barg unit is set, if not try find the actual one
            if ($agree['barg_unit_id'] == 0) {
            	$str = substr($agree['agree_name'],0,strlen($agree['agree_name']-16));
            	$this->_changeTable('barg_unit',&$this->_awardDB);
            	$actualUnitArr = $this->getAll("WHERE barg_unit_name LIKE '$str%'");
            	$actualUnit = current($actualUnitArr);
            	$agree['barg_unit_id'] = $actualUnit['barg_unit_id'];
            	$this->_changeTable('tbl_award_agree');
            }
            $newAgree['unitid'] = $agree['barg_unit_id'];
            $newAgree['name'] = $agree['agree_name'];
            $newAgree['implementation'] = date('Y-m-d',$agree['agree_date_implementation']);
            $newAgree['length'] = $agree['agree_length_months'];
            $newAgree['workers'] = ($agree['agree_num_workers_actual']==0)? $agree['agree_num_workers_est'] : $agree['agree_num_workers_actual'];
            $newAgree['notes'] = $agree['agree_notes'];
            $newAgree['active'] = 1;
            $newAgree['agree_typeId'] = 1;
            // insert preg_matches to fix the agreement type
            // not needed for nambian dataset
            //if (preg_match('/\s+WD\s*/',$agree['agree_name'])) {
            /*    $newAgree['agree_typeId'] = 1;
            } else {
                if (preg_match('/(\s+bc\s+)|(\s+ic\s+)|(\s*industry\s+)|(\s*trade\s+)/i',$agree['agree_name'])) {
                    $newAgree['agree_typeId'] = 3;
                } else {
                    if (preg_match('/(\s*mun\s+)|(\s*pscbc)/i',$agree['agree_name'])) {
                        $newAgree['agree_typeId'] = 4;
                    } else {
                        if (preg_match('/(sec.*det)|(s\/det)/i',$agree['agree_name'])) {
                            $newAgree['agree_typeId'] = 5;
                        } else {
                            $newAgree['agree_typeId'] = 2;
                        }
                    }
                }
            }*/
            if (!$this->insert($newAgree)) {
                $this->_error .= "id: {$newAgree['id']} name: {$newAgree['name']}<br />";
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Method to import values from the index types table
     *
     * @return true|false
     */
    function importIndexes() {
        $this->_changeTable('index_type', &$this->_awardDB);
        $indexes = array('CPI','CPIX','FPI');
        $indexTypes = $this->getAll("ORDER BY index_type_id ASC");
        $this->_changeTable('tbl_award_indexes');
        $success = true;
        $this->_error = 'Failed to insert row(s):<br />';
        foreach ($indexTypes as $index) {
            $newIndex['id'] = $index['index_type_id'];
            $newIndex['shortname'] = $index['index_type_abbr'];
            $newIndex['name'] = $index['index_type_name'];
            $newIndex['description'] = $index['index_type_name'];
            $newIndex['period'] = $index['index_type_period_months'];
            if (in_array($newIndex['shortname'],$indexes)) {
                if (!$this->insert($newIndex)) {
                    $this->_error .= "id: {$newIndex['id']} name: {$newIndex['name']}<br />";
                    $success = false;
                }
            }
        }
        return $success;
     }

     /**
     * Method to import values from the index values table
     *
     * @return true|false
     */
     function importIndexValues() {
        $this->_changeTable('index_type_index_capture_index', &$this->_awardDB);
        $indexValues = $this->getAll("ORDER BY index_type_id ASC");
        $this->_changeTable('tbl_award_indexes');
        $indexes = $this->getAll();
        foreach ($indexes as $ind) {
            $validIndexes[] = $ind['id'];
        }
        $this->_changeTable('tbl_award_index_values');
        $success = true;
        $this->_error = 'Failed to insert row(s):<br />';
        foreach ($indexValues as $index) {
            $newIndex['typeid'] = $index['index_type_id'];
            $newIndex['indexdate'] = date('Y-m-d',$index['index_date']);
            $newIndex['value'] = $index['index_value'];
            if (in_array($newIndex['typeid'],$validIndexes)) { //&& $index['index_capture_id'] == '32') {
                if (!$this->insert($newIndex)) {
                    $this->_error .= "typeId: {$newIndex['typeid']} date: {$newIndex['indexdate']}<br />";
                    $success = false;
                }
            }
        }
        return $success;
     }

     /**
      * Method to import region data
      *
      * @return true|false
      */
     function importRegions() {
         $this->_changeTable('region', &$this->_awardDB);
         $indexValues = $this->getAll("ORDER BY region_id ASC");
         $this->_changeTable('tbl_award_region');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($indexValues as $index) {
             $newIndex['id'] = $index['region_id'];
             $newIndex['abbreviation'] = $index['region_abbr'];
             $newIndex['name'] = $index['region_name'];
             if (!$this->insert($newIndex)) {
                 $this->_error .= "id: {$newIndex['id']} name: {$newIndex['name']}<br />";
                 $success = false;
             }
         }
         $newIndex['id'] = 'init_1';
         $newIndex['abbreviation'] = 'U';
         $newIndex['name'] = 'unknown';
         $this->insert($newIndex);
         return $success;
     }

     /**
      * Method to import district data
      *
      * @return true|false
      */
     function importDistricts() {
         $this->_changeTable('district', &$this->_awardDB);
         $indexValues = $this->getAll("ORDER BY district_id ASC");
         $this->_changeTable('tbl_award_district');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($indexValues as $index) {
             $newIndex['id'] = $index['district_id'];
             $newIndex['regionid'] = $index['region_id'];
             $newIndex['name'] = $index['district_name'];
             $newIndex['urbanindicator'] = $index['district_urban'];
             if (!$this->insert($newIndex)) {
                 $this->_error .= "id: {$newIndex['id']} name: {$newIndex['name']}<br />";
                 $success = false;
             }
         }
         $newIndex['id'] = 'init_1';
         $newIndex['regionId'] = 'init_1';
         $newIndex['name'] = 'unknown';
         $this->insert($newIndex);
         return $success;
     }

     /**
      * Method to import SIC major division data
      *
      * @return true|false
      */
     function importSicMajorDivs() {
         $this->_changeTable('sic_major_div', &$this->_awardDB);
         $indexValues = $this->getAll("ORDER BY sic_major_div_id ASC");
         $this->_changeTable('tbl_award_sicmajordiv');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($indexValues as $index) {
             $newIndex['id'] = $index['sic_major_div_id'];
             $newIndex['description'] = $index['sic_major_div_desc'];
             $newIndex['code'] = $index['sic_major_div_code'];
             $newIndex['notes'] = $index['sic_major_div_notes'];
             $newIndex['dateCreated'] = date('Y-m-d H:i:s');
             $newIndex['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($newIndex)) {
                 $this->_error .= "id: {$newIndex['id']} desc: {$newIndex['description']}<br />";
                 $success = false;
             }
         }
         $newIndex['id'] = "init_0";
         $newIndex['description'] = "Unknown";
         $newIndex['code'] = 0;
         $newIndex['notes'] = '';
         $newIndex['dateCreated'] = date('Y-m-d H:i:s');
         $newIndex['creatorId'] = $this->_objUser->userId();
         $this->insert($newIndex);
         return $success;
     }

     /**
      * Method to import organisation area data
      *
      * @return true|false
      */
    function importOrgArea() {
        $this->_changeTable("barg_unit_region", &$this->_awardDB);
        $indexValues = $this->getAll("ORDER BY barg_unit_id ASC");
        $this->_changeTable('tbl_award_unit_region');
        $success = true;
        $this->_error = 'Failed to insert row(s):<br />';
        foreach ($indexValues as $index) {
            $newIndex['unitid'] = $index['barg_unit_id'];
            $newIndex["regionid"] = $index["region_id"];
            if (!$this->insert($newIndex)) {
               $this->_error .= "unitid: {$newIndex['unitid']} $type id: {$newIndex['regionid']}<br />";
               $success = false;
            }
        }
        return $success;
    }

     /**
      * Method to import organisation party bridging data
      *
      * @return true|false
      */
     function importOrgParty() {
         $this->_changeTable('party', &$this->_awardDB);
         $values = $this->getAll("WHERE party_type_id = 1 ORDER BY party_id");
         $this->_changeTable('tbl_award_party');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['party_id'];
             $new['name'] = $value['party_name'];
             $new['abbreviation'] = $value['party_abbrev'];
             $new['registrationnumber'] = $value['party_reg_number'];
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} name: {$new['name']}<br />";
                 $success = false;
             }
         }
         return $success;
     }

      /**
      * Method to import wage data
      *
      * @return true|false
      */
     function importWages() {
         $this->_changeTable('wage', &$this->_awardDB);
         $values = $this->getAll("ORDER BY wage_id");
         $this->_changeTable('tbl_award_wage');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['wage_id'];
             $new['agreeid'] = $value['agree_id'];
             $new['payperiodtypeid'] = ($value['pay_period_type_id'] == 3)? 2 : $value['pay_period_type_id'];
             $new['notes'] = $value['wage_notes'];
             $new['weeklyrate'] = $value['wage_rate'];
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} agreeId: {$new['agreeid']}<br />";
                 $success = false;
             }
         }
         return $success;
     }

      /**
      * Method to import organisation type data
      *
      * @return true|false
      */
     function importOrgType() {
         $this->_changeTable('party_type', &$this->_awardDB);
         $values = $this->getAll("ORDER BY party_type_id");
         $this->_changeTable('tbl_lrs_org_type');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['party_type_id'];
             $new['name'] = $value['party_type_name'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} name: {$new['name']}<br />";
                 $success = false;
             }
         }
         return $success;
     }

     /**
      * Method to import SOC old data
      *
      * @return true|false
      */
     function importSocOld() {
         $this->_changeTable('soc_old', &$this->_awardDB);
         $values = $this->getAll("ORDER BY soc_old_id");
         $this->_changeTable('tbl_lrs_soc_old');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['soc_old_id'];
             $new['old_code'] = $value['soc_old_code'];
             $new['old_name'] = $value['soc_old_name'];
             $new['jobcode'] = $value['soc_old_jobcode'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} name: {$new['old_name']}<br />";
                 $success = false;
             }
         }
         $new['id'] = 'lrs_1';
         $new['old_code'] = 'none';
         $new['old_name'] = 'none';
         $new['jobcode'] = 'none';
         $new['dateCreated'] = date('Y-m-d H:i:s');
         $new['creatorId'] = $this->_objUser->userId();
         $this->insert($new);
         return $success;
     }

     /**
      * Method to import SOC Major Group data
      *
      * @return true|false
      */
     function importSocMajorGroup() {
         $this->_changeTable('soc_maj_grp', &$this->_awardDB);
         $values = $this->getAll("ORDER BY soc_maj_grp_id");
         $this->_changeTable('tbl_lrs_soc_major_group');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['soc_maj_grp_id'];
             $new['description'] = $value['soc_maj_grp_desc'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} name: {$new['description']}<br />";
                 $success = false;
             }
         }
         return $success;
     }

     /**
      * Method to import job codes data
      *
      * @return true|false
      */
     function importJobCodes() {
         $this->_changeTable('job_codes', &$this->_awardDB);
         $values = $this->getAll("ORDER BY job_code_id");
         $this->_changeTable('tbl_lrs_job_codes');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['job_code_id'];
             $new['name'] = $value['job_code_name'];
             $new['description'] = $value['job_code_desc'];
             $new['notes'] = $value['job_code_notes'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} name: {$new['name']}<br />";
                 $success = false;
             }
         }
         return $success;
     }

     /**
      * Method to import grades data
      *
      * @return true|false
      */
     function importGrades() {
         $this->_changeTable('grade', &$this->_awardDB);
         $values = $this->getAll("ORDER BY grade_id");
         $this->_changeTable('tbl_lrs_grades');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['grade_id'];
             $new['name'] = $value['grade_name'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} name: {$new['name']}<br />";
                 $success = false;
             }
         }
         return $success;
     }

     /**
      * Method to import SOC sub major group data
      *
      * @return true|false
      */
     function importSocSubMajorGroup() {
         $this->_changeTable('soc_sub_maj_grp', &$this->_awardDB);
         $values = $this->getAll("ORDER BY soc_maj_grp_id");
         $this->_changeTable('tbl_lrs_soc_sub_major_group');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = "{$value['soc_maj_grp_id']}{$value['soc_sub_maj_grp_id']}";
             $new['major_groupId'] = $value['soc_maj_grp_id'];
             $new['description'] = $value['soc_sub_maj_grp_desc'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} name: {$new['description']}<br />";
                 $success = false;
             }
         }
         $new['id'] = "1010";
         $new['major_groupId'] = '10';
         $new['description'] = 'Unknown';
         $new['dateCreated'] = date('Y-m-d H:i:s');
         $new['creatorId'] = $this->_objUser->userId();
         $this->insert($new);
         return $success;
     }

     /**
      * Method to import SOC minor group data
      *
      * @return true|false
      */
     function importSocMinorGroup() {
         $this->_changeTable('soc_min_grp', &$this->_awardDB);
         $values = $this->getAll("ORDER BY soc_maj_grp_id");
         $this->_changeTable('tbl_lrs_soc_minor_group');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = "{$value['soc_maj_grp_id']}{$value['soc_sub_maj_grp_id']}{$value['soc_min_grp_id']}";
             $new['major_groupId'] = $value['soc_maj_grp_id'];
             $new['sub_major_groupId'] = "{$value['soc_maj_grp_id']}{$value['soc_sub_maj_grp_id']}";
             $new['description'] = $value['soc_min_grp_desc'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} name: {$new['description']}<br />";
                 $success = false;
             }
         }
         $new['id'] = "101010";
         $new['major_groupId'] = '10';
         $new['sub_major_groupId'] = "1010";
         $new['description'] = 'Unknown';
         $new['dateCreated'] = date('Y-m-d H:i:s');
         $new['creatorId'] = $this->_objUser->userId();
         $this->insert($new);
         return $success;
     }

     /**
      * Method to import SOC unit group data
      *
      * @return true|false
      */
     function importSocUnitGroup() {
         $this->_changeTable('soc_unit_grp', &$this->_awardDB);
         $values = $this->getAll("ORDER BY soc_maj_grp_id");
         $this->_changeTable('tbl_lrs_soc_unit_group');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = "{$value['soc_maj_grp_id']}{$value['soc_sub_maj_grp_id']}{$value['soc_min_grp_id']}{$value['soc_unit_grp_id']}";
             $new['major_groupId'] = $value['soc_maj_grp_id'];
             $new['sub_major_groupId'] = "{$value['soc_maj_grp_id']}{$value['soc_sub_maj_grp_id']}";
             $new['minor_groupId'] = "{$value['soc_maj_grp_id']}{$value['soc_sub_maj_grp_id']}{$value['soc_min_grp_id']}";
             $new['description'] = $value['soc_unit_grp_desc'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                 $this->_error .= "id: {$new['id']} name: {$new['description']}<br />";
                 $success = false;
             }
         }
         $new['id'] = "10101010";
         $new['major_groupId'] = '10';
         $new['sub_major_groupId'] = "1010";
         $new['minor_groupId'] = "101010";
         $new['description'] = 'Unknown';
         $new['dateCreated'] = date('Y-m-d H:i:s');
         $new['creatorId'] = $this->_objUser->userId();
         $this->insert($new);
         return $success;
     }

     /**
      * Method to import SOC name data
      *
      * @return true|false
      */
     function importSocName() {
         $this->_changeTable('soc_name', &$this->_awardDB);
         $values = $this->getAll("ORDER BY soc_name_id");
         $this->_changeTable('tbl_lrs_soc_name');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['soc_name_id'];
             $new['major_groupId'] = $value['soc_maj_grp_id'];
             $new['sub_major_groupId'] = "{$value['soc_maj_grp_id']}{$value['soc_sub_maj_grp_id']}";
             $new['minor_groupId'] = "{$value['soc_maj_grp_id']}{$value['soc_sub_maj_grp_id']}{$value['soc_min_grp_id']}";
             $new['unit_groupId'] = "{$value['soc_maj_grp_id']}{$value['soc_sub_maj_grp_id']}{$value['soc_min_grp_id']}{$value['soc_unit_id']}";
             $new['name'] = $value['soc_name'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if ($new['major_groupId'] != '0') {
                 if (!$this->insert($new)) {
                    $this->_error .= "id: {$new['id']} name: {$new['name']}<br />";
                    $success = false;
                }
             }
         }
         $new['id'] = "10";
         $new['major_groupId'] = "10";
         $new['sub_major_groupId'] = "1010";
         $new['minor_groupId'] = "101010";
         $new['unit_groupId'] = "10101010";
         $new['name'] = 'Unmapped';
         $new['dateCreated'] = date('Y-m-d H:i:s');
         $new['creatorId'] = $this->_objUser->userId();
         $this->insert($new);
         return $success;
     }

     /**
      * Method to import wage SOC name data
      *
      * @return true|false
      */
     function importWageSocName() {
         $this->_changeTable('wage_soc_name', &$this->_awardDB);
         $values = $this->getAll("ORDER BY wage_id");
         //$this->_changeTable('tbl_lrs_job_codes');
         //$rec = $this->getRow("description",'unknown');
         //$unknownJobId = $rec['id'];
         $this->_changeTable('tbl_award_wage_socname');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['wage_id'];
             $new['socnameid'] = ($value['soc_name_id'] == '0')? "10" : $value['soc_name_id'];
             $new['gradeid'] = $value['grade_id'];
             $new['jobcodeid'] = ($value['job_code_id'] == '0')? $unknownJobId : $value['job_code_id'];
             if ($new['id'] != '0') {
                 if (!$this->insert($new)) {
                    $this->_error .= "id: {$new['id']} name: {$new['socnameid']}<br />";
                    $success = false;
                }
             }
         }
         return $success;
     }

     /**
      * Method to import organisation branch data
      *
      * @return true|false
      */
     function importOrgBranch() {
         $this->_changeTable('party_branch', &$this->_awardDB);
         $values = $this->getAll("ORDER BY party_branch_id");
         $this->_changeTable('tbl_award_branch');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['party_branch_id'];
             $new['partyid'] = $value['party_id'];
             $new['districtid'] = ($value['district_id'] == '0')? 'init_1' : $value['district_id'];
             $new['name'] = $value['party_branch_name'];
             $new['telephone'] = $value['party_branch_tel'];
             $new['fax'] = $value['party_branch_fax'];
             $new['url'] = $value['party_branch_URL'];
             $new['email'] = $value['party_branch_email'];
             $new['addressline1'] = $value['party_branch_address_line1'];
             $new['addressline2'] = $value['party_branch_address_line2'];
             $new['postalline1'] = $value['party_branch_postal_line1'];
             $new['postaltown'] = $value['party_branch_postal_town'];
             $new['postalcode'] = $value['party_branch_postal_code'];
             if (!$this->insert($new)) {
                $this->_error .= "id: {$new['id']} name: {$new['partyid']}<br />";
                $success = false;
             }
         }
         return $success;
     }

     /**
      * Method to import organisation branch/unit data
      *
      * @return true|false
      */
     function importOrgUnitBranch() {
         $this->_changeTable('barg_unit_party_branch', &$this->_awardDB);
         $values = $this->getAll("ORDER BY party_branch_id");
         $this->_changeTable('tbl_award_unit_branch');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             //$new['id'] = $value['id'];
             $new['branchid'] = $value['party_branch_id'];
             $new['unitid'] = $value['barg_unit_id'];
             if (!$this->insert($new)) {
                $this->_error .= "id: {$new['branchid']} name: {$new['unitid']}<br />";
                $success = false;
             }
         }
         return $success;
     }

     /**
      * Method to import SIC division data
      *
      * @return true|false
      */
     function importSicDiv() {
         $this->_changeTable('sic_div', &$this->_awardDB);
         $values = $this->getAll("ORDER BY sic_div_id");
         $this->_changeTable('tbl_lrs_sic_div');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['sic_div_id'];
             $new['major_divId'] = $value['sic_major_div_id'];
             $new['description'] = $value['sic_div_desc'];
             $new['code'] = $value['sic_div_code'];
             $new['notes'] = $value['sic_div_notes'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                $this->_error .= "id: {$new['id']} name: {$new['description']}<br />";
                $success = false;
             }
         }
         $new['id'] = 'init_0';
         $new['major_divId'] = "init_0";
         $new['description'] = "Unknown";
         $new['code'] = 0;
         $new['notes'] = "";
         $new['dateCreated'] = date('Y-m-d H:i:s');
         $new['creatorId'] = $this->_objUser->userId();
         $this->insert($new);
         return $success;
     }

     /**
      * Method to import SIC major group data
      *
      * @return true|false
      */
     function importSicMajorGroup() {
         $this->_changeTable('sic_major_group', &$this->_awardDB);
         $values = $this->getAll("ORDER BY sic_major_group_id");
         $this->_changeTable('tbl_lrs_sic_major_group');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['sic_major_group_id'];
             $new['divId'] = $value['sic_div_id'];
             $new['description'] = $value['sic_major_group_desc'];
             $new['code'] = $value['sic_major_group_code'];
             $new['notes'] = $value['sic_major_group_notes'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                $this->_error .= "id: {$new['id']} name: {$new['description']}<br />";
                $success = false;
             }
         }
         $new['id'] = "init_0";
         $new['divId'] = "init_0";
         $new['description'] = "Unknown";
         $new['code'] = 0;
         $new['notes'] = "";
         $new['dateCreated'] = date('Y-m-d H:i:s');
         $new['creatorId'] = $this->_objUser->userId();
         $this->insert($new);
         return $success;
     }

     /**
      * Method to import SIC group data
      *
      * @return true|false
      */
     function importSicGroup() {
         $this->_changeTable('sic_group', &$this->_awardDB);
         $values = $this->getAll("ORDER BY sic_group_id");
         $this->_changeTable('tbl_lrs_sic_group');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['sic_group_id'];
             $new['major_groupId'] = $value['sic_major_group_id'];
             $new['description'] = $value['sic_group_desc'];
             $new['code'] = $value['sic_group_code'];
             $new['notes'] = $value['sic_group_notes'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                $this->_error .= "id: {$new['id']} name: {$new['description']}<br />";
                $success = false;
             }
         }
         $new['id'] = "init_0";
         $new['major_groupId'] = "init_0";
         $new['description'] = "Unknown";
         $new['code'] = 0;
         $new['notes'] = "";
         $new['dateCreated'] = date('Y-m-d H:i:s');
         $new['creatorId'] = $this->_objUser->userId();
         $this->insert($new);
         return $success;
     }

     /**
      * Method to import SIC sub group data
      *
      * @return true|false
      */
     function importSicSubGroup() {
         $this->_changeTable('sic_sub_group', &$this->_awardDB);
         $values = $this->getAll("ORDER BY sic_sub_group_id");
         $this->_changeTable('tbl_lrs_sic_sub_group');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['sic_sub_group_id'];
             $new['groupId'] = $value['sic_group_id'];
             $new['description'] = $value['sic_sub_group_desc'];
             $new['code'] = $value['sic_sub_group_code'];
             $new['notes'] = $value['sic_sub_group_notes'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                $this->_error .= "id: {$new['id']} name: {$new['description']}<br />";
                $success = false;
             }
         }
         $new['id'] = "init_0";
         $new['groupId'] = "init_0";
         $new['description'] = "Unknown";
         $new['code'] = 0;
         $new['notes'] = "";
         $new['dateCreated'] = date('Y-m-d H:i:s');
         $new['creatorId'] = $this->_objUser->userId();
         $this->insert($new);
         return $success;
     }

     /**
      * Method to import old SIC data
      *
      * @return true|false
      */
     function importSicOld() {
         $this->_changeTable('sic_old', &$this->_awardDB);
         $values = $this->getAll("ORDER BY sic_old_id");
         $this->_changeTable('tbl_lrs_sic_old');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['id'] = $value['sic_old_id'];
             $new['major_divId'] = $value['sic_major_div_id'];
             $new['divId'] = $value['sic_div_id'];
             $new['major_groupId'] = $value['sic_major_group_id'];
             $new['groupId'] = $value['sic_group_id'];
             $new['sub_groupId'] = $value['sic_sub_group_id'];
             $new['dateCreated'] = date('Y-m-d H:i:s');
             $new['creatorId'] = $this->_objUser->userId();
             if (!$this->insert($new)) {
                $this->_error .= "id: {$new['id']} major_divId: {$new['major_divId']}<br />";
                $success = false;
             }
         }
         return $success;
     }


     /**
      * Method to import Party SIC data
      *
      * @return true|false
      */
     function importPartySic() {
         $this->_changeTable('party_sic', &$this->_awardDB);
         $values = $this->getAll("ORDER BY party_id");
         $this->_changeTable('tbl_award_unit_sic');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['unitid'] = $value['party_id'];
             $new['major_divid'] = ($value['sic_major_div_id'] == '0')? "init_0" : $value['sic_major_div_id'];
             $new['divid'] = ($value['sic_div_id'] == '0')? "init_0" : $value['sic_div_id'];
             $new['major_groupid'] = ($value['sic_major_group_id'] == '0')? "init_0" : $value['sic_major_group_id'];
             $new['groupid'] = ($value['sic_group_id'] == '0')? "init_0" : $value['sic_group_id'];
             $new['sub_groupid'] = ($value['sic_sub_group_id'] == '0')? "init_0" : $value['sic_sub_group_id'];
             if (!$this->insert($new)) {
                $this->_error .= "id: {$new['id']} unit: {$new['unitid']}<br />";
                $success = false;
             }
         }
         return $success;
     }

     /**
      * Method to import Party SIC data
      *
      * @return true|false
      */
     function importBargUnitSic() {
         $this->_changeTable('barg_unit_sic', &$this->_awardDB);
         $values = $this->getAll("ORDER BY barg_unit_id");
         $this->_changeTable('tbl_lrs_barg_unit_sic');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['unitid'] = $value['barg_unit_id'];
             $new['major_divid'] = ($value['sic_major_div_id'] == '0')? "init_0" : $value['sic_major_div_id'];
             $new['divid'] = ($value['sic_div_id'] == '0')? "init_0" : $value['sic_div_id'];
             $new['major_groupid'] = ($value['sic_major_group_id'] == '0')? "init_0" : $value['sic_major_group_id'];
             $new['groupid'] = ($value['sic_group_id'] == '0')? "init_0" : $value['sic_group_id'];
             $new['sub_groupid'] = ($value['sic_sub_group_id'] == '0')? "init_0" : $value['sic_sub_group_id'];
             if (!$this->insert($new)) {
                $this->_error .= "id: {$new['id']} unit: {$new['unitid']}<br />";
                $success = false;
             }
         }
         return $success;
     }

     /**
      * Method to import Agreement allowance data
      *
      * @return true|false
      */
     function importAgreeAllowance() {
         $this->_changeTable('agree_allowance_type', &$this->_awardDB);
         $values = $this->getAll("ORDER BY agree_id");
         $this->_changeTable('tbl_award_benefits');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
         	if ($value['allowance_type_id'] == 7) {
            	$new['agreeid'] = $value['agree_id'];
             	$new['nameid'] = "init_10";
             	$new['value'] = $value['agree_allowance_type_value'];
             	$new['notes'] = $value['agree_allowance_type_notes'];
             	if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                	if (!$this->insert($new)) {
                    	$this->_error .= "nameid: {$new['nameid']} agree: {$new['agreeid']}<br />";
                     	$success = false;
                 	}
             	}
         	}
         	else if ($value['allowance_type_id'] == 8) {
            	$new['agreeid'] = $value['agree_id'];
             	$new['nameid'] = "init_11";
             	$new['value'] = $value['agree_allowance_type_value'];
             	$new['notes'] = $value['agree_allowance_type_notes'];
             	if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                	if (!$this->insert($new)) {
                    	$this->_error .= "id: {$new['nameid']} agree: {$new['agreeid']}<br />";
                     	$success = false;
                 	}
             	}
         	}
         }
         return $success;
     }

      /**
      * Method to import Agreement childcare data
      *
      * @return true|false
      */
     function importAgreeChildcare() {
         $this->_changeTable('agree_childcare_type', &$this->_awardDB);
         $values = $this->getAll("ORDER BY agree_id");
         $this->_changeTable('tbl_award_benefits');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['agreeid'] = $value['agree_id'];
             $new['nameid'] = "init_23";
             $new['value'] = $value['agree_childcare_type_value'];
             $new['notes'] = $value['agree_childcare_type_notes'];
             if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                 if (!$this->insert($new)) {
                    $this->_error .= "id: {$new['id']} agree: {$new['agreeid']}<br />";
                    $success = false;
                 }
             }
         }
         return $success;
     }

      /**
      * Method to import Agreement hour data
      *
      * @return true|false
      */
     function importAgreeHour() {
         $this->_changeTable('agree_hour_type', &$this->_awardDB);
         $values = $this->getAll("ORDER BY agree_id");
         $this->_changeTable('tbl_award_benefits');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             $new['agreeid'] = $value['agree_id'];
             $new['nameid'] = "init_7";
             $new['value'] = $value['agree_hour_type_hours'];
             $new['notes'] = $value['agree_hour_type_notes'];
             if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                 if (!$this->insert($new)) {
                    $this->_error .= "id: {$new['nameid']} agree: {$new['agreeid']}<br />";
                    $success = false;
                 }
             }
         }
         return $success;
     }

      /**
      * Method to import Agreement leave data
      *
      * @return true|false
      */
     function importAgreeLeave() {
         $this->_changeTable('agree_leave_type', &$this->_awardDB);
         $values = $this->getAll("ORDER BY agree_id");
         $this->_changeTable('tbl_award_benefits');
         $success = true;
         $this->_error = 'Failed to insert row(s):<br />';
         foreach ($values as $value) {
             if ($value['leave_type_id'] == 1) {
             	$new['agreeid'] = $value['agree_id'];
             	$new['nameid'] = "init_8";
             	$new['value'] = $value['agree_leave_type_days'];
             	$new['notes'] = $value['agree_leave_type_notes'];
             	if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                 	if (!$this->insert($new)) {
                    	$this->_error .= "id: {$new['nameid']} agree: {$new['agreeid']}<br />";
                    	$success = false;
                 	}
             	}
         	}
         	else if ($value['leave_type_id'] == 3) {
             	$new['agreeid'] = $value['agree_id'];
             	$new['nameid'] = "init_27";
             	$new['value'] = $value['agree_leave_type_days'];
             	$new['notes'] = $value['agree_leave_type_notes'];
             	if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                 	if (!$this->insert($new)) {
                    	$this->_error .= "id: {$new['nameid']} agree: {$new['agreeid']}<br />";
                    	$success = false;
                 	}
             	}
         	}else if ($value['leave_type_id'] == 4) {
             	$new['agreeid'] = $value['agree_id'];
             	$new['nameid'] = "init_13";
             	$new['value'] = $value['agree_leave_type_days'];
             	$new['notes'] = $value['agree_leave_type_notes'];
             	if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                 	if (!$this->insert($new)) {
                    	$this->_error .= "id: {$new['nameid']} agree: {$new['agreeid']}<br />";
                    	$success = false;
                 	}
             	}
         	}else if ($value['leave_type_id'] == 5) {
             	$new['agreeid'] = $value['agree_id'];
             	$new['nameid'] = "init_14";
             	$new['value'] = $value['agree_leave_type_days'];
             	$new['notes'] = $value['agree_leave_type_notes'];
             	if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                 	if (!$this->insert($new)) {
                    	$this->_error .= "id: {$new['nameid']} agree: {$new['agreeid']}<br />";
                    	$success = false;
                 	}
             	}
         	}else if ($value['leave_type_id'] == 6) {
             	$new['agreeid'] = $value['agree_id'];
             	$new['nameid'] = "init_26";
             	$new['value'] = $value['agree_leave_type_days'];
             	$new['notes'] = $value['agree_leave_type_notes'];
             	if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                 	if (!$this->insert($new)) {
                    	$this->_error .= "id: {$new['nameid']} agree: {$new['agreeid']}<br />";
                    	$success = false;
                 	}
             	}
         	}else if ($value['leave_type_id'] == 7) {
             	$new['agreeid'] = $value['agree_id'];
             	$new['nameid'] = "init_24";
             	$new['value'] = $value['agree_leave_type_days'];
             	$new['notes'] = $value['agree_leave_type_notes'];
             	if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                 	if (!$this->insert($new)) {
                    	$this->_error .= "id: {$new['nameid']} agree: {$new['agreeid']}<br />";
                    	$success = false;
                 	}
             	}
         	}else if ($value['leave_type_id'] == 8) {
             	$new['agreeid'] = $value['agree_id'];
             	$new['nameid'] = "init_37";
             	$new['value'] = $value['agree_leave_type_days'];
             	$new['notes'] = $value['agree_leave_type_notes'];
             	if ($this->_objDBAgree->valueExists('id',$new['agreeid'])) {
                 	if (!$this->insert($new)) {
                    	$this->_error .= "id: {$new['nameid']} agree: {$new['agreeid']}<br />";
                    	$success = false;
                 	}
             	}
         	}
         }
         return $success;
     }

}
?>