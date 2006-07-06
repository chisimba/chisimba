<?
/**
* class to make unique primary keys for NextGen initial setup
* and module registration
* @author James Scoble
*/
class primarykey extends object
{
    var $tables;
    
    /**
    * class constructor function
    */
    function primarykey()
    {
        $this->init();
    }

    function init()
    {
        $this->tables=array();
    }
    
    /**
    * returns a key
    * @param string $table
    * @returns string $outstr
    */
    function newkey($table='blank')
    {
        if (isset($this->tables[$table]))
        {
            $this->tables[$table]++;
        } else {
            //$this->tables[$table]=rand(100,999);
            $this->tables[$table]=0;
        }
        //$outstr=date("Ymdhis").$this->tables[$table]."@init";
        $outstr=$this->tables[$table]."@init";
        return $outstr;
    }
}
?>
