<?php

//block all skin banners
$this->setVar('pageSuppressToolbar',true);
$this->setVar('pageSuppressSkin',true);
$this->setVar('pageSuppressBanner',true);
$this->setVar('suppressFooter',true);

if($_SESSION['id'] == "2"){
if($_GET['command'] == "add"){ $this->settings->add($_POST['new_station_n'],"N/A","N/A","N/A","N/A","test");}
if($_GET['command'] == "del"){ $this->stations->del($_GET['station_n']);}
if($_GET['command'] == "edit"){
	$station_n = $_GET['station_n'];
	if($_GET['opt'] == "update"){
	$this->settings->add($station_n,$_POST['headertitle'],$_POST['headergenre'],$_POST['headerbitrate'],$_POST['headersite'],$_POST['debugkey']);
	}
	$data = explode("&", $this->settings->get($station_n));
	echo "<center><form action=?module=radio&action=stations&station_n=$station_n&command=edit&opt=update  method=\"POST\"><table>";
	echo "<tr><td>Header title</td><td><input type=text name=headertitle value=".$data[0]."></td></tr>";
	echo "<tr><td>Header genre</td><td><input type=text name=headergenre value=".$data[1]."></td></tr>";
	echo "<tr><td>Header bitrate</td><td><input type=text name=headerbitrate value=".$data[2]."></td></tr>";
	echo "<tr><td>Header site</td><td><input type=text name=headersite value=".$data[3]."></td></tr>";
	echo "<tr><td>Debugkey</td><td><input type=text name=debugkey value=".$data[4]."></td></tr>";
	echo "<tr><td></td><td><input type=submit value=Update></td></tr></table></form>";



	}else{
echo "<center><table><tr><td>station Name</td><td>Options</td></tr>";
$data = explode("&",$this->stations->get());
$stop = "0";
$teller = "0";
while($stop == "0")
{
$station_n = $data[$teller];
if($station_n != "")
{
echo "<tr><td>$station_n</td><td><a href=?module=radio&action=stations&command=edit&station_n=$station_n>Edit</a> | <a href=?module=radio&action=stations&command=del&station_n=$station_n>Delete</a></td></tr>";
}else
{
	$stop ="yes";
}
$teller++;
}
echo "<tr><td><form action=?module=radio&action=stations&command=add  method=\"POST\"><input type=text name=new_station_n></td><td><input type=submit value=Add></td></form></tr>";
echo "</table></center>";
}
}else
{
$station_n = $_SESSION['station'];
	if($_GET['opt'] == "update"){
	$this->settings->add($station_n,$_POST['headertitle'],$_POST['headergenre'],$_POST['headerbitrate'],$_POST['headersite'],$_POST['debugkey']);
	}
	$data = explode("&", $this->settings->get($station_n));
	echo "<center><form action=?module=radio&action=stations&station_n=$station_n&command=edit&opt=update  method=\"POST\"><table>";
	echo "<tr><td>Header title</td><td><input type=text name=headertitle value=".$data[0]."></td></tr>";
	echo "<tr><td>Header ganre</td><td><input type=text name=headergenre value=".$data[1]."></td></tr>";
	echo "<tr><td>Header bitrate</td><td><input type=text name=headerbitrate value=".$data[2]."></td></tr>";
	echo "<tr><td>Header site</td><td><input type=text name=headersite value=".$data[3]."></td></tr>";
	echo "<tr><td>Debugkey</td><td><input type=text name=debugkey value=".$data[4]."></td></tr>";
	echo "<tr><td></td><td><input type=submit value=Update></td></tr></table></form>";
}
?>