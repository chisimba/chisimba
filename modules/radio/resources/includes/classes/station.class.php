<?php
class stations
{

function get()
{
if ($handle = opendir('includes/station')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
           $data .= $file."&";
           $a = "1";
        }
    }
    closedir($handle);
}
if($a == ""){$data = "test&";}
return $data;
}	
	

	
function default_s($station = "0")
{

	if($station == "0" or $station == "")
	{
		
		$once = "0";
	if ($handle = opendir('includes/station')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
        	if($once == "0")
        	{
           $data = $file;
           $once = "1";
           }
        }
    }
    closedir($handle);
}
if($data == ""){$data = "test";}
return $data;	
	}
	return $station;
}	

function del($station = "0")
{
	if($station != "0"){
	if (is_dir("includes/station/".$station)) {
		if ($handle = opendir("includes/station/".$station)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
        unlink("includes/station/".$station."/".$file);
           }
        }
    }
    closedir($handle);
    rmdir("includes/station/".$station);
}
}
		}

	
function login($station = "0", $uname = "0", $password = "0")
{
if (is_dir("includes/station/".$station)) {
	if (file_exists("includes/station/".$station."/".$uname.".data")) {
		$fp = fopen("includes/station/".$station."/".$uname.".data", "rb");
		$passwd = fread($fp,filesize("includes/station/".$station."/".$uname.".data"));
		fclose($fp);
		$password = md5("$password");
		if($passwd  == $password){return true;}	
		
		}
		return false;
	}	
}

function add_admin($station = "0", $uname = "0", $password = "")
{
if (is_dir("includes/station/".$station)) {
	if (!file_exists("includes/station/".$station."/".$uname.".data")) {
		$fp = fopen("includes/station/".$station."/".$uname.".data", "w+b");
		$password = md5("$password");
		fwrite($fp, $password);
		fclose($fp);
		return true;	
		}
		return false;
	}		
}

function del_admin($station = "0", $uname = "0")
{
		if (file_exists("includes/station/".$station."/".$uname.".data")) {
			unlink("includes/station/".$station."/".$uname.".data");
			return true;
			}
			return false;
}


function get_admins()
{

		if ($handle = opendir('includes/station')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
        	if($once == "")
        	{
        	$data .= $file;
        	$once = "1";
        	}else{
			$data .= ";".$file;	
			}
        		if ($handle2 = opendir('includes/station/'.$file)) {
    while (false !== ($file2 = readdir($handle2))) {
        if ($file2 != "." && $file2 != "..") {
        	$data .= "&".$file2;
           }
        }
    }
    closedir($handle2);	
           }
        }
    }
    closedir($handle);
	return $data;	
}	
	
}
?>