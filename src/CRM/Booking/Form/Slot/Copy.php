<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once "CRM/Core/Form.php";

/**
 * This class generates form components
 * 
 */
class CRM_Booking_Form_Slot_Copy extends CRM_Core_Form {

    function preProcess(){ 

    }

    function buildQuickForm( ){

        $sd = CRM_Utils_Request::retrieve( 'sd', 'Positive', $this );
        require_once 'CRM/Booking/Utils/DateTime.php';
        $daysOfWeek = CRM_Booking_Utils_DateTime::getWeeklyCalendar($sd);
        $startDate = array_shift(array_values($daysOfWeek));
        $endDate = end($daysOfWeek);
        $this->assign('startDate',date('l d/m/Y', $startDate));
        $this->assign('endDate', date('l d/m/Y',  $endDate));


        $weeksOpts = array( 1 => '1', 
                            2 => '2',
                            3 => '3', 
                            4 => '4',
                            5 => '5'
                            );
        $this->addElement('select', 
                        'weeks', 
                        ts('No of weeks:'),
                        $weeksOpts);  

        $this->assign('sDate', $startDate);
        
        $this->addElement('hidden',
                          'sd',
                          $startDate); 

        $this->addElement('text',
                          'proceed',
                          0); 


        $this->addButtons(array( 
                                    array ( 'type'      => 'submit', 
                                            'name'      => 'Copy slots', 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   )
                                     
                  
                                     ));
        $this->addFormRule( array( 'CRM_Booking_Form_Slot_Copy', 'formRule' ), $this );
    }


     /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) { 

      
    }
    

    function postProcess(){ 


        $params = $this->controller->exportValues( $this->_name );

        $proceed = CRM_Utils_Array::value('proceed',$params);
        if($proceed == 0){
          return;
        }
        require_once 'CRM/Booking/BAO/Slot.php';
        $results = CRM_Booking_BAO_Slot::copySlots($params);
        $isCreated = CRM_Utils_Array::value('is_created',$params);
        if($isCreated == 1){
          //TODO: Rediect to differnt page;
          CRM_Core_Session::setStatus( ts('Slots have been created') );
        }else{

        }

      
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
      $weeks = CRM_Utils_Array::value( 'weeks', $values );
      if ( $weeks == null || $weeks == '' ) {
        $errors['weeks'] = ts( 'No of weeks is a required field' );
      } 
      return empty( $errors ) ? true : $errors;
    }


}
