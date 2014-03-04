<?php
/*
* Download page for assignment.
* @package assignment
*/

/**
* Page to display dialog for downloading a file
* @author James Scoble
*/

$fileId=$this->getParam('fileid');
$data=$this->objFile->getArray("select * from tbl_assignment_filestore where fileId='$fileId'");
print_r($data);
die;
if (count($data)==0){ // if the file has been deleted
    header("Status: 404 Not Found");
} else {
    $name=$data[0]['filename'];
    $size=$data[0]['size'];
    $type=$data[0]['filetype']; 
    $fileId2=$data[0]['fileId']; 
    $list=$this->objFile->getArray("select id from tbl_assignment_blob where fileId='$fileId2' order by segment");

    header("Content-type: $type");
    header("Content-length: $size");
    header("Content-Disposition: attachment; filename=$name");
    header("Content-Description: PHP Generated Data");

    foreach ($list as $line)
    {
        $id=$line['id'];
        $filedata=$this->objFile->getArray("select * from tbl_assignment_blob where id='$id'");
        echo $filedata[0]['filedata'];
    }
}
?>
