<?php
/**
 *
 * Class to do regular expressions
 *
 * A number of regular expression methods that can be reused
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
 * @package   utilties
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check


/**
*
* Regular expression class
*
* @author Paul Scott <pscott@uwc.ac.za>
* @package utilties
*
*/
class regexes extends object
{
	public function init()
	{

	}

	// retrieve doctype of document
	public function get_doctype($file){
		preg_match_all('/<!DOCTYPE (\w.*)dtd">/is', $file, $tresults, PREG_PATTERN_ORDER);
		$res = $tresults[1][0];
		return $res;
	}

	// retrieve page title
	public function get_doc_title($file){
		preg_match_all('/\<title>(.*)\<\/title\>/U', $file, $tresults, PREG_PATTERN_ORDER);
		if(isset($tresults[1][0]))
		{
			$title = $tresults[1][0];
		}
		else {
			$title = "Title Missing!";
		}
		return $title;
	}

	// retrieve keywords
	public function get_keywords($file){
		$h1tags = preg_match('/(<meta name="keywords" content="(.*)" \/>)/i',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// get rel links in header of the site
	public function get_link_rel($file){
		$h1tags = preg_match_all('/(rel=)(".*") href=(".*")/im',$file,$patterns);
		$res = array();
		array_push($res,$patterns);
		array_push($res,count($patterns[2]));
		return $res;
	}

	public function get_external_css($file){
		$h1tags = preg_match_all('/(href=")(\w.*\.css)"/i',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve all h1 tags
	public function get_h1($file){
		$h1tags = preg_match_all("/(<h1.*>)(\w.*)(<\/h1>)/isxmU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve all h2 tags
	public function get_h2($file){
		$h1tags = preg_match_all("/(<h2.*>)(\w.*)(<\/h2>)/isxmU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve all h3 tags
	public function get_h3($file){
		$h1tags = preg_match_all("/(<h3.*>)(\w.*)(<\/h3>)/ismU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve all h4 tags
	public function get_h4($file){
		$h1tags = preg_match_all("/(<h4.*>)(\w.*)(<\/h4>)/ismU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve all h5 tags
	public function get_h5($file){
		$h1tags = preg_match_all("/(<h5.*>)(\w.*)(<\/h5>)/ismU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve all h5 tags
	public function get_h6($file){
		$h1tags = preg_match_all("/(<h6.*>)(\w.*)(<\/h6>)/ismU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve p tag contents
	public function get_p($file){
		$h1tags = preg_match_all("/(<p.*>)(\w.*)(<\/p>)/ismU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve names of links
	public function get_a_content($file){
		$h1count = preg_match_all("/(<a.*>)(\w.*)(<.*>)/ismU",$file,$patterns);
		return $patterns[2];
	}

	// retrieve link destinations
	public function get_a_href($file){
		$h1count = preg_match_all('/(href=")(.*?)(")/i',$file,$patterns);
		return $patterns[2];
	}

	// get count of href's
	public function get_a_href_count($file){
		$h1count = preg_match_all('/<(a.*) href=\"(.*?)\"(.*)<\/a>/',$file,$patterns);
		return count($patterns[0]);
	}

	//get all additional tags inside a link tag
	public function get_a_additionaltags($file){
		$h1count = preg_match_all('/<(a.*) href="(.*?)"(.*)>(.*)(<\/a>)/',$file,$patterns);
		return $patterns[3];
	}

	// retrieve span's
	public function get_span($file){
		$h1count = preg_match_all('/(<span .*>)(.*)(<\/span>)/',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve spans on the site
	public function get_script($file){
		$h1count = preg_match_all('/(<script.*>)(.*)(<\/script>)/imxsU',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve content of ul's
	public function get_ul($file){
		$h1count = preg_match_all('/(<ul \w*>)(.*)(<\/ul>)/ismxU',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	//retrieve li contents
	public function get_li($file){
		$h1count = preg_match_all('/(<li \w*>)(.*)(<\/li>)/ismxU',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve page comments
	public function get_comments($file){
		$h1count = preg_match_all('/(<!--).(.*)(-->)/isU',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve all used id's on the page
	public function get_ids($file){
		$h1count = preg_match_all('/(id="(\w*)")/is',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve all used classes ( inline ) of the document
	public function get_classes($file){
		$h1count = preg_match_all('/(class="(\w*)")/is',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// get the meta tag contents
	public function get_meta_content($file){
		$h1count = preg_match_all('/(<meta)(.*="(.*)").\/>/ix',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// get inline styles
	public function get_styles($file){
		$h1count = preg_match_all('/(style=")(.*?)(")/is',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// get titles of tags
	public function get_tag_titles($file){
		$h1count = preg_match_all('/(title=)"(.*)"(.*)/',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// get image alt descriptions
	public function get_image_alt($file){
		$h1count = preg_match_all('/(alt=.)([a-zA-Z0-9\s]{1,})/',$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}

	// retrieve images on the site
	public function get_images($file){
		$pattern = "/<a\s(.*)?href=(\"|')[a-z0-9\/\._-]*\.(jpe?g|png|gif)(\"|')><img\s(.*)?src=(\"|')[a-z0-9\/\._-]*\.(jpe?g|png|gif)(\"|')(\s\/)?>/i"; //'/(<img)\s (src="([a-zA-Z0-9\.;:\/\?&=_|\r|\n]{1,})")/i';
		//preg_match_all($pattern, $file, $images);
		//echo $file;
		//return $images;
		preg_match_all('/(<img)\s (src="([a-zA-Z0-9\.;:\/\?&=_|\r|\n]{1,})")/isxmU', $file, $results, PREG_PATTERN_ORDER);
		$res = $results;
		return $res;
	}

	// retrieve email address of the mailto tag if any
	public function get_mailto($file){
		$h1count = preg_match_all('/(<a\shref=")(mailto:)([a-zA-Z@0-9\.]{1,})"/ims',$file,$patterns);
		$res = array();
		array_push($res,$patterns[3]);
		array_push($res,count($patterns[3]));
		return $res;
	}

	// retrieve any email
	public function get_emails($file){
		$h1count = preg_match_all('/[a-zA-Z0-9_-]{1,}@[a-zA-Z0-9-_]{1,}\.[a-zA-Z]{1,4}/',$file,$patterns);
		$res = array();
		array_push($res,$patterns[0]);
		array_push($res,count($patterns[0]));
		return $res;
	}

	// count used keywords
	public function countkeyword($word,$file){
		$x = preg_match_all("/(.*)($word)(.*)/",$file,$patterns);
		return count($patterns);
	}

	// retrieve internal site links
	public function get_internal_links($array){
		$result = array();
		$count = count($array);
		for($i=0;$i<$count;$i++){
			if(!empty($array[$i])){
				if(strpos($array[$i],"www",0) === FALSE){
					if(strpos($array[$i],"http",0) === FALSE){
						array_push($result,$array[$i]);
					}
				}
			}
		}
		return $result;
	}

	// retrieve external links
	public function get_external_links($array){
		$result = array();
		$count = count($array);
		for($i=0;$i<$count;$i++){
			if(!empty($array[$i])){
				if(strpos($array[$i],"www",0) !== FALSE){
					if(strpos($array[$i],"http",0) !== FALSE){
						array_push($result,$array[$i]);
					}
				}
			}
		}
		return $result;
	}

	// retrieve the main url of the site
	public function get_main_url($url){
		$parts = parse_url($url);
		$url = $parts["scheme"] ."://".$parts["host"];
		return $url;
	}

	// retrieve just the name without www and com/edu etc
	public function get_domain_name_only($url){
		$match = preg_match("/(.*:\/\/)\w{0,}(.*)\.(.*)/",$url,$patterns);
		//$patterns[2] = str_replace(".","",$patterns[2]);
		return $patterns[2];
	}

}
?>