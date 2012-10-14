<?php
/**
 * This Template Demonstrates how the License Chooser works
 */

// Instantiate Class
$cc = $this->newObject('licensechooser');

// Set Default Value
$cc->defaultValue = 'by';

// Show
echo $cc->show();

// Instantiate Class
$cc = $this->newObject('licensechooserdropdown');

// Set Default Value
$cc->defaultValue = 'by';

// Show
echo '<form>'.$cc->show().'</form>';


$this->setVar('pageSuppressXML', TRUE);
?>