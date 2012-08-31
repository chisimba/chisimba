<?php
require_once 'Element.php';
class OpenDocument_Bookmark extends OpenDocument_Element
{
    private $name;
    
    public function __constructor($node, $document, $name)
    {
        parent::__constructor($node, $document);
        $this->name = $name;
    }
}
?>