<?php
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package groupadmin
* @subpackage template
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams
* @filesource
*/
?>
<script src="modules/tree/resources/TreeMenu.js" language="JavaScript" type="text/javascript"></script>
<script src="modules/groupadmin/resources/sorttable.js" language="JavaScript" type="text/javascript"></script>
<H1><?php echo $pageTitle.$lnkIcnCreate; ?></H1>

<DIV id='treenav'><?php echo $treeNav.$treeControls; ?></DIV>
<DIV id='treecontent'>
<?php if( isset($groupId) ) { ?>
    <DIV id='nodecontent'>
        <DIV id='blog'>
            <DIV id='blog-content'><?php echo $nodeList; ?></DIV>
            <DIV id='blog-footer'><?php echo $nodeControls; ?></DIV>
        </DIV>
    </DIV>
<?php } else { ?>
    <DIV id='nodeInfo'><?php echo $objLanguage->languageText('mod_groupadmin_hlpGroupAdmin'); ?></DIV>
<?php } ?>
</DIV>
<br clear="both" />