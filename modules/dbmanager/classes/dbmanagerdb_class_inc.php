<?PHP
/* -------------------- dbTable class for dbmanagerdb ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class dbmanagerdb extends dbTableManager
{
    public function init()
    {
        parent::init();
    }

    public function getSchema()
    {
        $this->dumpDatabaseToFile('dump','all','dumptest.xml');
    }

    public function createDBTable($fields, $tableName, $options)
    {
        return $this->createTable($tableName, $fields, $options);
    }

    public function listall()
    {
    	//$a = $this->createDb('punani');
    	//echo $a;
    	//$b = $this->listDatabases();
    	//print_r($b);
    	//$func = $this->listDbFunctions();
    	//print_r($func);
    	//$c = $this->listDbUsers();
    	//print_r($c);
    	//$d = $this->listDbViews();
    	//print_r($d);
    	//$listtabs = $this->listDbTables();
    	//print_r($listtabs);
    	//$listf = $this->listTableFields('tbl_users');
    	//print_r($listf);
    	$db = $this->getDatabase();
    	print_r($db);
    	$ver = $this->getServerVersion();
    	print_r($ver);

    	//$e = $this->dropDb('testing123fromclass');
    	//print_r($e);


    }
}