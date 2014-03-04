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
 * @package   wicid (document management system)
 * @author    Nguni Phakela, david wafula
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
class wicidupload extends object {
    /**
     * @var array $permittedTypes The permitted file types
     */
    var $permittedTypes = array();

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
        //Instantiate the language object
        $this->objLanguage =  $this->getObject('language', 'language');
        $this->objPermittedTypes = $this->getObject('dbpermittedtypes');
        $this->permittedTypes = $this->getTypes();
        //Set the default overwrite  $dir=$this->objSysConfig->getValue('FILES_DIR', 'wicid');

        $this->overWrite = FALSE;
        $this->objUploadTable = $this->getObject('dbfileuploads');
        $this->documents=$this->getObject('dbdocuments');
    }

    function getTypes() {
        $types = array();
        $data = $this->objPermittedTypes->getFileExtensions(false);
        $count = 1;
        foreach($data as $data) {
            $tmp = array($count=>$data['ext']);
            $types = $types + $tmp;
            $count++;
        }

        return $types;
    }

    function doUpload($docname,$docid) {
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
            $this->fileName = $_FILES['filenamefield']['name'];
            $this->type = $_FILES['filenamefield']['type'];



            if ($this->checkExtension()) {
                if (!$this->overWrite && $this->checkExists($docname.'.'.$this->type)) {
                    $this->isError = TRUE;
                    return array(
                            'success' => FALSE,
                            'message' => $this->objLanguage->languageText("error_UPLOAD_NOOVERWRITE", 'wicid'),
                    );

                }
                else {
                    $this->moveUploadedFile($docname,$docid);

                    // check if the file actually exists, i.e., it has been uploaded
                    if($this->checkExists()) {
                        return array(
                                'success' => TRUE,
                                'message' => $this->objLanguage->languageText("error_UPLOAD_ERR_OK", 'wicid'),
                                'filename' => $this->fileName,
                                'clonename' =>$this->clonename,
                                'mimetype' => $this->type,
                                'extension' => $this->getFileExtension(),
                        );
                    }
                    else {
                        return array(
                                'success' => FALSE,
                                'message' => $this->objLanguage->languageText("error_UPLOAD_FILENOTUPLOADED", 'wicid'),
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
                        'message' => $this->objLanguage->languageText("error_UPLOAD_NOOVERWRITE", 'wicid'),
                        );
                    
                }
                else {
                    $this->moveUploadedFile();

                    // check if the file actually exists, i.e., it has been uploaded
                    if($this->checkExists()) {
                        return array(
                            'success' => TRUE,
                            'message' => $this->objLanguage->languageText("error_UPLOAD_ERR_OK", 'wicid'),
                            'filename' => $this->fileName,
                            'mimetype' => $this->type,
                            'extension' => 'others',
                        );
                    }
                    else {
                      return array(
                            'success' => FALSE,
                            'message' => $this->objLanguage->languageText("error_UPLOAD_FILENOTUPLOADED", 'wicid'),
                      );
                    }
                }*/

                return array(
                        'success' => FALSE,
                        'message' => $this->objLanguage->languageText("error_UPLOAD_DISALLOWEDEXTENSION", 'wicid'),
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
        $pathParts = pathinfo($_FILES['filenamefield']['name']);
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
                return $this->objLanguage->languageText("mod_files_err_0", 'wicid');
                break;

            case 1:
                return $this->objLanguage->languageText("mod_files_err_1", 'wicid');
                break;

            case 2:
                return $this->objLanguage->languageText("mod_files_err_2", 'wicid');
                break;

            case 3:
                return $this->objLanguage->languageText("mod_files_err_3", 'wicid');
                break;

            case 4:
                return $this->objLanguage->languageText("mod_files_err_4", 'wicid');
                break;

            default:
                return $this->objLanguage->languageText("mod_files_err_default", 'wicid');
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
        if (file_exists($this->uploadFolder . $fileName)) {
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
    function moveUploadedFile($docname,$docid) {

        $instances=$this->objUploadTable->getFileInstances( $this->fileName, $this->uploadFolder);
        $instanceNo=count($instances);

        $this->fullFilePath = $this->uploadFolder . $this->fileName;

        if (file_exists($this->fullFilePath)) {
            $this->uploadType = "replace";
        } else {
            $this->uploadType = "new";
        }
        $instances=$this->objUploadTable->getFileInstances( $this->fileName, $this->fullFilePath );
        $instanceNo=count($instances);
        $fn=$this->getFileName($this->fileName);

        $ext=$this->getFileExtension();
        $this->clonename=$this->fileName;
        if($instanceNo > 0) {
            $this->fullFilePath =$this->uploadFolder . $fn.'-'.$instanceNo.'.'.$ext;
            $this->clonename=$fn.'-'.$instanceNo.'.'.$ext;
        }

        // now verify if file was successfully uploaded
        move_uploaded_file($_FILES['filenamefield']['tmp_name'], $this->fullFilePath);
        $newname=$this->uploadFolder . $docname.'.na';
        $this->documents->updateDocument($docid,array('ext'=>$ext));
        rename($this->fullFilePath, $newname);
    }

    function getFileName($filepath) {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];
        //split the string by the literal dot in the filename
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
        //get the last dot position
        $lastdot = $pattern[count($pattern)-1][1];
        //now extract the filename using the basename function
        $filename = basename(substr($string, 0, $lastdot-1));

        return $filename;
    }
}
?>
