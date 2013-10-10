<?php

/* ! \file action_error.php
 * \brief The action error template file
 * \brief This template is called by the controller.php class when
 * an action does not exist.
 * \section sec Explanation
 * - Get the CSS layout to make two column layout.
 * - Add some text to the left column.
 * - Make a button that returns to the home page of this module.
 * - Add the parsed string to the middle (right in two column layout) area. Add the
 * button underneath it.
 * - Display the css layout.
 */

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent("<h3>Error Encountered.</h3>");
$objHomeButton = new button('homeButton');
$objHomeButton->setValue('Go To Home');
$objHomeButton->setCSS("homeButton");
$mngHomelink = html_entity_decode($this->uri(array(
            'module' => 'formbuilder',
            'action' => 'home'
        )));
$objHomeButton->setOnClick("parent.location='$mngHomelink'");
$mngHomeButton = $objHomeButton->showDefault();
$cssLayout->setMiddleColumnContent($str . "<br>" . $mngHomeButton);
echo $cssLayout->show();
?>

