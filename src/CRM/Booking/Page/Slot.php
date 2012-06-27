<?php

require_once 'CRM/Core/Page.php';

/**
 * Slot page
 */
class CRM_Booking_Page_Slot extends CRM_Core_Page{

    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action.
     * Finally it calls the parent's run method.
     *
     * @return void
     * @access public
     *
     */
    function run() {

        require_once 'CRM/Booking/BAO/Room.php';
        require_once 'CRM/Booking/BAO/Slot.php';
        require_once 'CRM/Booking/Utils/DateTime.php';

        $sd = CRM_Utils_Request::retrieve( 'sd', 'Positive', $this );
        if (date('Y-m-d H:i:s', strtotime($sd)) == $sd) {
          $sd = null;
        }
        $daysOfNextweek = CRM_Booking_Utils_DateTime::getWeeklyCalendar();
        if(!is_null($sd)){
          $daysOfNextweek = CRM_Booking_Utils_DateTime::getWeeklyCalendar($sd);
        }

     
        $startDate = array_shift(array_values($daysOfNextweek));
        $endDate = end($daysOfNextweek);

        $slots = CRM_Booking_BAO_Slot::getSlotByDate(date('Y-m-d H:i:s', $startDate) ,date('Y-m-d H:i:s', $endDate));

        $classNames = array();
        //convert slot to use strtotime 
        foreach($slots as $k => $slot){
            $timeRange = CRM_Booking_Utils_DateTime::createTimeRange($slot['start_time'], $slot['end_time'], '10 mins');
            //$timeOptions = array();
            $lastKey = end(array_keys($timeRange));

            foreach ($timeRange as $key => $time) { 
               $generated = date('d-m-Y', strtotime($slot['slot_date'])) . $slot['room_no'] . $time;
               //$classNames[$generated] = $generated;
               if ($key == $lastKey) {
                  $classNames[$generated]['lastKey'] = true;
                } else {
                  $classNames[$generated]['lastKey'] = false;
                }               
            }      
         }

    
        $timeRange = CRM_Booking_Utils_DateTime::createTimeRange('8:30', '20:30', '10 mins');
        $timeOptions = array();
        foreach ($timeRange as $key => $time) { 
            $timeOptions[$time] = date('G:i', $time); 
        }

        $this->assign('timeOptions',$timeOptions);

 
        $roomResults = CRM_Booking_BAO_Room::getRoom();

        $days = array();
        $rooms = array();
        foreach ($daysOfNextweek as $k => $day) {
            $days[$k]  =  array( 'date' => date('l d/m/Y', $day),
                                 'timeOptions' => $timeOptions);

            //$rooms = array();
            foreach($roomResults as $room){
                $roomId = CRM_Utils_Array::value('id',$room);
                $rooms[$roomId] = array('room_no' => CRM_Utils_Array::value('room_no',$room),
                                        'room_id' => CRM_Utils_Array::value('id',$room)
                                        );  
                $tdVals = array();
                foreach($timeOptions as $timeKey => $time){
                  //generated Id
                  $id = date('d-m-Y', $day) . CRM_Utils_Array::value('room_no',$room) .  $timeKey;  
                  //check if generated Id is in the className array
                  //if (in_array($id, $classNames)) {   
                  if (isset($classNames[$id])){
                  $class = 'reserved';
                  $isLastKey = $classNames[$id]['lastKey'];
                  if($isLastKey){
                      $class = 'reservable';
                  }   
                  $tdVals[$id] = array('time' => $time,
                                       'defaultEndTime' => strtotime('+60 mins', $timeKey),
                                       'timeKey' => $timeKey,
                                       'tdataId' => $id,
                                       'className' =>  $class );
                  }else if  ($day < strtotime("now")){
                    $tdVals[$id] = array('time' => $time,
                                      'defaultEndTime' => strtotime('+60 mins', $timeKey),
                                      'timeKey' => $timeKey,
                                      'tdataId' => $id,
                                      'className' => 'pasttime');
                  }else{
                    $tdVals[$id] = array('time' => $time,
                                      'defaultEndTime' => strtotime('+60 mins', $timeKey),
                                      'timeKey' => $timeKey,
                                      'tdataId' => $id,
                                      'className' => 'reservable');
                  }
              }
              $rooms[$roomId]['tdVals'] = $tdVals;
            }
            $days[$k]['rooms'] = $rooms;
        } 

        $this->assign('slots',$days);
        $this->assign('rooms',$rooms);
      
        $this->assign('startDate',date('l d/m/Y', $startDate));
        $this->assign('endDate', date('l d/m/Y',  $endDate));

        $lastWeekUrl = CRM_Utils_System::url( 'civicrm/booking/create-slots',
                                  "reset=1&sd=" .strtotime("last Monday" , $startDate));

        $nextWeekUrl = CRM_Utils_System::url( 'civicrm/booking/create-slots',
                                    "reset=1&sd=" .strtotime("next Monday" , $startDate) );

        $this->assign('lastWeekUrl',$lastWeekUrl);
        $this->assign('nextWeekUrl',$nextWeekUrl);


        require_once 'api/api.php';
        $results = civicrm_api("Contact", "get", array ('version' => '3','sequential' =>'1', 'contact_type' =>'Individual', 'contact_sub_type' => 'Clinician' , 'rowCount' =>'0'));
                      
        $contacts = array();
        foreach($results['values'] as $contact){
            $id = CRM_Utils_Array::value('contact_id',$contact);  
            //$contacts[$id]['contact_id'] = CRM_Utils_Array::value('id',$contact);   
            $contacts[$id] = CRM_Utils_Array::value('display_name',$contact);   
        } 

        $this->assign('contacts', $contacts);

       $supplementaryAssessment = CRM_Core_OptionGroup::getValue( 'activity_type', 'Supplementary Assessment' );
       $initialAssessment = CRM_Core_OptionGroup::getValue( 'activity_type', 'Initial Assessment' );
       $regularSession = CRM_Core_OptionGroup::getValue( 'activity_type', 'Regular Session' );

       $activityTypes =  array(
          NULL => t('Select Activity type'),
          $initialAssessment => t('Initial assessment'),
          $supplementaryAssessment => t('Supplementary assessment'),
          $regularSession => t('Regular session'),
        );  


        $this->assign('activityTypes', $activityTypes);

        //TODO: Create BAO for getting these values to avoid five queries
       $counselling = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Counselling' );
       $psychotherapy = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Psychotherapy' );
       $psychosexual = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Psychosexual' );
       $parentingTogeter = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Parenting Together' );
       $wellbeing = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Wellbeing' );
       $dsu = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'DSU' );

       $sessionServices =  array(
          NULL => t('Select Session service'),
          $counselling => t('Counselling'),
          $psychotherapy => t('Psychotherapy'),
          $psychosexual => t('Psychosexual'),
          $parentingTogeter => t('Parenting Together'),
          $wellbeing => t('Wellbeing'),
          $dsu => t('DSU'),
        ); 

        $this->assign('sessionServices', $sessionServices);

      
        return parent::run();
        
    }

}

