<?php
if($_SESSION['id'] == "1"){
	exit();
	}
if($_GET['action'] == "del"){$stations->del_admin($_GET['station_n'],$_GET['uname']);}
if($_GET['action'] == "add"){$stations->add_admin($_POST['station_n'],$_POST['uname'],$_POST['password']);}
$data = explode(";",$stations->get_admins());
$stop = "0";
$teller = "0";
echo "<center><table>";
while($stop == "0")
{
$data2 = explode("&",$data[$teller]);
$station_n = $data2[0];
if($station_n != "")
{
echo "<tr><td>Station: $station_n</td><td></td></tr>";
$stop2 = "0";
$teller2 = "1";
while($stop2 == "0")
{
$uname = $data2[$teller2];
if($uname != ""){
$temp = explode(".",$uname);
$uname = $temp[0];	
if($uname != "settings"){
echo "<tr><td>User: $uname</td><td><a href=?page=admin&subpage=admins&action=del&uname=$uname&station_n=$station_n>Delete</a></td></tr>";
}
}else{$stop2 = "yes";}
$teller2++;	
}	
}else{$stop = "yes";}
$teller++;	
}
 echo "</table></center><hr><center><table><form action=?page=admin&subpage=admins&action=add method='POST'";
echo "<tr><td>Station:</td><td> ";
echo "<select class=\"searchselect\" name=\"station_n\" >";
$data = explode("&",$stations->get());

$stop = "0";
$teller = "0";
while($stop == "0")
{
$station_n = $data[$teller];
if($station_n != "")
{
	
echo "<option>$station_n</option>";
}else
{
	$stop ="yes";
}
$teller++;
}
echo "</select></td></tr><tr><td>Uname:</td><td> <input type=text name=uname></td></tr><tr><td> Password: </td><td><input type=password name=password></td></tr><tr></td><td><input type=submit value=Add></td></tr></form>";
echo "</table></center>";


?>