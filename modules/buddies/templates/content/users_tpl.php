<?php 
// Create header object
$pgTitle = &$this->getObject('htmlheading', 'htmlelements');
$pgTitle->type = 1;
if($searchField=='listall'){
$pgTitle->str = $objLanguage->languageText('mod_buddies_addabuddylistall', 'buddies');
} else {
$pgTitle->str = $objLanguage->languageText('mod_buddies_addabuddy', 'buddies')."&nbsp;"./*'-'."&nbsp;".$objLanguage->languageText('mod_buddies_letter')."&nbsp;".*/ '"' . $searchField . '"' ;
}
// Create alphabet display object
$objAlphabet = &$this->getObject('alphabet', 'navigation');
$linkarray = array('action' => 'ListUsers', 'how' => 'firstname', 'searchField' => 'LETTER');
$url = $this->uri($linkarray, 'buddies');
// Create a table
$objTableClass = $this->newObject('htmltable', 'htmlelements');
$objTableClass->cellspacing = "2";
$objTableClass->cellpadding = "2";
$objTableClass->width = "70%";
$objTableClass->attributes = "border='0'";
// Create the array for the table header
$tableRow = array();
$tableHd[] = $objLanguage->languageText('mod_buddies_picture', 'buddies');
$tableHd[] = $objLanguage->languageText('mod_buddies_name', 'buddies');
$tableHd[] = $objLanguage->languageText('mod_buddies_email', 'buddies');
$tableHd[] = $objLanguage->languageText('mod_buddies_action', 'buddies');

// Create the table header for display
$objTableClass->addHeader($tableHd, "heading");

$index = 0;
$rowcount = 0;
foreach ($allUsers as $user) {
    $rowcount++; 
    // Set odd even colour scheme
    $class = ($rowcount % 2 == 0)?'odd':'even'; 
    // Get user pic
    $objUserPic = &$this->getObject('imageupload', 'useradmin');
    $pic = "<image src=\"" . $objUserPic->smallUserPicture($user['userid']) . "\"/>"; 
    // Get user name
    $username = $user["firstname"] . "&nbsp;" . $user["surname"]; 
    // Get user email 
    $email = "<a href=\"mailto:" . $user["emailaddress"] . "\">" . $user["emailaddress"] . "</a>"; 
    // Create make buddy link or show is buddy icon
    $makeBuddy = '';
    if ($user['userid'] == $objUser->userId()) {    	
    }
    else if (!$isBuddy[$index]) {
        $makeBuddy = "<a href = \"" . $this->uri(array('module' => 'buddies',
                'action' => 'AddBudy',
                'buddyId' => $user["userid"],
                'how' => 'firstname',
                'searchField' => $this->getParam('searchField')
                ))
         . "\">" . $objLanguage->languageText('mod_buddies_makebuddy', 'buddies') . "</a>";
    } else {
        $makeBuddyIcon = $this->getObject('geticon', 'htmlelements');
        $makeBuddyIcon->setIcon('user_user');
        $makeBuddyIcon->alt = $objLanguage->languageText('mod_buddies_isabuddy', 'buddies');
        $makeBuddy = $makeBuddyIcon->show();
    } 
    // Add data to table
    $objTableClass->startRow();
    $objTableClass->addCell($pic, '', 'top', 'center', $class);
    $objTableClass->addCell($username, '', '', '', $class);
    $objTableClass->addCell($email, '', '', '', $class);
    $objTableClass->addCell($makeBuddy, '', '', 'center', $class);
    $index++;
} 

// Create link back to my buddies template
$objBackLink = &$this->getObject('link', 'htmlelements');
$objBackLink->link($this->uri(array('module' => 'buddies')));
$objBackLink->link = $objLanguage->languageText('mod_buddies_return', 'buddies'); //mod_buddies_return
// Set middleColumnContent
$middleColumnContent = "";
$middleColumnContent .= $pgTitle->show();
$middleColumnContent .= $objAlphabet->putAlpha($url);
$middleColumnContent .= "<p>"."</p>";
$middleColumnContent .= $objTableClass->show();
if (empty($allUsers)) {
    $middleColumnContent .= "<p>" . "<span class='noRecordsMessage'>" . $objLanguage->languageText('mod_buddies_nouser', 'buddies') . "&nbsp;" . '"' . $searchField . '"' . "</span>" . "</p>";
} 
$middleColumnContent .= $objBackLink->show();
echo $middleColumnContent;

?>
