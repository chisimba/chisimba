<?php
/**
*   vCard profile defined by RFC 2426 - Vocabulary (ResResource)
*
*   @version $Id: VCARD_RES.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Daniel Westphal (dawe@gmx.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of the
*   vCard Vocabulary.
*   For details about vCard see: http://www.w3.org/TR/vcard-rdf .
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/
class VCARD_RES{

	// VCARD concepts
	function UID()
	{
		return  new ResResource(VCARD_NS.'UID');

	}

	function ORGPROPERTIES()
	{
		return  new ResResource(VCARD_NS . 'ORGPROPERTIES');

	}

	function ADRTYPES()
	{
		return  new ResResource(VCARD_NS . 'ADRTYPES');

	}

	function NPROPERTIES()
	{
		return  new ResResource(VCARD_NS . 'NPROPERTIES');

	}

	function EMAILTYPES()
	{
		return  new ResResource(VCARD_NS . 'EMAILTYPES');

	}

	function TELTYPES()
	{
		return  new ResResource(VCARD_NS . 'TELTYPES');

	}

	function ADRPROPERTIES()
	{
		return  new ResResource(VCARD_NS . 'ADRPROPERTIES');

	}

	function TZTYPES()
	{
		return  new ResResource(VCARD_NS . 'TZTYPES');

	}

	function STREET()
	{
		return  new ResResource(VCARD_NS . 'Street');

	}

	function AGENT()
	{
		return  new ResResource(VCARD_NS . 'AGENT');

	}

	function SOURCE()
	{
		return  new ResResource(VCARD_NS . 'SOURCE');

	}

	function BDAY()
	{
		return  new ResResource(VCARD_NS . 'BDAY');

	}

	function REV()
	{
		return  new ResResource(VCARD_NS . 'REV');

	}

	function SORT_STRING()
	{
		return  new ResResource(VCARD_NS . 'SORT_STRING');

	}

	function ORGNAME()
	{
		return  new ResResource(VCARD_NS . 'Orgname');

	}

	function CATEGORIES()
	{
		return  new ResResource(VCARD_NS . 'CATEGORIES');

	}

	function N()
	{
		return  new ResResource(VCARD_NS . 'N');

	}

	function PCODE()
	{
		return  new ResResource(VCARD_NS . 'Pcode');

	}

	function PREFIX()
	{
		return  new ResResource(VCARD_NS . 'Prefix');

	}

	function PHOTO()
	{
		return  new ResResource(VCARD_NS . 'PHOTO');

	}

	function FN()
	{
		return  new ResResource(VCARD_NS . 'FN');

	}

	function SUFFIX()
	{
		return  new ResResource(VCARD_NS . 'Suffix');

	}

	function VCARD_CLASS()
	{
		return  new ResResource(VCARD_NS . 'CLASS');

	}

	function ADR()
	{
		return  new ResResource(VCARD_NS . 'ADR');

	}

	function REGION()
	{
		return  new ResResource(VCARD_NS . 'Region');

	}

	function GEO()
	{
		return  new ResResource(VCARD_NS . 'GEO');

	}

	function EXTADD()
	{
		return  new ResResource(VCARD_NS . 'Extadd');

	}

	function GROUP()
	{
		return  new ResResource(VCARD_NS . 'GROUP');

	}

	function EMAIL()
	{
		return  new ResResource(VCARD_NS . 'EMAIL');

	}

	function FAMILY()
	{
		return  new ResResource(VCARD_NS . 'Family');

	}

	function TZ()
	{
		return  new ResResource(VCARD_NS . 'TZ');

	}

	function NAME()
	{
		return  new ResResource(VCARD_NS . 'NAME');

	}

	function ORGUNIT()
	{
		return  new ResResource(VCARD_NS . 'Orgunit');

	}

	function COUNTRY()
	{
		return  new ResResource(VCARD_NS . 'Country');

	}

	function SOUND()
	{
		return  new ResResource(VCARD_NS . 'SOUND');

	}

	function TITLE()
	{
		return  new ResResource(VCARD_NS . 'TITLE');

	}

	function MAILER()
	{
		return  new ResResource(VCARD_NS . 'MAILER');

	}

	function OTHER()
	{
		return  new ResResource(VCARD_NS . 'Other');

	}

	function LOCALITY()
	{
		return  new ResResource(VCARD_NS . 'Locality');

	}

	function POBOX()
	{
		return  new ResResource(VCARD_NS . 'Pobox');

	}

	function KEY()
	{
		return  new ResResource(VCARD_NS . 'KEY');

	}

	function PRODID()
	{
		return  new ResResource(VCARD_NS . 'PRODID');

	}

	function GIVEN()
	{
		return  new ResResource(VCARD_NS . 'Given');

	}

	function LABEL()
	{
		return  new ResResource(VCARD_NS . 'LABEL');

	}

	function TEL()
	{
		return  new ResResource(VCARD_NS . 'TEL');

	}

	function NICKNAME()
	{
		return  new ResResource(VCARD_NS . 'NICKNAME');

	}

	function ROLE()
	{
		return  new ResResource(VCARD_NS . 'ROLE');
	}

}

?>