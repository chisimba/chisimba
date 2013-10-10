<?php 


//$objAlphabet = &$this->getObject('alphabet', 'navigation');
//$linkarray = array('action' => 'ListUsers', 'how' => 'firstname', 'searchField' => 'LETTER');
//$url = $this->uri($linkarray, 'buddies');

// Create add icon
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objLink = $this->uri(array('action' => 'listUsers',
        'how' => 'firstname',
        'searchField' => 'A'));
$objAddIcon->setIcon("add", "gif");
$objAddIcon->alt = $objLanguage->languageText('mod_courselecturers_addlecturer', 'courselecturers');
$add = $objAddIcon->getAddIcon($objLink); 
// Send an instant message to all buddies.
$imPopup =& $this->getObject('popup','instantmessaging');
$imPopup->setup(null, 'buddies', $objLanguage->languageText('phrase_sendinstantmessagetoall'));
$imAll = $imPopup->show(); 
// Create header with add icon and instant message to all buddies icon
$pgTitle = &$this->getObject('htmlheading', 'htmlelements');
$pgTitle->type = 1;
$pgTitle->str = $objLanguage->languageText('mod_courselecturers_heading', 'courselecturers') . "&nbsp;" . $add . "&nbsp;" . $imAll; 
// Create link to add template
$objAddLink = &$this->newObject('link', 'htmlelements');
$objAddLink->link($this->uri(array('action' => 'listUsers',
            'how' => 'firstname',
            'searchField' => 'A')));
$objAddLink->link = $objLanguage->languageText('mod_courselecturers_addbuddy', 'courselecturers'); 
// Show the add link
$objLink = &$this->getObject('link', 'htmlelements'); 
// module=buddies&action=ListUsers&how=firstname&searchField=A
$objLink->link($this->uri(array('module' => 'courselecturers',
            'action' => 'ListUsers',
            'how' => 'firstname',
            'searchField' => 'A'
            )));
// Create a table
$objTableClass = $this->newObject('htmltable', 'htmlelements');
$objTableClass->cellspacing = "2";
$objTableClass->cellpadding = "2";
$objTableClass->width = "90%";
$objTableClass->attributes = "border='0'";
// Create the array for the table header
$tableRow = array();
$tableHd[] = $objLanguage->languageText('mod_courselecturers_lecturer', 'courselecturers');
$tableHd[] = $objLanguage->languageText('mod_buddies_online', 'buddies');
$tableHd[] = $objLanguage->languageText('mod_buddies__name', 'buddies');
$tableHd[] = $objLanguage->languageText('mod_buddies_im', 'buddies');
$tableHd[] = $objLanguage->languageText('mod_buddies_email', 'buddies');
//$tableHd[] = $objLanguage->languageText('mod_buddies_icq', 'buddies');
//$tableHd[] = $objLanguage->languageText('mod_buddies_yahoo', 'buddies');
//$tableHd[] = $objLanguage->languageText('mod_buddies_homepage', 'buddies');
$tableHd[] = $objLanguage->languageText('mod_buddies_action', 'buddies');
// Create the table header for display
$objTableClass->addHeader($tableHd, "heading");
$index = 0;
$rowcount = 0;
//foreach ($allUsers as $users) {
//foreach ($buddies as $buddy) {
    $rowcount++;
  //  $buddyId = $buddy['buddyid'];
//    $isFan = $buddy['isfan'];
   // $isBuddy = $buddy['isbuddy']; 
   
    // Set odd even colour scheme
    $class = ($rowcount % 2 == 0)?'odd':'even';


$objUserPic = &$this->getObject('imageupload', 'useradmin');
    $pic = "<image src=\"" . $objUserPic->smallUserPicture($user['userid']) . "\"/>";

$objTableClass->addCell($pic, '', 'top', 'center', $class);
   // $objTableClass->startRow();
    // Is the buddy a buddy ?
  //  if ($isBuddy == '1') {

        //$buddyIcon = &$this->getObject('geticon', 'htmlelements');
        //$buddyIcon->setIcon('user_user');
     //   $objUserPic->alt = $objLanguage->languageText('mod_buddies_isabuddy', 'buddies');
//$buddyIcon->alt = $objLanguage->languageText('mod_buddies_isabuddy', 'buddies');
      //  $buddy = $ObjUserPic->show();
     //   $objTableClass->addCell($buddy, '', 'top', 'center', $class);
    //} else {
     //   $objTableClass->addCell('', '', '', '', $class);
  // } 
    
    // Display online/offline icon.
    if ($buddiesOnline[$index]) {
        $onlineIcon = &$this->getObject('geticon', 'htmlelements');
        $onlineIcon->setIcon('online');
        $onlineIcon->alt = $objLanguage->languageText('word_online');
        $online = $onlineIcon->show();
        $objTableClass->addCell($online, '', 'top', 'center', $class);
    } else {
        $onlineIcon = &$this->getObject('geticon', 'htmlelements');
        $onlineIcon->setIcon('offline');
        $onlineIcon->alt = $objLanguage->languageText('word_offline');
        $online = $onlineIcon->show();
        $objTableClass->addCell($online, '', 'top', 'center', $class);
    } 
    // Show Lecturer name
    $name = $objUser->fullname($buddyId);
    $objTableClass->addCell($name, '', '', '', $class); 
    // Show instant messaging icon
	$imPopup =& $this->getObject('popup','instantmessaging');
	$imPopup->setup($buddyId, null, $objLanguage->languageText('phrase_sendinstantmessage'));
	$im = $imPopup->show(); 
    $objTableClass->addCell($im, '', 'top', 'center', $class); 
    // Show email icon
    $userEmail = $this->objUser->email($buddyId);
    $emailIcon = $this->getObject('geticon', 'htmlelements');
    $emailIcon->alt = $objLanguage->languageText('phrase_sendemail');
    $emailIcon->setIcon('notes');
    $emailparam =  "<a href=\"mailto:" .$userEmail. "\">" . $emailIcon->show() . "</a>";
    $objTableClass->addCell($emailparam, '', 'top', 'center', $class); 
    // Show icq icon
   // $icq = $Icq[$index];
    //$objTableClass->addCell($icq, '', 'top', 'center', $class); 
    // Show yahoo icon
   // $yahoo = $Yahoo[$index];
   // $objTableClass->addCell($yahoo, '', 'top', 'center', $class); 
    // Show homepage icon
    $modules = &$this->getObject('modules', 'modulecatalogue');
    if ($modules->checkIfRegistered('homepage')) {
	    $dbHomepages =& $this->getObject('dbhomepages', 'homepage');
	    if ($dbHomepages->homepageExists($buddyId)){
	        $hpparam = $this->uri(array(
	            // 'module'=>'homepage',
	            'action' => 'ViewHomepage',
	            'userId' => $buddyId
	            ), 
				'homepage'
			);
	        $hpIcon = $this->getObject('geticon', 'htmlelements');
	        $hpIcon->alt = $objLanguage->languageText('phrase_viewhomepage');
	        $hp = $hpIcon->getLinkedIcon($hpparam, 'homepage');
	        //$objTableClass->addCell($hp, '', 'top', 'center', $class);
	    }
	    else {
	       	//$objTableClass->addCell('', '', '', '', $class);
	    }
    } 
    // Create delete icon
    $objDelIcon = $this->newObject('geticon', 'htmlelements'); 
    // Create delete icon
    $delLink = array('action' => 'RemoveBudy',
        'buddyId' => $buddyId,
        'module' => 'courselecturers',
        'confirm' => 'yes',
        );
    $deletephrase = $objLanguage->languageText('mod_courselecturers_removelecturer', 'courselecturers');
    $conf = $objDelIcon->getDeleteIconWithConfirm('', $delLink, 'courselecturers', $deletephrase);
    $objTableClass->addCell($conf, '', 'top', 'center', $class);
//} 
if (empty($buddies)) {
    $objTableClass->addCell("<span class='noRecordsMessage'>" . $objLanguage->languageText('mod_courselecturers_nodata', 'courselecturers') . "</span>", NULL, 'top', 'center', null, 'colspan=10');
} 
$middleColumnContent = "";
$middleColumnContent .= $pgTitle->show();
$middleColumnContent .= $objTableClass->show();
$middleColumnContent .= $objAddLink->show();
echo $middleColumnContent;

?>
