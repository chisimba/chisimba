<?php
// $Id$

require_once 'HTML/TagCloud.php';

// {{{ class MyTags extends HTML_TagCloud{
/**
 *
 * OverRide protected class vars
 *
 * @author     Shoma Suzuki <shoma@catbot.net> 
 * @version    $Id$
 */
class MyTags extends HTML_TagCloud{
    protected $epoc_level = array(
        array(
            'earliest' => array(
                'link'    => 'ffdfdf',
                'visited' => 'ffdfdf',
                'hover'   => 'ffdfdf',
                'active'  => 'ffdfdf',
            ),
        ),
        array(
            'earlier' => array(
                'link'    => 'ff7f7f',
                'visited' => 'ff7f7f',
                'hover'   => 'ff7f7f',
                'active'  => 'ff7f7f',
            ), 
        ),
        array(
            'previous' => array(
                'link'    => 'ff7f7f',
                'visited' => 'ff7f7f',
                'hover'   => 'ff7f7f',
                'active'  => 'ff7f7f',
            ), 
        ),
        array(
            'recent' => array(
                'link'    => 'ff4f4f',
                'visited' => 'ff4f4f',
                'hover'   => 'ff4f4f',
                'active'  => 'ff4f4f',
            ), 
        ),
        array(
            'later' => array(
                'link'    => 'ff1f1f',
                'visited' => 'ff1f1f',
                'hover'   => 'ff1f1f',
                'active'  => 'ff1f1f',
            ),
        ),
        array(
            'latest' => array(
                'link'    => 'ff0000',
                'visited' => 'ff0000',
                'hover'   => 'ff0000',
                'active'  => 'ff0000',
            ),
        ),
    );
    protected $size_suffix = 'pt';
    protected $fontsizerange = 0;
    protected $basefontsize = 12;
}
// }}}

date_default_timezone_set('Asia/Tokyo');
$tags = new MyTags();

// below same as example2
// add Element
$tags->addElement('PHP','http://www.php.net', 39, strtotime('-1 day'));
$tags->addElement('XML','http://www.xml.org', 21, strtotime('-2 week'));
$tags->addElement('Perl','http://www.xml.org', 15, strtotime('-1 month'));
$tags->addElement('PEAR','http://pear.php.net', 32, time());
$tags->addElement('MySQL','http://www.mysql.com', 10, strtotime('-2 day'));
$tags->addElement('PostgreSQL','http://pgsql.com', 6, strtotime('-3 week'));
// output HTML and CSS
print $tags->buildALL();
show_source(__FILE__);
?>
