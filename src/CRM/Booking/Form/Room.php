<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once "CRM/Core/Form.php";

/**
 * This class generates form components for Edit Room
 * 
 */
class CRM_Booking_Form_Room extends CRM_Core_Form {


    /**
     */

    public $_roomId; 

    function preProcess(){ 
        $id =CRM_Utils_Request::retrieve( 'roomId', 'Positive', $this );  /* request room_id from page */
        if($id){
            $this->_roomId = $id;
        }else{
            $this->_roomId = 0;
        }
        
    }

    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ){    /* set defaults for edit page */
        $defaults = array( );
        $action = $this->getAction();

         if($action == 2){ //edit
            $id =CRM_Utils_Request::retrieve( 'roomId', 'Positive', $this );  /* request room_id from page */
            require_once('CRM/Booking/BAO/Room.php'); 
            $room = CRM_Booking_BAO_Room::getRoomById($id); 
            $defaults['floor'] =  $room[0]['floor'];
            $defaults ['room_no'] = $room[0]['room_no'];
            $defaults['type'] = $room[0]['type'];
            $defaults['size'] = $room[0]['size'];
            $defaults['centre'] = $room[0]['building'];
            $defaults['extension'] =$room[0]['phone_extension_no'];
            $defaults ['status'] = $room[0]['is_active'];
        }
        return $defaults;

    }
       

    function buildQuickForm( ){

         $this->addElement('text', 
                        'room_no', 
                        ts('Room No:'));  


        $this->addElement('text', 
                        'type', 
                        ts('Room Type:'));  

        $this->addElement('text', 
                        'size', 
                        ts('Room Size:'));  

        $floorOpts = array('' => '-- Select --',
                           'Basement' => 'Basement',
                           'First' => 'First', 
                           'Second' => 'Second', 
                           'Third' => 'Third',
                           'Forth' => 'Forth');

        $this->addElement('select', 
                        'floor', 
                        ts('Floor:'),
                        $floorOpts);

        $centreOpts = array('' => '-- Select --', 'AL' => 'AL', 'WS' => 'WS');
        $this->addElement('select', 
                        'centre', 
                        ts('Centre:'),
                        $centreOpts);  

        $this->addElement('text', 
                        'extension', 
                        ts('Extension No:'));  

        $status = array( 1 => 'Active', 0 => 'In Active');
        $this->addElement('select', 
                        'status', 
                        ts('Status:'),
                        $status);  

        $this->addButtons(array( 
                                    array ( 'type'      => 'submit', 
                                            'name'      => 'Add', 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel'),
                                            )
                                     ));
        $this->addFormRule( array( 'CRM_Booking_Form_Room', 'formRule' ), $this );
 
    }

    function postProcess(){ 
       $params = $this->controller->exportValues($this->_name);
       $roomNo = $params['room_no'];
       $type   = $params['type'];
       $size = $params['size'];
       $floor = $params['floor'];
       $centre = $params['centre'];
       $extension = $params['extension'];
       $status = $params['status'];

       dump($params);
       exit;



    exit;
       $action = $this->getAction();

       if($action == 1){

           /*add data into database (Nee)*/
           $id = db_insert('civi_booking_room')
                  ->fields(array(
                    'room_no' => $roomNo,
                    'type' => $type,
                    'size' => $size,
                    'floor' => $floor,
                    'building' => $centre,
                    'phone_extension_no' => $extension,
                    'is_active' => $status
                      
                ))
                ->execute();
                $print_r("Some data from the form was forgotten. Please fill in the entire form.");  
        }else if($action == 2){

            if($this->_roomId != 0){

                $id = db_update('civi_booking_room') // Table name no longer needs {}
                    ->fields(array(
                    'room_no' => $roomNo,
                    'type' => $type,
                    'size' => $size,
                    'floor' => $floor,
                    'building' => $centre,
                    'phone_extension_no' => $extension,
                    'is_active' => $status
                ))
                ->condition('id', $this->_roomId , '=')

                ->execute(); 
               
                header("Location:http://erawat-virtualbox/tccr/civicrm/booking/room/manage");

            }

        }//else if($action == delete) {
           // if($this->_roomId != 0){

               // $id = msql_query("Delete from civi_booking_room where roomId=$id")
               // or die (mysql_error());
                
            //    header("Location:http://erawat-virtualbox/tccr/civicrm/booking/room/manage");
        //}
    }

    /**
     * global validation rules for the form
     *
     * @param array $values posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @access public
     */
    /* function formRule( $values, $files, $form )  {
        echo 'rule';
        return true;*/

        /* Nee@Validation */
                  
    function formRule( $values, $files, $form )  {

        $roomNo = CRM_Utils_Array::value( 'room_no', $values );
        $type = CRM_Utils_Array::value( 'type', $values );
        $size = CRM_Utils_Array::value( 'size', $values );
        $floor = CRM_Utils_Array::value( 'floor', $values );
        $centre = CRM_Utils_Array::value( 'centre', $values );
        /*$extension = CRM_Utils_Array::value( 'extension', $values );*/
        $status = CRM_Utils_Array::value( 'status', $values );
       
        if ( $roomNo == null || $roomNo == '' ) {
          $errors['room_no'] = ts( 'Please insert Room Number' );
        } 
        if ($type == null || $type == '') {
            $errors['type'] = ts ('Please insert type');
        }
        if ($size == null || $size == ''){
            $errors['size'] = ts('please insert size');
        }
        if ($floor == null || $floor == ''){
            $errors['floor'] = ts ('Please select the floor');
        }
        if ($centre == null|| $centre == ''){
            $errors ['builing'] = ts('please select the centre');
        }
        /*if  ($extension == null || $extension == ''){
            $errors ['extension']= ts ('Please insert phone extension number');
        }*/
        /*if ($status == null || $status == ''){
            $errors ['status'] = ts('Please select status');
        }*/
        return empty( $errors ) ? true : $errors; 
    
    /*}*/
    }


}
