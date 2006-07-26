<?php
/**
* A class to get the contents of a file, load it into a string
* and perform various transformations.
*/

class filegetter
{
    /**
    * Method to load a file into a string when supplied with the full
    * file path to the file
    *
    * @param string $file the file to open and get
    */
    function getFileToString($file)
    {
        if (file_exists($file)) {
            //read it into a string and return the string
            $fp = fopen($file, "r")
                or die("fopen failed");   /* the file_exists should
                                             prevent this error but trap
                                             it anyway */
            $contents = fread($fp, filesize($file));
            fclose($fp);
            return $contents;
        } else {
            return False;
        }
    }
} // end class

?>