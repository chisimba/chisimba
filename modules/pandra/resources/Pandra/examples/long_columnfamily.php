<?php
/**
 * Example LongType ColumnFamily vs default Cassandra storage-conf.xml
 *
 *  <ColumnFamily Name="StandardByLong1" CompareWith="LongType" />
 *
 * Script performs a save, plus two cross checks - loading directly from an
 * anonymous columnfamily model, and populating a model from a slice.
 *
 */

require_once('../config.php');

PandraCore::connectSeededKeyspace('localhost');

$ks = 'Keyspace1';
$cfName = 'StandardByLong1';
$keyID = 'PandraTestLong1';

$cf = new PandraColumnFamily($keyID, $ks, $cfName, PandraColumnFamily::TYPE_LONG);

$cf->addColumn(PandraCore::getTime())->setValue('numericly indexed!');
echo 'Saving...<br>';
print_r($cf->toJSON());
$cf->save();

// load from model
echo '<br><br>Loading via CF container...<br>';
$cfNew = new PandraColumnFamily($keyID, $ks, $cfName, PandraColumnFamily::TYPE_LONG);

$cfNew->limit(5)->load();
echo '<br>Loaded...<br>';
print_r($cfNew->toJSON());

// get slice
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

$cfNew = new PandraColumnFamily($keyID, $ks, $cfName, PandraColumnFamily::TYPE_LONG);

$cfNew->populate($result);

echo '<br>Imported...<br>';
print_r($cfNew->toJSON());

?>