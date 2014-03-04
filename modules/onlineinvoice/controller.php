<?php
/**
 * security check - must be included in all scripts
 */
   
if (!$GLOBALS['kewl_entry_point_run'])     
{
	die("You cannot view this page directly");
}

/*******************************************************************************
/**
* Invoice Controller                                                                 
* This class controls all functionality to run the onlineinvoice module.
* @author Colleen Tinker
* @copyright (c) 2004 University of the Western Cape
* @package invoice
* @version 1
********************************************************************************/

class onlineinvoice extends controller
{
    /** declare variable used within class**/     
           
    /**
      * objUser is an object from the user class, used to hold user information
      * @public 
        
    */
    public  $objUser = null;
    
    /**
      * $objdblodging is an object from the lodging class, used to hold user lodge expense information
      * @public 
        
    */
    
    public $objdblodging = NULL; 
    
    /**
      * $objdbinvoice is an object from the invoice class, used to hold user invoice date information
      * @public 
        
    */    
    public  $objinvdate = NULL;
    
    /**
      * $objdbperdiem is an object from the per diem class, used to hold user per diem information
      * @public 
        
    */ 
    public $objdbperdiem = NULL;
    
    public $submitdatesmsg = ' ';
    
    public  $submitmsg = 'no';
    
    /**
      * $total is an variable to hold the total daily amount for all breakfast, lunch and dinner rates
      * @public 
        
    */
    public $total = NULL;
    

    function init()
    {
    
        /**
         *create objects of the various classes used within this module
         *@author Colleen tinker         
         */
                          
        $this->objLanguage =& $this->getObject('language', 'language');
        
        $this->objUser =& $this->getObject('user', 'security');
        
        $this->objdblodge = & $this->getObject('dblodging','onlineinvoice');
        
        $this->objdbinvoice = & $this->getObject('dbinvoice','onlineinvoice');
        
        $this->objdbtev = & $this->getObject('dbtev','onlineinvoice');
        
        $this->objdbitinerary  = & $this->getObject('dbitinerary','onlineinvoice');
        
        $this->objincident  = & $this->getObject('dbincident','onlineinvoice');
        
        $this->objdbperdiem = & $this->getObject('dbperdiem','onlineinvoice');
        
        $this->objdbsys = & $this->getObject('altconfig','config');
        
        $this->objFile = & $this->getObject('dbfile', 'filemanager');
        //$this->objuploadfile  = & $this->getObject('fileupload','utilities');
        
        $this->objemail = & $this->getObject('kngemail','utilities');
       
        /**
         *pass variables to the template
         */         
        
        $this->setVarByRef('fullname', $this->objUser->fullname());
        $this->userId = $this->objUser->userId();
	     	$this->getObject('sidemenu','toolbar');

       
    }
    
    /**
    	* Method to process actions to be taken
      * @param string $action String indicating action to be taken
    	*/
	
  
    function dispatch($action)
    {
        
        $this->setVar('pageSuppressXML',true);
             
        switch($action){
              
             case 'createinvoice':
                $this->setLayoutTemplate('postlogin_layout_tpl.php');
                return 'postlogin_tpl.php';
              //  return 'createInvoice_tpl.php';   /*  display initial invoice   */
             break;
            
            case 'submitinvoicedates':
              /** call the function that stores date values entered by user into a session variable -- therefore setting the session
                *  return to the invoice template            
                */
                $submit = $this->getParam('submit');
                $edit = $this->getParam('edit');
                if(isset($submit)){
                    $submitdatesmsg = $this->getParam('submitdatesmsg', 'no');
                    $this->setVarByRef('submitdatesmsg', $submitdatesmsg);
                    $this->getInvoicedates();
                    $submitdates = $this->getSession('invoicedata');
                    $this->objdbinvoice->addinvoice($submitdates);
                    //$this->unsetSession('invoicedata'); 
                    $this->setLayoutTemplate('invoice_layout_tpl.php');
                    return 'createInvoice_tpl.php';
                }else{
                     $submitdatesmsg = $this->getParam('submitdatesmsg', 'no');
                     $this->setVarByRef('submitdatesmsg', $submitdatesmsg);
                     $this->unsetSession('invoicedata'); 
                     $this->setLayoutTemplate('invoice_layout_tpl.php');
                     return 'createInvoice_tpl.php';
                }
                
            break;
          
          case 'createtev':                   /*  display the tev voucher   */
                $this->setLayoutTemplate('invoice_layout_tpl.php'); 
                return 'tev_tpl.php';
                break;        
            
          case  'submitclaimantinfo':
                /** call the function that stores date values entered by user into a session variable
                  *  show the next template -- claimant output allows user to edit and save info entered
                  */
                $this->getClaimantdetails();
                $this->setLayoutTemplate('invoice_layout_tpl.php');
                return 'claimoutput_tpl.php';
                //return 'tev_tpl.php';
          break;
          
          case  'claimantoutput':
                $submit = $this->getParam('submit');
                $edit =   $this->getParam('edit');
                
                if(isset($submit)) {
                //submit claimant info to db and show next template, clear the session
                  $claimantdetails = $this->getSession('claimantdata');
                  $this->objdbtev->addclaimant($claimantdetails);
              //    $this->unsetSession('claimantdata');
                  $this->setLayoutTemplate('invoice_layout_tpl.php');
                  return 'itenirarymulti_tpl.php'; 
                }else {
                  //bring claimant form again to edit
                  //$this->unsetSession('claimantdata');
                  $this->setLayoutTemplate('invoice_layout_tpl.php'); 
                  return 'tev_tpl.php';
                }
          break;
/********************************************************************************************************************/            
          case  'submitmultiitinerary':
          /**
           *determines which button to call and which action to perform depending on user selection
           *           
           *$nextsection -- saves the itinerary filled in once / multiple times by user into session variable then goes to per diem template
           *
           *$additinerary -- calls the function to save information for the itinerary into an array 
           *also allows user to add itinerary as many times as needed                                          
           *creates a session variable to hold the information of the itinerary
           *returns back to the itinerary form
           *
           *back -- return to previous template -- tev
           *                      
           *$eixitinerary --  returns the initial template                                             
           */  
              
              $exitinerary  = $this->getParam('exit');
              $additinerary = $this->getParam('add');
              $nextsection  = $this->getParam('next');
              $back         = $this->getParam('back');
              if(isset($nextsection)) {
                    $this->getMultiItinerarydetails();                           
                    $this->setLayoutTemplate('invoice_layout_tpl.php');  
                    return  'itineraryoutput_tpl.php';
              }elseif(isset($additinerary)){
                    $this->getMultiItinerarydetails();
                    $this->setLayoutTemplate('invoice_layout_tpl.php');              
                    return 'itenirarymulti_tpl.php';                                 /** back to this form to fill in another itinerary**/
              }elseif(isset($back)){
                    $this->setLayoutTemplate('invoice_layout_tpl.php');
                    return 'tev_tpl.php';
              }else{
                  $submitdatesmsg = $this->getParam('submitdatesmsg', 'no');
                  $this->setVarByRef('submitdatesmsg', $submitdatesmsg);
                  $this->setLayoutTemplate('invoice_layout_tpl.php');
                  return 'createInvoice_tpl.php';                                    /** change template to return to post-login? **/ 
              }
          break;                 
          
          case 'itineraryoutput' :
            $submit = $this->getParam('submit');
            $edit =   $this->getParam('edit');
                
                if(isset($submit)) {
                //submit itinerary info to db and show next template
                  $itinerarydetails= $this->getSession('addmultiitinerary');
                   foreach($itinerarydetails as $sesItinerary){
                        $this->objdbitinerary->additinerary($sesItinerary);
                   }   
                    
                        //$this->unSetSession('addmultiitinerary');  -- cannot unset session -- date values used in per diem form
                   $this->setLayoutTemplate('invoice_layout_tpl.php');
                   return  'expenses_tpl.php';
               } else {
                //bring intinerary form again to edit
                  $this->unSetSession('addmultiitinerary');
                  $this->setLayoutTemplate('invoice_layout_tpl.php'); 
                  return  'itenirarymulti_tpl.php';
                  
                }
          break;   
/**************************************************************************************************************************/          
          case  'submitexpenses':
             /**
              *determines which button to call depending on user selection
              *$next    --    saves the itinerary filled in once by user into session variable then goes to lodge template
              *$addperdiem -- calls the function to save information for the per diem expenses into an array 
              *also allows user to add per diem as many times as needed                                          
              *creates a session variable to hold the information of the per diem exp selected / entered
              *returns back to the perd diem form
              *$exit    --  returns the initial template                            
              */
              $next = $this->getParam('saveperdiem');
              $addperdiem = $this->getParam('addperdiem');
              $exit = $this->getParam('exit');
              $back = $this->getParam('back');
              if(isset($next)) {
                  $total  =  $this->objdbperdiem->calculate();
                  $finaltotal = $this->objdbperdiem->calcutotal();
                  $this->getPerDiemExpenses($total,$finaltotal);
                  $this->setLayoutTemplate('invoice_layout_tpl.php');
                  return  'perdiemoutput_tpl.php';
              }elseif(isset($addperdiem)){
                  $total  =  $this->objdbperdiem->calculate();
                  $finaltotal = $this->objdbperdiem->calcutotal();
                  $this->getPerDiemExpenses($total,$finaltotal);
                  $this->setLayoutTemplate('invoice_layout_tpl.php');
                  return  'expenses_tpl.php';
              }else{                           
                $submitdatesmsg = $this->getParam('submitdatesmsg', 'no');
                $this->setVarByRef('submitdatesmsg', $submitdatesmsg);
                $this->setLayoutTemplate('invoice_layout_tpl.php');
                return  'itineraryoutput_tpl.php';
              }  
          break;
          
          case 'perdiemoutput':
              $submit = $this->getParam('submit');
              $edit =   $this->getParam('edit');
                
              if(isset($submit)){
                  //submit itinerary info to db and show next template
                  $perdiemdetails = $this->getSession('perdiemdetails');
                    if(!empty($perdiemdetails)){   
                        foreach($perdiemdetails as $sesPerdiem){           // fix insert of date values!!!!
                           $this->objdbperdiem->addperdiem($sesPerdiem);
                            //$this->unSetSession('perdiemdetails');
                            //$this->unSetSession('addmultiitinerary');          // cannot kill session when saving to db as needed for per diem dates
                            //$submitsmsg = $this->getParam('submitdatemsg', 'no');
                        } 
                    //$this->setVarByRef('submitmsg', $submitmsg);
                    $this->setLayoutTemplate('invoice_layout_tpl.php');
                    return  'lodging_tpl.php';
                  }
                }else{
                    //bring intinerary form again to edit
                    $this->unSetSession('perdiemdetails');
                    $this->setLayoutTemplate('invoice_layout_tpl.php'); 
                    return  'expenses_tpl.php';
                  }     
             
          break;
/**************************************************************************************************************************************************/          
          case  'submitlodgeexpenses':
            /**
             *determine which button the user clicks
             *next -- calls the function that saves info into a session variable
             *exit -- leave the form and return to original inv template
             *back -- return to the previous template                                         
             */
             $next  = $this->getParam('next');
             $exit  = $this->getParam('exit');
             $back  = $this->getParam('back');
             $add   = $this->getParam('add');
            
             if(isset($next))  {
                /**
                 *call the function that stores users values into a session variable
                 *display the next template -- lodgereceipt                                  
                 */
                  $finaltotlodge  = $this->objdblodge->calculodgerate();
                  //var_dump($finaltotlodge);
                  //die;
                  $this->getLodgeexpenses($finaltotlodge);
                 // $val  = $this->getSession('lodgedetails');
                  //var_dump($val);
                  $this->setLayoutTemplate('invoice_layout_tpl.php');
                 //return 'incidentinfo_tpl.php';
                 return 'lodgingoutput_tpl.ph.php';
             }elseif(isset($add)){
                /**
                 *call function and return back to lodge template
                 */
                 $finaltotlodge  = $this->objdblodge->calculodgerate();
                 $this->getLodgeexpenses($finaltotlodge);
                 $this->setLayoutTemplate('invoice_layout_tpl.php');
                 return  'lodging_tpl.php';                              
             }
             elseif(isset($back)){
                 $this->setLayoutTemplate('invoice_layout_tpl.php');
                 return  'expenses_tpl.php';                                                                                      
             }else{
                /**
                 *exit the form without submitting the invoice
                 */
                 //$submitdatesmsg = $this->getParam('submitdatesmsg', 'no');
                 //$this->setVarByRef('submitdatesmsg', $submitdatesmsg);
                 $this->setLayoutTemplate('invoice_layout_tpl.php');
                 return 'createInvoice_tpl.php';                                              
             }
          break;
          
          case 'lodgeoutput':
              $submit = $this->getParam('submit');
              $edit =   $this->getParam('edit');
                
                if(isset($submit)) {
                //submit itinerary info to db and show next template
                $lodgedetails = $this->getSession('lodgedetails');
               
                foreach($lodgedetails as $sesLodge){           
                
                  $this->objdblodge->addlodge($sesLodge);
                }   
                //$this->unsetSession('lodgedetails');
                $this->setLayoutTemplate('invoice_layout_tpl.php');
                return 'incidentinfo_tpl.php';
                }else {
                //bring intinerary form again to edit
                $this->unsetSession('lodgedetails');
                $this->setLayoutTemplate('invoice_layout_tpl.php'); 
                return  'lodging_tpl.php';
                  
                }
          break;
/**************************************************************************************************************************************************/          
                    
          case  'submitincidentinfo':
                $next  = $this->getParam('next');
                $exit  = $this->getParam('exit');
                $back  = $this->getParam('back');
                $add   = $this->getParam('add');
                 
               if(isset($next))  {
                /**
                  *call the function that saves all lodge information
                  */                  
                  $finaltotalincident  = $this->objincident->calcutotal();
                  //var_dump($finaltotal);
                  $this->getIncidentinfo($finaltotalincident);
                 // $val = $this->getSession('incidentdetails');
                 // var_dump($val);
                  $this->setLayoutTemplate('invoice_layout_tpl.php');
                  return 'incidentoutput_tpl.php';
                 //  return 'addtravel_tpl.php';
               }elseif(isset($add)) {
                /**
                  *call the function that saves all incident information and return the template
                  */                  
                  $finaltotalincident  = $this->objincident->calcutotal();
                  $this->getIncidentinfo($finaltotalincident);
                  $this->setLayoutTemplate('invoice_layout_tpl.php');
                   return 'incidentinfo_tpl.php';     
               }elseif(isset($exit)){
                 $submitdatesmsg = $this->getParam('submitdatesmsg', 'no');
                 $this->setVarByRef('submitdatesmsg', $submitdatesmsg);
                 //return the next template to upload the files for incident
                 $this->setLayoutTemplate('invoice_layout_tpl.php');
                 return 'createInvoice_tpl.php';
               }else{
                $this->setLayoutTemplate('invoice_layout_tpl.php');
                return  'lodging_tpl.php';
               }                                     
           break;
           
           case 'incidentoutput':
              $submit = $this->getParam('submit');
              $edit =   $this->getParam('edit');
                
              if(isset($submit)) {
                //submit itinerary info to db and show next template
                 $incidentdetails = $this->getSession('incidentdetails');
                    if(!empty($incidentdetails)){               
                         foreach($incidentdetails as $sesIncident){           
                            $this->objincident->addincident($sesIncident);
                         }
                  //      $this->unsetSession('incidentdetails');   
                        $this->setLayoutTemplate('invoice_layout_tpl.php');
                        return 'addtravel_tpl.php';
                    }
              } else {
                        //bring intinerary form again to edit
                        $this->unsetSession('incidentdetails');
                        $this->setLayoutTemplate('invoice_layout_tpl.php'); 
                        return  'incidentinfo_tpl.php';
              }     
                 
           break;
/**************************************************************************************************************************************************/          

          case 'viewtraveloutput':
              $this->setLayoutTemplate('invoice_layout_tpl.php');
              return 'tevoutput_tpl.php';
          break;
/**************************************************************************************************************************************************/          
          
          case  'addanothertev':
              $submitdatesmsg = $this->getParam('submitdatesmsg', 'no');
              $this->setVarByRef('submitdatesmsg', $submitdatesmsg);
              $this->unsetSession('invoicedata');
              $this->unsetSession('perdiemdetails');
              $this->unsetSession('lodgedetails');
              $this->unsetSession('incidentdetails');
              $this->setLayoutTemplate('invoice_layout_tpl.php');
              return 'createInvoice_tpl.php';
          break;
          
          case  'createitinerary':
              $this->unsetSession('addmultiitinerary');  
              $this->setLayoutTemplate('invoice_layout_tpl.php');
              return 'itenirarymulti_tpl.php';
          break;                    
          
          case  'createlodge':
              $this->unsetSession('lodgedetails');
              $this->setLayoutTemplate('invoice_layout_tpl.php');
              return 'lodging_tpl.php';
          break;
          
          case  'showperdiem':
            $this->unsetSession('perdiemdetails');
            $this->setLayoutTemplate('invoice_layout_tpl.php');
            return 'expenses_tpl.php';
          break;            
          
          case  'showincident':
            $this->unsetSession('incidentdetails');
            $this->setLayoutTemplate('invoice_layout_tpl.php');
            return 'incidentinfo_tpl.php';
          break; 
          
          case  'saveexit':
          $save = $this->getParam('save');
          $exit = $this->getParam('exitform');
          if(isset($save)) {
                $this->getSession('invoicedata');
                $this->getSession('claimantdata');
                $this->getSession('addmultiitinerary');
                $this->getSession('perdiemdetails');
                $this->getSession('lodgedetails');
                $this->getSession('incidentdetails');
                $this->setLayoutTemplate('postlogin_layout_tpl.php');
                return 'postlogin_tpl.php';
          }else{
                $this->setLayoutTemplate('postlogin_layout_tpl.php');
                return 'postlogin_tpl.php';
          }
          break;
/**************************************************************************************************************************************************/          
          
          case 'createservice':
              $this->setLayoutTemplate('invoice_layout_tpl.php');
              return 'service_tpl.php';
          break;
          
          case 'displayserviceinfo':
              $this->setLayoutTemplate('invoice_layout_tpl.php');
              return 'serviceinfo_tpl.php';
          case 'initialinvoice':
              $submitdatesmsg = $this->getParam('submitdatesmsg', 'no');
              $this->setVarByRef('submitdatesmsg', $submitdatesmsg);
              //Set layout template
              $this->setLayoutTemplate('invoice_layout_tpl.php');
              return 'createInvoice_tpl.php';
          break; 
          
          
          case  'showinvpending':
            $this->setLayoutTemplate('invoice_layout_tpl.php');          
            return 'invpending_tpl.php';
          break;
                           
            
          default:
            return $this->nextAction('createinvoice', array(NULL));
                
        }
    }
/*******************************************************************************************************************************************************************/                                          
    private function getInvoicedates()
    {
      /**
       *create an array - $invoicedate to store the invoice dates that the user selects
       *create a session variable to store the array data in       
       */
       $username  = $this->objUser->fullname();
       $invoicedate  = array('createdby'    =>  $username,
                             'datecreated'  =>  date('Y-m-d'),
                             'modifiedby'   =>  $this->objUser->fullname(),
                             'datemodified' =>  date('Y-m-d'),
                             'updated'      =>  date('Y-m-d'),
                             'begindate'    =>  $this->getParam('txtbegindate'),
                             'enddate'      =>  $this->getParam('txtenddate'),
                        );
                        
        $this->setSession('invoicedata',$invoicedate);
    }
/*******************************************************************************************************************************************************************/                            
    private function getClaimantdetails()
    {
      /**
       *create an array claimantdetails to store details user enters
       *create a session variable claimantdata to store the array data
       */
       
       $claimantinfo = array ('createdby'     =>    $this->objUser->fullname(),
                             'datecreated'    =>    date('Y-m-d'),
                             'modifiedby'     =>    $this->objUser->fullname(),
                             'datemodified'   =>    date('Y-m-d'),
                             'updated'        =>    date('Y-m-d'),
                             'name'           =>    $this->getParam('txtClaimantName'),
                             'title'          =>    $this->getParam('txtTitle'),
                             'address'        =>    $this->getParam('address'),
                             'city'           =>    $this->getParam('txtCity'),
                             'province'       =>    $this->getParam('txtprovince'),
                             'postalcode'     =>    $this->getParam('txtpostalcode'),
                             'country'        =>    $this->getParam('coutryvals'),
                             'travelpurpose'  =>    $this->getParam('travel')
                        );
       $this->setSession('claimantdata',$claimantinfo);
                       
    }
/*******************************************************************************************************************************************************************/                            
  private function getMultiItinerarydetails()
  {
        /**
       *create array to hold the users itinerary information
       *store array data in session variable ititenrarydata
       */
      $itinerarydata['createdby']    =  $this->objUser->fullname();
      $itinerarydata['datecreated']  =  date('Y-m-d');
      $itinerarydata['modifiedby']   =  $this->objUser->fullname();
      $itinerarydata['datemodified'] =  date('Y-m-d');
      $itinerarydata['updated']      =  date('Y-m-d');
      $itinerarydata['departuredate'] = $this->getParam('txtdeptddate');          //get date user selects -->
      $itinerarydata['departuretime'] = $this->getParam('departuretime') .$this->getParam('minutes') . ':00';
      $itinerarydata['departurecity'] = $this->getParam('txtdeptcity');
      $itinerarydata['arrivaledate']  = $this->getParam('txtarraivaldate');
      $itinerarydata['arrivaltime']   = $this->getParam('arrivaltime'). $this->getParam('minutes') . ':00';
      $itinerarydata['arrivalcity']   = $this->getParam('txtarrivcity');
                     
                    
      $itineraryinfo = $this->getSession('addmultiitinerary');  
      $itineraryinfo[] = $itinerarydata;
      $this->setSession('addmultiitinerary',$itineraryinfo);

  }
/*******************************************************************************************************************************************************************/                          
  private function getPerDiemExpenses($total,$finaltotal)
  {
  
   $brate  = $this->getParam('txtbreakfastRate');
    $lrate  = $this->getParam('txtlunchRate');
    $drate  = $this->getParam('txtdinnerRate');
    
      $perdiemdata = array('createdby'          =>  $this->objUser->fullname(),
                           'datecreated'        =>  date('Y-m-d'),
                           'modifiedby'         =>  $this->objUser->fullname(),
                           'datemodified'       =>  date('Y-m-d'),
                           'updated'            =>  date('Y-m-d'),
                           'foreignordomestic'  =>  $this->getParam('rates_radio'),
                           'date'               =>  $this->getParam('expdate'), // change to the date of depature 
                           'bchoice'            =>  $this->getParam('breakfast'),
                           'blocation'          =>  $this->getParam('txtbreakfastLocation'),
                           'btrate'             =>  $brate,
                           'lchoice'            =>  $this->getParam('lunch'),
                           'llocation'          =>  $this->getParam('txtlunchLocation'),
                           'lRate'              =>  $lrate,
                           'dchoice'            =>  $this->getParam('dinner'),
                           'dlocation'          =>  $this->getParam('txtdinnerLocation'),
                           'drrate'             =>  $drate ,
                           'total'              =>  $total,   
                           'finaltotal'         =>  $finaltotal,    
                           );               
                           
      $perdieminformation =  $this->getSession('perdiemdetails');
      $perdieminformation []  = $perdiemdata;
      $this->setSession('perdiemdetails',$perdieminformation);                          
      
  }
/*******************************************************************************************************************************************************************/
    /**
     *get all lodge information
     * function to add lodge expenses to a session variable
     * @private     
     */
     
    private function getLodgeexpenses($finaltotlodge)
    {
       $lodgedata  = array(
                  'createdby'         =>  $this->objUser->fullname(),
                  'datecreated'       =>  date('Y-m-d'),  
                  'modifiedby'        =>  $this->objUser->fullname(),
                  'datemodified'      =>  date('Y-m-d'),
                  'updated'           =>  date('Y-m-d'),
                  'date'              =>  $this->getParam('txtlodgedate'),     
                  'vendor'            =>  $this->getParam('txtvendor'),
                  'currency'          =>  $this->getParam('lodgecurrency'),
                  'cost'              =>  $this->getParam('txtcost'),
                  'exchangerate'      =>  $this->getParam('txtexchange'),
                  'quotesource'       =>  $this->getParam('txtquotesource'),
                  'exchangefile'      =>  $this->objFile->getFileName($this->getParam('exchangeratefile')),
                  'receiptfilename'   =>  $this->objFile->getFileName($this->getParam('receiptfile')),
                  'affidavitfilename' =>  $this->objFile->getFileName($this->getParam('affidavitfile')),
                  'totroomrate'       =>  $finaltotlodge,
        
     
                );
      $lodgeinformation =  $this->getSession('lodgedetails');
      $lodgeinformation []  = $lodgedata;         
      $this->setSession('lodgedetails',$lodgeinformation);
    }
/*******************************************************************************************************************************************************************/
   private function getIncidentinfo($finaltotalincident)
   {
      $incidentdata = array(
                            'createdby'     =>  $this->objUser->fullname(),
                            'datecreated'   =>  date('Y-m-d'),
                            'modifiedby'    =>  $this->objUser->fullname(),
                            'datemodified'  =>  date('Y-m-d'),
                            'updated'       =>  date('Y-m-d'),
                            'date'          =>  $this->getParam('incidentdate'),
                            'vendor'        =>  $this->getParam('txtvendor'),
                            'description'   =>  $this->getParam('description'),
                            'cost'          =>  $this->getParam('txtcost'),
                            'currency'      =>  $this->getParam('currency'),
                            'exchangerate'  =>  $this->getParam('txtexchange'),
                            'quotesource'   =>  $this->getParam('txtquotesource'),
                            'incidentratefile'  => $this->objFile->getFileName($this->getParam('incidentratefile')),
                            'receiptfiles' =>  $this->objFile->getFileName($this->getParam('incidentreceipt')),
                            'affidavitfiles'     =>  $this->objFile->getFileName($this->getParam('incidentaffidavit')),
                            'inidentexepense'    =>  $finaltotalincident,
                        );
                        
      $incidentinformation =  $this->getSession('incidentdetails');
      $incidentinformation[]  = $incidentdata;                  
      $this->setSession('incidentdetails',$incidentinformation);
   
   }
/*******************************************************************************************************************************************************************/
  private function sendmail()
  {
    //sending email function
    
    //  $from   = 'user@nextgen';
    //  $fromName = 'Robot';
    //  $host='localhost';
      
    //  $this->objemail->setup($from,$fromName,$host);
    //  $this->objemail->sendMail($name, $subject, $email, $body, $html = TRUE, $attachment = NULL, $attachment_descrip=NULL);
      
  }   
   
/*******************************************************************************************************************************************************************/
}

?>
