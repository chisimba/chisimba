<?php
/**
*   ATOM Vocabulary (Resource)
*
*   @version $Id: ATOM_C.php 431 2007-05-01 15:49:19Z cweiske $
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
class ATOM{

	function TYPE()
	{
		return  new Resource(ATOM_NS . 'type');

	}

	function MODE()
	{
		return  new Resource(ATOM_NS . 'mode');

	}

	function NAME()
	{
		return  new Resource(ATOM_NS . 'name');

	}

	function URL()
	{
		return  new Resource(ATOM_NS . 'url');

	}

	function EMAIL()
	{
		return  new Resource(ATOM_NS . 'email');

	}

	function REL()
	{
		return  new Resource(ATOM_NS . 'rel');

	}

	function HREF()
	{
		return  new Resource(ATOM_NS . 'href');

	}

	function TITLE()
	{
		return  new Resource(ATOM_NS . 'title');

	}

	function ATOM_CONSTRUCT()
	{
		return  new Resource(ATOM_NS . 'AtomConstruct');

	}

	function CONTENT()
	{
		return  new Resource(ATOM_NS . 'Content');

	}

	function PERSON()
	{
		return  new Resource(ATOM_NS . 'Person');

	}

	function VALUE()
	{
		return  new Resource(ATOM_NS . 'value');

	}

	function LINK()
	{
		return  new Resource(ATOM_NS . 'Link');

	}

	function FEED()
	{
		return  new Resource(ATOM_NS . 'Feed');

	}

	function VERSION()
	{
		return  new Resource(ATOM_NS . 'version');

	}

	function LANG()
	{
		return  new Resource(ATOM_NS . 'lang');

	}

	function AUTHOR()
	{
		return  new Resource(ATOM_NS . 'author');

	}

	function CONTRIBUTOR()
	{
		return  new Resource(ATOM_NS . 'contributor');

	}

	function TAGLINE()
	{
		return  new Resource(ATOM_NS . 'tagline');

	}

	function GENERATOR()
	{
		return  new Resource(ATOM_NS . 'generator');

	}

	function COPYRIGHT()
	{
		return  new Resource(ATOM_NS . 'copyright');

	}

	function INFO()
	{
		return  new Resource(ATOM_NS . 'info');

	}

	function MODIFIED()
	{
		return  new Resource(ATOM_NS . 'modified');

	}

	function ENTRY()
	{
		return  new Resource(ATOM_NS . 'Entry');

	}

	function HAS_CHILD()
	{
		return  new Resource(ATOM_NS . 'hasChild');

	}

	function HAS_ENTRY()
	{
		return  new Resource(ATOM_NS . 'hasEntry');

	}

	function HAS_LINK()
	{
		return  new Resource(ATOM_NS . 'hasLink');

	}

	function HAS_TITLE()
	{
		return  new Resource(ATOM_NS . 'hasTitle');

	}

	function ISSUED()
	{
		return  new Resource(ATOM_NS . 'issued');

	}

	function CREATED()
	{
		return  new Resource(ATOM_NS . 'created');

	}

	function SUMMARY()
	{
		return  new Resource(ATOM_NS . 'summary');
	}
}



?>