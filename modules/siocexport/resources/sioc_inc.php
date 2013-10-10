<?php
/**
 * SIOC Exporter API
 *
 * Allow people to easilly create their own SIOC Exporter for any PHP application
 *
 * @package sioc_inc
 * @author Alexandre Passant <alex@passant.org>
 * @author Uldis Bojars <captsolo@gmail.com> (adaptation to PHP4)
 * @author Thomas Schandl <tom.schandl@gmail.com> (addition of SIOCThread)
 * @author Fabrizio Orlandi <fabrizio.orlandi@deri.org> (addition of SIOCWIki SIOCWikiArticle SIOCCategory)
 */

define('FORUM_NODE', 'forum');		// TODO: Not used at the moment, remove it?
define('AUTHORS_NODE', 'authors');

define('EXPORTER_URL', 'http://wiki.sioc-project.org/index.php/PHPExportAPI');
define('EXPORTER_VERSION', '1.01');


/**
 * Main exporter class
 *
 * Generates RDF/XML content of SIOC export.
 * - Sets up parameters for generating RDF/XML
 * - Sets up parameters for SIOC URL generation
 */
class SIOCExporter {

    var $_title;
    var $_blog_url;
    var $_sioc_url;
    var $_encoding;
    var $_generator;

    var $_urlseparator;
    var $_urlequal;
    var $_url4type; // e.g. type   or   sioc_type
    var $_url4id; // TS e. g.  id    or sioc_id
    var $_url4page;
    var $_url_usetype; // TS: if true: appends the "type" of a class to the _url4id in order to compose the string for the "id part" of the siocURL. e. g. for a forum that could produce "forum_id=" or "forum_sioc_id="
    var $_url_suffix; // TS:  custom parameter to be appended at the end of a siocURL
    var $_type_table;
    var $_ignore_suffix; // TS: for types in this table the _url_suffix  won't be appended to their siocURL
    var $_export_email;

    var $_objects;

    function SIOCExporter() {
        $this->_urlseparator = '&';
        $this->_urlequal = '=';
        $this->_url4type = 'type';
        $this->_url4id = 'id';
        $this->_url4page = 'page';
        $this->_url_usetype = true;
        $this->_url_suffix = '';
        $this->_type_table = array();
        $this->_ignore_suffix = array();
        $this->_export_email = false;
        $this->_encoding = 'UTF-8';
        $this->_objects = array();
    }

    function setURLParameters($type='type', $id='id', $page='page', $url_usetype = true, $urlseparator='&', $urlequal='=', $suffix='') {
        $this->_urlseparator = $urlseparator;
        $this->_urlequal = $urlequal;
        $this->_url4type = $type;
        $this->_url4id = $id;
        $this->_url4page = $page;
        $this->_url_usetype = $url_usetype;
        $this->_url_suffix = $suffix;
    }

    function setParameters($title, $url, $sioc_url, $encoding, $generator, $export_email=false) {
        $this->_title = $title;
        $this->_blog_url = $url;
        $this->_sioc_url = $sioc_url;
        $this->_encoding = $encoding;
        $this->_generator = $generator;
	$this->_export_email = $export_email;
    }

    // Assigns some objects to the exporter
    function addObject( &$obj ) {
        $this->_objects[] = &$obj;
    }

	// TS: Used to replace _url4id in the siocURL for a given type (site, forum, etc.) with a parameter ($name) of your choice
	// E.g. b2evo exporter uses "blog=" instead of "sioc_id=" in the siocURL of a forum
    function setURLTypeParm($type, $name) {
        $this->_type_table[$type] = $name;
    }

    function setSuffixIgnore($type) {
        $this->_ignore_suffix[$type] = 1;
    }

	function siocURL($type, $id, $page=""){
		$type_part = $this->_url4type .$this->_urlequal .  $type ;

		if ($id) {
			if ( isset($this->_type_table[$type]) )
	            $myID = $this->_type_table[$type] ;
	        else
	            $myID = (($this->_url_usetype) ? $type . '_' : '') . $this->_url4id ;

			$id_part = $this->_urlseparator . $myID . $this->_urlequal . $id  ;
		} else {
			$id_part = '';
		}

		($page) ? $page_part = $this->_urlseparator . $this->_url4page . $this->_urlequal . $page : $page_part='' ;

		($this->_url_suffix && !isset($this->_ignore_suffix[$type])) ? $suffix = $this->_urlseparator . $this->_url_suffix : $suffix = '';

		$siocURL = $this->_sioc_url . $type_part . $id_part . $page_part . $suffix ;
		return clean($siocURL);
	}

    function export( $rdf_content='' ) {
        header('Content-Type: application/rdf+xml; charset='.$this->_encoding);
        echo $this->makeRDF($rdf_content);
    }

    function makeRDF( $rdf_content='' ) {
        $rdf = '<?xml version="1.0" encoding="'.$this->_encoding.'" ?>'."\n";
        $rdf .= '
<rdf:RDF
    xmlns="http://xmlns.com/foaf/0.1/"
    xmlns:foaf="http://xmlns.com/foaf/0.1/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:dcterms="http://purl.org/dc/terms/"
    xmlns:dcmitype="http://purl.org/dc/dcmitype/"
    xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:sioc="http://rdfs.org/sioc/ns#"
    xmlns:sioct="http://rdfs.org/sioc/types#"
    xmlns:owl="http://www.w3.org/2002/07/owl#">
<foaf:Document rdf:about="">
	<dc:title>SIOC profile for "'.clean($this->_title).'"</dc:title>
	<dc:description>A SIOC profile describes the structure and contents of a community site (e.g., weblog) in a machine processable form. For more information refer to the '.clean('<a href="http://rdfs.org/sioc">SIOC project page</a>').'</dc:description>
	<foaf:primaryTopic rdf:resource="'.clean($this->_objects[0]->_url).'"/>
	<admin:generatorAgent rdf:resource="'.clean(EXPORTER_URL).'?version='.EXPORTER_VERSION.'"/>
	<admin:generatorAgent rdf:resource="'.clean($this->_generator).'"/>
</foaf:Document>'."\n";
        if ($rdf_content) $rdf .= $rdf_content;
        if (sizeof($this->_objects)) {
            foreach($this->_objects as $object) {
                if($object) $rdf .= $object->getContent( $this );
            }
        }
        $rdf .= "\n</rdf:RDF>";
        return $rdf;
    }
}

/**
 * Generic SIOC Object
 *
 * All SIOC objects are derived from this.
 */
class SIOCObject {
    var $_note = '';

    function addNote($note) {
        $this->_note = $note;
    }

    function getContent( &$exp ) {
        $rdf =  "<sioc:Object>\n";
        $rdf .= "\t<rdfs:comment>Generic SIOC Object</rdfs:comment>\n";
        $rdf .= "</sioc:Object>\n";
        return $rdf;
    }
}

/**
 * SIOC::Site object
 *
 * Contains information about main SIOC page including:
 *  - site description
 *  - list of forums
 *  - list of users
 */
class SIOCSite extends SIOCObject {

    var $type = 'site';

    var $_url;
    var $_name;
    var $_description;
    var $_forums;
    var $_users;
    var $_page;
    var $_next_users;
    var $_next_forums;
    var $_usergroup_uri;

    function SIOCSite($url, $name, $description, $page='', $usergroup_uri='') {
        $this->_url = $url;
        $this->_name = $name;
        $this->_description = $description;
        $this->_forums = array();
        $this->_users = array();
	$this->_page = $page;
	$this->_next_users = false;
       	$this->_next_forums = false;
       	$this->_usergroup_uri = $usergroup_uri;
    }

    function addForum($id, $url) {
        $this->_forums[$id] = $url;
    }

    function addUser($id, $url) {
        $this->_users[$id] = $url;
    }

	function setNextPageUsers($next) {
        $this->_next_users = $next;
    }

    function setNextPageForums($next) {
        $this->_next_forums = $next;
    }

    function getContent( &$exp ) {
        $rdf = "<sioc:Site rdf:about=\"" . clean($this->_url) ."\">\n";
        $rdf .="\t<dc:title>" . clean($this->_name) . "</dc:title>\n";
        $rdf .= "\t<dc:description>" . clean($this->_description) . "</dc:description>\n";
        $rdf .= "\t<sioc:link rdf:resource=\"" . clean($this->_url) ."\"/>\n";
        if($this->_forums) {
            foreach ($this->_forums as $id => $url) {
                $rdf .= "\t<sioc:host_of rdf:resource=\"" . clean($url) . "\"/>\n";
            }
        }
		if($this->_next_forums) {
            $rdf .= "\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('site', "", $this->_page+1) ."\"/>\n";
        }
		if($this->_usergroup_uri) {
			$rdf .= "\t<sioc:has_Usergroup rdf:resource=\"" . $this->_usergroup_uri . "\"/>\n";
		} else {
			$rdf .= "\t<sioc:has_Usergroup rdf:nodeID=\"" . AUTHORS_NODE . "\"/>\n";
		}
        $rdf .= "</sioc:Site>\n";
        // Forums
        if($this->_forums) {
            $rdf .= "\n";
            foreach ($this->_forums as $id => $url) {
                $rdf .= '<sioc:Forum rdf:about="' . clean($url) ."\">\n";
                $rdf .= "\t<sioc:link rdf:resource=\"" . clean($url) . "\"/>\n";
                $rdf .= "\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('forum', $id) ."\"/>\n";
                $rdf .= "</sioc:Forum>\n";
            }
        }
        // Usergroup
        if($this->_users) {
            $rdf .= "\n";
			if($this->_usergroup_uri) {
				$rdf .= '<sioc:Usergroup rdf:about="' . $this->_usergroup_uri . "\">\n";
			} else {
				$rdf .= '<sioc:Usergroup rdf:nodeID="' . AUTHORS_NODE . "\">\n";
			}
            $rdf .= "\t<sioc:name>Authors for \"" . clean($this->_name) . "\"</sioc:name>\n";
                foreach ($this->_users as $id => $url) {
                    $rdf .= "\t<sioc:has_member>\n";
                    $rdf .= "\t\t<sioc:User rdf:about=\"" . clean($url) ."\">\n";
                    $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('user', $id). "\"/>\n";
                    $rdf .= "\t\t</sioc:User>\n";
                    $rdf .= "\t</sioc:has_member>\n";
                }
			if($this->_next_users) {
            $rdf .= "\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('site', "", $this->_page+1) ."\"/>\n";
			}
            $rdf .= "</sioc:Usergroup>\n";
        }

        return $rdf;
    }
}

// Export detaille d'un utilisateur
/**
 * SIOC::User object
 *
 * Contains user profile information
 */
class SIOCUser extends SIOCObject {

    var $type = 'user';

    var $_id;
    var $_nick;
    var $_uri;
    var $_name;
    var $_email;
    var $_sha1;
    var $_homepage;
    var $_foaf_uri;
    var $_role;
    var $_sioc_url;
    var $_foaf_url;

    function SIOCUser($id, $uri, $name, $email, $homepage='', $foaf_uri='', $role=false, $nick='', $sioc_url='', $foaf_url='') {
        $this->_id = $id;
        $this->_uri = $uri;
        $this->_name = $name;

        if (preg_match_all('/^.+@.+\..+$/Ui', $email, $check, PREG_SET_ORDER)) {
            if (preg_match_all('/^mailto:(.+@.+\..+$)/Ui', $email, $matches, PREG_SET_ORDER)) {
                $this->_email = $email;
                $this->_sha1 = sha1($email);
            } else {
                $this->_email = "mailto:".$email;
                $this->_sha1 = sha1("mailto:".$email);
            }
        }
        $this->_homepage = $homepage;
        $this->_foaf_uri = $foaf_uri;
        $this->_url = $foaf_uri;
        $this->_role = $role;
        $this->_nick = $nick;
        $this->_foaf_url = $foaf_url;
        $this->_sioc_url = $sioc_url;
    }

    function getContent( &$exp ) {
        $rdf = "<foaf:Person rdf:about=\"" . clean($this->_foaf_uri) . "\">\n";
        if($this->_name) $rdf .= "\t<foaf:name>". $this->_name . "</foaf:name>\n";
        if($this->_email) { $rdf .= "\t<foaf:mbox_sha1sum>" . $this->_sha1 . "</foaf:mbox_sha1sum>\n"; }
        if($this->_foaf_url) { $rdf .= "\t<rdfs:seeAlso rdf:resource=\"". $this->_foaf_url ."\"/>\n"; }
        $rdf .= "\t<foaf:holdsAccount>\n";
        $rdf .= "\t\t<sioc:User rdf:about=\"" . clean($this->_uri) ."\">\n";
        if($this->_nick) $rdf .= "\t\t\t<sioc:name>" . $this->_nick . "</sioc:name>\n";
        if($this->_email) {
            if ($exp->_export_email) { $rdf .= "\t\t\t<sioc:email rdf:resource=\"" . $this->_email ."\"/>\n"; }
            $rdf .= "\t\t\t<sioc:email_sha1>" . $this->_sha1 . "</sioc:email_sha1>\n";
        }
        if($this->_role) {
            $rdf .= "\t\t\t<sioc:has_function>\n";
            $rdf .= "\t\t\t\t<sioc:Role>\n";
            $rdf .= "\t\t\t\t\t<sioc:name>" . $this->_role . "</sioc:name>\n";
            $rdf .= "\t\t\t\t</sioc:Role>\n";
            $rdf .= "\t\t\t</sioc:has_function>\n";
        }
        if($this->_sioc_url) { $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"". $this->_sioc_url ."\"/>\n"; }
        $rdf .= "\t\t</sioc:User>\n";
        $rdf .= "\t</foaf:holdsAccount>\n";
        $rdf .= "</foaf:Person>\n";
        return $rdf;
    }
}

// Export detaille d'un utilisateur
/**
 * SIOC::Thread object
 *
 * Contains information about a SIOC Thread in a SIOC Forum
  - list of posts in that thread
 */

class SIOCThread extends SIOCObject {

    var $type = 'thread';
    var $_id;
    var $_url;
    var $_page;
    var $_posts;
    var $_next;
    var $_views;
    var $_tags;
    var $_related;
    var $_title;
    var $_created;
    var $_parents;

    function SIOCThread($id, $url, $page, $views='', $tags=array(), $subject='', $created='' ) {
        $this->_id = $id;
        $this->_url = $url;
        $this->_page = $page;
        $this->_posts = array();
        $this->_next = false;
        $this->_views = $views;
        $this->_tags = $tags;
        $this->_related = array();
        $this->_subject = $subject;
        $this->_created = $created;
    }

    function addPost($id, $url, $prev='', $next='') {
        $this->_posts[$id] = array("url" => $url, "prev" => $prev, "next" => $next);
    }

	// add links to things that are similar to this via sioc:related_to
    function addRelated($id, $url) {
        $this->_related[$id] = $url;
    }

    function setNextPage($next) {
        $this->_next = $next;
    }

	function addParentForum($id, $url) {
        $this->_parents[$id] = $url;
    }

    function getContent( &$exp) {
        $rdf .= '<sioc:Thread rdf:about="' . clean($this->_url) . "\">\n";
        $rdf .= "\t<sioc:link rdf:resource=\"" . clean($this->_url) . "\"/>\n";
        if ($this->_views)  $rdf .= "\t<sioc:num_views>" . $this->_views . "</sioc:num_views>\n";
        if ($this->_note)   $rdf .= "\t<rdfs:comment>" . $this->_note . "</rdfs:comment>\n";
		if ($this->_subject) { $rdf .= "\t<dc:title>" . $this->_subject . "</dc:title>\n"; }
        if ($this->_created) { $rdf .= "\t<dcterms:created>" . $this->_created . "</dcterms:created>\n"; }
        if ($this->_parents) {
			foreach($this->_parents as $id => $uri) {
				$rdf .= "\t<sioc:has_parent>\n";
				$rdf .= "\t\t<sioc:Forum rdf:about=\"" .clean($uri) ."\">\n";
				$rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('forum', $id) . "\"/>\n";
				$rdf .= "\t\t</sioc:Forum>\n";
				$rdf .= "\t</sioc:has_parent>\n";
			}
		}
		// here the tags are just used as keywords for dc:subject
        if ($this->_tags) {
            foreach ($this->_tags as $id => $tag) {
                $rdf .= "\t<dc:subject>" . $tag . "</dc:subject>\n";
            }
        }
		// here the tags are used by creating a tag object with a blank node, with the keyword as moat:name - if you use this insert prefixes for moat and tags
		// if ($this->_tags) {
			// $i=1;
            // foreach ($this->_tags as $id => $tag) {
				// $rdf .= "\t<tags:taggedWithTag>\n";
				// $rdf .= "\t\t<moat:tag rdf:nodeID=\"b$i\">\n";
				// // actually, the best way is to have 'reference URIs' for tags, e.g. URIs for all the platform (http://tags.example.org/tag/soccer
                // $rdf .= "\t\t\t<moat:name>" . $tag . "</moat:name>\n";
				// $rdf .= "\t\t</moat:tag>\n";
				// $rdf .= "\t</moat:taggedWithTag>\n";
				// $i++;
            // }
        // }

		// here the tags are used are used for sioc:topic, each topic needs to have a URI
		/*if($this->_tags) {
            foreach($this->_tags as $url=>$topic) {
                $rdf .= "\t<sioc:topic rdfs:label=\"$topic\" rdf:resource=\"" . clean($url) ."\"/>\n";
            }
        }
		*/
		if ($this->_related) {
			foreach ($this->_related as $id => $url) {
					$rdf .= "\t<sioc:related_to>\n";
					$rdf .= "\t\t<sioc:Thread rdf:about=\"" .clean($url) ."\"/>\n";
					$rdf .= "\t</sioc:related_to>\n"; // todo - each topic needs to have a URI
			}
		}

        if ($this->_posts) {
            foreach($this->_posts as $id => $data) {
                $rdf .= "\t<sioc:container_of>\n";
                $rdf .= "\t\t<sioc:Post rdf:about=\"" .clean($data[url]) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('post', $id) . "\"/>\n";
                if ($data[prev]) { $rdf .= "\t\t\t<sioc:previous_by_date rdf:resource=\"" . clean($data[prev]) . "\"/>\n"; }
                if ($data[next]) { $rdf .= "\t\t\t<sioc:next_by_date rdf:resource=\"" . clean($data[next]) . "\"/>\n"; }
                $rdf .= "\t\t</sioc:Post>\n";
                $rdf .= "\t</sioc:container_of>\n";
            }
        }
        if($this->_next) {
            $rdf .= "\r<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('thread', $this->_id, $this->_page+1) ."\"/>\n";
        }
        $rdf .= "</sioc:Thread>\n";
        return $rdf;
    }
}

// Export d'un forum avec une liste de posts -variable (next with seeAlso)
/**
 * SIOC::Forum object
 *
 * Contains information about SIOC Forum (blog, ...):
 *  - description of a forum
 *  - list of posts within a forum [partial, paged]
 */

class SIOCForum extends SIOCObject {

    var $type = 'forum';

    var $_id;
    var $_url;
    var $_page;
    var $_posts;
    var $_next;
    var $_blog_title;
    var $_description;
    var $_threads;
    var $_parents;
    var $_type;
    var $_creator;
    var $_administrator;

    function SIOCForum($id, $url, $page, $title='', $descr='', $type='sioc:Forum', $creator='', $admin='', $links=array() ) {
        $this->_id = $id;
        $this->_url = $url;
        $this->_page = $page;
        $this->_posts = array();
        $this->_next = false;
        $this->_blog_title = $title;
        $this->_description = $descr;
       	$this->_threads = array();
       	$this->_parents = array();
       	$this->_type = $type;
       	$this->_creator = $creator;
       	$this->_administrator = $admin;
       	$this->_links = $links;
    }

    function addPost($id, $url) {
        $this->_posts[$id] = $url;
    }

	function addThread($id, $url) {
        $this->_threads[$id] = $url;
    }

	function addParentForum($id, $url) {
        $this->_parents[$id] = $url;
    }

    function setNextPage($next) {
        $this->_next = $next;
    }

    function getContent( &$exp) {
        $rdf = '<'.$this->_type. ' rdf:about="' . clean($this->_url) . "\">\n";
		if ($this->_type != 'sioc:Forum') $rdf .= "\t<rdf:type rdf:resource=\"http://rdfs.org/sioc/ns#Forum\" />\n";
        $rdf .= "\t<sioc:link rdf:resource=\"" . clean($this->_url) . "\"/>\n";
        if ($this->_blog_title)  $rdf .= "\t<dc:title>" . $this->_blog_title . "</dc:title>\n";
        if ($this->_description) $rdf .= "\t<dc:description>" . $this->_description . "</dc:description>\n";
        if ($this->_note)        $rdf .= "\t<rdfs:comment>" . $this->_note . "</rdfs:comment>\n";

		if ($this->_parents) {
			foreach($this->_parents as $id => $uri) {
				$rdf .= "\t<sioc:has_parent>\n";
				$rdf .= "\t\t<sioc:Forum rdf:about=\"" .clean($uri) ."\">\n";
				$rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('forum', $id) . "\"/>\n";
				$rdf .= "\t\t</sioc:Forum>\n";
				$rdf .= "\t</sioc:has_parent>\n";
            }
		}

		if ($this->_threads) {
			foreach($this->_threads as $id => $uri) {
				$rdf .= "\t<sioc:parent_of>\n";
				$rdf .= "\t\t<sioc:Thread rdf:about=\"" .clean($uri) ."\">\n";
				$rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('thread', $id) . "\"/>\n";
				$rdf .= "\t\t</sioc:Thread>\n";
				$rdf .= "\t</sioc:parent_of>\n";
            }
		}

        if($this->_posts) {
            foreach($this->_posts as $id => $url) {
                $rdf .= "\t<sioc:container_of>\n";
                $rdf .= "\t\t<sioc:Post rdf:about=\"" .clean($url) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('post', $id) . "\"/>\n";
                $rdf .= "\t\t</sioc:Post>\n";
                $rdf .= "\t</sioc:container_of>\n";
            }
        }

		if ($this->_creator) {
            if ($this->_creator->_id) {
                $rdf .= "\t<sioc:has_creator>\n";
                $rdf .= "\t\t<sioc:User rdf:about=\"" . clean($this->_creator->_uri) ."\">\n";
                if($this->_creator->_sioc_url) { $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"". $this->_creator->_sioc_url ."\"/>\n"; }
                else $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('user', $this->_creator->_id). "\"/>\n";
                $rdf .= "\t\t</sioc:User>\n";
                $rdf .= "\t</sioc:has_creator>\n";
                $rdf .= "\t<foaf:maker>\n";
                $rdf .= "\t\t<foaf:Person rdf:about=\"" . clean($this->_creator->_foaf_uri) ."\">\n";
                if($this->_creator->_foaf_url) { $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"". $this->_creator->_foaf_url ."\"/>\n"; }
                else $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('user', $this->_creator->_id). "\"/>\n";
                $rdf .= "\t\t</foaf:Person>\n";
                $rdf .= "\t</foaf:maker>\n";
            } else {
                $rdf .= "\t<foaf:maker>\n";
                $rdf .= "\t\t<foaf:Person";
                if($this->_creator->_name) $rdf .= " foaf:name=\"" . $this->_creator->_name ."\"";
                if($this->_creator->_sha1) $rdf .= " foaf:mbox_sha1sum=\"" . $this->_creator->_sha1 ."\"";
                if($this->_creator->_name) $rdf .= ">\n\t\t\t<foaf:homepage rdf:resource=\"" . $this->_creator->_homepage ."\"/>\n\t\t</foaf:Person>\n";
                else $rdf .= "/>\n";
                $rdf .= "\t</foaf:maker>\n";
            }
        }

		if ($this->_administrator) {
            if ($this->_administrator->_id) {
                $rdf .= "\t<sioc:has_administrator>\n";
                $rdf .= "\t\t<sioc:User rdf:about=\"" . clean($this->_administrator->_uri) ."\">\n";
                if($this->_administrator->_sioc_url) { $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"". $this->_administrator->_sioc_url ."\"/>\n"; }
                else $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('user', $this->_administrator->_id). "\"/>\n";
                $rdf .= "\t\t</sioc:User>\n";
                $rdf .= "\t</sioc:has_administrator>\n";
            }
        }
		if ($this->_links) {
            foreach($this->_links as $url=>$link) {
                $rdf .= "\t<sioc:links_to rdfs:label=\"$link\" rdf:resource=\"" . clean($url) ."\"/>\n";
            }
        }

        if($this->_next) {
            $rdf .= "\r<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('forum', $this->_id, $this->_page+1) ."\"/>\n";
        }
		$rdf .=  "</".$this->_type.">";

        return $rdf;
    }
}

/**
 * SIOC::Post object
 *
 * Contains information about a post
 */
class SIOCPost extends SIOCObject {

    var $type = 'post';

    var $_url;
    var $_subject;
    var $_content;
    var $_encoded;
    var $_creator;
    var $_created;
    var $_updated;
    var $_topics;
    var $_links;
    var $_comments;
    var $_reply_of;
    var $_type;
    var $_has_part;

    function SIOCPost($url, $subject, $content, $encoded, $creator, $created, $updated="", $topics=array(), $links=array(), $type='sioc:Post', $has_part=array() ) {
        $this->_url = $url;
        $this->_subject = $subject;
        $this->_content = $content;
        $this->_encoded = $encoded;
        $this->_creator = $creator;
        $this->_created = $created;
        $this->_updated = $updated;
        $this->_topics = $topics;
        $this->_links = $links;
        $this->_comments = array();
        $this->_reply_of = array();
       	$this->_type = $type;
       	$this->_has_part = $has_part;
    }

    function addComment($id, $url) {
        $this->_comments[$id] = $url;
    }

    function addReplyOf($id, $url) {
        $this->_reply_of[$id] = $url;
    }

    function getContent( &$exp ) {
        $rdf = '<'.$this->_type." rdf:about=\"" . clean($this->_url) . "\">\n";
        if ($this->_type != 'sioc:Post') $rdf .= "\t<rdf:type rdf:resource=\"http://rdfs.org/sioc/ns#Post\" />\n";
        if ($this->_subject) { $rdf .= "\t<dc:title>" . $this->_subject . "</dc:title>\n"; }
        if ($this->_creator) {
            if ($this->_creator->_id) {
                $rdf .= "\t<sioc:has_creator>\n";
                $rdf .= "\t\t<sioc:User rdf:about=\"" . clean($this->_creator->_uri) ."\">\n";
                if($this->_creator->_sioc_url) { $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"". $this->_creator->_sioc_url ."\"/>\n"; }
                else $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('user', $this->_creator->_id). "\"/>\n";
                $rdf .= "\t\t</sioc:User>\n";
                $rdf .= "\t</sioc:has_creator>\n";
                $rdf .= "\t<foaf:maker>\n";
                $rdf .= "\t\t<foaf:Person rdf:about=\"" . clean($this->_creator->_foaf_uri) ."\">\n";
                if($this->_creator->_foaf_url) { $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"". $this->_creator->_foaf_url ."\"/>\n"; }
                else $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('user', $this->_creator->_id). "\"/>\n";
                $rdf .= "\t\t</foaf:Person>\n";
                $rdf .= "\t</foaf:maker>\n";
            } else {
                $rdf .= "\t<foaf:maker>\n";
                $rdf .= "\t\t<foaf:Person";
                if($this->_creator->_name) $rdf .= " foaf:name=\"" . $this->_creator->_name ."\"";
                if($this->_creator->_sha1) $rdf .= " foaf:mbox_sha1sum=\"" . $this->_creator->_sha1 ."\"";
                if($this->_creator->_name) $rdf .= ">\n\t\t\t<foaf:homepage rdf:resource=\"" . $this->_creator->_homepage ."\"/>\n\t\t</foaf:Person>\n";
                else $rdf .= "/>\n";
                $rdf .= "\t</foaf:maker>\n";
            }
        }
        $rdf .= "\t<dcterms:created>" . $this->_created . "</dcterms:created>\n";
        if ($this->_updated AND ($this->_created != $this->_updated) ) $rdf .= "\t<dcterms:modified>" . $this->_updated . "</dcterms:modified>\n";
        $rdf .= "\t<sioc:content>" . pureContent($this->_content) . "</sioc:content>\n";

        $rdf .= "\t<content:encoded><![CDATA[" . $this->_encoded . "]]></content:encoded>\n";
        if($this->_topics) {
            foreach($this->_topics as $url=>$topic) {
                $rdf .= "\t<sioc:topic rdfs:label=\"$topic\" rdf:resource=\"" . clean($url) ."\"/>\n";
            }
        }
        if($this->_links) {
            foreach($this->_links as $url=>$link) {
                $rdf .= "\t<sioc:links_to rdfs:label=\"$link\" rdf:resource=\"" . clean($url) ."\"/>\n";
            }
        }
		if($this->_has_part) {
            foreach($this->_has_part as $id=>$url) {
                $rdf .= "\t<dcterms:hasPart>\n";
				$rdf .= "\t\t<dcmitype:Image rdf:about=\"" . clean($url) . "\"/>\n";
				$rdf .= "\t</dcterms:hasPart>\n";
            }
        }
        if($this->_reply_of) {
            foreach($this->_reply_of as $id => $url) {
                $rdf .= "\t<sioc:reply_of>\n";
                $rdf .= "\t\t<sioc:Post rdf:about=\"" . clean($url) . "\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('post', $id) . "\"/>\n";
                $rdf .= "\t\t</sioc:Post>\n";
                $rdf .= "\t</sioc:reply_of>\n";
            }
        }
        if($this->_comments) {
            foreach($this->_comments as $id => $url) {
                $rdf .= "\t<sioc:has_reply>\n";
                $rdf .= "\t\t<sioc:Post rdf:about=\"" . clean($url) ."\">\n";
        //        if($comments->f('comment_trackback')) $rdf .= "\t\t\t<sioc:type>" . POST_TRACKBACK . "</sioc:type>\n";
        //        else $rdf .= "\t\t\t<sioc:type>" . POST_COMMENT  . "</sioc:type>\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('comment', $id) . "\"/>\n";
                $rdf .= "\t\t</sioc:Post>\n";
                $rdf .= "\t</sioc:has_reply>\n";
            }
        }
        $rdf .=  "</".$this->_type.">\n";
        return $rdf;
    }
}

/**
 * SIOC::WikiArticle object
 *
 * Contains information about a wiki article
 */
class SIOCWikiArticle extends SIOCObject {

    var $type = 'sioct:WikiArticle';

    var $_url;
    var $_api = null;
    var $_subject;
    var $_redirpage;
    var $_content;
    var $_creator;
    var $_created;
    var $_topics;
    var $_links;
    var $_ext_links;
    var $_type;
    var $_previous_version;
    var $_next_version;
    var $_latest_version;
    var $_has_discussion;
    var $_has_container;

    function SIOCWikiArticle($url, $api, $subject, $redir, $user, $created, $prev_vers, $next_vers, $latest_vers, $has_discuss, $container, $topics=array(), $links=array(), $ext_links=array(), $type='sioct:WikiArticle', $content=null) {
        $this->_url = $url;
        $this->_api = $api;
        $this->_subject = $subject;
        $this->_redirpage = $redir;
        $this->_content = $content;
        $this->_creator = $user;
        $this->_created = $created;
        $this->_topics = $topics;
        $this->_links = $links;
        $this->_ext_links = $ext_links;
       	$this->_type = $type;
        $this->_previous_version = $prev_vers;
       	$this->_next_version = $next_vers;
        $this->_latest_version = $latest_vers;
        $this->_has_discussion = $has_discuss;
        $this->_has_container = $container;
    }

    function getContent( &$exp ) {
        $rdf = '<'.$this->_type." rdf:about=\"" . clean($this->_url) . "\">\n";
        if ($this->_subject)
        {
            $rdf .= "\t<dc:title>" . clean($this->_subject) . "</dc:title>\n";
            if(strcmp($this->_has_container, 'http://en.wikipedia.org')===0)
                $rdf .= "\t<foaf:primaryTopic rdf:resource=\"".clean('http://dbpedia.org/resource/'.$this->_subject)."\"/>\n";
        }
        if ($this->_creator->_nick) {
            /*if ($this->_creator->_id) {
                $rdf .= "\t<sioc:has_creator>\n";
                $rdf .= "\t\t<sioc:User rdf:about=\"" . clean($this->_creator->_uri) ."\">\n";
                if($this->_creator->_sioc_url) { $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"". $this->_creator->_sioc_url ."\"/>\n"; }
                else $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('user', $this->_creator->_id). "\"/>\n";
                $rdf .= "\t\t</sioc:User>\n";
                $rdf .= "\t</sioc:has_creator>\n";
                $rdf .= "\t<foaf:maker>\n";
                $rdf .= "\t\t<foaf:Person rdf:about=\"" . clean($this->_creator->_foaf_uri) ."\">\n";
                if($this->_creator->_foaf_url) { $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"". $this->_creator->_foaf_url ."\"/>\n"; }
                else $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" . $exp->siocURL('user', $this->_creator->_id). "\"/>\n";
                $rdf .= "\t\t</foaf:Person>\n";
                $rdf .= "\t</foaf:maker>\n";
            } else {*/
                $rdf .= "\t<sioc:has_creator>\n";
                $rdf .= "\t\t<sioc:User rdf:about=\"" . clean($this->_creator->_uri) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$this->_creator->_uri);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioc:User>\n";
                $rdf .= "\t</sioc:has_creator>\n";
                $rdf .= "\t<dc:contributor>" . clean($this->_creator->_nick) ."</dc:contributor>\n";
                /*$rdf .= "\t<foaf:maker>\n";
                $rdf .= "\t\t<foaf:Person";
                if($this->_creator->_name) $rdf .= " foaf:name=\"" . $this->_creator->_name ."\"";
                if($this->_creator->_sha1) $rdf .= " foaf:mbox_sha1sum=\"" . $this->_creator->_sha1 ."\"";
                if($this->_creator->_homepage) $rdf .= ">\n\t\t\t<foaf:homepage rdf:resource=\"" . $this->_creator->_homepage ."\"/>\n\t\t</foaf:Person>\n";
                else $rdf .= "/>\n";
                $rdf .= "\t</foaf:maker>\n";
            }*/
        } else {
            if ($this->_creator !== 'void')
            {
                $rdf .= "\t<sioc:has_creator>\n";
                $rdf .= "\t\t<sioc:User>\n";
                $rdf .= "\t\t</sioc:User>\n";
                $rdf .= "\t</sioc:has_creator>\n";
            }
        }
        if ($this->_created) {
            $rdf .= "\t<dcterms:created>" . $this->_created . "</dcterms:created>\n";
        }
        if ($this->_content) {
            $rdf .= "\t<content:encoded><![CDATA[" . $this->_content . "]]></content:encoded>\n";
        }
        if(is_array($this->_topics)) {
            foreach($this->_topics as $topic=>$url) {
                $rdf .= "\t<sioc:topic>\n";
                $rdf .= "\t\t<sioct:Category rdf:about=\"" . clean($url) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$url);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioct:Category>\n";
                $rdf .= "\t</sioc:topic>\n";
            }
        }
        if (is_array($this->_links)) {
            foreach($this->_links as $label=>$url) {
                $rdf .= "\t<sioc:links_to>\n";
                $rdf .= "\t\t<sioct:WikiArticle rdf:about=\"" . clean($url) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$url);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioct:WikiArticle>\n";
                $rdf .= "\t</sioc:links_to>\n";
            }
        } else
        { if($this->_links)
            {
                $rdf .= "\t<sioc:links_to>\n";
                $rdf .= "\t\t<sioct:WikiArticle rdf:about=\"" . clean($this->_links) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$this->_links);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioct:WikiArticle>\n";
                $rdf .= "\t</sioc:links_to>\n";
            }
        }
        if(is_array($this->_ext_links)) {
            foreach($this->_ext_links as $label=>$url) {
                $rdf .= "\t<sioc:links_to rdf:resource=\"" . clean($url) ."\"/>\n";
            }
        }
        if($this->_previous_version) {
                $rdf .= "\t<sioc:previous_version>\n";
                $rdf .= "\t\t<sioct:WikiArticle rdf:about=\"" . clean($this->_previous_version) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$this->_previous_version);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioct:WikiArticle>\n";
                $rdf .= "\t</sioc:previous_version>\n";
                /*If there is support for inference and transitivity the following is not needed
                $rdf .= "\t<sioc:earlier_version>\n";
                $rdf .= "\t\t<sioct:WikiArticle rdf:about=\"" . clean($this->_previous_version) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$this->_previous_version);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioct:WikiArticle>\n";
                $rdf .= "\t</sioc:earlier_version>\n";
                 */
        }
        if($this->_next_version) {
                $rdf .= "\t<sioc:next_version>\n";
                $rdf .= "\t\t<sioct:WikiArticle rdf:about=\"" . clean($this->_next_version) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$this->_next_version);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioct:WikiArticle>\n";
                $rdf .= "\t</sioc:next_version>\n";
                /*If there is support for inference and transitivity the following is not needed
                $rdf .= "\t<sioc:later_version>\n";
                $rdf .= "\t\t<sioct:WikiArticle rdf:about=\"" . clean($this->_next_version) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$this->_next_version);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioct:WikiArticle>\n";
                $rdf .= "\t</sioc:later_version>\n";
                 */
        }
        if($this->_latest_version) {
                $rdf .= "\t<sioc:latest_version>\n";
                $rdf .= "\t\t<sioct:WikiArticle rdf:about=\"" . clean($this->_latest_version) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$this->_latest_version);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioct:WikiArticle>\n";
                $rdf .= "\t</sioc:latest_version>\n";
        }
        if($this->_has_discussion && (strpos($this->_has_discussion, 'Talk:Talk:') == FALSE)) {
                $rdf .= "\t<sioc:has_discussion>\n";
                $rdf .= "\t\t<sioct:WikiArticle rdf:about=\"" . clean($this->_has_discussion) ."\">\n";
                $rdf .= "\t\t\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$this->_has_discussion);
                if ($this->_api) $rdf .= clean("&api=" . $this->_api);
                $rdf .= "\"/>\n";
                $rdf .= "\t\t</sioct:WikiArticle>\n";
                $rdf .= "\t</sioc:has_discussion>\n";
        }
        if($this->_has_container) {
                $rdf .= "\t<sioc:has_container>\n";
                $rdf .= "\t\t<sioct:Wiki rdf:about=\"" . clean($this->_has_container) ."\"/>\n";
                $rdf .= "\t</sioc:has_container>\n";
        }
        if($this->_redirpage)
        {
            $rdf .= "\t<owl:sameAs rdf:resource=\"" . clean($this->_redirpage) ."\"/>\n";
            $rdf .= "\t<rdfs:seeAlso rdf:resource=\"" .
                        clean('http://ws.sioc-project.org/mediawiki/mediawiki.php?wiki='.$this->_redirpage);
            if ($this->_api) $rdf .= clean("&api=" . $this->_api);
            $rdf .= "\"/>\n";
        }

        $rdf .=  "</".$this->_type.">\n";
        return $rdf;
    }
}

/**
 * SIOC::Wiki object
 *
 * Contains information about a wiki site
 */
class SIOCWiki extends SIOCObject {

    var $_url;
    var $_type;

    function SIOCWiki ($url, $type='sioct:Wiki') {
        $this->_url = $url;
       	$this->_type = $type;
    }

    function getContent( &$exp ) {
        $rdf = '<'.$this->_type." rdf:about=\"" . clean($this->_url) . "\"/>\n";
        return $rdf;
    }
}

/**
 * SIOC::Category object
 *
 * Contains information about the category which is object of the sioc:topic property
 */
class SIOCCategory extends SIOCObject {

    var $_url;
    var $_type;

    function SIOCCategory ($url, $type='sioct:Category') {
        $this->_url = $url;
       	$this->_type = $type;
    }

    function getContent( &$exp ) {
        $rdf = '<'.$this->_type." rdf:about=\"" . clean($this->_url) . "\"/>\n";
        return $rdf;
    }
}


/**
 * "Clean" text
 *
 * Transforms text so that it can be safely put into XML markup
 */
if (!function_exists('clean')) {
  function clean( $text ) {
#    return htmlentities( $text );
    return htmlentities2( $text );
  }
}

/**
 * HTML Entities 2
 *
 * Same a HTMLEntities, but avoids double-encoding of entities
 */
if (!function_exists('htmlentities2')) {
  function htmlentities2($myHTML) {
    $translation_table=get_html_translation_table (HTML_ENTITIES,ENT_QUOTES);
    $translation_table[chr(38)] = '&';
    return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&amp;" , strtr($myHTML, $translation_table));
    //return htmlentities(strtr(str_replace(' ', '%20', $myHTML), $translation_table));
  }
}

/**
 * pureContent
 *
 * Prepares text-only representation of HTML content
 */
if (!function_exists('pureContent')) {
  function pureContent($content) {
    // Remove HTML tags
    // May add more cleanup code later, if validation errors are found
    return strip_tags($content);
  }
}
?>
