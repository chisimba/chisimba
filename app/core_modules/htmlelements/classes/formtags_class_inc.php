<?

/**
* Simple class to output start and end tags for form elements
*
*/

class formtags
{
    /**
    * @param string $action
    * @param string $method
    * @param string $name
    * @param string $onsubmit
    * @returns string $str
    */
    function startform($action,$method='GET',$id=FALSE,$onsubmit=FALSE)
    {
        $str="<form action=\"".$action."\" method=\"".$method."\" ";
        if ($id)
        {
            $str.="ID='".$id."' ";
        }
        if ($onsubmit)
        {
            $str.="onsubmit=\"".$onsubmit."\" ";
        }
        $str.=">\n";
        return $str;
    }

    /**
    * @returns string $str
    */
    function closeform()
    {
        $str="</form>\n";
        return $str;
    }
}
?>
