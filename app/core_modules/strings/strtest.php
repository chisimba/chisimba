<?php
/**
 * @author Derek Keats
 *
 * @version $Id$
 * @copyright 2003 Gnu GPL
 **/
 
 
 
/**
* Checks if a URL is validly formed based on the code in
* PHP Classes by Lennart Groetzbach <lennartg_at_web_dot_de>
* Adapted to KINKY by Derek
* 
* @param String $str The Url to validate
* @param boolean $strict Enforce strict checking?
* @return boolean TRUE|FALSE
*/
function isValidFormedUrl($str,$strict=false)
{
    $test="";
	if ($strict == true) {
		$test .= "/^http:\\/\\/([A-Za-z-\\.]*)\\//";
	} else {
		$test .= "/^http:\\/\\/([A-Za-z-\\.]*)/";
	}
	return @preg_match($test, $str);
}


function tagExtLinks($str, $repl="^") 
{
    $test="/(<a)(\s+)(href=\")(http|ftp):\/\/.*<\/a>/isU";
    return preg_replace($test, " \${0}".$repl, $str);
}

function activateLinks($str) 
{
  //Exclude matched inside anchor tags
  $not_anchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)'; 
  //Match he protocol with ://, e.g. http://
  $protocol = '(http|ftp|https):\/\/';
  $domain = '[\w]+(.[\w]+)';
  $subdir = '([\w\-\.;,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
  $test = '/' . $not_anchor . $protocol . $domain . $subdir . '/i';
  //Match and replace where there is a protocol and no anchor
  $ret = preg_replace( $test, "<a href='$0' title='$0'>$0</a>", $str );
  //  Now match things beginning with www.
  $not_anchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)';
  $not_http = '(?<!:\/\/)';
  $domain = 'www(.[\w]+)';
  $subdir = '([\w\-\.;,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
  $test = '/' . $not_anchor . $not_http . $domain . $subdir . '/i';
  $ret=preg_replace( $test, "<a href='http://$0' title='http://$0'>$0</a>", $ret );
  
  return $ret;
   
}


function tagExtLinks2($str, $activ=null, $repl="(*)")
{
    if ($activ==null) {
        $test = "/(<a href=)" // 1: Match the opening of the anchor tag
          ."(\"|\')" // 2: Match the first single or double quotes
          ."(http|https|ftp)" // 3: match the protocol identifier (Http, ftp, https)
          ."(:\/\/)" //4: Match the :// part of the URL
          ."(?!" . $_SERVER['HTTP_HOST'] . ")"  // 5: Do not match if it is the local server
          ."(.*)" // 6: Match any number of following characters up to the closing anchor tag
          ."(<\/a>)/isU"; // 7: Match the closing anchor tag
        return preg_replace($test, " \${0}" . $repl, $str);
    } else {
        $test = "/(<a href=)" // 1: Match the opening of the anchor tag
          ."(\"|\')" // 2: Match the first single or double quotes
          ."(http|https|ftp)" // 3: match the protocol identifier (Http, ftp, https)
          ."(:\/\/)" //4: Match the :// part of the URL
          ."(?!" . $_SERVER['HTTP_HOST'] . ")" // 5: Match domain but exclude the local server
          ."(.*)" // 6: Match any characters after the server
          ."(\"|\')" //7: Match a closing single or double quote (helps pull out the URL)
          ."(.*)" // 8: Match any further characters
          ."<\/a>/isU"; // 9: Match the closing anchor
        //return preg_replace($test, " \${0} xxxxxxxx" . $repl."</a>", $str);
        return preg_replace($test, " \${0}<a href=\"\${3}\${4}\${5}\" target=\"_BLANK\">" . $repl . "</a>", $str);
    }
}

function removeLinks($str)
{
        $test="/(<a\s+href=)"       //1: Match the start of the anchor tag followed by 
                                    //   any number of spaces followed by href followed
          ."([\"\'])"              //2: Match either of " or ' and remember it as \2 for a backreference
          . "(.*[\"'])"            //3: Match any characters up to the next " or ' 
          . "(.*>)"                //4: Anything else followed by the closing >
          . "(.+)"                 //5: Match any string of 1 or more characters
          . "(<\/a>)"               //6: Match the closing </a> tag
          . "/isU";                //Make it case insensitive (i), treat across newlines (s) 
                                    //and make it Ungreedy (U)
      return preg_replace($test, "\$5", $str); 
      
} 

//if ($matches[1][0] == "http://localhost" || "localhost" || "http://127.0.0.1") { do something } if ($matches[1][0] !== "http://localhost" || "localhost" || "http://127.0.0.1") { Do something else }

$str="Hello there everyone. I hope you are having a good day. 
 Please come to http://dog.cat/ for more information or visit
 <a href=\"http://www.google.com\">google</a> now which you ca find
 at www.google.com. On the other
 hand, <a href=\"http://sdasdas/fish.html\" class=\"someclass\">asdas</a>.
 This is test of <a href=\"http://test/\">this thing</a> but it does not 
 tag <a href=\"http://localhost/something.php\">localhost</a>.";

echo activateLinks($str)."<br /><br />";
echo tagExtLinks2(activateLinks($str), True)."<br /><br />";
echo removeLinks(tagExtLinks2(activateLinks($str), True))."<br /><br />";


//echo "<br/>http://www.google.com " . isValidFormedUrl("http://www.google.com")."<br />";
//echo "<br/>htp://www.google.com " . isValidFormedUrl("htp://www.google.com")."<br />";

?>