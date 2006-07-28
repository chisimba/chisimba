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
	 * KinkyII init function
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
    					$desc = $objXml->xpath("//UPDATE[VERSION='{$codeVersion}']/DESCRIPTION");
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
    * This method reads a register.conf file
    * And returns the module version number
    * @param string $module id
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