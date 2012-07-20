<?php

require_once 'CRM/Core/Form.php';

/**
 */
class CRM_Booking_Form_Report_RegularSessionFailure extends CRM_Core_Form {


    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ){    /* set defaults for edit page */
        $defaults = array( );
        
        return $defaults;

    }
       

    function buildQuickForm( ){

        $this->addDate( 'log_date', ts('Date'), false, array('formatType' => 'searchDate') );

        $this->addButtons(array( 
                            array ( 'type'  => 'submit', 
                                 'name'      => 'Search', 
                                 'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                 'isDefault' => true )
                                  )); 


      
        $this->addFormRule( array( 'CRM_Booking_Form_Report_RegularSessionFailure', 'formRule' ), $this );
 
    }

    function postProcess(){ 
        $params = $this->controller->exportValues($this->_name);
        $date = $params['log_date'];
        

        $qParams = array( 1 => array($date, 'String'));     

        $query = "SELECT l.*,
                         a.*,
                         cc.*,
                         con.*
                FROM civi_booking_failure_log l
                LEFT JOIN civicrm_case_activity ca ON ca.activity_id = l.entity_id
                LEFT JOIN civicrm_activity a ON a.id = ca.activity_id
                LEFT JOIN civicrm_case c ON c.id = ca.case_id
                LEFT JOIN civicrm_case_contact cc ON cc.case_id = c.id
                LEFT JOIN civicrm_contact con ON con.id = cc.contact_id
                WHERE DATE_FORMAT(l.created_time,'%m/%d/%Y') = %1
                AND l.type = 'automate_regular_session'
                ORDER BY l.created_time DESC";

      civicrm_initialize( ); 
      require_once('CRM/Core/DAO.php');   
      $dao = CRM_Core_DAO::executeQuery( $query, $qParams);
      $results = array();
      $i = 0;
      while ( $dao->fetch( ) ) {
        $results[] = $dao->toArray();
        $actDateTime = $results[$i]['activity_date_time'];
        $results[$i]['activity_date_time'] = date('d-m-Y g:iA', strtotime($actDateTime));
        $i++;
      }

      $this->assign('logs', $results);    
    }

    /**
     * global validation rules for the form
     *
     * @param array $values posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @access public
     */
    

                  
    function formRule( $values, $files, $form )  {

        $errors = array();
        $logDate = CRM_Utils_Array::value( 'log_date', $values );
       
        if ( $logDate == null || $logDate == '' ) {
          $errors['log_date'] = ts( 'Date is required' );
        } 
        return empty( $errors ) ? true : $errors; 
    
    /*}*/
    }

}

