<?php



/**
 * Start of Class
 */
class webpresentsearch extends object
{

    public function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject('user', 'security');

    }

    public function clearCache()
    {
        $objScanForSearch = $this->getObject('scanforsearch');

        $indexPath = $this->objConfig->getcontentBasePath().'webpresentsearch';

        $results = $objScanForSearch->scanDirectory($indexPath);

        if (count($results['files']) > 0)
        {
            foreach ($results['files'] as $file)
            {
                @unlink ($file);
            }
        }

    }

} // end class
?>