<?php

// +----------------------------------------------------------------------+
// | Decode and Encode data in Bittorrent format                          |
// +----------------------------------------------------------------------+
// | Copyright (C) 2004-2005 Markus Tacker <m@tacker.org>                 |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// |                                                                      |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// |                                                                      |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the                |
// | Free Software Foundation, Inc.                                       |
// | 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA               |
// +----------------------------------------------------------------------+

/**
* Encode data in Bittorrent format
*
* Based on
*   Original Python implementation by Petru Paler <petru@paler.net>
*   PHP translation by Gerard Krijgsman <webmaster@animesuki.com>
*   Gerard's regular expressions removed by Carl Ritson <critson@perlfu.co.uk>
* BEncoding is a simple, easy to implement method of associating
* data types with information in a file. The values in a torrent
* file are bEncoded.
* There are 4 different data types that can be bEncoded:
* Integers, Strings, Lists and Dictionaries.
* [http://www.monduna.com/bt/faq.html]
*
* @package File_Bittorrent2
* @category File
* @author Markus Tacker <m@tacker.org>
* @version $Id$
*/

/**
* Include required classes
*/
require_once 'PEAR.php';
require_once 'File/Bittorrent2/Exception.php';

/**
* Encode data in Bittorrent format
*
* Based on
*   Original Python implementation by Petru Paler <petru@paler.net>
*   PHP translation by Gerard Krijgsman <webmaster@animesuki.com>
*   Gerard's regular expressions removed by Carl Ritson <critson@perlfu.co.uk>
* BEncoding is a simple, easy to implement method of associating
* data types with information in a file. The values in a torrent
* file are bEncoded.
* There are 4 different data types that can be bEncoded:
* Integers, Strings, Lists and Dictionaries.
* [http://www.monduna.com/bt/faq.html]
*
* @package File_Bittorrent2
* @category File
* @author Markus Tacker <m@tacker.org>
*/
class File_Bittorrent2_Encode
{
    /**
    * Encode a var in BEncode format
    *
    * @param mixed    Variable to encode
    * @return string
    * @throws File_Bittorrent2_Exception if unsupported type should be encoded
    */
    function encode($mixed)
    {
        switch (gettype($mixed)) {
        case is_null($mixed):
            return $this->encode_string('');
        case 'string':
            return $this->encode_string($mixed);
        case 'integer':
        case 'double':
            return  $this->encode_int(round($mixed));
        case 'array':
            return $this->encode_array($mixed);
        default:
			throw new File_Bittorrent2_Exception('Unsupported type. Variable must be one of \'string\', \'integer\', \'double\' or \'array\'', File_Bittorrent2_Exception::encode);
        }
    }

    /**
    * BEncodes a string
    *
    * Strings are prefixed with their length followed by a colon.
    * For example, "Monduna" would bEncode to 7:Monduna and "BitTorrents"
    * would bEncode to 11:BitTorrents.
    *
    * @param string
    * @return string
    */
    function encode_string($str)
    {
        return strlen($str) . ':' . $str;
    }

    /**
    * BEncodes a integer
    *
    * Integers are prefixed with an i and terminated by an e. For
    * example, 123 would bEcode to i123e, -3272002 would bEncode to
    * i-3272002e.
    *
    * @param int
    * @return string
    */
    function encode_int($int)
    {
        return 'i' . $int . 'e';
    }

    /**
    * BEncodes an array
    * This code assumes arrays with purely integer indexes are lists,
    * arrays which use string indexes assumed to be dictionaries.
    *
    * Dictionaries are prefixed with a d and terminated by an e. They
    * are similar to list, except that items are in key value pairs. The
    * dictionary {"key":"value", "Monduna":"com", "bit":"Torrents", "number":7}
    * would bEncode to d3:key5:value7:Monduna3:com3:bit:8:Torrents6:numberi7ee
    *
    * Lists are prefixed with a l and terminated by an e. The list
    * should contain a series of bEncoded elements. For example, the
    * list of strings ["Monduna", "Bit", "Torrents"] would bEncode to
    * l7:Monduna3:Bit8:Torrentse. The list [1, "Monduna", 3, ["Sub", "List"]]
    * would bEncode to li1e7:Mondunai3el3:Sub4:Listee
    *
    * @param array
    * @return string
    */
    function encode_array(array $array)
    {
        // Check for strings in the keys
        $isList = true;
        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                $isList = false;
                break;
            }
        }
        if ($isList) {
            // Wie build a list
            ksort($array, SORT_NUMERIC);
            $return = 'l';
            foreach ($array as $val) {
                $return .= $this->encode($val);
            }
            $return .= 'e';
        } else {
            // We build a Dictionary
            ksort($array, SORT_STRING);
            $return = 'd';
            foreach ($array as $key => $val) {
                $return .= $this->encode(strval($key));
                $return .= $this->encode($val);
            }
            $return .= 'e';
        }
        return $return;
    }
}

?>
