<?php

class CRM_Booking_BAO_Slot{
     /**
     * class constructor
     */
    function __construct( ) {

    }

    static function getSlotByDate($startDate, $endDate){
      $params = array(
        1 => array( $startDate, 'String'),
        2 => array( $endDate, 'String')
          );

      $query = "SELECT civi_booking_slot.id as id, clinician_contact_id, start_time,  end_time, room_no, slot_date, session_service
        FROM civi_booking_slot
        LEFT JOIN civi_booking_room ON civi_booking_room.id = civi_booking_slot.room_id
        WHERE slot_date BETWEEN %1 AND %2";  
         
      require_once('CRM/Core/DAO.php'); 
      $dao = CRM_Core_DAO::executeQuery( $query,  $params );
      $results = array ();
      while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
      }
      return $results;

    }

    static function getSlots($activityType, $startDate = null, $endDate = null){
      $params = array(1 => array( $activityType, 'Integer'));
      $query = "SELECT civi_booking_slot.id, 
                      start_time as start_time,
                      end_time as end_time,
                      slot_date as slot_date, 
                      room_no as room_no,
                      sort_name as sort_name,
                      display_name as display_name,
                      session_service as session_service,
                      label as activity_name,
                      status as status
        FROM civi_booking_slot
        LEFT JOIN civi_booking_room ON civi_booking_room.id = civi_booking_slot.room_id
        LEFT JOIN civicrm_contact ON civicrm_contact.id = civi_booking_slot.clinician_contact_id
        LEFT JOIN civicrm_option_value ON civicrm_option_value.value = civi_booking_slot.activity_type
        WHERE civicrm_option_value.option_group_id = 2 
        AND civi_booking_slot.activity_type = %1";  
         
      require_once('CRM/Core/DAO.php'); 
      $dao = CRM_Core_DAO::executeQuery( $query,  $params );
      $results = array ();
      while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
      }
      return $results;
    }

}
     
        