<?
/**
* Class for reading the register.conf files.
* @author Nic Appleby
* @version $Id$
* @copyright 2004
* @category Chisimba
* @package modulecatalogue
* @license GNU GPL
*/

class filereader extends object
{

	public $objConfig;

	public function init ()
	{
		$this->objConfig=&$this->getObject('altconfig','config');
	}

	/**
    * Reads the 'register.conf' file provided by the module to be registered
    * and uses file() to load the contents into an array, then read through it
    * line by line, looking for keywords.
    * These are then returned as an associative array.
    * @author James Scoble
    * @param string $filepath  path and filename of file.
    * @param boolean $useDefine determine use of defined constants
    * @return array $registerdata all the info from the register.conf file
    */
	public function readRegisterFile($filepath,$useDefine=FALSE) {
		try {
			if (file_exists($filepath)) {
				$registerdata=array();
				$lines=file($filepath);
				foreach ($lines as $line) {
					$params=explode(':',$line);
					$len = count($params);
					for ($i=0; $i<$len; $i++) {
						$params[$i] = trim($params[$i]);
					}
					switch ($params[0]) {
						case 'MODULE_ID':
						case 'MODULE_NAME':
						case 'MODULE_DESCRIPTION':
						case 'MODULE_AUTHORS':
						case 'MODULE_RELEASEDATE':
						case 'MODULE_VERSION':
						case 'MODULE_PATH':
						case 'MODULE_ISADMIN':
						case 'MODULE_ISVISIBLE':
						case 'MODULE_HASADMINPAGE':
						case 'MODULE_LANGTERMS':
						case 'CONTEXT_AWARE':
						case 'DEPENDS_CONTEXT':
							$registerdata[$params[0]]=rtrim($params[1]);
							break;
						case 'ICON': 				//images for each module
						case 'NEWPAGE': 			//Add a new page
						case 'NEWPAGECATEGORY': 	//Add a new page category
						case 'NEWSIDEMENU': 		//Add a new sidemenu
						case 'NEWTOOLBARCATEGORY': 	//Add a new toolbar category
						case 'MENU_CATEGORY': 		//when the menu should display a link to this
						case 'SIDEMENU': 			//the side menus in the content page
						case 'PAGE': 				//lecturer or admin page links
						case 'SYSTEM_TYPE': 		//system type for text abstraction _Kevin Cyster
						case 'SYSTEM_TEXT': 		//text items for text abstraction _Kevin Cyster
						case 'ACL': 				//access permissions for the module
						case 'USE_GROUPS': 			//access groups for the module
						case 'USE_CONTEXT_GROUPS': 	//access groups for a context dependent module
						case 'USE_CONDITION': 		//use an existing security condition
						case 'CONDITION': 			//create a security condition
						case 'CONDITION_TYPE': 		//Create a condition type
						case 'RULE': 				//Create a rule linking conditions and actions
						case 'DIRECTORY': 			//Create a directory in content folder
						case 'SUBDIRECTORY': 		//Create a subdirectory in above directory
						case 'TABLE': 				//Names of SQL tables
						case 'BIGDATA': 			//data too large for the table-creation method
						case 'DEPENDS': 			//modules this module needs
						case 'CLASSES':
						case 'WARNING'; 			//Warning tag for modules with special requirements or functions
						case 'SOAP_CONTROLLER': 	//Boolean flag for SOAP controller
							$registerdata[$params[0]][]=rtrim($params[1]);
						break;
						case 'CONFIG': 				//configuration params
							if (isset($params[2])){
							$registerdata[$params[0]][]=array('pname'=>rtrim($params[1]),'pvalue'=>rtrim($params[2]));
						} else {
							$confArray=explode('|',$params[1]);
							$registerdata[$params[0]][]=array('pname'=>trim($confArray[0]),'pvalue'=>trim($confArray[1]));
						}
						break;
						case 'TEXT': 				//Languagetext items
						$registerdata['TEXT'][]=$params[1]; // Need to think this one out some more.
						break;
						case 'USES':
						case 'USESTEXT': 			//Languagetext items not loaded but used.
						$registerdata['USES'][]=$params[1];
						break;
						default:
					} //  end of switch()
				} //    end of foreach
				return ($registerdata);
			} else {
				return FALSE;
			} // end of if
		}
		catch (Exception $e) {
			echo customException::cleanUp($e->getMessage());
			exit(0);
		}
	} //end of function readRegisteFile
} //end of class definition
?>