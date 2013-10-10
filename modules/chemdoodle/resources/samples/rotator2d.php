<HTML>
<HEAD>
  <LINK rel="stylesheet" href="../ChemDoodleWeb.css" type="text/css">
  <SCRIPT type="text/javaSCRIPT" src="../ChemDoodleWeb-libs.js"></SCRIPT>
  <SCRIPT type="text/javaSCRIPT" src="../ChemDoodleWeb.js"></SCRIPT>
  <SCRIPT>
    <?php include('../CDWfile2js.php'); ?>
    larger = <?php file2js('../molecules/larger.mol'); ?>;
  </SCRIPT>
</HEAD>
<BODY>
  <SCRIPT>
    var rotate2D = new RotatorCanvas('rotate2D', 300, 300);
    rotate2D.specs.atoms_useJMOLColors = true;
    rotate2D.loadMolecule(readMOL(larger));
    rotate2D.startAnimation();
  </SCRIPT>
</BODY>
</HTML>
