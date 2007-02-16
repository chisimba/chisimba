<?
/**
* Class to detemine which subfolder a file should be placed in
*
* It does this based on an analysis of either:
* 1) mimetype
* 2) extension
*
* @author Tohir Solomons
*/
class filefolder extends object
{

    /**
    * Constructor
    */
    function init()
    {
        $this->objFileParts =& $this->getObject('fileparts', 'files');
    }
    
    /**
    * Method to determine which sub folder a file should be placed in
    *
    * Note: This function is pretty hardcoded in determining the result
    * More dynamic options are welcome.
    *
    * @param string $name Name of the File
    * @param string $mimetype Mimetype of the File
    * @return string Sub Folder file must be placed in
    */
    function getFileFolder($name, $mimetype)
    {
        $mimeSplit = explode ('/', $mimetype);
        $extension = $this->objFileParts->getExtension($name);
        
        // Possible Folders: 'images', 'audio', 'video', 'documents', 'flash', 'freemind', 'archives', 'other', 'obj3d', 'scripts'
        
        // Check by Full Mimetype
        switch ($mimetype)
        {
            case 'application/x-shockwave-flash': return 'flash'; break;
            case 'application/x-shockwave-flash2-preview': return 'flash'; break;
            case 'application/vnd.ms-excel': return 'documents'; break;
            case 'application/msword': return 'documents'; break;
            case 'application/powerpoint': return 'documents'; break;
            case 'application/vnd.ms-powerpoint': return 'documents'; break;
            case 'application/pdf': return 'documents'; break;
            case 'application/x-rar-compressed' : return 'archives'; break;
            case 'application/x-javascript' : return 'scripts'; break;
            case 'text/x-sql' : return 'scripts'; break;
            case 'text/css' : return 'scripts'; break;
            default : break;
        }
        
        // Check Second Part of Mimetype
        /* // Not Checked at the moment
        switch ($mimeSplit[1])
        {
            default : break;
        }*/
        
        // Check by extension
        switch ($extension)
        {
            case 'wbmp':
                return 'images'; break;
            case 'doc': // Microsoft Office
            case 'xls':
            case 'xlt':
            case 'ppt':
            case 'pps':
            case 'odb': // Open Office 2
            case 'odf':
            case 'odg':
            case 'odm':
            case 'odp':
            case 'odt':
            case 'otg':
            case 'oth':
            case 'otp':
            case 'ots':
            case 'ott':
            case 'sxc': // Open Office
            case 'sxd':
            case 'sxi':
            case 'sxw':
            case 'mdb': // MS Access Database
            case 'vsd': // Visio
            case 'chm': // Windows Help Files
            case 'rss': // RSS Feeds
                return 'documents'; break;
            case 'mm': 
                return 'freemind'; break;
            case 'zip': // Archives
            case 'tar':
            case 'gz':
            case 'rar':
            case 'arj':
            case 'ace':
                return 'archives'; break;
            case 'ogg':
                return 'audio'; break;
            case 'rm';
            case '3gp':
                return 'video'; break;
            case 'wrl': // VRML
            case 'vrml':
            case 'obj':
                return 'obj3d'; break;
            case 'php': // Programming Scripts
            case 'css':
            case 'js':
            case 'sql':
            case 'java':
            case 'py':
            case 'pl':
            case 'cgi':
            case 'jsp':
            case 'asp':
            case 'aspx':
            case 'cfm':
            case 'xml':
                return 'scripts'; break;
            default:
                break;
        }
        
        // Check First Part of Mimetype
        switch ($mimeSplit[0])
        {
            case 'image': return 'images'; break;
            case 'audio': return 'audio'; break;
            case 'video': return 'video'; break;
            case 'text': return 'documents'; break;
            default : break;
        }
        
        // If no other folder is possible, return 'other'
        return 'other';
    
    }
     

}

?>