<?php
/**
*   vCard profile defined by RFC 2426 - Vocabulary (Resource)
*
*   @version $Id: VCARD_C.php 431 2007-05-01 15:49:19Z cweiske $
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
class VCARD{

	// VCARD concepts
	function UID()
	{
		return  new Resource(VCARD_NS.'UID');

	}

	function ORGPROPERTIES()
	{
		return  new Resource(VCARD_NS . 'ORGPROPERTIES');

	}

	function ADRTYPES()
	{
		return  new Resource(VCARD_NS . 'ADRTYPES');

	}

	function NPROPERTIES()
	{
		return  new Resource(VCARD_NS . 'NPROPERTIES');

	}

	function EMAILTYPES()
	{
		return  new Resource(VCARD_NS . 'EMAILTYPES');

	}

	function TELTYPES()
	{
		return  new Resource(VCARD_NS . 'TELTYPES');

	}

	function ADRPROPERTIES()
	{
		return  new Resource(VCARD_NS . 'ADRPROPERTIES');

	}

	function TZTYPES()
	{
		return  new Resource(VCARD_NS . 'TZTYPES');

	}

	function STREET()
	{
		return  new Resource(VCARD_NS . 'Street');

	}

	function AGENT()
	{
		return  new Resource(VCARD_NS . 'AGENT');

	}

	function SOURCE()
	{
		return  new Resource(VCARD_NS . 'SOURCE');

	}

	function BDAY()
	{
		return  new Resource(VCARD_NS . 'BDAY');

	}

	function REV()
	{
		return  new Resource(VCARD_NS . 'REV');

	}

	function SORT_STRING()
	{
		return  new Resource(VCARD_NS . 'SORT_STRING');

	}

	function ORGNAME()
	{
		return  new Resource(VCARD_NS . 'Orgname');

	}

	function CATEGORIES()
	{
		return  new Resource(VCARD_NS . 'CATEGORIES');

	}

	function N()
	{
		return  new Resource(VCARD_NS . 'N');

	}

	function PCODE()
	{
		return  new Resource(VCARD_NS . 'Pcode');

	}

	function PREFIX()
	{
		return  new Resource(VCARD_NS . 'Prefix');

	}

	function PHOTO()
	{
		return  new Resource(VCARD_NS . 'PHOTO');

	}

	function FN()
	{
		return  new Resource(VCARD_NS . 'FN');

	}

	function SUFFIX()
	{
		return  new Resource(VCARD_NS . 'Suffix');

	}

	function VCARD_CLASS()
	{
		return  new Resource(VCARD_NS . 'CLASS');

	}

	function ADR()
	{
		return  new Resource(VCARD_NS . 'ADR');

	}

	function REGION()
	{
		return  new Resource(VCARD_NS . 'Region');

	}

	function GEO()
	{
		return  new Resource(VCARD_NS . 'GEO');

	}

	function EXTADD()
	{
		return  new Resource(VCARD_NS . 'Extadd');

	}

	function GROUP()
	{
		return  new Resource(VCARD_NS . 'GROUP');

	}

	function EMAIL()
	{
		return  new Resource(VCARD_NS . 'EMAIL');

	}

	function FAMILY()
	{
		return  new Resource(VCARD_NS . 'Family');

	}

	function TZ()
	{
		return  new Resource(VCARD_NS . 'TZ');

	}

	function NAME()
	{
		return  new Resource(VCARD_NS . 'NAME');

	}

	function ORGUNIT()
	{
		return  new Resource(VCARD_NS . 'Orgunit');

	}

	function COUNTRY()
	{
		return  new Resource(VCARD_NS . 'Country');

	}

	function SOUND()
	{
		return  new Resource(VCARD_NS . 'SOUND');

	}

	function TITLE()
	{
		return  new Resource(VCARD_NS . 'TITLE');

	}

	function MAILER()
	{
		return  new Resource(VCARD_NS . 'MAILER');

	}

	function OTHER()
	{
		return  new Resource(VCARD_NS . 'Other');

	}

	function LOCALITY()
	{
		return  new Resource(VCARD_NS . 'Locality');

	}

	function POBOX()
	{
		return  new Resource(VCARD_NS . 'Pobox');

	}

	function KEY()
	{
		return  new Resource(VCARD_NS . 'KEY');

	}

	function PRODID()
	{
		return  new Resource(VCARD_NS . 'PRODID');

	}

	function GIVEN()
	{
		return  new Resource(VCARD_NS . 'Given');

	}

	function LABEL()
	{
		return  new Resource(VCARD_NS . 'LABEL');

	}

	function TEL()
	{
		return  new Resource(VCARD_NS . 'TEL');

	}

	function NICKNAME()
	{
		return  new Resource(VCARD_NS . 'NICKNAME');

	}

	function ROLE()
	{
		return  new Resource(VCARD_NS . 'ROLE');
	}
}

?>