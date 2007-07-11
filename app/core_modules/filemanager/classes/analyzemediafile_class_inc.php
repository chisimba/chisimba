<?php
/**
* Class to Analyse Media Files
*
* This class analyses media files for metadata information from MP3s & 
* other multimedia file formats using GetID3. Where branching needs to take place
* for specific formats, these are also catered for in the system.
*
* @author Tohir Solomons
* @package filemanager
*/
class analyzemediafile extends object
{
    
    /**
    * Constructor
    */
    function init()
    {
        $this->objGetId3 =& $this->getObject('getid3analyzer', 'files');
        $this->objFileParts =& $this->getObject('fileparts', 'files');
        $this->objXMLSerializer =& $this->getObject('xmlserial', 'utilities');
        $this->objSingleArray = $this->getObject('singlearray');
        $this->objOggReader = $this->getObject('oggreader');
    }
    
    /**
    * Method to get media information about a file using getId3
    * This function takes the information and converts it into a single array
    * instead of going through it as a multi-dimensional array
    *
    * @param string $filePath Path to File
    * @return array The array consists of two parts. 
    *                   - The first is metadata in an array
    *                   - The second is a recommended alternative mimetype for this file
    */
    public function getId3Info($filepath)
    {
        // Get Details and Convert to SingleArray
        return $this->objSingleArray->convertArray($this->objGetId3->analyze($filepath));
    }
    
    /**
    * Function to analyze getId3 Media Info
    * 
    * This function takes a file, and requests getID3 to analyze the file.
    * It then processes the analysis, focussing on information it requires.
    * This information is stored in an array and returned.
    *
    * @param string $filepath Path to File
    * @return array Processed Information of the File
    */
    public function analyzeFile($filepath)
    {
        // Create Mimetype
        $mimetype = '';
        
        // Get Details and Convert to SingleArray
        $analysis = $this->getId3Info($filepath);
        
        // Remove Useless Information
        foreach ($analysis as $item=>$value)
        {
            // Remove if Key is a number
            if (is_int($item)) {
                unset($analysis[$item]);
            }
            
            // Remove if Item has no Value
            if (trim($value) == '' || trim($value) == '?=') {
                unset($analysis[$item]);
            }
            
        }
        
        // Create Array of Details
        $mediaInfo = array('width'=>'', 'height'=>'', 'playtime'=>'', 'format'=>'', 'framerate'=>'', 'bitrate'=>'', 'samplerate'=>'', 'title'=>'', 'artist'=>'', 'year'=>'', 'url'=>'');
        
        // Width
        if (isset($analysis['resolution_x'])) {
            $mediaInfo['width'] = $analysis['resolution_x'];
        }
        
        if (isset($analysis['frame_width']) && !isset($mediaInfo['width'])) {
            $mediaInfo['width'] = $analysis['frame_width'];
        }
        
        // Height
        if (isset($analysis['resolution_y'])) {
            $mediaInfo['height'] = $analysis['resolution_y'];
        }
        
        if (isset($analysis['frame_height']) && !isset($mediaInfo['height'])) {
            $mediaInfo['height'] = $analysis['frame_height'];
        }
        
        // Play Time
        if (array_key_exists('playtime_seconds', $analysis)) {
            $mediaInfo['playtime'] = floor($analysis['playtime_seconds']);
        }
        
        // Format
        if (array_key_exists('dataformat', $analysis)) {
            $mediaInfo['format'] = $analysis['dataformat'];
            
            // If JPEG, attempt to get width and height via Exif
            if ($format == 'jpg') {
                $info = getimagesize($filepath);
                $mediaInfo['width'] = $info[0]; // Width
                $mediaInfo['height'] = $info[1]; // Height
            }
        }
        
        // Frame Rate
        if (array_key_exists('framerate', $analysis)) {
            $mediaInfo['framerate'] = $analysis['framerate'];
        }
        
        // Bit Rate
        if (array_key_exists('bitrate', $analysis)) {
            $mediaInfo['bitrate'] = $analysis['bitrate'];
        }
        
        // Sample Rate
        if (array_key_exists('sample_rate', $analysis)) {
            $mediaInfo['samplerate'] = $analysis['sample_rate'];
        }
        
        // Title
        if (array_key_exists('title', $analysis)) {
            $mediaInfo['title'] = $analysis['title'];
        }
        
        // Artist
        if (array_key_exists('artist', $analysis)) {
            $mediaInfo['artist'] = $analysis['artist'];
        }
        
        // Comment / Description
        if (array_key_exists('comment', $analysis)) {
            $mediaInfo['description'] = $analysis['comment'];
        }
        
        // Year
        if (array_key_exists('year', $analysis)) {
            $mediaInfo['year'] = $analysis['year'];
        }
        
        // URL
        if (array_key_exists('url', $analysis)) {
            $mediaInfo['url'] = $analysis['url'];
        }
        
        // Convert rest of the data to XML for storage
        // NOTE: this xml may not be well formed, but keep so long
        
        // If Ogg Format, run through another parse
        if ($this->objFileParts->getExtension($filepath) == 'ogg') {
            
            // Get Metadata
            $metadata = $this->objOggReader->getMetadata($filepath);
            
            // Set Format to Ogg
            $mediaInfo['format'] = 'ogg';
            
            // Switch Mimetype
            $mimetype = $metadata['subtype'];
            
            // Set Width
            if (isset($metadata['width'])) {
                $mediaInfo['width'] = $metadata['width'];
            }
            
            // Set Height
            if (isset($metadata['height'])) {
                $mediaInfo['height'] = $metadata['height'];
            }
            
            // Set Framerate
            if (isset($metadata['rate'])) {
                $mediaInfo['framerate'] = $metadata['rate'];
            }
            
            // Add remaining items to the list
            foreach ($metadata as $item=>$value) {
                $analysis[$item] = $value;
            }
        }
        
        // Serialize All Information into XML so that are not lost
        $mediaInfo['getid3info'] = $this->objXMLSerializer->writeXML($analysis);
        
        return array($mediaInfo, $mimetype);
    }
    
    
    

}

?>