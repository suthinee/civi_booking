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
 * This class generates form components for Edit Room
 * 
 */
class CRM_Booking_Form_Room extends CRM_Core_Form {


    /**
     * Case Id
     */
    public $_roomId  = null;

    function preProcess(){ 
        echo 'preProcess';
        //TODO: Handle requests
        $roomId = CRM_Utils_Request::retrieve( 'roomId','Integer', $this ) ;  
        
    }

    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ){
       echo 'default';
        
        $defaults = array( );
        /*
        $defaults['is_reset_timeline'] = 1;
        
        $defaults['reset_date_time'] = array( );
        list( $defaults['reset_date_time'], $defaults['reset_date_time_time'] ) = CRM_Utils_Date::setDateDefaults( null, 'activityDateTime' );
        $defaults['case_type_id'] = $form->_caseTypeId; */

        return $defaults;
    }

    function buildQuickForm( ){
        echo 'buildform'; 

        $this->addElement('text', 
                        'room_no', 
                        ts('Room No:'));  


        $this->addElement('text', 
                        'type', 
                        ts('Room Type:'));  

        $this->addElement('text', 
                        'size', 
                        ts('Room Size:'));  

        $floorOpts = array('' => '-- Select --',
                           'Frist' => 'Frist', 
                           'Second' => 'Second', 
                           'Third' => 'Third',
                           'Forth' => 'Forth');

        $this->addElement('select', 
                        'floor', 
                        ts('Floor:'),
                        $floorOpts);

        $centreOpts = array('' => '-- Select --', 'AL' => 'AL', 'WS' => 'WS');
        $this->addElement('select', 
                        'centre', 
                        ts('Centre:'),
                        $centreOpts);  

        $this->addElement('text', 
                        'extension', 
                        ts('Extension No:'));  

        $status = array( 1 => 'Active', 0 => 'In Active');
        $this->addElement('select', 
                        'status', 
                        ts('Status:'),
                        $status);  

        $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => 'Add', 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel'),
                                            )
                                     ));
 
    }

    function postProcess(){ 
        echo 'post';
        exit;
        //TODO: Handle requests
        $roomId = CRM_Utils_Request::retrieve( 'roomId','Integer', $this ) ;  
        
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
        echo 'rule';
        return true;
    }


}
