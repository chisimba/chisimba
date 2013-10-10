<?php
class console
{
function start_up($station = "0")
{
	$data = "0";
	if($station != "0")
	{
	 if (file_exists("includes/log/".$station.".data")) {
	 	$fp = fopen("includes/log/".$station.".data", "rb");
	 	$data = fread($fp, filesize("includes/log/".$station.".data"));
	 	fclose($fp);
		 }else{return "0";}	
	}
	return $data;
}

function add_log($station = "0",$data_q = "0")
{
	$time_stamp = time();
	if($station != "0")
	{
		
	 if (file_exists("includes/log/".$station.".data")) {
	 	$fp = fopen("includes/log/".$station.".data", "rb");
	 	$data = fread($fp, filesize("includes/log/".$station.".data"));
	 	fclose($fp);
	 	$data .= $data.$data_q.";".$time_stamp."&";
	 	$fp = fopen("includes/log/".$station.".data", "w+b");
	 	fwrite($fp,$data);
	 	fclose($fp);
	 	}else
	 	{
		$data = $data_q.";".$time_stamp."&";
	 	$fp = fopen("includes/log/".$station.".data", "w+b");
	 	fwrite($fp,$data);
	 	fclose($fp);	
		}
	}	
}

function add_online_user($station = "0")
{

	$ip = $_SERVER["REMOTE_ADDR"];
	$time = time() + 300;
	if($station != "0" && $ip != "0")
	{
		if ($handle = opendir('includes/users')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
        			if ($handle2 = opendir('includes/users/'.$file)) {
    while (false !== ($file2 = readdir($handle2))) {
        if ($file2 != "." && $file2 != "..") {
        	unlink("includes/users/".$file."/".$file2);
        	  }
    }
    closedir($handle2);
}
        	  }
    }
    closedir($handle);
}
	if (is_dir("includes/users/".$ip)) {
		$fp = fopen("includes/users/".$ip."/".$station.".".$time.".data","w+b");
		fclose($fp);
		}else
		{
		mkdir("includes/users/".$ip, 0777);
		$fp = fopen("includes/users/".$ip."/".$station.".".$time.".data","w+b");
		fclose($fp);
		}
	}
}


function update_online_users()
{
	$time_end = time() + 300;
	if ($handle = opendir('includes/users')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            	if ($handle2 = opendir('includes/users/'.$file)) {
    while (false !== ($file2 = readdir($handle2))) {
        if ($file2 != "." && $file2 != "..") {
          $fp = fopen("includes/users/".$file."/".$file2, "rb");
		  $time_start = fread($fp,filesize("includes/users/".$file."/".$file2));
		  fclose($fp);
		  if($time_end <= $time_start){unlink("includes/users/".$file."/".$file2);}
        }
    }
    closedir($handle2);
}
        }
    }
    closedir($handle);
}	
}

function ban_check()
{	
		if ($handle = opendir('includes/ban')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            if($file == $_SERVER["REMOTE_ADDR"].".data")
            {
			exit();	
			}
           }
        }
    }
    closedir($handle);

}

function ban_check2($ip = "0")
{	
		if ($handle = opendir('includes/ban')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            if($file == $ip.".data")
            {
			return true;	
			}
           }
        }
    }
    closedir($handle);
    return false;

}
function add_to_ban($ip = "0")
{
	if($ip != "0" && $ip != "")
	{
	$fp = fopen("includes/ban/".$ip.".data", "w+b");
	fclose($fp);
	}
}

function remove_ban($ip)
{
	if(file_exists("includes/ban/".$ip.".data"))
	{
		unlink("includes/ban/".$ip.".data");
	}
}

function commands()
{
$data =	$_REQUEST['command'];
if($data != ""){
$data_temp = explode(" ",$data);
$options_teller = count($data_temp);
$command = $data_temp[0];
include('console.commands.php');
return "command not recognized";
}
}	
	
}
?>