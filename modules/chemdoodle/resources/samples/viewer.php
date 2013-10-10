<HTML>
<HEAD>
  <LINK rel="stylesheet" href="../ChemDoodleWeb.css" type="text/css">
  <SCRIPT type="text/javaSCRIPT" src="../ChemDoodleWeb-libs.js"></SCRIPT>
  <SCRIPT type="text/javaSCRIPT" src="../ChemDoodleWeb.js"></SCRIPT>
  <SCRIPT>
    <?php include('../CDWfile2js.php'); ?>
    caffeine = <?php file2js('../molecules/caffeine.mol'); ?>;
  </SCRIPT>
</HEAD>
<BODY>
  <SCRIPT>
    var view1 = new ViewerCanvas('view1', 100, 100);
    view1.loadMolecule(readMOL(caffeine));
  </SCRIPT>
</BODY>
</HTML>
