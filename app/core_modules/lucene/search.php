<?php


/**
 * Description for require_once
 */
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
<?php 
/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 3
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   lucene
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
} ?>