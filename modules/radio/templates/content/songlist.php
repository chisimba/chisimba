<?php
/**
 * Redirect template to show the current radio playlist
 *
 * @param variant $timee. The times to refresh
 * @param string $topage. Where to redirect
 */


//block all skin banners
$this->setVar('pageSuppressToolbar',true);
$this->setVar('pageSuppressSkin',true);
$this->setVar('pageSuppressBanner',true);
$this->setVar('suppressFooter',true);

function Redirect($timee, $topage) {
echo "<meta http-equiv=\"refresh\" content=\"{$timee}; url={$topage}\" /> ";
}
if (is_array($data)) {
	$stop = "0";
	$teller = "0";
	echo "<table><tr><td>NR</td><td>Song</td><td>Bitrate</td><td>Time</td></tr>";
	while($stop == "0")
	{
		$out = explode("&",$data[$teller]);
		array_keys($out);
		$song_name =  isset($out[0]) ? $out[0] : 0;
		$bitrate =  isset($out[1]) ? $out[1] : 0;
		$start =  isset($out[2]) ? $out[2] : 0;
		$end =  isset($out[3]) ? $out[3] : 0;
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
	$page_to =  $this->uri(array('action'=>'loadlist','station'=>$station),'radio');
	Redirect("20",$page_to);
}else{
	echo "<table><tr><td>NR</td><td>Song</td><td>Bitrate</td><td>Time</td></tr>";
	$song_name = '';
	if($song_name != "")
	{
		if($end == ""){ echo "<tr><td>[$teller]</td><td>".$song_name."</td><td>".$bitrate."</td><td> $start </td></tr>";}else{
			echo "<tr><td>[$teller]</td><td>".$song_name."</td><td>".$bitrate."</td><td> $start / $end </td></tr>";
		}
	}else{$stop = "yes";}

	echo "</table>";
	$page_to = $this->uri(array('action'=>'loadlist','station'=>$station),'radio');
	Redirect("20",$page_to);
}

?>