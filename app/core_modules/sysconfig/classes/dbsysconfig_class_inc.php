<?php

/**
* Class to handle system configuration properties. This replaces
* the functionality of the config object, but requires configuration
* changes in code.
*
* @author Derek Keats
*/
class dbsysconfig extends dbTable
{
    /**
    * Property to hold the user object
    *
    * @var object $objUser The user object
    */
    var $objUser;

    /**
    * Property to hold the config properties object
    *
    * @var object $objConfig The config object
    */
    var $objConfig;

    /**
    * Standard init function to set the database table and instantiate
    * common classes.
    */
    function init()
    {
        // Set the database table for this class and set mirroring to
        // false since configuration paramaters should not be mirrored.
        parent::init('tbl_sysconfig_properties', false);
        // Get an instance of the user object
        $this->objUser = $this->getObject('user', 'security');
        // Get an instance of the language object
        $this->objConfig = $this->getObject('altconfig','config');
    }

    /**
    * Method to get the list of modules that have configuration parameters
    * as well the number of parameters they have
    * @return array List of Modules with Number of Parameters they have
    */
    function getModulesParamList()
    {
        $sql = 'SELECT pmodule, count( pmodule ) AS paramcount FROM tbl_sysconfig_properties GROUP BY pmodule ORDER BY pmodule';
        return $this->getArray($sql);
    }

    /**
    * Method to insert a configuration parameter. It first checks if an
    * insert would produce a duplicate record using the private method
    * _checkForDuplicate.
    *
    * DO NOT CHANGE THIS FUNCTION - IT IS USED BY MODULE ADMIN
    *
    * @var string $pmodule The module code of the module owning the config item
    * @var string $pname The name of the parameter being set, use UPPER_CASE
    * @var string $plabel A label for the config parameter, usually a language string
    * @var string $value The value of the config parameter
    * @var boolean $isAdminConfigurable TRUE | FALSE Whether the parameter is admin configurable or not
    */
    function insertParam($pname, $pmodule, $pvalue, $pdesc)
    {
        // Check if an insert would produce a duplicate
        if (!$this->_checkForDuplicate($pname, $pmodule)) {
            $this->insert(array('pmodule' => $pmodule,
                    'pname' => $pname,
                    'pvalue' => $pvalue,
                    'pdesc' => $pdesc,
                    'creatorId' => $this->objUser->userId(),
                    'dateCreated' => date("Y/m/d H:i:s")));
            return true;
        } else {
            $id = $this->_lookUpId($pname, $pmodule);
            // Bail out if this is an update, and the field was edited
            if (isset($this->updateFlag)){
                $line=$this->getRow('id',$id);
                if (($line['datemodified']!=NULL) || (($line['pvalue'] == $pvalue) && ($line['pdesc'] == $pdesc)) || ($line['modifierid']!=NULL) ){
                  //return true;
                  $pvalue = $line['pvalue'];
                }
            }

            $this->update('id',$id,array('pmodule' => $pmodule,
                    'pname' => $pname,
                    'pvalue' => $pvalue,
                    'pdesc' => $pdesc,
                    //'modifierId' => $this->objUser->userId(),
                    //'dateModified' => date("Y/m/d H:i:s")
                    ));
            return true;
        } #if
    } #function insertParam

    /**
    * Method to change a configuration parameter
    *
    * @var string $module The module code of the module owning the config item
    * @var string $name The name of the parameter being set, use UPPER_CASE
    * @var string $label A label for the config parameter, usually a language string
    * @var string $value The value of the config parameter
    * @var boolean $isAdminConfigurable TRUE | FALSE Whether the parameter is admin configurable or not
    */
    function changeParam($pname, $pmodule, $pvalue)
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
    * @var string $module The module code of the module owning the config item
    * @var string $name The name of the parameter being set, use UPPER_CASE
    * @return only the value of the parameter
    */
    function getValue($pname, $pmodule = "_site_")
    {
        if (!isset($this->$pname)) {
            $this->setProperties($pmodule);
        }
        if (isset($this->$pname)) {
            return $this->$pname;
        } else {
            return null;
        }
    } #function getValue

    /**
    * Method to read a configuration parameter and associated data. Use this
    * method only if you need all the extra data.
    *
    * @var string $module The module code of the module owning the config item
    * @var string $name The name of the parameter being set, use UPPER_CASE
    * @return An array of name, value, creatorId, dateAdded, modifierId, dateModified
    */
    function getValueFull($pname, $pmodule = "_site_")
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
    function setProperties($pmodule)
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
    function getProperties($pmodule)
    {
        $where = " WHERE pmodule='$pmodule' ";
        $ar = $this->getAll($where);
        return $ar;
    } #function setProperties

    /**
    * Method to delete a configuration parameter
    *
    * @var string $module The module code of the module owning the config item
    * @var string $name The name of the parameter being set, use UPPER_CASE
    */
    function deleteValue($pname, $pmodule = "_site_")
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
    * @var string $module The module to delete from
    *
    */
    function deleteModuleValues($pmodule)
    {
        if ($this->delete('pmodule',$pmodule)) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Method to check if a configuration parameter is set
    *
    * @var string $module The module code of the module owning the config item
    * @var string $name The name of the parameter being set
    */
    function checkIfSet($pname, $pmodule = "_site_")
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
    * DO NOT CHANGE THIS FUNCTION - IT IS USED BY MODULE ADMIN
    *
    * @param string $module The module code of the module
    * @param array $ar The array of parameter, value pairs
    */
    function registerModuleParams($pmodule, $ar)
    {
        // Loop through the array, and register each one
        foreach ($ar as $line) {
            $pname = $line['pname'];
            $pvalue = $line['pvalue'];
            $pdesc = $line['pdesc'];
            $this->insertParam($pname, $pmodule, $pvalue, $pdesc);
        } #foreach
    } #function registerModuleParams

    /**
    * Save method to update the results of a single record
    */
    function updateSingle()
    {
        $pvalue = TRIM($_POST['pvalue']);
        $id = TRIM($_POST['id']);
        $modifierId = $this->objUser->userId();
        $dateModified = date("Y/m/d H:i:s");
        $this->update("id", $id, array('pvalue' => $pvalue, 'modifierId' => $modifierId, 'dateModified' => $dateModified));
    } #function updateSingle


    /*------------------ PRIVATE METHODS BELOW LINE ------------------------*/

    /**
    * Method to get the config file as a string
    *
    * @var string $name The name of the parameter being looked up
    * @var string $module The module code of the module owning the config item
    */
    function _checkForDuplicate($name, $module = "_site_")
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
    * @var string $name The name of the parameter being looked up
    * @var string $module The module code of the module owning the config item
    */
    function _lookUpId($name, $module = "_site_")
    {
        $where = " WHERE pmodule='$module' AND pname='$name' ";
        $ar = $this->getAll($where);
        if (count($ar) > 0) {
            return $ar['0']['id'];
        } else {
            //die($this->objLanguage->languageText("mod_sysconfig_err_keynotexist",'sysconfig'));
            return false;
        }
    } #function _lookUpId

    /*------------------ SUBSTITUTION FOR OLD METHODS BELOW LINE -----------------------------*/

    /**
    * The name of the website
    *
    * @return the name of the site as string
    */
    function siteName()
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
    function institutionShortName()
    {
        //return $this->getValue("institution_shortname");
        return $this->getValue("KEWL_INSTITUTION_SHORTNAME");
        // KEWL_INSTITUTION_SHORTNAME;
    }

    /**
    * The email address of the website
    *
    * @return the short name of the institution as string
    */
    function institutionName()
    {
        //return $this->getValue("institution_name");
        return $this->getValue("KEWL_INSTITUTION_NAME");
        // KEWL_INSTITUTION_NAME;
    }

    /**
    * The name of the institution
    *
    * @return the email address for the site as string
    */
    function siteEmail()
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
    function systemTimeout()
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
    function siteRoot()
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
    function skinRoot()
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
    function defaultSkin()
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
    function defaultLanguage()
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
    function defaultLanguageAbbrev()
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
    function bannerExtension()
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
    function siteRootPath()
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
    function templatePath()
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
    function loginTemplate()
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
    function loggedInTemplate()
    {
        //return $this->getValue("logged_in_template");
        return $this->getValue("KEWL_LOGGED_IN_TEMPLATE");
        // KEWL_LOGGED_IN_TEMPLATE;
    }

    /**
    *
    * @DEPRECATED The default layout template name as string
    * @return string Name of default layout template
    */
    function defaultLayoutTemplate()
    {
        //return $this->getValue("default_layout_template");
        return $this->getValue("KEWL_DEFAULT_LAYOUT_TEMPLATE");
        // KEWL_DEFAULT_LAYOUT_TEMPLATE;
    }

    /**
    * Whether to allow users to register themselves
    *
    * @return TRUE or FALSE
    */
    function allowSelfRegister()
    {
        //return $this->getValue("allow_selfregister");
        return $this->getValue("KEWL_ALLOW_SELFREGISTER");
        // KEWL_ALLOW_SELFREGISTER;
    }

    /**
    * Returns name of post-login module
    */
    function defaultModuleName()
    {
        //return $this->getValue("postlogin_module");
        return $this->getValue("KEWL_POSTLOGIN_MODULE");
        // KEWL_POSTLOGIN_MODULE;
    }

    /**
    * Returns whether LDAP functionality should be used
    */
    function useLDAP()
    {
        return $this->getValue("ldap_used");;
    }

    /**
    * *---------------- FILE SYSTEM PROPERTIES -----------*
    */

    /**
    * Returns the base path for all user files
    */
    function userfiles()
    {
        //return $this->getValue("user_files");
        return $this->getValue("USER_FILES");
        // USER_FILES;
    }

    /**
    * Returns the base path for content files
    */
    function contentBasePath()
    {
        //return $this->getValue("content_basepath");
        return $this->getValue("KEWL_CONTENT_BASEPATH");
        // KEWL_CONTENT_BASEPATH;
    }

    /**
    * Returns the base url for content files
    */
    function contentBaseURL()
    {
        //return $this->getValue("content_url");
        return $this->getValue("KEWL_CONTEN_URL");
        // KEWL_CONTENT_URL;
    }

    /**
    * *---------------- MIRRORING PROPERTIES -----------*
    */

    /**
    * Return's server name (used for dynamic mirroring)
    */
    function serverName()
    {
        //return $this->getValue("server_name");
        return $this->getValue("KEWL_SERVERNAME");
        // KEWL_SERVERNAME;
    }

    /**
    * Returns mirror webservice WSDL URL (in production will usually be a service
    * on a non-standard port on the localhost)
    *
    * @return string WSDL URL
    */
    function mirrorWsdlUrl()
    {
        return $this->getValue("mirror_wsdl_url");
    }

    /**
    * Method used to get the proxy server
    * @return string the proxy string in standard format (used for dynamic mirroring)
    */
    function proxyName()
    {
        return $this->getValue("KEWL_PROXY");
    }
} #dbconfig class

?>