<?php
/* ----------- data class extends dbTable for tbl_internalmail_config ----------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_internalmail_config
 * @author Kevin Cyster
 */
class dbconfiguration extends dbTable
{
    /**
     * @var string $userId The userId of the current user
     * @access private
     */
    private $userId;

    /**
     * Method to construct the class.
     *
     * @access public
     * @return
     */
    public function init()
    {
        parent::init('tbl_internalmail_config');
        $this->table = 'tbl_internalmail_config';
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to retrieve configs from the data base
     *
     * @access public
     * @return array $data The config data
     */
    public function getConfigs()
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE user_id='".$this->userId."'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $data = $data[0];
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to set configs on the data base
     *
     * @access public
     * @param string $field The config value to change
     * @param string $value The config value
     * @return
     */
    public function setConfig($field, $value)
    {
        $fields = array();
        $fields['user_id'] = $this->userId;
        $fields[$field] = $value;
        $fields['updated'] = date("Y-m-d H:i:s");
        $config = $this->getConfigs();
        if ($config != FALSE) {
            $this->update('id', $config['id'], $fields);
        } else {
            $this->insert($fields);
        }
    }
}
?>