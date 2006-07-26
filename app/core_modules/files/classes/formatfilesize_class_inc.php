<?php
/**
* Format File Size
* Convert the size in bytes for a file into a more user-friendly format 
* http://www.drquincy.com/resources/code/php/formatfilesize/
*/
class formatfilesize
{
    /**
    * Constructor
    */
    function formatfilesize()
    {  }
    
    /**
    * Method to format the size of a file
    * @param int $size Size of the file
    * @return string Formatted Size of file
    */
    function formatsize($size)
    {
        // bytes
        if( $size < 1024 ) {
            return $size . " bytes";
        }
        // kilobytes
        else if( $size < 1024000 ) {
            return round( ( $size / 1024 ), 1 ) . "k";
        }
        // megabytes
        else {
            return round( ( $size / 1024000 ), 1 ) . " MB";
        }
    }
    
}
?>