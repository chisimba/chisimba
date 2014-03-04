<?php
/**
 * FileMaker PHP API.
 *
 * @package FileMaker
 *
 * Copyright � 2005, FileMaker, Inc.� All rights reserved.
 * NOTE:� Use of this source code is subject to the terms of the FileMaker
 * Software License which accompanies the code.� Your use of this source code
 * signifies your agreement to such license terms and conditions.� Except as
 * expressly granted in the Software License, no other copyright, patent, or
 * other intellectual property license or right is granted, either expressly or
 * by implication, by FileMaker.
 */

/**
 * Always load the error class and the implementation delegate.
 */
require_once dirname(__FILE__) . '/FileMaker/Error.php';
require_once dirname(__FILE__) . '/FileMaker/Implementation/FileMakerImpl.php';

/**
 * Find constants.
 */
define('FILEMAKER_FIND_LT', '<');
define('FILEMAKER_FIND_LTE', '<=');
define('FILEMAKER_FIND_GT', '>');
define('FILEMAKER_FIND_GTE', '>=');
define('FILEMAKER_FIND_RANGE', '...');
define('FILEMAKER_FIND_DUPLICATES', '!');
define('FILEMAKER_FIND_TODAY', '//');
define('FILEMAKER_FIND_INVALID_DATETIME', '?');
define('FILEMAKER_FIND_CHAR', '@');
define('FILEMAKER_FIND_DIGIT', '#');
define('FILEMAKER_FIND_CHAR_WILDCARD', '*');
define('FILEMAKER_FIND_LITERAL', '""');
define('FILEMAKER_FIND_RELAXED', '~');
define('FILEMAKER_FIND_FIELDMATCH', '==');

/**
 * Find logical operator constants.
 */
define('FILEMAKER_FIND_AND', 'and');
define('FILEMAKER_FIND_OR', 'or');

/**
 * Validation rule constants.
 */
define('FILEMAKER_RULE_NOTEMPTY', 1);
define('FILEMAKER_RULE_NUMERICONLY', 2);
define('FILEMAKER_RULE_MAXCHARACTERS', 3);
define('FILEMAKER_RULE_FOURDIGITYEAR', 4);
define('FILEMAKER_RULE_TIMEOFDAY', 5);
define('FILEMAKER_RULE_TIMESTAMP_FIELD', 6);
define('FILEMAKER_RULE_DATE_FIELD', 7);
define('FILEMAKER_RULE_TIME_FIELD', 8);

/**
 * Sort direction constants.
 */
define('FILEMAKER_SORT_ASCEND', 'ascend');
define('FILEMAKER_SORT_DESCEND', 'descend');

/**
 * Logging constants.
 */
define('FILEMAKER_LOG_ERR', 3);
define('FILEMAKER_LOG_INFO', 6);
define('FILEMAKER_LOG_DEBUG', 7);

/**
 * Base FileMaker class.
 *
 * @package FileMaker
 */
class FileMaker
{
    /**
     * Implementation. This is the object that actually implements the API.
     *
     * @var FileMaker_Implementation
     * @access private
     */
    var $_impl;

    /**
     * Test for whether or not a variable is a FileMaker API Error.
     *
     * @param mixed $variable
     * @return boolean Whether or not the variable is a FileMaker API Error.
     * @static
     *
     */
    function isError($variable)
    {
        return is_a($variable, 'FileMaker_Error');
    }

    /**
     * Returns the API version.
     *
     * @return string The API version.
     * @static
     */
    function getAPIVersion()
    {
        return FileMaker_Implementation::getAPIVersion();
    }

    /**
     * Return the minimum server version this API will work with.
     *
     * @return string The minimum server version.
     * @static
     */
    function getMinServerVersion()
    {
        return FileMaker_Implementation::getMinServerVersion();
    }

    /**
     * FileMaker object constructor. If you want to use the constructor
     * without having to specify all the parameters, pass in null for 
     * the parameters you would like to omit.
     * 
     * For example: To only specify the database name, username, and 
     * password, but omit the hostspec, call the constructor as,
     *  
     * new FileMaker('DatabaseName', null, 'username', 'password');
     * 
     * @param string $database The name of the database to use
     * @param string $hostspec Hostspec to use
     * @param string $username Username to login into database as
     * @param string $password Password for username
     */
    function FileMaker($database = NULL, $hostspec = NULL, $username = NULL, $password = NULL)
    {
        $this->_impl =& new FileMaker_Implementation($database, $hostspec, $username, $password);
    }

    /**
     * Set $prop to a new value for all API calls.
     *
     * @param string $prop The name of the property
     * @param string $value Its new value.
     */
    function setProperty($prop, $value)
    {
        $this->_impl->setProperty($prop, $value);
    }

    /**
     * Returns the currently set value of $prop.
     *
     * @param string $prop The name of the property.
     *
     * @return string The property's current value.
     */
    function getProperty($prop)
    {
        return $this->_impl->getProperty($prop);
    }

    /**
     * Get an associative array of property name => property value for
     * all current properties and the values currently in effect. This
     * allows introspection and debugging when necessary.
     *
     * @return array All current properties.
     */
    function getProperties()
    {
        return $this->_impl->getProperties();
    }

    /**
     * Associate a PEAR Log object with the API for logging requests
     * and responses.
     *
     * @param Log &$logger
     */
    function setLogger(&$logger)
    {
        $this->_impl->setLogger($logger);
    }

    /**
     * Create a new FileMaker_Command_Add object.
     *
     * @param string $layout The layout to add to.
     * @param array $values A hash of fieldname => value pairs. Repetions can be set
     * by making the value for a field a numerically indexed array, with the numeric keys
     * corresponding to the repetition number to set.
     *
     * @return FileMaker_Command_Add The new add command.
     */
    function &newAddCommand($layout, $values = array())
    {
        return $this->_impl->newAddCommand($layout, $values);
    }

    /**
     * Create a new FileMaker_Command_Edit object.
     *
     * @param string $layout The layout the record is part of.
     * @param integer $recordId The id of the record to edit.
     * @param array $values A hash of fieldname => value pairs. Repetions can be set
     * by making the value for a field a numerically indexed array, with the numeric keys
     * corresponding to the repetition number to set.
     *
     * @return FileMaker_Command_Edit The new edit command.
     */
    function &newEditCommand($layout, $recordId, $updatedValues = array())
    {
        return $this->_impl->newEditCommand($layout, $recordId, $updatedValues);
    }

    /**
     * Create a new FileMaker_Command_Delete object.
     *
     * @param string $layout The layout to delete from.
     * @param integer $recordId The id of the record to delete.
     *
     * @return FileMaker_Command_Delete The new delete command.
     */
    function &newDeleteCommand($layout, $recordId)
    {
        return $this->_impl->newDeleteCommand($layout, $recordId);
    }

    /**
     * Create a new FileMaker_Command_Duplicate object.
     *
     * @param string $layout The layout the record to duplicate is in.
     * @param integer $recordId The id of the record to duplicate.
     *
     * @return FileMaker_Command_Duplicate The new duplicate command.
     */
    function &newDuplicateCommand($layout, $recordId)
    {
        return $this->_impl->newDuplicateCommand($layout, $recordId);
    }

    /**
     * Create a new FileMaker_Command_Find object.
     *
     * @param string $layout The layout to find records in.
     *
     * @return FileMaker_Command_Find The new find command.
     */
    function &newFindCommand($layout)
    {
        return $this->_impl->newFindCommand($layout);
    }

    /**
     * 
     * Create a new FileMaker_Compound_Find_Set object.
     *
     * @param string $layout The layout to find records in.
     *
     * @return FileMaker_Command_CompoundFind The new find set.
     */
    function &newCompoundFindCommand($layout)
    {
        return $this->_impl->newCompoundFindCommand($layout);
    }
    
     /**
     * 
     * Create a new FileMaker_Command_FindRequest object. Each of these individual finds are added to a
     * Compound Find Set. 
     *
     * @param string $layout The layout to find records in.
     *
     * @return FileMaker_Command_FindRequest The new find request command.
     */
    function &newFindRequest($layout)
    {
        return $this->_impl->newFindRequest($layout);
    }
    
    /**
     * Create a new FileMaker_Command_FindAny object.
     *
     * @param string $layout The layout to find a random record from.
     *
     * @return FileMaker_Command_FindAny The new find-any command.
     */
    function &newFindAnyCommand($layout)
    {
        return $this->_impl->newFindAnyCommand($layout);
    }

    /**
     * Create a new FileMaker_Command_FindAll object.
     *
     * @param string $layout The layout to find all records in.
     *
     * @return FileMaker_Command_FindAll The new find-all command.
     */
    function &newFindAllCommand($layout)
    {
        return $this->_impl->newFindAllCommand($layout);
    }

    /**
     * Create a new FileMaker_Command_PerformScript object.
     *
     * @param string $layout The layout to use for script context.
     * @param string $scriptName The name of the script to run.
     * @param string $scriptParameters Any parameters to pass to the script.
     *
     * @return FileMaker_Command_PerformScript The new perform-script command.
     */
    function &newPerformScriptCommand($layout, $scriptName, $scriptParameters = null)
    {
        return $this->_impl->newPerformScriptCommand($layout, $scriptName, $scriptParameters);
    }

    /**
     * Creates a new FileMaker_Record object. This method does not
     * save the new record to the database. The record is not created
     * on the server until you call its commit() method. You must
     * specify a layout name, and you can optionally specify an array
     * of field values. Values can be set on the new record object
     * individually as well.
     *
     * @param string $layout The layout to create a new record for.
     * @param array $fieldValues Initial values for the new record's fields.
     *
     * @return FileMaker_Record The new record object.
     */
    function &createRecord($layout, $fieldValues = array())
    {
        return $this->_impl->createRecord($layout, $fieldValues);
    }

    /**
     * Returns a single FileMaker_Record object matching the given
     * layout and record ID, or a FileMaker_Error object if the fetch
     * fails.
     *
     * @param string $layout The layout $recordId is in.
     * @param integer $recordId The record id to fetch.
     *
     * @return FileMaker_Record|FileMaker_Error Either a record object or an error.
     */
    function &getRecordById($layout, $recordId)
    {
        return $this->_impl->getRecordById($layout, $recordId);
    }

    /**
     * Get a Layout object describing $layout.
     *
     * @param string $layout The name of the layout to describe.
     *
     * @return FileMaker_Layout|FileMaker_Error The layout description object or an error.
     */
    function &getLayout($layout)
    {
        return $this->_impl->getLayout($layout);
    }

    /**
     * Obtain a list of databases that are available with the current
     * server settings and the current username and password
     * credentials.
     *
     * @return array|FileMaker_Error Either an array of database names or an error.
     */
    function listDatabases()
    {
        return $this->_impl->listDatabases();
    }

    /**
     * Obtain a list of scripts from the current database that are
     * available with the current server settings and the current
     * username and password credentials.
     *
     * @return array|FileMaker_Error Either an array of script names or an error.
     */
    function listScripts()
    {
        return $this->_impl->listScripts();
    }

    /**
     * Obtain a list of layouts from the current database that are
     * available with the current server settings and the current
     * username and password credentials.
     *
     * @return array|FileMaker_Error Either an array of layout names or an error.
     */
    function listLayouts()
    {
        return $this->_impl->listLayouts();
    }

    /**
     * Get the data for a given container field.
     *
     * @param string $url The location of the data.
     *
     * @return string The raw field data.
     */
    function getContainerData($url)
    {
        return $this->_impl->getContainerData($url);
    }

}
