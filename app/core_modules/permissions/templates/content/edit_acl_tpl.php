<?php
//------------- EDIT THE Access control list -----------------------------
// PRECONDITION : $Acl is the parameter containing the Acl to be modified.
// POSTCONDTION : all modification are done to the given Acl.
//------------------------------------------------------------------------

//------ FUNCTIONS --------------
//------ creatList function ------
function createList ( $name, $list )
{
    $lst = "<SELECT size=5 style='width:100pt' name='$name'>\n";
    foreach (  $list as $item ){
        list( $key, $value )  = each ($item);
        list( $key, $option ) = each ($item);
        $lst.= "<OPTION value='$value'>$option</OPTION>\n";
    }
    $lst.= "</SELECT>\n";
    return $lst;
}
//-------- showList function -------
function showList( $hdr, $list ){
$tbl = "<TABLE cellspacing='0' align='center' >";
$tbl.= "<TR><TD class='heading' align='center'>".$hdr."</TD><TR>";
$tbl.= "<TR><TD>".$list."</TR></TD>";
$tbl.= "</TABLE>";
return $tbl;
}

//----- USER LIST ------------
$usersInAcl       = $this->objPermAcl->getAclUsers( $aclId );
$usersNotInAcl    = $this->objGAModel->getUsers( );

$lstUsersInAcl    = createList ( 'lstUserIn', $usersInAcl );
$lstUsersNotInAcl = createList ( 'lstUserNotIn', $usersNotInAcl );

$hdrUsersInAcl    = 'Access control users';
$hdrUsersNotInAcl = 'Users';

$tblUsersInAcl    = showList( $hdrUsersInAcl, $lstUsersInAcl );
$tblUsersNotInAcl = showList( $hdrUsersNotInAcl, $lstUsersNotInAcl );

//----- GROUP LIST ------------
$tree =& $this->getObject( 'groupadmin_tree', 'groupadmin' );

$groupsInAcl       = $this->objPermAcl->getAclGroups( $aclId );
$groupsNotInAcl    = $this->objGAModel->getGroups( array ("id", "name") );

$lstGroupsInAcl    = createList ( 'lstGroupIn', $groupsInAcl );
$lstGroupsNotInAcl = createList ( 'lstGroupNotIn', $groupsNotInAcl );

$hdrGroupsInAcl    = 'Access control groups';
$hdrGroupsNotInAcl = 'Groups';

$tblGroupsInAcl    = showList( $hdrGroupsInAcl, $lstGroupsInAcl );
$tblGroupsNotInAcl = showList( $hdrGroupsNotInAcl,$lstGroupsNotInAcl );

//------- BUTTONS -----------
$objSkin =& $this->getObject('skin', 'skin');
$iconsURL = $objSkin->getSkinURL()."icons";

$tblUserButtons = "<TABLE>";
$tblUserButtons.= "<TR><TD><BUTTON class='button' type='submit' onClick='document.frmEditAcl.btnUserInsert.value=\"clicked\"'>&gt;&gt;</BUTTON></TD></TR>";
$tblUserButtons.= "<TR><TD><BUTTON class='button' type='submit' onClick='document.frmEditAcl.btnUserRemove.value=\"clicked\"'>&lt;&lt;</BUTTON></TD></TR>";
$tblUserButtons.= "</TABLE>";
$tblUserButtons.= "<INPUT type='hidden' name='btnUserInsert' value='notClicked'>";
$tblUserButtons.= "<INPUT type='hidden' name='btnUserRemove' value='notClicked'>";

$tblGroupButtons = "<TABLE>";
$tblGroupButtons.= "<TR><TD><BUTTON class='button' type='submit' onClick='document.frmEditAcl.btnGroupInsert.value=\"clicked\"'>&gt;&gt;</BUTTON></TD></TR>";
$tblGroupButtons.= "<TR><TD><BUTTON class='button' type='submit' onClick='document.frmEditAcl.btnGroupRemove.value=\"clicked\"'>&lt;&lt;</BUTTON></TD></TR>";
$tblGroupButtons.= "</TABLE>";
$tblGroupButtons.= "<INPUT type='hidden' name='btnGroupInsert' value='notClicked'>";
$tblGroupButtons.= "<INPUT type='hidden' name='btnGroupRemove' value='notClicked'>";

//----- SAVE BUTTON ------
$btnSaveUser  = "<INPUT class='button' type='submit' name='btnUserSave' value='Save'>";
$btnSaveGroup = "<INPUT class='button' type='submit' name='btnGroupSave' value='Save'>";

// ---- USER -----
$tblUsers = "<TABLE width='100%'>";
$tblUsers.= "<TR>";
$tblUsers.= "<TD>".$tblUsersNotInAcl."</TD>";
$tblUsers.= "<TD>".$tblUserButtons."</TD>";
$tblUsers.= "<TD>".$tblUsersInAcl."</TD>";
$tblUsers.= "</TR>";
$tblUsers.= "</TABLE>";

// ----- GROUP --------
$tblGroups = "<TABLE width='100%'>";
$tblGroups.= "<TR>";
$tblGroups.= "<TD>".$tblGroupsNotInAcl."</TD>";
$tblGroups.= "<TD>".$tblGroupButtons."</TD>";
$tblGroups.= "<TD>".$tblGroupsInAcl."</TD>";
$tblGroups.= "</TR>";
$tblGroups.= "</TABLE>";

//-------- LAYOUT ------------
$tblAcl = "<FORM name='frmEditAcl' method='POST'>";
$tblAcl.= "<INPUT type='hidden' name='action' value='edit_form'>";
$tblAcl.= "<INPUT type='hidden' name='module' value='permissions'>";
$tblAcl.= "<INPUT type='hidden' name='aclId' value='$aclId'>";

$tblAcl.= "<TABLE cellspacing='0' width='99%'>";
$tblAcl.= "<TR><TD id='bltitle'>Access Control List:".$this->objPermAcl->getDescription( $aclId )."</TD></TR>";
$tblAcl.= "<TR align='center'><TD id='blog-content'>".$tblUsers."</TD></TR>";

$tblAcl.= "<TR><TD id='blog-content'>".$tblGroups."</TD></TR>";
$tblAcl.= "<TR><TD id='blog-footer'>".$btnSaveGroup."</TD></TR>";

$tblAcl.= "</TABLE></FORM>";
echo $tblAcl;
//----- GROUP LIST -----------
?>
