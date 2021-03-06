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
        if (date('Y-m-d H:i:s', strtotime($sd)) == $sd) { /*if (date('Y-m-d H:i:s', strtotime($sd)) == $sd) */
          $sd = null;
        }
        $daysOfNextweek = CRM_Booking_Utils_DateTime::getWeeklyCalendar();
        if(!is_null($sd)){
          $daysOfNextweek = CRM_Booking_Utils_DateTime::getWeeklyCalendar($sd);
        }

     
        $startDate = array_shift(array_values($daysOfNextweek));
        $endDate = end($daysOfNextweek);

        $slots = CRM_Booking_BAO_Slot::getSlots(date('Y-m-d H:i:s', $startDate) ,date('Y-m-d H:i:s', $endDate), 0, 0); 
        $classNames = array();
        //convert slot to use strtotime 
        foreach($slots as $k => $slot){
            $timeRange = CRM_Booking_Utils_DateTime::createTimeRange($slot['start_time'], $slot['end_time'], '5 mins');
            //$timeOptions = array();
            $lastKey = end(array_keys($timeRange));
            foreach ($timeRange as $key => $time) { 
               $generated = date('d-m-Y', strtotime($slot['slot_date'])) . $slot['room_no'] . $time;
               $classNames[$generated]['sessionService'] = $slot['session_service'];
               $classNames[$generated]['activityTypes'] = $slot['activity_type'];
               $classNames[$generated]['status'] = $slot['status'];
               $classNames[$generated]['slotId'] = $slot['id']; 

               if($slot['attended_clinician_name'] == null){
                $classNames[$generated]['tooltip'] = $slot['display_name'] . ', ' . $slot['start_time'] . ' - ' . $slot['end_time'] . ', ' . $slot['session_service'].', ' . $slot['id']; // added $slot[id] 24/07
               }else{
                $classNames[$generated]['tooltip'] = $slot['display_name'] . ' and ' . $slot['attended_clinician_name'] . ', ' . $slot['start_time'] . ' - ' . $slot['end_time'] . ', ' . $slot['session_service'].', ' . $slot['id'];// added $slot[id] 24/07
               }          
               if ($key == $lastKey) {
                  $classNames[$generated]['lastKey'] = true;
               } else {
                  $classNames[$generated]['lastKey'] = false;
               } 
               if($key == 1){
                $classNames[$generated]['textStart'] = true;
               }else{
                $classNames[$generated]['textStart'] = false;
               }              
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

        $this->assign('timeOptions',$timeOptions);
     
        $roomResults = CRM_Booking_BAO_Room::getRooms();

        $days = array();
        $rooms = array();
        foreach ($daysOfNextweek as $k => $day) {
            $days[$k]  =  array( 'date' => date('l d/m/Y', $day),
                                 'timeOptions' => $timeOptions);

            //$rooms = array();
            foreach($roomResults as $room){
                $roomId = CRM_Utils_Array::value('id',$room);
                $rooms[$roomId] = array('room_no' => CRM_Utils_Array::value('room_no',$room),
                                        'room_type' => CRM_Utils_Array::value('type',$room),
                                        'room_floor' => CRM_Utils_Array::value('floor',$room),
                                        'room_centre' => CRM_Utils_Array::value('building',$room),
                                        'room_id' => CRM_Utils_Array::value('id',$room)
                                        );  
                $tdVals = array();
                foreach($timeOptions as $timeKey => $time){
                  //generated Id
                  $id = date('d-m-Y', $day) . CRM_Utils_Array::value('room_no',$room) .  $timeKey;  
                  $title = '';
                  $slotId = 0;
                  $text = '';
                  //check if generated Id is in the className array
                  //if (in_array($id, $classNames)) {   
                  if (isset($classNames[$id])){
                    $isLastKey = $classNames[$id]['lastKey'];
                    if($isLastKey){
                        if($day < strtotime("now")){
                          $class = 'pasttime'; //set the last block of time to be reseveable
                        }else{
                          $class = 'reservable'; //set the last block of time to be reseveable
                           
                        }
                    }else{
                       $title = $classNames[$id]['tooltip'];
                       $status = $classNames[$id]['status'];
                       $slotId = $classNames[$id]['slotId'];
                       $service = $classNames[$id]['sessionService'];
                       $type = $classNames[$id]['activityTypes'];
                       $textStart = $classNames[$id]['textStart'];
                       /*
                       if($type == 50){
                         $class = $status == 1 ?  'initial-assessment' : 'initial-assessment-booked';
                       }else if($type == 51){
                          $class = $status == 1 ? 'supplementary-assessment' :  'supplementary-assessment-booked';
                       }else if($type == 51){
                         $class = $status == 1 ?  'regular-session' :  'regular-session-book'; 
                       }*/
                       /*
                         switch ($type) {
                          case 50:
                            $class = $status == 1 ?  'initial-assessment' : 'initial-assessment booked';
                            break;          $roomResults = CRM_Booking_BAO_Room::getRooms();
      $roomResults = CRM_Booking_BAO_Room::getRooms();

                          case 51:
                            $class = $status == 1 ? 'supplementary-assessment' :  'supplementary-assessment booked';
                            break;
                       }*/


                       if($type == 0){ //meeting or other
                        $class = 'other';
                       }

                       if($type == 50)   {

                        switch ($service) {
                          case 'Counselling':
                                $class = $status == 1 ?  'initial-assessment-counselling' :  'initial-assessment-counselling slot-booked'; 
                              
                               break;
                          case 'Psychotherapy':
                               $class = $status == 1 ?  'initial-assessment-psychotherapy' :  'initial-assessment-psychotherapy slot-booked'; 
                               
                               break;
                          case 'Psychosexual':
                               $class = $status == 1 ?  'initial-assessment-psychosexual' :  'initial-assessment-psychosexual slot-booked'; 
                               break;
                          case 'Parenting Together':
                                $class = $status == 1 ?  'initial-assessment-parenting' :  'initial-assessment-parenting slot-booked'; 
                                break;
                          case 'Wellbeing':
                               $class = $status == 1 ?  'initial-assessment-wellbeing' :  'initial-assessment-wellbeing slot-booked'; 
                               break;
                          case 'DSU':
                               $class = $status == 1 ?  'initial-assessment-dsu' :  'initial-assessment-dsu slot-booked'; 
                               break;
                          default: $class = $status == 1 ?  'initial-assessment-unknown' :  'initial-assessment-unknown slot-booked'; 
                        }
                       }

                       if($type == 51){
                        switch ($service) {
                          case 'Counselling':
                                $class = $status == 1 ?  'supplementary-assessment-counselling' :  'supplementary-assessment-counselling slot-booked'; 
                               break;
                          case 'Psychotherapy':
                               $class = $status == 1 ?  'supplementary-assessment-psychotherapy' :  'supplementary-assessment-psychotherapy slot-booked'; 
                               break;
                          case 'Psychosexual':
                               $class = $status == 1 ?  'supplementary-assessment-psychosexual' :  'supplementary-assessment-psychosexual slot-booked'; 
                               break;
                          case 'Parenting Together':
                                $class = $status == 1 ?  'supplementary-assessment-parenting' :  'supplementary-assessment-parenting slot-booked'; 
                                break;
                          case 'Wellbeing':
                               $class = $status == 1 ?  'supplementary-assessment-wellbeing' :  'supplementary-assessment-wellbeing slot-booked'; 
                               break;
                          case 'DSU':
                               $class = $status == 1 ?  'supplementary-assessment-dsu' :  'supplementary-assessment-dsu slot-booked'; 
                               break;
                          default: $class = $status == 1 ?  'supplementary-assessment-unknown' :  'supplementary-assessment-unknown slot-booked';

                        }  
                       }
                       
                       if($type == 52){
                       switch ($service)  {
                          case 'Counselling':
                                $class = $status == 1 ?  'regularsession-counselling' :  'regularsession-counselling slot-booked'; 
                              
                               break;
                          case 'Psychotherapy':
                               $class = $status == 1 ?  'regularsession-psychotherapy' :  'regularsession-psychotherapy slot-booked'; 
                               break;
                          case 'Psychosexual':
                               $class = $status == 1 ?  'regularsession-psychosexual' :  'regularsession-psychosexual slot-booked'; 
                               break;
                          case 'Parenting Together':
                                $class = $status == 1 ?  'regularsession-parenting' :  'regularsession-parenting slot-booked'; 
                                break;
                          case 'Wellbeing':
                               $class = $status == 1 ?  'regularsession-wellbeing' :  'regularsession-wellbeing slot-booked'; 
                               break;
                          case 'DSU':
                               $class = $status == 1 ?  'regularsession-dsu' :  'regularsession-dsu slot-booked'; 
                               break;
                        }  
                      }
                    }

                    if($textStart){
                      switch ($type) {
                        case 50:
                          $text = 'Init Acessment';
                          break;
                        case 51:
                          $text = 'Sup Acessment';
                          break;
                        case 52:
                          $text = 'Reg Session';
                          break;
                      }
                    }



                    $tdVals[$id] = array('time' => $time,
                                       'defaultEndTime' => strtotime('+60 mins', $timeKey),
                                       'timeKey' => $timeKey,
                                       'tdataId' => $id,
                                       'className' =>  $class,
                                       'title' => $title,
                                       'slotId' => $slotId,
                                       'text' => $text );
                  }else if  ($day < strtotime("now")){
                    $tdVals[$id] = array('time' => $time,
                                      'defaultEndTime' => strtotime('+60 mins', $timeKey),
                                      'timeKey' => $timeKey,
                                      'tdataId' => $id,
                                      'className' => 'pasttime',
                                      'text' => $text);
                  }else{
                    $tdVals[$id] = array('time' => $time,
                                      'defaultEndTime' => strtotime('+60 mins', $timeKey),
                                      'timeKey' => $timeKey,
                                      'tdataId' => $id,
                                      'className' => 'reservable',
                                      'text' => $text);
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

        $lastWeekUrl = CRM_Utils_System::url( 'civicrm/booking/slot/manage',
                                  "reset=1&sd=" .strtotime("last Monday" , $startDate));

        $nextWeekUrl = CRM_Utils_System::url( 'civicrm/booking/slot/manage',
                                    "reset=1&sd=" .strtotime("next Monday" , $startDate) );

        $this->assign('lastWeekUrl',$lastWeekUrl);
        $this->assign('nextWeekUrl',$nextWeekUrl);

        $copySlotsURL = CRM_Utils_System::url( 'civicrm/booking/slot/manage/copy',
                                    "action=add&sd=" . $startDate );
        
        $this->assign('copySlotsURL',$copySlotsURL);



       // require_once 'api/api.php';
       // $results = civicrm_api("Contact", "get", array ('version' => '3','sequential' =>'1', 'contact_type' =>'Individual', 'contact_sub_type' => 'Clinician' , 'rowCount' =>'0', 'group_id' => '27'));
       // dump($results);
        $query = "
          SELECT c.* 
          FROM civicrm_contact c
          LEFT JOIN civicrm_value_clinician_infomation_5 ci ON ci.entity_id = c.id 
          WHERE ci.status_13 = 'active' 
          AND c.contact_sub_type = 'Clinician'
          GROUP BY c.id";

        require_once('CRM/Core/DAO.php');   
        $dao = CRM_Core_DAO::executeQuery( $query);
        $results = array ();
        while ( $dao->fetch( ) ) {
            $results[] = $dao->toArray();   
         } 
                     
        $contacts = array();
        foreach($results as $contact){
            //$id = CRM_Utils_Array::value('contact_id',$contact);  
            $id = CRM_Utils_Array::value('id',$contact);  

            //$contacts[$id]['id']] = CRM_Utils_Array::value('id',$contact);   
            $contacts[$id] = CRM_Utils_Array::value('display_name',$contact);   
        } 
    

        $this->assign('contacts', $contacts);

       $supplementaryAssessment = CRM_Core_OptionGroup::getValue( 'activity_type', 'Supplementary Assessment' );
       $initialAssessment = CRM_Core_OptionGroup::getValue( 'activity_type', 'Initial Assessment' );
       $regularSession = CRM_Core_OptionGroup::getValue( 'activity_type', 'Regular Session' );

       $activityTypes =  array(
          NULL => t('Select Activity type'),
          $initialAssessment => t('Initial Assessment'),
          $supplementaryAssessment => t('Supplementary Assessment'),
          $regularSession => t('Regular Session'),
        );  


      $this->assign('activityTypes', $activityTypes);

        //TODO: Create BAO for getting these values to avoid five queries
       $counselling = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Counselling' );
       $psychotherapy = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Psychotherapy' );
       $psychosexual = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Psychosexual' );
       $parentingTogeter = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Parenting Together' );
       $wellbeing = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Wellbeing' );
       $dsu = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'DSU' );
       $undecided = CRM_Core_OptionGroup::getValue( 'undecided', 'Undecided' );

       $sessionServices =  array(
          NULL => t('Select Session service'),
          $counselling => t('Counselling'),
          $psychotherapy => t('Psychotherapy'),
          $psychosexual => t('Psychosexual'),
          $parentingTogeter => t('Parenting Together'),
          $wellbeing => t('Wellbeing'),
          $dsu => t('DSU'),
          $undecided = t('Undecided'),

        ); 

        $this->assign('sessionServices', $sessionServices);

        // Floor filter
        $floors = CRM_Booking_BAO_Room::getFloors();

        $this->assign('floors', $floors);

      
        return parent::run();
        
    }

}

