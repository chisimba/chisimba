<?
/**
* Class which handles patches to modules
* @author Nic Appleby
* @copyright AVOIR
* @license GNU/GPL
* @category Chisimba
* @package modulecatalogue
* @version $Id$
*/

class patch extends dbtable {

	/**
	 * Configuration object
	 *
	 * @var object $objConfig
	 */
	public $objConfig;

	/**
	 * Object to find module register file
	 *
	 * @var object $objModFile
	 */
	protected $objModfile;

	/**
	 * Object to get language elements
	 *
	 * @var object $objLanguage
	 */
	public $objLanguage;

	/**
	 * Chisimba init function
	 *
	 */
	public function init() {
		try {
			parent::init('tbl_modules');
			$this->objConfig = &$this->getObject('altconfig','config');
			$this->objModFile = &$this->getObject('modulefile','modulecatalogue');
			$this->objLanguage = &$this->getObject('language','language');
		} catch (Exception $e) {
			echo customException::cleanUp($e->getMessage());
			exit(0);
		}
	}

	/**
    * This is a method to return an array of the registered modules
    * that have a more recent version in code than in the database
    * @returns array $modules
    */
	public function checkModules() {
		try {
			$modArray=$this->getAll();
			$modules=array();
			foreach ($modArray as $module) {
				$codeVersion = (float)$this->readVersion($module['module_id']);
				$dbVersion = (float)$module['module_version'];
				// Now compare the two
				//echo "{$module['module_id']} $dbVersion >= $codeVersion<br/>";
				if ($codeVersion>$dbVersion) {
					//check for xml document
					$description = $this->objLanguage->languageText('mod_modulecatalogue_newlangitems','modulecatalogue');
					if ($updateFile = $this->objModFile->findSqlXML($module['module_id'])) {
						if (!$objXml = simplexml_load_file($updateFile)) {
    						throw new Exception($this->objLanguage->languageText('mod_modulecatalogue_badxml').' '.$updateFile);
    					}
    					$desc = $objXml->xpath("//update[version='{$codeVersion}']/description");
    					$description = $desc[0];
    					//echo $desc[0]."<br/>";var_dump($desc);die();
					}
					$modules[]=array('module_id'=>$module['module_id'],'old_version'=>$dbVersion,'new_version'=>$codeVersion,'desc'=>$description);
				}
			}
			return $modules;
		} catch (Exception $e) {
			echo customException::cleanUp($e->getMessage());
			exit(0);
		}
	}

	/**
    * This method returns the version of a module in the database
    * ie: The version level of the emodule at the time it was registered.
    * @param string $module the module to lookup
    * @return string $version the version in the database
    */
	function getVersion($module) {
		try {
			$row = $this->getRow('module_id',$module);
			if (!is_array($row)) {
				return FALSE;
			}
			return $row['module_version'];
		} catch (Exception $e) {
			echo customException::cleanUp($e->getMessage());
			exit(0);
		}
	}

	/**
    * This method calls a function to read the XML file
    * and walks through it, processing each update
    * @param string $modname the name of the module
    * @param string $file optional 2nd param for non-standard location of file
    */
	function applyUpdates($modname) {
		try {
			// Find the updates file
			$this->objModule = &$this->getObject('modules','modulecatalogue');
			$this->objModuleAdmin = &$this->getObject('modulesadmin','modulecatalogue');
			$this->objModfile = &$this->getObject('modulefile','modulecatalogue');
			$data=array();
			$file="modules/$modname/sql/sql_updates.xml";
			// Apply the table changes
			$oldversion = (float)$this->getVersion($modname);
			$result = array();
			if (file_exists($file)){
				$objXml = simplexml_load_file($file);
				foreach ($objXml->update as $update) {
					$ver = (float)$update->version;
					$verStr = str_replace('.','_',$update->version);
					if ($ver>$oldversion) {
						foreach ($update->data as $data) {
							//$this->objModuleAdmin->alterTable($update->table,$data,false);
							foreach ($data as $opKey => $opValue) {
								$pData = array();
								switch ($opKey) {
									case 'name':
										$pData[$opKey] = (string)$opValue;
										break;
									case 'add':
										$name = (string)$opValue->name;
										$innerData = array();
										foreach ($opValue as $rowKey => $rowVal) {
											if ($rowKey != 'name') {
												$k = (string)$rowKey;
												$v = (string)$rowVal;
												$innerData[$k] = $v;
											}
										}
										$pData[$opKey] = array($name=>$innerData);
										break;
									case 'remove':
										$op = (string)$opKey;
										$strVal = (string)$opValue;
										$pData[$op] = array($strVal => array());
										break;
									case 'change':
										break;
									case 'rename':
										break;
									default:
										throw new customException('error in patch data');
										break;
								}

								print_r($pData);
								$patch = array('moduleid'=>$modname,'version'=>$ver,'tablename'=>$update->table,
								'patchdata'=>$pData,'applied'=>$this->objModule->now());
								//$this->objModule->insert($patch,'tbl_module_patches');
							}
						}
					}
				}
			}
			//update version info in db
			$regData = $this->objModfile->readRegisterFile($this->objModfile->findregisterfile($modname));
			//$this->objModuleAdmin->installModule($regData,TRUE);
			$result['current'] = $this->getVersion($modname);
			$result['old'] = $oldversion;
			// Now pass along the info to the template.
			return $result;
		} catch (Exception $e) {
			echo customException::cleanUp($e->getMessage());
			exit(0);
		}
	}

	/**
    * This method reads a register.conf file
    * And returns the module version number
    * @param string $module the module id
    * $returns string $version
    */
	private function readVersion($module) {
		try {
			//Check that the register file is there.
			if (!$regdata = file($this->objModFile->findRegisterFile($module))) {
				return FALSE;
			}
			// Now look up the version number from that file
			foreach  ($regdata as $line) {
				$array = explode(': ',$line);
				switch ($array[0]) {
					case 'MODULE_VERSION':
						return $array[1];
						break;
					default:
						break;
				}
			}
		} catch (Exception $e) {
			echo customException::cleanUp($e->getMessage());
			exit(0);
		}
	}
}
?>