<?php
/**
 *
 * Provides a means to include Disqus comments in a page or section of a page.
 *
  * Provides a means to include Disqus comments in a page or section of a
 * page, such as a blog or forum post. Disqus is blog comment Web service.
 * Webmasters employ Disqus' service to add Web 2.0-style interactive blog
 * discussion. The service is delivered via a hosted, Software as a Service
 * (SaaS) model. Disqus is installed into a blog via a javascript Web widget.
 * Website visitors can leave comments on a blog and save those comments on
 * the Disqus.com website. See disqus.com for more.
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
 * @package   disqus
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbdisqus.php,v 1.2 2008-01-08 13:07:15 dkeats Exp $
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
* Database accesss class for Chisimba for the module disqus
*
* @author Derek Keats
* @package disqus
*
*/
class disquselems extends dbtable
{
    /**
    *
    * Constructor for the disquselems class
    * @access public
    *
    */
    public function init()
    {
        //
    }

    /**
    *
    * Get the div tag that identifies where the discussion must be inserted.
    * It is a md5 hash of the permalink
    *
    * @param string $disqusDiv the md5 hashed id for the div.
    * @access public
    */
    public function getDiv($disqusDiv)
    {
        return "<div id=\"$disqusDiv\">&nbsp;</div>\n";
    }

    /**
    *
    * Method to get the inline Javascript that identifies the correct
    * discussion to display
    * @param string $url The permalink that defines the discussion
    * @param string $disqusDIv The name of the layer that displays the discussion
    * @return string The formatted script tags.
    * @access public
    *
    */
    public function getInlineJs($url, $disqusDiv)
    {
        return "<script type=\"text/javascript\">\n"
          . "var disqus_url='$url';\n"
          . "var disqus_container_id = \"$disqusDiv\";\n"
          . "</script>\n";
    }

    /**
    *
    * Get the script that does the embedding of the discussion into
    * the page
    * @return string The formatted Script and noscript code
    * @access public
    */
    public function getEmbedJs($disqusUser)
    {
        $ret = "<script type=\"text/javascript\" "
          . "src=\"http://disqus.com/forums/$disqusUser/embed.js\">\n"
          . "</script><noscript>\n"
          . "<a href=\"http://$disqusUser.disqus.com/?url=ref\">"
          . "View the forum thread.</a></noscript>";
        return $ret;
    }

    /**
    *
    * Add the widget to the page. This is used in a page template,
    * just before the closing BODY tag (&lt;/body&gt;) insert
    *    $objDq = $this->getObject('disquselems', 'disqus');
    *    echo $objDq->addWidget;
    *
    * @return VOID
    * @access public
    *
    */
    public function addWidget()
    {

       $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
       $developermode=$objSysConfig->getValue('mod_disqus_developermode', 'disqus');
       $user=$objSysConfig->getValue('mod_disqus_defaultuser', 'disqus');

       $div='<div id="disqus_thread"></div><script type="text/javascript" src="http://disqus.com/forums/'.$user.'/embed.js"></script><noscript><a href="http://witsfossad.disqus.com/?url=ref">View the discussion thread.</a></noscript><a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>';


$ret = "<script type=\"text/javascript\">
var disqus_developer=".$developermode.";
//<![CDATA[
(function() {
	var links = document.getElementsByTagName('a');
	var query = '?';
	for(var i = 0; i < links.length; i++) {
	if(links[i].href.indexOf('#disqus_thread') >= 0) {
		query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
	}
	}
	document.write('<script charset=\"utf-8\" type=\"text/javascript\" src=\"http://disqus.com/forums/".$user."/get_num_replies.js' + query + '\"></' + 'script>');
})();
//]]>
</script>";
        return $ret.$div;
    }

}
?>
