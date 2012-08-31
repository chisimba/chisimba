<?php

// +----------------------------------------------------------------------+
// | Decode and Encode data in Bittorrent format                          |
// +----------------------------------------------------------------------+
// | Copyright (C) 2004-2006 Markus Tacker <m@tacker.org>                 |
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
* Info on the .torrent file format
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
* @author Robin H. Johnson <robbat2@gentoo.org>
* @version $Id$
*/

/**
* Include required classes
*/
require_once 'PEAR.php';
require_once 'File/Bittorrent2/Encode.php';
require_once 'File/Bittorrent2/Exception.php';

/**
* Encode data in Bittorrent format
*
* Based on
*   Original Python implementation by Petru Paler <petru@paler.net>
*   PHP translation by Gerard Krijgsman <webmaster@animesuki.com>
*   Gerard's regular expressions removed by Carl Ritson <critson@perlfu.co.uk>
* Info on the .torrent file format
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
* @author Robin H. Johnson <robbat2@gentoo.org>
*/
class File_Bittorrent2_Decode
{
    /**
    * @var string   Name of the torrent
    */
    protected $name = '';

    /**
    * @var string   Filename of the torrent
    */
    protected $filename = '';

    /**
    * @var string   Comment
    */
    protected $comment = '';

    /**
    * @var int   Creation date as unix timestamp
    */
    protected $date = 0;

    /**
    * @var array    Files in the torrent
    */
    protected $files = array();

    /**
    * @var int      Size of of the full torrent (after download)
    */
    protected $size = 0;

    /**
    * @var string   Signature of the software which created the torrent
    */
    protected $created_by = '';

    /**
    * @var string    tracker (the tracker the torrent has been received from)
    */
    protected $announce = '';

    /**
    * @var array     List of known trackers for the torrent
    */
    protected $announce_list = array();

    /**
    * @var string   Source string
    */
    protected $source = '';

    /**
    * @var int      Source length
    */
    protected $source_length = 0;

    /**
    * @var int      Current position of the string
    */
    protected $position = 0;

    /**
    * @var string   Info hash
    */
    protected $info_hash;

    /**
    * @var array    Decoded data from File_Bittorrent2_Decode::decodeFile()
    */
    protected $decoded = array();

    /**
    * Decode a Bencoded string
    *
    * @param string
    * @return mixed
    * @throws File_Bittorrent2_Exception if decoded data contains trailing garbage
    */
    function decode($str)
    {
        $this->source = $str;
        $this->position  = 0;
        $this->source_length = strlen($this->source);
        $result = $this->bdecode();
        if ($this->position < $this->source_length) {
			throw new File_Bittorrent2_Exception('Trailing garbage in file.', File_Bittorrent2_Exception::decode);
        }
        return $result;
    }

    /**
    * Decode .torrent file and accumulate information
    *
    * @param string    Filename
    * @return mixed    Returns an arrayon success or false on error
    * @throws File_Bittorrent2_Exception if no file given or bencoded data is corrupt
    */
    function decodeFile($file)
    {
        // Check file
        if (!is_file($file)) {
            throw new File_Bittorrent2_Exception('Given filename \'' . $file . '\' is not a valid file.', File_Bittorrent2_Exception::source);
        }

        // Reset public attributes
        $this->name          = '';
        $this->filename      = '';
        $this->comment       = '';
        $this->date          = 0;
        $this->files         = array();
        $this->size          = 0;
        $this->created_by    = '';
        $this->announce      = '';
        $this->announce_list = array();
        $this->position     = 0;
        $this->info_hash     = '';

        // Decode .torrent
        $this->source = file_get_contents($file);
        $this->source_length = strlen($this->source);
        $this->decoded = $this->bdecode();
        if (!is_array($this->decoded)) {
			throw new File_Bittorrent2_Exception('Corrupted bencoded data. Failed to decode data from file \'$file\'.', File_Bittorrent2_Exception::decode);
        }

        // Compute info_hash
        $Encoder = new File_Bittorrent2_Encode;
        $this->info_hash = sha1($Encoder->encode($this->decoded['info']));

        // Pull information form decoded data
        $this->filename = basename($file);
        // Name of the torrent - statet by the torrent's author
        $this->name     = $this->decoded['info']['name'];
        // Authors may add comments to a torrent
        if (isset($this->decoded['comment'])) {
            $this->comment = $this->decoded['comment'];
        }
        // Creation date of the torrent as unix timestamp
        if (isset($this->decoded['creation date'])) {
            $this->date = $this->decoded['creation date'];
        }
        // This contains the signature of the application used to create the torrent
        if (isset($this->decoded['created by'])) {
            $this->created_by = $this->decoded['created by'];
        }
        // Get the directory separator
        $sep = (PHP_OS == 'Linux') ? '/' : '\\';
        // There is sometimes an array listing all files
        // in the torrent with their individual filesize
        if (isset($this->decoded['info']['files']) and is_array($this->decoded['info']['files'])) {
            foreach ($this->decoded['info']['files'] as $file) {
                $path = join($sep, $file['path']);
                // We are computing the total size of the download heres
                $this->size += $file['length'];
                $this->files[] = array(
                    'filename' => $path,
                    'size'     => $file['length'],
                );
            }
        // In case the torrent contains only on file
        } elseif (isset($this->decoded['info']['name']))  {
                $this->files[] = array(
                   'filename' => $this->decoded['info']['name'],
                   'size'     => $this->decoded['info']['length'],
                );
        }
        // If the the info->length field is present we are dealing with
        // a single file torrent.
        if (isset($this->decoded['info']['length']) and $this->size == 0) {
            $this->size = $this->decoded['info']['length'];
        }

        // This contains the tracker the torrent has been received from
        if (isset($this->decoded['announce'])) {
            $this->announce = $this->decoded['announce'];
        }

        // This contains a list of all known trackers for this torrent
        if (isset($this->decoded['announce-list']) and is_array($this->decoded['announce-list'])) {
            $this->announce_list = $this->decoded['announce-list'];
        }

        // Currently, I'm not sure how to determine an error
        // Just try to fetch the info from the decoded data
        // and return it
        return array(
            'name'          => $this->name,
            'filename'      => $this->filename,
            'comment'       => $this->comment,
            'date'          => $this->date,
            'created_by'    => $this->created_by,
            'files'         => $this->files,
            'size'          => $this->size,
            'announce'      => $this->announce,
            'announce_list' => $this->announce_list,
            'info_hash'     => $this->info_hash,
        );
    }

    /**
    * Decode a BEncoded String
    *
    * @return mixed    Returns the representation of the data in the BEncoded string or false on error
    */
    protected function bdecode()
    {
        switch ($this->getChar()) {
        case 'i':
            $this->position++;
            return $this->decode_int();
            break;
        case 'l':
            $this->position++;
            return $this->decode_list();
            break;
        case 'd':
            $this->position++;
            return $this->decode_dict();
            break;
        default:
            return $this->decode_string();
        }
    }

    /**
    * Decode a BEncoded dictionary
    *
    * Dictionaries are prefixed with a d and terminated by an e. They
    * are similar to list, except that items are in key value pairs. The
    * dictionary {"key":"value", "Monduna":"com", "bit":"Torrents", "number":7}
    * would bEncode to d3:key5:value7:Monduna3:com3:bit:8:Torrents6:numberi7ee
    *
    * @return array
    * @throws File_Bittorrent2_Exception if bencoded dictionary contains invalid data
    */
    protected function decode_dict()
    {
        $return = array();
        $ended = false;
        $lastkey = NULL;
        while ($char = $this->getChar()) {
            if ($char == 'e') {
                $ended = true;
                break;
            }
            if (!ctype_digit($char)) {
				throw new File_Bittorrent2_Exception('Invalid dictionary key.', File_Bittorrent2_Exception::decode);
            }
            $key = $this->decode_string();
            if (isset($return[$key])) {
                throw new File_Bittorrent2_Exception('Duplicate dictionary key.', File_Bittorrent2_Exception::decode);
            }
            if ($key < $lastkey) {
                throw new File_Bittorrent2_Exception('Missorted dictionary key.', File_Bittorrent2_Exception::decode);
            }
            $val = $this->bdecode();
            if ($val === false) {
                throw new File_Bittorrent2_Exception('Invalid value.', File_Bittorrent2_Exception::decode);
            }
            $return[$key] = $val;
            $lastkey = $key;
        }
        if (!$ended) {
            throw new File_Bittorrent2_Exception('Unterminated dictionary.', File_Bittorrent2_Exception::decode);
        }
        $this->position++;
        return $return;
    }

    /**
    * Decode a BEncoded string
    *
    * Strings are prefixed with their length followed by a colon.
    * For example, "Monduna" would bEncode to 7:Monduna and "BitTorrents"
    * would bEncode to 11:BitTorrents.
    *
    * @return string|false
    * @throws File_Bittorrent2_Exception if bencoded data is invalid
    */
    protected function decode_string()
    {
        // Check for bad leading zero
        if (substr($this->source, $this->position, 1) == '0' and
        substr($this->source, $this->position + 1, 1) != ':') {
			throw new File_Bittorrent2_Exception('Leading zero in string length.', File_Bittorrent2_Exception::decode);
        }
        // Find position of colon
        // Supress error message if colon is not found which may be caused by a corrupted or wrong encoded string
        if (!$pos_colon = @strpos($this->source, ':', $this->position)) {
            throw new File_Bittorrent2_Exception('Colon not found.', File_Bittorrent2_Exception::decode);
        }
        // Get length of string
        $str_length = intval(substr($this->source, $this->position, $pos_colon));
        if ($str_length + $pos_colon + 1 > $this->source_length) {
            throw new File_Bittorrent2_Exception('Input too short for string length.', File_Bittorrent2_Exception::decode);
        }
        // Get string
        if ($str_length === 0) {
            $return = '';
        } else {
            $return = substr($this->source, $pos_colon + 1, $str_length);
        }
        // Move Pointer after string
        $this->position = $pos_colon + $str_length + 1;
        return $return;
    }

    /**
    * Decode a BEncoded integer
    *
    * Integers are prefixed with an i and terminated by an e. For
    * example, 123 would bEcode to i123e, -3272002 would bEncode to
    * i-3272002e.
    *
    * @return int
    * @throws File_Bittorrent2_Exception if bencoded data is invalid
    */
    protected function decode_int()
    {
        $pos_e  = strpos($this->source, 'e', $this->position);
        $p = $this->position;
        if ($p === $pos_e) {
            throw new File_Bittorrent2_Exception('Empty integer.', File_Bittorrent2_Exception::decode);
        }
        if (substr($this->source, $this->position, 1) == '-') $p++;
        if (substr($this->source, $p, 1) == '0' and
        ($p != $this->position or $pos_e > $p+1)) {
            throw new File_Bittorrent2_Exception('Leading zero in integer.', File_Bittorrent2_Exception::decode);
        }
        for ($i = $p; $i < $pos_e-1; $i++) {
            if (!ctype_digit(substr($this->source, $i, 1))) {
                throw new File_Bittorrent2_Exception('Non-digit characters in integer.', File_Bittorrent2_Exception::decode);
            }
        }
        // The return value showld be automatically casted to float if the intval would
        // overflow. The "+ 0" accomplishes exactly that, using the internal casting
        // logic of PHP
        $return = substr($this->source, $this->position, $pos_e - $this->position) + 0;
        $this->position = $pos_e + 1;
        return $return;
    }

    /**
    * Decode a BEncoded list
    *
    * Lists are prefixed with a l and terminated by an e. The list
    * should contain a series of bEncoded elements. For example, the
    * list of strings ["Monduna", "Bit", "Torrents"] would bEncode to
    * l7:Monduna3:Bit8:Torrentse. The list [1, "Monduna", 3, ["Sub", "List"]]
    * would bEncode to li1e7:Mondunai3el3:Sub4:Listee
    *
    * @return array
    * @throws File_Bittorrent2_Exception if bencoded data is invalid
    */
    protected function decode_list()
    {
        $return = array();
        $char = $this->getChar();
        $p1 = $p2 = 0;
        if ($char === false) {
            throw new File_Bittorrent2_Exception('Unterminated list.', File_Bittorrent2_Exception::decode);
        }
        while ($char !== false && substr($this->source, $this->position, 1) != 'e') {
            $p1 = $this->position;
            $val = $this->bdecode();
            $p2 = $this->position;
            // Empty does not work here
            if($p1 == $p2)  {
                throw new File_Bittorrent2_Exception('Unterminated list.', File_Bittorrent2_Exception::decode);
            }
            $return[] = $val;
        }
        $this->position++;
        return $return;
    }

    /**
    * Get the char at the current position
    *
    * @return string|false
    */
    protected function getChar()
    {
        if (empty($this->source)) return false;
        if ($this->position >= $this->source_length) return false;
        return substr($this->source, $this->position, 1);
    }

    /**
    * Returns the online stats for the torrent
    *
    * @return array|false
    * @throws File_Bittorrent2_Exception if allow_url_fopen is disabled or scrape data is invalid
    */
    function getStats()
    {
        // Check if we can access remote data
        if (!ini_get('allow_url_fopen')) {
            throw new File_Bittorrent2_Exception('\'allow_url_fopen\' must be enabled.', File_Bittorrent2_Exception::source);
            return false;
        }
        // Query the scrape page
        $packed_hash = pack('H*', $this->info_hash);
        $scrape_url = preg_replace('/\/announce$/', '/scrape', $this->announce) . '?info_hash=' . urlencode($packed_hash);
        $scrape_data = file_get_contents($scrape_url);
        try {
			$stats = $this->decode($scrape_data);
		} catch (File_Bittorrent2_Exception $e) {
			throw new File_Bittorrent2_Exception('Invalid scrape data: \'' . $scrape_data . '\'', File_Bittorrent2_Exception::decode);
		}
		if (!isset($stats['files'][$packed_hash])) {
			throw new File_Bittorrent2_Exception('Invalid scrape data: \'' . $scrape_data . '\'', File_Bittorrent2_Exception::decode);
		}
        return $stats['files'][$packed_hash];
    }

	/**
	* Returns the Name of the torrent
	*
	* @return string
	*/
	function getName()
	{
		return $this->name;
	}

	/**
	* Returns the Filename of the torrent
	*
	* @return string
	*/
	function getFilename()
	{
		return $this->filename;
	}

	/**
	* Returns the Comment of the torrent
	*
	* @return string
	*/
	function getComment()
	{
		return $this->comment;
	}

	/**
	* Returns the Date of the torrent
	*
	* @return string
	*/
	function getDate()
	{
		return $this->date;
	}

	/**
	* Returns the Creator info of the torrent
	*
	* @return string
	*/
	function getCreator()
	{
		return $this->created_by;
	}

	/**
	* Returns the Files of the torrent
	*
	* @return array
	*/
	function getFiles()
	{
		return $this->files;
	}

	/**
	* Returns the the tracker the torrent has been received from
	*
	* @return string
	*/
	function getAnnounce()
	{
		return $this->announce;
	}

	/**
	* Returns the known tracker list of the torrent
	*
	* @return array
	*/
	function getAnnounceList()
	{
		return $this->announe_list;
	}

	/**
	* Returns the info hash of the torrent
	*
	* @return string
	*/
	function getInfoHash()
	{
		return $this->info_hash;
	}
}

?>
