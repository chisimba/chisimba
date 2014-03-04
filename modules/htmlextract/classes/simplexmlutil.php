<?php

/**
 * Web Utility Class
 * @author Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright Copyleft 2009 Charl van Niekerk
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */
class WebUtil
{
 /**
  * Gets a list of elements that matches the given criteria.
  * @param object $root The SimpleXMLElement node to start searching.
  * @param string $name The attribute name.
  * @param string $value The attribute value.
  * @param string $separator The attribute value delimiter.
  * @return array Flat list of the matching SimpleXMLElement objects.
  */
 public static function getElementsByAttribute($root, $name, $value, $separator=null)
 {
  $matches = array();
  if ($root[$name]) {
   $attribute = trim($root[$name]);
   if ($separator !== null && strpos($attribute, $separator) !== false) {
    $values = explode($separator, $attribute);
    if (in_array($value, $values)) {
     $matches[] = $root;
    }
   } elseif ($attribute == $value) {
    $matches[] = $root;
   }
  }
  foreach ($root->children() as $child) {
   foreach (self::getElementsByAttribute($child, $name, $value, $separator) as $match) {
    $matches[] = $match;
   }
  }
  return $matches;
 }

 /**
  * Gets a list of elements that have a particular class name.
  * @param object $root The SimpleXMLElement node to start searching.
  * @param string $class The name of the class to search for.
  * @return array Flat list of the matching SimpleXMLElement objects.
  */
 public static function getElementsByClassName($root, $class)
 {
  return self::getElementsByAttribute($root, 'class', $class, ' ');
 }

 /**
  * Gets a list of elements that have a particular relation.
  * @param object $root The SimpleXMLElement node to start searching.
  * @param string $relation The name of the relation to search for.
  * @return array Flat list of the matching SimpleXMLElement objects.
  */
 public static function getElementsByRelation($root, $relation)
 {
  return self::getElementsByAttribute($root, 'rel', $relation, ' ');
 }

 /**
  * Fetches and parses an HTML document and returns the corresponding DOM.
  * @param $uri The URI of the document to fetch.
  * @return object The root element as an instance of SimpleXMLElement.
  */
 public static function getSimpleHtmlDom($uri)
 {
  return new SimpleXMLElement(new tidy($uri, array('output-xhtml'=>true), 'utf8'));
 }
}
