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
	$date = $params['date'];	
	$startTime = $params['start_time'];	
	$endTime = $params['end_time'];	
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
	    'start_time' => date('G:i',$startTime),
	    'end_time' => date('G:i',$endTime),
	    'slot_date' => date('Y-m-d H:i:s',$date),
	    'activity_type' => $activityType,
  	    'session_service' => $sessionService,
	    'status ' => 1,
	    'created_by' => 102,
      ))->execute();

      require_once 'CRM/Booking/Utils/DateTime.php';
      //get start/end time range
      $timeRange = CRM_Booking_Utils_DateTime::createTimeRange(date('G:i', $startTime), date('G:i',$endTime), '10 mins');
      $timeOptions = array();
      foreach ($timeRange as $key => $time) { 
        $timeOptions[] =$time; 
      }

      $slot = array(
      	'slot_id' => $id,
 		'room_no' => $roomNo,
	    'start_time' => $startTime,
	    'end_time' => $endTime,
	    'slot_date' => $date,
	    'time_range' => $timeOptions
      );
      $value = array($slot);
      return civicrm_api3_create_success($value,$params,'slot', 'create');
    }catch (Exception $e){
      return civicrm_api3_create_error(  $e  );
    }  
  
}


function civicrm_api3_slot_get( $params ){

	$counsellorId = $params['counsellor_id'];	
	$activityType = $params['activity_type'];	


    try{
      require_once 'Date.php';
      require_once 'CRM/Booking/BAO/Slot.php';
      $results = CRM_Booking_BAO_Slot::getSlotByCounsellorId($counsellorId, $activityType);

      //dump($results);
      $events = array();
      foreach($results as $slot){
      	$date = date('Y-m-d', strtotime ($slot['slot_date'])) ;
      	$startTime = date('g:i',strtotime($slot['start_time']));
      	$start = new DateTime($date . ' '. $startTime);
		
		$endTime = date('g:i',strtotime($slot['end_time']));
      	$end = new DateTime($date . ' '. $endTime);

      	$events[] = array( 
         'id' => $slot['id'], 
         'title' => t('Room no: ') . $slot['room_no'], 
         'start' => $start->format('Y-m-d H:i:s'), 
         'end' => $end->format('Y-m-d H:i:s'),
         'allDay' => false,
         'description' => $slot['session_service']

      	);
      }
	 //$json =  json_encode($events);
    
      return civicrm_api3_create_success($events,$params,'slot', 'get');
    }catch (Exception $e){
      return civicrm_api3_create_error($e);
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