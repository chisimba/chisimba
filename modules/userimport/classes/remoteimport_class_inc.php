<?php
/**
* Class remoteimport
* PHP class library for getting user lists from a remote webservice
* @author James Scoble
*/
class remoteimport extends object
{
    
    var $errorFlag=FALSE;
    
    /**
    * init function used by object-derived classes
    * instantiates other classes used
    */
    function init()
    {   
        // set exec time to 5 mins
        ini_set("max_execution_time",300);
        // Config and language objects
        $this->objConfig=$this->getObject('dbsysconfig','sysconfig');
        $this->objLanguage=$this->getObject('language','language');
        $soapserver=$this->objConfig->getValue('remotedata','userimport');
        //if ($soapserver==NULL){
        //    $soapserver="http://172.16.65.134/webservice/userimport.php?wsdl";
        //}
        // Get value of remote webservice class name if needed
        $soapEntry=$this->objConfig->getValue('remotedata2','userimport');
        if (($soapEntry=='REMOTE_SERVICE') || ($soapEntry=='NULL') || ($soapEntry=='0')){
            $soapEntry=NULL;
        }
        $this->soapEntry=$soapEntry;
        //require_once('lib/nusoap/nusoap.php');
        try {
        $this->soapClient = new SoapClient($soapserver);
        } catch (Exception $e){
            //print "Problem creating SOAP object.<br />\n";
            $this->errorFlag=TRUE;
                    die($e->getMessage());
        }
    }

    /**
    * Method to get a list of Faculties
    * @param string $server
    * @returns array $result
    */
    function getFaculties($server='default')
    {
        if ($this->errorFlag) {
            return FALSE;
        }
        $result = $this->soapClient->getFaculty('');
        // Make sure result is an array
        if (is_array($result)){
            return $result;
        } else {
            $objXML=$this->getObject('xmlserial','utilities');
            return $objXML->readXML($result,FALSE);
        }            
    }

    /**
    * Method to contact SOAP client
    * Returns whatever data the client reports
    * @param string $function the SOAP function to call
    * @param string $value the value to call across
    * @returns array $result
    */
    function getSOAP($function,$value)
    {
        if ($this->errorFlag) {
            return FALSE;
        }
        // If there is an extra param we need to specify which webservice to use
        try {
            if ($this->soapEntry==NULL){
                $result = $this->soapClient->$function($value);
            } else {
                $result = $this->soapClient->soapEntry($function,$value);
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
        // Make sure result is an array
        if (is_array($result)){
            return $result;
        } else {
            $objXML=$this->getObject('xmlserial','utilities');
            return $objXML->readXML($result,FALSE);
        }            
    }

    /**
    * Method to get a list of Programmes
    * @param string $faculty
    * @returns array $result
    */
    function getProgrammes($faculty)
    {
        return $this->getSOAP('getProgramme',$faculty);   
    }

    /**
    * Method to get a list of Modules
    * @param string $program
    * @returns array $result
    */
    function getModules($program)
    {
        return $this->getSOAP('getModule',$program);
    }

    /**
    * Method to get an array of Students
    * @param string $module
    * @returns array $result
    */
    function getClassList($module)
    {
        $catch= $this->getSOAP('getClassList',$module);
        return $catch;
    }
    
    /**
    * Method to get the name of a class from the code
    * @param string $program
    * @returns array $result
    */
    function getModuleName($module)
    {
        return $this->getSOAP('getModuleName',$module);
    }
    

    /**
    * Method to get the name of a faculty from the code
    * @param string $faculty
    * @returns array $result
    */
    function getFacultyName($faculty)
    {
        return $this->getSOAP('getFacultyName',$faculty);
    }

    /**
    * Method to get the name of a programme from the code
    * @param string $program
    * @returns array $result
    */
    function getProgramName($program)
    {
        return $this->getSOAP('getProgramName',$program);                    
    }

    
    /**
    * Method to export a classlist as XML
    * @param string $classmodule
    * @returns string $xml
    */
    function XMLexport($classmodule)
    {
        $asciiFlag=$this->objConfig->getValue('set_to_ascii','userimport');

        $classlist=$this->getClassList($classmodule);
        $xml="<batch>\n";
        $xml.="<batchcode>$classmodule</batchcode>\n";
        if (is_array($classlist)){
            $emaildomain=$this->objConfig->getValue('emaildomain');
            if ($emaildomain==NULL){
                $emaildomain='uwc.ac.za';
            }
            foreach ($classlist as $line)
            {
                // Strip Problem Chars
                $line->firstname=html_entity_decode($line->firstname,ENT_NOQUOTES,'UTF-8');
                $line->surname=html_entity_decode($line->surname,ENT_NOQUOTES,'UTF-8');
                if ($asciiFlag==1){
                    setlocale(LC_ALL, 'en_US.UTF8');
                    $line->firstname=iconv('UTF-8', 'ASCII//TRANSLIT', $line->firstname);
                    $line->surname=iconv('UTF-8', 'ASCII//TRANSLIT', $line->surname);
                }
                $xml.="<student>\n";
                $xml.="<userId>".$line->studnum."</userId>\n";
                $xml.="<username>".$line->username."</username>\n";
                $xml.="<firstname>".$line->firstname."</firstname>\n";
                $xml.="<surname>".$line->surname."</surname>\n";
                $xml.="<sex>".$line->sex."</sex>\n";
                if (isset($line->password)){
                    $xml.="<password>".$line->password."</password>\n";
                }
                if (isset($line->cryptpassword)){
                    $xml.="<cryptpassword>".$line->cryptpassword."</cryptpassword>\n";
                }
                $email=$line->email;
                // If no email addr put the UWC one.
                // This will need to be changed to a config param later
                if (substr_count($email, '@')==0){
                    $email=$line->studnum.'@'.$emaildomain;
                }
                $xml.="<emailAddress>".$email."</emailAddress>\n";
                $xml.="<title>".$line->title."</title>\n";
                $xml.="</student>\n";
            }

        }
        $xml.="</batch>\n";
        return $xml;
    }

    /**
    * Method to dump XML classlist list to the filesystem
    * @param string $classmodule
    * @returns string $filename;
    */
    function writeXML($classmodule)
    {
        $filename=tempnam('/temp','XML');
        $fp=fopen($filename,'w');
        fwrite($fp,$this->XMLexport($classmodule));
        fclose($fp);
        return $filename;
    }
}
?>
