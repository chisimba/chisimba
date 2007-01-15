<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to Show the Icons, Link to Website and RDF tags for a Creative Commons license
 * @author Tohir Solomons
 */
class displaylicense extends object
{

    
    /**
     * Size of Icons to be Use, either big (32x32) or small (20x20)
     *
     * @var string
     */
    public $icontype = 'small'; // or big
    
    /**
    * @var string $license
    */
    public $license;
    
    /**
    * @var array $rdfList List of RDF tags for licenses
    */
    private $rdfList;
    
    /**
     * Constructor
     */
    public function init()
    {
        // Load the Creative Commons Object
        $this->objCC =& $this->getObject('dbcreativecommons');
        
        // Load the GetIcon Object
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        
        // Load the Link Class
        $this->loadClass('link', 'htmlelements');
        
        // Setup RDF Array
        $this->setUpRdf();
    }
    
    /**
     * Method to show a license
     *
     * @return string Rendered Input
     */
    public function show($license='')
    {
        if ($license != '') {
            $this->license = $license;
        }
        
        // Get All Licenses
        $licenseInfo = $this->objCC->getLicense($this->license);
        
        if ($licenseInfo == FALSE) {
            return NULL;
        }
        
        // Determine Size of Icon
        if ($this->icontype == 'big') {
            $iconsFolder = 'icons/creativecommons';
        } else {
            $iconsFolder = 'icons/creativecommons_small';
        }
        
        // Get List of Icons
        $icons = explode(',', $licenseInfo['images']);

        // Add nowrap class to prevent overflow
        $iconList = '<span class="nowrap">';
        
        // Generate Icons
        foreach ($icons as $icon)
        {
            $this->objIcon->setIcon ($icon, NULL, $iconsFolder);
            $iconList .= $this->objIcon->show();
    
        }
        
        // End Span
        $iconList .= '</span>';
        
        // Show as link if it has a link, else just show icons
        if (trim($licenseInfo['url']) == '') {
            return $iconList.$this->getRdf($this->license);//$radio->show();
        } else {
            $link = new link ($licenseInfo['url']);
            $link->link = $iconList;
            return $link->show().$this->getRdf($this->license);
        }
    }
    
    /**
    * Method to get the RDF tags for a license
    * @param string $license License to get RDF tags for
    * @return string
    */
    public function getRdf($license)
    {
        if (array_key_exists($license, $this->rdfList)) {
            return $this->rdfList[$license];
        } else {
            return NULL;
        }
    }
    
    /**
    * Method to setup the RDF tags. It creates an array with a list of RDF tags.
    */
    private function setUpRdf()
    {
        $this->rdfList = array();
        
        // Attribution
        $this->rdfList['by'] = '
<!-- <rdf:RDF xmlns="http://web.resource.org/cc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">
	<Work rdf:about="">
		<license rdf:resource="http://creativecommons.org/licenses/by/2.5/" />
	</Work>
	<License rdf:about="http://creativecommons.org/licenses/by/2.5/"><permits rdf:resource="http://web.resource.org/cc/Reproduction"/><permits rdf:resource="http://web.resource.org/cc/Distribution"/><requires rdf:resource="http://web.resource.org/cc/Notice"/><requires rdf:resource="http://web.resource.org/cc/Attribution"/><permits rdf:resource="http://web.resource.org/cc/DerivativeWorks"/></License></rdf:RDF> -->';
    
        // Attribution Non-commercial
        $this->rdfList['by-nc'] = '
<!-- <rdf:RDF xmlns="http://web.resource.org/cc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">
	<Work rdf:about="">
		<license rdf:resource="http://creativecommons.org/licenses/by-nc/2.5/" />
	</Work>
	<License rdf:about="http://creativecommons.org/licenses/by-nc/2.5/"><permits rdf:resource="http://web.resource.org/cc/Reproduction"/><permits rdf:resource="http://web.resource.org/cc/Distribution"/><requires rdf:resource="http://web.resource.org/cc/Notice"/><requires rdf:resource="http://web.resource.org/cc/Attribution"/><prohibits rdf:resource="http://web.resource.org/cc/CommercialUse"/><permits rdf:resource="http://web.resource.org/cc/DerivativeWorks"/></License></rdf:RDF> -->
';
        
        // Attribution Share Alike
        $this->rdfList['by-sa'] = '
<!-- <rdf:RDF xmlns="http://web.resource.org/cc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">
	<Work rdf:about="">
		<license rdf:resource="http://creativecommons.org/licenses/by-sa/2.5/" />
	</Work>
	<License rdf:about="http://creativecommons.org/licenses/by-sa/2.5/"><permits rdf:resource="http://web.resource.org/cc/Reproduction"/><permits rdf:resource="http://web.resource.org/cc/Distribution"/><requires rdf:resource="http://web.resource.org/cc/Notice"/><requires rdf:resource="http://web.resource.org/cc/Attribution"/><permits rdf:resource="http://web.resource.org/cc/DerivativeWorks"/><requires rdf:resource="http://web.resource.org/cc/ShareAlike"/></License></rdf:RDF> -->
';
        
        // Attribution No Derivatives
        $this->rdfList['by-nd'] = '
<!-- <rdf:RDF xmlns="http://web.resource.org/cc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">
	<Work rdf:about="">
		<license rdf:resource="http://creativecommons.org/licenses/by-nd/2.5/" />
	</Work>
	<License rdf:about="http://creativecommons.org/licenses/by-nd/2.5/"><permits rdf:resource="http://web.resource.org/cc/Reproduction"/><permits rdf:resource="http://web.resource.org/cc/Distribution"/><requires rdf:resource="http://web.resource.org/cc/Notice"/><requires rdf:resource="http://web.resource.org/cc/Attribution"/></License></rdf:RDF> -->';
        
        // Attribution Non-commercial Share Alike
        $this->rdfList['by-nc-sa'] = '
<!-- <rdf:RDF xmlns="http://web.resource.org/cc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">
	<Work rdf:about="">
		<license rdf:resource="http://creativecommons.org/licenses/by-nc-sa/2.5/" />
	</Work>
	<License rdf:about="http://creativecommons.org/licenses/by-nc-sa/2.5/"><permits rdf:resource="http://web.resource.org/cc/Reproduction"/><permits rdf:resource="http://web.resource.org/cc/Distribution"/><requires rdf:resource="http://web.resource.org/cc/Notice"/><requires rdf:resource="http://web.resource.org/cc/Attribution"/><prohibits rdf:resource="http://web.resource.org/cc/CommercialUse"/><permits rdf:resource="http://web.resource.org/cc/DerivativeWorks"/><requires rdf:resource="http://web.resource.org/cc/ShareAlike"/></License></rdf:RDF> -->';
    
        // Attribution Non-commercial No Derivatives
        $this->rdfList['by-nc-nd'] = '
<!-- <rdf:RDF xmlns="http://web.resource.org/cc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">
	<Work rdf:about="">
		<license rdf:resource="http://creativecommons.org/licenses/by-nc-nd/2.5/" />
	</Work>
	<License rdf:about="http://creativecommons.org/licenses/by-nc-nd/2.5/"><permits rdf:resource="http://web.resource.org/cc/Reproduction"/><permits rdf:resource="http://web.resource.org/cc/Distribution"/><requires rdf:resource="http://web.resource.org/cc/Notice"/><requires rdf:resource="http://web.resource.org/cc/Attribution"/><prohibits rdf:resource="http://web.resource.org/cc/CommercialUse"/></License></rdf:RDF> -->
';
    }
}

?>