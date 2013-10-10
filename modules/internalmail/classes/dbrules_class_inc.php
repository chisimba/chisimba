<?php
/* ----------- data class extends dbTable for tbl_internalmail_rules ----------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_internalmail_rules
 * @author Kevin Cyster
 */
class dbrules extends dbTable
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
        parent::init('tbl_internalmail_rules');
        $this->table = 'tbl_internalmail_rules';
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->dbEmail = $this->getObject('dbemail');
        $this->dbRouting = $this->getObject('dbrouting');
    }

    /**
     * Method to retrieve rules from the data base
     *
     * @access public
     * @return array $data The rule data or false on failure
     */
    public function getRules()
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE user_id='".$this->userId."'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to retrieve a rule from the data base
     *
     * @access public
     * @param string $ruleId The id of the rule to retrieve
     * @return array $data The rule data or false on failure
     */
    public function getRule($ruleId)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE id='".$ruleId."'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $data = $data[0];
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to add rules to the data base
     *
     * @access public
     * @param string $mailAction The mail (1-incoming|2-outgoing)
     * @param string $mailField The field to search (1-to|2-from|3-subject|4-message)
     * @param string $criteria The value to search for
     * @param string $ruleAction The action to perform (1-move|2-copy|3-read)
     * @param string $destFolderId The folder emails must be moved/copied to
     * @return string $ruleId The id of the new rule that was added
     */
    public function addRule($mailAction, $mailField, $criteria, $ruleAction, $destFolderId)
    {
        $fields = array();
        $fields['user_id'] = $this->userId;
        $fields['mail_action'] = $mailAction;
        $fields['mail_field'] = $mailField;
        $fields['criteria'] = $criteria;
        $fields['rule_action'] = $ruleAction;
        $fields['dest_folder_id'] = $destFolderId;
        $fields['updated'] = date("Y-m-d H:i:s");
        $ruleId = $this->insert($fields);
    }

    /**
     * Method to edit rules to the data base
     *
     * @access public
     * @param string $ruleId The id of the rule
     * @param string $mailAction The mail (1-incoming|2-outgoing)
     * @param string $mailField The field to search (1-to|2-from|3-subject|4-message)
     * @param string $criteria The value to search for
     * @param string $ruleAction The action to perform (1-move|2-copy|3-read)
     * @param string $destFolderId The folder emails must be moved/copied to
     * @return
     */
    public function editRule($ruleId, $mailAction, $mailField, $criteria, $ruleAction, $destFolderId)
    {
        $fields = array();
        $fields['user_id'] = $this->userId;
        $fields['mail_action'] = $mailAction;
        $fields['mail_field'] = $mailField;
        $fields['criteria'] = $criteria;
        $fields['rule_action'] = $ruleAction;
        $fields['dest_folder_id'] = $destFolderId;
        $fields['updated'] = date("Y-m-d H:i:s");
        $this->update('id', $ruleId, $fields);
    }

    /**
     * Method to delete rules from the data base
     *
     * @access public
     * @param string $ruleId The id of the rule to delete
     * @return
     */
    public function deleteRule($ruleId)
    {
        $this->delete('id', $ruleId);
    }

    /**
     * Method to apply email rules
     *
     * @access public
     * @param string $folderId The folder the email rules are being applied to
     * @return
     */
    public function applyRules($folderId)
    {
        // only works on new or sent email
        if ($folderId != 'init_1' && $folderId != 'init_3') {
            return FALSE;
        }
        $rules = $this->getRules();
        $emails = $this->dbRouting->getAllMail($folderId, NULL, NULL);
        // only works if there are rules or if there are emails in the folder
        if ($rules == FALSE || $emails == FALSE) {
            return FALSE;
        }
        foreach($rules as $rule) {
            if ($folderId == 'init_1' && $rule['mail_action'] == 1) {
                if ($rule['mail_field'] == NULL) {
                    foreach($emails as $email) {
                        if ($rule['rule_action'] == 1 && $email['read_email'] != 1 && $email['sent_email'] != 1) {
                            $this->dbRouting->moveEmail(array(
                                $email['routing_id']
                            ) , $rule['dest_folder_id']);
                        }
                        if ($rule['rule_action'] == 2 && $email['sent_email'] != 1) {
                            $this->dbRouting->markAsRead($email['routing_id']);
                        }
                    }
                } else {
                    foreach($emails as $email) {
                        $criteria = FALSE;
                        if ($rule['mail_field'] == 1) {
                            $toList = explode('|', $email['recipient_list']);
                            foreach($toList as $to) {
                                $field = $this->objUser->fullname($to);
                                $check = strpos(strtolower($field) , strtolower($rule['criteria']));
                                if ($check !== FALSE) {
                                    $criteria = TRUE;
                                    break;
                                }
                            }
                        } elseif ($rule['mail_field'] == 2) {
                            $field = $this->objUser->fullname($email['sender_id']);
                            $check = strpos(strtolower($field) , strtolower($rule['criteria']));
                            if ($check !== FALSE) {
                                $criteria = TRUE;
                            }
                        } elseif ($rule['mail_field'] == 3) {
                            $field = $email['subject'];
                            $check = strpos(strtolower($field) , strtolower($rule['criteria']));
                            if ($check !== FALSE) {
                                $criteria = TRUE;
                            }
                        } elseif ($rule['mail_field'] == 4) {
                            $field = $email['message'];
                            $check = strpos(strtolower($field) , strtolower($rule['criteria']));
                            if ($check !== FALSE) {
                                $criteria = TRUE;
                            }
                        } elseif ($rule['mail_field'] == 5) {
                            if ($email['attachments'] >= 1) {
                                $criteria = TRUE;
                            }
                        }
                        if ($criteria) {
                            if ($rule['rule_action'] == 1 && $email['read_email'] != 1 && $email['sent_email'] != 1) {
                                $this->dbRouting->moveEmail(array(
                                    $email['routing_id']
                                ) , $rule['dest_folder_id']);
                            }
                            if ($rule['rule_action'] == 2 && $email['sent_email'] != 1) {
                                $this->dbRouting->markAsRead($email['routing_id']);
                            }
                        }
                    }
                }
            } elseif ($folderId == 'init_3' && $rule['mail_action'] == 2) {
                if ($rule['mail_field'] == NULL) {
                    foreach($emails as $email) {
                        if ($rule['rule_action'] == 1 && $email['read_email'] != 1 && $email['sent_email'] == 1) {
                            $this->dbRouting->moveEmail(array(
                                $email['routing_id']
                            ) , $rule['dest_folder_id']);
                        }
                        if ($rule['rule_action'] == 2 && $email['sent_email'] == 1) {
                            $this->dbRouting->markAsRead($email['routing_id']);
                        }
                    }
                } else {
                    foreach($emails as $email) {
                        $criteria = FALSE;
                        if ($rule['mail_field'] == 1) {
                            $toList = explode('|', $email['recipient_list']);
                            foreach($toList as $to) {
                                $field = $this->objUser->fullname($to);
                                $check = strpos(strtolower($field) , strtolower($rule['criteria']));
                                if ($check !== FALSE) {
                                    $criteria = TRUE;
                                    break;
                                }
                            }
                        } elseif ($rule['mail_field'] == 2) {
                            $field = $this->objUser->fullname($email['sender_id']);
                            $check = strpos(strtolower($field) , strtolower($rule['criteria']));
                            if ($check !== FALSE) {
                                $criteria = TRUE;
                            }
                        } elseif ($rule['mail_field'] == 3) {
                            $field = $email['subject'];
                            $check = strpos(strtolower($field) , strtolower($rule['criteria']));
                            if ($check !== FALSE) {
                                $criteria = TRUE;
                            }
                        } elseif ($rule['mail_field'] == 4) {
                            $field = $email['message'];
                            $check = strpos(strtolower($field) , strtolower($rule['criteria']));
                            if ($check !== FALSE) {
                                $criteria = TRUE;
                            }
                        } elseif ($rule['mail_field'] == 5) {
                            if ($email['attachments'] >= 1) {
                                $criteria = TRUE;
                            }
                        }
                        if ($criteria) {
                            if ($rule['rule_action'] == 1 && $email['read_email'] != 1 && $email['sent_email'] == 1) {
                                $this->dbRouting->moveEmail(array(
                                    $email['routing_id']
                                ) , $rule['dest_folder_id']);
                            }
                            if ($rule['rule_action'] == 2 && $email['sent_email'] == 1) {
                                $this->dbRouting->markAsRead($email['routing_id']);
                            }
                        }
                    }
                }
            }
        }
    }
}
?>