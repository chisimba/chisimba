<?php
/**
 * Class to Get Metadata from OGG Files whether audio or video
 *
 * Adapted from: http://people.xiph.org/~giles/2006/meta.php
 *
 * @author Tohir Solomons
 * @package filemanager
 */
class oggreader extends object
{

    /**
    * Constructor 
    */
    function int()
    { }
    
    /**
    * Method to get the metadata from ogg files
    * @param string $file Path to the file
    * @return array metadata as an array
    */
    function getMetadata($file)
    {
        $f = fopen($file, 'rb');
        $page = array();
    	$header = fread($f, 512);
    	$page['magic'] = substr($header, 0, 4);
    	$page['serial'] = substr($header, 14, 4);
    	$page['segments'] = ord($header[26]);
    	$page['packet_length'] = 0;
    	for ($i = 0; $i < $page['segments']; $i++) {
            $page['packet_length'] += ord($header[27+$i]);
    	}
    	$page['packet_magic'] = substr($header,27+$page['segments'],8);
    	if (0 == strncmp($page['packet_magic'],"\x01vorbis",7)) {
            $page['subtype'] = 'audio/x-vorbis';
    	} elseif (0 == strncmp($page['packet_magic'],"\x80theora",7)) {
            $page['subtype'] = 'video/x-theora';
    	} else {
            $page['subtype'] = 'unknown';
    	}
    	//echo " <tt>".$page['subtype']."</tt>";
    	if ($page['subtype'] == 'audio/x-vorbis') {
            $page['channels'] = ord($header[27+$page['segments']+11]);
            $page['rate'] = ord($header[27+$page['segments']+15]);
            $page['rate'] = ($page['rate'] << 8) | ord($header[27+$page['segments']+14]);
            $page['rate'] = ($page['rate'] << 8) | ord($header[27+$page['segments']+13]);
            $page['rate'] = ($page['rate'] << 8) | ord($header[27+$page['segments']+12]);
            //echo " ".$page['channels']." channel ".$page['rate']."Hz";
    	} elseif ($page['subtype'] == 'video/x-theora') {
            $page['width'] = ord($header[27+$page['segments']+14]);
            $page['width'] = ($page['width'] << 8) | ord($header[27+$page['segments']+15]);
            $page['width'] = ($page['width'] << 8) | ord($header[27+$page['segments']+16]);
            $page['height'] = ord($header[27+$page['segments']+17]);
            $page['height'] = ($page['height'] << 8) | ord($header[27+$page['segments']+18]);
            $page['height'] = ($page['height'] << 8) | ord($header[27+$page['segments']+19]);
            $num = ord($header[27+$page['segments']+22]);
            $num = ($num << 8) | ord($header[27+$page['segments']+23]);
            $num = ($num << 8) | ord($header[27+$page['segments']+24]);
            $num = ($num << 8) | ord($header[27+$page['segments']+25]);
            $den = ord($header[27+$page['segments']+26]);
            $den = ($den << 8) | ord($header[27+$page['segments']+27]);
            $den = ($den << 8) | ord($header[27+$page['segments']+28]);
            $den = ($den << 8) | ord($header[27+$page['segments']+29]);
            if ($den == 0 || $num == 0) {
                $page['rate'] = 'unknown';
            } else {
                $page['rate'] = $num/$den;
            }
            //echo " ".$page['width']."x".$page['height'];
            //echo " ".$page['rate']." fps";
            
            return $page;
    	}
    }
    
    
    

}

?>