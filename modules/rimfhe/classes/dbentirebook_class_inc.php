<?php
/*
* This is the dbentirebook
* Module
*
*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
/**
 *
 * @package rimfhe
 * @version 0.1
 * @Copyright January 2009
 * @author Joey Akwunwa
 */

class dbentirebook extends dbtable
{
    public $objUrl;
    public $mode;

    //method to define the table
    public function init()
    {
        parent::init('tbl_rimfhe_books');
        $this->objUrl = $this->getObject('url', 'strings');
    }//end init()

    public function entireBook()
    {
        $id = $this->getParam('bookid');//get record id when in edit mode
        $bookname = $this->getParam('bookname');
        $isbn= $this->getParam('isbnnumber');
        $publishinghouse= $this->getParam('publishinghouse');
        $author1= $this->getParam('author1');
        $author2= $this->getParam('author2');
        $author3= $this->getParam('author3');
        $author4= $this->getParam('author4');
        $author1affiliate= $this->getParam('author1affiliate');
        $author2affiliate= $this->getParam('author2affiliate');
        $author3affiliate= $this->getParam('author3affiliate');
        $author4affiliate= $this->getParam('author4affiliate');
        $firstpage= $this->getParam('firstpage');
        $lastpage= $this->getParam('lastpage');
        $pagetotal= ($lastpage - $firstpage) + 1;

        //check which author fieild is not empty
        //Differiate authors affiliated to UWC with bold text
        if (!empty($author1)){

            switch($author1affiliate){
                case 'UWC Staff Member':
                    $author1 ='<b>'.$author1.'</b><br />';
                    $fractionalweightedavg=0.67;
                    break;
                case 'UWC Student':
                    $author1 = '<span>'.$author1.'</span><br />';
                    $fractionalweightedavg=NULL;
                    break;
                case 'External Author':
                    $author1 = $author1.'<br />';
                    $fractionalweightedavg=NULL;
                    break;
            }

        }
        if (!empty($author2)){

            switch($author2affiliate){
                case 'UWC Staff Member':
                    $author2 ='<b>'.$author2.'</b><br />';
                    $fractionalweightedavg=0.67;
                    break;
                case 'UWC Student':
                    $author2 = '<span>'.$author2.'</span><br />';
                    $fractionalweightedavg=NULL;
                    break;
                case 'External Author':
                    $author2 = $author2.'<br />';
                    $fractionalweightedavg=NULL;
                    break;
            }
        }
        if (!empty($author3)){

            switch($author3affiliate){
                case 'UWC Staff Member':
                    $author3 ='<b>'.$author3.'</b><br />';
                    $fractionalweightedavg=0.67;
                    break;
                case 'UWC Student':
                    $author3 = '<span>'.$author3.'</span><br />';
                    $fractionalweightedavg=NULL;
                    break;
                case 'External Author':
                    $author3 = $author3.'<br />';
                    $fractionalweightedavg=NULL;
                    break;
            }
        }
        if (!empty($author4)){

            switch($author4affiliate){
                case 'UWC Staff Member':
                    $author4 ='<b>'.$author4.'</b><br />';
                    $fractionalweightedavg=0.67;
                    break;
                case 'UWC Student':
                    $author4 = '<span>'.$author4.'</span><br />';
                    $fractionalweightedavg=NULL;
                    break;
                case 'External Author':
                    $author4 = $author4.'<br />';
                    $fractionalweightedavg=NULL;
                    break;
            }
        }
        $author = $author1.$author2.$author3.$author3;
        $peerreview= $this->getParam('peerreview');

        $fields =array(
        'booktitle'=> $bookname,
        'isbn'=> $isbn,
        'publishinghouse' => $publishinghouse,
        'authorname' => $author,
        'firstchapterpageno' => $firstpage,
        'lastchapterpageno'=> $lastpage,
        'totalpages'=> $pagetotal,
        'peerreviewed' => $peerreview,
        );
        //if not edite mode, add record tp database
        if(empty($id)){
            //Cheeck if book with same title is already in the database
            $where = "WHERE booktitle='" . $bookname . "'";
            $checkRecord = $this->getAll($where);
            if(count($checkRecord) > 0){
                return FALSE;
            }
            else{
                return $this->insert($fields);
            }
        }
        else{
            //update record
            return $this->update('id', $id, $fields);
        }

    }//end entireBook

    public function getAllEntireBooks()
    {
        return $this->getAll();
    }

    //This public method counts the totall number of Books
    public function totalBooks()
    {
        $query ="SELECT COUNT(*) AS totalbooks FROM tbl_rimfhe_books";
        $result = $this->query($query);
        $return = $result[0]['totalbooks'];
        return $return;
    }
}//end dbstaffmember
?>
