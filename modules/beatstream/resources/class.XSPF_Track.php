<?php
/*********************************************  
* XSPF_Track Class is a helperclass to generate
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
*		-XSPF
*	mimetype: application/xspf+xml.
*
*********************************************/

/* Enable these three lines during debugging: */
	ini_set("display_errors",1);
	ini_set("display_startup_errors",1);
	error_reporting(E_NOTICE | E_ALL );

	class XSPF_Track {
		var $track_title;							// value or NULL,
		var $track_nr;								// value or NULL,
		var $track_album;  							// value or NULL
		var $track_annotation; 						// value or NULL
		var $track_creator; 						// value or NULL
		var $track_duration;						// value or NULL
		var $track_image; 							// value or NULL
		var $track_info;							// value or NULL,
		var $track_links = array();	       			// plural element names are arrays
		var $track_locations = array();    			// plural element names are arrays
		var $track_metas= array(); 	       			// plural element names are arrays
		var $track_extensions = array(); 			// plural element names are arrays			 
		var $track_identifiers = array(); 			// plural element names are arrays
	

		/* CONSTRUCTOR */
		function XSPF_Track($params) {
			if(!is_array($params)) {
				return false;
			}
			if(!empty($params)) {
				foreach($params as $index=>$value) {
					switch ($index) {
						case "track_title":
							$this->setTrackTitle($value);
							break;
							
						case "track_nr":
							$this->setTrackNr($value);
							break;	
	
						case "track_album":
							$this->setTrackAlbum($value);
							break;	
		
						case "track_annotation":
							$this->setTrackAnnotation($value);
							break;	
		
						case "track_creator":
							$this->setTrackCreator($value);
							break;	
							
						case "track_duration":
							$this->setTrackDuration($value);
							break;	
							
						case "track_info":
							$this->setTrackInfo($value);
							break;	
							
						case "track_image":
							$this->setTrackImage($value);
							break;	
							
						case "track_location":
							$this->setTrackLocation($value);
							break;
						
						case "track_identifier":
							$this->setTrackIdentifier($value);
							break;	
							
						case "track_link":
							$this->setTrackLink($value);
							break;
							
						case "track_meta":
							$this->setTrackMeta($value);
							break;
							
						case "track_extension":
							$this->setTrackExtension($value);
							break;	
					}
				}
			}
		}
		
		/***** SETTERS *****/
		
		/* 
		* set the title of a track
		* Human-readable name of the track 
		* that authored the resource which 
		* defines the duration of track rendering.
		* 
		*/
		function setTrackTitle($title) {
			$this->track_title = $title;
		}
		
		
		/* 
		* set the track number of a track 
		* Integer with value greater than 
		* zero giving the ordinal position
		* of the media on the xspf:album.
		*
		*/
		function setTrackNr($nr) {
			if($nr < 0 || !is_numeric($nr)) {
				return false;
			}
			$this->track_nr = $nr;
		}
		
		/* 
		* set the album of a track 
		* Human-readable name of 
		* the collection from which
		* the resource which defines 
		* the duration of track rendering 
		* comes.
		*
		*/
		function setTrackAlbum($album) {
			$this->track_album = $album;
		}
		
		
		/*
		* set the annotation of a track 
		* A human-readable comment on the track.
		* This is character data, not HTML
		*/
		function setTrackAnnotation($annotation) {
			$this->track_annotation = $annotation;
		}
		
		/* 
		* set the creator of a track 
		* Human-readable name of the
		* entity (author, authors, group,
		* company, etc) that authored the
		* resource which defines the 
		* duration of track rendering.
		*/
		function setTrackCreator($creator) {
			$this->track_creator = $creator;
		}
	
		/* 
		* set the duration of a track 
		* The time to render a resource, 
		* in milliseconds. It MUST be a 
		* valid XML Schema nonNegativeInteger.
		* This value is only a hint
		*/
		function setTrackDuration($duration) {
			$this->track_duration = $duration;
		}
		
		/*
		* set the image belong to a track
		* URL of an image to display 
		* for the duration of the track	
		*/
		function setTrackImage($image) {
			$this->track_image = $image;
		}
		
		/*
		* set a url belonging to the track
		* URL of a place where this resource
		* can be bought or more info can be found.
		*/
		function setTrackInfo($info) {
			$this->track_info = $info;
		}
		
		
		/*
		* set one ore more locations for a track
		* expects an array as argument
		* URL of resource to be rendered. 
		* Probably an audio resource, but MAY be
		* any type of resource with a well-known 
		* duration, such as video, a SMIL document,
		* or an XSPF document. The duration of the
		* resource defined in this element defines 
		* the duration of rendering. xspf:track 
		* elements MAY contain zero or more location
		* elements, but a user-agent MUST NOT render
		* more than one of the named resources.
		*/
		function setTrackLocation($locations) {
			if(!is_array($locations)) {
				return false;		
			}
			foreach($locations as $key=>$value) {
				array_push($this->track_locations, $value);
			}
		}

		function setTrackIdentifier($identifiers) {
			if(!is_array($identifiers)) {
				return false;		
			}
			foreach($identifiers as $key=>$value) {
				array_push($this->track_identifiers, $value);
			}
		}

		
		function setTrackLink($links) {
			if(!is_array($links)) {
				return false;		
			}
			
			foreach($links as $key=>$value) {
				array_push($this->track_links, $value);
			}
		}

		function setTrackMeta($metas) {
			if(!is_array($metas)) {
				return false;		
			}
			
			foreach($metas as $key=>$value) {
				array_push($this->track_metas, $value);
			}
		}

		function setTrackExtension($extensions) {
			if(!is_array($extensions)) {
				return false;		
			}
			
			foreach($extensions as $key=>$value) {
				array_push($this->track_extensions, $value);
			}
		}
		
		/***** GETTERS *****/
		
		
		function getTrackTitle() {
			return $this->track_title;
		}
		
		function getTrackAlbum() {
			return $this->track_album;
		}
		
		function getTrackNr() {
			return $this->track_nr;
		}

		function getTrackAnnotation() {
			return $this->track_annotation;
		}
		
		function getTrackCreator() {
			return $this->track_creator;
		}
		
		function getTrackDuration() {
			return $this->track_duration;
		}
		
		function getTrackImage() {
			return $this->track_image;
		}
		
		function getTrackIdentifier() {
			return $this->track_identifiers;
		}
		
		
		function getTrackInfo() {
			return $this->track_info;
		}
		
		
		/* get ONE of the possible multiple locations */
		function getTrackLocation($index = 0) {
			if(is_array($this->track_locations) && sizeof($this->track_locations) > 0) {
				return $this->track_locations[$index];
			}
		}
	}





	
?>
