<?php
$this->loadClass('link', 'htmlelements');

echo '<h1>'.$this->objLanguage->languageText('mod_filemanager_overwriteresults', 'filemanager', 'Overwrite Results').'</h1>';

$results = explode('____', $results);

$list = array();

foreach ($results as $result)
{
    $result = explode('__', $result);
    
    if (count($result) == 2) {
        $file = $this->objFiles->getFile($result[0]);
        
        if ($file != FALSE) {
            $link = new link ($this->uri(array('action'=>'fileinfo', 'id'=>$file['id'])));
            $link->link = $file['filename'];
            
            switch ($result[1])
            {
                case 'overwrite':
                    $list[] = '<span class="confirm">'.$link->show().' - '.$this->objLanguage->languageText('mod_filemanager_filehasbeenoverwritten', 'filemanager', 'file has been overwritten').'</span>';
                    break;
                default:
                    $list[] = $link->show().' - '.$this->objLanguage->languageText('mod_filemanager_filehasnotbeenoverwritten', 'filemanager', 'file has been not been overwritten');
                    break;
            }
        }
    }
}

if (count($list) > 0) {
    echo '<ul>';
        foreach ($list as $message)
        {
            echo '<li>'.$message.'</li>';
        }
    echo '</ul>';
    
}

?>