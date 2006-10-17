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

?>