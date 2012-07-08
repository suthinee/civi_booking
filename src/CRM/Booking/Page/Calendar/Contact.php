<?php

require_once 'CRM/Core/Page.php';

/**
 *  Calendar view for contact
 */
class CRM_Booking_Page_Calendar_Contact extends CRM_Core_Page {
    public static $_contactId = null;

    function preProcess() {
        $this->_contactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->assign( 'contactId', $this->_contactId );
        
        require_once 'CRM/Contact/BAO/Contact.php';
        $displayName = CRM_Contact_BAO_Contact::displayName( $this->_contactId );
        $this->assign( 'displayName', $displayName );

        // check logged in url permission
        require_once 'CRM/Contact/Page/View.php';
        CRM_Contact_Page_View::checkUserPermission( $this );
        
        $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this, false, 'browse');
        $this->assign( 'action', $this->_action);

              $basePath = base_path();
        $this->assign( 'basePath', $basePath);

    }

   /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) {
        $this->preProcess( );

        return parent::run( );
    }

}


