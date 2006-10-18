<?
$this->loadClass('form', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');

// echo '<pre>';
// print_r($_POST);
// echo '</pre>';

echo '<h1>Confirm Delete Files?</h1>';

if ($this->getParam('files') == NULL || !is_array($this->getParam('files')) || count($this->getParam('files')) == 0) {
    echo '<div class="noRecordsMessage">No Files Were Selected</div>';
    echo '<p><a href="javascript:history.back()">Back to Previous Page</a> / ';
    echo '<a href="'.$this->uri(NULL).'">Return to File Manager</a></p>';
} else {
    
    echo '<p>Are you sure you want to delete these files?</p>';
    $files = $this->getParam('files');
    
    $form = new form('confirmdelete', $this->uri(array('action'=>'multideleteconfirm')));
    
    $form->addToForm ('<ul>');
    
    foreach ($files as $file)
    {
        
        
        $fileDetails = $this->objFiles->getFile($file);
        
        if ($fileDetails != FALSE) {
            $checkbox = new checkbox('files[]', htmlentities($fileDetails['filename']), TRUE);
            $checkbox->value = $file;
            $form->addToForm ('<li>'.$checkbox->show().' '.htmlentities($fileDetails['filename']).'</li>');
        }
    }
    
    $form->addToForm ('</ul>');
    
    $button = new button ('submitform', 'Confirm Delete Selected Items');
    $button->setToSubmit();
    
    $form->addToForm ($button->show());
    
    echo $form->show();
}
        
?>