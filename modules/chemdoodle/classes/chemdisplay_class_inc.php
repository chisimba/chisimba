<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}

/**
 * Class chemdisplay containing all display/output functions of the chemdoodle module
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright Wits University 2010
 * @license http://opensource.org/licenses/lgpl-2.1.php
 * @package chemdoodle
 *
 */
class chemdisplay extends object
{
   /** @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;

   /** @var object $objUser: The user class of the buddies module
    * @access public
    */
   public $objUser;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // system classes
        $this->objLanguage = $this->getObject('language','language');
        $this->objUser = $this->getObject('user','security');
        $chem_stylesheet = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ChemDoodleWeb.css').'"/>';
	$this->appendArrayVar('headerParams', $chem_stylesheet);

        $this->appendArrayVar('headerParams', $this->getJavascriptFile('ChemDoodleWeb-libs.js', ''));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('ChemDoodleWeb.js', ''));

    }

    /**
     * Method to show a chemical structure using a 3D Rotator
     *
     * @param string $file The file to display
     * @return string $output The output to display the molecule
     */
    public function show3dMolecule($file, $width = 350, $height = 400, $useJMOLColors = 'true', $circles = 'true', $symmetrical = 'true', $background = '#E4FFC2')
    {
        $jsData = "<script> theed = ". $this->file2js($file).";</script>";
        
        $this->appendArrayVar('headerParams', $jsData);

        $output = '<script>';
        $output .= "var rotate3D = new RotatorCanvas('rotate3D', $width, $height, true);\n";
        $output .= "rotate3D.specs.atoms_useJMOLColors = $useJMOLColors;\n";
        $output .= "rotate3D.specs.atoms_circles_2D = $circles;\n";
        $output .= "rotate3D.specs.bonds_symmetrical_2D = $symmetrical;\n";
        $output .= "rotate3D.specs.backgroundColor = '$background';\n";
        $output .= "rotate3D.loadMolecule(readMOL(threed));\n";
        $output .= "rotate3D.startAnimation();\n";
        $output .= '</script>';

        return $output;
    }

    /**
     * Method to show a chemical structure using a Transformer
     *
     * @param string $file The file to display
     * @return string $output The output to display the molecule
     */
    public function showTransformerMolecule($file, $width = 350, $height = 400, $useJMOLColors = 'true', $bonds_width = 3, $atoms_display = 'true', $background = 'white')
    {
        $jsData = "<script> dna = ". $this->file2js($file).";</script>";

        $this->appendArrayVar('headerParams', $jsData);

        $output = '<script>';
        $output .= "var transform1 = new TransformCanvas('transform1', $width, $height, true);\n";
        $output .= "transform1.specs.bonds_useJMOLColors = $useJMOLColors;\n";
        $output .= "transform1.specs.bonds_width_2D = $bonds_width;\n";
        $output .= "transform1.specs.atoms_display = $atoms_display;\n";
        $output .= "transform1.specs.backgroundColor = '$background';\n";
        $output .= "transform1.loadMolecule(readMOL(dna));\n";
        $output .= '</script>';

        return $output;
    }

    /**
     * Method to show a chemical structure using a 2D Rotator
     *
     * @param string $file The file to display
     * @return string $output The output to display the molecule
     */
    public function show2dMolecule($file, $width = 350, $height = 400, $useJMOLColors = 'true')
    {
        $jsData = "<script> larger = ". $this->file2js($file).";</script>";

        $this->appendArrayVar('headerParams', $jsData);

        $output = '<script>';
        $output .= "var rotate2D = new RotatorCanvas('rotate2D', $width, $height);\n";
        $output .= "rotate2D.specs.atoms_useJMOLColors = $useJMOLColors;\n";
        $output .= "rotate2D.loadMolecule(readMOL(larger));\n";
        $output .= "rotate2D.startAnimation();\n";
        $output .= '</script>';

        return $output;
    }

    /**
     * Method to show a chemical structure
     *
     * @param string $file The file to display
     * @return string $output The output to display the molecule
     */
    public function showMolecule($file, $width = 350, $height = 400)
    {
        $jsData = "<script> caffeine = ". $this->file2js($file).";</script>";

        $this->appendArrayVar('headerParams', $jsData);

        $output = '<script>';
        $output .= "var view1 = new ViewerCanvas('view1', $width, $height);\n";
        $output .= "view1.loadMolecule(readMOL(caffeine));\n";
        $output .= '</script>';

        return $output;
    }

    /**
     * Method to show a form to select and display a molecule
     *
     * @param string $file The file to display
     * @return string $output The output to display the molecule
     */
    public function getMolecule($width = 350, $height = 400)
    {
        $output = '<script>';
        $output .= "var file1 = new FileCanvas('file1', $width, $height, '".$this->getResourcePath('CDWGetLocalFile.php', 'chemdoodle')."');\n";
        $output .= '</script>';

        return $output;
    }

    private function file2js($f)
    {
        if (ereg('/', $f)){
            $file = $f;
        } else {
            $file = "./molecules/$f.mol";
        }

        if (empty($file)){
            return "File error. File not found.";
        } else {
            $fileContents = file_get_contents($file);
            if ($fileContents){
                // We don't need REMARKs for our examples.  If REMARKS are needed, comment out the following line.
                $fileContents = preg_replace('/REMARK.*\n/', '', $fileContents);
                return "'".str_replace(array("\r\n", "\n", "\r", "'"), array("\\n", "\\n", "\\n", "\\'"), $fileContents)."'";
            } else {
                return "File error. Empty file.";
	    }
        }
    }
}
?>