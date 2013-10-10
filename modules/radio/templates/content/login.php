<?php

//block all skin banners
$this->setVar('pageSuppressToolbar',true);
$this->setVar('pageSuppressSkin',true);
$this->setVar('pageSuppressBanner',true);
$this->setVar('suppressFooter',true);

if (isset($message)) {
	echo "<center><span class='error'>$message</span></center>";
}

echo "<center><table><form action={$this->uri(array('action'=>'dologin'),'radio')} method='POST'";
echo "<tr><td>Station</td><td>";
echo "<select name=station_n>";
$data = explode("&",$this->stations->get());

$stop = "0";
$teller = "0";
while($stop == "0")
{
$station_n = $data[$teller];
if($station_n != "")
{
echo "<option  value='$station_n'>$station_n</option>";
}else
{
	$stop ="yes";
}
$teller++;
}
echo "</select></td></tr>";
echo "<tr><td>Uname</td><td><input type=text name=uname></td></tr>";
echo "<tr><td>Password</td><td><input type=password name=password></td></tr>";
echo "<tr><td></td><td><input type=submit value=Login></td></tr>";
echo "</form></table></center>";

?>