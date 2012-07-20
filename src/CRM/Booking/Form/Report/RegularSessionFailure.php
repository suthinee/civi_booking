<?php

require_once 'CRM/Core/Form.php';

/**
 */
class CRM_Booking_Form_Report_RegularSessionFailure extends CRM_Core_Form {


    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ){    /* set defaults for edit page */
        $defaults = array( );
        
        return $defaults;

    }
       

    function buildQuickForm( ){

        $this->addDate( 'log_date', ts('Date'), false, array('formatType' => 'searchDate') );

        $this->addButtons(array( 
                            array ( 'type'  => 'submit', 
                                 'name'      => 'Search', 
                                 'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                 'isDefault' => true )
                                  )); 


      
        $this->addFormRule( array( 'CRM_Booking_Form_Report_RegularSessionFailure', 'formRule' ), $this );
 
    }

    function postProcess(){ 
        $params = $this->controller->exportValues($this->_name);
        $date = $params['log_date'];
        

        $qParams = array( 1 => array($date, 'String'));     

        $query = "SELECT l.*,
                         a.*,
                         cc.*,
                         con.*
                FROM civi_booking_failure_log l
                LEFT JOIN civicrm_case_activity ca ON ca.activity_id = l.entity_id
                LEFT JOIN civicrm_activity a ON a.id = ca.activity_id
                LEFT JOIN civicrm_case c ON c.id = ca.case_id
                LEFT JOIN civicrm_case_contact cc ON cc.case_id = c.id
                LEFT JOIN civicrm_contact con ON con.id = cc.contact_id
                WHERE DATE_FORMAT(l.created_time,'%m/%d/%Y') = %1
                AND l.type = 'automate_regular_session'
                ORDER BY l.created_time DESC";

      civicrm_initialize( ); 
      require_once('CRM/Core/DAO.php');   
      $dao = CRM_Core_DAO::executeQuery( $query, $qParams);
      $results = array();
      $i = 0;
      while ( $dao->fetch( ) ) {
        $results[] = $dao->toArray();
        $actDateTime = $results[$i]['activity_date_time'];
        $results[$i]['activity_date_time'] = date('d-m-Y g:iA', strtotime($actDateTime));
        $i++;
      }

      $this->assign('logs', $results);    
    }

    /**
     * global validation rules for the form
     *
     * @param array $values posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @access public
     */
    

                  
    function formRule( $values, $files, $form )  {

        $errors = array();
        $logDate = CRM_Utils_Array::value( 'log_date', $values );
       
        if ( $logDate == null || $logDate == '' ) {
          $errors['log_date'] = ts( 'Date is required' );
        } 
        return empty( $errors ) ? true : $errors; 
    
    /*}*/
    }


    static function automate_generate_regular_session(){
    $datetime = date('Y-m-d H:i:s');
    $params = array( 1 => array($datetime, 'String'));     
    $query = "SELECT c.id as case_id, 
                      a.id as activity_id,
                      a.*,
                      rs.*,
                      ai.*,
                      s.*
                FROM civicrm_case c
                LEFT JOIN civicrm_case_activity ca ON ca.case_id = c.id
                LEFT JOIN civicrm_activity a ON a.id = ca.activity_id
                LEFT JOIN civicrm_value_repeat_session_12 rs ON rs.entity_id = a.id
                LEFT JOIN civicrm_value_additional_activity_info_8 ai ON ai.entity_id = a.id
                LEFT JOIN civi_booking_slot s ON s.id = ai.slot_id_34
                WHERE c.is_deleted = 0
                AND c.status_id <>2
                AND a.activity_type_id = 52
                AND a.is_current_revision = 1
                AND a.is_deleted = 0
                AND a.activity_date_time = (SELECT max(sa.activity_date_time)
                                    FROM civicrm_activity sa 
                                    LEFT JOIN civicrm_case_activity sca ON sca.activity_id = sa.id
                                    WHERE sca.case_id = c.id
                                    AND sa.activity_type_id = 52
                                    AND sa.is_current_revision = 1
                                    AND sa.is_deleted = 0)
                AND a.activity_date_time > %1
                AND rs.final_assessment_47 is null
                ORDER BY a.activity_date_time DESC";

      civicrm_initialize( ); 
      require_once('CRM/Core/DAO.php');   
      $dao = CRM_Core_DAO::executeQuery( $query, $params);
      $results = array();
      while ( $dao->fetch( ) ) {
        $results[] = $dao->toArray();
      }
      require_once('CRM/Booking/BAO/Slot.php');   

      $weeksForward = 5; //default to roll forward 
     // $activities = array();
      $uncreatableList = array();

      foreach ($results as $key => $value) {

        $slotDate = CRM_Utils_Array::value('slot_date',$value);

        $nextFiveWeek = strtotime("+ 5 week",strtotime($slotDate));
        if(strtotime($slotDate) > $nextFiveWeek ){
          //  continue;
        }

        $caseId = CRM_Utils_Array::value('case_id', $value);
        $activityId = CRM_Utils_Array::value('activity_id', $value);
        $slotId = CRM_Utils_Array::value('slot_id_34',$value);
        $startTime =  CRM_Utils_Array::value('start_time',$value); 
        $endTime =  CRM_Utils_Array::value('end_time',$value);
        $clinician_1 =  CRM_Utils_Array::value('clinician_contact_id',$value);
        $clinician_2 =  CRM_Utils_Array::value('attended_clinician_contact_id',$value);

        $isActiviyCreable = false;
        $slotIds = array();
        for($i = 0; $i < $weeksForward; $i++){ 
            $nextSevenDay = strtotime("+". $i + 1 . " week",strtotime($datetime));
            $nd = date('Y-m-d H:i:s', $nextSevenDay);
            $params = array( 1 => array($nd,'String'),
                         2 => array($startTime,'String'),
                         3 => array($endTime,'String'),
                         4 => array($clinician_1,'String'),
                         5 => array($clinician_2,'String'));
            $query = "SELECT id
                       FROM civi_booking_slot s
                       WHERE s.slot_date = %1
                       AND s.start_time = %2
                       AND s.end_time = %4
                       AND s.clinician_contact_id = %4
                       AND s.activity_type = 52
                    ";

            civicrm_initialize( ); 
            require_once('CRM/Core/DAO.php'); 
            $slotId    = CRM_Core_DAO::singleValueQuery( $query, $params );
            if(!$slotId){
                $isActiviyCreable = false;
                $slotIds = array();
                $uncreatableList[] = array('activity_id' => $activityId,
                                           'description' => 'no slots found'
                                           );
                break;
            }else{
                $isActiviyCreable = true;
                $slotIds[$i] = $slotId;
            }
        }

        if($isActiviyCreable && sizeof($slotIds) == 5){
            foreach ($slotIds as $key => $slodId) { civicrm_initialize( );
               require_once 'CRM/Booking/BAO/Slot.php';
               CRM_Booking_BAO_Slot::setSlotStatus(array('slot_id' => $slotId ,'status' => 2)); //set status to 2 to reserved 
 
               $slotResults = CRM_Booking_BAO_Slot::getSlotById($slotId);
               $slot = $slotResults[0]; //getSlotId
               
               $startTime = (CRM_Utils_Array::value('start_time',$slot)); 
               $endTime   = (CRM_Utils_Array::value('end_time',$slot)); 

               //Convert date to slot timestamp
               $slotTimestamp = strtotime(CRM_Utils_Array::value('slot_date',$slot));

               //Convert timestamp to date formate thst CiviCRM uses.
               $params['activity_date_time'] =  date('m/d/Y', $slotTimestamp);
               $params['activity_date_time_time'] = date('g:iA', strtotime($startTime));

               //process date for proper formate
               $params['activity_date_time'] = CRM_Utils_Date::processDate( $params['activity_date_time'], $params['activity_date_time_time'] );
                 
               //set assignee contact ids
               $params['assignee_contact_id'] = array();
               $params['assignee_contact_id'][0] = CRM_Utils_Array::value('clinician_contact_id',$slot);
               if(CRM_Utils_Array::value('attended_clinician_contact_id',$slot)){
                 $params['assignee_contact_id'][1] = CRM_Utils_Array::value('attended_clinician_contact_id',$slot);
               }
               
               //set activity location
               $params['location'] = CRM_Utils_Array::value('centre',$slot);
               $params['details'] = 'Room no: ' . CRM_Utils_Array::value('room_no',$slot);
               $params['duration'] = round(abs(strtotime($endTime) - strtotime($startTime)) / 60,2);               

                  //TODO: add session service
               //hacked adding session service for custom field'
               $params['custom_16_-1'] =  CRM_Utils_Array::value('session_service',$slot);
               $params['custom']['16']['-1']['value'] =  CRM_Utils_Array::value('session_service',$slot);

               $params['custom_22_-1'] = CRM_Utils_Array::value('centre',$slot);
               $params['custom']['22']['-1']['value'] = CRM_Utils_Array::value('centre',$slot);
               
               
               //slotId
               $params['custom_34_-1'] = CRM_Utils_Array::value('id',$slot);
               $params['custom']['34']['-1']['value'] = CRM_Utils_Array::value('id',$slot);
            
               $params['subject'] = 'Initial appointment';
               $params['source_contact_id'] = 102; //TODO: Hook to use contact Id use system admin as default
               $params['activity_type_id'] = 52;
               $params['status_id'] = 1;
               $params['priority_id'] = 2;
               $params['case_id'] = $caseId;

               $params['version'] = 3;
               $params['check_permissions'] = FALSE;

               require_once 'api/api.php';
               $results = civicrm_api("Activity","create", $params);

               if($results['is_error'] == 1){
                  CRM_Booking_BAO_Slot::setSlotStatus(array('slot_id' => $slotId ,'status' => 1)); //set status to 2 to availables 
                  //TODO: Addd activity uncreableList if activity cannot be created with reason
                  $uncreatableList[] = array('activity_id' => $activityId,
                                             'description' => $results['error_message']
                                            );

               }
            }
        }
        
      }
     if(sizeof($uncreatableList) != 0){
        $query= db_insert('civi_booking_failure_log')
              ->fields(array(
                      'entity_id',
                      'created_time',
                      'data',
                      'type'
                     
            ));
          $txn = db_transaction();
          try{
            foreach ($uncreatableList as $obj) {
              $record = array('entity_id' => $obj['activity_id'],
                              'created_time' => $datetime,
                              'data' => $obj['description'],
                              'type' => 'automate_regular_session');
              $query->values($record);
            }
            $query->execute();
            $results = array('is_created' => 1);
          }catch(Exception $e){
            //TODO: Implement proper exception handler
            dump($e->getMessage());
          }
     }

    }

}

