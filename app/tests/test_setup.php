<?php

$testcases = array(
    'MDB2_manager_testcase',
#    'MDB2_api_testcase',
#    'MDB2_usage_testcase',
#    'MDB2_bugs_testcase',
#    'MDB2_reverse_testcase',
#    'MDB2_native_testcase',
#    'MDB2_function_testcase',
#    'MDB2_datatype_testcase',
);

// use a user that has full permissions on a database named "driver_test"
$mysql = array(
    'dsn' => array(
        'phptype' => 'mysql',
        'username' => 'root',
        'password' => '',
        'hostspec' => 'localhost',
    ),
    'options' => array(
        'use_transactions' => true
    )
);

$pgsql = array(
    'dsn' => array(
        'phptype' => 'pgsql',
        'username' => 'postgres',
        'password' => '',
        'hostspec' => 'localhost',
    )
);
/*
$oci8 = array(
    'dsn' => array(
        'phptype' => 'oci8',
        'username' => '',
        'password' => 'password',
        'hostspec' => 'hostname',
    ),
    'options' => array(
        'DBA_username' => 'username',
        'DBA_password' => 'password'
    )
);

$sqlite = array(
    'dsn' => array(
        'phptype' => 'sqlite',
        'username' => '',
        'password' => 'password',
        'hostspec' => 'hostname',
    ),
    'options' => array(
        'database_path' => '',
        'database_extension' => '',
    )
);

// must be a user with system administrator privileges
$mssql = array(
    'dsn' => array(
        'phptype' => 'mssql',
        'username' => 'username',
        'password' => 'password',
        'hostspec' => 'hostname',
    )
);

$fbsql = array(
    'dsn' => array(
        'phptype' => 'fbsql',
        'username' => 'username',
        'password' => 'password',
        'hostspec' => 'hostname',
    )
);


$ibase = array(
    'dsn' => array(
        'phptype' => 'ibase',
        'username' => 'username',
        'password' => 'password',
        'hostspec' => 'hostname',
    )
);
*/
$dbarray = array();
$dbarray[] = $mysql;
#$dbarray[] = $pgsql;
#$dbarray[] = $oci8;
#$dbarray[] = $sqlite;
#$dbarray[] = $mssql;
#$dbarray[] = $fbsql;
#$dbarray[] = $ibase;

// you may need to uncomment the line and modify the multiplier as you see fit
#set_time_limit(60*count($dbarray));
?>
