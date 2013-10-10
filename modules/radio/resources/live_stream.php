<?php
error_reporting(1);
include('includes/system.inc.php');
$target = $_FILES['data']['name'];
$restart = $_POST['restart'];
$bitrate = $_POST['bitrate'];
$key = $_POST['key'];
$temp = explode("&",$key);
$key = $temp[0];
$station_name = $temp[1];
$metadata = $temp[2];
$stop = $_POST['stop'];
if(!is_dir("includes/live/".$station_name))
	{
		mkdir ("includes/live/".$station_name, 0777);
	
	}
if($key == $debugkey)
{
	if($stop == true)
{
	unlink("includes/live/".$station_name."/live.mp3");
	unlink("includes/live/".$station_name."/live.data");
	unlink("includes/live/".$station_name."/live_ghost.mp3");
	echo "Removeing files!";
}
else {
if($restart == true)
{
unlink("includes/live/".$station_name."/live.mp3");
$data_file = fopen("includes/live/".$station_name."/live.data", 'w+b');
$data = time()."&".$bitrate."&live&$metadata";
fwrite($data_file, $data);
fclose($data_file);	
}else
{
if(move_uploaded_file($_FILES['data']['tmp_name'], "temp/".$target))
{
$bert= fopen("temp/live.mp3", 'rb');
if(filesize("includes/live/".$station_name."/live.mp3") <= 10000000){
$data_file = fopen("includes/live/".$station_name."/live.mp3", 'ab');
if(filesize("includes/live/".$station_name."/live.mp3") >= 2000000){
	unlink("includes/live/".$station_name."/live_ghost.mp3");
	}
}
else{	
	if(filesize("include/live/".$station_name."/live_ghost.mp3") <= 10000000){
$fp = fopen("includes/live/".$station_name."/live.data", 'rb');
$filedata = fread($fp, 262144);
fclose($fp);
$filedata2 = explode("&", $filedata);
if($filedata2[2] == "live"){
$fp = fopen("includes/live/".$station_name."/live.data", 'w+b');
$tter = time()."&".$filedata2[1]."&live_ghost&$metadata";
fwrite($fp, $tter);
fclose($fp);
}
	$data_file = fopen("includes/live/".$station_name."/live_ghost.mp3", 'ab');
	}
	else
	{
		$data_file = fopen("includes/live/".$station_name."/live.mp3", 'w+b');
		$fp = fopen("includes/live/".$station_name."/live.data", 'rb');
$filedata = fread($fp, 262144);
fclose($fp);
$filedata2 = explode("&", $filedata);
$fp = fopen("includes/live/".$station_name."/live.data", 'w+b');
$tter = time()."&".$filedata2[1]."&live&$metadata";
fwrite($fp, $tter);
fclose($fp);
	}
	
}

while(!feof($bert)) {
$data=	fread($bert, 50000);
fwrite($data_file, $data);
}
fclose($data_file);
fclose($bert); 
echo "Streaming for ";
 }
else {echo "Error Uploading!";}
}
}
}
else {echo "incorrect";}
 ?>