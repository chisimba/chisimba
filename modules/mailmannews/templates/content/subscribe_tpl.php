<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

$middleColumn = NULL;
$leftCol = NULL;
$rightCol = NULL;

if ($this->objUser->isLoggedIn()) {
	$leftMenu = $this->newObject('usermenu', 'toolbar');
	$leftCol .= $leftMenu->show();
	$middleColumn .= $this->objMailmanSignup->createList();
}
//$leftCol .= "sign up here...";
$middleColumn .= 'While Mailman is best suited for a discussion "listserv", it is not difficult to configure it to operate as an announce-only newsletter-style mailing list.

First, go to:

http://www.yourdomain.com/mailman/admin/yourlistname

where "yourdomain" is your domain name and "yourlistname" is the name of your mailing list (the part to the left of the @ symbol in the list\'s email address).

Enter the list administrative password and click the "Let me in" button. Configure the following settings:

Privacy Options >> Subscription Rules
Who can view subscription list? (List admin only)

Privacy Options >> Sender Filters
By default, should new list member postings be moderated? (Yes)
Action to take when a moderated member posts to the list. (Hold)
Action to take for postings from non-members for which no explicit action is defined. (Hold)

Auto-responder settings
Should Mailman send an auto-response to mailing list posters? (Yes)
Auto-response text to send to mailing list posters:
"Sorry, only the site administrator can post to this list."
Number of days between auto-responses: (0)

Membership Management >> Membership List
Set everyone\'s moderation bit, including those members not currently visible (On)

Now that everyone has their moderation bit on (which means their posts are subject to moderator approval) and "Action to take when a moderated member posts to the list" is Hold, the administrator will have to approve their own posts. You could turn off the moderation bit for moderators\' email addresses, but this is not very secure because email addresses can be easily forged.

You can customize these settings as you like, but to prevent unauthorized people from posting to your list, always have their moderation bit(flag) on. You should always keep "By default, should new list member postings be moderated?" set to "Yes" so new members are also moderated.'; 

$middleColumn .= $this->objMailmanSignup->subsBox();

$objBlocks = $this->getObject('blocks', 'blocks');


$rightCol = $objBlocks->showBlock('subscribe', 'mailmannews');
$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol);
$cssLayout->setRightColumnContent($rightCol);
echo $cssLayout->show();

?>