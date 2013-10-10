<?php
/**
*   RSS Vocabulary
*
*   @version $Id: RSS.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Tobias Gauß (tobias.gauss@web.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of
*   RSS.
*   For details about RSS see: http://purl.org/rss/1.0/.
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/
// RSS concepts
$RSS_channel = new Resource(RSS_NS . 'channel');
$RSS_image = new Resource(RSS_NS . 'image');
$RSS_item = new Resource(RSS_NS . 'item');
$RSS_textinput = new Resource(RSS_NS . 'textinput');

$RSS_items = new Resource(RSS_NS . 'items');
$RSS_title = new Resource(RSS_NS . 'title');
$RSS_link = new Resource(RSS_NS . 'link');
$RSS_url = new Resource(RSS_NS . 'url');
$RSS_description = new Resource(RSS_NS . 'description');
$RSS_name = new Resource(RSS_NS . 'name');






?>