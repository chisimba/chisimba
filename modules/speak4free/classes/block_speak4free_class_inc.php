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
class block_speak4free {
    function init() {
        $this->title="Speak4Free Uploads";
        $this->objFiles = $this->getObject('dbspeak4freefiles');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser=$this->getObject('user','security');
    }
    function show() {
        $files=  $this->objFiles->getLatestUploads();
        $objAltConfig = $this->getObject('altconfig','config');
        $latestUploads=array();


        $myfiles= $this->objFiles->getByUser($this->objUser->userid());
        $table = $this->newObject('htmltable', 'htmlelements');
        foreach($myfiles as $file) {
            $ext = pathinfo($file['filename']);
            $ext = $ext['extension'];
            $fullPath = $this->objConfig->getcontentBasePath().'speak4free/'.$file['id'].'/'.$file['id'].'.'.$ext;

            $replacewith="";
            $docRoot=$_SERVER['DOCUMENT_ROOT'];
            $resourcePath=str_replace($docRoot,$replacewith,$fullPath);
            $codebase="http://" . $_SERVER['HTTP_HOST'].'/'.$resourcePath;
            $content="";
            $fileTypes = array(
                    'png'=>'image',
                    'flv'=>'flv',
                    'mp3'=>'audio',
                    'mov'=>'quicktime',
                    'wmv'=>'wmv',
                    'ogg'=>'ogg',
                    'mpg'=>'mpg',
                    'mpeg'=>'mpeg',
                    'mp4'=>'mp4'

            );
            foreach ($fileTypes as $fileType=>$fileName) {
                if($fileType == $ext) {
                    $content = $this->objFileEmbed->embed($codebase, $fileName,"150","100").
                            '<br/>By '.$this->objUser->fullname($file['creatorid']).
                            '<br/>Date '.$file['dateuploaded'];
                }

            }


            $table->startRow();
            $table->addCell($content);
            $table->addCell($file['dateuploaded']);
            $table->endRow();
        }
        return $table->show();
    }
}
?>
