<?php
require_once 'OpenDocument.php'; // open document class

//open test.odt
$odt = new OpenDocument('test.odt');

//loop throught document children
foreach ($odt->getChildren() as $child) {
    //strip headings
    if ($child instanceof OpenDocument_Heading) {
        $child->delete();
    }
}

//save as no_headings.pdt
$odt->save('no_headings.odt');
echo 'saved as no-headings.odt';
?>