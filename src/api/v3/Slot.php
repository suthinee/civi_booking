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

function civicrm_api3_slot_update($params){

  $session =& CRM_Core_Session::singleton( );
  $userId = $session->get( 'userID' ); // which is contact id of the user

  $slotId = $params['slot_id'];
  if(!is_numeric($slotId)){
    return;
  }

  $contactId = $params['contact_id']; 
  $contactId2 = $params['contact_id_2']; 

  $roomNo = $params['room_id']; 
  $date = $params['date'] . ' 00:00:00';  
  $d = date_create_from_format('l d/F/Y H:i:s',$date);

  $startTime = $params['start_time']; 
  $endTime = $params['end_time']; 
  $sessionService = $params['session_service']; 
  $activityType = $params['activity_type'];
  $description = strip_tags($params['description']);
  $roomId = $params['room_id'];


  $contactId2 = ($contactId2 == '')  ? null : $contactId2;

  $args = array("date" =>   $d->format('Y-m-d H:i:s'),
                "startTime" => $startTime,
                "endTime" => $endTime,
                "contactId" => $contactId,
                "contactId2" => $contactId2);
 
  require_once 'CRM/Booking/BAO/Slot.php';
  $result = CRM_Booking_BAO_Slot::isSlotCreatable($args);
  $isSlotCreatable = count($result) > 0 ? false : true;
 
  if($isSlotCreatable){
    $txn = db_transaction();
    try{
      $numUpdated = db_update('civi_booking_slot') // Table name no longer needs {}
          ->fields(array(
              'clinician_contact_id' => $contactId,
              'attended_clinician_contact_id' => $contactId2,
              'room_id' => $roomId,
              'start_time' => date('G:i',$startTime),
              'end_time' => date('G:i',$endTime),
              'slot_date' =>   $d->format('Y-m-d H:i:s'),
              'activity_type' => $activityType,
              'session_service' => $sessionService,
              'description' => $description,
              'status ' => 1, //set status to free
              'updated_by' => $userId,
              'updated_date' => date('Y-m-d H:i:s')
          ))
          ->condition('id', $slotId , '=')
          ->execute(); 
      $return = array(
         'is_updated' => 1
      );
      $value = array($return);
      return civicrm_api3_create_success($value,$params,'slot', 'update');
    }catch(Exception $e){
      $txn->rollback();

      $error = array("is_created" => 0,
                       "error_message" => $e->getMessage());
      $value = array($error);
        //jQuery cannot handle JSON that create_error function return so use create_success instead.
      return civicrm_api3_create_success($value, $params, 'slot', 'update');
    }  
  }else{
      $error = array("is_created" => 0,
                      "error_message" => "Unable to update slot. Please check the slot times are valid and that both clinicians are available for that date or time.");
      $value = array($error);
      return civicrm_api3_create_success($value,$params,'slot', 'update'); 
  }
}



function civicrm_api3_slot_create( $params ){

  $contactId = $params['contact_id']; 
  $contactId2 = $params['contact_id_2']; 

  $roomId = $params['room_id']; 
  $date = $params['date'];  
  $startTime = $params['start_time']; 
  $endTime = $params['end_time']; 
  $sessionService = $params['session_service']; 
  $activityType = $params['activity_type'];
  $description = $params['description'];


  $args = array("date" =>  date('Y-m-d H:i:s', $date),
                "startTime" => date('G:i',$startTime),
                "endTime" => date('G:i',$endTime),
                "contactId" => $contactId,
                "contactId2" => $contactId2);
 
  require_once 'CRM/Booking/BAO/Slot.php';
  $result = CRM_Booking_BAO_Slot::isSlotCreatable($args);
  $isSlotCreatable = count($result) > 0 ? false : true;
 
  if($isSlotCreatable){

    $txn = db_transaction();
    try{
     if($contactId2 === ''){
       $contactId2 = null;
     }

     $session =& CRM_Core_Session::singleton( );
     $userId = $session->get( 'userID' ); // which is contact id of the user

  	 $id = db_insert('civi_booking_slot')
          ->fields(array(
            'clinician_contact_id' => $contactId,
            'attended_clinician_contact_id' => $contactId2,
      	    'room_id' => $roomId,
      	    'start_time' => date('G:i',$startTime),
      	    'end_time' => date('G:i',$endTime),
      	    'slot_date' => date('Y-m-d H:i:s',$date),
      	    'activity_type' => $activityType,
        	  'session_service' => $sessionService,
            'description' => $description,
      	    'status ' => 1, //set status to free
      	    'created_by' => $userId,
            'updated_by' => $userId,
            'updated_date' => date('Y-m-d H:i:s')
        ))->execute();

        $slot = array(
         'slot_id' => $id,
         'is_created' => 1
        );
        $value = array($slot);
        return civicrm_api3_create_success($value,$params,'slot', 'create');
    }catch (Exception $e){
        $txn->rollback();

        $error = array("is_created" => 0,
                       "error_message" => get_object_vars($e));
        $value = array($error);
        //jQuery cannot handle JSON that create_error function return so use create_success instead.
        return civicrm_api3_create_success($value, $params, 'slot', 'create');
    }  
  }else{
      $error = array("is_created" => 0,
                      "error_message" => "Unable to create slot. Please check the slot times are valid and that both clinicians are available for that date or time.");
      $value = array($error);
      return civicrm_api3_create_success($value,$params,'slot', 'create'); 
  }
  
}

function civicrm_api3_slot_copy($params){

  require_once 'CRM/Booking/BAO/Slot.php';
  $results = CRM_Booking_BAO_Slot::copySlots($params);
  $isCreated = CRM_Utils_Array::value('is_created',$results);
  if($isCreated == 0){
    $uncreatableList = CRM_Utils_Array::value('uncreatableList',$results);
    return civicrm_api3_create_success($uncreatableList, $params, 'slot', 'is_copyable');
  }else{
    //Return empty array
    return civicrm_api3_create_success(array(), $params, 'slot', 'is_copyable');
  }

      

}

//an APIs for displaying a slot using slot id
function civicrm_api3_slot_get_by_id($params){
  $sid = CRM_Utils_Array::value('sid',$params);
  try{
    if(!isset($sid)) throw new Exception('Slot id not found.');
    require_once 'CRM/Booking/BAO/Slot.php';
    $results = CRM_Booking_BAO_Slot::getSlotById($sid);
    $value = array();
    foreach ($results as $key => $slot) {
      $value[$key]['id'] = $slot['id'];
      $value[$key]['start_time'] = date('G:i', strtotime($slot['start_time']));
      $value[$key]['end_time'] = date('G:i', strtotime($slot['end_time']));
      $value[$key]['slot_date'] = $slot['slot_date'];
      $value[$key]['room_no'] = $slot['room_no'];
      $value[$key]['room_id'] = $slot['room_id'];
      $value[$key]['centre'] = $slot['centre'];
      $value[$key]['session_service'] = $slot['session_service'];
      $value[$key]['activity_type'] = $slot['activity_type'];
      $value[$key]['clinician_contact_display_name'] = $slot['clinician_contact_display_name'];
      $value[$key]['attended_clinician_contact_display_name'] = $slot['attended_clinician_contact_display_name'];
      $value[$key]['status'] = $slot['status'];
      $value[$key]['description'] = $slot['description'];

      break;
    }
    return civicrm_api3_create_success($value, $params, 'slot', 'get_by_id');

  }catch(Exception $e){
      return civicrm_api3_create_error($e->getMessage());
  }
}                           

//an APIs for displaying individual clinician's slot
function civicrm_api3_slot_get_by_contact( $params ){

  $cid = CRM_Utils_Array::value('cid',$params);
  require_once 'CRM/Booking/BAO/Slot.php';
  $slots = CRM_Booking_BAO_Slot::getSlotsByContact($cid);
  $events = array();
  $key = 0;
  foreach($slots as $k => $slot){

    $date = strtotime(($slot['slot_date']));
    $stime = strtotime(($slot['start_time'])) ;
    $etime = strtotime(($slot['end_time'])) ;

    $st = date('Y-m-d', $date) . ' ' . date('H:i:s', $stime);
    $et = date('Y-m-d', $date) . ' ' . date('H:i:s', $etime);
    
    
    $events[$k]['title'] = $slot['session_service'];
    $events[$k]['start'] = $st;
    $events[$k]['end'] = $et;
    $events[$k]['allDay'] = false;
    if($slot['status'] == 2){
      $events[$k]['color'] = '#CF9D9B';
    }else{
      $events[$k]['color'] = '#408AD2';
    }
    $key = $k;
  }


  

    ///Unavaiability clinicain
  $queryParams = array(1 => array($cid, 'Integer'));
  $query = "SELECT id,entity_id,unavailable_start_date__time_41, unavailable_end_date__time_42,subject_44
            From  civicrm_value_clinician_unavailability_14 
            Where entity_id = %1";

  require_once('CRM/Core/DAO.php');   
  $dao = CRM_Core_DAO::executeQuery( $query , $queryParams );
  $results = array();
  while ( $dao->fetch( ) ) {
        $results[] = $dao->toArray();   
  }
  foreach($results as $k => $unavailability){
      $k += 1;
      $i = $key + $k;
      $events[$i]['title'] = $unavailability['subject_44'];
      $events[$i]['start'] = $unavailability['unavailable_start_date__time_41'];
      $events[$i]['end'] = $unavailability['unavailable_end_date__time_42'];
      $events[$i]['allDay'] = false;
      $events[$i]['color'] = '#BDBDBD';

  }

 

  $return = array();
  $return['is_error'] = 0;
  $return['version'] = 3;
  $return['results'] = $events;

  return $return;

}

function civicrm_api3_slot_delete($params){

  $sid = CRM_Utils_Array::value('id',$params);
  try{
    if(!isset($sid)) throw new Exception('Slot id not found.'); 
    require_once 'CRM/Booking/BAO/Slot.php';
    $status = CRM_Booking_BAO_Slot::getSlotStatus($sid);
    $isDeletable = $status == 1 ? true : false;
    if(!$isDeletable) throw new Exception('Cannot delete a slot, the slot is in used');
    CRM_Booking_BAO_Slot::deleteSlot($sid);
    $value = array('id' => $sid);
    return civicrm_api3_create_success($value, $params, 'slot', 'delete');
  }catch(Exception $e){
    return civicrm_api3_create_error($e->getMessage());

  }
}


function civicrm_api3_slot_get( $params ){

	$activityType = $params['activity_type'];	
  $sd = CRM_Utils_Array::value('date',$params);

   try{
      $return = array();
      require_once 'Date.php';
      require_once 'CRM/Booking/BAO/Slot.php';
      require_once 'CRM/Booking/Utils/DateTime.php';

      //$results = CRM_Booking_BAO_Slot::getSlots($activityType);

      //$sd = null;
      $daysOfNextweek = CRM_Booking_Utils_DateTime::getWeeklyCalendar();
      if(!is_null($sd)){
        $daysOfNextweek = CRM_Booking_Utils_DateTime::getWeeklyCalendar($sd);
      }

      $startDate = array_shift(array_values($daysOfNextweek));
      $endDate = end($daysOfNextweek);

      $slots = CRM_Booking_BAO_Slot::getSlots(date('Y-m-d H:i:s', $startDate) ,date('Y-m-d H:i:s', $endDate), $activityType);
  
      $slotTypes = array();
      $classNames = array();
        //convert slot to use strtotime 
      foreach($slots as $k => $slot){
          $timeRange = CRM_Booking_Utils_DateTime::createTimeRange($slot['start_time'], $slot['end_time'], '5 mins');
          //$timeOptions = array();
          foreach ($timeRange as $key => $time) { 
              //$timeOptions[] =$time; 
              $className = date('d-m-Y', strtotime($slot['slot_date'])) . $slot['clinician_contact_id'] . $time;
              $classNames[] = $className;
              if($slot['attended_clinician_name'] == null){
                $tooltip = $slot['display_name'] . ', ' . $slot['start_time'] . ' - ' . $slot['end_time'];
              }else{
                $tooltip = $slot['display_name'] . ' and ' . $slot['attended_clinician_name'] . ', ' . $slot['start_time'] . ' - ' . $slot['end_time'];
              }      
              $slotTypes[$className] = array( 'sessionService' => $slot['session_service'],
                                              'slotId' => $slot['id'],
                                              'title' => $tooltip,
                                              'date' => date('d-m-Y', strtotime($slot['slot_date'])),
                                              'time' => date('G:i', strtotime($slot['start_time'])) . ' - ' . date('G:i', strtotime($slot['end_time'])));

          }
      }

      $timeRange = CRM_Booking_Utils_DateTime::createTimeRange('8:30', '20:30', '5 mins');
      $timeDisplayRange = CRM_Booking_Utils_DateTime::createTimeRange('8:30', '20:30', '30 mins'); //for screen to display
      $timeOptions = array();
      foreach ($timeRange as $key => $time) { 
        $timeOptions[$time]['time'] = date('G:i', $time); 
        if(in_array($time, $timeDisplayRange)){
          $timeOptions[$time]['isDisplay'] = true;
        }else{
          $timeOptions[$time]['isDisplay'] = false;
        } 
      }

      $results = civicrm_api("Contact","get", array ('version' => '3',
                          'sequential' =>'1', 
                          'contact_type' =>'Individual',
                          'contact_sub_type' => 'Clinician', 
                          'rowCount' =>'0'));

      $contacts = array();
      foreach($results['values'] as $contact){
        $id = CRM_Utils_Array::value('contact_id',$contact);   
        $contacts[$id]['contact_id'] = CRM_Utils_Array::value('id',$contact); 
        $contacts[$id]['display_name'] = CRM_Utils_Array::value('display_name',$contact);    
        $contacts[$id]['sort_name'] = CRM_Utils_Array::value('sort_name',$contact);    
      } 
        $days = array();
        $conts = array();
        foreach ($daysOfNextweek as $k => $day) {
            $days[$k]  =  array( 
                                 'date' => date('l d/m/Y', $day),
                                 'timeOptions' => $timeOptions);

            foreach($contacts as $contact){
                $contactId = CRM_Utils_Array::value('contact_id',$contact);           
                $display_name =  CRM_Utils_Array::value('display_name',$contact);    
                $conts[$contactId] = array('display_name' => $display_name,
                                        'sort_name' => CRM_Utils_Array::value('sort_name',$contact),
                                        'contact_id' => CRM_Utils_Array::value('contact_id',$contact)
                                        );  
                $tdVals = array();
                foreach($timeOptions as $timeKey => $time){
                  $id = date('d-m-Y', $day) . CRM_Utils_Array::value('contact_id',$contact) .  $timeKey;  
                  if (in_array($id, $classNames)) { 
                    $type = $slotTypes[$id]['sessionService'];
                    $className = null;                  
                    switch ($type) {
                    case 'Counselling':
                        $className = 'counselling';
                        break;
                    case 'Psychotherapy':
                         $className = 'psychotherapy';
                         break;
                    case 'Psychosexual':
                         $className = 'psychosexual';
                         break;
                    case 'Parenting Together':
                         $className = 'parenting';
                          break;
                    case 'Wellbeing':
                         $className = 'wellbeing';
                         break;
                    case 'DSU':
                         $className = 'dsu';
                         break;
                    } 
                              
                    $tdVals[$id] = array('time' => $time,
                                       'timeKey' => $timeKey,
                                       'tdataId' => $id,
                                       'className' =>  $className,
                                       'slotId' => $slotTypes[$id]['slotId'],
                                       'title' => $slotTypes[$id]['title'],
                                       'clinician' => $display_name,
                                       'service' => $slotTypes[$id]['sessionService'],
                                       'time' => $slotTypes[$id]['time']);
                  }else if  ($day < strtotime("now")){
                    $tdVals[$id] = array('time' => $time,
                                      'timeKey' => $timeKey,
                                      'tdataId' => $id,
                                      'className' => 'pasttime',
                                      'title' => 0);
                  }else{
                    $tdVals[$id] = array('time' => $time,
                                      'timeKey' => $timeKey,
                                      'tdataId' => $id,
                                      'className' => 'unavailable',
                                       'title' => 0);
                  }
              }
              $conts[$contactId]['tdVals'] = $tdVals;
            }
            $days[$k]['contacts'] = $conts;
        } 

      $return['sd'] = $sd;
      $return['is_error'] = 0;
      $return['version'] = 3;

      $return['startDate'] = array("strtotime" => $startDate,
                                 "date" =>  date('l d/m/Y', $startDate));
      $return['endDate'] = array("strtotime" => $endDate,
                                  "date" =>  date('l d/m/Y', $endDate));
      $return['nextWeek'] = strtotime("next Monday" , $startDate);
      $return['lastWeek'] = strtotime("last Monday" , $startDate);

      $return['days'] = $days;
      
      return $return;
    }catch (Exception $e){
      return civicrm_api3_create_error($e);
    }  
  
}
function _civicrm_api3_slot_update_spec(&$params){
  $params['slot_id']['api.required'] =1;
  $params['room_id']['api.required'] =1;
  $params['contact_id']['api.required'] =1;
  $params['date']['api.required'] =1;
  $params['start_time']['api.required'] =1;
  $params['end_time']['api.required'] =1;
  $params['activity_type']['api.required'] =1;
  $params['session_service']['api.required'] =1;

}


function _civicrm_api3_slot_create_spec(&$params){
  $params['room_id']['api.required'] =1;
  $params['contact_id']['api.required'] =1;
  $params['date']['api.required'] =1;
  $params['start_time']['api.required'] =1;
  $params['end_time']['api.required'] =1;
  $params['activity_type']['api.required'] =1;
  $params['session_service']['api.required'] =1;

}