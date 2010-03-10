<?php

class multisearchapi extends object
{
    protected $objOps;

    public function init()
    {
        $this->objOps = $this->getObject('multisearchops', 'multisearch');
    }
}
