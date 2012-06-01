<?php

class CRM_Booking_BAO_Room{
     /**
     * class constructor
     */
    function __construct( ) {

    }

    static function getRoom(){
         $query = "SELECT id, room_no, floor
      FROM civi_booking_room
      WHERE is_active = 1";  
      require_once('CRM/Core/DAO.php'); 
      $dao = CRM_Core_DAO::executeQuery( $query );
      $results = array ();
      while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
      }
      return $results;


    }


    static function getRoomByNo($roomNo){
        $params = array( 1 => array( $roomNo, 'String'));

        $query = "SELECT id, room_no, floor
            FROM civi_booking_room
            WHERE room_no = %1
            AND is_active = 1";  
      
        require_once('CRM/Core/DAO.php'); 
        $dao = CRM_Core_DAO::executeQuery( $query, $params);
        $results = array ();
        while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
        }
        return $results;

    }


}
     
        