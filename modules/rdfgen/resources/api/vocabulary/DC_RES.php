<?php
/**
*   Dublin Core Vocabulary (ResResource)
*
*   @version $Id: DC_RES.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Chris Bizer (chris@bizer.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of the Dublin
*   Core Vocabulary. For details about DC see: http://dublincore.org/
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/
class DC_RES{
	// DC concepts
	function CONTRIBUTOR()
	{
		return  new ResResource(DC_NS . 'contributor');

	}

	function COVERAGE()
	{
		return  new ResResource(DC_NS . 'coverage');

	}

	function CREATOR()
	{
		return  new ResResource(DC_NS . 'creator');

	}

	function DATE()
	{
		return  new ResResource(DC_NS . 'date');

	}

	function DESCRIPTION()
	{
		return  new ResResource(DC_NS . 'description');

	}

	function FORMAT()
	{
		return  new ResResource(DC_NS . 'format');

	}

	function IDENTIFIER()
	{
		return  new ResResource(DC_NS . 'identifier');

	}

	function LANGUAGE()
	{
		return  new ResResource(DC_NS . 'language');

	}

	function PUBLISHER()
	{
		return  new ResResource(DC_NS . 'publisher');

	}

	function RIGHTS()
	{
		return  new ResResource(DC_NS . 'rights');

	}

	function SOURCE()
	{
		return  new ResResource(DC_NS . 'source');

	}

	function SUBJECT()
	{
		return  new ResResource(DC_NS . 'subject');

	}

	function TITLE()
	{
		return  new ResResource(DC_NS . 'title');

	}

	function TYPE()
	{
		return  new ResResource(DC_NS . 'type');
	}


	// Other Elements and Element Refinements
	function ABSTRACT_()
	{
		return  new ResResource(DCTERM_NS . 'abstract');

	}

	function ACCESS_RIGHTS()
	{
		return  new ResResource(DCTERM_NS . 'accessRights');

	}

	function ALTERNATIVE()
	{
		return  new ResResource(DCTERM_NS . 'alternative');

	}

	function AUDIENCE()
	{
		return  new ResResource(DCTERM_NS . 'audience');

	}

	function AVAILABLE()
	{
		return  new ResResource(DCTERM_NS . 'available');

	}

	function BIBLIOGRAPHIC_CITATION()
	{
		return  new ResResource(DCTERM_NS . 'bibliographicCitation');

	}

	function CONFORMS_TO()
	{
		return  new ResResource(DCTERM_NS . 'conformsTo');

	}

	function CREATED()
	{
		return  new ResResource(DCTERM_NS . 'created');

	}

	function DATE_ACCEPTED()
	{
		return  new ResResource(DCTERM_NS . 'dateAccepted');

	}

	function DATE_COPYRIGHTED()
	{
		return  new ResResource(DCTERM_NS . 'dateCopyrighted');

	}

	function DATE_SUBMITTED()
	{
		return  new ResResource(DCTERM_NS . 'dateSubmitted');

	}

	function EDUCATION_LEVEL()
	{
		return  new ResResource(DCTERM_NS . 'educationLevel');

	}

	function EXTENT()
	{
		return  new ResResource(DCTERM_NS . 'extent');

	}

	function HAS_FORMAT()
	{
		return  new ResResource(DCTERM_NS . 'hasFormat');

	}

	function HAS_PART()
	{
		return  new ResResource(DCTERM_NS . 'hasPart');

	}

	function HAS_VERSION()
	{
		return  new ResResource(DCTERM_NS . 'hasVersion');

	}

	function IS_FORMAT_OF()
	{
		return  new ResResource(DCTERM_NS . 'isFormatOf');

	}

	function IS_PART_OF()
	{
		return  new ResResource(DCTERM_NS . 'isPartOf');

	}

	function IS_REFERENCED_BY()
	{
		return  new ResResource(DCTERM_NS . 'isReferencedBy');

	}

	function IS_REPLACED_BY()
	{
		return  new ResResource(DCTERM_NS . 'isReplacedBy');

	}

	function IS_REQUIRED_BY()
	{
		return  new ResResource(DCTERM_NS . 'isRequiredBy');

	}

	function ISSUED()
	{
		return  new ResResource(DCTERM_NS . 'issued');

	}

	function IS_VERSION_OF()
	{
		return  new ResResource(DCTERM_NS . 'isVersionOf');

	}

	function LICENSE()
	{
		return  new ResResource(DCTERM_NS . 'license');

	}

	function MEDIATOR()
	{
		return  new ResResource(DCTERM_NS . 'mediator');

	}

	function MEDIUM()
	{
		return  new ResResource(DCTERM_NS . 'medium');

	}

	function MODIFIED()
	{
		return  new ResResource(DCTERM_NS . 'modified');

	}

	function REFERENCES()
	{
		return  new ResResource(DCTERM_NS . 'references');

	}

	function REPLACES()
	{
		return  new ResResource(DCTERM_NS . 'replaces');

	}

	function REQUIRES()
	{
		return  new ResResource(DCTERM_NS . 'requires');

	}

	function RIGHTS_HOLDER()
	{
		return  new ResResource(DCTERM_NS . 'rightsHolder');

	}

	function SPATIAL()
	{
		return  new ResResource(DCTERM_NS . 'spatial');

	}

	function TABLE_OF_CONTENTS()
	{
		return  new ResResource(DCTERM_NS . 'tableOfContents');

	}

	function TEMPORAL()
	{
		return  new ResResource(DCTERM_NS . 'temporal');

	}

	function VALID()
	{
		return  new ResResource(DCTERM_NS . 'valid');

	}


	// Encoding schemes
	function BOX()
	{
		return  new ResResource(DCTERM_NS . 'Box');

	}

	function DCMI_TYPE()
	{
		return  new ResResource(DCTERM_NS . 'DCMIType');

	}

	function IMT()
	{
		return  new ResResource(DCTERM_NS . 'IMT');

	}

	function ISO3166()
	{
		return  new ResResource(DCTERM_NS . 'ISO3166');

	}

	function ISO639_2()
	{
		return  new ResResource(DCTERM_NS . 'ISO639-2');

	}

	function LCC()
	{
		return  new ResResource(DCTERM_NS . 'LCC');

	}

	function LCSH()
	{
		return  new ResResource(DCTERM_NS . 'LCSH');

	}

	function MESH()
	{
		return  new ResResource(DCTERM_NS . 'MESH');

	}

	function PERIOD()
	{
		return  new ResResource(DCTERM_NS . 'Period');

	}

	function POINT()
	{
		return  new ResResource(DCTERM_NS . 'Point');

	}

	function RFC1766()
	{
		return  new ResResource(DCTERM_NS . 'RFC1766');

	}

	function RFC3066()
	{
		return  new ResResource(DCTERM_NS . 'RFC3066');

	}

	function TGN()
	{
		return  new ResResource(DCTERM_NS . 'TGN');

	}

	function UDC()
	{
		return  new ResResource(DCTERM_NS . 'UDC');

	}

	function URI()
	{
		return  new ResResource(DCTERM_NS . 'URI');

	}

	function W3CDTF()
	{
		return  new ResResource(DCTERM_NS . 'W3CDTF');

	}


	// DCMI Type Vocabulary
	function COLLECTION()
	{
		return  new ResResource(DCMITYPE_NS . 'Collection');

	}

	function DATASET()
	{
		return  new ResResource(DCMITYPE_NS . 'Dataset');

	}

	function EVENT()
	{
		return  new ResResource(DCMITYPE_NS . 'Event');

	}

	function IMAGE()
	{
		return  new ResResource(DCMITYPE_NS . 'Image');

	}

	function INTERACTIVE_RESOURCE()
	{
		return  new ResResource(DCMITYPE_NS . 'Interactive_Resource');

	}

	function MOVINGIMAGE()
	{
		return  new ResResource(DCMITYPE_NS . 'Moving_Image');

	}

	function PHYSICALOBJECT()
	{
		return  new ResResource(DCMITYPE_NS . 'Physical_Object');

	}

	function SERVICE()
	{
		return  new ResResource(DCMITYPE_NS . 'Service');

	}

	function SOFTWARE()
	{
		return  new ResResource(DCMITYPE_NS . 'Software');

	}

	function SOUND()
	{
		return  new Resource(DCMITYPE_NS . 'Sound');

	}

	function STILLIMAGE()
	{
		return  new ResResource(DCMITYPE_NS . 'Still_Image');

	}

	function TEXT()
	{
		return  new ResResource(DCMITYPE_NS . 'Text');
	}
}



?>