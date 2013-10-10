<?php
/**
* Template for no content permissions page
*
* @author Charl Mert
* @package cmsadmin
*/

?>

<h1>Authorization Required</h1>
<br/>
<?php
if ($securityType == 'no_write'){
?>
<h2>You are not authorized to edit this content</h2>
<?php
} else if ($securityType == 'no_read'){
?>
<h2>You are not authorized to read this content</h2>
<?php
} else if ($securityType == 'no_sections'){
?>
<h2>You haven't been assigned write access to any section</h2>
<?php
} else {
?>
<h2>You are not authorized to this content</h2>
<?php
}
?>

<br/>
<h2>Contact your system administrator to gain access.</h2>
