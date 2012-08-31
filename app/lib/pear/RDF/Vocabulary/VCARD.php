<?php 
// ----------------------------------------------------------------------------------
// vCard profile defined by RFC 2426 - Vocabulary
// ----------------------------------------------------------------------------------
// Version                   : 0.4
// Authors                   : Daniel Westphal (dawe@gmx.de)

// Description               : Wrapper, defining resources for all terms of the
// vCard Vocabulary. For details about vCard see: http://www.w3.org/TR/vcard-rdf .
// Using the wrapper allows you to define all aspects of
// the vocabulary in one spot, simplifing implementation and
// maintainence. Working with the vocabulary, you should use
// these resources as shortcuts in your code.
// 
// define DC namespace
define('RDF_VCARD_NS', 'http://www.w3.org/2001/vcard-rdf/3.0#');

// VCARD concepts
$VCARD_UID =& RDF_Resource::factory(RDF_VCARD_NS . 'UID');
$VCARD_ORGPROPERTIES =& RDF_Resource::factory(RDF_VCARD_NS . 'ORGPROPERTIES');
$VCARD_ADRTYPES =& RDF_Resource::factory(RDF_VCARD_NS . 'ADRTYPES');
$VCARD_NPROPERTIES =& RDF_Resource::factory(RDF_VCARD_NS . 'NPROPERTIES');
$VCARD_EMAILTYPES =& RDF_Resource::factory(RDF_VCARD_NS . 'EMAILTYPES');
$VCARD_TELTYPES =& RDF_Resource::factory(RDF_VCARD_NS . 'TELTYPES');
$VCARD_ADRPROPERTIES =& RDF_Resource::factory(RDF_VCARD_NS . 'ADRPROPERTIES');
$VCARD_TZTYPES =& RDF_Resource::factory(RDF_VCARD_NS . 'TZTYPES');
$VCARD_Street =& RDF_Resource::factory(RDF_VCARD_NS . 'Street');
$VCARD_AGENT =& RDF_Resource::factory(RDF_VCARD_NS . 'AGENT');
$VCARD_SOURCE =& RDF_Resource::factory(RDF_VCARD_NS . 'SOURCE');
$VCARD_BDAY =& RDF_Resource::factory(RDF_VCARD_NS . 'BDAY');
$VCARD_REV =& RDF_Resource::factory(RDF_VCARD_NS . 'REV');
$VCARD_SORT_STRING =& RDF_Resource::factory(RDF_VCARD_NS . 'SORT_STRING');
$VCARD_Orgname =& RDF_Resource::factory(RDF_VCARD_NS . 'Orgname');
$VCARD_CATEGORIES =& RDF_Resource::factory(RDF_VCARD_NS . 'CATEGORIES');
$VCARD_N =& RDF_Resource::factory(RDF_VCARD_NS . 'N');
$VCARD_Pcode =& RDF_Resource::factory(RDF_VCARD_NS . 'Pcode');
$VCARD_Prefix =& RDF_Resource::factory(RDF_VCARD_NS . 'Prefix');
$VCARD_PHOTO =& RDF_Resource::factory(RDF_VCARD_NS . 'PHOTO');
$VCARD_FN =& RDF_Resource::factory(RDF_VCARD_NS . 'FN');
$VCARD_Suffix =& RDF_Resource::factory(RDF_VCARD_NS . 'Suffix');
$VCARD_CLASS =& RDF_Resource::factory(RDF_VCARD_NS . 'CLASS');
$VCARD_ADR =& RDF_Resource::factory(RDF_VCARD_NS . 'ADR');
$VCARD_Region =& RDF_Resource::factory(RDF_VCARD_NS . 'Region');
$VCARD_GEO =& RDF_Resource::factory(RDF_VCARD_NS . 'GEO');
$VCARD_Extadd =& RDF_Resource::factory(RDF_VCARD_NS . 'Extadd');
$VCARD_GROUP =& RDF_Resource::factory(RDF_VCARD_NS . 'GROUP');
$VCARD_EMAIL =& RDF_Resource::factory(RDF_VCARD_NS . 'EMAIL');
$VCARD_Family =& RDF_Resource::factory(RDF_VCARD_NS . 'Family');
$VCARD_TZ =& RDF_Resource::factory(RDF_VCARD_NS . 'TZ');
$VCARD_NAME =& RDF_Resource::factory(RDF_VCARD_NS . 'NAME');
$VCARD_Orgunit =& RDF_Resource::factory(RDF_VCARD_NS . 'Orgunit');
$VCARD_Country =& RDF_Resource::factory(RDF_VCARD_NS . 'Country');
$VCARD_SOUND =& RDF_Resource::factory(RDF_VCARD_NS . 'SOUND');
$VCARD_TITLE =& RDF_Resource::factory(RDF_VCARD_NS . 'TITLE');
$VCARD_MAILER =& RDF_Resource::factory(RDF_VCARD_NS . 'MAILER');
$VCARD_Other =& RDF_Resource::factory(RDF_VCARD_NS . 'Other');
$VCARD_Locality =& RDF_Resource::factory(RDF_VCARD_NS . 'Locality');
$VCARD_Pobox =& RDF_Resource::factory(RDF_VCARD_NS . 'Pobox');
$VCARD_KEY =& RDF_Resource::factory(RDF_VCARD_NS . 'KEY');
$VCARD_PRODID =& RDF_Resource::factory(RDF_VCARD_NS . 'PRODID');
$VCARD_Given =& RDF_Resource::factory(RDF_VCARD_NS . 'Given');
$VCARD_LABEL =& RDF_Resource::factory(RDF_VCARD_NS . 'LABEL');
$VCARD_TEL =& RDF_Resource::factory(RDF_VCARD_NS . 'TEL');
$VCARD_NICKNAME =& RDF_Resource::factory(RDF_VCARD_NS . 'NICKNAME');
$VCARD_ROLE =& RDF_Resource::factory(RDF_VCARD_NS . 'ROLE');

?>