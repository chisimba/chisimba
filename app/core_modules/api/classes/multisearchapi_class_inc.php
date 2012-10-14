<?php

class multisearchapi extends object
{
    protected $objOps;
    protected $dataCapable;

    public function init()
    {
        $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
        if($this->objModuleCat->checkIfRegistered('multisearch'))
        {
            $this->dataCapable = TRUE;
            $this->objOps = $this->getObject('multisearchops', 'multisearch');
        }
        else {
            $this->dataCapable = FALSE;
        }
    }

    public function query($params)
    {
        if($this->dataCapable == TRUE) {
            $query = $params->getParam(0)->scalarval();
            $builtQuery = $this->objOps->buildQuery($query);
            $data = $this->objOps->doQuery($builtQuery);
            $output = $this->objOps->formatQuery($data, 'plaintext');
            $text = implode('', $output);
            $value = new XML_RPC_Value($text, 'string');
            $response = new XML_RPC_Response($value);
            return $response;
        }
        else {
            $ret = "This server is not yet capable of handling multisearch data. Please install the multisearch module!";
            $val = new XML_RPC_Value($ret, 'string');
            return new XML_RPC_Response($val);
        }
    }
}
