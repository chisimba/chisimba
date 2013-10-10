<?php
include('includes/system.inc.php');
function Redirect($timee, $topage) {
echo "<meta http-equiv=\"refresh\" content=\"{$timee}; url={$topage}\" /> ";
}
$data = explode(";", $playlist->get_playlist_info($station,$playlist_name));
$stop = "0";
$teller = "0";
echo "<table><tr><td>NR</td><td>Song</td><td>Bitrate</td><td>Time</td></tr>";
while($stop == "0")
{
$out = explode("&",$data[$teller]);

$song_name = $out[0];
$bitrate = $out[1];
$start = $out[2];
$end = $out[3];
$song_name = str_replace("_", " ",$song_name);
$song_name = substr($song_name,0,40);
if($song_name != "")
{
if($end == ""){ echo "<tr><td>[$teller]</td><td>".$song_name."</td><td>".$bitrate."</td><td> $start </td></tr>";}else{
echo "<tr><td>[$teller]</td><td>".$song_name."</td><td>".$bitrate."</td><td> $start / $end </td></tr>";
}
}else{$stop = "yes";}
$teller++;
}
echo "</table>";
$page_to = $this->uri(array('action'=>'songlist','station'=>$station),'radio');
Redirect("20",$page_to);
?>