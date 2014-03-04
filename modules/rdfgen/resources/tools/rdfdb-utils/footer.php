<?php

// ----------------------------------------------------------------------------------
// RDFDBUtils : Footer 
// ----------------------------------------------------------------------------------

/** 
 * At the bottom of each page
 * 
 * @version $Id: footer.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/


if (isset($db)) { $db->close(); } 


?>

<div class="footer">
<hr> 
RDF DB Utils - a part of <a href="http://www.wiwiss.fu-berlin.de/suhl/bizer/rdfapi/index.html">RAP</a><br/>
Written by <a href="http://www.csd.abdn.ac.uk/~ggrimnes/">Gunnar Grimnes</a>
</div>

</body>
</html>

