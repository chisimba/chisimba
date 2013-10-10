<?php
require_once('abfeedentry_class_inc.php');
class feedentryrss extends abfeedentry
{
    /**
     * Root XML element for RSS items.
     *
     * @var string
     */
    protected $_rootElement = 'item';

}
?>