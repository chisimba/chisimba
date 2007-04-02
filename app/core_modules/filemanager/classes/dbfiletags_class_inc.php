<?
/**
* Class to handle interaction with table tbl_files_filetags
* This table relates to story tags and keywords of files
*
* @author Tohir Solomons
*/
class dbfiletags extends dbTable
{
    
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files_filetags');
    }
    
    /**
    * Method to get the list of tags for a file
    *
    * This function does some processing to strip off all other database columns,
    * and only return the list of tags as an array.
    *
    * @param string $fileId Record Id of the File
    * @return array List of Tags
    */
    public function getFileTags($fileId)
    {
        // Get List of Tags for the files
        $tags = $this->getAll(' WHERE fileid=\''.$fileId.'\' ORDER BY tag');
        
        // Prepare return array
        $results = array();
        
        // Check that item has tags
        if (count($tags) > 0) {
            // Loop through tags
            foreach ($tags as $tag)
            {
                // Add tag to return array
                $results[] = $tag['tag'];
            }
        }
        
        // Return results
        return $results;
    }
    
    /**
    * Method to add a comma separated string as tags for a file
    * @param string $fileId Record Id of the File
    * @param string $tagString Comma Separated List of Tags
    */
    public function addFileTags($fileId, $tagString)
    {
        // Delete existing tags
        $this->delete('fileid', $fileId);
        
        // Convert string to array
        $tags = explode(',', $tagString);
        
        // Create an array of tags added to prevent duplication
        $alreadyAdded = array();
        
        // Check that user entered tags
        if (count($tags) > 0) {
            // Loop through each tag
            foreach ($tags as $tag)
            {
                // Trim whitespace from front and end
                $tag = trim($tag);
                
                // If tag is not equal to 'nothing' and has not been added,
                if ($tag != '' && !in_array(strtolower($tag), $alreadyAdded)) {
                    // Add tag to file
                    $this->addFileTag($fileId, $tag);
                }
                
                // Add to list of already added tags
                $alreadyAdded[] = strtolower($tag);
            }
        }
    }
    
    /**
    * Private Method to store file tag to database
    * @param string $fileId Record Id of the File
    * @param string $tag Tag
    */
    private function addFileTag($fileId, $tag)
    {
        return $this->insert(array('fileid'=>$fileId, 'tag'=>$tag));
    }
    
    function getTagCloudResults($user)
    {
        $sql = 'SELECT tag, count(tag) AS weight FROM tbl_files_filetags INNER JOIN tbl_files ON ( tbl_files.id = tbl_files_filetags.fileid AND tbl_files.userid = \''.$user.'\' ) GROUP BY tag ORDER BY tag';
        return $this->getArray($sql);
    }
    

}

?>