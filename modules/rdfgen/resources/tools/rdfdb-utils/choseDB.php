<?php 

// ----------------------------------------------------------------------------------
// RDFDBUtils : Chose Model
// ----------------------------------------------------------------------------------

/** 
 * This lets you chose a model setup in 
 *
 * @version $Id: choseDB.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/



include("config.inc.php"); 
include("utils.php"); 
include("setup.php");


if (isset($_REQUEST["mysql_host"])) { 
  $_DB["non_config"]["host"]=$_REQUEST["mysql_host"];
  $_DB["non_config"]["type"]=$_REQUEST["mysql_type"];
  $_DB["non_config"]["port"]=$_REQUEST["mysql_port"];
  $_DB["non_config"]["dbName"]=$_REQUEST["mysql_db"];
  $_DB["non_config"]["username"]=$_REQUEST["mysql_username"];
  $_DB["non_config"]["password"]=$_REQUEST["mysql_password"];
  
  $_REQUEST["db"]="non_config"; 
  
  $_SESSION["non_config"]=$_DB["non_config"];

}

if (isset($_REQUEST["db"])) { 
  $db=$_REQUEST["db"];
  if (isset($_DB[$db])) {
    $_SESSION["activeDB"]=$db;
    $_SESSION["gunnar"]="kjedelig";

    session_write_close();
    header("Location: listModels.php");
    exit();

  } else { 
    $msg="No such DB!";
  }
}

$title="RAP DB &raquo; Chose DB";
include("header.php"); 
include("menu.php"); 

?>

<h1>Please chose a database to edit:</h1>

<ul>
<?php 

if (isset($msg)) print "<span class=\"warning\">$msg</span>\n";

foreach($_DB as $k=>$v) { 
?>
  <li><a href="choseDB.php?db=<?php print $k; ?>"><?php print dbLink($k); ?></a><?php print ($k=="non_config"?" (specified this session)":""); ?></li>

<?php } 

if (count($_DB)==0) print "<li>No databases configured.</li>\n";

?>


</ul>


<h2>or enter new:</h2>
<form method="post" > 
<input type="hidden" name="newdb" value="true" />
<table>

<tr><td>
Type:</td><td><select name="mysql_type">
  <option<?php print isset($mysql_type)&&$mysql_type=="MySQL"?"checked":""; ?> value="MySQL">MySQL</option>
  <option<?php print isset($mysql_type)&&$mysql_type=="MySQL"?"checked":""; ?> value="MsAccess">MsAccess</option>
</select>
</td></tr>

<tr><td>
Host: </td><td><input type="text" size="40" name="mysql_host" value="<?php print isset($mysql_host)?$mysql_host:"";?>"/>
</td></tr>
<tr><td>
Port: </td><td><input type="text" size="40" name="mysql_port" value="<?php print isset($mysql_port)?$mysql_port:""; ?>"/>
</td></tr>
<tr><td>
Db: </td><td><input type="text" size="40" name="mysql_db" value="<?php print isset($mysql_db)?$mysql_db:""; ?>"/>
</td></tr>
<tr><td>
Username: </td><td><input type="text" size="40" name="mysql_username" value="<?php print isset($mysql_username)?$mysql_username:""?>"/>
</td></tr>
<tr><td>
Password: </td><td><input type="text" size="40" name="mysql_password" value="<?php print isset($mysql_password)?$mysql_password:""?>"/>
</td></tr>
</table>
<input type="submit" class="okbutton" value="Ok"/> 
</form>

<?php include("footer.php"); ?>
