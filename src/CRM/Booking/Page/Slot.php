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
        require_once 'CRM/Booking/Utils/DateTime.php';

        $results = CRM_Booking_BAO_Room::getRoom();

        $rooms = array();
        foreach($results as $room){
            $id = CRM_Utils_Array::value('id',$room);
            $rooms[$id]['id'] = CRM_Utils_Array::value('id',$room);   
            $rooms[$id]['room_no'] = CRM_Utils_Array::value('room_no',$room);   
            $rooms[$id]['floor'] = CRM_Utils_Array::value('floor',$room);   
        } 
        $this->assign('rooms',$rooms);
       
        $daysOfNextweek = CRM_Booking_Utils_DateTime::getDaysOfNextweek();
        $this->assign('daysOfNextweek',$daysOfNextweek);

        $timeRange = CRM_Booking_Utils_DateTime::createTimeRange('9:30', '17:30', '30 mins');
        $timeOptions = array();
        foreach ($timeRange as $key => $time) { 
            $timeOptions[$time] = date('G:i', $time); 
        }
        $this->assign('timeOptions',$timeOptions);

        $this->assign('startDate',array_shift(array_values($daysOfNextweek)));
        $this->assign('endDate', array_pop(array_values($daysOfNextweek)));


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

       $sessionType =  array(
          NULL => t('Select session type'),
          $initialAssessment => t('Initial assessment'),
          $supplementaryAssessment => t('Supplementary assessment'),
          $regularSession => t('Regular session'),
        );  


        $this->assign('sessionType', $sessionType);
          
        return parent::run();
        
    }

}

