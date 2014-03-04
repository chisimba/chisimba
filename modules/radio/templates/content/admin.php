<?php
include_once('includes/system.inc.php');
session_start();
function Redirect($timee, $topage) {
echo "<meta http-equiv=\"refresh\" content=\"{$timee}; url={$topage}\" /> ";
}
$subpage = $_REQUEST['subpage'];
if($_SESSION['station'] != ""){$station = $_SESSION['station'];}
if($_SESSION['id'] != ""){
if($subpage == ""){$subpage = "home.php";} else{$subpage = $subpage.".php";}
}else{$subpage = "login.php";}
echo "<center>";
include("pages/".$subpage);
echo "</center>";
?>