<?php

/**
* Class to handle system configuration properties. This replaces
* the functionality of the config object, but requires configuration
* changes in code.
*
* @author Paul Scott based on methods by Derek Keats
*/
require_once "config/global_vars_inc.php";

class config extends dbTable
{
    /**
    * Property to hold the user object
    *
    * @public object $objUser The user object
    */
    public $objUser;

    /**
    * Property to hold the language object
    *
    * @public object $objLanguage The language object
    */
    public $objLanguage;

    public $error_reporting;

    /**
    * Standard init function to set the database table and instantiate
    * common classes.
    */
    public function init()
    {
        // Set the database table for this class and set mirroring to
        // false since configuration paramaters should not be mirrored.
        parent::init('tbl_sysconfig_properties', false);
        //$this->objUser = $this->getObject('user', 'security');
    }

    /**
    * Method to insert a configuration parameter. It first checks if an
    * insert would produce a duplicate record using the private method
    * _checkForDuplicate.
    *
    * @public string $pmodule The module code of the module owning the config item
    * @public string $pname The name of the parameter being set, use UPPER_CASE
    * @public string $plabel A label for the config parameter, usually a language string
    * @public string $value The value of the config parameter
    * @public boolean $isAdminConfigurable TRUE | FALSE Whether the parameter is admin configurable or not
    */
    public function insertParam($pname, $pmodule, $pvalue)
    {
        // Check if an insert would produce a duplicate
        if (!$this->_checkForDuplicate($pname, $pmodule)) {
            $this->insert(array('pmodule' => $pmodule,
                    'pname' => $pname,
                    'pvalue' => $pvalue,
                    'creatorId' => '1',//$this->objUser->userId(),
                    'dateCreated' => date("Y/m/d H:i:s")));
            return true;
        } else {
            // Get an instance of the language object
            $this->objLanguage = &$this->getObject('language', 'language');
            die($this->objLanguage->languageText('mod_sysconfig_err_dupattempt'));
        } #if
    } #function insertParam

    /**
    * Method to change a configuration parameter
    *
    * @public string $module The module code of the module owning the config item
    * @public string $name The name of the parameter being set, use UPPER_CASE
    * @public string $label A label for the config parameter, usually a language string
    * @public string $value The value of the config parameter
    * @public boolean $isAdminConfigurable TRUE | FALSE Whether the parameter is admin configurable or not
    */
    public function changeParam($pname, $pmodule, $pvalue)
    {
        $id = $this->_lookUpId($pname, $pmodule);
        $rsArray = (array('pmodule' => $pmodule,
                'pname' => $pname,
                'pvalue' => $pvalue));
        $this->update("id", $id, $rsArray);
    }

    /**
    * Method to read a configuration parameter. This is the preferred
    * method for routine lookups.
    *
    * @public string $module The module code of the module owning the config item
    * @public string $name The name of the parameter being set, use UPPER_CASE
    * @return only the value of the parameter
    */
    public function getValue($pname, $pmodule = "_site_")
    {
        if (!isset($this->$pname)) {
            $this->setProperties($pmodule);
        }
        if (isset($this->$pname)) {
            return $this->$pname;
        } else if ( defined( $pname ) ) {
            $defValue = constant( $pname );
            $this->insert(array('pmodule' => '_site_',
                    'pname' => $pname,
                    'pvalue' => $defValue,
                    'creatorId' => NULL,
                    'dateCreated' => date("Y/m/d H:i:s")));
            return $defValue;
        } else {
            return NULL;
        }
    } #function getValue

    /**
    * Method to read a configuration parameter and associated data. Use this
    * method only if you need all the extra data.
    *
    * @public string $module The module code of the module owning the config item
    * @public string $name The name of the parameter being set, use UPPER_CASE
    * @return An array of name, value, creatorId, dateAdded, modifierId, dateModified
    */
    public function getValueFull($pname, $pmodule = "_site_")
    {
        $where = " WHERE pmodule='$pmodule' AND pname='$pname' ";
        return $this->getAll($where);
    }

    /**
    * Set properties of the configuration object corresponding to all the
    * config parameters for $module.
    *
    * @param string $module The module for which to set the properties
    */
    public function setProperties($pmodule)
    {
        $where = " WHERE pmodule='$pmodule' ";
        $ar = $this->getAll($where);
        if (!count($ar) >= 1) {
            return false;
        } else {
            foreach ($ar as $line) {
                $pname = $line['pname'];
                $pvalue = $line['pvalue'];
                $this->$pname = $pvalue;
            } #foreach
        } #if
        return true; //$ar;
    } #function setProperties

    /**
    * Get properties of the configuration object corresponding to all the
    * config parameters for $module.
    *
    * @param string $module The module for which to set the properties
    */
    public function getProperties($pmodule)
    {
        $where = " WHERE pmodule='$pmodule' ";
        $ar = $this->getAll($where);
        return $ar;
    } #function setProperties

    /**
    * Method to delete a configuration parameter
    *
    * @public string $module The module code of the module owning the config item
    * @public string $name The name of the parameter being set, use UPPER_CASE
    */
    public function deleteValue($pname, $pmodule = "_site_")
    {
        $id = $this->_lookUpId($pname, $pmodule);
        $this->delete('id', $id);
    } #function deleteValue

    /**
    *
    * Method to remove all entries for a particular module.
    * Note: this module is allowed to execute a delete statement using
    *  the dbTable query method because the config data has mirroring
    *  turned off. In a normal module, this would violate the capability
    *  for mirroring. Rather it would be necessary to do a SELECT first
    *  and do individual deletes on the returned id fields. This should
    *  only be used when unregistering a module, and NEVER be used anywhere
    *  else.
    *
    * @public string $module The module to delete from
    *
    */
    public function deleteModuleValues($pmodule)
    {
        $sql = "DELETE FROM tbl_sysconfig_properties WHERE pmodule='$pmodule'";
        if ($this->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Method to check if a configuration parameter is set
    *
    * @public string $module The module code of the module owning the config item
    * @public string $name The name of the parameter being set
    */
    public function checkIfSet($pname, $pmodule = "_site_")
    {
        $where = " WHERE pmodule='$pmodule' AND pname='$pname'";
        if ($this->getRecordCount($where) >= 1) {
            return true;
        } else {
            return false;
        } #if
    } #function checkIfSet
    /**
    * Method to register a module by accepting the module name
    * and an array of parameter, value pairs.
    *
    * @param string $module The module code of the module
    * @param array $ar The array of parameter, value pairs
    */
    public function registerModuleParams($pmodule, $ar)
    {
        // Loop through the array, and register each one
        foreach ($ar as $line) {
            $pname = $line['pname'];
            $pvalue = $line['pvalue'];
            $this->insertParam($pname, $pmodule, $pvalue);
        } #foreach
    } #function registerModuleParams
    /**
    * Save method to save the results of a single addition
    */
    public function saveSingle($mode = "edit")
    {
        $pmodule = TRIM($_POST['pmodule']);
        $pname = TRIM($_POST['pname']);
        $pvalue = TRIM($_POST['pvalue']);
        if ($mode == "add") {
            $this->insertParam($pname, $pmodule, $pvalue);
        } else {
            $id = TRIM($_POST['id']);
            // Get an instance of the user object
            $this->objUser = &$this->getObject('user', 'security');
            $modifierId = $this->objUser->userId();
            $dateModified = date("Y/m/d H:i:s");
            $this->update("id", $id, array('pname' => $pname,
                    'pvalue' => $pvalue,
                    'modifierId' => $modifierId,
                    'dateModified' => $dateModified));
        } #if
    } #function saveSingle
    /*------------------ PRIVATE METHODS BELOW LINE ------------------------*/

    /**
    * Method to get the config file as a string
    *
    * @public string $name The name of the parameter being looked up
    * @public string $module The module code of the module owning the config item
    */
    private function _checkForDuplicate($name, $module = "_site_")
    {
        $where = " WHERE pmodule='$module' AND pname='$name' ";
        if ($this->getRecordCount($where) >= 1) {
            return true;
        } else {
            return false;
        } #if
    } #function _checkForDuplicate

    /**
    * Method to get the id field for a module/name combination
    *
    * @public string $name The name of the parameter being looked up
    * @public string $module The module code of the module owning the config item
    */
    private function _lookUpId($name, $module = "_site_")
    {
        $where = " WHERE pmodule='$module' AND pname='$name' ";
        $ar = $this->getAll($where);
        if (count($ar) > 0) {
            return $ar['0']['id'];
        } else {
            // Get an instance of the language object
            $this->objLanguage = &$this->getObject('language', 'language');
            die($this->objLanguage->languageText("mod_sysconfig_err_keynotexist"));
        }
    } #function _lookUpId
    /*------------------ SUBSTITUTION FOR OLD METHODS BELOW LINE -----------------------------*/

    /**
    * The name of the website
    *
    * @return the name of the site as string
    */
    public function siteName()
    {
        //return $this->getValue("sitename");
        return $this->getValue("KEWL_SITENAME");
        // KEWL_SITENAME;
    }

    /**
    * The short name of the website
    *
    * @return the short name of the site as string
    */
    public function institutionShortName()
    {
        //return $this->getValue("institution_shortname");
        return $this->getValue("KEWL_INSTITUTION_SHORTNAME");
        // KEWL_INSTITUTION_SHORTNAME;
    }

    /**
    * The name of the institution
    *
    * @return the short name of the institution as string
    */
    public function institutionName()
    {
        //return $this->getValue("institution_name");
        return $this->getValue("KEWL_INSTITUION_NAME");
        // KEWL_INSTITUTION_NAME;
    }

    /**
    * The email address of the website
    *
    * @return the email address for the site as string
    */
    public function siteEmail()
    {
        //return $this->getValue("site_email");
        return $this->getValue("KEWL_SITEEMAIL");
        // KEWL_SITEEMAIL;
    }

    /**
    * The script timeout
    *
    * @return the script timout in seconds
    */
    public function systemTimeout()
    {
        //return $this->getValue("system_timeout");
        return $this->getValue("KEWL_SYSTEMTIMEOUT");
        // KEWL_SYSTEMTIMEOUT;
    }

    /**
    * The URL root of the site
    *
    * @return the the site root, normally / as string
    */
    public function siteRoot()
    {
        //return $this->getValue("site_root");
        return $this->getValue("KEWL_SITE_ROOT");
        // KEWL_SITE_ROOT;
    }

    /**
    * The URL location of any generic icons used across skins
    *
    * @return the icon lication, normally /icons/ including
    * leading and trailing forward slash (/) as string
    *//*DEPRECATED
    function defaultIconFolder() {
        return getValue("default_iconfolder")
        //KEWL_DEFAULTICONFOLDER;
    }*/

    /**
    * The URL location of skins
    *
    * @return the skin location, normally /skins/ including
    * leading and trailing forward slash (/) as string
    */
    public function skinRoot()
    {
        //return $this->getValue("skin_root");
        return $this->getValue("KEWL_SKIN_ROOT");
        // KEWL_SKIN_ROOT;
    }

    /**
    * The folder name of the default skin
    *
    * @return the default skin name (normally default)
    * leading and trailing forward slash (/)  as string
    */
    public function defaultSkin()
    {
        //return $this->getValue("default_skin");
        return $this->getValue("KEWL_DEFAULT_SKIN");
        // KEWL_DEFAULT_SKIN;
    }

    /**
    * The name of the default language (normally english)
    *
    * @return the name of the default language as string
    */
    public function defaultLanguage()
    {
        //return $this->getValue("default_language");
        return $this->getValue("KEWL_DEFAULT_LANGUAGE");
        // KEWL_DEFAULT_LANGUAGE;
    }

    /**
    * The abbreviation of the default language (normally EN)
    *
    * @return the abbreviation of the default language as string
    */
    public function defaultLanguageAbbrev()
    {
        //return $this->getValue("default_language_abbrev");
        return $this->getValue("KEWL_DEFAULT_LANGUAGE_ABBREV");
        // KEWL_DEFAULT_LANGUAGE_ABBREV;
    }

    /**
    * The default extension for banners (jpg, gif, png)
    *
    * @return default extension for banners (jpg, gif, png) as string
    */
    public function bannerExtension()
    {
        //return $this->getValue("banner_extension");
        return $this->getValue("KEWL_BANNER_EXT");
        // KEWL_BANNER_EXT;
    }

    /**
    * The default site root path as string
    *
    * @return default site root path as string
    */
    public function siteRootPath()
    {
        //return $this->getValue("siteroot_path");
        return $this->getValue("KEWL_SITEROOT_PATH");
        // KEWL_SITEROOT_PATH;
    }

    /**
    *
    * @DEPRECATED The default site template path as string
    * @return default site template path as string
    */
    public function templatePath()
    {
        //return $this->getValue("template_path");
        return $this->getValue("KEWL_TEMPLATE_PATH");
        // KEWL_TEMPLATE_PATH;
    }

    /**
    *
    * @DEPRECATED The default site login template name as string
    * @return string The name of the default login template
    */
    public function loginTemplate()
    {
        //return $this->getValue("login_template");
        return $this->getValue("KEWL_LOGIN_TEMPLATE");
        // KEWL_LOGIN_TEMPLATE;
    }

    /**
    *
    * @DEPRECATED The default logged in template name as string
    * @return string The name of the default logged in template
    */
    public function loggedInTemplate()
    {
        //return $this->getValue("logged_in_template");
        return $this->getValue("KEWL_LOGGED_IN_TEMPLATE");
        // KEWL_LOGGED_IN_TEMPLATE;
    }

    /**
    *
    * The default layout template name as string
    * @return string Name of default layout template
    */
    public function defaultLayoutTemplate()
    {
        //return $this->getValue("default_layout_template");
        return $this->getValue("KEWL_DEFAULT_LAYOUT_TEMPLATE");
        // KEWL_DEFAULT_LAYOUT_TEMPLATE;
    }

    /**
    * The default Page template name as string
    * @return string Name of default page template
    */
    public function defaultPageTemplate()
    {

        // Check if Parameter is set
        if ($this->checkIfSet('KEWL_DEFAULT_PAGE_TEMPLATE')) {
            // Get Value if it is set
            $template = $this->getValue('KEWL_DEFAULT_PAGE_TEMPLATE');

            // Prevent system from using KEWL_DEFAULT_PAGE_TEMPLATE as a default value
            if (strtoupper($template) == 'KEWL_DEFAULT_PAGE_TEMPLATE') {
                // change to default
                $this->changeParam('KEWL_DEFAULT_PAGE_TEMPLATE', '_site_', 'default_page_tpl.php');
                $this->setProperties('_site_'); // Refresh Properties
                return $this->getValue('KEWL_DEFAULT_PAGE_TEMPLATE');
            } else { // return given template
                return $template;
            }
        } else {
            // Insert Parameter if not set
            $this->insertParam('KEWL_DEFAULT_PAGE_TEMPLATE', '_site_', 'default_page_tpl.php');
            // Return template
            return $this->getValue('KEWL_DEFAULT_PAGE_TEMPLATE');
        }
    }

    /**
    * Whether to allow users to register themselves
    *
    * @return TRUE or FALSE
    */
    public function allowSelfRegister()
    {
        //return $this->getValue("allow_selfregister");
        return $this->getValue("KEWL_ALLOW_SELFREGISTER");
        // KEWL_ALLOW_SELFREGISTER;
    }

    /**
    * Returns name of post-login module
    */
    public function defaultModuleName()
    {
        //return $this->getValue("postlogin_module");
        return $this->getValue("KEWL_POSTLOGIN_MODULE");
        // KEWL_POSTLOGIN_MODULE;
    }

    /**
    * Returns whether LDAP functionality should be used
    */
    public function useLDAP()
    {
        if (function_exists("ldap_connect")){
            return $this->getValue("LDAP_USED");
        } else {
            return FALSE;
        }
    }

    /**
    * Returns the country 2-letter code
    * Defaults to 'ZA'
    * @returns string $code
    */
    public function getCountry()
    {
        $code=$this->getValue('KEWL_SERVERLOCATION');
        if ($code==NULL){
            $this->KEWL_SERVERLOCATION='ZA';
            $code='ZA';
        }
        return $code;
    }

    /**
    * Determines if the site is an "alumni" one
    * @returns TRUE|FALSE
    */
    public function isAlumni()
    {
        $systemType = $this->getValue("SYSTEM_TYPE", "contextabstract");
        if ($systemType=='alumni'){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    * *---------------- FILE SYSTEM PROPERTIES -----------*
    */

    /**
    * Returns the base path for all user files
    */
    public function userfiles()
    {
        //return $this->getValue("user_files");
        return $this->getValue("USER_FILES");
        // USER_FILES;
    }

    /**
    * Returns the base path for content files
    */
    public function contentBasePath()
    {
        //return $this->getValue("content_basepath");
        return $this->getValue("KEWL_CONTENT_BASEPATH");
        // KEWL_CONTENT_BASEPATH;
    }

    /**
    * Returns the path for content files
    */
    public function contentPath()
    {
        //return $this->getValue("content_path");
        return $this->getValue("KEWL_CONTENT_PATH");
        // KEWL_CONTENT_PATH;
    }

    /**
    * Returns the root path for content files
    */
    public function contentRoot()
    {
        //return $this->getValue("content_path");
        return $this->getValue("KEWL_CONTENT_ROOT");
        // KEWL_CONTENT_PATH;
    }

    public function error_reporting()
    {
        return $this->getValue("KEWL_ERROR_REPORTING");
    }

}
?>