<?php

/**
* 
* A file upload class to use with the KEWL.NextGen modules
* requiring upload capability. It provides methods as abstractions 
* of all the parameters of the uploaded file.
* 
* Useage:
*
* @version $Id$
* @author Derek Keats
* @copyright 2003 GNU GPL
**/

class upload extends object
{

    /**
    * @var string $uploadFolder The folder to upload into. This 
    * can be set before doing the upload to upload to any folder.
    * This is an absolute filesystem path 
    * (e.g. /var/www/html/nextgen/mypath or
    *  E:\Inetpub\cvsCheckouts\nextgen\mypath\)
    */
    var $uploadFolder;
    
    /**
    * @var string $overWrite TRUE|FALSE Whether or not to overwrite existing files
    */
    var $overWrite;
    
    /**
    * @var string $createFolder Whether or not to create the uploadFolder if 
    * it does not exist.
    */
    var $createFolder;
    
    /**
    * @var array $permittedTypes The permitted file types
    */
    var $permittedTypes = array();
    
    /**
    * @var string $fullFilePath THe full filesystem path to the file
    */
    var $fullFilePath;
    
    /**
    * @var string $name The name of the uploaded file
    */
    var $name;
    
    /**
    * @var string $size The file size of the uploaded file
    */
    var $size;
    
    /**
    * @var int $maxSize The maximum permitted upload size in bytes
    */
    var $maxSize;
    
    /**
    * @var string $type The mime type of the uploaded file
    */
    var $type;
    
    /**
    * @var string $uploadType Upload type can be used to check for
    * new upload or a replacement and then if using a database you
    * can either update or insert accordingly
    */
    var $uploadType;
    
    /**
    * @var boolean $isError TRUE|FALSE
    */
    var $isError;
    
    
    /**
    * Standard init method. It sets the default uploadFolder and
    * instantiates commonly used classes.
    */
    function init()
    {
        //Create an instance of the User object
        $this->objUser =  & $this->getObject("user", "security");
        //Instantiate the language object
        $this->objLanguage = & $this->getObject('language', 'language');
        //Set the permitted file types
        $this->permittedTypes = array("mp3");
        //Set the default max filesize
        $this->maxSize=15000000; //15m override if needed
        //Set the default overwrite
        $this->overWrite = FALSE;

    }
    
    /**
    * Method to do the upload. It performs some tests and then
    * moves the uploaded file to its destination if everything is
    * OK. It performs the folowing tests as indicated by the 
    * numbers at the end of the line:
    * 
    * 1. Check if the file was uploaded OK
    * 2. Check the extension if it is a permitted upload
    * 3. Check the filesize against the maxSize property
    * 4. Check if we are allowed to overwrite existing files
    *    and if not make sure it doesn't exist
    * 
    * Use: $results = $this->doUpload();
    * 
    */
    function doUpload()
    {
        //Make sure that there are no accidental double slashes
        $this->uploadFolder = str_replace("//", "/", $this->uploadFolder);
        //Check for errors
        $errCode = $this->getFileError();
        if ($errCode!=UPLOAD_ERR_OK) { #1
            return $this->getErrorCodeMeaning($errCode);
        } else {
            if ($this->checkExtension()) { #2
                if ($this->sizeOk($this->size)) { #3
                    if (!$this->overWrite && $this->checkExists()) { #4
                        $this->isError = TRUE;
                        return $this->objLanguage->languageText("error_UPLOAD_NOOVERWRITE");
                    } else {
                        $this->moveUploadedFile();
                        $this->isError = FALSE;
                        return $this->objLanguage->languageText("error_UPLOAD_ERR_OK");
                    }
                } else {
                    $this->isError = TRUE;
                    return $this->objLanguage->languageText("error_UPLOAD_TOOBIG");
                }
            } else {
                $this->isError = TRUE;
                return $this->objLanguage->languageText("error_UPLOAD_DISALLOWEDEXTENSION");
            }
        }
    }
    
    /**
    * 
    * Method to set the upload folder
    * 
    * @param string $uploadFolder The folder to upload into
    *
    */
    function setUploadFolder($uploadFolder)
    {
        $this->uploadFolder = $uploadFolder;
        return $this->checkExistsFolder();
    }
    
    /**
    * 
    * Method to get the filename of the uploaded file.
    * It retrieves from the global $_FILES array, which  
    * contains all the uploaded file information. 
    * 
    * Usage: $fName = $this->getFileName();
    * 
    * @return The name of the file as string
    * 
    */
    function getFileName()
    {
        if (isset($_FILES['fileupload']['name'])) {
            $this->name = $_FILES['fileupload']['name'];
            return $_FILES['fileupload']['name'];
        } else {
            return NULL;
        }    
    }
    
    /**
    * 
    * Method to get the mime type of the uploaded file.
    * It retrieves from the global $_FILES array, which  
    * contains all the uploaded file information. The 
    * browser may not provide this information. 
    * 
    * Usage: $fType = $this->getFileType();
    * 
    * @return The file mime type as string, for example 
    *   "image/gif"
    * 
    */
    function getFileType()
    {
        if (isset($_FILES['fileupload']['type'])) {
            $this->type = $_FILES['fileupload']['type'];
            return $_FILES['fileupload']['type'];
        } else {
            return NULL;
        }    
    }

    /**
    * 
    * Method to get the file size of the uploaded file.
    * It retrieves from the global $_FILES array, which  
    * contains all the uploaded file information. 
    * 
    * Usage: $fSize = $this->getFileSize();
    * 
    * @return The file size in bytes
    * 
    */
    function getFileSize()
    {
        if (isset($_FILES['fileupload']['size'])) {
            $this->size = $_FILES['fileupload']['size'];
            return $_FILES['fileupload']['size'];
        } else {
            return NULL;
        }    
    }
    
    /**
    * 
    * Method to check the file size against the maxSize
    * 
    * @return TRUE|FALSE True if its OK, false if its 
    * too big
    *
    */
    function sizeOk($size) {
        if ($size > $this->maxSize) {
            return FALSE;
        } else {
            return TRUE;
        }    
    }
    
    /**
    * 
    * Method to get the temporary filename of the file in which the 
    * uploaded file was stored on the server. It retrieves from the 
    * global $_FILES array, which contains all the uploaded file 
    * information. 
    * 
    * Usage: $fSize = $this->getFileTmpName();
    * 
    * @return The file size in bytes
    * 
    */
    function getFileTmpName()
    {
        if (isset($_FILES['fileupload']['tmp_name'])) {
            return $_FILES['fileupload']['tmp_name'];
        } else {
            return NULL;
        }    
    }
    
    /**
    * 
    * Method to retrieve the error code associated with this file upload 
    * as added in PHP 4.2.0. It reads from the global $_FILES array, which 
    * contains all the uploaded file information. 
    * 
    * Usage: $fSize = $this->getFileError();
    * 
    * @return The file size in bytes
    * 
    */
    function getFileError()
    {
        if (isset($_FILES['fileupload']['error'])) {
            return $_FILES['fileupload']['error'];
        } else {
            return NULL;
        }    
    }
    
    /**
    * Method to get the file extension of the uploaded file
    */
    function getFileExtension()
    {
        $pathParts = array();
        $pathParts = pathinfo($this->getFileName());
        if (isset($pathParts['extension'])) {
            return $pathParts['extension'];
        } else {
            return NULL;
        }
    }
    
    /**
    * 
    * Method to return PHP errors as meaningful text. Use it to translate
    * the error codes returned by getFileError() into meaningful text
    * 
    * Use: $errTxt = $this->getErrorCodeMeaning($this->getFileError());
    * 
    * @param string $errCode THe error code to translate
    * 
    */
    function getErrorCodeMeaning($errCode)
    {
        switch ($errCode) {
        
            case 0:
                return $this->objLanguage->languageText("mod_files_err_0");
                break;
            
            case 1:
                return $this->objLanguage->languageText("mod_files_err_1");
                break;
            
            case 2:
                return $this->objLanguage->languageText("mod_files_err_2");
                break;
            
            case 3: 
                return $this->objLanguage->languageText("mod_files_err_3");
                break;
            
            case 4:
                return $this->objLanguage->languageText("mod_files_err_4");
                break;
                
            default:
                return $this->objLanguage->languageText("mod_files_err_default");
                break;
        }
    
    }
    
    /**
    * 
    * Method to check if the file is in fact an uploaded file. This is 
    * a security method because it returns TRUE if the file named by 
    * filename was uploaded via HTTP POST. This is useful to help ensure 
    * that a malicious user hasn't tried to trick the script into working 
    * on files upon which it should not be working, such as /etc/passwd.
    * 
    * @return TRUE|FALSE
    * 
    */
    function checkFileUploaded()
    {
        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * 
    * Method to move the uploaded file into the correct folder
    * as specified by the $this->uploadFolder property. It checks
    * if the file exists and set the upload type property to either
    * replace or new. This can then be accessed by the module
    * doing the upload, for example, to decide whether to do
    * a database update or insert where adding file details
    * to the database.
    * 
    */
    function moveUploadedFile()
    {
        $this->fullFilePath = $this->uploadFolder . $this->getFileName();
        if (file_exists($this->fullFilePath)) {
            $this->uploadType = "replace";
        } else {
            $this->uploadType = "new";
        }
        move_uploaded_file($this->getFileTmpName(), $this->fullFilePath);
    }
    
    /**
    * 
    * Method to check the extension of the uploaded file to 
    * see if it is a permitted extension.
    * 
    * @return TRUE|FALSE, TRUE if it is a permitted extension
    * and FALSE if not.
    * 
    */
    function checkExtension()
    {
        $ext = strtolower($this->getFileExtension());
        //Check against all extensions
        foreach($this->permittedTypes as $type) {
            $type = strtolower($type);
            if ($type==$ext) {
                return TRUE;
            }
        }
        return FALSE;
     }
    
    /**
    * 
    * Method to check if the uploaded file aready exists
    * in the destination folder
    * 
    * @return boolean TRUE|FALSE True if file exists, false if not.
    * 
    */
    function checkExists()
    {
        if (file_exists($this->uploadFolder . $this->getFileName())) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * 
    * Method to check if the upload folder aready exists
    * and if not create it
    * 
    * @return boolean TRUE|FALSE True if file exists, false if not.
    * 
    */
    function checkExistsFolder()
    {
        if (!file_exists($this->uploadFolder)) {
            if (mkdir($this->uploadFolder, 0777)) {
                return "folder_was_created";
            } else {
                return "folder_creation_failed";
            }
            
        } else {
            return "folder_exists";
        }
    }
    
    /**
    * Method to set the array of valid file
    * extensions
    * 
    * @param string List of comma separated file extensions
    */
    function setAllowedTypes($str)
    {
        $this->permittedTypes = explode(",", $str);
    }
} #class
?>
