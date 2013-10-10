<?php
/**
 * Example TimeUUIDType SuperColumnFamily.  The following new schema is needed
 * for Keyspace1 in Cassandra's storage-conf.xml
 *
 * <ColumnFamily ColumnType="Super"
 *                   CompareWith="TimeUUIDType"
 *                   CompareSubcolumnsWith="BytesType"
 *                   Name="SuperUUID1"
 *                   Comment="A column family with timeuuid ordered supercolumns and bytes type subcolumns"/>
 *
 */

require_once('../config.php');
PandraCore::connect('default', 'localhost');

$ks = 'Keyspace1';
$cfName = 'SuperUUID1';
$keyID = 'PandraTestUUID1';

$scf = new PandraSuperColumnFamily($keyID, $ks, $cfName, PandraColumnContainer::TYPE_UUID);

class BlogPost extends PandraSuperColumn {
    public function init() {
        $this->addColumn('title');
        $this->addColumn('body');
    }
}

// A helper function to pump some random data into our blog entires
// @link http://www.php.net/manual/en/function.rand.php#90773
function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
    // Length of character list
    $chars_length = (strlen($chars) - 1);

    // Start our string
    $string = $chars{rand(0, $chars_length)};

    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string)) {
        // Grab a random character from our list
        $r = $chars{rand(0, $chars_length)};

        // Make sure the same two characters don't appear next to each other
        if ($r != $string{$i - 1}) $string .=  $r;
    }

    // Return the string
    return $string;
}

// generate 5 timestamped supercolumns
for ($i = 1; $i <= 5; $i++) {
    $bp = new BlogPost(UUID::v1());
    $bp->column_title = rand_str();
    $bp->column_body = rand_str();

    $scf->addSuper($bp);
}

echo 'Saving SuperColumnFamily...<br>';
print_r($scf->toJSON());
$scf->save();

// get slice of the 5 most recent entries (count = 5, reversed = true)
echo '<br><br>Loading via SuperColumnFamily container...<br>';
$scNew = new PandraSuperColumnFamily($keyID, $ks, $cfName, PandraColumnContainer::TYPE_UUID);

$scNew->limit(5)->load();

echo '<br>Loaded...<br>';
print_r($scNew->toJSON());

echo '<br><br>Loading SuperColumn Slice...<br>';
$result = PandraCore::getCFSlice($ks,
        $keyID,
        new cassandra_ColumnParent(array(
                'column_family' => $cfName,
        )),
        new PandraSlicePredicate(
        PandraSlicePredicate::TYPE_RANGE,
        array('start' => '',
                'finish' => '',
                'count' => 5,
                'reversed' => true))
);

$scNew = new PandraSuperColumnFamily($keyID, $ks, $cfName, PandraColumnContainer::TYPE_UUID);

var_dump($result);

$scNew->populate($result);

echo '<br>Imported...<br>';
print_r($scNew->toJSON());
?>