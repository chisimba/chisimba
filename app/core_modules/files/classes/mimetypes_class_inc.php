<?php
/**
* 
* Class to get the mimetype of a file based on its extension.
* Useful if mime magic is enabled.
*
* @author Tohir Solomons
*
* Adapted from: 
* http://pccepa3.if.usp.br/trac/demi/browser/trunk/elgg/lib/filelib.php
*/
class mimetypes extends object 
{

    /**
    * @var array $mimeinfo Array containing all the extensions
    */
    private $mimeinfo;
    
    /**
    * Constructor
    */
    function init()
    {
        $this->get_mimetype_array();
    }
    
    /**
    * Method to get the mimetype of a file
    * @param string $filename File Name
    * @return string $mimetype
    */
    public function getMimeType($fileName)
    {
        return $this->mimeinfo($fileName, 'type');
    }
    
    /**
    * Method to get either the mimetype or icon from the array of mimetypes
    * @param string $filename Name of the File
    * @param string $element either type or icon
    */
    public function mimeinfo($filename, $element='type') {
        
        if (eregi('\.([a-z0-9]+)$', $filename, $match)) {
            if (isset($this->mimeinfo[strtolower($match[1])][$element])) {
                return $this->mimeinfo[strtolower($match[1])][$element];
            } else {
                return $this->mimeinfo['xxx'][$element];   // By default
            }
        } else {
            return $this->mimeinfo['xxx'][$element];   // By default
        }
    }

    /**
    * Method to generate the mimetype array
    *
    * The icon part is from the original, and could be made more useful,
    * but don't use or bother with this part for the moment.
    */
    private function get_mimetype_array() {
        
        $this->mimeinfo = array (
            'xxx'  => array ('type'=>'document/unknown', 'icon'=>'unknown.gif'),
            '3gp'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
            'ai'   => array ('type'=>'application/postscript', 'icon'=>'image.gif'),
            'aif'  => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
            'aiff' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
            'aifc' => array ('type'=>'audio/x-aiff', 'icon'=>'audio.gif'),
            'applescript'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'asc'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'asm'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'au'   => array ('type'=>'audio/au', 'icon'=>'audio.gif'),
            'avi'  => array ('type'=>'video/x-ms-wm', 'icon'=>'avi.gif'),
            'bmp'  => array ('type'=>'image/bmp', 'icon'=>'image.gif'),
            'c'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'cct'  => array ('type'=>'shockwave/director', 'icon'=>'flash.gif'),
            'cpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'cs'   => array ('type'=>'application/x-csh', 'icon'=>'text.gif'),
            'css'  => array ('type'=>'text/css', 'icon'=>'text.gif'),
            'dv'   => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
            'dmg'  => array ('type'=>'application/octet-stream', 'icon'=>'dmg.gif'),
            'doc'  => array ('type'=>'application/msword', 'icon'=>'word.gif'),
            'dcr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
            'dif'  => array ('type'=>'video/x-dv', 'icon'=>'video.gif'),
            'dir'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
            'dxr'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
            'eps'  => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
            'gif'  => array ('type'=>'image/gif', 'icon'=>'image.gif'),
            'gtar' => array ('type'=>'application/x-gtar', 'icon'=>'zip.gif'),
            'tgz'  => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
            'gz'   => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
            'gzip' => array ('type'=>'application/g-zip', 'icon'=>'zip.gif'),
            'h'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'hpp'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'hqx'  => array ('type'=>'application/mac-binhex40', 'icon'=>'zip.gif'),
            'html' => array ('type'=>'text/html', 'icon'=>'html.gif'),
            'htm'  => array ('type'=>'text/html', 'icon'=>'html.gif'),
            'java' => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'jcb'  => array ('type'=>'text/xml', 'icon'=>'jcb.gif'),
            'jcl'  => array ('type'=>'text/xml', 'icon'=>'jcl.gif'),
            'jcw'  => array ('type'=>'text/xml', 'icon'=>'jcw.gif'),
            'jmt'  => array ('type'=>'text/xml', 'icon'=>'jmt.gif'),
            'jmx'  => array ('type'=>'text/xml', 'icon'=>'jmx.gif'),
            'jpe'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
            'jpeg' => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
            'jpg'  => array ('type'=>'image/jpeg', 'icon'=>'image.gif'),
            'jqz'  => array ('type'=>'text/xml', 'icon'=>'jqz.gif'),
            'js'   => array ('type'=>'application/x-javascript', 'icon'=>'text.gif'),
            'latex'=> array ('type'=>'application/x-latex', 'icon'=>'text.gif'),
            'm'    => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'mov'  => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
            'movie'=> array ('type'=>'video/x-sgi-movie', 'icon'=>'video.gif'),
            'm3u'  => array ('type'=>'audio/x-mpegurl', 'icon'=>'audio.gif'),
            'mp3'  => array ('type'=>'audio/mp3', 'icon'=>'audio.gif'),
            'mp4'  => array ('type'=>'video/mp4', 'icon'=>'video.gif'),
            'mpeg' => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
            'mpe'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),
            'mpg'  => array ('type'=>'video/mpeg', 'icon'=>'video.gif'),

            'odt'  => array ('type'=>'application/vnd.oasis.opendocument.text', 'icon'=>'odt.gif'),
            'ott'  => array ('type'=>'application/vnd.oasis.opendocument.text-template', 'icon'=>'odt.gif'),
            'oth'  => array ('type'=>'application/vnd.oasis.opendocument.text-web', 'icon'=>'odt.gif'),
            'odm'  => array ('type'=>'application/vnd.oasis.opendocument.text-master', 'icon'=>'odt.gif'),
            'odg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics', 'icon'=>'odt.gif'),
            'otg'  => array ('type'=>'application/vnd.oasis.opendocument.graphics-template', 'icon'=>'odt.gif'),
            'odp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation', 'icon'=>'odt.gif'),
            'otp'  => array ('type'=>'application/vnd.oasis.opendocument.presentation-template', 'icon'=>'odt.gif'),
            'ods'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet', 'icon'=>'odt.gif'),
            'ots'  => array ('type'=>'application/vnd.oasis.opendocument.spreadsheet-template', 'icon'=>'odt.gif'),
            'odc'  => array ('type'=>'application/vnd.oasis.opendocument.chart', 'icon'=>'odt.gif'),
            'odf'  => array ('type'=>'application/vnd.oasis.opendocument.formula', 'icon'=>'odt.gif'),
            'odb'  => array ('type'=>'application/vnd.oasis.opendocument.database', 'icon'=>'odt.gif'),
            'odi'  => array ('type'=>'application/vnd.oasis.opendocument.image', 'icon'=>'odt.gif'),

            'pct'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
            'pdf'  => array ('type'=>'application/pdf', 'icon'=>'pdf.gif'),
            'php'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'pic'  => array ('type'=>'image/pict', 'icon'=>'image.gif'),
            'pict' => array ('type'=>'image/pict', 'icon'=>'image.gif'),
            'png'  => array ('type'=>'image/png', 'icon'=>'image.gif'),
            'pps'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
            'ppt'  => array ('type'=>'application/vnd.ms-powerpoint', 'icon'=>'powerpoint.gif'),
            'ps'   => array ('type'=>'application/postscript', 'icon'=>'pdf.gif'),
            'qt'   => array ('type'=>'video/quicktime', 'icon'=>'video.gif'),
            'ra'   => array ('type'=>'audio/x-realaudio', 'icon'=>'audio.gif'),
            'ram'  => array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
            'rhb'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
            'rm'   => array ('type'=>'audio/x-pn-realaudio', 'icon'=>'audio.gif'),
            'rtf'  => array ('type'=>'text/rtf', 'icon'=>'text.gif'),
            'rtx'  => array ('type'=>'text/richtext', 'icon'=>'text.gif'),
            'sh'   => array ('type'=>'application/x-sh', 'icon'=>'text.gif'),
            'sit'  => array ('type'=>'application/x-stuffit', 'icon'=>'zip.gif'),
            'smi'  => array ('type'=>'application/smil', 'icon'=>'text.gif'),
            'smil' => array ('type'=>'application/smil', 'icon'=>'text.gif'),
            'sqt'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
            'svg'  => array ('type'=>'image/svg+xml', 'icon'=>'image.gif'),
            'svgz' => array ('type'=>'image/svg+xml', 'icon'=>'image.gif'),
            'swa'  => array ('type'=>'application/x-director', 'icon'=>'flash.gif'),
            'swf'  => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),
            'swfl' => array ('type'=>'application/x-shockwave-flash', 'icon'=>'flash.gif'),

            'sxw'  => array ('type'=>'application/vnd.sun.xml.writer', 'icon'=>'odt.gif'),
            'stw'  => array ('type'=>'application/vnd.sun.xml.writer.template', 'icon'=>'odt.gif'),
            'sxc'  => array ('type'=>'application/vnd.sun.xml.calc', 'icon'=>'odt.gif'),
            'stc'  => array ('type'=>'application/vnd.sun.xml.calc.template', 'icon'=>'odt.gif'),
            'sxd'  => array ('type'=>'application/vnd.sun.xml.draw', 'icon'=>'odt.gif'),
            'std'  => array ('type'=>'application/vnd.sun.xml.draw.template', 'icon'=>'odt.gif'),
            'sxi'  => array ('type'=>'application/vnd.sun.xml.impress', 'icon'=>'odt.gif'),
            'sti'  => array ('type'=>'application/vnd.sun.xml.impress.template', 'icon'=>'odt.gif'),
            'sxg'  => array ('type'=>'application/vnd.sun.xml.writer.global', 'icon'=>'odt.gif'),
            'sxm'  => array ('type'=>'application/vnd.sun.xml.math', 'icon'=>'odt.gif'),

            'tar'  => array ('type'=>'application/x-tar', 'icon'=>'zip.gif'),
            'tif'  => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
            'tiff' => array ('type'=>'image/tiff', 'icon'=>'image.gif'),
            'tex'  => array ('type'=>'application/x-tex', 'icon'=>'text.gif'),
            'texi' => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
            'texinfo' => array ('type'=>'application/x-texinfo', 'icon'=>'text.gif'),
            'tsv'  => array ('type'=>'text/tab-separated-values', 'icon'=>'text.gif'),
            'txt'  => array ('type'=>'text/plain', 'icon'=>'text.gif'),
            'wav'  => array ('type'=>'audio/wav', 'icon'=>'audio.gif'),
            'wmv'  => array ('type'=>'video/x-ms-wmv', 'icon'=>'avi.gif'),
            'asf'  => array ('type'=>'video/x-ms-asf', 'icon'=>'avi.gif'),
            'xls'  => array ('type'=>'application/vnd.ms-excel', 'icon'=>'excel.gif'),
            'xml'  => array ('type'=>'application/xml', 'icon'=>'xml.gif'),
            'xsl'  => array ('type'=>'text/xml', 'icon'=>'xml.gif'),
            'zip'  => array ('type'=>'application/zip', 'icon'=>'zip.gif'),

            'asx'  => array ('type'=>'video/x-ms-asf', 'icon'=>'unknown.gif'),
            'bcpio' => array ('type'=>'application/x-bcpio', 'icon'=>'unknown.gif'),
            'cdf'  => array ('type'=>'application/x-netcdf', 'icon'=>'unknown.gif'),
            'cpio' => array ('type'=>'application/x-cpio', 'icon'=>'unknown.gif'),
            'cpt'  => array ('type'=>'application/mac-compactpro', 'icon'=>'unknown.gif'),
            'csh'  => array ('type'=>'application/x-csh', 'icon'=>'unknown.gif'),
            'dtd'  => array ('type'=>'text/xml', 'icon'=>'unknown.gif'),
            'dvi'  => array ('type'=>'application/x-dvi', 'icon'=>'unknown.gif'),
            'etx'  => array ('type'=>'text/x-setext', 'icon'=>'unknown.gif'),
            'evy'  => array ('type'=>'application/x-envoy', 'icon'=>'unknown.gif'),
            'fif'  => array ('type'=>'application/fractals', 'icon'=>'unknown.gif'),
            'hdf'  => array ('type'=>'application/x-hdf', 'icon'=>'unknown.gif'),
            'hpx'  => array ('type'=>'application/mac-binhex40', 'icon'=>'unknown.gif'),
            'ice'  => array ('type'=>'x-conference/x-cooltalk', 'icon'=>'unknown.gif'),
            'ief'  => array ('type'=>'image/ief', 'icon'=>'unknown.gif'),
            'iges' => array ('type'=>'model/iges', 'icon'=>'unknown.gif'),
            'igs'  => array ('type'=>'model/iges', 'icon'=>'unknown.gif'),
            'isv'  => array ('type'=>'bws-internal/intrasrv-urlencoded', 'icon'=>'unknown.gif'),
            'jfm'  => array ('type'=>'bws-internal/intrasrv-form', 'icon'=>'unknown.gif'),
            'jrp'  => array ('type'=>'bws-internal/intrasrv-report', 'icon'=>'unknown.gif'),
            'kar'  => array ('type'=>'audio/midi', 'icon'=>'unknown.gif'),
            'ls'   => array ('type'=>'application/x-javascript', 'icon'=>'unknown.gif'),
            'man'  => array ('type'=>'application/x-troff-man', 'icon'=>'unknown.gif'),
            'me'   => array ('type'=>'application/x-troff-me', 'icon'=>'unknown.gif'),
            'mesh' => array ('type'=>'model/mesh', 'icon'=>'unknown.gif'),
            'mid'  => array ('type'=>'audio/midi', 'icon'=>'unknown.gif'),
            'midi' => array ('type'=>'audio/midi', 'icon'=>'unknown.gif'),
            'mif'  => array ('type'=>'application/x-mif', 'icon'=>'unknown.gif'),
            'mocha' => array ('type'=>'application/x-javascript', 'icon'=>'unknown.gif'),
            'mp2'  => array ('type'=>'audio/mpeg', 'icon'=>'unknown.gif'),
            'mpga' => array ('type'=>'audio/mpeg', 'icon'=>'unknown.gif'),
            'ms'   => array ('type'=>'application/x-troff-ms', 'icon'=>'unknown.gif'),
            'msh'  => array ('type'=>'model/mesh', 'icon'=>'unknown.gif'),
            'nc'   => array ('type'=>'application/x-netcdf', 'icon'=>'unknown.gif'),
            'oda'  => array ('type'=>'application/oda', 'icon'=>'unknown.gif'),
            'ogg'  => array ('type'=>'application/ogg', 'icon'=>'unknown.gif'),
            'pac'  => array ('type'=>'application/x-ns-proxy-autoconfig', 'icon'=>'unknown.gif'),
            'pbm'  => array ('type'=>'image/x-portable-bitmap', 'icon'=>'unknown.gif'),
            'pdb'  => array ('type'=>'chemical/x-pdb', 'icon'=>'unknown.gif'),
            'pgm'  => array ('type'=>'image/x-portable-graymap', 'icon'=>'unknown.gif'),
            'php3' => array ('type'=>'application/x-httpd-php3', 'icon'=>'unknown.gif'),
            'msql2' => array ('type'=>'application/x-httpd-php-msql2', 'icon'=>'unknown.gif'),
            'phtml' => array ('type'=>'application/x-httpd-php', 'icon'=>'unknown.gif'),
            'pnm'  => array ('type'=>'image/x-portable-anymap', 'icon'=>'unknown.gif'),
            'ppm'  => array ('type'=>'image/x-portable-pixmap', 'icon'=>'unknown.gif'),
            'ras'  => array ('type'=>'image/x-cmu-raster', 'icon'=>'unknown.gif'),
            'rgb'  => array ('type'=>'image/x-rgb', 'icon'=>'unknown.gif'),
            'roff' => array ('type'=>'application/x-troff', 'icon'=>'unknown.gif'),
            'rpm'  => array ('type'=>'audio/x-pn-realaudio-plugin', 'icon'=>'unknown.gif'),
            'sgm'  => array ('type'=>'text/x-sgml', 'icon'=>'unknown.gif'),
            'sgml' => array ('type'=>'text/x-sgml', 'icon'=>'unknown.gif'),
            'shar' => array ('type'=>'application/x-shar', 'icon'=>'unknown.gif'),
            'silo' => array ('type'=>'model/mesh', 'icon'=>'unknown.gif'),
            'skd'  => array ('type'=>'application/x-koan', 'icon'=>'unknown.gif'),
            'skm'  => array ('type'=>'application/x-koan', 'icon'=>'unknown.gif'),
            'skp'  => array ('type'=>'application/x-koan', 'icon'=>'unknown.gif'),
            'skt'  => array ('type'=>'application/x-koan', 'icon'=>'unknown.gif'),
            'snd'  => array ('type'=>'audio/basic', 'icon'=>'unknown.gif'),
            'src'  => array ('type'=>'application/x-wais-source', 'icon'=>'unknown.gif'),
            'sv4cpio' => array ('type'=>'application/x-sv4cpio', 'icon'=>'unknown.gif'),
            'sv4crc'=> array ('type'=>'application/x-sv4crc', 'icon'=>'unknown.gif'),
            't'    => array ('type'=>'application/x-troff', 'icon'=>'unknown.gif'),
            'tcl'  => array ('type'=>'application/x-tcl', 'icon'=>'unknown.gif'),
            'text' => array ('type'=>'text/plain', 'icon'=>'unknown.gif'),
            'tr'   => array ('type'=>'application/x-troff', 'icon'=>'unknown.gif'),
            'tsp'  => array ('type'=>'application/dsptype', 'icon'=>'unknown.gif'),
            'ustar' => array ('type'=>'application/x-ustar', 'icon'=>'unknown.gif'),
            'vcd'  => array ('type'=>'application/x-cdlink', 'icon'=>'unknown.gif'),
            'vox'  => array ('type'=>'audio/voxware', 'icon'=>'unknown.gif'),
            'vrml' => array ('type'=>'model/vrml', 'icon'=>'unknown.gif'),
            'wax'  => array ('type'=>'audio/x-ms-wax', 'icon'=>'unknown.gif'),
            'wm'   => array ('type'=>'video/x-ms-wm', 'icon'=>'unknown.gif'),
            'wma'  => array ('type'=>'audio/x-ms-wma', 'icon'=>'unknown.gif'),
            'wmd'  => array ('type'=>'application/x-ms-wmd', 'icon'=>'unknown.gif'),
            'wmx'  => array ('type'=>'video/x-ms-wmx', 'icon'=>'unknown.gif'),
            'wmz'  => array ('type'=>'application/x-ms-wmz', 'icon'=>'unknown.gif'),
            'wrl'  => array ('type'=>'model/vrml', 'icon'=>'unknown.gif'),
            'wvx'  => array ('type'=>'video/x-ms-wvx', 'icon'=>'unknown.gif'),
            'xbm'  => array ('type'=>'image/x-xbitmap', 'icon'=>'unknown.gif'),
            'xpm'  => array ('type'=>'image/x-xpixmap', 'icon'=>'unknown.gif'),
            'xwd'  => array ('type'=>'image/x-xwindowdump', 'icon'=>'unknown.gif'),
            'xyz'  => array ('type'=>'chemical/x-pdb', 'icon'=>'unknown.gif'),
            'z'    => array ('type'=>'application/x-compress', 'icon'=>'unknown.gif'),
        );
    }

    
    
} // end of class
?>