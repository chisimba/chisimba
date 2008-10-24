<?php

/**
 * Class to Handle Uploads for User Files
 *
 * This class can be called by any module, and will handle the upload process for that module.
 * Apart from the upload, this class also places the file in a suitable subfolder, updates the
 * database, parses files for metadata, and creates thumbnails for images.
 *
 * PHP version 5
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
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: upload_class_inc.php 3082 2007-10-22 11:39:33Z tohir $
 * @link      http://avoir.uwc.ac.za
 * @see
 * @todo      Improve Code to Handle Large Files
 */


/**
 * Class to Handle Uploads for User Files
 *
 * This class can be called by any module, and will handle the upload process for that module.
 * Apart from the upload, this class also places the file in a suitable subfolder, updates the
 * database, parses files for metadata, and creates thumbnails for images.
 *
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class swfupload extends object
{
    
    public $uploadModule='filemanager';
    public $uploadAction='ajaxupload';
    public $additionalParams=array();
    
    
    
    /**
    * Constructor
    */
    public function init()
    {
        
        
    }
    
    
    public function show()
    {
        $this->loadJS();
        $this->appendArrayVar('bodyOnLoad', $this->generateJS());
        
        return '
        <div id="flashUI1" style="display: none;">
            <fieldset class="flash" id="fsUploadProgress1">
                <legend>Large File Upload Site</legend>
            </fieldset>

            <div>
                <input type="button" value="Upload file (Max 100 MB)" onclick="upload1.selectFiles()" style="font-size: 8pt;" />
                <input id="btnCancel1" type="button" value="Cancel Uploads" onclick="cancelQueue(upload1);" disabled="disabled" style="font-size: 8pt;" /><br />
            </div>
            <div id="divFileProgressContainer" style="height: 75px;"></div>
        </div>
        <div id="degradedUI1">
            <fieldset>
                <legend>Large File Upload Site</legend>

                <input type="file" name="anyfile1" /> (Any file, Max 100 MB)<br/>
            </fieldset>
            <div>
                <input type="submit" value="Submit Files" />
            </div>
        </div>';
    }
    
    private function loadJS()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('swfupload/2.0.2/swfupload.js'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('swfupload/2.0.2/swfupload.graceful_degradation.js'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('swfupload/2.0.2/swfupload.queue.js'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('swfupload/chisimba_swfupload_handlers.js'));
    }
    
    private function generateJS()
    {
        $script = '
            upload1 = new SWFUpload({
                // Backend Settings
                upload_url: "/chisimba/framework/app/index.php",	// Relative to the SWF file (or you can use absolute paths)
                post_params: {[-POSTPARAMS-]},
                // File Upload Settings
                file_size_limit : "102400",	// 100MB
                file_types : "*.*",
                file_types_description : "All Files",
                file_upload_limit : "10",
                file_queue_limit : "0",
                
                // Event Handler Settings (all my handlers are in the Handler.js file)
                file_dialog_start_handler : fileDialogStart,
                file_queued_handler : fileQueued,
                file_queue_error_handler : fileQueueError,
                file_dialog_complete_handler : fileDialogComplete,
                upload_start_handler : uploadStart,
                upload_progress_handler : uploadProgress,
                upload_error_handler : uploadError,
                upload_success_handler : uploadSuccess,
                upload_complete_handler : uploadComplete,
                
                // Flash Settings
                flash_url : "[-SWFFILE-]",	// Relative to this file (or you can use absolute paths)
                
                swfupload_element_id : "flashUI1",		// Setting from graceful degradation plugin
                degraded_element_id : "degradedUI1",	// Setting from graceful degradation plugin
                
                custom_settings : {
                    progressTarget : "fsUploadProgress1",
                    cancelButtonId : "btnCancel1",
                    upload_target : "divFileProgressContainer",
                },
                
                // Debug Settings
                debug: true
            });
        ';
        
        $script = str_replace('[-SWFFILE-]', $this->getResourceUri('swfupload/2.0.2/swfupload_f8.swf'), $script);
        
        $postParams = array('module'=>$this->uploadModule, 'action'=>$this->uploadAction);
        
        $postParams = array_merge($postParams, $this->additionalParams);
        
        $postParamStr = '';
        $divider = '';
        
        foreach ($postParams as $param=>$value)
        {
            $postParamStr .= $divider.'"'.$param.'" : "'.$value.'"';
            $divider = ', ';
        }
        
        $script = str_replace('[-POSTPARAMS-]', $postParamStr, $script);
        
        return $script;
    }
}

?>