<?PHP

/**
 * Portability example
 * @example
 * @since 27 Feb 2006
 * @copyright GNU GPL/UWC AVOIR 2006
 * @author Paul Scott
 */
require_once('portability_class_inc.php');
require_once 'MDB2.php';

//Which db do we wanna connect to?
$dbms = 'pgsql';
//set up the user and pass, as well as the db to connect to

switch($dbms)
{
    case 'pgsql':
        //postgres
        $user = "postgres";
        $pw = "";
        $db = "portability";
        break;

    case 'mysqli':
        //mysqli
        $user = "root";
        $pw = "";
        $db = "portability";
        break;

    case 'mysql':
        //mysql
        $user = "root";
        $pw = "";
        $db = "portability";
        break;

    case 'mssql':
        //mssql
        $user = "root";
        $pw = "";
        $db = "portability";
        break;

    case 'oci8':
        //oracle
        $user = '';
        $pw = '';
        $db = '';
        break;
}

$dsns = array(
    //'access'   => "odbc(access)://admin@/$dba",
    //'db2'      => "odbc(db2)://$userd:$pwd@/$dbd",
    //'fbsql'    => "fbsql://$user:$pw@/$db",
    //'firebird' => "ibase(firebird)://$user:$pw@localhost:3050/$dbf",
    'mysql'    => "mysql://$user:$pw@/$db",
    'mysqli'   => "mysqli://$user:$pw@localhost:3307/$db",
    'oci8'     => "oci8://$user:$pw@/$db",
    'pgsql'    => "pgsql://$user:$pw@192.102.9.54/$db",
    //'sqlite'   => "sqlite:///$dbs?mode=0666",
    //'sybase'   => "sybase://$user:$pw@$mach/$db",
    'mssql'    => "mssql://$user:$pw@172.16.64.128/$db",
);

$options = array(
    'debug'       => 2,  // on live sites, comment out this line
    'portability' => MDB2_PORTABILITY_ALL,
);

$db =& MDB2::factory($dsns[$dbms], $options);
if (MDB2::isError($dsns[$dbms])) {
    if ($options['debug'] >= 2) {
        die($db->getUserInfo());
    } else {
        die($db->getMessage());
    }
}

$p = new portability($db);

//If you want to clean up set $drop = TRUE;
$drop = FALSE;

//timestamp abstraction
$query = 'CREATE TABLE xyz (ts ' . $p->getTimestampType() . ')';
echo $query . "<br><br>";
$db->queryAll($query);
if($drop == TRUE)
{
  $db->queryAll('DROP TABLE xyz ' . $p->getDropRestrict());
}

$query = 'SELECT ' . $p->formatTimestampQuery('ts') . ' FROM xyz';
echo $query . "<br><br>";
$out = $db->queryOne($query);
var_dump($out);

//date and timestamp abstraction
$query = 'INSERT INTO xyz (df, ts) VALUES (' .
         $p->getDateLiteralType() . "'1980-01-01', " .
         $p->getTimestampLiteralType() . " '2004-11-10 15:30:00')";
echo $query . "<br><br>";
$db->query($query);
if($drop == TRUE)
{
    $db->query("DELETE FROM xyz WHERE cf = 'n/a'");
}

//date literal abstraction
$query = 'CREATE TABLE dateliteral (d ' . $p->getDateType() . ')';
echo $query . "<br><br>";
$db->query($query);
if($drop ==TRUE)
{
    $db->query('DROP TABLE dateliteral ' . $p->getDropRestrict());
}

$query = 'SELECT ' . $p->formatDateQuery('d') . ' FROM dateliteral';
echo $query . "<br><br>";
$out = $db->queryOne($query);
var_dump($out);

$query = 'INSERT INTO dateliteral (d) VALUES (' .
         $p->getDateLiteralType() . " '2004-11-10')";
echo $query . "<br><br>";
$db->query($query);

//boolean types
$query = 'CREATE TABLE bool (b ' . $p->getBooleanType() . ')';
echo $query . "<br><br>";
$db->query($query);
if($drop == TRUE)
{
    $db->query('DROP TABLE xyz ' . $p->getDropRestrict());
}

/**
//bool tests
$db->query("INSERT INTO t (cf, daf, bf) VALUES ('bt1', " . $p->getDateLiteralType()
              . "'2005-01-01', " . $db->quote(true) . ')');
$db->query("INSERT INTO t (cf, daf, bf) VALUES ('bt2', " . $p->getDateLiteralType()
              . "'2005-01-02', " . $db->quote(false) . ')');
$db->query("INSERT INTO t (cf, daf, bf) VALUES ('bt3', " . $p->getDateLiteralType()
              . "'2005-01-03', " . $db->quote(null) . ')');
$res = $db->query("SELECT bf, cf FROM t WHERE cf LIKE 'bt%' ORDER BY cf");
$out = '';
while ($res->fetchInto($row)) {
    $o = $p->castToBoolean($row[0]);
    ob_start();
    var_dump($o);
    $out .= ' ' . trim(ob_get_clean());
}
echo $out;
$res->free();
$db->query("DELETE FROM t WHERE cf LIKE 'bt%'");
*/

//clob (text) fields
$query = 'CREATE TABLE clob (b ' . $p->getClobType() . ')';
echo $query . "<br><br>";
$db->query($query);
if($drop == TRUE)
{
    $db->query('DROP TABLE clob ' . $p->getDropRestrict());
}

$query = 'CREATE TABLE decfield (d ' . $p->formatDecimalType(2, 1) . ')';
echo $query . "<br><br>";
$db->query($query);
if($drop == TRUE)
{
    $db->query('DROP TABLE decfield ' . $p->getDropRestrict());
}

//set the NULL keyword
$query = 'CREATE TABLE setnull (c CHAR(1) ' . $p->getNullKeyword() . ')';
echo $query . "<br><br>";
$db->query($query);
if($drop == TRUE)
{
    $db->query('DROP TABLE setnull ' . $p->getDropRestrict());
}
?>