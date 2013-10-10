<?php
class simpleregistrationutils extends object{

   /**
    * initialize the object, and set the necessary ext js scripts
    */
    public function init(){
        $this->loadclass('link','htmlelements');
    }
    function downloadfile() {
        $homeUrl =new link( $this->uri(array('action'=>'memberlist')));
        $homeUrl->link='Back';
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $downloadfolder=$objSysConfig->getValue('DOWNLOAD_FOLDER', 'simpleregistration');
        $codebase="http://" . $_SERVER['HTTP_HOST'];
        $docRoot=$_SERVER['DOCUMENT_ROOT'];
        if ( !preg_match( '/^(\w)+\.(?!php|html|htm|shtml|phtml)[a-z0-9]{2,4}$/i', 'listing.xls' ) )    {

            error_log( "USER REQUESTed for:\t".'listing.xls' );
            die;
        };

        $filename =$docRoot. $downloadfolder.'listing.xls';
        //echo $filename;
        if ( !file_exists($filename) )    {
            echo "No file here. [<a href='javascript:self.close();'>close</a>]";
            error_log( "USER REQUESTed for non-existing file:\t".'listing.xls' );
            exit;
        };

        return '<h1><a href="'.$codebase.$downloadfolder.'listing.xls">Click here to download file</a></h1><br/>'.$homeUrl->show();

        
    }
}
?>
