<?php
/**
 * uses PackageFileManager
 */
require_once 'PEAR/PackageFileManager2.php';
//require_once 'PEAR/PackageFileManager/Svn.php';

include dirname( __FILE__ ) . '/package-config.php';

$package = new PEAR_PackageFileManager2();

$result = $package->setOptions( $options );
if( PEAR::isError( $result ) ) {
    echo $result->getMessage();
    die( __LINE__ . "\n" );
}

$package->setPackage($name);
$package->setSummary($summary);
$package->setDescription($description);

$package->setChannel($channel);
$package->setAPIVersion($apiVersion);
$package->setReleaseVersion($version);
$package->setReleaseStability('stable');
$package->setAPIStability($apiStability);
$package->setNotes($notes);
$package->setPackageType('php'); // this is a PEAR-style php script package
$package->setLicense('GPL', 'http://www.gnu.org/copyleft/gpl.txt');

foreach ($roles as $r) {
	$package->addRole($r['role'], $r['type']);
}
foreach($maintainer as $m) {
    $package->addMaintainer($m['role'], $m['handle'], $m['name'], $m['email'], $m['active']);
}

foreach($dependency as $d) {
    $package->addPackageDepWithChannel($d['type'], $d['package'], $d['channel'], $d['version']);
}
$package->setPhpDep( $require['php'] );
$package->setPearinstallerDep($require['pear_installer']);

$package->generateContents();

$result = $package->writePackageFile();
if (PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}
exit( 0 );
?>