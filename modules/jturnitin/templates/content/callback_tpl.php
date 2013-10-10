<h1> An Error has been encountered by Turnitin</h1>
<?php

echo "This is the CALLBACK ...<BR>";
				$m = var_export($_REQUEST, true);
				$this->objUtils->send_alert($m);
				error_log($m);
				var_dump($_REQUEST);

?>