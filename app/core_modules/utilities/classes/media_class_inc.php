<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class media extends object 
{
	public $ffmpeg;
	
	public function init()
	{
		try {
			$this->ffmpeg = $this->getResourcePath('ffmpeg');
			$this->objConfig = $this->getObject('altconfig', 'config');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	public function convert3gp2flv($file, $savepath)
	{
		$rfile = basename($file, ".3gp");
		$newfile = $rfile.time().".flv";
		system("$this->ffmpeg -i $file -acodec mp3 -ar 22050 -ab 32 -f flv -s 320x240 $newfile", $results);
		if($results == 0)
		{
			return $savepath.$newfile; //$siteroot.'usrfiles/mediaconverter/'.$newfile;
		}
		else {
			return FALSE;
		}
	}
	
	public function convertAmr2Mp3($file, $savepath)
	{
		$rfile = basename($file, ".amr");
		$newfile = $rfile.time().".mp3";
		system("$this->ffmpeg -i $file -acodec mp3 -ar 22050 -ab 32 -f mp3 $newfile", $results);
		if($results == 0)
		{
			return $savepath.$newfile;
		}
		else {
			return FALSE;
		}
		
	}
	
	public function convertMp32Amr($file, $savepath)
	{
		$rfile = basename($file, ".mp3");
		$newfile = $rfile.time().".amr";
		system("$this->ffmpeg -i $file -ac 1 -ab 8 -ar 8000 -f amr -acodec amr_nb $newfile", $results);
		if($results == 0)
		{
			return $savepath.$newfile;
		}
		else {
			return FALSE;
		}
		
	}
	
	public function convertMp42flv($file, $savepath)
	{
		$rfile = basename($file, ".mp4");
		$newfile = $rfile.time().".flv";
		system("$this->ffmpeg -i $file -acodec mp3 -ar 22050 -ab 32 -f flv -s 320x240 $newfile", $results);
		if($results == 0)
		{
			return $savepath.$newfile;
		}
		else {
			return FALSE;
		}	
	}
}
?>