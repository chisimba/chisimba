<HTML>
<HEAD>
  <LINK rel="stylesheet" href="../ChemDoodleWeb.css" type="text/css">
  <SCRIPT type="text/javaSCRIPT" src="../ChemDoodleWeb-libs.js"></SCRIPT>
  <SCRIPT type="text/javaSCRIPT" src="../ChemDoodleWeb.js"></SCRIPT>
  <SCRIPT>
    <?php include('../CDWfile2js.php'); ?>
    dna = <?php file2js('../molecules/dna.mol'); ?>;
  </SCRIPT>
</HEAD>
<BODY>
  <SCRIPT>
    var transform1 = new TransformCanvas('transform1', 350, 600, true);
    transform1.specs.bonds_useJMOLColors = true;
    transform1.specs.bonds_width_2D = 3;
    transform1.specs.atoms_display = false;
    transform1.specs.backgroundColor = 'black';
    transform1.loadMolecule(readMOL(dna));
  </SCRIPT>
</BODY>
</HTML>
