<?php
//block all skin banners
$this->setVar('pageSuppressToolbar',true);
$this->setVar('pageSuppressSkin',true);
$this->setVar('pageSuppressBanner',true);
$this->setVar('suppressFooter',true);
$ban_src = $this->getResourcePath('includes/ban','radio');

if($_GET['action'] == "ban"){$this->console->add_to_ban($_GET['ip']);}
if($_GET['action'] == "unban"){$this->console->remove_ban($_GET['ip']);}
$data = $this->stats->get_users_online_names($station);
$data_out = explode("&",$data);
$stop = "0";
$teller = "0";
echo "<center><table><tr><td>IP</td><td>Option</td></tr>";
while($stop == "0")
{
$user_online = $data_out[$teller];
if($user_online != "")
{

echo "<tr><td>$user_online</td><td><a href=?page=admin&subpage=users&action=ban&ip=$user_online>Ban</a></td></tr>";

}else{$stop = "yes";}
$teller++;
}
echo "</table><hr>Banlist</center>";
echo "<center><table><tr><td>IP</td><td>Option</td></tr>";
if ($handle = opendir("$ban_src")) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
        	$temp = explode(".",$file);
        	$file = $temp[0].".".$temp[1].".".$temp[2].".".$temp[3];
            echo "<tr><td>$file</td><td><a href=?page=admin&subpage=users&action=unban&ip=$file>Unban</a></td></tr>";
           }
        }
    }
    closedir($handle);
   echo "</table></center>";
?>