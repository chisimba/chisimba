<?php
/**
*   Friend of a Friend (FOAF) Vocabulary (ResResource)
*
*   @version $Id: FOAF_RES.php 431 2007-05-01 15:49:19Z cweiske $
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
class FOAF_RES{

	function AGENT()
	{
		return  new ResResource(FOAF_NS . 'Agent');

	}

	function DOCUMENT()
	{
		return  new ResResource(FOAF_NS . 'Document');

	}

	function GROUP()
	{
		return  new ResResource(FOAF_NS . 'Group');

	}

	function IMAGE()
	{
		return  new ResResource(FOAF_NS . 'Image');

	}

	function ONLINE_ACCOUNT()
	{
		return  new ResResource(FOAF_NS . 'OnlineAccount');

	}

	function ONLINE_CHAT_ACCOUNT()
	{
		return  new ResResource(FOAF_NS . 'OnlineChatAccount');

	}

	function ONLINE_ECOMMERCE_ACCOUNT()
	{
		return  new ResResource(FOAF_NS . 'OnlineEcommerceAccount');

	}

	function ONLINE_GAMING_ACCOUNT()
	{
		return  new ResResource(FOAF_NS . 'OnlineGamingAccount');

	}

	function ORGANIZATION()
	{
		return  new ResResource(FOAF_NS . 'Organization');

	}

	function PERSON()
	{
		return  new ResResource(FOAF_NS . 'Person');

	}

	function PERSONAL_PROFILE_DOCUMENT()
	{
		return  new ResResource(FOAF_NS . 'PersonalProfileDocument');

	}

	function PROJECT()
	{
		return  new ResResource(FOAF_NS . 'Project');

	}

	function ACCOUNT_NAME()
	{
		return  new ResResource(FOAF_NS . 'accountName');

	}

	function ACCOUNT_SERVICE_HOMEPAGE()
	{
		return  new ResResource(FOAF_NS . 'accountServiceHomepage');

	}

	function AIM_CHAT_ID()
	{
		return  new ResResource(FOAF_NS . 'aimChatID');

	}

	function BASED_NEAR()
	{
		return  new ResResource(FOAF_NS . 'based_near');

	}

	function CURRENT_PROJECT()
	{
		return  new ResResource(FOAF_NS . 'currentProject');

	}

	function DEPICTION()
	{
		return  new ResResource(FOAF_NS . 'depiction');

	}

	function DEPICTS()
	{
		return  new ResResource(FOAF_NS . 'depicts');

	}

	function DNA_CHECKSUM()
	{
		return  new ResResource(FOAF_NS . 'dnaChecksum');

	}

	function FAMILY_NAME()
	{
		return  new ResResource(FOAF_NS . 'family_name');

	}

	function FIRST_NAME()
	{
		return  new ResResource(FOAF_NS . 'firstName');

	}

	function FUNDED_BY()
	{
		return  new ResResource(FOAF_NS . 'fundedBy');

	}

	function GEEKCODE()
	{
		return  new ResResource(FOAF_NS . 'geekcode');

	}

	function GENDER()
	{
		return  new ResResource(FOAF_NS . 'gender');

	}

	function GIVENNAME()
	{
		return  new ResResource(FOAF_NS . 'givenname');

	}

	function HOLDS_ACCOUNT()
	{
		return  new ResResource(FOAF_NS . 'holdsAccount');

	}

	function HOMEPAGE()
	{
		return  new ResResource(FOAF_NS . 'homepage');

	}

	function ICQ_CHAT_ID()
	{
		return  new ResResource(FOAF_NS . 'icqChatID');

	}

	function IMG()
	{
		return  new ResResource(FOAF_NS . 'img');

	}

	function INTEREST()
	{
		return  new ResResource(FOAF_NS . 'interest');

	}

	function JABBER_ID()
	{
		return  new ResResource(FOAF_NS . 'jabberID');

	}

	function KNOWS()
	{
		return  new ResResource(FOAF_NS . 'knows');

	}

	function LOGO()
	{
		return  new ResResource(FOAF_NS . 'logo');

	}

	function MADE()
	{
		return  new ResResource(FOAF_NS . 'made');

	}

	function MAKER()
	{
		return  new ResResource(FOAF_NS . 'maker');

	}

	function MBOX()
	{
		return  new ResResource(FOAF_NS . 'mbox');

	}

	function MBOX_SHA1SUM()
	{
		return  new ResResource(FOAF_NS . 'mbox_sha1sum');

	}

	function MEMBER()
	{
		return  new ResResource(FOAF_NS . 'member');

	}

	function MEMBERSHIP_CLASS()
	{
		return new ResResource(FOAF_NS . 'membershipClass');

	}

	function MSN_CHAT_ID()
	{
		return  new ResResource(FOAF_NS . 'msnChatID');

	}

	function MYERS_BRIGGS()
	{
		return  new ResResource(FOAF_NS . 'myersBriggs');

	}

	function NAME()
	{
		return  new ResResource(FOAF_NS . 'name');

	}

	function NICK()
	{
		return  new ResResource(FOAF_NS . 'nick');

	}

	function PAGE()
	{
		return  new ResResource(FOAF_NS . 'page');

	}

	function PAST_PROJECT()
	{
		return  new ResResource(FOAF_NS . 'pastProject');

	}

	function PHONE()
	{
		return  new ResResource(FOAF_NS . 'phone');

	}

	function PLAN()
	{
		return  new ResResource(FOAF_NS . 'plan');

	}

	function PRIMARY_TOPIC()
	{
		return  new ResResource(FOAF_NS . 'primaryTopic');

	}

	function PUBLICATIONS()
	{
		return  new ResResource(FOAF_NS . 'publications');

	}

	function SCHOOL_HOMEPAGE()
	{
		return  new ResResource (FOAF_NS . 'schoolHomepage');

	}

	function SHA1()
	{
		return  new ResResource (FOAF_NS . 'sha1');

	}

	function SURNAME()
	{
		return  new ResResource (FOAF_NS . 'surname');

	}

	function THEME()
	{
		return  new ResResource(FOAF_NS . 'theme');

	}

	function THUMBNAIL()
	{
		return  new ResResource(FOAF_NS . 'thumbnail');

	}

	function TIPJAR()
	{
		return  new ResResource(FOAF_NS . 'tipjar');

	}

	function TITLE()
	{
		return  new ResResource(FOAF_NS . 'title');

	}

	function TOPIC()
	{
		return  new ResResource(FOAF_NS . 'topic');

	}

	function TOPIC_INTEREST()
	{
		return  new ResResource(FOAF_NS . 'topic_interest');

	}

	function WEBLOG()
	{
		return  new ResResource(FOAF_NS . 'weblog');

	}

	function WORK_INFO_HOMEPAGE()
	{
		return  new ResResource(FOAF_NS . 'workInfoHomepage');

	}

	function WORKPLACE_HOMEPAGE()
	{
		return  new ResResource(FOAF_NS . 'workplaceHomepage');

	}

	function YAHOO_CHAT_ID()
	{
		return  new ResResource(FOAF_NS . 'yahooChatID');
	}
}




?>