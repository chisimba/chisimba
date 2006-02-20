<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package contextgroups
* @subpackage templates
* @version 0.1
* @since 16 February 2005
* @author Jonathan Abrahams
* @filesource
*/
?>
<?php
// Page headers and layout template
$this->setLayoutTemplate('contextgroups_layout_tpl.php');
$this->appendArrayVar('headerParams',$this->getJavascriptFile('sorttable.js','groupadmin') );
?>
<DIV style='padding:1em;'>
        <DIV id='bltitle'><?php echo $ttlLecturers; ?></DIV>
        <DIV id='blog-content'>
        <?php
            $objMembers->setGroupId( $lectGroupId );
            echo $objMembers->show('lects');
        ?>
        </DIV>
        <?php if ( $this->isValid('manage_lect') ) { ?>
        <DIV id='blog-footer'>
            <?php echo $lnkLect->show(); ?>
        </DIV>
        <?php } ?>
</DIV>

<DIV style='padding:1em;'>
        <DIV id='bltitle'><?php echo $ttlStudents; ?></DIV>
        <DIV id='blog-content'>
        <?php
            $objMembers->setGroupId( $studGroupId );
            echo $objMembers->show('studs');
        ?>
        </DIV>
        <?php if ( $this->isValid('manage_stud') ) { ?>
        <DIV id='blog-footer'>
            <?php echo $lnkStud->show(); ?>
        </DIV>
        <?php } ?>
</DIV>

<DIV style='padding:1em;'>
        <DIV id='bltitle'><?php echo $ttlGuests; ?></DIV>
        <DIV id='blog-content'>
        <?php
            $objMembers->setGroupId( $guestGroupId );
            echo $objMembers->show('guest');
        ?>
        </DIV>
        <?php if ( $this->isValid('manage_guest') ) { ?>
        <DIV id='blog-footer'>
            <?php echo $lnkGuest->show(); ?>
        </DIV>
        <?php } ?>
</DIV>
<?php
echo $linkToContextHome;
?>
