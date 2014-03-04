<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* DB Manager Controller
*
* @author Paul Scott
* @copyright (c) 2004 University of the Western Cape
* @package dbmanager
* @version 1
*/
class dbmanager extends controller
{

    /**
	* Constructor method to instantiate objects and get variables
	*/
    function init()
    {
        try {
    		$this->manager =& $this->getObject('dbmanagerdb');

        	// User Details
        	$this->objUser =& $this->getObject('user', 'security');
        	$this->userId =& $this->objUser->userId();

        	// Load Language Class
        	$this->objLanguage = &$this->getObject('language', 'language');
        	$this->setVarByRef('objLanguage', $this->objLanguage);

        	//Get the activity logger class
        	$this->objLog=$this->newObject('logactivity', 'logger');
        	//Log this module call
        	$this->objLog->log();
        }
        catch (customException $e)
        {
        	echo customException::cleanUp();
        	die();
        }
    }

    /**
	* Method to process actions to be taken
    *
    * @param string $action String indicating action to be taken
	*/
    function dispatch($action=Null)
    {
        switch ($action)
        {
            default:
                die("choose action");
                break;

            case 'dumpdb':
                $this->manager->getSchema();
                echo "Done";
                break;

            case 'listall':
            	echo $this->manager->listall();
                return ;

            case 'getdefinition':
                $this->manager->getDefFromDb();
                echo "Done";
                return;

            case 'createtable':

            	$table = 'bullshit';
            	// Table Name


//Options line for comments, encoding and character set
$options = array('comment' => 'cms categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'parent_id' => array(
		'type' => 'text',
		'length' => 32,
        	'notnull' => TRUE,
		'default' => '0'
		),
    'title' => array(
		'type' => 'text',
		'length' => 50
		),
    'menutext' => array(
		'type' => 'text',
		'length' => 255
		),
    'image' => array(
		'type' => 'text',
		'length' => 100
		),
    'sectionid' => array(
		'type' => 'text',
		'length' => 50
		),
    'image_position' => array(
		'type' => 'text',
		'length' => 10
		),
    'description' => array(
		'type' => 'text',
		'length' => 255
		),
    'published' => array(
		'type' => 'integer',
		'length' => 1,
        'notnull' => TRUE,
		'default' => '0'
		),
    'checked_out' => array(
		'type' => 'int',
		'length' => 11,
        'notnull' => TRUE,
		'default' => '0'
		),
    'checked_out_time' => array(
		'type' => 'date',
		'notnull' => TRUE,
		'default' => '0000-00-00 00:00:00'
		),
    'editor' => array(
		'type' => 'text',
		'length' => 32
		),
    'ordering' => array(
		'type' => 'text',
		'length' => 32
		),
    'access' => array(
		'type' => 'text',
		'length' => 32
		),
    'count' => array(
		'type' => 'integer',
		'length' => 11,

		),
    'params' => array(
		'type' => 'text',
		'length' =>255
		)
    );

//create other indexes here...

$name = 'cat';

$indexes = array(
                'fields' => array(
                	'sectionid' => array(),
                	'published' => array(),
                	'access' => array(),
                    'checked_out' => array(),
                )
        );


$this->manager->createDBTable($fields, $table, $options);
$this->manager->createPK($table);
$this->manager->createTableIndex($table, $name, $indexes);

                    echo "Good job";
                break;


        }
    }
}
?>