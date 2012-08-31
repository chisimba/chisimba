<?php

/**
 *
 */
class dbuseractivity extends dbtable {

    function init() {
        parent::init('tbl_useractivity');
        $this->objUser = $this->getObject('user', 'security');
    }

    function getUserActivityByModule($startdate, $enddate, $module, $studentsonly, $usersInContext, $contextcode) {
        $results = array();
        foreach ($usersInContext as $user) {
            $sql =
                    "select count(userid) as accesscount,createdon from tbl_useractivity
        where  (createdon between '$startdate' and '$enddate')
        and contextcode ='$contextcode' and userid='" . $user['auth_user_id'] . "'";

            if ($module != 'all') {
                $sql.=" and module='$module' ";
            }

            $sql.="group by userid order by accesscount";


            $data = $this->getArray($sql);
            $activity = array();
            $activity['userid'] = $user['auth_user_id'];
            if (count($data) > 0) {
                $activity['accesscount'] = $data[0]['accesscount'];
                $activity['lastaccess'] = $data[0]['createdon'];
            } else {
                $activity['accesscount'] = '0';
                $activity['lastaccess'] = 'Never';
            }
            $results[] = $activity;
        }
        $results = $this->subval_sort($results, 'accesscount');
        return $results;
    }

    function getUserActivityById($startdate, $enddate, $module, $userid, $contextcode) {
        $sql =
                "select * from tbl_useractivity
        where  (createdon between '$startdate' and '$enddate')

        and contextcode ='$contextcode' and userid='" . $userid . "' and module='$module' order by createdon";
        return $this->getArray($sql);
    }

    function getToolsActivity($startdate, $enddate, $contextcode, $plugins) {
        $results = array();
        foreach ($plugins as $plugin) {
            $sql =
                    "select count(module) as activitycount from tbl_useractivity where  (createdon between '$startdate' and '$enddate')  and contextcode = '$contextcode' and module = '" . $plugin['module_id'] . "'";

            $data = $this->getArray($sql);
            $row = array();
            $row['activitycount'] = $data[0]['activitycount'];
            $row['module_id'] = $plugin['module_id'];

            $results[] = $row;
        }
        $results = $this->subval_sort($results, 'activitycount');
        return $results;
    }

    function getContextsActivity($startdate, $enddate, $contexts) {
        $results = array();
        foreach ($contexts as $context) {
            $sql =
                    "select count(action) as activitycount from tbl_useractivity where  (createdon between '$startdate' and '$enddate') and contextcode ='" . $context['contextcode'] . "'";
            $data = $this->getArray($sql);
            $row = array();
            $row['activitycount'] = $data[0]['activitycount'];
            $row['contextcode'] = $context['contextcode'];
            $row['owner']= '<a href="mailto:'.$this->objUser->email($context['userid']).'">'.$this->objUser->fullname($context['userid']).'</a>';
            $row['title'] = $context['title'];
            $results[] = $row;
        }
        $results = $this->subval_sort($results, 'activitycount');
        return $results;
    }

    function subval_sort($a, $subkey) {
        foreach ($a as $k => $v) {
            $b[$k] = $v[$subkey];
        }
        arsort($b);

        foreach ($b as $key => $val) {
            $c[$key] = $a[$key];
        }
        return $c;
    }

// End of subval_sort
}

?>
