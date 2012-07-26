<?php

class CRM_Booking_BAO_Room{
     /**
     * class constructor
     */
    function __construct( ) {}

    

      /* edit mode Nee*/
    static function getRoomById($roomId, $status = 1){
        $params = array( 1 => array( $roomId, 'Integer'),
                         2 => array( $status, 'Integer'));

        $query = "SELECT  id, 
                  room_no,
                  type,
                  size,
                  floor,
                  building,
                  phone_extension_no,
                  is_active
            FROM civi_booking_room
            WHERE id = %1
            AND is_active = %2";  
      
        require_once('CRM/Core/DAO.php'); 
        $dao = CRM_Core_DAO::executeQuery( $query, $params);
        $results = array ();
        while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
        }
        return $results;

    }

    static function getRooms($isActive = 1){
      $params = array( 1 => array( $isActive, 'Integer'));

      $query = "SELECT id, 
                  room_no,
                  type,
                  size,
                  floor,
                  building,
                  phone_extension_no,
                  is_active
      FROM civi_booking_room
      WHERE is_active = %1";  
      require_once('CRM/Core/DAO.php'); 
      $dao = CRM_Core_DAO::executeQuery( $query, $params );
      $results = array ();
      while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
      }
      return $results;


    }

    
    static function getFloors() {
     

      $query = "SELECT  DISTINCT floor     
      FROM civi_booking_room";
      
      require_once('CRM/Core/DAO.php'); 
        $dao = CRM_Core_DAO::executeQuery( $query);
        $results = array ();
        while ( $dao->fetch( ) ) {
          $results[] = $dao->toArray();          
        }
        return $results;
    }


    static function getRoomByNo ($isActive = 1){
        $params = array( 1 => array( $roomNo, 'String'));

        $query = "SELECT id, 
        room_no, floor
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