<?php
/*********************************************  
* XSPF Class is a class to generate
* a XSPF( http://www.xspf.org) playlist. 
* Copyright (C) 2005  Bjorn Wijers [me@bjornwijers.com]
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*
*	dependencies: 
*		-XML_Serializer, use PEAR to install
*		-XSPF_Track
*	mimetype: application/xspf+xml.
*
*	this class can be used to generate
*	a XSPF( http://www.xspf.org) playlist. 
*
*	usefull urls:
*	http://www.sitepoint.com/print/xml-php-pear-xml_serializer
*	http://www.melonfire.com/community/columns/trog/article.php?id=244&page=5
*
*	
*********************************************/

/* Enable these three lines during debugging: */
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
error_reporting(E_NOTICE | E_ALL );

/* set include path for pear install */
if(!ini_set("include_path",".:/usr/lib/php")) {
	die ("Sorry, but include_path could not be set");
}


	class XSPF {
		var $log = "LOG\n\n";			// value
		var $version;      				// value or NULL
		var $charset;      				// value or NULL
		var $xmlns;        				// value or NULL
		var $playlist_title;			// value or NULL
		var $playlist_creator;      	// value or NULL
		var $playlist_annotation;   	// value or NULL
		var $playlist_info;         	// value or NULL
		var $playlist_location;     	// value or NULL
		var $playlist_identifier;   	// value or NULL
		var $playlist_image;        	// value or NULL
		var $playlist_date;         	// value or NULL
		var $playlist_license;      	// value or NULL
		var $track;						// value or NULL
		var $attributions = array() ;	// plural element names are arrays
		var $links = array(); 			// plural element names are arrays
		var $metas = array();			// plural element names are arrays
		var $extensions = array(); 		// plural element names are arrays
		var $tracklist = array(); 		// contains track objects      
		var $playlist_xml; 				// value or NULL
		
		/* CONSTRUCTOR */
		function XSPF() {
			require_once('class.XSPF_Track.php');	// track definition
			require_once('XML/Serializer.php');		// Serializer class from PEAR
			$this->setVersion();
			$this->setCharSet();
			$this->setXMLNameSpace();

		}

		
		
		/*
		* creates a track object based
		* on the data received and 
		* adds it to the tracklist array
		*/
		function addTrack($params) {
			// make sure addtrack gets an array
			if(!is_array($params)) {
				$this->writeToLog("addTrack did not receive a parameter of the type array");
				return false;
			}
			
			// create a Track object
			// the track object handles
			// the data itself
			$this->track = new XSPF_Track($params);
			array_push($this->tracklist, $this->track);
		}
		
		/*
		* output the playlist
		*/
		function serializePlaylist() {
			
			// get all the tracks for the list
			$all_tracks = $this->getTrackList();
			$nr_tracks = sizeof($all_tracks);
			$list = array("track"=>array());
			// NOTE: attribution, link, meta and extension are not functional yet!
			if($nr_tracks >= 0) {
				foreach($all_tracks as $index=>$value) {
					$a_track =	array (
									'location'=> $value->getTrackLocation(),
									'identifier'=> $value->getTrackIdentifier(),
									'title'=> $value->getTrackTitle(),
									'creator'=> $value->getTrackCreator(),
									'annotation'=> $value->getTrackAnnotation(),
									'info'=> $value->getTrackInfo(),
									'image'=> $value->getTrackImage(),
									'album'=> $value->getTrackAlbum(),
									'trackNum'=> $value->getTrackNr(),
									'duration'=> $value->getTrackDuration()
								 );
					array_push($list['track'], $a_track);			 
				}
			}
			//echo "<pre>";
			//print_r($list);
			//echo "</pre>";
			// playlist datamodel
			// NOTE: attribution, link, meta and extension are not functional yet!
			$playlist = array (
				'title'=>$this->getPlaylistTitle(),
				'creator'=>$this->getPlaylistCreator(),
				'annotation'=>$this->getPLaylistAnnotation(),
				'info'=>$this->getPlaylistInfo(),
				'location'=>$this->getPlaylistLocation(),
				'identifier'=>$this->getPlaylistIdentifier(),
				'image'=>$this->getPlaylistImage(),
				'date'=>$this->getPlaylistDate(),
				'license'=>$this->getPlaylistLicense(),
				'trackList'=>$list
			);
			
					
			// attributes for playlist root node
			$root_attributes = array();
			$root_attributes['version'] = $this->getVersion();
			$root_attributes['xmlns'] = $this->getXMLNS();
			
			// serializer options
			// this have been changed with version 0.16.0 of XML_Serializer
			$serializer_options = array (
				XML_SERIALIZER_OPTION_XML_DECL_ENABLED=>true,
				XML_SERIALIZER_OPTION_XML_ENCODING=>$this->getCharSet(),
				XML_SERIALIZER_OPTION_ROOT_NAME=>'playlist',
				XML_SERIALIZER_OPTION_ROOT_ATTRIBS=>$root_attributes,
				XML_SERIALIZER_OPTION_MODE=>XML_SERIALIZER_MODE_SIMPLEXML
			);
			  
			// Instantiate the serializer with the options
			$Serializer = &new XML_Serializer($serializer_options);
			
			// Serialize the data structure
			$status = $Serializer->serialize($playlist);

			// Check whether serialization worked
			if (PEAR::isError($status)) {
			   die($status->getMessage());
			}
			
			// Display the XML document
			//header('Content-type: text/xml');
			$this->playlist_xml = $Serializer->getSerializedData(); 
		}
		
		
		
		
		function writePlaylist($path = null) {
			// make sure the playlist is serialized
			$this->serializePlaylist();
					
			if(!$handle = fopen($path, "w+")) {
				return false;
			}
			
			if(fwrite($handle, $this->getPlaylistXML()) === FALSE) {
				return false;
			}
			fclose($handle);
			return true;
		
		}
		
		
		
		
		/* 
		* util function to keep a log 
		*	
		* NOTICE:
		* feedback will be appended to a var,
		* you can get the log by using getLog
		*	
		*/
		function WriteToLog($error) {
			$this->log .= $error . "\n"; //each line contains 1 error
		}
		
			
		/****** SETTERS *****/
		
		/* set the version defaults to version 1 */
		function setVersion($version = '1') {
			$this->version = $version;
		}
		
		/* set the version defaults to UTF-8 */
		function setCharSet($charset = 'UTF-8') {
			$this->charset = $charset;
		}
	
		/* 
		* set the xml namespace 
		* defaults to http://xspf.org/ns/0/
		*
		* NOTE: 
		* according to the xspf specs:
		* Notice that the default namespace is 
		* 0 but the default version is 1. 
		* This is because version 1 playlists 
		* are backwards compatible with version 
		* 0 parsers.
		*/
		function setXMLNameSpace($xmlns = 'http://xspf.org/ns/0/') {
			$this->xmlns = $xmlns;
		}
	
		/* set the playlist title defaults to testplaylist */
		function setPlaylistTitle($title = 'testplaylist') {
			$this->playlist_title = $title;
		}
		
		/* 
		* set the playlist creator defaults to testcreator 
		* Human-readable name of the entity (author, authors, group, company, etc) 
		* that authored the playlist
		*/
		function setPlaylistCreator($creator = 'testcreator') {
			$this->playlist_creator = $creator;
		}
		
		/* 
		* set the playlist annotation 
		* A human-readable comment on the playlist	
		*/
		function setPlaylistAnnotation($annotation) {
			$this->playlist_annotation = $annotation;
		}
	
		/* 
		* set the playlist info 
		* URL of a web page to find 
		* out more about this playlist
		*/
		function setPlaylistInfo($info) {
			$this->playlist_info = $info;
		}	
		
		/* 
		* set the playlist location. 
		* A source URL for this playlist
		*/
		function setPlaylistLocation($location) {
			$this->playlist_location = $location;
		}
		
		/* 
		* set the playlist identifier. 
		* Canonical ID for this playlist. 
		* Likely to be a hash or other 
		* location-independent name.
		*/
		function setPlaylistIdentifier($identifier) {
			$this->playlist_identifier = $identifier;
		}
		
		/*
		* set a playlist image
		* URL of an image to display 
		* in the absence of a 
		* playlist/trackList/image element
		*/
		function setPlaylistImage($image) {
			$this->playlist_image = $image;
		}
		
		/*
		* set a playlist date default to current time and date
		*  
		*  
		* NOTE: 
		* call setVersion before you call this one,
		* because there is a difference in handling 
		* dates between version 0 and version 1 and
		* this function handles the difference for you.
		* If you don't call version first this will result 
		* in an error.
		*
		* VERSION 0: 
		* in version 0 of XSPF, 
		* this was specifed as an ISO 8601 
		* YYYY-MM-DD format.
		* http://www.w3.org/QA/Tips/iso-date
		*
		* VERSION 1:
		* Creation date (not last-modified date)
		* of the playlist, formatted as a XML schema dateTime
		*
		*   	
		*/
		function setPlaylistDate($timestamp = NULL) {
			//default timestamp to current time and date
			if(empty($timestamp)) {
				$timestamp = time();
			}
			
			//get version and process the date accordingly
			switch ($this->getVersion()){
				//version 0
				case 0:
					$the_date = date("Y-m-d", $timestamp);
					break;
				//version 1
				case 1: 
					$main_date = date("Y-m-d\TH:i:s", $timestamp);				
					$tz = date("O", $timestamp);				
					$tz = substr_replace ($tz, ':', 3, 0);
					$the_date = $main_date . $tz;
					break;
				default:
					$this->writeToLog("Could not determine required date");
					break;
			}
			$this->playlist_date = $the_date;
		}
		
		/*
		* set a playlist license
		* URL of a resource that describes 
		* the license under which this playlist was released 
		*/
		function setPlaylistLicense($license) {
			$this->playlist_license = $license;
		}	
		
		//////////// NOT QUITE CLEAR HOW TO HANDLE THESE BELOW ///////////////////
		
		
		/*
		* set a playlist attribution list
		*
		* expects array with index: location OR indentifier
		*
		*
		* An ordered list of URIs. 
		* The purpose is to satisfy licenses 
		* allowing modification but requiring 
		* attribution. If you modify such a playlist, 
		* move its ##playlist/location OR ##playlist/identifier 
		* element to the top of the items in 
		* the //playlist/attribution element.
		*/
		function setPlaylistAttribution($attribution) {
			array_push($this->attributions, $attribution);
		}
		
		
		
		/*
		* set a playlist link list
		*
		* The link element allows non-XSPF 
		* web resources to be included in XSPF documents
		* without breaking XSPF validation
		*/
		function setPlaylistlink($link) {
			array_push($this->links, $link);
		}
		
		
		/*
		* set a playlist meta list
		*
		* The meta element allows non-XSPF
		* metadata to be included in XSPF 
		* documents without breaking XSPF 
		* validation
		*/
		function setPlaylistMeta($meta) {
			array_push($this->metas, $meta);
		}
		
		/*
		* set a playlist extension list
		*
		* The extension element 
		* allows non-XSPF XML to be
		* included in XSPF documents
		* without breaking XSPF validation
		*/
		function setPlaylistExtension($extension) {
			array_push($this->extensions, $extension);
		}
		
		//////////////////////////////////////////////////////////////////////////
		
		
		
		/***** GETTERS *****/
		
		
		/* get the version */
		function getVersion() {
			return $this->version;
		}
		
		function getXMLNS() {
			return $this->xmlns;
		}
		
		function getCharset() {
			return $this->charset;
		}
		
		function getPlaylistTitle() {
			return $this->playlist_title;
		}
		
		function getPlaylistCreator() {
			return $this->playlist_creator;
		}
		
		function getPlaylistAnnotation() {
			return $this->playlist_annotation;
		}
		
		function getPlaylistInfo() {
			return $this->playlist_info;
		}
		
		function getPlaylistImage() {
			return $this->playlist_image;
		}
		
		function getPlaylistDate() {
			return $this->playlist_date;
		}
		
		function getPlaylistLicense() {
			return $this->playlist_license;
		}
		
		function getPlaylistLocation() {
			return $this->playlist_location;
		}
		
		function getPlaylistIdentifier() {
			return $this->playlist_identifier;
		}
		
		
		function getLog() {
			return $this->log;
		}
		
		function getTracklist() {
			return $this->tracklist;
		}
		
		function getPlaylistXML() {
			if(!empty($this->playlist_xml)) {
				return $this->playlist_xml;
			} else {
				$this->serializePlaylist();
				return $this->playlist_xml;
			}
 			
		}
		
}




	
?>
