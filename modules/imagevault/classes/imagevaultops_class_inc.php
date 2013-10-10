<?php
/**
 *
 * imagevault helper class
 *
 * PHP version 5.1.0+
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   imagevault
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * imagevault helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package imagevault
 *
 */
class imagevaultops extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;
    
    /**
     * @var array $data Object property for holding the data
     *
     * @access public
     */
    public $data = array();

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        $this->objCC         = $this->getObject('displaylicense', 'creativecommons');
        $this->objDbVault    = $this->getObject('dbvault');
        $this->objDateTime   = $this->getObject('dateandtime', 'utilities');
        $this->objExif       = $this->getObject('exifmeta', 'metadata');
        $this->objIPTC       = $this->getObject('iptcmeta', 'metadata');
    }
    
    public function getMetaFromImage($image) {
        $this->objIPTC->setImage($image);
        $valid = $this->objIPTC->isValid();
        //var_dump($valid);
        if($valid == TRUE) {
            // EXIF
            $imagetype = $this->objExif->getImageType($image);
            $headers = $this->objExif->readHeaders($image, FALSE);
            $thumb = $this->objExif->getExifThumb($image, 200, 200);
            
            // build up the array of data we want
            $insarr = array();
            // IPTC
            $tagarr = $this->objIPTC->getAllTags();
            if(is_array($tagarr)) {
                if(array_key_exists('2#116', $tagarr)) {
                    $copyarr = $tagarr['2#116'];
                }
                else {
                    $copyarr = array();
                }
                if(array_key_exists('2#025', $tagarr)) {
                    $keywords = $tagarr['2#025'];
                }
                else {
                    $keywords = array();
                }
            }
            else {
                $copyarr = array();
                $keywords = array();
            }
            $iptc = array_merge($copyarr, $keywords);
            
            $insarr['iptc'] = $iptc;
            $insarr['exif'] = $headers;
            $insarr['thumb'] = $thumb;
            $insarr['imagetype'] = $imagetype;
                        
            return $insarr;
        }
        
        
        
    }
    
    public function createthumb($name, $new_w, $new_h)
    {
        $filename = $name."_tn";
	    $system = explode(".", $name);
	    if(count($system) == 3) {
	        $sys = $system[2];
	    }
	    else {
	        $sys = $system[1];
	    }
	    if (preg_match("/jpg|jpeg|JPG/", $sys)) {
	        $src_img = imagecreatefromjpeg($name);
	    }
	    if (preg_match("/png/", $sys)) {
	        $src_img = imagecreatefrompng($name);
	    }
	    $old_x = imageSX($src_img);
	    $old_y = imageSY($src_img);
	    if ($old_x > $old_y) 
	    {
		    $thumb_w = $new_w;
		    $thumb_h = $old_y*($new_h/$old_x);
	    }
	    if ($old_x < $old_y) 
	    {
		    $thumb_w = $old_x*($new_w/$old_y);
		    $thumb_h = $new_h;
	    }
	    if ($old_x == $old_y) 
	    {
		    $thumb_w = $new_w;
		    $thumb_h = $new_h;
	    }
	    $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
	    imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y); 
	    if (preg_match("/png/", $system[1]))
	    {
		    imagepng($dst_img, $filename); 
	    } else {
		    imagejpeg($dst_img, $filename); 
	    }
	    imagedestroy($dst_img); 
	    imagedestroy($src_img); 
	    
	    return file_get_contents($filename);
    }  
    
    /**
     * Method to display the login box for prelogin operations
     *
     * @param  bool   $featurebox
     * @return string
     */
    public function loginBox($featurebox = FALSE)
    {
        $objBlocks = $this->getObject('blocks', 'blocks');
        if ($featurebox == FALSE) {
            return $objBlocks->showBlock('login', 'security') . "<br />" . $objBlocks->showBlock('register', 'security');
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            return $objFeatureBox->show($this->objLanguage->languageText("word_login", "system") , $objBlocks->showBlock('login', 'security', 'none')
              . "<br />" . $objBlocks->showBlock('register', 'security', 'none') );
        }
    }
    
    public function insertImageData($userid, $image) {
        $meta = $this->getMetaFromImage($image);
        
        // var_dump($meta);
        $data = file_get_contents($image);
        $hash = sha1($data);
        $thumb = $this->objExif->getExifThumb($image, 200, 200);
        $tarr = explode(",", $thumb);
        if($tarr[1] == "'>") {
            $thumb = "<img  width='200' height='200' src='data:image;base64,".base64_encode(file_get_contents($this->objConfig->getsiteRootPath()."skins/_common/icons/noimage200.jpg"))."'>";
        }
        $license = $this->objDbVault->getLicense($userid);
        if($license === FALSE) {
            $license = 'copyright';
        }
        $insarr = array('userid' => $userid,
                   'filename' => $meta['exif']['FILE']['FileName'] ,
                   'thumbnail' => $thumb,
                   'hash' => $hash,
                   'license' => $license,
                   'metadataid' => NULL,
                   );   
        $imgid = $this->objDbVault->insertImage($userid, $insarr);
        
        // insert the keywords
        $this->insertKeywords($meta, $imgid, $userid);
        // now the file section metadata
        $this->insertFileSection($meta, $imgid, $userid);
        // computed section
        $this->insertComputed($meta, $imgid, $userid);
        // GPS data
        $this->insertGPS($meta, $imgid, $userid);
        // IFD0 data
        $this->insertIFD0($meta, $imgid, $userid);
        // IFD1 Section (not commonly used in consumer level cameras...)
        $this->insertIFD1($meta, $imgid, $userid);
        // SubIFD section
        $this->insertSubIFD($meta, $imgid, $userid);
        // Thumbnail section (this is defined elsewhere too, and can be computed, but this is for convenience sake
        $this->insertThumbnail($meta, $imgid, $userid);
        // EXIF section
        $this->insertExif($meta, $imgid, $userid);
        // Generate the RDF file
        $this->getExifRDF($image, $userid);
        
        return $imgid;
    }
    
    private function insertKeywords($meta, $imgid, $userid) {
        $kwordsarr = $meta['iptc'];
        foreach($kwordsarr as $keys) {
            $kinsarr = array('userid' => $userid,
                             'imageid' => $imgid,
                             'keyword' => $keys,
                            );
            return $this->objDbVault->insertKeywords($kinsarr);
        }
    }
    
    private function insertFileSection($meta, $imgid, $userid) {
        if(array_key_exists('FILE', $meta['exif'])) {
            $file = $meta['exif']['FILE'];
            $insarr = array(
                      'imageid' => $imgid,
                      'userid'=> $userid,
                      'filename' => $file['FileName'],
                      'filedatetime' => $file['FileDateTime'],
                      'filesize' => $file['FileSize'],
                      'filetype' => $file['FileType'],
                      'mimetype' => $file['MimeType'],
                      'sectionsfound' => $file['SectionsFound'],
                  );
            return $this->objDbVault->insertFileData($insarr);
        }
        else {
            return FALSE;
        }
    }
    
    public function insertComputed($meta, $imgid, $userid) {
        if(array_key_exists('COMPUTED', $meta['exif'])) {
            $computed = $meta['exif']['COMPUTED'];
            $carr = array('html', 'Width', 'Height', 'IsColor', 'ByteOrderMotorola', 'CCDWidth', 'ApertureFNumber', 'Thumbnail.FileType', 'Thumbnail.MimeType');
            foreach($carr as $keys) {
                if(!array_key_exists($keys, $computed)) {
                    $computed[$keys] = '';
                }
            }
            $insarr = array(
                      'imageid' => $imgid,
                      'userid'=> $userid,
                      'html' => $computed['html'],
                      'height' => $computed['Height'],
                      'width' => $computed['Width'],
                      'iscolor' => $computed['IsColor'],
                      'byteordermotorola' => $computed['ByteOrderMotorola'],
                      'ccdwidth' => $computed['CCDWidth'],
                      'aperturefnumber' => $computed['ApertureFNumber'],
                      'thumbnail_filetype' => $computed['Thumbnail.FileType'],
                      'thumbnail_mimetype' => $computed['Thumbnail.MimeType'],
            );
            $this->objDbVault->insertComputed($insarr);
            
            
        }
        else {
            return FALSE;
        }
    }
    
    private function insertGPS($meta, $imgid, $userid) {
        if(array_key_exists('GPS', $meta['exif'])) {
            $gps = $meta['exif']['GPS'];
            $gpsarr = array('GPSLatitude', 'GPSLatitudeRef', 'GPSLongitude', 'GPSLongitudeRef', 'GPSAltitude', 'GPSAltitudeRef', 'GPSTimeStamp', 'GPSImgDirectionRef', 'GPSImgDirection',
                            'GPSSatellites', 'GPSStatus', 'GPSMeasureMode', 'GPSDOP', 'GPSSpeedRef', 'GPSSpeed', 'GPSTrackRef', 'GPSTrack', 'GPSMapDatum', 'GPSDestLatitudeRef', 'GPSDestLatitude', 
                            'GPSDestLongitudeRef', 'GPSDestLongitude', 'GPSDestBearingRef', 'GPSDestBearing', 'GPSDestDistanceRef', 'GPSDestDistance', 'GPSProcessingMethod', 'GPSAreaInformation',
                            'GPSDateStamp', 'GPSDifferential', 'GPSHPositioningError',);
            foreach($gpsarr as $keys) {
                if(!array_key_exists($keys, $gps)) {
                    $gps[$keys] = '';
                }
            }
            if(is_array($gps['GPSLatitude']) && !empty($gps['GPSLatitude'])) {
                $gpslat = $gps['GPSLatitude'][0].", ".$gps['GPSLatitude'][1].", ".$gps['GPSLatitude'][2];
                $lattranslated = $this->getGps($gps['GPSLatitude'], $gps['GPSLatitudeRef']);
            }
            else {
                $gpslat = '';
                $lattranslated = '';
            }
            if(is_array($gps['GPSLongitude']) && !empty($gps['GPSLongitude'])) {
                $gpslon = $gps['GPSLongitude'][0].", ".$gps['GPSLongitude'][1].", ".$gps['GPSLongitude'][2];
                $lontranslated = $this->getGps($gps['GPSLongitude'], $gps['GPSLongitudeRef']);
            }
            else {
                $gpslon = '';
                $lontranslated = '';
            }
            if(is_array($gps['GPSDestLatitude']) && !empty($gps['GPSDestLatitude'])) {
                $gpsdestlat = $gps['GPSDestLatitude'][0].", ".$gps['GPSDestLatitude'][1].", ".$gps['GPSDestLatitude'][2];
                $destlattranslated = $this->getGps($gps['GPSDestLatitude'], $gps['GPSDestLatitudeRef']);
            }
            else {
                $gpsdestlat = '';
                $destlattranslated = '';
            }
            if(is_array($gps['GPSDestLongitude']) && !empty($gps['GPSDestLongitude'])) {
                $gpsdestlon = $gps['GPSDestLongitude'][0].", ".$gps['GPSDestLongitude'][1].", ".$gps['GPSDestLongitude'][2];
                $destlontranslated = $this->getGps($gps['GPSDestLongitude'], $gps['GPSDestLongitudeRef']);
            }
            else {
                $gpsdestlon = '';
                $destlontranslated = '';
            }
            if(is_array($gps['GPSTimeStamp']) && !empty($gps['GPSTimeStamp'])) {
                $gpsts = $gps['GPSTimeStamp'][0].", ".$gps['GPSTimeStamp'][1].", ".$gps['GPSTimeStamp'][2];
                $tstrans = $gpsts; //'timetranslated';
            }
            else {
                $gpsts = '';
                $tstrans = '';
            }
            if(isset($gps['GPSAltitude']) && !empty($gps['GPSAltitude'])) {
                $alttrans = $this->formatGPSAltitude($gps['GPSAltitude']);
            }
            else {
                $alttrans = '';
            }
            $insarr = array(
                      'imageid' => $imgid,
                      'userid'=> $userid,
                      'gpslatitude' => $gpslat,
                      'latitudetranslated' => $lattranslated,
                      'gpslatituderef' => $gps['GPSLatitudeRef'],
                      'gpslongitude' => $gpslon,
                      'longitudetranslated' => $lontranslated,
                      'gpslongituderef' => $gps['GPSLongitudeRef'],
                      'gpsaltitude' => $gps['GPSAltitude'],
                      'altitudetranslated' => $alttrans,
                      'gpsaltituderef' => $gps['GPSAltitudeRef'],
                      'gpstimestamp' => $gpsts,
                      'timestamptranslated' => $tstrans,
                      'gpsimgdirectionref' => $gps['GPSImgDirectionRef'],
                      'gpsimgdirection' => $gps['GPSImgDirection'],
                      'gpssatellites' => $gps['GPSSatellites'],
                      'gpsstatus' => $gps['GPSStatus'], 
                      'gpsmeasuremode' => $gps['GPSMeasureMode'], 
                      'gpsdop' => $gps['GPSDOP'], 
                      'gpsspeedref' => $gps['GPSSpeedRef'], 
                      'gpsspeed' => $gps['GPSSpeed'], 
                      'gpstrackref' => $gps['GPSTrackRef'], 
                      'gpstrack' => $gps['GPSTrack'], 
                      'gpsmapdatum' => $gps['GPSMapDatum'], 
                      'gpsdestlatituderef' => $gps['GPSDestLatitudeRef'], 
                      'gpsdestlatitude' => $gps['GPSDestLatitude'], 
                      'destlatitudetranslated' => $destlattranslated,
                      'gpsdestlongituderef' => $gps['GPSDestLongitudeRef'], 
                      'gpsdestlongitude' => $gps['GPSDestLongitude'],
                      'destlongitudetranslated' => $destlontranslated, 
                      'gpsdestbearingref' => $gps['GPSDestBearingRef'], 
                      'gpsdestbearing' => $gps['GPSDestBearing'], 
                      'gpsdestdistanceref' => $gps['GPSDestDistanceRef'], 
                      'gpsdestdistance' => $gps['GPSDestDistance'], 
                      'gpsprocessingmethod' => $gps['GPSProcessingMethod'],  
                      'gpsareainformation' => $gps['GPSAreaInformation'], 
                      'gpsdatestamp' => $gps['GPSDateStamp'], 
                      'gpsdifferential' => $gps['GPSDifferential'], 
                      'gpshpositioningerror' => $gps['GPSHPositioningError'], 
            );
            $this->objDbVault->insertGPS($insarr);
            // var_dump($insarr);
            
        }
        else {
            return FALSE;
        }
    }
    
    private function insertIFD0($meta, $imgid, $userid) {
        if(array_key_exists('IFD0', $meta['exif'])) {
            $ifd0 = $meta['exif']['IFD0'];
            $ifd0arr = array('Make', 'Model', 'Orientation', 'XResolution', 'YResolution', 'ResolutionUnit', 'Software', 'DateTime', 'Whitepoint',
                             'PrimaryChromaticities', 'YCBCRcoefficients', 'YCBCRpositioning', 'ReferenceBlackWhite', 'Copyright', 'ExifOffset', 'HostComputer', 'Exif_IFD_Pointer',
                             'ProcessingSoftware', 'SubfileType', 'OldSubfileType', 'ImageWidth', 'ImageHeight', 'BitsPerSample', 'Compression', 'PhotometricInterpretation', 'Thresholding', 
                             'CellWidth', 'CellLength', 'FillOrder', 'DocumentName', 'ImageDescription', 'SamplesPerPixel', 'RowsPerStrip', 'MinSampleValue', 'MaxSampleValue', 'XResolution', 
                             'YResolution', 'PlanarConfiguration', 'PageName', 'XPosition', 'YPosition', 'GrayResponseUnit', 'ResolutionUnit', 'PageNumber', 'TransferFunction', 'Software',
                             'ModifyDate', 'Artist', 'HostComputer', 'Predictor', 'WhitePoint', 'PrimaryChromaticities', 'HalftoneHints', 'TileWidth', 'TileLength', 'InkSet', 'DotRange', 
                             'YCbCrCoefficients', 'YCbCrPositioning', 'ReferenceBlackWhite', 'Rating', 'RatingPercent', 'Copyright', 'IPTC-NAA', 'SEMInfo', 'ImageSourceData', 'XPTitle', 
                             'XPComment', 'XPAuthor', 'XPKeywords', 'XPSubject', 'PrintIM', 'DNGVersion', 'DNGBackwardVersion', 'UniqueCameraModel', 'LocalizedCameraModel', 'ColorMatrix1',
                             'ColorMatrix2', 'CameraCalibration1', 'CameraCalibration2', 'ReductionMatrix1', 'ReductionMatrix2', 'AnalogBalance', 'AsShotNeutral', 'AsShotWhiteXY', 'BaselineExposure',
                             'BaselineNoise', 'BaselineSharpness', 'LinearResponseLimit', 'CameraSerialNumber', 'DNGLensInfo', 'ShadowScale', 'MakerNoteSafety', 'CalibrationIlluminant1', 
                             'CalibrationIlluminant2', 'RawDataUniqueID', 'OriginalRawFileName', 'OriginalRawFileData', 'AsShotICCProfile', 'AsShotPreProfileMatrix', 'CurrentICCProfile', 
                             'CurrentPreProfileMatrix', 'ColorimetricReference', 'PanasonicTitle', 'PanasonicTitle2', 'CameraCalibrationSig', 'ProfileCalibrationSig', 'AsShotProfileName',
                             'ProfileName', 'ProfileHueSatMapDims', 'ProfileHueSatMapData1', 'ProfileHueSatMapData2', 'ProfileToneCurve', 'ProfileEmbedPolicy', 'ProfileCopyright', 
                             'ForwardMatrix1', 'ForwardMatrix2', 'PreviewApplicationName', 'PreviewApplicationVersion', 'PreviewSettingsName', 'PreviewSettingsDigest', 'PreviewColorSpace',
                             'PreviewDateTime', 'RawImageDigest', 'OriginalRawFileDigest', 'ProfileLookTableDims', 'ProfileLookTableData', 'ExifOffset', 
                            );
            foreach($ifd0arr as $keys) {
                if(!array_key_exists($keys, $ifd0)) {
                    $ifd0[$keys] = '';
                }
            }
            $insarr = array(
                      'imageid' => $imgid,
                      'userid'=> $userid,
                      'make' => $ifd0['Make'],
                      'model' => $ifd0['Model'],
                      'orientation' => $ifd0['Orientation'],
                      'xresolution' => $ifd0['XResolution'],
                      'yresolution' => $ifd0['YResolution'],
                      'resolutionunit' => $ifd0['ResolutionUnit'],
                      'software' => $ifd0['Software'],
                      'imgdatetime' => $ifd0['DateTime'],
                      'whitepoint' => $ifd0['Whitepoint'],
                      'primarychromaticities' => $ifd0['PrimaryChromaticities'],
                      'ycbcrcoefficients' => $ifd0['YCBCRcoefficients'],
                      'ycbcrpositioning' => $ifd0['YCBCRpositioning'],
                      'referenceblackwhite' => $ifd0['ReferenceBlackWhite'],
                      'copyright' => $ifd0['Copyright'],
                      'exifoffset' => $ifd0['ExifOffset'],
                      'hostcomputer' => $ifd0['HostComputer'],
                      'exif_ifd_pointer' => $ifd0['Exif_IFD_Pointer'],
                      'processingsoftware' => $ifd0['ProcessingSoftware'],
                      'subfiletype' => $ifd0['SubfileType'],
                      'oldsubfiletype' => $ifd0['OldSubfileType'],
                      'imagewidth' => $ifd0['ImageWidth'],
                      'imageheight' => $ifd0['ImageHeight'],
                      'bitspersample' => $ifd0['BitsPerSample'],
                      'compression' => $ifd0['Compression'],
                      'photometricinterpretation' => $ifd0['PhotometricInterpretation'],
                      'thresholding' => $ifd0['Thresholding'],
                      'cellwidth' => $ifd0['CellWidth'],
                      'celllength' => $ifd0['CellLength'],
                      'fillorder' => $ifd0['FillOrder'],
                      'documentname' => $ifd0['DocumentName'],
                      'imagedescription' => $ifd0['ImageDescription'],
                      'samplesperpixel' => $ifd0['SamplesPerPixel'],
                      'rowsperstrip' => $ifd0['RowsPerStrip'],
                      'minsamplevalue' => $ifd0['MinSampleValue'],
                      'maxsamplevalue' => $ifd0['MaxSampleValue'],
                      'xresolution' => $ifd0['XResolution'],
                      'yresolution' => $ifd0['YResolution'],
                      'planarconfiguration' => $ifd0['PlanarConfiguration'],
                      'pagename' => $ifd0['PageName'],
                      'xposition' => $ifd0['XPosition'],
                      'yposition' => $ifd0['YPosition'],
                      'grayresponseunit' => $ifd0['GrayResponseUnit'],
                      'resolutionunit' => $ifd0['ResolutionUnit'],
                      'pagenumber' => $ifd0['PageNumber'],
                      'transferfunction' => $ifd0['TransferFunction'],
                      'software' => $ifd0['Software'],
                      'imgdatetime' => $ifd0['ModifyDate'],
                      'artist' => $ifd0['Artist'],
                      'predictor' => $ifd0['Predictor'],
                      'whitepoint' => $ifd0['WhitePoint'],
                      'primarychromaticities' => $ifd0['PrimaryChromaticities'],
                      'halftonehints' => $ifd0['HalftoneHints'],
                      'tilewidth' => $ifd0['TileWidth'],
                      'tilelength' => $ifd0['TileLength'],
                      'inkset' => $ifd0['InkSet'],
                      'dotrange' => $ifd0['DotRange'],
                      'ycbcrcoefficients' => $ifd0['YCbCrCoefficients'],
                      'ycbcrpositioning' => $ifd0['YCbCrPositioning'],
                      'referenceblackwhite' => $ifd0['ReferenceBlackWhite'],
                      'rating' => $ifd0['Rating'],
                      'ratingpercent' => $ifd0['RatingPercent'],
                      'copyright' => $ifd0['Copyright'],
                      'iptcnaa' => $ifd0['IPTC-NAA'],
                      'seminfo' => $ifd0['SEMInfo'],
                      'imagesourcedata' => $ifd0['ImageSourceData'],
                      'xptitle' => $ifd0['XPTitle'],
                      'xpcomment' => $ifd0['XPComment'],
                      'xpauthor' => $ifd0['XPAuthor'],
                      'xpkeywords' => $ifd0['XPKeywords'],
                      'xpsubject' => $ifd0['XPSubject'],
                      'printim' => $ifd0['PrintIM'],
                      'dngversion' => $ifd0['DNGVersion'],
                      'dngbackwardversion' => $ifd0['DNGBackwardVersion'],
                      'uniquecameramodel' => $ifd0['UniqueCameraModel'],
                      'localizedcameramodel' => $ifd0['LocalizedCameraModel'],
                      'colormatrix1' => $ifd0['ColorMatrix1'],
                      'colormatrix2' => $ifd0['ColorMatrix2'],
                      'cameracalibration1' => $ifd0['CameraCalibration1'],
                      'cameracalibration2' => $ifd0['CameraCalibration2'],
                      'reductionmatrix1' => $ifd0['ReductionMatrix1'],
                      'reductionmatrix2' => $ifd0['ReductionMatrix2'],
                      'analogbalance' => $ifd0['AnalogBalance'],
                      'asshotneutral' => $ifd0['AsShotNeutral'],
                      'asshotwhitexy' => $ifd0['AsShotWhiteXY'],
                      'baselineexposure' => $ifd0['BaselineExposure'],
                      'baselinenoise' => $ifd0['BaselineNoise'],
                      'baselinesharpness' => $ifd0['BaselineSharpness'],
                      'linearresponselimit' => $ifd0['LinearResponseLimit'],
                      'cameraserialnumber' => $ifd0['CameraSerialNumber'],
                      'dnglensinfo' => $ifd0['DNGLensInfo'],
                      'shadowscale' => $ifd0['ShadowScale'],
                      'makernotesafety' => $ifd0['MakerNoteSafety'],
                      'calibrationilluminant1' => $ifd0['CalibrationIlluminant1'],
                      'calibrationilluminant2' => $ifd0['CalibrationIlluminant2'],
                      'rawdatauniqueid' => $ifd0['RawDataUniqueID'],
                      'originalrawfilename' => $ifd0['OriginalRawFileName'],
                      'originalrawfiledata' => $ifd0['OriginalRawFileData'],
                      'asshoticcprofile' => $ifd0['AsShotICCProfile'],
                      'asshotpreprofilematrix' => $ifd0['AsShotPreProfileMatrix'],
                      'currenticcprofile' => $ifd0['CurrentICCProfile'],
                      'currentpreprofilematrix' => $ifd0['CurrentPreProfileMatrix'],
                      'colorimetricreference' => $ifd0['ColorimetricReference'],
                      'panasonictitle' => $ifd0['PanasonicTitle'],
                      'panasonictitle2' => $ifd0['PanasonicTitle2'],
                      'cameracalibrationig' => $ifd0['CameraCalibrationSig'],
                      'profilecalibrationsig' => $ifd0['ProfileCalibrationSig'],
                      'asshotprofilename' => $ifd0['AsShotProfileName'],
                      'profilename' => $ifd0['ProfileName'],
                      'profilehuesatmapdims' => $ifd0['ProfileHueSatMapDims'],
                      'profilehuesatmapdata1' => $ifd0['ProfileHueSatMapData1'],
                      'profilehuesatmapdata2' => $ifd0['ProfileHueSatMapData2'],
                      'profiletonecurve' => $ifd0['ProfileToneCurve'],
                      'profileembedpolicy' => $ifd0['ProfileEmbedPolicy'],
                      'profilecopyright' => $ifd0['ProfileCopyright'],
                      'forwardmatrix1' => $ifd0['ForwardMatrix1'],
                      'forwardmatrix2' => $ifd0['ForwardMatrix2'],
                      'previewapplicationname' => $ifd0['PreviewApplicationName'],
                      'previewapplicationversion' => $ifd0['PreviewApplicationVersion'],
                      'previewsettingsname' => $ifd0['PreviewSettingsName'],
                      'previewsettingsdigest' => $ifd0['PreviewSettingsDigest'],
                      'previewcolorspace' => $ifd0['PreviewColorSpace'],
                      'previewdatetime' => $ifd0['PreviewDateTime'],
                      'rawimagedigest' => $ifd0['RawImageDigest'],
                      'originalrawfiledigest' => $ifd0['OriginalRawFileDigest'],
                      'profilelooktabledims' => $ifd0['ProfileLookTableDims'],
                      'profilelooktabledata' => $ifd0['ProfileLookTableData'],
                      'exifoffset' => $ifd0['ExifOffset'],
                      'hostcomputer' => $ifd0['HostComputer'],
                  );
            // var_dump($insarr);
            return $this->objDbVault->insertIFD0Data($insarr);
        }
        else {
            return FALSE;
        }
    }
    
    private function insertIFD1($meta, $imgid, $userid) {
        if(array_key_exists('IFD1', $meta['exif'])) {
            $ifd1 = $meta['exif']['IFD1'];
            $ifd1arr = array('ImageLength', 'StripOffsets', 'StripByteCounts', 'YCbCrSubSampling', 'ThumbnailOffset', 'ThumbnailLength', );
            foreach($ifd1arr as $keys) {
                if(!array_key_exists($keys, $ifd1)) {
                    $ifd1[$keys] = '';
                }
            }
            $insarr = array(
                      'imageid' => $imgid,
                      'userid'=> $userid,
                      'imagelength' => $ifd1['ImageLength'],
                      'stripoffsets' => $ifd1['StripOffsets'],
                      'stripbytecounts' => $ifd1['StripByteCounts'],
                      'ycbcrsubsampling' => $ifd1['YCbCrSubSampling'],
                      'thumbnailoffset' => $ifd1['ThumbnailOffset'],
                      'thumbnaillength' => $ifd1['ThumbnailLength'],
                  );
            return $this->objDbVault->insertIFD1Data($insarr);
        }
        else {
            return FALSE;
        }
    }
    
    private function insertSubIFD($meta, $imgid, $userid) {
        if(array_key_exists('SUBIFD', $meta['exif'])) {
            $subifd = $meta['exif']['SUBIFD'];
            $subifdarr = array('BlackLevelRepeatDim', 'BlackLevel', 'WhiteLevel', 'DefaultScale', 'DefaultCropOrigin', 'DefaultCropSize', 'BayerGreenSplit', 'ChromaBlurRadius', 'AntiAliasStrength',
                               'BestQualityScale', 'ActiveArea', 'MaskedAreas', 'NoiseReductionApplied', );
            foreach($subifdarr as $keys) {
                if(!array_key_exists($keys, $subifd)) {
                    $subifd[$keys] = '';
                }
            }
            $insarr = array(
                      'imageid' => $imgid,
                      'userid'=> $userid,
                      'blacklevelrepeatdim' => $subifd['BlackLevelRepeatDim'],
                      'blacklevel' => $subifd['BlackLevel'],
                      'whitelevel' => $subifd['WhiteLevel'],
                      'defaultscale' => $subifd['DefaultScale'],
                      'defaultcroporigin' => $subifd['DefaultCropOrigin'],
                      'defaultcropsize' => $subifd['DefaultCropSize'],
                      'bayergreensplit' => $subifd['BayerGreenSplit'],
                      'chromablurradius' => $subifd['ChromaBlurRadius'],
                      'antialiasstrength' => $subifd['AntiAliasStrength'],
                      'bestqualityscale' => $subifd['BestQualityScale'],
                      'activearea' => $subifd['ActiveArea'],
                      'maskedareas' => $subifd['MaskedAreas'],
                      'noisereductionapplied' => $subifd['NoiseReductionApplied'],
                  );
            return $this->objDbVault->insertSubIFDData($insarr);
        }
        else {
            return FALSE;
        }
    }
    
    private function insertThumbnail($meta, $imgid, $userid) {
        if(array_key_exists('THUMBNAIL', $meta['exif'])) {
            $thumb = $meta['exif']['THUMBNAIL'];
            $thumbarr = array('Compression', 'XResolution', 'YResolution', 'ResolutionUnit', 'JPEGInterchangeFormat', 'JPEGInterchangeFormatLength', 'YCbCrPositioning', );
            foreach($thumbarr as $keys) {
                if(!array_key_exists($keys, $thumb)) {
                    $thumb[$keys] = '';
                }
            }
            $insarr = array(
                      'imageid' => $imgid,
                      'userid'=> $userid,
                      'compression' => $thumb['Compression'],
                      'xresolution' => $thumb['XResolution'],
                      'yresolution' => $thumb['YResolution'],
                      'resolutionunit' => $thumb['ResolutionUnit'],
                      'jpeginterchangeformat' => $thumb['JPEGInterchangeFormat'],
                      'jpeginterchangeformatlength' => $thumb['JPEGInterchangeFormatLength'],
                      'ycbcrpositioning' => $thumb['YCbCrPositioning'],
                  );
            return $this->objDbVault->insertThumbData($insarr);
        }
        else {
            return FALSE;
        }
    }
    
    private function insertExif($meta, $imgid, $userid) {
        if(array_key_exists('EXIF', $meta['exif'])) {
            $exif = $meta['exif']['EXIF'];
            $exifarr = array('ApplicationNotes', 'ExposureTime', 'FNumber', 'ExposureProgram', 'SpectralSensitivity', 'ISO', 'ISOSpeedRatings', 'PhotographicSensitivity',
                             'TimeZoneOffset', 'SelfTimerMode', 'SensitivityType', 'StandardOutputSensitivity', 'RecommendedExposureIndex', 'ISOSpeed', 'ISOSpeedLatitudeyyy', 
                             'ISOSpeedLatitudezzz', 'ExifVersion', 'DateTimeOriginal', 'CreateDate', 'ComponentsConfiguration', 'CompressedBitsPerPixel', 'ShutterSpeedValue', 
                             'ApertureValue', 'BrightnessValue', 'ExposureCompensation', 'ExposureBiasValue', 'MaxApertureValue', 'SubjectDistance', 'MeteringMode', 'LightSource', 
                             'Flash', 'FocalLength', 'ImageNumber', 'SecurityClassification', 'ImageHistory', 'SubjectArea', 'SensingMethod', 'UserComment', 'SubSecTime', 'SubSecTimeOriginal', 
                             'SubSecTimeDigitized', 'FlashpixVersion', 'ColorSpace', 'PixelXDimension', 'PixelYDimension', 'ExifImageWidth', 'ExifImageHeight', 'RelatedSoundFile', 
                             'FlashEnergy', 'FocalPlaneXResolution', 'FocalPlaneYResolution', 'FocalPlaneResolutionUnit', 'SubjectLocation', 'ExposureIndex', 'FileSource', 
                             'SceneType', 'CFAPattern', 'CustomRendered', 'ExposureMode', 'WhiteBalance', 'DigitalZoomRatio', 'FocalLengthIn35mmFormat', 'SceneCaptureType', 'GainControl', 
                             'Contrast', 'Saturation', 'Sharpness', 'SubjectDistanceRange', 'ImageUniqueID', 'OwnerName', 'CameraOwnerName', 'BodySerialNumber', 'SerialNumber', 
                             'LensInfo', 'LensMake', 'LensModel', 'LensSerialNumber', 'Gamma', 'Padding', 'OffsetSchema', 'OwnerName', 'Lens', 'RawFile', 'Converter', 'WhiteBalance', 
                             'Exposure', 'Shadows', 'Brightness', 'Contrast', 'Saturation', 'Sharpness', 'Smoothness', 'MoireFilter', );
            foreach($exifarr as $keys) {
                if(!array_key_exists($keys, $exif)) {
                    $exif[$keys] = '';
                }
            }
            $insarr = array(
                      'imageid' => $imgid,
                      'userid'=> $userid,
                      'applicationnotes' => $exif['ApplicationNotes'],
                      'exposuretime' => $exif['ExposureTime'],
                      'fnumber' => $exif['FNumber'],
                      'exposureprogram' => $exif['ExposureProgram'],
                      'spectralsensitivity' => $exif['SpectralSensitivity'],
                      'iso' => $exif['ISO'],
                      'isospeedratings' => $exif['ISOSpeedRatings'],
                      'photographicsensitivity' => $exif['PhotographicSensitivity'],
                      'timezoneoffset' => $exif['TimeZoneOffset'],
                      'selftimermode' => $exif['SelfTimerMode'],
                      'sensitivitytype' => $exif['SensitivityType'],
                      'standardoutputsensitivity' => $exif['StandardOutputSensitivity'],
                      'recommendedexposureindex' => $exif['RecommendedExposureIndex'],
                      'isospeed' => $exif['ISOSpeed'],
                      'isospeedlatitudeyyy' => $exif['ISOSpeedLatitudeyyy'],
                      'isospeedlatitudezzz' => $exif['ISOSpeedLatitudezzz'],
                      'exifversion' => $exif['ExifVersion'],
                      'datetimeoriginal' => $exif['DateTimeOriginal'],
                      'createdate' => $exif['CreateDate'],
                      'componentsconfiguration' => $exif['ComponentsConfiguration'],
                      'compressedbitsperpixel' => $exif['CompressedBitsPerPixel'],
                      'shutterspeedvalue' => $exif['ShutterSpeedValue'],
                      'aperturevalue' => $exif['ApertureValue'],
                      'brightnesvalue' => $exif['BrightnessValue'],
                      'exposurecompensation' => $exif['ExposureCompensation'],
                      'exposurebiasvalue' => $exif['ExposureBiasValue'],
                      'maxaperturevalue' => $exif['MaxApertureValue'],
                      'subjectdistance' => $exif['SubjectDistance'],
                      'meteringmode' => $exif['MeteringMode'],
                      'lightsource' => $exif['LightSource'],
                      'flash' => $exif['Flash'],
                      'focallength' => $exif['FocalLength'],
                      'imagenumber' => $exif['ImageNumber'],
                      'securityclassification' => $exif['SecurityClassification'],
                      'imagehistory' => $exif['ImageHistory'],
                      'subjectarea' => $exif['SubjectArea'],
                      'sensingmethod' => $exif['SensingMethod'],
                      'usercomment' => $exif['UserComment'],
                      'subsectime' => $exif['SubSecTime'],
                      'subsectimeoriginal' => $exif['SubSecTimeOriginal'],
                      'subsectimedigitized' => $exif['SubSecTimeDigitized'],
                      'flashpixversion' => $exif['FlashpixVersion'],
                      'colorspace' => $exif['ColorSpace'],
                      'pixelxdimension' => $exif['PixelXDimension'],
                      'pixelydimension' => $exif['PixelYDimension'],
                      'exifimagewidth' => $exif['ExifImageWidth'],
                      'exifimageheight' => $exif['ExifImageHeight'],
                      'relatedsoundfile' => $exif['RelatedSoundFile'],
                      'flashenergy' => $exif['FlashEnergy'],
                      'focalplanexresolution' => $exif['FocalPlaneXResolution'],
                      'focalplaneyresolution' => $exif['FocalPlaneYResolution'],
                      'focalplaneresolutionunit' => $exif['FocalPlaneResolutionUnit'],
                      'subjectlocation' => $exif['SubjectLocation'],
                      'exposureindex' => $exif['ExposureIndex'],
                      'filesource' => $exif['FileSource'],
                      'scenetype' => $exif['SceneType'],
                      'cfapattern' => $exif['CFAPattern'],
                      'customrendered' => $exif['CustomRendered'],
                      'exposuremode' => $exif['ExposureMode'],
                      'whitebalance' => $exif['WhiteBalance'],
                      'digitalzoomratio' => $exif['DigitalZoomRatio'],
                      'focallengthin35mmformat' => $exif['FocalLengthIn35mmFormat'],
                      'scenecapturetype' => $exif['SceneCaptureType'],
                      'gaincontrol' => $exif['GainControl'],
                      'contrast' => $exif['Contrast'],
                      'saturation' => $exif['Saturation'],
                      'sharpness' => $exif['Sharpness'],
                      'subjectdistancerange' => $exif['SubjectDistanceRange'],
                      'imageuniqueid' => $exif['ImageUniqueID'],
                      'ownername' => $exif['OwnerName'],
                      'cameraownername' => $exif['CameraOwnerName'],
                      'bodyserialnumber' => $exif['BodySerialNumber'],
                      'serialnumber' => $exif['SerialNumber'],
                      'lensinfo' => $exif['LensInfo'],
                      'lensmake' => $exif['LensMake'],
                      'lensmodel' => $exif['LensModel'],
                      'lensserialnumber' => $exif['LensSerialNumber'],
                      'gamma' => $exif['Gamma'],
                      'padding' => $exif['Padding'],
                      'offsetschema' => $exif['OffsetSchema'],
                      'lens' => $exif['Lens'],
                      'rawfile' => $exif['RawFile'],
                      'converter' => $exif['Converter'],
                      'exposure' => $exif['Exposure'],
                      'shadows' => $exif['Shadows'],
                      'brightness' => $exif['Brightness'],
                      'smoothness' => $exif['Smoothness'],
                      'moirefilter' => $exif['MoireFilter'],
                  );
            return $this->objDbVault->insertExifData($insarr);
        }
        else {
            return FALSE;
        }
    } 
    
    private function getGps($exifCoord, $hemi) {
        $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;
        $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

        return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
    }

    private function gps2Num($coordPart) {
        $parts = explode('/', $coordPart);
        if (count($parts) <= 0) {
            return 0;
        }
        if (count($parts) == 1) {
            return $parts[0];
        }
        
        return floatval($parts[0]) / floatval($parts[1]);
    }
    
    private function formatGPSAltitude($alt) {
        $altarr = explode("/", $alt);
        $dividend = floatval($altarr[0]);
        $divisor = floatval($altarr[1]);
        $alt = $dividend / $divisor;
        
        return $alt;
    }
    
    private function getExifRDF($image, $userid) {
        $fname = basename($image);
        $fname = explode(".", $fname);
        $file = $this->objConfig->getcontentBasePath()."imagevaultrdf/".$userid."/".$fname[0].".rdf";
        if(!file_exists($this->objConfig->getcontentBasePath()."imagevaultrdf/")) {
           mkdir($this->objConfig->getcontentBasePath()."imagevaultrdf/", 0777);
        }
        if(!file_exists($this->objConfig->getcontentBasePath()."imagevaultrdf/".$userid."/")) {
            mkdir($this->objConfig->getcontentBasePath()."imagevaultrdf/".$userid."/", 0777);
        }
        // if(strtolower($fname[1]) == 'cr2' || strtolower($fname[1]) == 'nef') {
        $hash = sha1(file_get_contents($image));
        $user = $this->objUser->userName($userid); 
        @exec("exiftool -keywords=~$hash -keywords=$user $image", $return);
        //} 
        @exec("exiftool -x Directory -x ExifToolVersion -x FilePermissions -X PreviewImage -X JpgFromRaw -H -b -X $image > $file", $return);
        return $fname;
    }

}
?>
