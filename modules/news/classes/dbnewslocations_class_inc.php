<?php

class dbnewslocations extends dbtable
{

    public function init()
    {
        parent::init('tbl_news_locations');
        
        $this->loadClass('treemenu','tree');
		$this->loadClass('treenode','tree');
		$this->loadClass('htmllist','tree');
		$this->loadClass('htmldropdown','tree');
        
        $this->objUser = $this->getObject('user', 'security');
    }
    
    public function getLocation($id)
    {
        return $this->getRow('id', $id);
    }
    
    public function getLocationsTree($name='parentlocation')
    {
        $locations = $this->getAll('ORDER BY level, location');
        
        $nodeArray = array();
        
        $treeMenu = new treemenu();
        $rootnode =& new treenode (array('text'=>'[- Root -]'));
        
        foreach ($locations as $location)
        {
            $nodeDetails = array('text'=>htmlentities($location['location']), 'link'=>$location['id']);
            
            $node =& new treenode ($nodeDetails);
            $nodeArray[$location['id']] =& $node;
            
            if ($location['location_parent'] == 'root') {
                $rootnode->addItem($node);
            } else {
                if (array_key_exists($location['location_parent'], $nodeArray)) {
                    $nodeArray[$location['location_parent']]->addItem($node);
                }
            }
        }
        
        $treeMenu->addItem($rootnode);
        
        $tree = &new htmldropdown($treeMenu, array('inputName'=>$name, 'id'=>'input_parentlocation'));
        
        return $tree->getMenu();
    }
    
    /**
    *
    *
    */
    public function addLocation($location, $parentLocation, $locationType=NULL, $locationImage=NULL, $latitude=NULL, $longitude=NULL, $zoomlevel=NULL, $viewbounds=NULL, $currentcenter=NULL)
    {
        $location = trim(stripslashes($location));
        
        if ($location == '') {
            return 'emptystring';
        } else {
            $parentLevel = $this->getParentLevel($parentLocation);
            
            if ($parentLevel === FALSE) {
                return 'parentdoesnotexist';
            }
            $level = $parentLevel+1;
            
            $lastRight = $this->getLastRight($parentLocation);
            $leftPointer = $lastRight;
            
            if ($parentLocation == '') {
                $leftPointer++;
            }
            $rightPointer = $leftPointer+1;
            
            if ($parentLocation == '') {
                $pageOrder = 1;
                $parentLocation = 'root';
            } else {
                $this->updateLeftRightPointers($lastRight-1);
            }
            
            return $this->saveLocation($location, $parentLocation, $locationType, $level, $locationImage, $latitude, $longitude, $zoomlevel, $viewbounds, $currentcenter, $leftPointer, $rightPointer);
        }
    }
    
    /**
    *
    *
    */
    private function saveLocation($location, $parentLocation, $locationType, $level, $locationImage, $latitude, $longitude, $zoomlevel, $viewbounds, $currentcenter, $leftPointer, $rightPointer)
    {
        return $this->insert(array(
            'location' => $location,
            'location_parent' => $parentLocation,
            'location_type' => $locationType,
            'level' => $level,
            'locationImage' => $locationImage,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'latituderad' => deg2rad($latitude),
            'longituderad' => deg2rad($longitude),
            'zoomlevel' => $zoomlevel,
            'latlongcenterbounds' => $viewbounds,
            'latlongcenter' => $currentcenter,
            'lft' => $leftPointer,
            'rght' => $rightPointer,
            'creatorid' => $this->objUser->userId(),
            'datecreated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));
    }
    
    private function updateLeftRightPointers($base, $amount=2)
    {
        $sqlLeft = 'UPDATE tbl_news_locations SET rght=rght+'.$amount.' WHERE rght > '.$base;
        $sqlRight = 'UPDATE tbl_news_locations SET lft=lft+'.$amount.' WHERE lft > '.$base;
        
        $this->query($sqlLeft);
        $this->query($sqlRight);
    }
    
    private function getLastRight($parent='')
    {
        if ($parent == '') {
            $result = $this->getAll(' ORDER BY rght DESC LIMIT 1');
        } else {
            $result = $this->getAll('WHERE id =\''.$parent.'\' ORDER BY rght DESC LIMIT 1');
        }
        
        
        if (count($result) == 0) {
            return 0;
        } else {
            return $result[0]['rght'];
        }
    }
    
    private function getParentLevel($parent)
    {
        if ($parent == '' || $parent == NULL) {
            return 0;
        } else {
            $parentRow = $this->getRow('id', $parent);
            
            if ($parentRow == FALSE) {
                return FALSE;
            } else {
                return $parentRow['level'];
            }
        }
    }
    
    

}
?>