<?php

class xml2array
{
// {{{ toString()
/**
 * This method converts a file to a string. It returns an Error object if it is unable to open the file.
 *
 * @param    fileName String. The name of the file to convert.
 *
 * @return    String
 * @author  simgar
 */

function toString( $fileName )
{
    if ($content_array = file($fileName))
    {
        return implode("", $content_array);
    }
    else
    {
        // Error
        return false;
    }
}
// }}}

// {{{ xmlFileToArray()
/**
 * This static method converts an xml file to an associative array
 * duplicating the xml file structure.
 *
 * @param    $fileName. String. The name of the xml file to convert.
 *             This method returns an Error object if this file does not
 *             exist or is invalid.
 * @param    $includeTopTag. booleal. Whether or not the topmost xml tag
 *             should be included in the array. The default value for this is false.
 * @param    $lowerCaseTags. boolean. Whether or not tags should be
 *            set to lower case. Default value for this parameter is true.
 * @access    public static
 * @return    Associative Array
 * @author    Jason Read <jason@ace.us.com>
 */
function xmlFileToArray($fileName, $includeTopTag = false, $lowerCaseTags = true)
{
    // Definition file not found
    if (!file_exists($fileName))
    {
        // Error
        return false;
    }
    $p = xml_parser_create();
    xml_parse_into_struct($p,$this->toString($fileName),$vals,$index);
    xml_parser_free($p);
    $xml = array();
    $levels = array();
    $multipleData = array();
    $prevTag = "";
    $currTag = "";
    $topTag = false;
    foreach ($vals as $val)
    {
        // Open tag
        if ($val["type"] == "open")
        {
            if (!$this->_xmlFileToArrayOpen($topTag, $includeTopTag, $val, $lowerCaseTags,
                                           $levels, $prevTag, $multipleData, $xml))
            {
                continue;
            }
        }
        // Close tag
        else if ($val["type"] == "close")
        {
            if (!$this->_xmlFileToArrayClose($topTag, $includeTopTag, $val, $lowerCaseTags,
                                            $levels, $prevTag, $multipleData, $xml))
            {
                continue;
            }
        }
        // Data tag
        else if ($val["type"] == "complete" && isset($val["value"]))
        {
            $loc =& $xml;
            foreach ($levels as $level)
            {
                $temp =& $loc[str_replace(":arr#", "", $level)];
                $loc =& $temp;
            }
            $tag = $val["tag"];
            if ($lowerCaseTags)
            {
                $tag = strtolower($val["tag"]);
            }
            $loc[$tag] = str_replace("\\n", "\n", $val["value"]);
        }
        // Tag without data
        else if ($val["type"] == "complete")
        {
            $this->_xmlFileToArrayOpen($topTag, $includeTopTag, $val, $lowerCaseTags,
                                      $levels, $prevTag, $multipleData, $xml);
            $this->_xmlFileToArrayClose($topTag, $includeTopTag, $val, $lowerCaseTags,
                                      $levels, $prevTag, $multipleData, $xml);
        }
    }
    return $xml;
}
// }}}

// {{{ _xmlFileToArrayOpen()
/**
 * Private support function for xmlFileToArray. Handles an xml OPEN tag.
 *
 * @param    $topTag. String. xmlFileToArray topTag variable
 * @param    $includeTopTag. boolean. xmlFileToArray includeTopTag variable
 * @param    $val. String[]. xmlFileToArray val variable
 * @param    $currTag. String. xmlFileToArray currTag variable
 * @param    $lowerCaseTags. boolean. xmlFileToArray lowerCaseTags variable
 * @param    $levels. String[]. xmlFileToArray levels variable
 * @param    $prevTag. String. xmlFileToArray prevTag variable
 * @param    $multipleData. boolean. xmlFileToArray multipleData variable
 * @param    $xml. String[]. xmlFileToArray xml variable
 * @access    private static
 * @return    boolean
 * @author    Jason Read <jason@ace.us.com>
 */
function _xmlFileToArrayOpen(& $topTag, & $includeTopTag, & $val, & $lowerCaseTags,
                             & $levels, & $prevTag, & $multipleData, & $xml)
{
    // don't include top tag
    if (!$topTag && !$includeTopTag)
    {
        $topTag = $val["tag"];
        return false;
    }
    $currTag = $val["tag"];
    if ($lowerCaseTags)
    {
        $currTag = strtolower($val["tag"]);
    }
    $levels[] = $currTag;

    // Multiple items w/ same name. Convert to array.
    if ($prevTag === $currTag)
    {
        if (!array_key_exists($currTag, $multipleData) ||
            !$multipleData[$currTag]["multiple"])
        {
            $loc =& $xml;
            foreach ($levels as $level)
            {
                $temp =& $loc[$level];
                $loc =& $temp;
            }
            $loc = array($loc);
            $multipleData[$currTag]["multiple"] = true;
            $multipleData[$currTag]["multiple_count"] = 0;
        }
        $multipleData[$currTag]["popped"] = false;
        $levels[] = ":arr#" . ++$multipleData[$currTag]["multiple_count"];
    }
    else
    {
        $multipleData[$currTag]["multiple"] = false;
    }

    // Add attributes array
    if (array_key_exists("attributes", $val))
    {
        $loc =& $xml;
        foreach ($levels as $level)
        {
            $temp =& $loc[str_replace(":arr#", "", $level)];
            $loc =& $temp;
        }
        $keys = array_keys($val["attributes"]);
        foreach ($keys as $key)
        {
            $tag = $key;
            if ($lowerCaseTags)
            {
                $tag = strtolower($tag);
            }
            $loc["attributes"][$tag] = & $val["attributes"][$key];
        }
    }
    return true;
}
// }}}

// {{{ _xmlFileToArrayClose()
/**
 * Private support function for xmlFileToArray. Handles an xml OPEN tag.
 *
 * @param    $topTag. String. xmlFileToArray topTag variable
 * @param    $includeTopTag. boolean. xmlFileToArray includeTopTag variable
 * @param    $val. String[]. xmlFileToArray val variable
 * @param    $currTag. String. xmlFileToArray currTag variable
 * @param    $lowerCaseTags. boolean. xmlFileToArray lowerCaseTags variable
 * @param    $levels. String[]. xmlFileToArray levels variable
 * @param    $prevTag. String. xmlFileToArray prevTag variable
 * @param    $multipleData. boolean. xmlFileToArray multipleData variable
 * @param    $xml. String[]. xmlFileToArray xml variable
 * @access    private static
 * @return    boolean
 * @author    Jason Read <jason@ace.us.com>
 */
function _xmlFileToArrayClose(& $topTag, & $includeTopTag, & $val, & $lowerCaseTags,
                              & $levels, & $prevTag, & $multipleData, & $xml)
{
    // don't include top tag
    if ($topTag && !$includeTopTag && $val["tag"] == $topTag)
    {
        return false;
    }
    if ($multipleData[$currTag]["multiple"])
    {
        $tkeys = array_reverse(array_keys($multipleData));
        foreach ($tkeys as $tkey)
        {
            if ($multipleData[$tkey]["multiple"] && !$multipleData[$tkey]["popped"])
            {
                array_pop($levels);
                $multipleData[$tkey]["popped"] = true;
                break;
            }
            else if (!$multipleData[$tkey]["multiple"])
            {
                break;
            }
        }
    }
    $prevTag = array_pop($levels);
    if (strpos($prevTag, "arr#"))
    {
        $prevTag = array_pop($levels);
    }
    return true;
}
// }}}
}//end class
?>