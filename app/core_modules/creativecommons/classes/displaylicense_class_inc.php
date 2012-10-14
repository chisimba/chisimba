<?php

/**
 * License display
 * 
 * Class to display the Creative Commons license in a module
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   creativecommons
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
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
    public $icontype = 'big'; // or small
    
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
        $this->objCC = $this->getObject('dbcreativecommons');
        
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
        $objModules = $this->getObject('modules', 'modulecatalogue');
        
        if (!$objModules->checkIfRegistered('creativecommons')) {
            return '';
        }
        
        
        if ($license != '') {
            $this->license = $license;
        }
        
        // Get All Licenses
        $licenseInfo = $this->objCC->getLicense($this->license);
        
        if ($licenseInfo == FALSE) {
            return NULL;
        }
        
        $iconsFolder = 'icons/creativecommons_v3';
        
        // Get List of Icons
        $icons = explode(',', $licenseInfo['images']);

        // Add nowrap class to prevent overflow
        $iconList = '<span class="nowrap">';
        
        if ($this->icontype == 'big') {
            $filename = $licenseInfo['code'].'_big';
        } else {
            $filename = $licenseInfo['code'];
        }
        
        $filename = str_replace('/', '_', $filename);
        
        $this->objIcon->setIcon ($filename, NULL, $iconsFolder);
        $this->objIcon->alt = $licenseInfo['title'];
        $this->objIcon->title = $licenseInfo['title'];
        
        $iconList .= $this->objIcon->show();
        
        // End Span
        $iconList .= '</span>';
        
        // Show as link if it has a link, else just show icons
        if (trim($licenseInfo['url']) == '') {
            return $iconList.$this->getRdf($this->license);//$radio->show();
        } else {
            $this->loadClass('href', 'htmlelements');
            $link = new href($licenseInfo['url'], $iconList, 'rel="license"');
            return $link->show().$this->getRdf($this->license);
        }
    }
    
    /**
    * Method to get the RDF tags for a license
    * @param  string $license License to get RDF tags for
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
