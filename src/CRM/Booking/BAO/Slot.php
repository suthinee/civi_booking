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

      $query = "SELECT contact_id, start_time,  end_time, room_no, slot_date
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

}
     
        