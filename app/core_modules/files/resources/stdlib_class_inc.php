<?php

class RecursiveTreeIterator extends RecursiveIteratorIterator
{
	const BYPASS_CURRENT = 0x00000004;
	const BYPASS_KEY     = 0x00000008;

	private $rit_flags;

	function __construct(RecursiveIterator $it, $rit_flags = self::BYPASS_KEY, $cit_flags = CachingIterator::CATCH_GET_CHILD, $mode = self::SELF_FIRST)
	{
		parent::__construct(new RecursiveCachingIterator($it, $cit_flags), $mode, $rit_flags);
		$this->rit_flags = $rit_flags;
	}

	public $prefix = array(0=>'', 1=>'| ', 2=>'  ', 3=>'|-', 4=>'\-', 5=>'');

	function getPrefix()
	{
		$tree = '';
		for ($level = 0; $level < $this->getDepth(); $level++)
		{
			$tree .= $this->getSubIterator($level)->hasNext() ? $this->prefix[1] : $this->prefix[2];
		}
		$tree .= $this->getSubIterator($level)->hasNext() ? $this->prefix[3] : $this->prefix[4];

		return $this->prefix[0] . $tree . $this->prefix[5];
	}

	function getEntry()
	{
		return @(string)parent::current();
	}

	function getPostfix()
	{
		return '';
	}

	function current()
	{
		if ($this->rit_flags & self::BYPASS_CURRENT)
		{
			return parent::current();
		}
		else
		{
			return $this->getPrefix() . $this->getEntry() .  $this->getPostfix();
		}
	}

	function key()
	{
		if ($this->rit_flags & self::BYPASS_KEY)
		{
			return parent::key();
		}
		else
		{
			return $this->getPrefix() . parent::key() .  $this->getPostfix();
		}
	}

	function __call($func, $params)
	{
		return call_user_func_array(array($this->getSubIterator(), $func), $params);
	}
}

class DirectoryFilterDots extends RecursiveFilterIterator
{
	/** Construct from a path.
	 * @param $path directory to iterate
	 */
	function __construct($path)
	{
		parent::__construct(new RecursiveDirectoryIterator($path));
	}

	/** @return whether the current entry is neither '.' nor '..'
	 */	
	function accept()
	{
		return !$this->getInnerIterator()->isDot();
	}

	/** @return the current entries path name
	 */
	function key()
	{
		return $this->getInnerIterator()->getPathname();
	}
}

class DirectoryTreeIterator extends RecursiveIteratorIterator
{
	function __construct($path)
	{
		parent::__construct(
		new RecursiveCachingIterator(
		new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_FILENAME
		),
		CachingIterator::CALL_TOSTRING|CachingIterator::CATCH_GET_CHILD
		),
		parent::SELF_FIRST
		);
	}

	function current()
	{
		$tree = '';
		for ($l=0; $l < $this->getDepth(); $l++) {
			$tree .= $this->getSubIterator($l)->hasNext() ? '| ' : '  ';
		}
		return $tree . ($this->getSubIterator($l)->hasNext() ? '|-' : '\-')
		. $this->getSubIterator($l)->__toString();
	}

	function __call($func, $params)
	{
		return call_user_func_array(array($this->getSubIterator(), $func), $params);
	}
}

class DirectoryGraphIterator extends DirectoryTreeIterator
{
	function __construct($path)
	{
		RecursiveIteratorIterator::__construct(
			new RecursiveCachingIterator(
				new ParentIterator(
					new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_FILENAME
						)
					),
				CachingIterator::CALL_TOSTRING|CachingIterator::CATCH_GET_CHILD
			),
			parent::SELF_FIRST
		);
	}
}

class SmartDirectoryIterator implements Iterator 
{
    
    protected $key;
    protected $current;
    protected $valid;
    
    protected $path;
    protected $handle;

    public function __construct($path) {
        $this->handle = opendir($path);
        $this->path = $path;
    }

    private function getFile() {
        if ( false !== ($file = readdir($this->handle)) ) {
            $path = $this->path . $file;
            $this->current['name'] = $file;
            $this->current['size'] = filesize($path);
            $this->current['modified'] = filemtime($path);
            return true;
        } else {
            return false;
        }
    }

    public function next() {
        $this->valid = $this->getFile();
        $this->key++;
    }

    public function rewind() {
        $this->key = 0;
        rewinddir($this->handle);
        $this->valid = $this->getFile();
    }

    public function valid() {
        return $this->valid;
    }

    public function key() {
        return $this->key;
    }

    public function current() {
        return $this->current;
    }

    public function __destruct( ) {
        closedir($this->handle);
    }

}

class KeyFilter extends FilterIterator
{
  private $_rx;
 
  function __construct(Iterator $it, $regex)
  {
    parent::__construct($it);
    $this->_rx= $regex;
  }

  function accept()
  {
    return preg_match($this->_rx,$this->getInnerIterator()->key());
  }

  protected function __clone() {
    return false;
  }
}

class DirMach extends KeyFilter
{
  function __construct($path , $regex)
  {
    parent::__construct(
    new DirTreeIterator($path), $regex);
  }

  function current()
  {
    return parent::key();
  }

  function key()
  {
    return parent::key();
  }
}

class DirTreeIterator extends RecursiveIteratorIterator
{
    /** Construct from a path.
     * @param $path directory to iterate
     */
    function __construct($path)
    {
     try {
      parent::__construct(
            new RecursiveCachingIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_FILENAME
                ),
                CachingIterator::CALL_TOSTRING|CachingIterator::CATCH_GET_CHILD
            ),
            parent::SELF_FIRST
        );
     } catch(Exception $e) {
       echo $e->getMessage();
       exit;
     }
    }

    /** @return the current element prefixed with ASCII graphics
     */   
    function current()
    {
      if ($this->hasChildren())
        $this->next(); 
      return $this->key();
    }

    /** Aggregates the inner iterator
     */   
    function __call($func, $params)
    {
      return call_user_func_array(array($this->getSubIterator(), $func), $params);
    }
}

class ExtensionFilter extends FilterIterator {
    
    private $ext;
    private $it;
    
    public function __construct(DirectoryIterator $it, $ext) {
        parent::__construct($it);
        $this->it = $it;
        $this->ext = $ext;
    }
    
    public function accept() {
        if ( ! $this->it->isDir() ) {
            $ext = array_pop(explode('.', $this->current()));
            return $ext != $this->ext;
        }
        return true;
    }
}

?>