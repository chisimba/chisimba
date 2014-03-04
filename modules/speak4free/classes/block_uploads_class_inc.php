<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of block_speak4free_class_inc
 *
 * @author davidwaf
 */
class block_uploads extends object {

    function init() {
        $this->title="Speak4Free Uploads";
        $this->objFiles = $this->getObject('dbspeak4freefiles');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser=$this->getObject('user','security');
        $this->objAltConfig = $this->getObject('altconfig','config');
        $this->objFileEmbed = $this->getObject('fileembed','filemanager');
        $this->objViewerUtils = $this->getObject('viewerutils');

    }
    function show() {
       return $this->objViewerUtils->getMyUploads();
    }
}
?>
