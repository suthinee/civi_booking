<?php

require_once 'CRM/Core/Page.php';

/**
 * Slot page
 */
class CRM_Booking_Page_Room extends CRM_Core_Page{

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
        $results = CRM_Booking_BAO_Room::getRooms();
        $this->assign('rooms',$results);

      
        return parent::run();
        
    }

}

