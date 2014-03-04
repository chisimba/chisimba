<?php
/**
*   Dublin Core Vocabulary (Resource)
*
*   @version $Id: DC_C.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Chris Bizer (chris@bizer.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of the Dublin
*   Core Vocabulary. For details about DC see: http://dublincore.org/
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/
class DC{
	// DC concepts
	function CONTRIBUTOR()
	{
		return  new Resource(DC_NS . 'contributor');

	}

	function COVERAGE()
	{
		return  new Resource(DC_NS . 'coverage');

	}

	function CREATOR()
	{
		return  new Resource(DC_NS . 'creator');

	}

	function DATE()
	{
		return  new Resource(DC_NS . 'date');

	}

	function DESCRIPTION()
	{
		return  new Resource(DC_NS . 'description');

	}

	function FORMAT()
	{
		return  new Resource(DC_NS . 'format');

	}

	function IDENTIFIER()
	{
		return  new Resource(DC_NS . 'identifier');

	}

	function LANGUAGE()
	{
		return  new Resource(DC_NS . 'language');

	}

	function PUBLISHER()
	{
		return  new Resource(DC_NS . 'publisher');

	}

	function RIGHTS()
	{
		return  new Resource(DC_NS . 'rights');

	}

	function SOURCE()
	{
		return  new Resource(DC_NS . 'source');

	}

	function SUBJECT()
	{
		return  new Resource(DC_NS . 'subject');

	}

	function TITLE()
	{
		return  new Resource(DC_NS . 'title');

	}

	function TYPE()
	{
		return  new Resource(DC_NS . 'type');
	}


	// Other Elements and Element Refinements
	function ABSTRACT_()
	{
		return  new Resource(DCTERM_NS . 'abstract');

	}

	function ACCESS_RIGHTS()
	{
		return  new Resource(DCTERM_NS . 'accessRights');

	}

	function ALTERNATIVE()
	{
		return  new Resource(DCTERM_NS . 'alternative');

	}

	function AUDIENCE()
	{
		return  new Resource(DCTERM_NS . 'audience');

	}

	function AVAILABLE()
	{
		return  new Resource(DCTERM_NS . 'available');

	}

	function BIBLIOGRAPHIC_CITATION()
	{
		return  new Resource(DCTERM_NS . 'bibliographicCitation');

	}

	function CONFORMS_TO()
	{
		return  new Resource(DCTERM_NS . 'conformsTo');

	}

	function CREATED()
	{
		return  new Resource(DCTERM_NS . 'created');

	}

	function DATE_ACCEPTED()
	{
		return  new Resource(DCTERM_NS . 'dateAccepted');

	}

	function DATE_COPYRIGHTED()
	{
		return  new Resource(DCTERM_NS . 'dateCopyrighted');

	}

	function DATE_SUBMITTED()
	{
		return  new Resource(DCTERM_NS . 'dateSubmitted');

	}

	function EDUCATION_LEVEL()
	{
		return  new Resource(DCTERM_NS . 'educationLevel');

	}

	function EXTENT()
	{
		return  new Resource(DCTERM_NS . 'extent');

	}

	function HAS_FORMAT()
	{
		return  new Resource(DCTERM_NS . 'hasFormat');

	}

	function HAS_PART()
	{
		return  new Resource(DCTERM_NS . 'hasPart');

	}

	function HAS_VERSION()
	{
		return  new Resource(DCTERM_NS . 'hasVersion');

	}

	function IS_FORMAT_OF()
	{
		return  new Resource(DCTERM_NS . 'isFormatOf');

	}

	function IS_PART_OF()
	{
		return  new Resource(DCTERM_NS . 'isPartOf');

	}

	function IS_REFERENCED_BY()
	{
		return  new Resource(DCTERM_NS . 'isReferencedBy');

	}

	function IS_REPLACED_BY()
	{
		return  new Resource(DCTERM_NS . 'isReplacedBy');

	}

	function IS_REQUIRED_BY()
	{
		return  new Resource(DCTERM_NS . 'isRequiredBy');

	}

	function ISSUED()
	{
		return  new Resource(DCTERM_NS . 'issued');

	}

	function IS_VERSION_OF()
	{
		return  new Resource(DCTERM_NS . 'isVersionOf');

	}

	function LICENSE()
	{
		return  new Resource(DCTERM_NS . 'license');

	}

	function MEDIATOR()
	{
		return  new Resource(DCTERM_NS . 'mediator');

	}

	function MEDIUM()
	{
		return  new Resource(DCTERM_NS . 'medium');

	}

	function MODIFIED()
	{
		return  new Resource(DCTERM_NS . 'modified');

	}

	function REFERENCES()
	{
		return  new Resource(DCTERM_NS . 'references');

	}

	function REPLACES()
	{
		return  new Resource(DCTERM_NS . 'replaces');

	}

	function REQUIRES()
	{
		return  new Resource(DCTERM_NS . 'requires');

	}

	function RIGHTS_HOLDER()
	{
		return  new Resource(DCTERM_NS . 'rightsHolder');

	}

	function SPATIAL()
	{
		return  new Resource(DCTERM_NS . 'spatial');

	}

	function TABLE_OF_CONTENTS()
	{
		return  new Resource(DCTERM_NS . 'tableOfContents');

	}

	function TEMPORAL()
	{
		return  new Resource(DCTERM_NS . 'temporal');

	}

	function VALID()
	{
		return  new Resource(DCTERM_NS . 'valid');

	}


	// Encoding schemes
	function BOX()
	{
		return  new Resource(DCTERM_NS . 'Box');

	}

	function DCMI_TYPE()
	{
		return  new Resource(DCTERM_NS . 'DCMIType');

	}

	function IMT()
	{
		return  new Resource(DCTERM_NS . 'IMT');

	}

	function ISO3166()
	{
		return  new Resource(DCTERM_NS . 'ISO3166');

	}

	function ISO639_2()
	{
		return  new Resource(DCTERM_NS . 'ISO639-2');

	}

	function LCC()
	{
		return  new Resource(DCTERM_NS . 'LCC');

	}

	function LCSH()
	{
		return  new Resource(DCTERM_NS . 'LCSH');

	}

	function MESH()
	{
		return  new Resource(DCTERM_NS . 'MESH');

	}

	function PERIOD()
	{
		return  new Resource(DCTERM_NS . 'Period');

	}

	function POINT()
	{
		return  new Resource(DCTERM_NS . 'Point');

	}

	function RFC1766()
	{
		return  new Resource(DCTERM_NS . 'RFC1766');

	}

	function RFC3066()
	{
		return  new Resource(DCTERM_NS . 'RFC3066');

	}

	function TGN()
	{
		return  new Resource(DCTERM_NS . 'TGN');

	}

	function UDC()
	{
		return  new Resource(DCTERM_NS . 'UDC');

	}

	function URI()
	{
		return  new Resource(DCTERM_NS . 'URI');

	}

	function W3CDTF()
	{
		return  new Resource(DCTERM_NS . 'W3CDTF');

	}


	// DCMI Type Vocabulary
	function COLLECTION()
	{
		return  new Resource(DCMITYPE_NS . 'Collection');

	}

	function DATASET()
	{
		return  new Resource(DCMITYPE_NS . 'Dataset');

	}

	function EVENT()
	{
		return  new Resource(DCMITYPE_NS . 'Event');

	}

	function IMAGE()
	{
		return  new Resource(DCMITYPE_NS . 'Image');

	}

	function INTERACTIVERESOURCE()
	{
		return  new Resource(DCMITYPE_NS . 'Interactive_Resource');

	}

	function MOVINGIMAGE()
	{
		return  new Resource(DCMITYPE_NS . 'Moving_Image');

	}

	function PHYSICALOBJECT()
	{
		return  new Resource(DCMITYPE_NS . 'Physical_Object');

	}

	function SERVICE()
	{
		return  new Resource(DCMITYPE_NS . 'Service');

	}

	function SOFTWARE()
	{
		return  new Resource(DCMITYPE_NS . 'Software');

	}

	function SOUND()
	{
		return  new Resource(DCMITYPE_NS . 'Sound');

	}

	function STILLIMAGE()
	{
		return  new Resource(DCMITYPE_NS . 'Still_Image');

	}

	function TEXT()
	{
		return  new Resource(DCMITYPE_NS . 'Text');
	}

}


?>