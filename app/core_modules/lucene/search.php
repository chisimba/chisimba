<?php
    require_once('resources/Search/Lucene.php');

    $query = isset($_GET['query']) ? $_GET['query'] : '';
    $query = trim($query);

    $indexPath = '/var/www/phpman/';

    $index = new Zend_Search_Lucene($indexPath);
//var_dump($index);

    if (strlen($query) > 0) {
        $hits = $index->find($query);
       // print_r($hits);
        $numHits = count($hits);
    }
?>

<form method="get" action="search.php">
    <input type="text" name="query" value="<?= htmlSpecialChars($query) ?>" />
    <input type="submit" value="Search" />
</form>

<?php if (strlen($query) > 0) { ?>
    <p>
        Found <?= $numHits ?> result(s) for query <?= $query ?>.
    </p>

    <?php foreach ($hits as $hit) {
    	//var_dump($hit);
    	?>
        <h3><?= $hit->title ?> (score: <?= $hit->score ?>)</h3>
        <p>
            content: <?//= $hit->contents ?>
        </p>
        <p>
            <?//=// $hit->teaser ?><br />
            <a href="<?//= //$hit->url ?>">Read more...</a>
        </p>
    <?php } ?>
<?php } ?>