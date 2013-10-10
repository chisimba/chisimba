<?php
/**
*   Friend of a Friend (FOAF) Vocabulary (Resource)
*
*   @version $Id: FOAF_C.php 431 2007-05-01 15:49:19Z cweiske $
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
class FOAF{


	function AGENT()
	{
		return  new Resource(FOAF_NS . 'Agent');

	}

	function DOCUMENT()
	{
		return  new Resource(FOAF_NS . 'Document');

	}

	function GROUP()
	{
		return  new Resource(FOAF_NS . 'Group');

	}

	function IMAGE()
	{
		return  new Resource(FOAF_NS . 'Image');

	}

	function ONLINE_ACCOUNT()
	{
		return  new Resource(FOAF_NS . 'OnlineAccount');

	}

	function ONLINE_CHAT_ACCOUNT()
	{
		return  new Resource(FOAF_NS . 'OnlineChatAccount');

	}

	function ONLINE_ECOMMERCE_ACCOUNT()
	{
		return  new Resource(FOAF_NS . 'OnlineEcommerceAccount');

	}

	function ONLINE_GAMING_ACCOUNT()
	{
		return  new Resource(FOAF_NS . 'OnlineGamingAccount');

	}

	function ORGANIZATION()
	{
		return  new Resource(FOAF_NS . 'Organization');

	}

	function PERSON()
	{
		return  new Resource(FOAF_NS . 'Person');

	}

	function PERSONAL_PROFILE_DOCUMENT()
	{
		return  new Resource(FOAF_NS . 'PersonalProfileDocument');

	}

	function PROJECT()
	{
		return  new Resource(FOAF_NS . 'Project');

	}

	function ACCOUNT_NAME()
	{
		return  new Resource(FOAF_NS . 'accountName');

	}

	function ACCOUNT_SERVICE_HOMEPAGE()
	{
		return  new Resource(FOAF_NS . 'accountServiceHomepage');

	}

	function AIM_CHAT_ID()
	{
		return  new Resource(FOAF_NS . 'aimChatID');

	}

	function BASED_NEAR()
	{
		return  new Resource(FOAF_NS . 'based_near');

	}

	function CURRENT_PROJECT()
	{
		return  new Resource(FOAF_NS . 'currentProject');

	}

	function DEPICTION()
	{
		return  new Resource(FOAF_NS . 'depiction');

	}

	function DEPICTS()
	{
		return  new Resource(FOAF_NS . 'depicts');

	}

	function DNA_CHECKSUM()
	{
		return  new Resource(FOAF_NS . 'dnaChecksum');

	}

	function FAMILY_NAME()
	{
		return  new Resource(FOAF_NS . 'family_name');

	}

	function FIRST_NAME()
	{
		return  new Resource(FOAF_NS . 'firstName');

	}

	function FUNDED_BY()
	{
		return  new Resource(FOAF_NS . 'fundedBy');

	}

	function GEEKCODE()
	{
		return  new Resource(FOAF_NS . 'geekcode');

	}

	function GENDER()
	{
		return  new Resource(FOAF_NS . 'gender');

	}

	function GIVENNAME()
	{
		return  new Resource(FOAF_NS . 'givenname');

	}

	function HOLDS_ACCOUNT()
	{
		return  new Resource(FOAF_NS . 'holdsAccount');

	}

	function HOMEPAGE()
	{
		return  new Resource(FOAF_NS . 'homepage');

	}

	function ICQ_CHAT_ID()
	{
		return  new Resource(FOAF_NS . 'icqChatID');

	}

	function IMG()
	{
		return  new Resource(FOAF_NS . 'img');

	}

	function INTEREST()
	{
		return  new Resource(FOAF_NS . 'interest');

	}

	function JABBER_ID()
	{
		return  new Resource(FOAF_NS . 'jabberID');

	}

	function KNOWS()
	{
		return  new Resource(FOAF_NS . 'knows');

	}

	function LOGO()
	{
		return  new Resource(FOAF_NS . 'logo');

	}

	function MADE()
	{
		return  new Resource(FOAF_NS . 'made');

	}

	function MAKER()
	{
		return  new Resource(FOAF_NS . 'maker');

	}

	function MBOX()
	{
		return  new Resource(FOAF_NS . 'mbox');

	}

	function MBOX_SHA1SUM()
	{
		return  new Resource(FOAF_NS . 'mbox_sha1sum');

	}

	function MEMBER()
	{
		return  new Resource(FOAF_NS . 'member');

	}

	function MEMBERSHIP_CLASS()
	{
		return new Resource(FOAF_NS . 'membershipClass');

	}

	function MSN_CHAT_ID()
	{
		return  new Resource(FOAF_NS . 'msnChatID');

	}

	function MYERS_BRIGGS()
	{
		return  new Resource(FOAF_NS . 'myersBriggs');

	}

	function NAME()
	{
		return  new Resource(FOAF_NS . 'name');

	}

	function NICK()
	{
		return  new Resource(FOAF_NS . 'nick');

	}

	function PAGE()
	{
		return  new Resource(FOAF_NS . 'page');

	}

	function PAST_PROJECT()
	{
		return  new Resource(FOAF_NS . 'pastProject');

	}

	function PHONE()
	{
		return  new Resource(FOAF_NS . 'phone');

	}

	function PLAN()
	{
		return  new Resource(FOAF_NS . 'plan');

	}

	function PRIMARY_TOPIC()
	{
		return  new Resource(FOAF_NS . 'primaryTopic');

	}

	function PUBLICATIONS()
	{
		return  new Resource(FOAF_NS . 'publications');

	}

	function SCHOOL_HOMEPAGE()
	{
		return  new Resource (FOAF_NS . 'schoolHomepage');

	}

	function SHA1()
	{
		return  new Resource (FOAF_NS . 'sha1');

	}

	function SURNAME()
	{
		return  new Resource (FOAF_NS . 'surname');

	}

	function THEME()
	{
		return  new Resource(FOAF_NS . 'theme');

	}

	function THUMBNAIL()
	{
		return  new Resource(FOAF_NS . 'thumbnail');

	}

	function TIPJAR()
	{
		return  new Resource(FOAF_NS . 'tipjar');

	}

	function TITLE()
	{
		return  new Resource(FOAF_NS . 'title');

	}

	function TOPIC()
	{
		return  new Resource(FOAF_NS . 'topic');

	}

	function TOPIC_INTEREST()
	{
		return  new Resource(FOAF_NS . 'topic_interest');

	}

	function WEBLOG()
	{
		return  new Resource(FOAF_NS . 'weblog');

	}

	function WORK_INFO_HOMEPAGE()
	{
		return  new Resource(FOAF_NS . 'workInfoHomepage');

	}

	function WORKPLACE_HOMEPAGE()
	{
		return  new Resource(FOAF_NS . 'workplaceHomepage');

	}

	function YAHOO_CHAT_ID()
	{
		return  new Resource(FOAF_NS . 'yahooChatID');
	}
}





?>