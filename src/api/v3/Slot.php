<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
 +--------------------------------------------------------------------+
 | Copyright Compucorp Ltd (c) 2012 - 2013                              |
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
 * File for the CiviCRM APIv3 group functions
 *
 * @package CiviCRM_APIv3
 * @subpackage API_Slot
 * @copyright CiviCRM LLC (c) 2004-2011
 */

//require_once 'CRM/Booking/BAO/Slot.php';


function civicrm_api3_slot_create( $params ){

	$contactId = $params['contact_id'];	
	$roomNo = $params['room_no'];	
	$date = date('Y-m-d H:i:s',$params['date']);	
	$startTime = date('G:i',$params['start_time']);	
	$endTime = date('G:i',$params['end_time']);	
	$sessionService = $params['session_service'];	
	$activityType = $params['activity_type'];

	require_once 'CRM/Booking/BAO/Room.php';
	$results = CRM_Booking_BAO_Room::getRoomByNo($roomNo);

	$roomId = null;
	foreach($results as $room){
	    $roomId = CRM_Utils_Array::value('id',$room);
	    //break; //break as expected one
	} 
    $txn = db_transaction();
    try{
	 $id = db_insert('civi_booking_slot')
      ->fields(array(
        'contact_id' => $contactId,
	    'room_id' => $roomId,
	    'start_time' => $startTime,
	    'end_time' => $endTime,
	    'slot_date' => $date,
	    'session_type' => $activityType,
	    'status ' => 1,
	    'created_by' => 102,
      ))->execute();
      $slot = array(
      	'slot_id' => $id,
 		'room_id' => $roomId,
	    'start_time' => $startTime,
	    'end_time' => $endTime,
	    'slot_date' => $date,
      );
      $value = array($slot);
      return civicrm_api3_create_success($value,$params,'slot', 'create');
    }catch (Exception $e){
      return civicrm_api3_create_error(  $e  );
    }  
  
}


function _civicrm_api3_slot_create_spec(&$params){
  $params['room_no']['api.required'] =1;
  $params['contact_id']['api.required'] =1;
  $params['date']['api.required'] =1;
  $params['start_time']['api.required'] =1;
  $params['end_time']['api.required'] =1;
  $params['activity_type']['api.required'] =1;
  $params['session_service']['api.required'] =1;

}