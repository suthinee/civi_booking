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

       
        $daysOfNextweek = CRM_Booking_Utils_DateTime::getDaysOfNextweek();

        $days = array();
        foreach ($daysOfNextweek as $k => $day) {
            $days[$k] =  date('l d/m/Y', $day);
        }       

        $this->assign('daysOfNextweek',$days);


        $startDate = array_shift(array_values($daysOfNextweek));
        $endDate = end($daysOfNextweek);

        $slots = CRM_Booking_BAO_Slot::getSlotByDate(date('Y-m-d H:i:s', $startDate) ,date('Y-m-d H:i:s', $endDate));

        //dump($slots);

        $classNames = array();

        //convert slot to use strtotime 
        foreach($slots as $k => $slot){

            $timeRange = CRM_Booking_Utils_DateTime::createTimeRange($slot['start_time'], $slot['end_time'], '10 mins');
            $timeOptions = array();
            foreach ($timeRange as $key => $time) { 
                $timeOptions[] =$time; 
                $classNames[] = strtotime($slot['slot_date']) . $slot['room_no'] . $time;
            }
            $slots[$k]['time_range'] = $timeOptions; //add time range options
            $slots[$k]['start_time'] = strtotime($slot['start_time']);
            $slots[$k]['end_time'] = strtotime($slot['end_time']);
            $slots[$k]['slot_date'] = strtotime($slot['slot_date']);
         }

         //dump($slots);
         //dump($classNames);
         $this->assign('reservedSlots',$classNames);



        $timeRange = CRM_Booking_Utils_DateTime::createTimeRange('8:30', '20:30', '10 mins');
        $timeOptions = array();
        foreach ($timeRange as $key => $time) { 
            $timeOptions[$time] = date('G:i', $time); 
        }

        $this->assign('timeOptions',$timeOptions);

        $this->assign('startDate',array_shift(array_values($days)));
        $this->assign('endDate', array_pop(array_values($days)));

        $results = CRM_Booking_BAO_Room::getRoom();

        $rooms = array();
        foreach($results as $room){
            $id = CRM_Utils_Array::value('id',$room);
            $rooms[$id]['id'] = CRM_Utils_Array::value('id',$room);   
            $rooms[$id]['room_no'] = CRM_Utils_Array::value('room_no',$room);   
            $rooms[$id]['floor'] = CRM_Utils_Array::value('floor',$room);   
        } 
        $this->assign('rooms',$rooms);


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
       $psychotherapy = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Psychotherapy' );
       $psychosexual = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Psychosexual' );
       $parentingTogeter = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Parenting Together' );
       $wellbeing = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'Wellbeing' );
       $dsu = CRM_Core_OptionGroup::getValue( 'service_20120221114757', 'DSU' );

       $sessionServices =  array(
          NULL => t('Select Session service'),
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

