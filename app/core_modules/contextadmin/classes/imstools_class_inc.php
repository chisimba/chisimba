<?php
/**
 * The class imstools that manages the creation of the imsmanifest.xml file
 * 
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   contextadmin
 * @author    Jarrett Jordaan
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   1.0
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
class imstools extends object 
{

	/**
	 * The constructor
	*/
	function init()
	{
        	$this->objUser =& $this->getObject('user', 'security');
		//Load Import Export Utilities class
		$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
	}

	function createResource($dom, $lom, $eduCommons)
	{
		$support = $this->lomSupported();
		// LOM Structure
		// Some Notes:
		// If an element occurs 0 or more times, the smallest permitted maximum is 10 instances.
		// Unless otherwise specified.
		// Not all LOM metadata fields are supported, and although eduCommons may populate some of these extra fields on output, it only supports reading data from the following fields
		// LOM Metadata Fields Read By eduCommons on Import:
		// GENERAL:
		// Title.
		// Language.
		// Description.
		// Keyword.
		// LIFECYCLE:
		// contribute.
		// RIGHTS
		// 
		$languageDefault = 'en';
		$definedVocab = 'LOMv1.0';
		// LOM Defined Vocabularies for <structure>:
		// Collection.
		// Mixed.
		// Linear.
		// Hierarchical.
		// Networked.
		// Branched.
		// Parceled.
		// Atomic.
		$structureDefault = 'Hierarchical';
		// LOM Defined Vocabularies for <aggregationlevel>:
		// 1 - the smallest level of aggregation, e.g. raw media data or fragments
		// 2 - a collection of atoms, e.g. an HTML document with some embedded pictures or a lesson
		// 3 - a collection of level 2 learning objects, e.g. a 'web' of HTML documents, with an index page that links the pages together or a course
		// 4 - the largest level of granularity, e.g. a set of courses that lead to a certificate. 
		// Create resource.
		$aggLevelDefault = '2';
		// LOM Defined Vocabularies for <status>:
		// Draft
		// Final
		// Revised
		// Unavailable
		$statusDefault = 'Unavailable';
		// LOM Defined Vocabularies for <role>:
		// Author
		// Publisher Unknown
		// Initiator
		// Terminator
		// Validator
		// Editor
		// Graphical Designer
		// Technical Implementer
		// Content Provider
		// Technical Validator
		// Educational Validator
		// Script Writer
		// Instructional Designer 
		$roleDefault = 'Author';
		// LOM Defined Vocabularies for <requirment><type>:
		// Operating System.
		// Browser 
		$requireOS = 'Operating System';
		$requireBrowser = 'Browser';
		// LOM Defined Vocabularies for <requirment><type><application>:
		// PC-DOS.
		// MS-Windows.
		// MacOS.
		// Unix.
		// Multi-OS
		// None
		$requirementsOS = 'None';
		// LOM Defined Vocabularies for <requirment><type><application>:
		// Any.
		// Netscape Communicator.
		// Microsoft Internet Explorer.
		// Opera.
		// Amaya.
		$requirementsBrowser = 'Any';
		// LOM Defined Vocabularies for <interactivitytype>:
		// Active.
		// Expositive.
		// Mixed.
		// Undefined.
		$interActivityTypeDefault = 'Undefined';
		// LOM Defined Vocabularies for <learningresourcetype>:
		// Exercise.
		// Simulation.
		// Questionnaire.
		// Diagram.
		// Figure.
	 	// Graph.
		// Index.
		// Slide.
		// Table.
		// Narrative Text.
		// Exam.
		// Experiment.
		// ProblemStatement.
		// SelfAssesment .
		$resourseTypeDefault = 'Narrative Text';
		// LOM Defined Vocabularies for <interactivitylevel>:
		// very low.
		// low.
		// medium.
		// high.
		// very high.
		$interActivityLevelDefault = 'medium';
		// LOM Defined Vocabularies for <semanticdensity>:
		// very low.
		// low.
		// medium.
		// high.
		// very high.
		$semanticDensityDefault = 'medium';
		// LOM Defined Vocabularies for <intendedenduserrole>:
		// Teacher.
		// Author.
		// Learner.
		// Manager .
		$intendedEndUserRoleDefault = 'learner';
		// LOM Defined Vocabularies for <context>:
		// Primary Education.
		// Secondary Education.
		// Higher Education.
		// University First Cycle.
		// University Second Cycle.
		// University Postgrade.
		// Technical School First Cycle.
		// Technical School Second Cycle.
		// Professional Formation.
		// Continuous Formation.
		// Vocational Training .
		$contextDefault = 'Higher Education';
		// LOM Defined Vocabularies for <difficulty>:
		// very easy.
		// easy.
		// medium.
		// difficult.
		// very difficult.
		$difficultyDefault = 'medium';
		// LOM Defined Vocabularies for <cost>:
		// yes.
		// no.
		$costDefault = 'no';
		// LOM Defined Vocabularies for <copyrighandotherrestrictions>:
		// yes.
		// no.
		$copyrighAndOtherRestrictionsDefault = 'no';
		// LOM Defined Vocabularies for <kind>:
		// IsPartOf.
		// HasPart.
		// IsVersionOf.
		// HasVersion.
		// IsFormatOf.
		// HasFormat.
		// References.
		// IsReferencedBy.
		// IsBasedOn.
		// IsBasisFor.
		// Requires.
		// IsRequiredBy.
		$kindDefault = 'IsPartOf';
		// LOM Defined Vocabularies for <$purpose>:
		// Discipline.
		// Idea.
		// Prerequisite.
		// Educational Objective.
		// Accessibility Restrictions.
		// Educational Level.
		// Skill Level.
		// Security Level.
		$purposeDefault = 'Educational Objective';
		// Retrieve resource data from system.
		$locationURI = '';
		$resource = $dom->createElement('resource');
		$resource->setAttribute('identifier', $resId);
		$resource->setAttribute('type', 'webcontent');
		$resource->setAttribute('href', $location);
		$metadata = $resource->appendChild($dom->createElement('metadata'));
		// Name : <lom> Element in <metadata>.
		// Description : General information that describes the learning object as a whole.
		// Multiplicity : This element should occur 1 and only 1 time in an IMS XML Meta-Data instance.
		// Attributes Contained in <lom>:
		// xmlns - indication of the IMS Meta-Data Namespace.
		// Elements Contained in <lom>:
		// <general>.
		// <lifecycle>.
		// <metametadata>.
		// <technical>.
		// <educational>.
		// <rights>.
		// <relation>.
		// <annotation>.
		// <classification>.
		$lom = $metadata->appendChild($dom->createElement('lom'));
		$lom->setAttribute('xmlns', 'http://www.imsglobal.org/xsd/imsmd_v1p2');
		if($support['general'])
		{
			$generalSupport = $this->lomGeneral();
		// SECTION 1
		// Name : <general> Element in <lom>.
		// Description : General information that describes the learning object as a whole.
		// Multiplicity : The <general> element occurs 0 or 1 time within the top-level <lom> element.
		// Attributes Contained in <general>:
		// none
		// Elements Contained in <general>:
		// <identifier>.
		// <title>.
		// <catalogentry>.
		// <language>.
		// <description>.
		// <keyword>.
		// <coverage>.
		// <structure>.
		// <aggregationlevel>.
			$general = $lom->appendChild($dom->createElement('general'));
			if($generalSupport['identifier'])
			{
		// Name : <identifier> Element in <general>.
		// Description : A globally unique label that identifies this learning object.
		// Multiplicity : The <identifier> element occurs 0 or 1 time within the <general> element.
		// Elements Contained in <identifier>:
		// none.
		// Attributes Contained in <identifier>:
		// none.
				$identifier = $general->appendChild($dom->createElement('identifier'));
			}
			if($generalSupport['title'])
			{
		// Name : <title> Element in <general>.
		// Description : Name given to the learning object.
		// Multiplicity : The <title> element occurs 0 or 1 time within the <general> element.
		// Attributes Contained in <title>:
		// none.
		// Elements Contained in <title>:
		// <langstring>.
				$title = $general->appendChild($dom->createElement('title'));
		// Name : <langstring> Element in <title>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
				$langstring = $title->appendChild($dom->createElement('langstring'));
				$langstring->setAttribute('xml:lang', $languageDefault);
			}
			if($generalSupport['catalogentry'])
			{
		// Name : <catalogentry> Element in <general>.
		// Description : This data element defines an entry within a catalog assigned to this learning object. ie., An ISBN number.
		// Multiplicity : The <catalogentry> element occurs 0 or more times within the <general> element.
		// Attributes Contained in <catalogentry>:
		// none.
		// Elements Contained in <catalogentry>:
		// <catalog>
		// <entry>
				$catalogentry = $general->appendChild($dom->createElement('catalogentry'));
		// Name : <catalog> Element in <catalogentry>.
		// Description :  The name of the catalog (i.e. listing identification system).
		// Multiplicity : The <catalog> element must occur 1 and only 1 time within the <catalogentry> element
		// Attributes Contained in <catalog>:
		// none.
		// Elements Contained in <catalog>:
		// none.
				$catalog = $catalogentry->appendChild($dom->createElement('catalog'));
		// Name : <entry> Element in <catalogentry>.
		// Description : Actual string value of the entry within the catalog.
		// Multiplicity : The <entry> element occurs 1 and only 1 time with the <catalogentry> element. If the <catalogentry> element is used.
		// Attributes Contained in <entry>:
		// none.
		// Elements Contained in <entry>:
		// <langstring>.
				$entry = $catalogentry->appendChild($dom->createElement('entry'));
		// Name : <langstring> Element in <entry>.
		// Description : Entry language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
				$langstring = $entry->appendChild($dom->createElement('langstring'));
				$langstring->setAttribute('xml:lang', $languageDefault);
			}
			if($generalSupport['language'])
			{
		// Name : <language> Element in <general>.
		// Description : The primary human language or languages used within this learning object.
		// Multiplicity : The <language> element occurs 0 or more times within the <general> element.
		// Attributes Contained in <language>:
		// none.
		// Elements Contained in <language>:
		// none.
				$language = $general->appendChild($dom->createElement('language'));
			}
			if($generalSupport['description'])
			{
		// Name : <description> Element in <general>.
		// Description :   A textual description of the content of this learning object.
		// Multiplicity : The <description> element occurs 0 or more times within the <general> element.
		// Attributes Contained in <description>:
		// none.
		// Elements Contained in <description>:
		// <langstring>.
				$description = $general->appendChild($dom->createElement('description'));
		// Name : <langstring> Element in <description>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// none.
		// Elements Contained in <langstring>:
		// none.
				$langstring = $description->appendChild($dom->createElement('langstring'));
			}
			if($generalSupport['keyword'])
			{
		// Name : <keyword> Element in <general>.
		// Description :  A collection of keywords or phrases describing this learning object.
		// Multiplicity : The <keyword> element occurs 0 or more times within the <general> element.
		// Attributes Contained in <keyword>:
		// none.
		// Elements Contained in <keyword>:
		// <langstring>.
				$keyword = $general->appendChild($dom->createElement('keyword'));
		// Name : <langstring> Element in <keyword>.
		// Description : Entry language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times within the <general> element.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
				$langstring = $keyword->appendChild($dom->createElement('langstring'));
				$langstring->setAttribute('xml:lang', $languageDefault);
			}
			if($generalSupport['coverage'])
			{
		// Name : <coverage> Element in <general>.
		// Description : The span or extent of such things as time, culture, geography or region.
		// Multiplicity : The <coverage> element occurs 0 or more times within the <general> element.
		// Attributes Contained in <coverage>:
		// none.
		// Elements Contained in <coverage>:
		// <langstring>.
				$coverage = $general->appendChild($dom->createElement('coverage'));
		// Name : <langstring> Element in <coverage>.
		// Description : Entry language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times within the <coverage> element.
		// Attributes Contained in <language>:
		// none.
		// Elements Contained in <language>:
		// none.
				$langstring = $coverage->appendChild($dom->createElement('langstring'));
				$langstring->setAttribute('xml:lang', $languageDefault);
			}
			if($generalSupport['structure'])
			{
		// Name : <structure> Element in <general>.
		// Description : Title language.
		// Multiplicity : The <structure> element occur 0 or 1 time within the <general> element.
		// Attributes Contained in <structure>:
		// none.
		// Elements Contained in <structure>:
		// <source>.
		// <value>.
				$structure = $general->appendChild($dom->createElement('structure'));
		// Name : <source> Element in <structure>.
		// Description : Source of structure.
		// Multiplicity : The <source> element occur 1 and only 1 time in <structure> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
				$source = $structure->appendChild($dom->createElement('source', $definedVocab));
				$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <structure>.
		// Description : LOM Defined Vocabularies for structure.
		// Multiplicity : The <value> element occur 1 and only 1 time in <structure> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
				$value = $structure->appendChild($dom->createElement('value', $structureDefault));
				$value->setAttribute('xml:lang', $languageDefault);
			}
			if($generalSupport['aggregationlevel'])
			{
		// Name : <aggregationlevel> Element in <general>.
		// Description : The functional granularity of this learning object.
		// Multiplicity : The <aggregationlevel> element occurs 0 or 1 time within the <general> element.
		// Attributes Contained in <aggregationlevel>:
		// none.
		// Elements Contained in <aggregationlevel>:
		// <source>.
		// <value>.
				$aggregationlevel = $general->appendChild($dom->createElement('aggregationlevel'));
		// Name : <source> Element in <aggregationlevel>.
		// Description : Source of structure.
		// Multiplicity : The <source> element occur 1 and only 1 time in <aggregationlevel> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
				$source = $aggregationlevel->appendChild($dom->createElement('source', $definedVocab));
				$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <structure>.
		// Description : LOM Defined Vocabularies for aggregation level.
		// Multiplicity : The <value> element occur 1 and only 1 time in <aggregationlevel> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
				$value = $aggregationlevel->appendChild($dom->createElement('value', $aggLevelDefault));
				$value->setAttribute('xml:lang', $languageDefault);
			}
		}
   		if($support['lifecycle'])
		{
			$lifecycleSupport = $this->lomLifecycle();
		// SECTION 2
		// Name : <lifecycle> Element in <lom>.
		// Description : Features related to the history and current state of this learning object and those who have affected this learning object during its evolution.
		// Multiplicity : The <lifecycle> element occurs 0 or 1 time within <lom> element.
		// Attributes Contained in <lifecycle>:
		// none.
		// Elements Contained in <lifecycle>:
		// <version>.
		// <status>.
		// <contribute>.
			$lifecycle = $lom->appendChild($dom->createElement('lifecycle'));
			if($lifecycleSupport['version'])
			{
		// Name : <version> Element in <lifecycle>.
		// Description : The edition of this learning object.
		// Multiplicity : The <version> element occurs 0 or 1 time within the <lifecycle> element.
		// Attributes Contained in <version>:
		// none.
		// Elements Contained in <version>:
		// <langstring>.
				$version = $lifecycle->appendChild($dom->createElement('version'));
		// Name : <langstring> Element in <version>.
		// Description : Entry language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times within the <coverage> element.
		// Attributes Contained in <language>:
		// none.
		// Elements Contained in <language>:
		// none.
				$langstring = $version->appendChild($dom->createElement('langstring'));
				$langstring->setAttribute('xml:lang', $languageDefault);
			}
			if($lifecycleSupport['status'])
			{
		// Name : <status> Element in <lifecycle>.
		// Description : The state or condition of this learning object.
		// Multiplicity : The <status> element occurs 0 or 1 time within the <lifecycle> element.
		// Attributes Contained in <status>:
		// none.
		// Elements Contained in <status>:
		// <source>.
		// <value>.
				$status = $lifecycle->appendChild($dom->createElement('status'));
		// Name : <source> Element in <status>.
		// Description : Source of status.
		// Multiplicity : The <source> element occur 1 and only 1 time in <structure> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
				$source = $status->appendChild($dom->createElement('source', $definedVocab));
				$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <status>.
		// Description : LOM Defined Vocabularies for status.
		// Multiplicity : The <value> element occur 1 and only 1 time in <status> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
				$value = $status->appendChild($dom->createElement('value', $statusDefault));
				$value->setAttribute('xml:lang', $languageDefault);
			}
			if($lifecycleSupport['contribute'])
			{
		// Name : <contribute> Element in <lifecycle>.
		// Description : This data element describes those people or organizations that have affected the state of this learning object during its evolution.
		// Multiplicity : The <contribute> element occurs 0 or more times within the <lifecycle> element. Maximum permitted instances is 30.
		// Attributes Contained in <contribute>:
		// none.
		// Elements Contained in <contribute>:
		// <role>.
		// <centity>.
		// <date>.
				$contribute = $lifecycle->appendChild($dom->createElement('contribute'));
		// Name : <role> Element in <contribute>.
		// Description : This data element describes the kind of contribution. It is recommended that at least the Author(s) of the learning object should be described.
		// Multiplicity : The <role> element occurs 1 and only 1 time within the <contribute> element. If the <contribute> element is used
		// Attributes Contained in <role>:
		// none.
		// Elements Contained in <role>:
		// <source>.
		// <value>.
				$role = $contribute->appendChild($dom->createElement('role'));
		// Name : <source> Element in <role>.
		// Description : Source of role.
		// Multiplicity : The <source> element occur 1 and only 1 time in <structure> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
				$source = $role->appendChild($dom->createElement('source', $definedVocab));
				$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <status>.
		// Description : LOM Defined Vocabularies for status.
		// Multiplicity : The <value> element occur 1 and only 1 time in <role> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
				$value = $role->appendChild($dom->createElement('value', $roleDefault));
				$value->setAttribute('xml:lang', $languageDefault);
		// Name : <centity> Element in <contribute>.
		// Description : This data element is the identification of and information about people or organizations contributing to this learning object, most relevant first.
		// Multiplicity : The <centity> element occurs 0 or more times within the <contribute> element. Maximum permitted instances is 40.
		// Attributes Contained in <centity>:
		// none.
		// Elements Contained in <centity>:
		// <vcard>.
				$centity = $contribute->appendChild($dom->createElement('centity'));
				$vcard = $centity->appendChild($dom->createElement('vcard'));
		// Name : <date> Element in <contribute>.
		// Description : This data element describes date of the contribution.
		// Multiplicity : The <date> element occurs 0 or 1 time within the <contribute> element.
		// Attributes Contained in <date>:
		// none.
		// Elements Contained in <date>:
		// <datetime>.
		// <description>.
				$date = $contribute->appendChild($dom->createElement('date'));
				$datetime = $date->appendChild($dom->createElement('datetime'));
				$description = $date->appendChild($dom->createElement('description'));
			}
		}
		if($support['metametadata'])
		{
			$metametadataSupport = $this->lomMetametadata();
		// SECTION 3
		// Name : <metametadata> Element in <lom>.
		// Description : Groups information about the meta-data instance itself.
		// Multiplicity : The <metametadata> element occurs 0 or 1 time within <lom> element.
		// Attributes Contained in <metametadata>:
		// none.
		// Elements Contained in <metametadata>:
		// <identifier>.
		// <catalogentry>.
		// <contribute>.
		// <metadatascheme>.
		// <language>.
			$metametadata = $lom->appendChild($dom->createElement('metametadata'));
			if($metametadataSupport['identifier'])
			{
		// Name : <identifier> Element in <metametadata>.
		// Description : A globally unique label that identifies this meta-data instance.
		// Multiplicity : The <identifier> element occurs 0 or 1 time within the <metametadata> element.
		// Attributes Contained in <identifier>:
		// none.
		// Elements Contained in <identifier>:
		// none.
				$identifier = $metametadata->appendChild($dom->createElement('identifier'));
			}
			if($metametadataSupport['catalogentry'])
			{
		// Name : <catalogentry> Element in <metametadata>.
		// Description : This data element defines an entry within a catalog assigned to this learning object. ie., An ISBN number.
		// Multiplicity : The <catalogentry> element occurs 0 or more times within the <general> element.
		// Attributes Contained in <catalogentry>:
		// none.
		// Elements Contained in <catalogentry>:
		// <catalog>
		// <entry>
				$catalogentry = $metametadata->appendChild($dom->createElement('catalogentry'));
		// Name : <catalog> Element in <catalogentry>.
		// Description :  The name of the catalog (i.e. listing identification system).
		// Multiplicity : The <catalog> element must occur 1 and only 1 time within the <catalogentry> element
		// Attributes Contained in <catalog>:
		// none.
		// Elements Contained in <catalog>:
		// none.
				$catalog = $catalogentry->appendChild($dom->createElement('catalog'));
		// Name : <entry> Element in <catalogentry>.
		// Description : Actual string value of the entry within the catalog.
		// Multiplicity : The <entry> element occurs 1 and only 1 time with the <catalogentry> element. If the <catalogentry> element is used.
		// Elements Contained in <entry>:
		// <langstring>.
		// Attributes Contained in <entry>:
		// none.
				$entry = $catalogentry->appendChild($dom->createElement('entry'));
		// Name : <langstring> Element in <entry>.
		// Description : Entry language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
				$langstring = $entry->appendChild($dom->createElement('langstring'));
				$langstring->setAttribute('xml:lang', $languageDefault);
			}
			if($metametadataSupport['contribute'])
			{
		// Name : <contribute> Element in <metametadata>.
		// Description : This data element describes those people or organizations that have affected the state of this meta-data instance during its evolution.
		// Multiplicity : The <contribute> element occurs 0 or more times within the <metametadata> element.
		// Attributes Contained in <contribute>:
		// none.
		// Elements Contained in <contribute>:
		// <role>.
		// <centity>.
		// <date>.
				$contribute = $metametadata->appendChild($dom->createElement('contribute'));
		// Name : <role> Element in <contribute>.
		// Description : This data element describes the kind of contribution. It is recommended that at least the Creator(s) of the meta-data instance should be described.
		// Multiplicity : The <role> element occurs 1 and only 1 time within the <contribute> element. If the <contribute> element is used
		// Attributes Contained in <role>:
		// none.
		// Elements Contained in <role>:
		// <source>.
		// <value>.
				$role = $contribute->appendChild($dom->createElement('role'));
		// Name : <source> Element in <role>.
		// Description : Source of role.
		// Multiplicity : The <source> element occur 1 and only 1 time in <structure> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
				$source = $role->appendChild($dom->createElement('source', $definedVocab));
				$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <status>.
		// Description : LOM Defined Vocabularies for status.
		// Multiplicity : The <value> element occur 1 and only 1 time in <role> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
				$value = $role->appendChild($dom->createElement('value', $roleDefault));
				$value->setAttribute('xml:lang', $languageDefault);
		// Name : <centity> Element in <contribute>.
		// Description : This data element is the identification of and information about people or organizations contributing to this learning object, most relevant first.
		// Multiplicity : The <centity> element occurs 0 or more times within the <contribute> element. Maximum permitted instances is 40.
		// Attributes Contained in <centity>:
		// none.
		// Elements Contained in <centity>:
		// <vcard>.
				$centity = $contribute->appendChild($dom->createElement('centity'));
				$vcard = $centity->appendChild($dom->createElement('vcard'));
		// Name : <date> Element in <contribute>.
		// Description : This data element describes date of the contribution.
		// Multiplicity : The <date> element occurs 0 or 1 time within the <contribute> element.
		// Attributes Contained in <date>:
		// none.
		// Elements Contained in <date>:
		// <datetime>.
		// <description>.
				$date = $contribute->appendChild($dom->createElement('date'));
				$datetime = $date->appendChild($dom->createElement('datetime'));
				$description = $date->appendChild($dom->createElement('description'));
			}
			if($metametadataSupport['metadatascheme'])
			{
		// Name : <metadatascheme> Element in <metametadata>.
		// Description : This data element represents the name and version of the authoritative specification used to create this meta-data instance.
		// Multiplicity : The <metadatascheme> element occurs 0 or more times within the <metametadata> element.
		// Attributes Contained in <metadatascheme>:
		// none.
		// Elements Contained in <metadatascheme>:
		// none.
				$metadatascheme = $metametadata->appendChild($dom->createElement('metadatascheme', $definedVocab));
			}
			if($metametadataSupport['language'])
			{
		// Name : <language> Element in <metametadata>.
		// Description : This data element describes the language of this meta-data instance.
		// Multiplicity : The <language> element occurs 0 or 1 time within the <metametadata> element.
		// Attributes Contained in <language>:
		// none.
		// Elements Contained in <language>:
		// none.
				$language = $metametadata->appendChild($dom->createElement('language', $languageDefault));
			}
		}
		if($support['technical'])
		{
			$technicalSupport = $this->lomTechnical();
		// SECTION 4
		// Name : <technical> Element in <lom>.
		// Description : Groups the technical requirements and characteristics of the learning object.
		// Multiplicity : The <technical> element occurs 0 or 1 time within the top-level <lom> element.
		// Attributes Contained in <technical>:
		// none.
		// Elements Contained in <technical>:
		// <format>.
		// <size>.
		// <location>.
		// <requirement>.
		// <installationremarks>.
		// <otherplatformrequirements>.
		// <duration> .
			$technical = $lom->appendChild($dom->createElement('technical'));
			if($technicalSupport['format'])
			{
		// Name : <format> Element in <technical>.
		// Description : This data element describes the technical data type(s) of (all the components of) this learning object.
		// Multiplicity : The <format> element occurs 0 or more times within the <technical> element. Maximum permitted instances is 40.
		// Attributes Contained in <format>:
		// none.
		// Elements Contained in <format>:
		// none.
				$format = $technical->appendChild($dom->createElement('format'));
			}
			if($technicalSupport['size'])
			{
		// Name : <size> Element in <technical>.
		// Description :  This data element describes the size of the digital learning object in bytes.
		// Multiplicity : The <size> element occurs 0 or 1 time within the <technical> element.
		// Attributes Contained in <size>:
		// none.
		// Elements Contained in <size>:
		// none.
				$size = $technical->appendChild($dom->createElement('size'));
			}
			if($technicalSupport['location'])
			{
		// Name : <location> Element in <technical>.
		// Description : This data element is a string that is used to access this learning object. This is where the learning object described by this meta-data instance is physically located.
		// Multiplicity : The <location> element occurs 0 or more times within the <technical> element.
		// Attributes Contained in <location>:
		// type. Valid values:
		// URI - a resource available on the Internet with a specific address such as a URL.
		// TEXT - simple textual description of where the resource is located.
		// Elements Contained in <location>:
		// none.
				$location = $technical->appendChild($dom->createElement('location'));
				$location->setAttribute('type', $locationURI);
			}
			if($technicalSupport['requirement'])
			{
		// Name : <requirement> Element in <technical>.
		// Description :  This data element describes the size of the digital learning object in bytes.
		// Multiplicity : The <size> element occurs 0 or 1 time within the <technical> element.
		// Attributes Contained in <requirement>:
		// none.
		// Elements Contained in <requirement>:
		// <type>.
		// <name>.
		// <minimumversion>.
		// <maximumversion>.
				$requirement = $technical->appendChild($dom->createElement('requirement'));
		// Name : <type> Element in <technical>.
		// Description :  This data element describes the technology required to use this learning object.
		// Multiplicity : The <type> element occurs 0 or 1 time within the <requirement> element.
		// Attributes Contained in <type>:
		// none.
		// Elements Contained in <type>:
		// <source>.
		// <value>.
				$type = $requirement->appendChild($dom->createElement('type'));
		// Name : <source> Element in <type>.
		// Description : Source of type.
		// Multiplicity : The <source> element occur 1 and only 1 time in <type> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
				$source = $type->appendChild($dom->createElement('source', $definedVocab));
				$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <type>.
		// Description : LOM Defined Vocabularies for type.
		// Multiplicity : The <value> element occur 1 and only 1 time in <type> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
				$value = $type->appendChild($dom->createElement('value', $requireBrowser));
				$value->setAttribute('xml:lang', $languageDefault);
		// Name : <name> Element in <technical>.
		// Description : This data element describes name of the required technology to use this learning object.
		// Multiplicity : he <name> element occurs 0 or 1 time within the <requirement> element.
		// Attributes Contained in <name>:
		// none.
		// Elements Contained in <name>:
		// <source>.
		// <value>.
				$name = $requirement->appendChild($dom->createElement('name'));
		// Name : <source> Element in <name>.
		// Description : Source of name.
		// Multiplicity : The <source> element occur 1 and only 1 time in <requirement> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
				$source = $name->appendChild($dom->createElement('source', $definedVocab));
				$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <name>.
		// Description : LOM Defined Vocabularies for name.
		// Multiplicity : The <value> element occur 1 and only 1 time in <name> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
				$value = $name->appendChild($dom->createElement('value', $requirementsBrowser));
				$value->setAttribute('xml:lang', $languageDefault);
		// Name : <minimumversion> Element in <technical>.
		// Description : This data element describes the lowest possible version of the required technology to use this learning object.
		// Multiplicity : The <minimumversion> element occurs 0 or 1 time within the <requirement> element.
		// Attributes Contained in <minimumversion>:
		// none.
		// Elements Contained in <minimumversion>:
		// none.
				$minimumversion = $requirement->appendChild($dom->createElement('minimumversion'));
		// Name : <maximumversion> Element in <technical>.
		// Description : This data element describes the highest version of the technology known to support the use of this learning object.
		// Multiplicity : The <maximumversion> element occurs 0 or 1 time within the <requirement> element.
		// Attributes Contained in <maximumversion>:
		// none.
		// Elements Contained in <maximumversion>:
		// none.
				$maximumversion = $requirement->appendChild($dom->createElement('maximumversion'));
			}
			if($technicalSupport['installationremarks'])
			{
		// Name : <installationremarks> Element in <technical>.
		// Description : This data element contains the description of how to install this learning object.
		// Multiplicity : The <installationremarks> element occurs 0 or 1 time within the <technical> element.
		// Attributes Contained in <installationremarks>:
		// none.
		// Elements Contained in <installationremarks>:
		// langstring.
				$installationremarks = $technical->appendChild($dom->createElement('installationremarks'));
		// Name : <langstring> Element in <installationremarks>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
				$langstring = $installationremarks->appendChild($dom->createElement('langstring'));
				$langstring->setAttribute('xml:lang', $languageDefault);
			}
			if($technicalSupport['otherplatformrequirements'])
			{
		// Name : <otherplatformrequirements> Element in <technical>.
		// Description : This data element contains the description of how to install this learning object.
		// Multiplicity : The <installationremarks> element occurs 0 or 1 time within the <technical> element.
		// Attributes Contained in <otherplatformrequirements>:
		// none.
		// Elements Contained in <otherplatformrequirements>:
		// langstring.
				$otherplatformrequirements = $technical->appendChild($dom->createElement('otherplatformrequirements'));
		// Name : <langstring> Element in <otherplatformrequirements>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
				$langstring = $otherplatformrequirements->appendChild($dom->createElement('langstring'));
				$langstring->setAttribute('xml:lang', $languageDefault);
			}
			if($technicalSupport['duration'])
			{
		// Name : <duration> Element in <technical>.
		// Description : This data element the time a continuous learning object takes when played at the intended speed.
		// Multiplicity : The <duration> element occurs 0 or 1 time within the <technical> element.
		// Attributes Contained in <duration>:
		// none.
		// Elements Contained in <duration>:
		// <datetime>.
		// <description>.
				$duration = $technical->appendChild($dom->createElement('duration'));
				$datetime = $duration->appendChild($dom->createElement('datetime'));
				$description = $duration->appendChild($dom->createElement('description'));
			}
		}
		if($support['educational'])
		{
		// SECTION 5
		// Name : <educational> Element in <lom>.
		// Description : Conditions of use of the resource.
		// Multiplicity :  The <educational> element occurs 0 or 1 time within the top-level <lom> element.
		// Attributes Contained in <duration>:
		// none.
		// Elements Contained in <duration>:
		// <interactivitytype>.
		// <learningresourcetype>.
		// <interactivitylevel>.
		// <semanticdensity>.
		// <intendedenduserrole>.
		// <context>.
		// <typicalagerange>.
		// <difficulty>.
		// <typicallearningtime>.
		// <description>.
		// <language>.
		$educational = $lom->appendChild($dom->createElement('educational'));
		// Name : <interactivitytype> Element in <educational>.
		// Description : The type of interactivity supported by the learning object.
		// Multiplicity : The <interactivitytype> element occurs 0 or 1 time within the <educational> element.
		// Attributes Contained in <interactivitytype>:
		// none.
		// Elements Contained in <interactivitytype>:
		// <source>.
		// <value>.
		$interactivitytype = $educational->appendChild($dom->createElement('interactivitytype'));
		// Name : <source> Element in <interactivitytype>.
		// Description : Source of interactivitytype.
		// Multiplicity : The <source> element occur 1 and only 1 time in <interactivitytype> element.
		// Attributes Contained in <source>:
		// none.
		// Elements Contained in <source>:
		// none.
		$source = $interactivitytype->appendChild($dom->createElement('source', $definedVocab));
		// Name : <value> Element in <interactivitytype>.
		// Description : LOM Defined Vocabularies for interactivitytype.
		// Multiplicity : The <value> element occur 1 and only 1 time in <interactivitytype> element.
		// Attributes Contained in <value>:
		// none.
		// Elements Contained in <value>:
		// none.
		$value = $interactivitytype->appendChild($dom->createElement('value', $interActivityTypeDefault));
		// Name : <learningresourcetype> Element in <educational>.
		// Description : Specific kind of resource, most dominant kind first.
		// Multiplicity : The <learningresourcetype> element occurs 0 or more times within the <educational> element.
		// Attributes Contained in <learningresourcetype>:
		// none.
		// Elements Contained in <learningresourcetype>:
		// <source>.
		// <value>.
		$learningresourcetype = $educational->appendChild($dom->createElement('learningresourcetype'));
		// Name : <source> Element in <learningresourcetype>.
		// Description : Source of learningresourcetype.
		// Multiplicity : The <learningresourcetype> element occurs 0 or more times within the <educational> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
		$source = $learningresourcetype->appendChild($dom->createElement('source', $definedVocab));
		$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <learningresourcetype>.
		// Description : LOM Defined Vocabularies for learningresourcetype.
		// Multiplicity : The <value> element occur 1 and only 1 time in <learningresourcetype> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
		$value = $learningresourcetype->appendChild($dom->createElement('value', $resourseTypeDefault));
		$value->setAttribute('xml:lang', $languageDefault);
		// Name : <interactivitylevel> Element in <educational>.
		// Description : Level of interactivity between an end user and the learning object.
		// Multiplicity : The <interactivitylevel> element occurs 0 or 1 time within the <educational> element.
		// Attributes Contained in <interactivitylevel>:
		// none.
		// Elements Contained in <interactivitylevel>:
		// <source>.
		// <value>.
		$interactivitylevel = $educational->appendChild($dom->createElement('interactivitylevel'));
		// Name : <source> Element in <interactivitylevel>.
		// Description : Source of interactivitylevel.
		// Multiplicity : The <source> element occur 1 and only 1 time in <interactivitylevel> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
		$source = $interactivitylevel->appendChild($dom->createElement('source', $definedVocab));
		$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <interactivitylevel>.
		// Description : LOM Defined Vocabularies for interactivitylevel.
		// Multiplicity : The <value> element occur 1 and only 1 time in <interactivitylevel> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
		$value = $interactivitylevel->appendChild($dom->createElement('value', $interActivityLevelDefault));
		$value->setAttribute('xml:lang', $languageDefault);
		// Name : <semanticdensity> Element in <educational>.
		// Description : Subjective measure of the learning object's usefulness as.compared to its size or duration.
		// Multiplicity : The <semanticdensity> element occurs 0 or 1 time within the <educational> element.
		// Attributes Contained in <semanticdensity>:
		// none.
		// Elements Contained in <semanticdensity>:
		// <source>.
		// <value>.
		$semanticdensity = $educational->appendChild($dom->createElement('semanticdensity'));
		// Name : <source> Element in <semanticdensity>.
		// Description : Source of semanticdensity.
		// Multiplicity : The <source> element occur 1 and only 1 time in <semanticdensity> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
		$source = $semanticdensity->appendChild($dom->createElement('source', $definedVocab));
		$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <semanticdensity>.
		// Description : LOM Defined Vocabularies for interactivitylevel.
		// Multiplicity : The <value> element occur 1 and only 1 time in <semanticdensity> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
		$value = $semanticdensity->appendChild($dom->createElement('value', $semanticDensityDefault));
		$value->setAttribute('xml:lang', $languageDefault);
		// Name : <intendedenduserrole> Element in <educational>.
		// Description : Normal user of the learning object, most dominant first.
		// Multiplicity : The <intendedenduserrole> element occurs 0 or more times within the <educational> element.
		// Attributes Contained in <intendedenduserrole>:
		// none.
		// Elements Contained in <intendedenduserrole>:
		// <source>.
		// <value>.
		$intendedenduserrole = $educational->appendChild($dom->createElement('intendedenduserrole'));
		// Name : <source> Element in <intendedenduserrole>.
		// Description : Source of intendedenduserrole.
		// Multiplicity : The <source> element occur 1 and only 1 time in <intendedenduserrole> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
		$source = $intendedenduserrole->appendChild($dom->createElement('source', $definedVocab));
		$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <intendedenduserrole>.
		// Description : LOM Defined Vocabularies for intendedenduserrole.
		// Multiplicity : The <value> element occur 1 and only 1 time in <intendedenduserrole> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
		$value = $intendedenduserrole->appendChild($dom->createElement('value', $intendedEndUserRoleDefault));
		$value->setAttribute('xml:lang', $languageDefault);
		// Name : <context> Element in <educational>.
		// Description : The typical learning environment where use of learning object is intended to take place.
		// Multiplicity : The <context> element occurs 0 or more times within the <educational> element.
		// Attributes Contained in <context>:
		// none.
		// Elements Contained in <context>:
		// <source>.
		// <value>.
		$context = $educational->appendChild($dom->createElement('context'));
		// Name : <source> Element in <context>.
		// Description : Source of context.
		// Multiplicity : The <source> element occur 1 and only 1 time in <context> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
		$source = $context->appendChild($dom->createElement('source', $definedVocab));
		$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <context>.
		// Description : LOM Defined Vocabularies for context.
		// Multiplicity : The <value> element occur 1 and only 1 time in <context> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
		$value = $context->appendChild($dom->createElement('value', $contextDefault));
		$value->setAttribute('xml:lang', $languageDefault);
		// Name : <typicalagerange> Element in <educational>.
		// Description : Age of the typical intended user.
		// Multiplicity : The <typicalagerange> element occurs 0 or more times within the <educational> element.
		// Attributes Contained in <typicalagerange>:
		// none.
		// Elements Contained in <typicalagerange>:
		// <langstring>.
		$typicalagerange = $educational->appendChild($dom->createElement('typicalagerange'));
		// Name : <langstring> Element in <title>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
		$langstring = $typicalagerange->appendChild($dom->createElement('langstring'));
		$langstring->setAttribute('xml:lang', $languageDefault);
		// Name : <difficulty> Element in <educational>.
		// Description : How hard it is to work through the learning object for the typical target audience.
		// Multiplicity : The <difficulty> element occurs 0 or 1 time within the <educational> element.
		// Attributes Contained in <difficulty>:
		// none.
		// Elements Contained in <difficulty>:
		// <source>.
		// <value>.
		$difficulty = $educational->appendChild($dom->createElement('difficulty'));
		// Name : <source> Element in <difficulty>.
		// Description : Source of difficulty.
		// Multiplicity : The <source> element occur 1 and only 1 time in <difficulty> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
		$source = $difficulty->appendChild($dom->createElement('source', $definedVocab));
		$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <difficulty>.
		// Description : LOM Defined Vocabularies for difficulty.
		// Multiplicity : The <value> element occur 1 and only 1 time in <difficulty> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
		$value = $difficulty->appendChild($dom->createElement('value', $difficultyDefault));
		$value->setAttribute('xml:lang', $languageDefault);
		// Name : <typicallearningtime> Element in <educational>.
		// Description : Approximate or typical time it takes to work with the resource.
		// Multiplicity : The <typicallearningtime> element occurs 0 or 1 time within the <educational> element.
		// Attributes Contained in <typicallearningtime>:
		// none.
		// Elements Contained in <typicallearningtime>:
		// <datetime>.
		// <description>.
		$typicallearningtime = $educational->appendChild($dom->createElement('typicallearningtime'));
		$date = $typicallearningtime->appendChild($dom->createElement('date'));
		$datetime = $date->appendChild($dom->createElement('datetime'));
		// Name : <description> Element in <educational>.
		// Description : Comments on how the learning object is to be used.
		// Multiplicity : The <description> element occurs 0 or 1 time within the <educational> element.
		// Attributes Contained in <description>:
		// none.
		// Elements Contained in <description>:
		// <langstring>.
		$description = $educational->appendChild($dom->createElement('description'));
		// Name : <langstring> Element in <description>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
		$langstring = $description->appendChild($dom->createElement('langstring'));
		$langstring->setAttribute('xml:lang', $languageDefault);
		// Name : <language> Element in <educational>.
		// Description : User's natural language.
		// Multiplicity : The <language> element occurs 0 or more times within the <educational> element.
		// Attributes Contained in <language>:
		// none.
		// Elements Contained in <language>:
		// none.
		$language = $educational->appendChild($dom->createElement('language'));
		}
		if($support['rights'])
		{
			$rightsSupport = $this->lomRights();
		// SECTION 6
		// Name : <rights> Element in <lom>.
		// Description : Conditions of use of the resource.
		// Multiplicity : The <rights> element occurs 0 or 1 time within the top-level <educational> element.
		// Attributes Contained in <rights>:
		// none
		// Elements Contained in <rights>:
		// <cost>.
		// <copyrightandotherrestrictions>.
		// <description>.
			$rights = $lom->appendChild($dom->createElement('rights'));
			if($rightsSupport['cost'])
			{
		// Name : <cost> Element in <rights>.
		// Description : Whether use of the resource requires payment.
		// Multiplicity : The <cost> element occurs 0 or 1 time within the <rights> element.
		// Attributes Contained in <cost>:
		// none
		// Elements Contained in <cost>:
		// <source>.
		// <value>.
				$cost = $rights->appendChild($dom->createElement('cost'));
		// Name : <source> Element in <cost>.
		// Description : Source of cost.
		// Multiplicity : The <source> element occur 1 and only 1 time in <cost> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
				$source = $cost->appendChild($dom->createElement('source', $definedVocab));
				$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <cost>.
		// Description : LOM Defined Vocabularies for cost.
		// Multiplicity : The <value> element occur 1 and only 1 time in <cost> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
				$value = $cost->appendChild($dom->createElement('value', $costDefault));
				$value->setAttribute('xml:lang', $languageDefault);
			}
			if($rightsSupport['copyrightandotherrestrictions'])
			{
		// Name : <copyrightandotherrestrictions> Element in <rights>.
		// Description : Whether copyright or other restrictions apply.
		// Multiplicity : The <copyrightandotherrestrictions> element occurs 0 or 1 time within the <rights> element.
		// Attributes Contained in <copyrightandotherrestrictions>:
		// none
		// Elements Contained in <copyrightandotherrestrictions>:
		// <source>.
		// <value>.
				$copyrightandotherrestrictions = $rights->appendChild($dom->createElement('copyrightandotherrestrictions'));
		// Name : <source> Element in <copyrightandotherrestrictions>.
		// Description : Source of copyrightandotherrestrictions.
		// Multiplicity : The <source> element occur 1 and only 1 time in <copyrightandotherrestrictions> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
				$source = $copyrightandotherrestrictions->appendChild($dom->createElement('source', $definedVocab));
				$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <copyrightandotherrestrictions>.
		// Description : LOM Defined Vocabularies for copyrightandotherrestrictions.
		// Multiplicity : The <value> element occur 1 and only 1 time in <copyrightandotherrestrictions> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
				$value = $copyrightandotherrestrictions->appendChild($dom->createElement('value', $copyrighAndOtherRestrictionsDefault));
				$value->setAttribute('xml:lang', $languageDefault);
			}
			if($rightsSupport['description'])
			{
		// Name : <description> Element in <rights>.
		// Description : Comments on the conditions of use of the resource.
		// Multiplicity : The <description> element occurs 0 or 1 time within the <rights> element.
		// Attributes Contained in <description>:
		// none.
		// Elements Contained in <description>:
		// <langstring>.
				$description = $rights->appendChild($dom->createElement('description'));
		// Name : <langstring> Element in <description>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// none.
		// Elements Contained in <langstring>:
		// none.
				$langstring = $description->appendChild($dom->createElement('langstring'));
			}
		}
		if($support['relation'])
		{
		// SECTION 7
		// Name : <relation> Element in <lom>.
		// Description : Features of the resource in relationship to other learning objects.
		// Multiplicity : The <relation> element occurs 0 or more times within the top-level <lom> element.
		// Attributes Contained in <rights>:
		// none
		// Elements Contained in <rights>:
		// <kind>.
		// <resource>.
		$relation = $lom->appendChild($dom->createElement('relation'));
		// Name : <kind> Element in <relation>.
		// Description :  Nature of the relationship between the resource being described and the one identified.
		// Multiplicity : The <kind> element occurs 0 or 1 time within the <resource> element.
		// Attributes Contained in <rights>:
		// none
		// Elements Contained in <rights>:
		// <source>.
		// <value>.
		$kind = $relation->appendChild($dom->createElement('kind'));
		// Name : <source> Element in <kind>.
		// Description : Source of kind.
		// Multiplicity : The <source> element occur 1 and only 1 time in <kind> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
		$source = $kind->appendChild($dom->createElement('source', $definedVocab));
		$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <kind>.
		// Description : LOM Defined Vocabularies for kind.
		// Multiplicity : The <value> element occur 1 and only 1 time in <kind> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
		$value = $kind->appendChild($dom->createElement('value', $kindDefault));
		$value->setAttribute('xml:lang', $languageDefault);
		// Name : <resource> Element in <relation>.
		// Description : The target learning object that this relationship references.
		// Multiplicity : The <resource> element occurs 0 or 1 time within the <relation> element.
		// Attributes Contained in <resource>:
		// none
		// Elements Contained in <resource>:
		// <identifier>.
		// <description>.
		$resourceRelation = $relation->appendChild($dom->createElement('resource'));
		// Name : <identifier> Element in <relation>.
		// Description :  Unique identifier of the other resource.
		// Multiplicity : The <identifier> element occurs 0 or 1 time within the <resource> element.
		// Attributes Contained in <resource>:
		// none
		// Elements Contained in <resource>:
		// none
		$identifier = $resourceRelation->appendChild($dom->createElement('identifier'));
		// Name : <description> Element in <educational>.
		// Description : Description of the other resource.
		// Multiplicity : The <description> element occurs 0 or 1 time within the <resource> element.
		// Attributes Contained in <description>:
		// none.
		// Elements Contained in <description>:
		// <langstring>.
		$description = $resourceRelation->appendChild($dom->createElement('description'));
		// Name : <langstring> Element in <description>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// none.
		// Elements Contained in <langstring>:
		// none.
		$langstring = $description->appendChild($dom->createElement('langstring'));
		// Name : <catalogentry> Element in <resource>.
		// Description : Reference to the other resource.
		// Multiplicity : The <catalogentry> element occurs 0 or more times within the <resource> element.
		// Attributes Contained in <catalogentry>:
		// none.
		// Elements Contained in <catalogentry>:
		// <catalog>
		// <entry>
		$catalogentry = $resourceRelation->appendChild($dom->createElement('catalogentry'));
		// Name : <catalog> Element in <catalogentry>.
		// Description : Source of the following string value.
		// Multiplicity : The <catalog> element occurs 1 and only 1 time within the <catalogentry> element.
		// Attributes Contained in <catalog>:
		// none.
		// Elements Contained in <catalog>:
		// none.
		$catalog = $catalogentry->appendChild($dom->createElement('catalog'));
		// Name : <entry> Element in <catalogentry>.
		// Description : Actual string value of the entry within the catalog.
		// Multiplicity : The <entry> element occurs 1 and only 1 time with the <catalogentry> element. If the <catalogentry> element is used.
		// Attributes Contained in <entry>:
		// none.
		// Elements Contained in <entry>:
		// <langstring>.
		$entry = $catalogentry->appendChild($dom->createElement('entry'));
		// Name : <langstring> Element in <entry>.
		// Description : Entry language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
		$langstring = $entry->appendChild($dom->createElement('langstring'));
		$langstring->setAttribute('xml:lang', $languageDefault);
		}
		if($support['annotation'])
		{
		// SECTION 8
		// Name : <annotation> Element in <lom>.
		// Description : Comments on the educational use of the learning object.
		// Multiplicity : The <annotation> element occurs 0 or more times within the top-level <lom> element
		// Attributes Contained in <rights>:
		// none
		// Elements Contained in <rights>:
		// <person>.
		// <date>.
		// <description>.
		$annotation = $lom->appendChild($dom->createElement('annotation'));
		// Name : <person> Element in <annotation>.
		// Description : Comments on the educational use of the learning object.
		// Multiplicity : The <person> element occurs 0 or 1 time within the <annotation> element.
		// Attributes Contained in <entry>:
		// none.
		// Elements Contained in <entry>:
		// <vcard>
		$person = $annotation->appendChild($dom->createElement('person'));
		$vcard = $person->appendChild($dom->createElement('vcard'));
		// Name : <date> Element in <annotation>.
		// Description : Date that this annotation was created.
		// Multiplicity : The <date> element occurs 0 or 1 time within the <annotation> element.
		// Attributes Contained in <entry>:
		// none.
		// Elements Contained in <entry>:
		// <datetime>.
		// <description>.
		$date = $annotation->appendChild($dom->createElement('date'));
		$datetime = $date->appendChild($dom->createElement('datetime'));
		$description = $date->appendChild($dom->createElement('description'));
		// Name : <description> Element in <annotation>.
		// Description : The content of the annotation.
		// Multiplicity : The <description> element occurs 0 or 1 time within the <annotation> element.
		// Attributes Contained in <entry>:
		// none.
		// Elements Contained in <entry>:
		// <langstring>.
		$description = $annotation->appendChild($dom->createElement('description'));
		// Name : <langstring> Element in <title>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
		$langstring = $description->appendChild($dom->createElement('langstring'));
		$langstring->setAttribute('xml:lang', $languageDefault);
		}
		if($support['classification'])
		{
		// SECTION 9
		// Name : <classification> Element in <lom>.
		// Description : Description of a characteristic of the resource by entries in classifications.
		// Multiplicity : The <classification> element occurs 0 or more times within the top-level <lom> element.
		// Attributes Contained in <rights>:
		// none
		// Elements Contained in <rights>:
		// <purpose>.
		// <taxonpath>.
		// <description>.
		// <keyword>.
		$classification = $lom->appendChild($dom->createElement('classification'));
		// Name : <purpose> Element in <classification>.
		// Description : Characteristics of the resource described by this classification entry.
		// Multiplicity : The <purpose> element occurs 0 or 1 time within the <classification> element.
		// Attributes Contained in <rights>:
		// none
		// Elements Contained in <rights>:
		// <source>.
		// <value>.
		$purpose = $classification->appendChild($dom->createElement('purpose'));
		// Name : <source> Element in <purpose>.
		// Description : Source of structure.
		// Multiplicity : The <source> element occur 1 and only 1 time in <purpose> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// none.
		$source = $purpose->appendChild($dom->createElement('source', $definedVocab));
		$source->setAttribute('xml:lang', $languageDefault);
		// Name : <value> Element in <purpose>.
		// Description : LOM Defined Vocabularies for purpose.
		// Multiplicity : The <value> element occur 1 and only 1 time in <purpose> element.
		// Attributes Contained in <value>:
		// xml:lang.
		// Elements Contained in <value>:
		// none.
		$value = $purpose->appendChild($dom->createElement('value', $purposeDefault));
		$value->setAttribute('xml:lang', $languageDefault);
		// Name : <taxonpath> Element in <classification>.
		// Description : A taxonomic path in a specific classification.
		// Multiplicity : The <taxonpath> element occurs 0 or more times within the <classification> element. Maximum permitted instances is 15.
		// Attributes Contained in <taxonpath>:
		// none
		// Elements Contained in <taxonpath>:
		// <source>.
		// <taxon>.
		$taxonpath = $classification->appendChild($dom->createElement('taxonpath'));
		// Name : <source> Element in <taxonpath>.
		// Description : A specific classification. Any recognized "official" taxonomy.
		// Multiplicity : The <source> element occurs 0 or 1 time within the <taxonpath> element.
		// Attributes Contained in <source>:
		// xml:lang.
		// Elements Contained in <source>:
		// <langstring>.
		$source = $taxonpath->appendChild($dom->createElement('source'));
		// Name : <langstring> Element in <source>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
		$langstring = $source->appendChild($dom->createElement('langstring'));
		$langstring->setAttribute('xml:lang', $languageDefault);
		// Name : <taxon> Element in <classification>.
		// Description : An entry in a classification.
		// Multiplicity : The <taxon> element occurs 0 or 1 time within the <taxonpath> element. If the <taxonpath> element is used, the <taxon> element must occur 1 and only 1 time with the <taxonpath> element. Maximum permitted instances is 15.
		// Attributes Contained in <rights>:
		// none
		// Elements Contained in <rights>:
		// <id>.
		// <entry>.
		// <taxon>.
		$taxon = $taxonpath->appendChild($dom->createElement('taxon'));
		// Name : <id> Element in <taxon>.
		// Description : A specific classification. Any recognized "official" taxonomy.
		// Multiplicity : The <id> element occurs 0 or 1 time within the <taxon> element.
		// Attributes Contained in <id>:
		// none.
		// Elements Contained in <id>:
		// none.
		$id = $taxon->appendChild($dom->createElement('id'));
		// Name : <id> Element in <taxon>.
		// Description : A specific classification. Any recognized "official" taxonomy.
		// Multiplicity : The <id> element occurs 0 or 1 time within the <taxon> element.
		// Attributes Contained in <id>:
		// none.
		// Elements Contained in <id>:
		// <langstring>.
		$entry = $taxon->appendChild($dom->createElement('entry'));
		// Name : <langstring> Element in <entry>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
		$langstring = $entry->appendChild($dom->createElement('langstring'));
		$langstring->setAttribute('xml:lang', $languageDefault);
		// Name : <description> Element in <classification>.
		// Description : A textual description of the learning object relative to its stated purpose.
		// Multiplicity : The <description> element occurs 0 or 1 time within the <classification> element.
		// Attributes Contained in <description>:
		// none
		// Elements Contained in <description>:
		// <langstring>.
		$description = $classification->appendChild($dom->createElement('description'));
		// Name : <langstring> Element in <description>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
		$langstring = $description->appendChild($dom->createElement('langstring'));
		$langstring->setAttribute('xml:lang', $languageDefault);
		// Name : <keyword> Element in <classification>.
		// Description : A collection of keywords or phrases describing the learning objective relative to its stated purpose.
		// Multiplicity : The <keyword> element occurs 0 or more times within the <classification> element.
		// Attributes Contained in <keyword>:
		// none
		// Elements Contained in <keyword>:
		// <langstring>.
		$keyword = $classification->appendChild($dom->createElement('keyword'));
		// Name : <langstring> Element in <keyword>.
		// Description : Title language.
		// Multiplicity : The <langstring> element can be repeated 1 or more times.
		// Attributes Contained in <langstring>:
		// xml:lang.
		// Elements Contained in <langstring>:
		// none.
		$langstring = $description->appendChild($dom->createElement('langstring'));
		$langstring->setAttribute('xml:lang', $languageDefault);
		}
		// eduCommons Structure.
		// Some Notes:
		// eduCommons specific fields not represented in standard metadata sets. Used to set specific eduCommons options on content objects.
		// eduCommons Defined Vocabularies for <objectType>:
		// Course.
		// Document.
		// File.
		// Image.
		// Link.
		$objectType = $eduCommons['objectType'];
		// eduCommons Defined Vocabularies for <copyright>:
		// The field should specify both the copyright and the date. e. g. "Copyright 2006". This field is optional. If it is not included, the site default copyright string will be used instead.
		$copyright = $eduCommons['copyright'];
		// eduCommons Defined Vocabularies for <licenseName>:
		// This name will be used in the copyright byline, and should slot into the following sentence: This resource is licensed under a ____________. This is an optional field only if "(site default)" is chosen. Otherwise it must be specified.
		$licenseName = $eduCommons['licenseName'];
		// eduCommons Defined Vocabularies for <licenseUrl>:
		// It allows the license name in the copyright byline to be linked directly to the definition. It is optional.
		$licenseUrl = $eduCommons['licenseUrl'];
		// eduCommons Defined Vocabularies for <licenseIconUrl>:
		// This field is optional, and likely not to be included, unless the license includes a representative icon. An example where you would want to include this field would be to specify a creative commons icon along with the license.
		$licenseIconUrl = $eduCommons['licenseIconUrl'];
		// eduCommons Defined Vocabularies for <clearedCopyright>:
		// Should either be set to true or false, and is set to "false" by default.
		$clearedCopyright = $eduCommons['clearedCopyright'];
		// eduCommons Defined Vocabularies for <courseId>:
		// Although this tag not required, is highly recommended.
		$courseId = $eduCommons['courseId'];
		// eduCommons Defined Vocabularies for <term>:
		// Although this tag is not required, it is highly recommended. e. g. "Summer 2006"
		$term = $eduCommons['term'];
		// eduCommons Defined Vocabularies for <displayInstructorEmail>:
		// It can be set to "true" or "false". This tag is optional and defaults to "false".
		$displayInstructorEmail = $eduCommons['displayInstructorEmail'];
		$name = $eduCommons['name'];
		$eduCommons = $metadata->appendChild($dom->createElement('eduCommons'));
		$eduCommons->setAttribute('xmlns', 'http://cosl.usu.edu/xsd/eduCommonsv1.1');
		// Name : <objectType> Element in <eduCommons>.
		// Description : A list of possible types that a content object can have. eduCommons uses this information to create the correct type of object in an eduCommons environment.
		// Attributes Contained in <objectType>:
		// none
		// Elements Contained in <objectType>:
		// none
		if($objectType)
			$objectType = $eduCommons->appendChild($dom->createElement('objectType', $objectType));
		// Name : <copyright> Element in <eduCommons>.
		// Description : The copyright tag is used by eduCommons to license content objects. It also uses the field to render copyright bylines for objects.
		// Attributes Contained in <copyright>:
		// none
		// Elements Contained in <copyright>:
		// none
		if($copyright)
		$copyright = $eduCommons->appendChild($dom->createElement('copyright', $copyright));
		if($license)
		{
		// Name : <license> Element in <eduCommons>.
		// Description : The license tag is used to assign a copyright license to a content object. It is also used to render a copyright byline for an object.
		// Attributes Contained in <license>:
		// none
		// Elements Contained in <license>:
		// <licenseName>
		// <licenseUrl>
		// <licenseIconUrl>
			$license = $eduCommons->appendChild($dom->createElement('license'));
		// Name : <licenseName> Element in <license>.
		// Description : The licenseName tag is used to identify the name of the license.
		// Attributes Contained in <licenseName>:
		// none
		// Elements Contained in <licenseName>:
		// none
			if($licenseName)
				$licenseName = $license->appendChild($dom->createElement('licenseName', $licenseName));
		// Name : <licenseUrl> Element in <license>.
		// Description : The licenseUrl tag is used to specify a public web site where the legal definition of the license is displayed.
		// Attributes Contained in <licenseUrl>:
		// none
		// Elements Contained in <licenseUrl>:
		// none
			if($licenseUrl)
				$licenseUrl = $license->appendChild($dom->createElement('licenseUrl', $licenseUrl));
		// Name : <licenseIconUrl> Element in <license>.
		// Description : The licenseIconUrl tag is used to specify a public icon image that represents the content license.
		// Attributes Contained in <licenseIconUrl>:
		// none
		// Elements Contained in <licenseIconUrl>:
		// none
			if($licenseIconUrl)
				$licenseIconUrl = $license->appendChild($dom->createElement('licenseIconUrl', $licenseIconUrl));
		}
		// Name : <clearedCopyright> Element in <eduCommons>.
		// Description : The clearedCoypright field is used by eduCommons to keep track of whether or not a content object has been cleared for publication in an open content environment.
		// Attributes Contained in <clearedCopyright>:
		// none
		// Elements Contained in <clearedCopyright>:
		// none
		if($clearedCopyright)
		$clearedCopyright = $eduCommons->appendChild($dom->createElement('clearedCopyright', $clearedCopyright));
		// Name : <courseId> Element in <eduCommons>.
		// Description : The courseId tag is used to identify the course catalog number at an institution. It is used by eduComomns to render a full title of a course.
		// Attributes Contained in <courseId>:
		// none
		// Elements Contained in <courseId>:
		// none
		if($courseId)
		$courseId = $eduCommons->appendChild($dom->createElement('courseId', $courseId));
		// Name : <term> Element in <eduCommons>.
		// Description : The term tag is used by eduCommons to specify which term, or semester the course was taught in. It is used by eduCommons to render a full title of a course.
		// Attributes Contained in <term>:
		// none
		// Elements Contained in <term>:
		// none
		if($term)
		$term = $eduCommons->appendChild($dom->createElement('term', $term));
		// Name : <displayInstructorEmail> Element in <eduCommons>.
		// Description : The displayInstructorEmail tag is used to specify whether or not an Instructor's Email address should be published or not.
		// Attributes Contained in <displayInstructorEmail>:
		// none
		// Elements Contained in <displayInstructorEmail>:
		// none
		if($displayInstructorEmail)
		$displayInstructorEmail = $eduCommons->appendChild($dom->createElement('displayInstructorEmail', $displayInstructorEmail));
		// Name : <file> Element in <resource>.
		// Description : Used by eduCommons to name the content object and find it within the package
		// Attributes Contained in <file>:
		// none
		// Elements Contained in <file>:
		// none
		if($name)
		{
			$file = $resource->appendChild($dom->createElement('file', $file));
			$file->setAttribute('href', $name);
		}



		return $resource;
	}

	// The following functions are merely truth tables holding keys to which nodes are supported.
	function lomSupported()
	{
		// eduCommons supported lom fields
		$support = array('general' => true,
				'lifecycle' => true ,
				'metametadata' => true,
				'technical' => true,
				'rights' => true
				);

		return $support;
	}

	function lomGeneral()
	{
		// eduCommons supported lom fields
		$generalSupport = array('identifier' => true,
				'title' => true ,
				'language' => true,
				'description' => true,
				'keyword' => true
				);

		return $generalSupport;
	}

	function lomLifecycle()
	{
		// eduCommons supported lom fields
		$lifecycleSupport = array('contribute' => true
				);

		return $lifecycleSupport;
	}

	function lomMetametadata()
	{
		// eduCommons supported lom fields
		$metametadataSupport = array('catalogentry' => true,
				'contribute' => true ,
				'metadatascheme' => true,
				'language' => true
				);

		return $metametadataSupport;
	}

	function lomTechnical()
	{
		// eduCommons supported lom fields
		$technicalSupport = array('format' => true,
				'size' => true ,
				'location' => true
				);

		return $technicalSupport;
	}

	function lomRights()
	{
		// eduCommons supported lom fields
		$rightsSupport = array('copyrightandotherrestrictions' => true,
				'description' => true
				);

		return $rightsSupport;
	}

	function getManifest()
	{
		//Start of xml file
		$imsmanifest = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		//manifest
		$imsmanifest .= "<manifest ";
		$imsmanifest .= "identifier=\"MAN".$this->objIEUtils->generateUniqueId()."\" ";
		$imsmanifest .= "xmlns=\"http://www.imsglobal.org/xsd/imscp_v1p1\" xmlns:eduCommons=\"http://cosl.usu.edu/xsd/eduCommonsv1.1\" xmlns:imsmd=\"http://www.imsglobal.org/xsd/imsmd_v1p2\" xmlns:version=\"".date('Y-m-j G:i:s')."\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p2.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p4.xsd http://cosl.usu.edu/xsd/eduCommonsv1.1 eduCommonsv1.1.xsd\">\n";	

		return $imsmanifest;
	}

	function getMetadata()
	{
		//metadata
		$metadata = "<metadata>\n";
		$metadata .= "<schema>\n";
		$metadata .= "IMS CONTENT";
		$metadata .= "</schema>\n";
		$metadata .= "<schemaversion>\n";
		$metadata .= "1.2\n";
		$metadata .= "</schemaversion>\n";
		$metadata .= "</metadata>\n";

		return $metadata;
	}
// Structure
	public $orgsId;
	public $itemIds;
	public $resIds;

	function getIMS($contextcode)
	{
		$imsmanifest = $this->getManifest();
		$imsmanifest .= $this->getMetadata();
		$this->orgsId = 'ORG'.$this->objIEUtils->generateUniqueId();
		$imsmanifest .= "<organizations ";
		$imsmanifest .= "default= \"".$this->orgsId."\">\n";
		$chapterOrders = $this->objIEUtils->chapterOrder($contextcode);
		foreach($chapterOrders as $chapter)
		{
//var_dump($chapterOrders);die;
			$imsmanifest .= "<organization ";
			$imsmanifest .= "default= \"".$this->orgsId."\">\n";
			$chapterContent = $this->objIEUtils->chapterContent($chapter['chapterid']);
			$pageOrders = $this->objIEUtils->pageOrder($contextcode);
//var_dump($pageOrders);die;
			foreach($pageOrders as $pageOrder)
			{
				$pageContents = $this->objIEUtils->pageContent($pageOrder['titleid']);
				$i = 0;
//var_dump($pageContents);die;
				foreach($pageContents as $pageContent)
				{	
//var_dump($pageContent);die;
					$imsmanifest .= $this->getItem($pageContent['menutitle'],$i);
					$i++;
				}
			}

			foreach($chapterContent as $content)
			{
				
			}
			$imsmanifest .= "</organization>\n";
		}
		$imsmanifest .= "</organizations>\n";

//var_dump($chapterOrder);die;
		//$chapterContent = $this->objIEUtils->chapterContent();
		//$pageOrder = $this->objIEUtils->pageOrder($contextcode);
//var_dump($pageOrder);die;
		//$pageContent = $this->objIEUtils->pageContent();
		$imsmanifest .= "</manifest>";

		return $imsmanifest;
	}

	function getOrganizations($menutitles)
	{
		//organizations
		$this->orgsId = 'ORG'.$this->objIEUtils->generateUniqueId();
		//organization
		$organizations .= "<organizations ";
		$organizations .= "default= \"".$this->orgsId."\">\n";
		$organizations .= $this->getOrganization($menutitles);
		$organizations .= "</organizations>\n";

		return $organizations;
	}
// Chapters
	function getOrganization($menutitles)
	{
		//organization
		$organization .= "<organization ";
		$organization .= "default= \"".$this->orgsId."\">\n";
		$i = 0;
		foreach($menutitles as $menutitle)
		{
			$organization .= $this->getItem($menutitle, $i);
			$i++;
		}
		$organization .= "</organization>\n";

		return $organization;
	}
// Pages
	function getItem($menutitle, $i)
	{
		$this->itemIds[$i] = 'ITM'.$this->objIEUtils->generateUniqueId();
		$this->resIds[$i] = 'RES'.$this->objIEUtils->generateUniqueId();
		$this->menutitles[$i] = $menutitle;
		$item = "<item ";
		$item .= "identifier=\"".$itemId."\"";
		$item .= "identifierref=\"".$resId."\"";
		$item .= "isVisible=\"".'true'."\">\n";
		$item .= "<title>";
		$item .= $menutitle."\n";
		$item .= "</title>";
		$item .= "</item>\n";

		return $item;
	}
//Resources
	function getResources($contextcode)
	{
		$resources = "<resources>\n";
		foreach($this->menutitles as $menutitle)
		{
			$resources .= $this->getResource($contextcode);
		}
		$resources .= "</resources>\n";

		return $resources;
	}

	function getResource($contextcode)
	{
		$resource = "<resource>\n";
		$resource .= "<file ";
		$resource .= "href=\"".$contextcode.'/'.$filename."\">\n";
		$resource .= "</resource>\n";

		return $resource;
	}

	function getCourseResource($contextcode)
	{
		$resource = "<resource>\n";
		$resource .= "<metadata>\n";

		$resource .= "</metadata>\n";
		$resource .= "<file>\n";

		$resource .= "href=\"".$contextcode.'/'.$filename."\">\n";

		$resource .= "</resource>\n";

		return $resource;
	}

	function getImageResource($contextcode, $filename)
	{
		$resource = "<resource>\n";
		$resource .= "<metadata>\n";
		$resource .= "<lom>\n";
		$resource .= "<general>\n";
		$resource .= "<identifier>\n";
		$resource .= $filename;
		$resource .= "</identifier>\n";
		$resource .= "</general>\n";
		$resource .= "</lom>\n";
		$resource .= "</metadata>\n";
		$resource .= "<file\n";
		$resource .= "href=\"".$contextcode.'/'.$filename."\">\n";
		$resource .= "</resource>\n";

		return $resource;
	}

//===========================================================================
//eduCommons IMS Skeleton
function moodle($courseData, $filelist, $tempDirectory)
{
	//Retrieve all directories
	$dirlist = $this->objIEUtils->list_dir($tempDirectory,0);
	//start of xml document
	$imsmanifest = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
//manifest attributes example
/*
xmlns="http://www.imsglobal.org/xsd/imscp_v1p1" 
xmlns:eduCommons="http://cosl.usu.edu/xsd/eduCommonsv1.1" 
xmlns:imsmd="http://www.imsglobal.org/xsd/imsmd_v1p2" 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p2.xsd 
                    http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p4.xsd 
                    http://cosl.usu.edu/xsd/eduCommonsv1.1 eduCommonsv1.1.xsd">

*/

//manifest
	$imsmanifest .= "<manifest "; 
	$imsmanifest .= "identifier =\"".$courseData['id']."\" ";
	$imsmanifest .= "version =\"".$courseData['contextcode']."\" ";
	$imsmanifest .= "xmlns=\"http://www.imsglobal.org/xsd/imscp_v1p1\" xmlns:eduCommons=\"http://cosl.usu.edu/xsd/eduCommonsv1.1\" xmlns:imsmd=\"http://www.imsglobal.org/xsd/imsmd_v1p2\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p2.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p4.xsd http://cosl.usu.edu/xsd/eduCommonsv1.1 eduCommonsv1.1.xsd\">\n";
//===========================================================================
//metadata
	$imsmanifest .= "<metadata>\n";
	$imsmanifest .= "<schema>\n";
	$imsmanifest .= "IMS CONTENT";
	$imsmanifest .= "</schema>\n";
	$imsmanifest .= "<schemaversion>\n";
	$imsmanifest .= "1.2\n";
	$imsmanifest .= "</schemaversion>\n";
	$imsmanifest .= "</metadata>\n";
//===========================================================================
//organization
	$imsmanifest .= "<organizations ";
	$org_identifier = $this->genORG();
	$imsmanifest .= "default= \"".$org_identifier."\">\n";
//organization
	$imsmanifest .= "<organization ";
	$imsmanifest .= "identifier=\"".$org_identifier."\">\n";
	$idenrefs = array();
	foreach($filelist as $file)
	{
		static $i=0;
		$idenrefref = $this->genRES();
		$idenrefs[$i] = $idenrefref;
		$i++; 
		$imsmanifest .= $this->createOrg($org_identifier,$this->genITM(),$idenrefref,"TRUE",$file);
	}
//var_dump($idenrefs);
	$imsmanifest .= "</organization>";
	$imsmanifest .= "</organizations>\n";
//=========================================================================== 
//resources
	$imsmanifest .= "<resources>\n";
//resource
	$resValues = array();
	for($i=0; $i<count($idenrefs); $i++)
	{
//$imsmanifest .= $this->createRes($idenrefs[$i],"webcontent","1");
		$resValues["identifier"] = $idenrefs[$i];
		$resValues["type"] = "webcontent";
		$resValues["href"] = "1";
		$imsmanifest .= $this->createRes($resValues);
	}
	$imsmanifest .= "</resources>\n";
//===========================================================================
	$imsmanifest .= "</manifest>\n";

//eduCommons object
/*
<eduCommons xmlns="http://cosl.usu.edu/xsd/eduCommonsv1.1">
    <objectType>
        Course
    </objectType>
</eduCommons> 
*/

//the rights holder field
/*
<contribute>
    <role>
        <source>
            <langstring xml:lang="en">
                eduCommonsv1.1
            </langstring>
        </source>
        <value>
            <langstring xml:lang="en">
                rights holder
            </langstring>
        </value>
    </role>
    <centity>
        <vcard>
            BEGIN:VCARD
            FN: John Smith
            END:VCARD
        </vcard>
    </centity>
    <date>
        <datetime>
            2006-08-07 15:59:23
        </datetime>
    </date>
</contribute>
*/

//
/*

*/
return $imsmanifest;
}
//===========================================================================
//create organization
//organization example
/*
<organizations default="ORG1234">
    <organization identifier="ORG1234">
        <item identifier="ITM1234" identifierref="RES1234" isVisible="true">
            <title>
                Hello World
            </title>
        </item>
        ...
    </organization>
</organizations>
<resources>
    <resource identifier="RES1234">
       ...
    </resource>
    ...
</resources>
*/

function createOrg($org_identifier, $item_identifier, $item_identifierref, $item_isVisible,$file)
{
	$org .= "<item ";
	$org .= "identifier=\"".$item_identifier."\" ";
	$org .= "identifierref=\"".$item_identifierref."\" ";
	$org .= "isVisible=\"".$item_isVisible."\">\n";
	$org .= "<title>";
	$org .= $file."\n";
	$org .= "</title>";
	$org .= "</item>\n";

	return $org;
}
//===========================================================================
//function createRes($res_identifier,$res_type,$res_href)
function createRes($resValues)
{
	$res = "<resource ";
	$res .= "identifier=\"".$resValues["identifier"]."\" ";
	$res .= "type=\"".$resValues["type"]."\" ";
	$res .= "href=\"".$resValues["href"]."\">\n";
	$res .= "<metadata>\n";
	$res .= "<lom>\n";
	$res .= "<general>\n";
	$res .= "<identifier>";
	$res .= $courseData['title'];
	$res .= "</identifier>\n";
	$res .= "<title>\n";
	$res .= "<langstring>";
	$res .= $courseData['title'];
	$res .= "</langstring>\n";
	$res .= "</title>\n"; 
	$res .= "<language>";
	$res .= "</language>\n";
	$res .= "<description>\n";
	$res .= "<langstring>";
	$res .= "</langstring>\n";
	$res .= "</description>\n";
	$res .= "<keyword>";
	$res .= "</keyword>\n";
	$res .= "</general>\n";
	$res .= "<lifecycle>\n";
	$res .= "<contribute>\n";
	$res .= "</contribute>\n";
	$res .= "</lifecycle>\n";
	$res .= "<metametadata>\n";
	$res .= "<catalogentry>\n";
	$res .= "<catalog>\n";
	$res .= "</catalog>\n";
	$res .= "<entry>\n";
	$res .= "<langstring>";
	$res .= $courseData['title'];
	$res .= "</langstring>\n";
	$res .= "</entry>\n";
	$res .= "</catalogentry>\n";
	$res .= "<metadataschema>";
	$res .= "LOMv1.0";
	$res .= "</metadataschema>\n";
	$res .= "<language>";
	$res .= "en";
	$res .= "</language>\n";
	$res .= "</metametadata>\n";
	$res .= "<technical>\n";
	$res .= "<format>";
	$res .= "text/html";
	$res .= "</format>\n";
	$res .= "<size>";
	$res .= "</size>\n";
	$res .= "<location>";
	$res .= "http://localhost:8080/".$courseData['contextcode'];
	$res .= "</location>\n";
	$res .= "</technical>\n";
	$res .= "<rights>";
	$res .= "<copyrightandotherrestrictions>";
	$res .= "<source>";
	$res .= "<langstring>";
	$res .= "eduCommonsv1.1";
	$res .= "</langstring>\n";
	$res .= "</source>\n";
	$res .= "<value>";
	$res .= "<langstring>";
	$res .= "</langstring>\n";
	$res .= "</value>\n";
	$res .= "<description>";
	$res .= "<langstring>";
	$res .= "</langstring>\n";
	$res .= "</description>\n";
	$res .= "</copyrightandotherrestrictions>\n";
	$res .= "</rights>\n";
	$res .= "</lom>\n";
	$res .= "<eduCommons>\n";
	$res .= "<objectType>";
	$res .= "</objectType>\n";
	$res .= "<license>";
	$res .= "</license>\n";
	$res .= "<clearedCopyright>";
	$res .= "</clearedCopyright>\n";
	$res .= "<courseId>";
	$res .= $courseData['contextcode'];
	$res .= "</courseId>\n";
	$res .= "<term>";
	$res .= "</term>\n";
	$res .= "<displayInstructorEmail>";
	$res .= "</displayInstructorEmail>\n";
	$res .= "</eduCommons>\n";
	$res .= "</metadata>\n";
	$res .= "<file>\n";
	$res .= "</file>\n";
	$res .= "</resource>\n";
	
	return $res;
}
}
?>