<?php

class CRM_Booking_Utils_DateTime{
     /**
     * class constructor
     */
    function __construct( ) {

    }

    /** 
   * create_time_range  
   *  
   * @param mixed $start start time, e.g., 9:30am or 9:30 
   * @param mixed $end   end time, e.g., 5:30pm or 17:30 
   * @param string $by   1 hour, 1 mins, 1 secs, etc. 
   * @access public 
   * @return void 
   */ 
  static function createTimeRange($start, $end, $by='30 mins') { 

      $start_time = strtotime($start); 
      $end_time   = strtotime($end); 

      $current    = time(); 
      $add_time   = strtotime('+'.$by, $current); 
      $diff       = $add_time-$current; 

      $times = array(); 
      while ($start_time < $end_time) { 
          $times[] = $start_time; 
          $start_time += $diff; 
      } 
      $times[] = $start_time; 
      return $times; 
  } 


  static function getWeeklyCalendar($startDate = null){

    if(is_null($startDate)){
      $date = strtotime('next Monday');
    }else{
      $date = $startDate;
    }

    $dayOfWeek = array();
    for($i = 0; $i <= 5; $i++){
       $day = strtotime('+'. $i .' day', $date);
       //$dayOfWeek[$day] =  date('l d/m/Y', $day);
       $dayOfWeek[$day] =  $day; 
 
    }
    return $dayOfWeek;
  }

}
     
        