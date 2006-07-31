<?php
 //-------------------------//
 // Acl list
 $fields = array ( 'id', 'name', 'description' );
 $aclList =& $this->objPermAcl->getAcls( $fields );

 //Create dropdown.
 $ddbAcls = "<SELECT style='width:100pt' name='aclId' >";
 foreach( $aclList as $acl ) {
     $ddbAcls.= "<OPTION value='".$acl['id']."'>";
     $ddbAcls.= $acl['name']."-".$acl['description'];
     $ddbAcls.= "</OPTION>";
 }
 $ddbAcls.= "</SELECT>";

 $hdrPermissions = "Permissions";

 $frmContent = "
     <TABLE>
       <TR>
           <TD align='right'>ACL Name:</TD>
           <TD><INPUT type='text' name='tinName' /></TD>
           <TD><INPUT class='button' type='submit' name='btnCreateAcl' value='Create' /></TD>

       </TR>
       <TR>
           <TD align='right'>ACL Description:</TD>
           <TD><INPUT type='text' name='tinDescription' /></TD>
       </TR>
    </TABLE>";

 $tblFooter = "<TABLE>";
 $tblFooter.= "<TR>";
 $tblFooter.= "<TD>".$ddbAcls."</TD>";
 $tblFooter.= "<TD><INPUT type='submit' class='button' name='btnEditAcl' value='Edit' /></TD>";
 $tblFooter.= "<TD><INPUT type='submit' class='button' name='btnDeleteAcl' value='Delete' /></TD>";
 $tblFooter.= "</TR>";
 $tblFooter.= "</TABLE>";

// Form Elements
 $frmPermissions =& $this->getObject('form', 'htmlelements');
 $frmPermissions->name = 'frmPermissions';
 $frmPermissions->displayType = '3';
 $frmPermissions->action = $this->uri ( array( 'action'=>'edit_form' ) );
 $frmPermissions->addToForm( "<DIV id='blog-content'> " );
 $frmPermissions->addToForm( $frmContent );
 $frmPermissions->addToForm( "</DIV>" );
 $frmPermissions->addToForm( "<DIV id='blog-footer'>" );
 $frmPermissions->addToForm( $tblFooter);
 $frmPermissions->addToForm( "</DIV>" );
 $frmPermissions->addToForm( "<input type='hidden' name='action' value='create_form' />" );
?>

  <DIV id='bltitle'>
       Create a new access control
  </DIV>
   <?php echo $frmPermissions->show(); ?>