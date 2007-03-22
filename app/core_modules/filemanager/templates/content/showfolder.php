<?php
echo '<p>'.$breadcrumbs.'</p>';

switch ($this->getParam('message'))
{
    default:
        break;
    case 'foldercreated':
        echo '<span class="confirm">Folder has been created </span>'; break;
}

switch ($this->getParam('error'))
{
    default:
        break;
    case 'nofoldernameprovided':
        echo '<span class="error">Folder was not created. No name provided</span>'; break;
    case 'illegalcharacters':
        echo '<span class="error">Folder was not created. Folders cannot contain any of the following characters: \ / : * ? &quot; &lt; &gt; |</span>'; break;
}

echo '<h1>'.$folderpath.'</h1>';

echo $table;

echo $this->objFolders->showCreateFolderForm($folderId);
echo $this->objUpload->show($folderId);
 ?>


