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
  $contactId2 = $params['contact_id_2']; 

  $roomNo = $params['room_no']; 
  $date = $params['date'];  
  $startTime = $params['start_time']; 
  $endTime = $params['end_time']; 
  $sessionService = $params['session_service']; 
  $activityType = $params['activity_type'];
  $description = $params['description'];


  $args = array("date" =>  date('Y-m-d H:i:s', $date),
                "startTime" => date('G:i',$startTime),
                "endTime" => date('G:i',$endTime),
                "roomNo" => $roomNo,
                "contactId" => $contactId,
                "contactId2" => $contactId2);
 

  require_once 'CRM/Booking/BAO/Slot.php';
  $result = CRM_Booking_BAO_Slot::isSlotCreatable($args);
  $isSlotCreatable = count($result) > 0 ? false : true;
  if($isSlotCreatable){

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
        'clinician_contact_id' => $contactId,
        'attended_clinician_contact_id' => $contactId2,
  	    'room_id' => $roomId,
  	    'start_time' => date('G:i',$startTime),
  	    'end_time' => date('G:i',$endTime),
  	    'slot_date' => date('Y-m-d H:i:s',$date),
  	    'activity_type' => $activityType,
    	  'session_service' => $sessionService,
        'description' => $description,
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
  	     'slot_date' =>  date('d-m-Y', $date),
  	     'time_range' => $timeOptions,
         'is_created' => 1
        );
        $value = array($slot);
        return civicrm_api3_create_success($value,$params,'slot', 'create');
      }catch (Exception $e){
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


function civicrm_api3_slot_get( $params ){

	$activityType = $params['activity_type'];	
  $sd = $params['date']; 


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

      $slots = CRM_Booking_BAO_Slot::getSlotByDate(date('Y-m-d H:i:s', $startDate) ,date('Y-m-d H:i:s', $endDate));
      $slotTypes = array();
      $classNames = array();
        //convert slot to use strtotime 
      foreach($slots as $k => $slot){
          $timeRange = CRM_Booking_Utils_DateTime::createTimeRange($slot['start_time'], $slot['end_time'], '10 mins');
          $timeOptions = array();
          foreach ($timeRange as $key => $time) { 
              $timeOptions[] =$time; 
              $className = date('d-m-Y', strtotime($slot['slot_date'])) . $slot['clinician_contact_id'] . $time;
              $classNames[] = $className;
              $slotTypes[$className] = array( 'sessionService' => $slot['session_service'],
                                              'slotId' => $slot['id']);

          }
      }

      $timeRange = CRM_Booking_Utils_DateTime::createTimeRange('8:30', '20:30', '10 mins');
      $timeOptions = array();
      foreach ($timeRange as $key => $time) { 
        $timeOptions[$time] = date('G:i', $time); 
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
                $conts[$contactId] = array('display_name' => CRM_Utils_Array::value('display_name',$contact),
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
                                       'slotId' => $slotTypes[$id]['slotId']);
                  }else if  ($day < strtotime("now")){
                    $tdVals[$id] = array('time' => $time,
                                      'timeKey' => $timeKey,
                                      'tdataId' => $id,
                                      'className' => 'pasttime');
                  }else{
                    $tdVals[$id] = array('time' => $time,
                                      'timeKey' => $timeKey,
                                      'tdataId' => $id,
                                      'className' => 'unavailable');
                  }
              }
              $conts[$contactId]['tdVals'] = $tdVals;
            }
            $days[$k]['contacts'] = $conts;
        } 

      /*
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
         'session' => $slot['session_service'],
         'status' => $slot['status']
      	);
      }
      */
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

      //  dump(json_encode($return));
      //return json_encode($return);
      //$json = json_encode($return);
      //require_once 'CRM/Utils/JSON.php';

      //return CRM_Utils_JSON::encode($json);
      return $return;
      //return civicrm_api3_create_success(json_encode($return),$params,'slot', 'get');
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