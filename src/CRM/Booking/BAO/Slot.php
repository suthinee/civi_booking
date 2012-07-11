<?php

class CRM_Booking_BAO_Slot{
     /**
     * class constructor
     */
    function __construct( ) {

    }

    static function isSlotCreatable($args){

      //civicrm_initialize( );
      $params = array(
        1 => array( $args['date'], 'String'),
        2 => array($args['startTime'], 'String'),
        3 => array($args['endTime'], 'String'),
        4 => array($args['contactId'], 'Integer')
      );
      $query = "SELECT civi_booking_slot.id
            FROM civi_booking_slot 
            LEFT JOIN civicrm_contact ON civicrm_contact.id = civi_booking_slot.clinician_contact_id
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
          $query = "SELECT civi_booking_slot.id
                FROM civi_booking_slot 
                LEFT JOIN civicrm_contact ON civicrm_contact.id = civi_booking_slot.attended_clinician_contact_id
                WHERE civi_booking_slot.slot_date  = %1
                AND civi_booking_slot.attended_clinician_contact_id = %4
                AND (civi_booking_slot.start_time BETWEEN %2 AND %3 OR civi_booking_slot.end_time BETWEEN %2 AND %3)";

          require_once('CRM/Core/DAO.php');   
          $dao = CRM_Core_DAO::executeQuery( $query , $params );
           while ( $dao->fetch( ) ) {
              $results[] = $dao->toArray();   
           } 
         }
       }
      return $results;
    }

    static function getSlots($startDate = null, $endDate = null, $activityType = 0, $status = 1){  
      $query = "SELECT civi_booking_slot.id as id,
               con1.display_name as display_name,
               con2.display_name as attended_clinician_name,
               clinician_contact_id, 
               start_time,  
               end_time, 
               room_no,
               slot_date,
               session_service,
               status
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
    
    static function getSlotById($id){
      if(!isset($id)){
        return;
      }
      $params = array(1 => array( $id, 'Integer'));
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
    

}
     
        