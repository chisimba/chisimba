<?
/**
* class to read an XML file and turn it into an assoc array
* makes no assumptions about what the xml contains
* may have bugs
* @author James Scoble
*/
class contentread
{
    /**
    * method to load XML and return an assoc array
    * @param string $xmlsource
    * @param string $type
    * @returns array $data
    */
    function xmlread($xmlsource,$type='file')
    {
        if ($type!='text')
        {
            $pp=xml_parser_create();
            $long=file($xmlsource);
            $str='';
            foreach ($long as $line)
            {
                $str.=$line;
            }
            unset($long);
        } else {
            $str=$xmlsource;
            unset($xmlsource);
        }
        xml_parse_into_struct($pp, $str, $vals, $index);
        xml_parser_free($pp);

        $data=array();
        $fields=array();
        $oldlevel=0;
        $oldtype=null;
        $oldtag=null;
        $offset=array();
        for ($i=0;$i<199;$i++)
        {
            $offset[$i]=0;
        }
        foreach ($vals as $line)
        {
            $tag=$line['tag'];
            $level=$line['level'];
            $type=$line['type'];
            $fields[$level]=$tag;
            if (($level==$oldlevel)&&($tag==$oldtag)&&($type!='cdata')){
                $offset[$level]=$offset[$level]+1;
            }
            if (($level>$oldlevel)&&($oldtype!='cdata')){
                $offset[$level]=0;
            }
            if (($level<$oldlevel)&&($type!='cdata')){
                for ($i=$oldlevel;$i<199;$i++)
                {
                    $offset[$i]=0;
                }
                //if ($type!='cdata'){
                    $offset[$level]=$offset[$level]+1;
                //}
            }
            if ((isset($line['value']))&&($type=='complete')){
            $dline="data";
            for ($i=1;$i<=$level;$i++)
            {
                $dline.="[".$fields[$i]."][".$offset[$i]."]";
            }
            $info=trim($line['value']);
            $php=$dline."=".$info."";
            parse_str($php);
            }
            $oldlevel=$level;
            $oldtag=$tag;
            $oldtype=$type;
        }
        return($data);
    }
}

?>