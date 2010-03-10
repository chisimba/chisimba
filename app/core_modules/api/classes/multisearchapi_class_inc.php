<?php

class multisearchapi extends object
{
    protected $objOps;

    public function init()
    {
        $this->objOps = $this->getObject('multisearchops', 'multisearch');
    }

    public function query($params)
    {
        $query = $params->getParam(0)->scalarval();
        $builtQuery = $this->objOps->buildQuery($query);
        $data = $this->objOps->doQuery($builtQuery);
        $output = $this->objOps->formatQuery($data);
        $text = '';
        foreach ($output as $type => $html) {
            $text .= sprintf("--- %s ---\n", ucfirst($type));
            $text .= strip_tags($html);
        }
        $value = new XML_RPC_Value($text, 'string');
        $response = new XML_RPC_Response($value);

        return $response;
    }
}
