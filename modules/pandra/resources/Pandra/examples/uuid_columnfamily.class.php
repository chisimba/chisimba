<?php
/**
 * Example TimeUUIDType ColumnFamily vs default Cassandra storage-conf.xml
 *
 *  <ColumnFamily CompareWith="TimeUUIDType" Name="StandardByUUID1"/>
 *
 */

require_once('../config.php');
PandraCore::connect('default', 'localhost');

// ---- TIMEUUID ColumnFamily Example

$ks = 'Keyspace1';
$cfName = 'StandardByUUID1';
$keyID = 'PandraTestUUID1';

$cf = new PandraColumnFamily($keyID,
                                $ks,
                                $cfName,
                                PandraColumnFamily::TYPE_UUID);

// generate 5 timestamped columns
for ($i = 1; $i <= 5; $i++) {
    $cf->addColumn(UUID::v1())->setValue($i);
}

echo 'Saving...<br>';
print_r($cf->toJSON());
$cf->save();

// get slice of the 5 most recent entries (count = 5, reversed = true)
echo '<br><br>Loading via CF container...<br>';
$cfNew = new PandraColumnFamily($keyID,
                                    $ks,
                                    $cfName,
                                    PandraColumnFamily::TYPE_UUID);
$cfNew->limit(5)->load();
echo '<br>Loaded...<br>';
print_r($cfNew->toJSON());

echo '<br><br>Loading Slice...<br>';
$result = PandraCore::getCFSlice($ks,
                                    $keyID,
                                    new cassandra_ColumnParent(array(
                                            'column_family' => $cfName
                                    )),
                                    new PandraSlicePredicate(
                                            PandraSlicePredicate::TYPE_RANGE,
                                            array('start' => '',
                                                    'finish' => '',
                                                    'count' => 5,
                                                    'reversed' => true))
                                    );

var_dump($result);

$cfNew = new PandraColumnFamily($keyID,
                                    $ks,
                                    $cfName,
                                    PandraColumnFamily::TYPE_UUID);
$cfNew->populate($result);

echo '<br>Imported...<br>';
print_r($cfNew->toJSON());



?>