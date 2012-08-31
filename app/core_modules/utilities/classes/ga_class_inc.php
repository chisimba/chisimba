<?php
class ga // extends object
{
    /**
     * Objects array
     *
     * @var array
     */
    public $population;

    /**
     * Fitness function name
     *
     * @var string
     */
    public $fitness_function;
    
    /**
     * Crossover function name
     *
     * @var string | array
     */
    public $crossover_functions;
    
    /**
     * Mutation function name
     *
     * @var string
     */
    public $mutation_function;
    
    /**
     * Mutation rate per child (%)
     *
     * @var float
     */
    public $mutation_rate;
    
    /**
     * Number of generations
     *
     * @var integer
     */
    public $generations;
    
    /**
     * Number of couples for each generation
     *
     * @var integer
     */
    public $num_couples;
    
    /**
     * Death rate or number of killed objects for each generation
     *
     * @var integer
     */
    public $death_rate;
    

    /**
     * Standard init function
     *
     */
    public function init()
    {
        
    }
    
    public function crossover($parent1,$parent2,$cross_functions) 
    {
        $class = get_class($parent1);
        if ($class != get_class($parent2)) 
        {
            return FALSE;
        }
        if (!is_array($cross_functions)) 
        {
            $cross_function = $cross_functions;
            $cross_functions = array();
        }
        $child = new $class();
        $properties = get_object_vars($parent1);
        foreach ($properties as $property => $value) 
        {
            if ($cross_function)
            {
                $cross_functions[$property] = $cross_function;
            }
            if (function_exists($cross_functions[$property]))
            {
                $child->$property = $cross_functions[$property]($parent1->$property,$parent2->$property);
            }
        }
        return $child;
    }

    public function mutate(&$object,$mutation_function) 
    {
        $properties = get_object_vars($object);
        foreach ($properties as $property => $value) 
        {
                $object->$property = $mutation_function($object->$property);
        }
    }
    
    public function fitness($object,$fitness_function) 
    {
        return $fitness_function($object);
    }
        
    private function best($a, $b) {   
           if ($a[1] == $b[1])
           {
               return 0;
           }
        return ($a[1] < $b[1]) ? 1 : -1;
    }


    public function select($objects, $fitness_function, $n=2) 
    {
        foreach ($objects as $object) 
        {
            $selection[] = array($object,$fitness_function($object));
        }
        usort($selection,array("GA", "best"));
        $selection = array_slice($selection, 0, $n);
        foreach ($selection as $selected) 
        {
            $winners[] = $selected[0];
        }
        return $winners;
    }
    
    private function worst($a, $b) 
    {   
           if ($a[1] == $b[1])
           {
               return 0;
           }
        return ($a[1] < $b[1]) ? -1 : 1;
    }

    public function kill(&$objects, $fitness_function, $n=2) 
    {
        foreach ($objects as $object) 
        {
            $selection[] = array($object, $fitness_function($object));
        }
        usort($selection, array("GA", "worst"));
        $selection = array_slice($selection, 0, count($selection)-$n);
        $objects = array();
        foreach ($selection as $selected) 
        {
            $objects[] = $selected[0];
        }
    }
    
    private function mass_crossover($objects, $cross_functions) 
    {
        foreach ($objects as $object) 
        {
            if (!$obj1)
            {
                $obj1 = $object;
            }
            else {
                $children[] = $this->crossover($obj1, $object, $this->crossover_functions);
                $obj1 = NULL;
            }
        }
        return $children;
    }

    private function mass_mutation(&$objects) 
    {
        foreach($objects as $key => $object) 
        {
            if (rand(1,100) <= $this->mutation_rate) $this->mutate($objects[$key], $this->mutation_function);
        }
    }

    public function evolve() 
    {
        for ($i=0; $i<$this->generations; $i++) 
        {
            $couples = $this->select($this->population, $this->fitness_function, 2*min($this->num_couples, floor(count($this->population)/2)));
            $children = $this->mass_crossover($couples, $this->crossover_functions);
            $this->mass_mutation($children);
            $this->population = array_merge($this->population, $children);
            $this->kill($this->population, $this->fitness_function, min($this->death_rate,count($this->population)-2));
        }
    }
}
?>