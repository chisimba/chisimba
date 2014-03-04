<?php
/**
 * Example TimeUUIDType SuperColumn.  The following new schema is needed
 * for Keyspace1 in Cassandra's storage-conf.xml
 *
 * <ColumnFamily ColumnType="Super"
 *                   CompareWith="BytesType"
 *                   CompareSubcolumnsWith="TimeUUIDType"
 *                   Name="SuperColumnUUID1"
 *                   Comment="A column family with supercolumns containing time uuid ordered columns
 *
 */

require_once('../config.php');
PandraCore::connect('default', 'localhost');

$ks = 'Keyspace1';
$cfName = 'SuperColumnUUID1';
$keyID = 'PandraTestUUID1';

$superName = 'Super1';

// Create a TimeUUID supercolumn
$sc = new PandraSuperColumn($superName, $keyID, $ks, NULL, PandraColumnContainer::TYPE_UUID);
$sc->setColumnFamilyName($cfName);

// generate 5 timestamped columns
for ($i = 1; $i <= 5; $i++) {
    $sc->addColumn(UUID::v1())->setValue($i);
}

echo 'Saving SuperColumn...<br>';
print_r($sc->toJSON());
$sc->save();

// get slice of the 5 most recent entries (count = 5, reversed = true)
echo '<br><br>Loading via SuperColumn container...<br>';
$scNew = new PandraSuperColumn($superName, $keyID, $ks, NULL, PandraColumnContainer::TYPE_UUID);
$scNew->setColumnFamilyName($cfName);

$scNew->limit(5)->load();
echo '<br>Loaded...<br>';
print_r($scNew->toJSON());

echo '<br><br>Loading SuperColumn Slice...<br>';
$result = PandraCore::getCFSlice($ks,
                                    $keyID,
                                    new cassandra_ColumnParent(array(
                                            'column_family' => $cfName,
                                            'super_column' => $superName
                                    )),
                                    new PandraSlicePredicate(
                                            PandraSlicePredicate::TYPE_RANGE,
                                            array('start' => '',
                                                    'finish' => '',
                                                    'count' => 5,
                                                    'reversed' => true))
                                    );

var_dump($result);

$scNew = new PandraSuperColumn($superName, $keyID, $ks, NULL, PandraColumnContainer::TYPE_UUID);
$scNew->setColumnFamilyName($cfName);

$scNew->populate($result);

echo '<br>Imported...<br>';
print_r($scNew->toJSON());
?>