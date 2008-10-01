<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * This class is used to convert different kinds of media files using ffmpeg
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class media extends object 
{
	public $ffmpeg;
	
	/**
        * standard init function
        *
        */
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
	
	/**
        * convert 3gp to flv
        *
	* @param string $file The name of the file
	* @param string $savepath The name of the path to save the new file
        */
	public function convert3gp2flv($file, $savepath)
	{
		$rfile = basename($file, ".3gp");
		$newfile = $savepath.$rfile.time().".flv";
		system("$this->ffmpeg -i $file -acodec mp3 -ar 22050 -ab 32 -f flv -s 320x240 $newfile", $results);
		//log_debug("$this->ffmpeg -i $file -acodec mp3 -ar 22050 -ab 32 -f flv -s 320x240 $newfile");
		if($results == 0)
		{
			return $newfile; //$siteroot.'usrfiles/mediaconverter/'.$newfile;
		}
		else {
			return FALSE; // "$this->ffmpeg -i $file -acodec mp3 -ar 22050 -ab 32 -f flv -s 320x240 $newfile";
		}
	}
	
	/**
        * convert Amr to Mp3
        *
	* @param string $file The name of the file
	* @param string $savepath The name of the path to save the new file
        */
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
	
	/**
        * convert Mp3 to Amr
        *
	* @param string $file The name of the file
	* @param string $savepath The name of the path to save the new file
        */
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
	
	/**
        * convert Avi to Flv
        *
	* @param string $file The name of the file
	* @param string $savepath The name of the path to save the new file
        */
	public function convertAvi2Flv($file, $savepath)
	{
		$rfile = basename($file, ".avi");
		$newfile = $rfile.time().".flv";
		system("$this->ffmpeg -i $file -ac 1 -ab 8 -ar 8000 -f amr -acodec amr_nb $newfile", $results);
		if($results == 0)
		{
			return $savepath.$newfile;
		}
		else {
			return FALSE;
		}
		
	}
	
	/**
        * convert Mp4 to Flv
        *
	* @param string $file The name of the file
	* @param string $savepath The name of the path to save the new file
        */
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
	
	public function convertOgg2Mp3($file, $savepath, $idauthor = NULL, $idtitle = NULL, $idcomment = NULL)
	{
		$rfile = basename($file, ".ogg");
		$newfile = $savepath."/".$rfile.".mp3";
		$iddate = date('Y');
		// oggdec -Q /home/paul/Desktop/music/zola.ogg -o - | lame --vbr-new --replaygain-fast --quiet --ta "Paul Scott" --tg "Other" --tt "Title of file" --tc "Comment that I am making" --ty "2008" - /home/paul/outfile.mp3
		system("oggdec -Q $file -o - | lame --vbr-new --replaygain-fast --quiet --replaygain-fast --ta '$idauthor' --tg 'Other' --tt '$idtitle' --tc '$idcomment' --ty '$iddate' - $newfile", $results);
		if($results == 0)
		{
			return $newfile;
		}
		else {
			return FALSE;
		}
		
	}
	
	public function convertWav2Mp3($file, $savepath)
	{
		$rfile = basename($file, ".wav");
		$newfile = $rfile.".mp3";
		$newfile = $savepath.$newfile;
		log_debug("$this->ffmpeg -i $file $newfile");
		system("$this->ffmpeg -i $file $newfile", $results);
		log_debug($results);
		if($results == 0)
		{
			return $newfile;
		}
		else {
			return FALSE;
		}	
	}
	
}
?>