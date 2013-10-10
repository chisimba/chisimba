<HTML>
<HEAD>
  <LINK rel="stylesheet" href="../ChemDoodleWeb.css" type="text/css">
  <SCRIPT type="text/javaSCRIPT" src="../ChemDoodleWeb-libs.js"></SCRIPT>
  <SCRIPT type="text/javaSCRIPT" src="../ChemDoodleWeb.js"></SCRIPT>
  <SCRIPT>
    <?php include('../CDWfile2js.php'); ?>
    threed = <?php file2js('../molecules/3d.mol'); ?>;
  </SCRIPT>
</HEAD>
<BODY>
  <SCRIPT>
    var rotate3D = new RotatorCanvas('rotate3D', 400, 400, true);
    rotate3D.specs.atoms_useJMOLColors = true;
    rotate3D.specs.atoms_circles_2D = true;
    rotate3D.specs.bonds_symmetrical_2D = true;
    rotate3D.specs.backgroundColor = '#E4FFC2';
    rotate3D.loadMolecule(readMOL(threed));
    rotate3D.startAnimation();
  </SCRIPT>
</BODY>
</HTML>
