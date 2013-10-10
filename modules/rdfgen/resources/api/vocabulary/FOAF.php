<?php
/**
*   Friend of a Friend (FOAF) Vocabulary (Resource)
*
*   @version $Id: FOAF.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Tobias Gauß (tobias.gauss@web.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of the
*   Friend of a Friend project (FOAF).
*   For details about FOAF see: http://xmlns.com/foaf/0.1/.
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/



// FOAF concepts
$FOAF_Agent = new Resource(FOAF_NS . 'Agent');
$FOAF_Document = new Resource(FOAF_NS . 'Document');
$FOAF_Group = new Resource(FOAF_NS . 'Group');
$FOAF_Image = new Resource(FOAF_NS . 'Image');
$FOAF_OnlineAccount = new Resource(FOAF_NS . 'OnlineAccount');
$FOAF_OnlineChatAccount = new Resource(FOAF_NS . 'OnlineChatAccount');
$FOAF_OnlineEcommerceAccount = new Resource(FOAF_NS . 'OnlineEcommerceAccount');
$FOAF_OnlineGamingAccount = new Resource(FOAF_NS . 'OnlineGamingAccount');
$FOAF_Organization = new Resource(FOAF_NS . 'Organization');
$FOAF_Person = new Resource(FOAF_NS . 'Person');
$FOAF_PersonalProfileDocument = new Resource(FOAF_NS . 'PersonalProfileDocument');
$FOAF_Project = new Resource(FOAF_NS . 'Project');
$FOAF_accountName = new Resource(FOAF_NS . 'accountName');
$FOAF_accountServiceHomepage = new Resource(FOAF_NS . 'accountServiceHomepage');
$FOAF_aimChatID = new Resource(FOAF_NS . 'aimChatID');
$FOAF_based_near = new Resource(FOAF_NS . 'based_near');
$FOAF_currentProject = new Resource(FOAF_NS . 'currentProject');
$FOAF_depiction = new Resource(FOAF_NS . 'depiction');
$FOAF_depicts = new Resource(FOAF_NS . 'depicts');
$FOAF_dnaChecksum = new Resource(FOAF_NS . 'dnaChecksum');
$FOAF_family_name = new Resource(FOAF_NS . 'family_name');
$FOAF_firstName = new Resource(FOAF_NS . 'firstName');
$FOAF_fundedBy = new Resource(FOAF_NS . 'fundedBy');
$FAOF_geekcode = new Resource(FOAF_NS . 'geekcode');
$FOAF_gender = new Resource(FOAF_NS . 'gender');
$FOAF_givenname = new Resource(FOAF_NS . 'givenname');
$FOAF_holdsAccount = new Resource(FOAF_NS . 'holdsAccount');
$FOAF_homepage = new Resource(FOAF_NS . 'homepage');
$FOAF_icqChatID = new Resource(FOAF_NS . 'icqChatID');
$FOAF_img = new Resource(FOAF_NS . 'img');
$FOAF_interest = new Resource(FOAF_NS . 'interest');
$FOAF_jabberID = new Resource(FOAF_NS . 'jabberID');
$FOAF_knows = new Resource(FOAF_NS . 'knows');
$FOAF_logo = new Resource(FOAF_NS . 'logo');
$FOAF_made = new Resource(FOAF_NS . 'made');
$FOAF_maker = new Resource(FOAF_NS . 'maker');
$FOAF_mbox = new Resource(FOAF_NS . 'mbox');
$FOAF_mbox_sha1sum = new Resource(FOAF_NS . 'mbox_sha1sum');
$FOAF_member = new Resource(FOAF_NS . 'member');
$FOAF_membershipClass =new Resource(FOAF_NS . 'membershipClass');
$FOAF_msnChatID = new Resource(FOAF_NS . 'msnChatID');
$FOAF_myersBriggs = new Resource(FOAF_NS . 'myersBriggs');
$FOAF_name = new Resource(FOAF_NS . 'name');
$FOAF_nick = new Resource(FOAF_NS . 'nick');
$FOAF_page = new Resource(FOAF_NS . 'page');
$FOAF_pastProject = new Resource(FOAF_NS . 'pastProject');
$FOAF_phone = new Resource(FOAF_NS . 'phone');
$FOAF_plan = new Resource(FOAF_NS . 'plan');
$FOAF_primaryTopic = new Resource(FOAF_NS . 'primaryTopic');
$FOAF_publications = new Resource(FOAF_NS . 'publications');
$FOAF_schoolHomepage = new Resource (FOAF_NS . 'schoolHomepage');
$FOAF_sha1 = new Resource (FOAF_NS . 'sha1');
$FOAF_surname = new Resource (FOAF_NS . 'surname');
$FOAF_theme = new Resource(FOAF_NS . 'theme');
$FOAF_thumbnail = new Resource(FOAF_NS . 'thumbnail');
$FOAF_tipjar = new Resource(FOAF_NS . 'tipjar');
$FOAF_title = new Resource(FOAF_NS . 'title');
$FOAF_topic = new Resource(FOAF_NS . 'topic');
$FOAF_topic_interest = new Resource(FOAF_NS . 'topic_interest');
$FOAF_weblog = new Resource(FOAF_NS . 'weblog');
$FOAF_workInfoHomepage = new Resource(FOAF_NS . 'workInfoHomepage');
$FOAF_workplaceHomepage = new Resource(FOAF_NS . 'workplaceHomepage');
$FOAF_yahooChatID = new Resource(FOAF_NS . 'yahooChatID');




?>