<?php
/**
 * Class xbel to cater for the XML Bookmark Exchange Language, some of the ideas were borrowed
 * from http://carthik.net/wpplugins/xbel.phps
 * @author James Kariuki Njenga
 * @version $Id: xbookmark_class_inc.php 6229 2007-04-26 07:13:17Z megan $
 * @copyright 2005, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package kbookmark
 * 
*/

class xbookmark extends object
{
	/**
    *  Public output containers.
    */
    
	/**
	* @val newFolder, array
    */
	var $newFolder=array();

    /**
	* @val newBookmark, array
    */
	var $newBookmark=array();

	/**
	* @val error string
    */
    var $Error;
    
    /**
	* @val bookmarks, array
    */
	var $bookmarks=array();
	
	/**
	* @val folders, array
    */
	var $folders=array();


	/**
	* Private properties
    */ 
    
	/**
	* @val xmlParser string
    */
    var $xmlParser;

    /**
	* @val xbelFile string
    */
    var $xbelFile;

	/**
	* @val currentElement string
    */
    var $currentElement;

    /**
	* @val parentElement string
    */
	var $parentElement;

    /**
	* @val parseLevel int
    */
	var $parseLevel= 1;
	
	/**
	* @val folderCount int
    */
	var $folderCount = 0;

    /**
	* @val bkCount int
    */
	var $bkCount=0;

    /**
	* @val fCount string
    */
	var $fCount=0;
	
	/**
	* @val folder string
    */
	var $folder;
	
	/**
	* @val bookmark string
    */
	var $bookmark;
	
	/**
	* @val xbelOutput string
    */
	var $xbelOutput;
	
   /**
    * @var object $objUser
    */
    var $objUser;

    /**
    * @var object $objLanguage
    */
    var $objLanguage;


    /**
    * @var object $objDbBookmark
    */
    var $objDbBookmark;

    /**
    * @var object $objDbGroup
    */
    var $objDbGroup;

    /**
    * @var object $objLink: Use to create links
    */
    var $objLink;

    /**
    * @var string $extention: checking the file extenstion
    */
    var $fileExtension;
    
    /**
    * @var string $extention: checking the file extenstion
    */
    var $fileName;
    /**
    * @var string $fileName
    */
    var $objIcon;

    function init()
    {
        $this->objLanguage=& $this->getObject('language', 'language');
        $this->objIcon=& $this->getObject('geticon','htmlelements');
        $this->objUser= & $this->getObject('user','security');
        $this->objLink=&$this->newObject('link','htmlelements');
        $this->objDbBookmark=& $this->newObject('dbbookmark', 'kbookmark');
        $this->objDbGroup=& $this->newObject('dbgroup','kbookmark');
        $this->objLanguage=& $this->getObject('language', 'language');
    }


    /**
	* Method to return the extension of the file
	* @return string
	*/
	
    function getFileExtension($filename){
		$this->fileExtension=strtolower(strrchr(trim($filename), '.' ));
		return $this->fileExtension;
    }
    
    /**
    * Method to check if a file is of allowed format
    * @param $filename string
    *
    * @return bool
    */
    function isAllowedFile($filename)
    {
        //Check extension
        if ($this->getFileExtension($filename)=='.xml') {
            return True;
        } else {
            return False;
        }
    }
    
    
	function startElement($parser, $name, $attrs = array())
    {
        $this->parentElement = $this->currentElement;
		$this->currentElement = $name;
		switch($name) {
		case "XBEL":
			break;
		case "TITLE":
		  if ("BOOKMARK" != $this->parentElement) {
			}
			break;
		case "DESC":
		  if ("BOOKMARK" != $this->parentElement) {
			}
			break;
		case "INFO":
			break;
		case "METADATA":
			break;
		case "FOLDER":
		    $this->parseLevel++;
			$this->folderCount++;
			break;
		case "SEPERATOR":
		    break;
		case "BOOKMARK":
		    $this->newBookmark['url']=$attrs['HREF'];
		    if (isset($attrs['VISITED'])) {
                $this->newBookmark['visted']=$attrs['VISITED'];
            } else {
                 $this->newBookmark['visted']=mktime();
            }
			break;
		case "ALIAS":
		  break;
		}
	}

    function characterData($parser, $data){
		switch($this->currentElement){
			case "XBEL":
			 	break;
			case "TITLE":
			    if ("BOOKMARK" != $this->parentElement) {
			        $this->newFolder['title']=trim($data);
                    $this->folder=trim($data);
				} else {
			        $this->newBookmark['title']=trim($data);
			        $this->bookmark=trim($data);
	            }
			  break;
			case "DESC":
			  if ($this->parentElement=="BOOKMARK") {
			     if (count(trim($data,"\t\n\0\x0B"))<2) {
                     $this->newBookmark['description']=$this->bookmark;
			    } else {
                      $this->newBookmark['description']=trim($data);
                }
                if (count($this->newBookmark)>1) {
                $this->newBookmark['folder']=$this->folder;
                $this->bookmarks[$this->bkCount]=$this->newBookmark;
	            $this->bkCount++;
	            $this->newBookmark='';
                }

			  } else {
			      if (count(trim($data,"\t\n\0\x0B"))<2) {
                      $this->newFolder['description'] = $this->folder;
			       } else {
			          $this->newFolder['description']=trim($data);
			       }
			        if (isset($this->newFolder['title'])) {
                        if ($this->newFolder['title']!="") {
			                $this->folders[$this->fCount]=$this->newFolder;
			                $this->fCount++;
			                $this->newFolder="";
			            }
			       }
              }
				break;
			case "INFO":
			  break;
			case "METADATA":
			  break;
			case "FOLDER":
			  break;
			case "SEPERATOR":
			  break;
			case "BOOKMARK":
			  break;
			case "ALIAS":
			  break;
		}
	}

    function endElement($parser, $name)
    {
	      switch($name){
		  case "ALIAS":
			  break;
		  case "BOOKMARK":
			  break;
		  case "SEPERATOR":
			  break;
		  case "FOLDER":
		      $this->parseLevel--;
			  break;
		  case "METADATA":
			  break;
			case "INFO":
			  break;
		  case "DESC":
			  if ("BOOKMARK" != $this->parentElement) {
			  }
			  break;
		  case "TITLE":
				if ("bookmark" != $this->parentElement) {
			    }
				break;
		  case "XBEL":
				break;
	  }
		$this->currentElement = $this->parentElement;
  }
  
  	function xbelbookmark($filename=null)
    {
        $this->fileName=$filename;
	    if($this->xbelFile = @file($this->fileName, 1)){
            $this->xmlParser = xml_parser_create();
			xml_set_object($this->xmlParser, $this);
			xml_parser_set_option($this->xmlParser, XML_OPTION_CASE_FOLDING, true);
			xml_parser_set_option($this->xmlParser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
			xml_set_element_handler($this->xmlParser, "startElement", "endElement");
			xml_set_character_data_handler($this->xmlParser, "characterData");
			return $this->parse();
		} else {
		$this->Error = $this->objLanguage->languageText('mod_bookmark_filemissing');//'Could not fetch file';
			return false;
		}
	}

	function parse()
    {
		if(!count($this->xbelFile)){
			$this->Error =$this->objLanguage->languageText('mod_bookmark_fileempty');// "Empty or missing data.";
			return false;
		} else {
			while(list($line_num, $line) = each($this->xbelFile)) {
				if(!xml_parse($this->xmlParser, ereg_replace('&', '&amp;', $line))) {
					$this->Error = "on line:" . $line_num ." ". xml_error_string(xml_get_error_code($this->xmlParser));
					return false;
				}
			}
			xml_parser_free($this->xmlParser);
			unset($this->xbelFile);
		}
		return true;
	}


    /**
    * Method to create a simple xbel output.
    * @return string
    */
    function xbel()
    {
        $this->xbelOutput ="<?xml version=\"1.0\" ?>\n";
        $this->xbelOutput .="<!DOCTYPE xbel PUBLIC \"+//IDN python.org//DTD XML Bookmark Exchange Language 1.0//EN//XML\" \"http://www.python.org/topics/xml/dtds/xbel-1.0.dtd\">\n";
        $this->xbelOutput .="<xbel>\n";
        $this->xbelOutput .="\t<info>\n";
        $this->xbelOutput .="\t\t<metadata owner =\"my name\" />\n";
        $this->xbelOutput .="\t</info>\n";
        $userId=$this->objUser->userId();
        $filter="where creatorid='$userId'";

        $list=$this->objDbGroup->getAll($filter);
        foreach ($list as $line) {
            $id=$line['id'];
            $title=$line['title'];
            $desc=$line['description'];
            $this->xbelOutput .=$this->folder2xbel($id,$title,$desc);
        }
        $this->xbelOutput .="</xbel>\n";
//	echo "asdf";
	
$xbelOutput = trim($this->xbelOutput);
//echo trim($xbelOutput," ");

        return trim($this->xbelOutput," ");
    }


    /**
    * Method to convert a bookmark to xbel xml format.
    *
    * @var bookmarkId String
    */
    function bookmark2xbel($url, $dateVisted, $datecreated, $datemodified, $title, $description)
    {
        $this->xbelOutput.="\t\t<bookmark href =\"".$url."\" ";
        $this->xbelOutput.="visited =\"". $dateVisted."\" ";
        $this->xbelOutput.="added =\"". $datecreated."\" ";
        $this->xbelOutput.="modified =\"". $datemodified."\" ";
        $this->xbelOutput.=" > \n";
        $this->xbelOutput.="\t\t\t<title>".$title."</title>\n";
        $this->xbelOutput.="\t\t\t<desc>".$description."</desc>\n";
        $this->xbelOutput.="\t\t</bookmark>\n";
        //$this->xbelOutput;
    }

    /**
    * Method to convert a folder to xbel xml format.
    *
    * @var bookmarkId String
    */

    function folder2xbel ($id,$title,$description)
    {
        $this->xbelOutput .="\t<folder>\n";
        $this->xbelOutput .="\t\t<title>".$title."</title>\n";
        $this->xbelOutput .="\t\t<desc>".$description."</desc>\n";
        $filter="Where groupId='$id'";
        $list=$this->objDbBookmark->getAll($filter);
        foreach ($list as $line) {
            $url=$line['url'];
            $dateVisted=$line['datelastaccessed'];
            $datecreated=$line['datecreated'];
            $datemodified=$line['datemodified'];
            $title=$line['title'];
            $description=$line['description'];
            $this->xbelOutput .=$this->bookmark2xbel($url, $dateVisted, $datecreated, $datemodified, $title, $description);
        }
        $this->xbelOutput .="\t</folder>\n";
        //return $this->xbelOutput;
    }
    
    
    /**
    * Method to add bookmarks generated from xbel to the database
    *
    *@var xfolders array containing the foldrs to be added
    *
    */
    function insertXbelFolders($xfolders)
    {
         foreach ($xfolders as $line) {
             $title=$line['title'];
             $description=$line['description'];
             $isprivate = "1";
             $datecreated=$this->now();
             $isdefault='0';
             $creatorid=$this->objUser->userId();
             //insert into db
             $this->objDbGroup->insertSingle($title,$description,
             $isprivate,$datecreated,$isdefault,$creatorid);
        }
     
    
    }
    
    /**
    * Method to add bookmarks generated from xbel to the database
    *
    *@var bookmaks array containing the bookmarks to be added
    *
    */
    function insertXbelBookmarks($bookmarks)
    {
        foreach ($bookmarks as $line) {
            $title=$line['title'];
            $description=$line['description'];
            $isprivate = "1";
            $datecreated=$this->now();
            $isdefault='0';
            $creatorid=$this->objUser->userId();
            $groupid=$this->objDbGroup->folderById($line['folder']);;
            $url=$line['url'];
            $visitcount='0';
            $isdeleted='0';
            //insert into db
            $this->objDbBookmark->insertSingle($groupid,$title, $url,
            $description, $datecreated, $isprivate,
            $creatorid, $visitcount);
        }
    }
    
    /**
    * Method to insert the bookmarks generated from xbel into the database
    */
    function xbelInsert()
    {   //get the bookmarks and foldrs
        $xbookmarks=$this->bookmarks;
        $xfolders=$this->folders;
        //call methods to prepare them for inputs into the database
        $this->insertXbelFolders($xfolders);
        $this->insertXbelBookmarks($xbookmarks);
    }
}; //end class
?>