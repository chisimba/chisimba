<?php
/**
*   vCard profile defined by RFC 2426 - Vocabulary
*
*   @version $Id: VCARD.php 431 2007-05-01 15:49:19Z cweiske $
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

// VCARD concepts
$VCARD_UID = new Resource(VCARD_NS.'UID');
$VCARD_ORGPROPERTIES = new Resource(VCARD_NS . 'ORGPROPERTIES');
$VCARD_ADRTYPES = new Resource(VCARD_NS . 'ADRTYPES');
$VCARD_NPROPERTIES = new Resource(VCARD_NS . 'NPROPERTIES');
$VCARD_EMAILTYPES = new Resource(VCARD_NS . 'EMAILTYPES');
$VCARD_TELTYPES = new Resource(VCARD_NS . 'TELTYPES');
$VCARD_ADRPROPERTIES = new Resource(VCARD_NS . 'ADRPROPERTIES');
$VCARD_TZTYPES = new Resource(VCARD_NS . 'TZTYPES');
$VCARD_Street = new Resource(VCARD_NS . 'Street');
$VCARD_AGENT = new Resource(VCARD_NS . 'AGENT');
$VCARD_SOURCE = new Resource(VCARD_NS . 'SOURCE');
$VCARD_BDAY = new Resource(VCARD_NS . 'BDAY');
$VCARD_REV = new Resource(VCARD_NS . 'REV');
$VCARD_SORT_STRING = new Resource(VCARD_NS . 'SORT_STRING');
$VCARD_Orgname = new Resource(VCARD_NS . 'Orgname');
$VCARD_CATEGORIES = new Resource(VCARD_NS . 'CATEGORIES');
$VCARD_N = new Resource(VCARD_NS . 'N');
$VCARD_Pcode = new Resource(VCARD_NS . 'Pcode');
$VCARD_Prefix = new Resource(VCARD_NS . 'Prefix');
$VCARD_PHOTO = new Resource(VCARD_NS . 'PHOTO');
$VCARD_FN = new Resource(VCARD_NS . 'FN');
$VCARD_Suffix = new Resource(VCARD_NS . 'Suffix');
$VCARD_CLASS = new Resource(VCARD_NS . 'CLASS');
$VCARD_ADR = new Resource(VCARD_NS . 'ADR');
$VCARD_Region = new Resource(VCARD_NS . 'Region');
$VCARD_GEO = new Resource(VCARD_NS . 'GEO');
$VCARD_Extadd = new Resource(VCARD_NS . 'Extadd');
$VCARD_GROUP = new Resource(VCARD_NS . 'GROUP');
$VCARD_EMAIL = new Resource(VCARD_NS . 'EMAIL');
$VCARD_Family = new Resource(VCARD_NS . 'Family');
$VCARD_TZ = new Resource(VCARD_NS . 'TZ');
$VCARD_NAME = new Resource(VCARD_NS . 'NAME');
$VCARD_Orgunit = new Resource(VCARD_NS . 'Orgunit');
$VCARD_Country = new Resource(VCARD_NS . 'Country');
$VCARD_SOUND = new Resource(VCARD_NS . 'SOUND');
$VCARD_TITLE = new Resource(VCARD_NS . 'TITLE');
$VCARD_MAILER = new Resource(VCARD_NS . 'MAILER');
$VCARD_Other = new Resource(VCARD_NS . 'Other');
$VCARD_Locality = new Resource(VCARD_NS . 'Locality');
$VCARD_Pobox = new Resource(VCARD_NS . 'Pobox');
$VCARD_KEY = new Resource(VCARD_NS . 'KEY');
$VCARD_PRODID = new Resource(VCARD_NS . 'PRODID');
$VCARD_Given = new Resource(VCARD_NS . 'Given');
$VCARD_LABEL = new Resource(VCARD_NS . 'LABEL');
$VCARD_TEL = new Resource(VCARD_NS . 'TEL');
$VCARD_NICKNAME = new Resource(VCARD_NS . 'NICKNAME');
$VCARD_ROLE = new Resource(VCARD_NS . 'ROLE');

?>