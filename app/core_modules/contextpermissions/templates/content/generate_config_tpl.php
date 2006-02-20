<H1><?php echo $title; ?></H1>
<DIV style="margin:50px; padding:10px; border: dashed 1px"><PRE>
<?php
    $decisionTable->retrieve();
    
    $arrConditions = array();
    $arrRules = array();
    foreach( $decisionTable->_arrActions as $objAction ){
        foreach( $objAction->_arrChildren as $objRule ){
            $objRule->retrieve();
            foreach( $objRule->_arrChildren as $objCondition ) {
                $objCondition->retrieve();
                $arrConditions[$objCondition->_name]['params'] = $objCondition->_params;
                $arrConditions[$objCondition->_name]['function'] = $objCondition->_arrFunction;
                $arrRules[$objRule->_name]['CONDITION'][$objCondition->_name] = $objCondition->_name;
                $arrRules[$objRule->_name]['ACTION'][$objAction->_name] = $objAction->_name;
            }
        }
    }
    // Build list of conditions and collect the Groups and Permissions
    $regCondition = '';
    $arrFunctions = array();
    foreach( $arrConditions as $name=>$params ) {
        $_params = ereg_replace(' \| ', '|', $params['params']); // Insert spaces
        $arrFunctions[$params['function']['function']][] = $params['function']['params'];
        $regCondition .= 'CONDITION: '.$name.'|'.$_params."<BR>";
    }
    // Build the groups and permissions
    $regACLs = '';
    $regGroups = '';
    foreach( $arrFunctions as $function=>$params ) {
        switch( $function ) {
            case 'isMember' : 
                $regGroups .= 'USE_GROUPS: ';
                $list = array();
                foreach( $params as  $group ) {
                    if( isset($group[0]) ) $list[] = $group[0];
                }
                $regGroups.= implode( ',', $list)."<BR>";
                break;
            case 'isContextMember' : 
                $regGroups .= 'USE_CONTEXT_GROUPS: ';
                $list = array();
                foreach( $params as  $group ) {
                    if( isset($group[0]) ) $list[] = $group[0];
                }
                $regGroups.= implode( ',', $list)."<BR>";
                break;
            case 'hasPermission' : 
                $list = array();
                $objACL = &$this->getObject( 'permissions_model', 'permissions');
                foreach ( $params as $acl ) {
                    $aclId = $objACL->getId( $acl[0] );
                    $arrGroups = $objACL->getAclGroups($aclId);
                    $groups = array();
                    foreach( $arrGroups as $aGroup ) {
                        $groups[] = $aGroup['name'];
                    }
                    $aclGroups = !empty( $groups ) 
                    ? '|'.implode(',',$groups)
                    : NULL;
                    $acl = ereg_replace( $this->getSession( 'module_name' ).'_', '', $acl[0] );
                    $list[] = 'ACL: '.$acl.$aclGroups."<BR>";
                }
                $regACLs .= implode ( '', $list );

                break;
            case 'hasContextPermission' : break;
        }
    }
    
    // Build the rules 
    $regRule = '';
    foreach( $arrRules as $rule ) {
        $regRule .= 'RULE: '.implode( ',', $rule['ACTION'] ).'|'.implode( ',', $rule['CONDITION'])."<BR>";
    }
    echo $regGroups.'<p>';
    echo $regACLs. '<p>';
    echo $regCondition.'<p>';
    echo $regRule;

?></DIV></PRE>