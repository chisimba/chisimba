<?php header("Content-type: text/xml; charset=utf-8"); ?>
<?php echo '<?xml version="1.0"?>'; ?>
<?php echo '<?xml-stylesheet type="text/xsl" href="modules/mathml/resources/mathml.xsl"?>'; ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html" />
  <title>MathML</title>
</head>
<body>
<?php
echo $this->getContent();
?>
</body>
</html>