<?php


/**
 * sysconfig for skins
 *
 * Chisimba skin system configuration manipulation class
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wesleynitsckie@gmail.com>
 * @copyright 2009 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class sysconfig_extjs_theme extends object {

    /**
     * Standard Constructor
     */
    public function init() {
		 $this->objConfig = $this->getObject('altconfig','config');
    }

    /**
     * Method to set the current default value
     *
     */
    public function setDefaultValue($value) {
        $this->defaultVaule = $value;
    }

    /**
     * Method to display the sysconfig interface
     *
     */
    public function show() {
        // Load the Radio button class
        $this->loadClass ( 'radio', 'htmlelements' );

        // Load the Skin Object
        //$objSkin = $this->getObject ( 'skin', 'skin' );

        $skinsList = $this->getListofThemes();//$objSkin->getListofSkins ();

        // Input MUST be called 'pvalue'
        $objElement = new radio ( 'pvalue' );

        $objElement->addOption ( "Blue", "Blue (default)" );
        foreach ( $skinsList as $element => $value ) {
            $objElement->addOption ( $element, $value );
        }

        // Set Default Selected
        $objElement->setSelected ( $this->defaultVaule );

        // Set radio buttons to be one per line
        $objElement->setBreakSpace ( '<br />' );

        // return finished radio button
        return $objElement->show ();
    }

    
     /**
    * Method to get the list of skins available
    * @return array List of available skins
    */
    public function getListofThemes()
    {
        $currentDir = getcwd();
        //loop through the folders and build an array of available skins
        $basedir=$this->objConfig->getsiteRootPath().$this->objConfig->getskinRoot()."_common/css/extjs/themes/";
        
        chdir($basedir);
        $dh=opendir($basedir);
        $dirList=array();
		while (false !== ($file = readdir($dh))) { #see http://www.php.net/manual/en/function.readdir.php
            if ($file != '.' && $file != '..' && strtolower($file)!='.svn') {

                if (is_dir($file)){

                    $skinnameFile=$basedir.$file.'/themename.txt';
                    $skinConfigFile=$basedir.$file.'/skin.conf';

                    if (file_exists($skinConfigFile)) {
                        $skinData = $this->readConf($skinConfigFile);
                        $dirList[$file] = $skinData['SKIN_NAME'];
                    } else if (file_exists($skinnameFile)) {
                        $ts=fopen($skinnameFile,'r');
                        $ts_content=fread($ts, filesize($skinnameFile));
                        $dirList[$file] = $ts_content;
                    } else {
                        $dirList[$file] = $file;
                    }

                }
            }
        }


        closedir($dh);
        chdir($currentDir);

        return $dirList;
    }
    
    /**
     * Method to run actions that need to occur once the parameter is updated
     *
     */
    public function postUpdateActions() {
        return NULL;
    }
}

?>