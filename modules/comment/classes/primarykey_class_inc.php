<?php
/**
* class to make unique primary keys for NextGen initial setup
* and module registration
* @author James Scoble
*/
class primarykey extends object
{
    public $tables;
    
    /**
    * class constructor function
    */
    public function primarykey()
    {
        $this->init();
    }

    public function init()
    {
        $this->tables=array();
    }
    
    /**
    * returns a key
    * @param string $table
    * @returns string $outstr
    */
    public function newkey($table='blank')
    {
        if (isset($this->tables[$table]))
        {
            $this->tables[$table]++;
        } else {
            //$this->tables[$table]=rand(100,999);
            $this->tables[$table]=0;
        }
        //$outstr=date("Ymdhis").$this->tables[$table]."@init";
        $outstr="init_".$this->tables[$table];
        return $outstr;
    }
}
?>
