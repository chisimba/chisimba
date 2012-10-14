<?php
class ZipWrapper
{
    public static function read($archive, $filename)
    {
        $zip = new ZipArchive;
        if (file_exists($archive)) {
            if ($zip->open(realpath($archive))) {
                if ($zip->locateName($filename) !== false) {
                    return $zip->getFromName($filename);
                }
            }
        }
        return false;
    }
    
    public static function write($archive, $filename, $content)
    {
        $zip = new ZipArchive;
        if (file_exists($archive)) {
            $zip->open(realpath($archive));
        } else {
            $zip->open(getcwd() . '/' .  $archive, ZipArchive::CREATE);
        }

        if ($zip->locateName($filename) !== false) {
            $zip->deleteName($filename);
        }
        $error = $zip->addFromString($filename, $content);

        return $error;
    }
}
?>