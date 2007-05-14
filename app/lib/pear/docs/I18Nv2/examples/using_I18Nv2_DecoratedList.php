<?php

/**
* Using I18Nv2_DecoratedList
* ==========================
*
* I18Nv2 provides decorated classes for country and language lists.
* 
* $Id$
*/

require_once 'I18Nv2/Country.php';
require_once 'I18Nv2/DecoratedList/HtmlSelect.php';
require_once 'I18Nv2/DecoratedList/HtmlEntities.php';

$c = &new I18Nv2_Country('it', 'iso-8859-1');
$e = &new I18Nv2_DecoratedList_HtmlEntities($c);
$s = &new I18Nv2_DecoratedList_HtmlSelect($e);

// set some attributes
$s->attributes['select']['name'] = 'CountrySelect';
$s->attributes['select']['onchange'] = 'this.form.submit()';

// set a selected entry
$s->selected['DE'] = true;

// print a HTML safe select box
echo $s->getAllCodes();
?>