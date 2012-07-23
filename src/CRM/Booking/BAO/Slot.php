<?php

class CRM_Booking_BAO_Slot{
     /**
     * class constructor
     */
    function __construct( ) {

    }

    static function isSlotCreatable($args){

      $roomId = $args['roomId'];

      //civicrm_initialize( );
      $params = array(
        1 => array( $args['date'], 'String'),
        2 => array($args['startTime'], 'String'),
        3 => array($args['endTime'], 'String'),
        4 => array($args['contactId'], 'Integer')
      );
      $query = "SELECT civi_booking_slot.id
            FROM civi_booking_slot 
            WHERE civi_booking_slot.slot_date  = %1
            AND civi_booking_slot.clinician_contact_id = %4
            AND (civi_booking_slot.start_time BETWEEN %2 AND %3 OR civi_booking_slot.end_time BETWEEN %2 AND %3)";

      require_once('CRM/Core/DAO.php');   
      $dao = CRM_Core_DAO::executeQuery( $query , $params );
      $results = array ();
      while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();   
       } 
      if(!is_null($args['contactId2'])){
         if(empty($results) && !$args['contactId2'] == ''){
           $params = array(
            1 => array( $args['date'], 'String'),
            2 => array($args['startTime'], 'String'),
            3 => array($args['endTime'], 'String'),
            4 => array($args['contactId2'], 'Integer')
          );
          /*$query = "SELECT civi_booking_slot.id
                FROM civi_booking_slot 
                WHERE civi_booking_slot.slot_date  = %1
                AND civi_booking_slot.attended_clinician_contact_id = %4
                AND (civi_booking_slot.start_time BETWEEN %2 AND %3 OR civi_booking_slot.end_time BETWEEN %2 AND %3)"; */

          //require_once('CRM/Core/DAO.php');   
          $dao = CRM_Core_DAO::executeQuery( $query , $params );
           while ( $dao->fetch( ) ) {
              $results[] = $dao->toArray();   
           } 
         }
       }
      ///Checked unavaiability clinicain
      $date = strtotime(($args['date']));
      $stime = strtotime(($args['startTime'])) ;
      $etime = strtotime(($args['endTime'])) ;

      $st = date('Y-m-d', $date) . ' ' . date('H:i:s', $stime);
      $et = date('Y-m-d', $date) . ' ' . date('H:i:s', $etime);
      $params = array(1 => array($args['contactId'], 'Integer'),
                      2 => array($stime, 'String'),
                      3 => array($etime, 'String'));
     $query = "SELECT cu.id,
                       cu.entity_id,
                       cu.unavailable_start_date__time_41,
                       cu.unavailable_end_date__time_42
                FROM  civicrm_value_clinician_unavailability_14 cu
                WHERE cu.entity_id = %1
                AND (cu.unavailable_start_date__time_41 BETWEEN %2 AND %3 OR cu.unavailable_end_date__time_42 BETWEEN %2 AND %3)";
      //require_once('CRM/Core/DAO.php');   
      $dao = CRM_Core_DAO::executeQuery( $query , $params );
      $results = array();
      while ( $dao->fetch( ) ) {
            $results[] = $dao->toArray();   
      }
      if(!is_null($args['contactId2'])){
         if(empty($results) && !$args['contactId2'] == ''){
             ///Checked unavaiability clinicain
            $params = array(1 => array($args['contactId2'], 'Integer'),
                            2 => array($stime, 'String'),
                            3 => array($etime, 'String'));
                        

            //require_once('CRM/Core/DAO.php');   
            $dao = CRM_Core_DAO::executeQuery( $query , $params );
            $results = array();
            while ( $dao->fetch( ) ) {
                  $results[] = $dao->toArray();   
            }
         }
       }
       if(empty($results)){
          require_once('CRM/Booking/BAO/Room.php');   
          $results = CRM_Booking_BAO_Room::getRoomById($roomId, $status = 0); //check if the room is unactive
       }
       //TODO
       //1: Check if the room is active
      return $results;
    }

    static function getSlots($startDate = null, $endDate = null,  $activityType = 0, $status = 0 ){  
      $query = "SELECT civi_booking_slot.id as id,
               con1.display_name as display_name,
               con2.display_name as attended_clinician_name,
               clinician_contact_id, 
               attended_clinician_contact_id,
               start_time,  
               end_time, 
               room_no,
               room_id,
               slot_date,
               session_service,
               activity_type,
               status,
               description
        FROM civi_booking_slot
        LEFT JOIN civi_booking_room ON civi_booking_room.id = civi_booking_slot.room_id
        LEFT JOIN civicrm_contact con1 ON con1.id = civi_booking_slot.clinician_contact_id
        LEFT JOIN civicrm_contact con2 ON con2.id = civi_booking_slot.attended_clinician_contact_id
        WHERE 1";
      
      if($status != 0){
        $query .= "\n AND civi_booking_slot.status = %4";
      }

      if(isset($startDate) && isset($endDate)){  
        $query .= "\n AND civi_booking_slot.slot_date BETWEEN %1 AND %2";
      }  
      if(isset($activityType) && $activityType != 0){
        $query .= "\n AND civi_booking_slot.activity_type = %3";
      }
     // }else if($activityType == 0){
     //   $query .= "\n AND civi_booking_slot.activity_type <> 0";
    //  }
      $params = array(
        1 => array( $startDate, 'String'),
        2 => array( $endDate, 'String'),
        3 => array( $activityType, 'Integer'),
        4 => array( $status, 'Integer')
      ); 
      require_once('CRM/Core/DAO.php'); 
      $dao = CRM_Core_DAO::executeQuery( $query,  $params );
      $results = array ();
      while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
      }
      return $results;
    }

   /**
     * Reserve slot record
     *
     * @param array    slot_id, stautus
     *
     * @return null
     * @access public
     */
    static function setSlotStatus($params){
    
       if ( !$params['slot_id'] && !$params['status'] ) {
            return;
       }

    $query = "
UPDATE civi_booking_slot
SET status = %1    
WHERE id = %2
";
 
      $params = array( 1  => array( $params['status'], 'Integer' ),
                     2  => array( $params['slot_id'], 'Integer' ));
      CRM_Core_DAO::executeQuery( $query, $params );
    }

    static function deleteSlot($id){
      if(!isset($id)){
        return 0;
      }
      $params = array(1 => array( $id, 'Integer'));
      $query = "
        DELETE FROM civi_booking_slot
        WHERE id = %1
        ";
      $dao = CRM_Core_DAO::executeQuery( $query,  $params );
      return $dao;
    }


    static function getSlotStatus($id){
      if(!isset($id)){
        return 0;
      }
      $params = array(1 => array( $id, 'Integer'));
      $query = "SELECT s.status as status           
        FROM civi_booking_slot s
        WHERE s.id = %1";
         
      
      require_once('CRM/Core/DAO.php'); 
      return CRM_Core_DAO::singleValueQuery( $query,  $params );
    }

    static function getActivityType($id){
      $params = array(1 => array( $id, 'Integer'));
      $query = "SELECT s.activity_type as type           
        FROM civi_booking_slot s
        WHERE s.id = %1";
         
      
      require_once('CRM/Core/DAO.php'); 
      return CRM_Core_DAO::singleValueQuery( $query,  $params );

    }

    static function getSlotAttendee($id){
      $params = array(1 => array( $id, 'Integer'));
      $query = "SELECT c.*
          FROM civi_booking_slot s
          LEFT JOIN civi_booking_slot_attendee sa ON sa.slot_id = s.id
          LEFT JOIN civicrm_contact c ON c.id = sa.contact_id
          WHERE s.id = %1";     
      
      require_once('CRM/Core/DAO.php'); 
      $dao = CRM_Core_DAO::executeQuery( $query,  $params );
      $results = array ();
      while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
      }
      return $results;
    }
    
    static function getSlotById($id, $type = 1){
      if(!isset($id)){
        return;
      }
      $params = array(1 => array( $id, 'Integer'));
      if($type == 1){
        $query = "SELECT s.id as id, 
                        s.start_time as start_time,
                        s.end_time as end_time,
                        s.slot_date as slot_date, 
                        s.room_id as room_id,
                        r.room_no as room_no,
                        s.session_service as session_service,
                        s.clinician_contact_id as clinician_contact_id,
                        s.attended_clinician_contact_id as attended_clinician_contact_id,
                        ov.label as activity_type,
                        s.status as status,
                        s.description as description,
                        r.building as centre,
                        con1.display_name as clinician_contact_display_name,
                        con2.display_name as attended_clinician_contact_display_name
          FROM civi_booking_slot s
          LEFT JOIN civi_booking_room r ON s.room_id = r.id
          LEFT JOIN civicrm_contact con1 ON con1.id = s.clinician_contact_id
          LEFT JOIN civicrm_contact con2 ON con2.id = s.attended_clinician_contact_id
          JOIN civicrm_option_value ov ON ov.value = s.activity_type AND ov.option_group_id = 2
          WHERE s.id = %1";
         
      }else{
         $query = "SELECT s.*,
                        r.*
          FROM civi_booking_slot s
          LEFT JOIN civi_booking_room r ON s.room_id = r.id
          WHERE s.id = %1";     
      }
      require_once('CRM/Core/DAO.php'); 
      $dao = CRM_Core_DAO::executeQuery( $query,  $params );
      $results = array ();
      while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
      }
      return $results;
    }

     static function getSlotsByContact($id, $startDate = null, $endDate = null){
      if(!isset($id)){
        return;
      }
      $params = array(1 => array( $id, 'Integer'));
      $query = "SELECT civi_booking_slot.id, 
                      start_time,
                      end_time,
                      slot_date, 
                      room_no,
                      session_service,
                      clinician_contact_id,
                      attended_clinician_contact_id,
                      status,
                      civi_booking_room.building as centre
        FROM civi_booking_slot
        LEFT JOIN civi_booking_room ON civi_booking_slot.room_id = civi_booking_room.id
        WHERE civi_booking_slot.clinician_contact_id = %1";
         
      
      require_once('CRM/Core/DAO.php'); 
      $dao = CRM_Core_DAO::executeQuery( $query,  $params );
      $results = array ();
      while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
      }
      return $results;
    }

    static function copySlots($params){

      $results = array();

      $proceed = CRM_Utils_Array::value('proceed',$params);
      if(isset($proceed)){

        $session =& CRM_Core_Session::singleton( );
        $userId = $session->get( 'userID' ); // which is contact id of the user

   
        $sd = CRM_Utils_Array::value('sd',$params);
        $weeksForward = CRM_Utils_Array::value('weeks',$params);

        require_once 'CRM/Booking/Utils/DateTime.php';
        $daysOfWeek = CRM_Booking_Utils_DateTime::getWeeklyCalendar($sd);
        $startDate = array_shift(array_values($daysOfWeek));
        $endDate = end($daysOfWeek);
        $args = array();
        require_once 'CRM/Booking/BAO/Slot.php';
        $slots = CRM_Booking_BAO_Slot::getSlots(date('Y-m-d H:i:s', $startDate) ,date('Y-m-d H:i:s', $endDate),0);
        $uncreatableList = array();
        $values = array();

        for($i = 0; $i < $weeksForward; $i++){
         foreach ($slots as $key => $slot) {
            $slotDate = $slot['slot_date'];
            $nextSevenDay = strtotime("+". $i + 1 . " week",strtotime($slotDate));
            $nd = date('Y-m-d H:i:s', $nextSevenDay);

            $args['date'] = $nd;
            $args['startTime'] = $slot['start_time'];
            $args['endTime'] = $slot['end_time'];
            $args['contactId'] = $slot['clinician_contact_id'];
            $args['contactId2'] = $slot['attended_clinician_contact_id'];
            $args['roomId'] = $slot['room_id'];
          
            $results = CRM_Booking_BAO_Slot::isSlotCreatable($args);
            $isSlotCreatable = count($results) > 0 ? false : true;

            if(!$isSlotCreatable){
                $uncreatableList[] = array('slot_date' => date('l d/m/Y', $nextSevenDay),
                                           'clinician_1' => $slot['display_name'],
                                           'clinician_2' => $slot['attended_clinician_name'],
                                           'start_time' => $slot['start_time'],
                                           'end_time' => $slot['end_time'],
                                           'room_no' => $slot['room_no'],
                                           'status' => $slot['status']
                                           );
            }else{
              //if($proceed == 1){
                $contactId2 = $slot['attended_clinician_contact_id'];
                if($contactId2 === ''){
                  $contactId2 = null;
                }
               $values[] = array(
                  'clinician_contact_id' => $slot['clinician_contact_id'],
                  'attended_clinician_contact_id' => $contactId2,
                  'room_id' => $slot['room_id'],
                  'start_time' => $slot['start_time'],
                  'end_time' => $slot['end_time'],
                  'slot_date' => $nd,
                  'activity_type' => $slot['activity_type'],
                  'session_service' => $slot['session_service'],
                  'description' => $slot['description'],
                  'status ' => 1, //set status to free
                  'created_by' => $userId,
                  'updated_by' => $userId,
                  'updated_date' => date('Y-m-d H:i:s')
                );
             //}
            }
         }
        }
        if(sizeof($uncreatableList) == 0 || (sizeof($uncreatableList) > 0 && $proceed ==1)){
           $query= db_insert('civi_booking_slot')
              ->fields(array(
                      'clinician_contact_id',
                      'attended_clinician_contact_id',
                      'room_id',
                      'start_time',
                      'end_time',
                      'slot_date',
                      'activity_type',
                      'session_service',
                      'description',
                      'status ', //set status to free
                      'created_by',
                      'updated_by',
                      'updated_date'
            ));
          $txn = db_transaction();
          try{
            foreach ($values as $record) {
              $query->values($record);
            }

            $query->execute();
            $results = array('is_created' => 1);
          }catch(Exception $e){
            //TODO: Implement proper exception handler
            dump($e->getMessage());
          }
        }else{
          $results = array('is_created' => 0,
                          'uncreatableList' => $uncreatableList);
          return $results;
        } 
      }//end isset($proceed);
      $results;
    }
    

}
     
        