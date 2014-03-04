<?php
/**
*   ATOM Vocabulary (Resource)
*
*   @version $Id: ATOM_RES.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Tobias Gauß (tobias.gauss@web.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of the
*   ATOM Vocabulary;.
*   For details about ATOM see: http://semtext.org/atom/atom.html.
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/
class ATOM_RES{

	function TYPE()
	{
		return  new ResResource(ATOM_NS . 'type');

	}

	function MODE()
	{
		return  new ResResource(ATOM_NS . 'mode');

	}

	function NAME()
	{
		return  new ResResource(ATOM_NS . 'name');

	}

	function URL()
	{
		return  new ResResource(ATOM_NS . 'url');

	}

	function EMAIL()
	{
		return  new ResResource(ATOM_NS . 'email');

	}

	function REL()
	{
		return  new ResResource(ATOM_NS . 'rel');

	}

	function HREF()
	{
		return  new ResResource(ATOM_NS . 'href');

	}

	function TITLE()
	{
		return  new ResResource(ATOM_NS . 'title');

	}

	function ATOM_CONSTRUCT()
	{
		return  new ResResource(ATOM_NS . 'AtomConstruct');

	}

	function CONTENT()
	{
		return  new ResResource(ATOM_NS . 'Content');

	}

	function PERSON()
	{
		return  new ResResource(ATOM_NS . 'Person');

	}

	function VALUE()
	{
		return  new ResResource(ATOM_NS . 'value');

	}

	function LINK()
	{
		return  new ResResource(ATOM_NS . 'Link');

	}

	function FEED()
	{
		return  new ResResource(ATOM_NS . 'Feed');

	}

	function VERSION()
	{
		return  new ResResource(ATOM_NS . 'version');

	}

	function LANG()
	{
		return  new ResResource(ATOM_NS . 'lang');

	}

	function AUTHOR()
	{
		return  new ResResource(ATOM_NS . 'author');

	}

	function CONTRIBUTOR()
	{
		return  new ResResource(ATOM_NS . 'contributor');

	}

	function TAGLINE()
	{
		return  new ResResource(ATOM_NS . 'tagline');

	}

	function GENERATOR()
	{
		return  new ResResource(ATOM_NS . 'generator');

	}

	function COPYRIGHT()
	{
		return  new ResResource(ATOM_NS . 'copyright');

	}

	function INFO()
	{
		return  new ResResource(ATOM_NS . 'info');

	}

	function MODIFIED()
	{
		return  new ResResource(ATOM_NS . 'modified');

	}

	function ENTRY()
	{
		return  new ResResource(ATOM_NS . 'Entry');

	}

	function HAS_CHILD()
	{
		return  new ResResource(ATOM_NS . 'hasChild');

	}

	function HAS_ENTRY()
	{
		return  new ResResource(ATOM_NS . 'hasEntry');

	}

	function HAS_LINK()
	{
		return  new ResResource(ATOM_NS . 'hasLink');

	}

	function HAS_TITLE()
	{
		return  new ResResource(ATOM_NS . 'hasTitle');

	}

	function ISSUED()
	{
		return  new ResResource(ATOM_NS . 'issued');

	}

	function CREATED()
	{
		return  new ResResource(ATOM_NS . 'created');

	}

	function SUMMARY()
	{
		return  new ResResource(ATOM_NS . 'summary');
	}
}



?>