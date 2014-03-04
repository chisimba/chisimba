<?php
/**
 * This class is used for processing uploads of new files
 *  PHP version 5
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
 * @author    david wafula
 * @copyright 2010
 =
 */
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
class turnitinupload extends object {
    /**
     * @var array $permittedTypes The permitted file types
     */
    var $permittedTypes = array('txt','doc','docx','pdf');

    /**
     * @var string $type The mime type of the uploaded file
     */
    var $type;

    /**
     * @var boolean $isError TRUE|FALSE
     */
    var $isError;

    /**
     * @var string $fileName The name the file is to be stored as
     */
    var $fileName;

    /**
     * @var string $overWrite TRUE|FALSE Whether or not to overwrite existing files
     */
    var $overWrite;

    /**
     * @var string $uploadFolder The folder to upload into. This
     *             can be set before doing the upload to upload to any folder.
     *             This is an absolute filesystem path
     *             (e.g. /var/www/html/nextgen/mypath or
     *             E:\Inetpub\cvsCheckouts\nextgen\mypath\)
     */
    var $uploadFolder;

    /**
     * this is a file name with a number appended at the end. This number is
     * the total number of times the file has been uploaded
     * @var <type>
     */
    var $clonename;

    public function init() {
        $this->objLanguage =  $this->getObject('language', 'language');
       
        $this->overWrite = FALSE;
    }

  

    function doUpload($docname) {
        //Make sure that there are no accidental double slashes
        $this->uploadFolder = str_replace("//", "/", $this->uploadFolder);
        //Check for errors
        $errCode = isset($_FILES[$this->inputname]['error']);
        if ($errCode!=UPLOAD_ERR_OK) { #1
            if ($giveResults) {
                return array(
                        'success' => FALSE,
                        'message' => $this->getErrorCodeMeaning($errCode),
                );
            } else {
                return $this->getErrorCodeMeaning($errCode);
            }
        }
        else {
            //set fileName
            $this->fileName = $_FILES['file']['name'];
            $this->type = $_FILES['file']['type'];



            if ($this->checkExtension()) {
                if (!$this->overWrite && $this->checkExists($docname.'.'.$this->type)) {
                    $this->isError = TRUE;
                    return array(
                            'success' => FALSE,
                            'message' => $this->objLanguage->languageText("error_UPLOAD_NOOVERWRITE", 'jturnitin'),
                    );

                }
                else {
                    $this->moveUploadedFile($docname);
                    // check if the file actually exists, i.e., it has been uploaded
                    if($this->checkExists($docname.'.'.$this->getFileExtension())) {
                        return array(
                                'success' => TRUE,
                                'message' => $this->objLanguage->languageText("error_UPLOAD_ERR_OK", 'jturnitin'),
                                'filename' => $this->fileName,
                                'clonename' =>$this->clonename,
                                'mimetype' => $this->type,
                                'extension' => $this->getFileExtension(),
                        );
                    }
                    else {
                        return array(
                                'success' => FALSE,
                                'message' => $this->objLanguage->languageText("error_UPLOAD_FILENOTUPLOADED", 'jturnitin'),
                        );
                    }
                }
            }
            else {
                // save the extension in the database and have it's description be 'others'
                //$data = array('ext'=>$this->getFileExtension(), 'name'=>'Others');
                //$this->objPermittedTypes->saveNewExt($data);

                /*if (!$this->overWrite && $this->checkExists()) {
                    $this->isError = TRUE;
                    return array(
                        'success' => FALSE,
                        'message' => $this->objLanguage->languageText("error_UPLOAD_NOOVERWRITE", 'turnitin'),
                        );
                    
                }
                else {
                    $this->moveUploadedFile();

                    // check if the file actually exists, i.e., it has been uploaded
                    if($this->checkExists()) {
                        return array(
                            'success' => TRUE,
                            'message' => $this->objLanguage->languageText("error_UPLOAD_ERR_OK", 'turnitin'),
                            'filename' => $this->fileName,
                            'mimetype' => $this->type,
                            'extension' => 'others',
                        );
                    }
                    else {
                      return array(
                            'success' => FALSE,
                            'message' => $this->objLanguage->languageText("error_UPLOAD_FILENOTUPLOADED", 'turnitin'),
                      );
                    }
                }*/

                return array(
                        'success' => FALSE,
                        'message' => $this->objLanguage->languageText("error_UPLOAD_DISALLOWEDEXTENSION", 'jturnitin'),
                );
            }
        }
    }

    /**
     *
     * Method to check the extension of the uploaded file to
     * see if it is a permitted extension.
     *
     * @return TRUE|FALSE, TRUE if it is a permitted extension
     *                     and FALSE if not.
     *
     */
    function checkExtension() {
        $ext = strtolower($this->getFileExtension());
        //Check against all extensions
        foreach($this->permittedTypes as $type) {
            $type = strtolower($type);
            if ($type=='all') {
                return TRUE;
            } elseif ($type == $ext) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Method to get the file extension of the uploaded file
     */
    function getFileExtension() {
        $pathParts = array();
        $pathParts = pathinfo($_FILES['file']['name']);
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
    function getErrorCodeMeaning($errCode) {
        switch ($errCode) {

            case 0:
                return $this->objLanguage->languageText("mod_files_err_0", 'tjurnitin');
                break;

            case 1:
                return $this->objLanguage->languageText("mod_files_err_1", 'jturnitin');
                break;

            case 2:
                return $this->objLanguage->languageText("mod_files_err_2", 'jturnitin');
                break;

            case 3:
                return $this->objLanguage->languageText("mod_files_err_3", 'jturnitin');
                break;

            case 4:
                return $this->objLanguage->languageText("mod_files_err_4", 'jturnitin');
                break;

            default:
                return $this->objLanguage->languageText("mod_files_err_default", 'jturnitin');
                break;
        }

    }

    /**
     *
     * Method to check if the uploaded file aready exists
     * in the destination folder
     *
     * @return boolean TRUE|FALSE True if file exists, false if not.
     *
     */
    function checkExists($filename) {
        if (file_exists($this->uploadFolder . $filename)) {
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
     * @param string $storedName The name to store the file as (if not the original name)
     */
    function moveUploadedFile($docname) {

        $this->fullFilePath = $this->uploadFolder . $this->fileName;

        if (file_exists($this->fullFilePath)) {
            $this->uploadType = "replace";
        } else {
            $this->uploadType = "new";
        }
       
        $fn=$this->getFileName($this->fileName);

        $ext=$this->getFileExtension();
        $this->clonename=$this->fileName;
        

        // now verify if file was successfully uploaded
        move_uploaded_file($_FILES['file']['tmp_name'], $this->fullFilePath);
        $newname=$this->uploadFolder . $docname.'.'.$ext;
        rename($this->fullFilePath, $newname);
    }
    /**
     * returns filename with ext stripped
     */
    function getFileName($filepath) {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];
        //split the string by the literal dot in the filename
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
        //get the last dot position
        $lastdot = $pattern[count($pattern)-1][1];
        //now extract the filename using the basename function
        $filename = basename(substr($string, 0, $lastdot-1));
        $exts = split("[/\\.]", $filepath) ;
        $n = count($exts)-1;
        $ext = $exts[$n];

        return $filename.'.'.$ext;
    }

  
}
?>
