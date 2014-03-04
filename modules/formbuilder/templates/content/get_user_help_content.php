<?php

/* ! \file get_user_help_content.php
 * \brief The template file is called by AJAX functions all over the module to
 * specfic help content.
 * \section sec Template Code Explanation
 * - Request the help content type or the specific section or entry in the
 * entire help database.
 * - Request wehther or not this help content is part of the main help section of
 * the module or seperate help in the individual templates. If it is part of the
 * main help, the main help toolbar gets added in the help content.
 * - Spit out the relavent help content from the database.
 */

$helpContentType = $this->getParam('contentType');
$seperateHelpContentBool = $this->getParam("isNotPartOfMainHelpBool",null);

$content = $this->getObject('help_page_handler', 'formbuilder');
echo $content->showContent($helpContentType,$seperateHelpContentBool);
?>
