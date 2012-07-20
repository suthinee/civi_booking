<?php

class CRM_Activity_Utils{	

    static function automateGenerateRegularSession(){
        $datetime = date('Y-m-d H:i:s');
        $params = array( 1 => array($datetime, 'String'));     
        $query = "SELECT c.id as case_id, 
                          a.id as activity_id,
                          a.*,
                          rs.*,
                          ai.*,
                          s.*
                    FROM civicrm_case c
                    LEFT JOIN civicrm_case_activity ca ON ca.case_id = c.id
                    LEFT JOIN civicrm_activity a ON a.id = ca.activity_id
                    LEFT JOIN civicrm_value_repeat_session_12 rs ON rs.entity_id = a.id
                    LEFT JOIN civicrm_value_additional_activity_info_8 ai ON ai.entity_id = a.id
                    LEFT JOIN civi_booking_slot s ON s.id = ai.slot_id_34
                    WHERE c.is_deleted = 0
                    AND c.status_id <>2
                    AND a.activity_type_id = 52
                    AND a.is_current_revision = 1
                    AND a.is_deleted = 0
                    AND a.activity_date_time = (SELECT max(sa.activity_date_time)
                                        FROM civicrm_activity sa 
                                        LEFT JOIN civicrm_case_activity sca ON sca.activity_id = sa.id
                                        WHERE sca.case_id = c.id
                                        AND sa.activity_type_id = 52
                                        AND sa.is_current_revision = 1
                                        AND sa.is_deleted = 0)
                    AND a.activity_date_time > %1
                    AND rs.final_assessment_47 is null
                    ORDER BY a.activity_date_time DESC";

          civicrm_initialize( ); 
          require_once('CRM/Core/DAO.php');   
          $dao = CRM_Core_DAO::executeQuery( $query, $params);
          $results = array();
          while ( $dao->fetch( ) ) {
            $results[] = $dao->toArray();
          }
          require_once('CRM/Booking/BAO/Slot.php');   

          $weeksForward = 5; //default to roll forward 
         // $activities = array();
          $uncreatableList = array();

          foreach ($results as $key => $value) {

            $slotDate = CRM_Utils_Array::value('slot_date',$value);

            $nextFiveWeek = strtotime("+ 5 week",strtotime($slotDate));
            if(strtotime($slotDate) > $nextFiveWeek ){
              //  continue;
            }

            $caseId = CRM_Utils_Array::value('case_id', $value);
            $activityId = CRM_Utils_Array::value('activity_id', $value);
            $slotId = CRM_Utils_Array::value('slot_id_34',$value);
            $startTime =  CRM_Utils_Array::value('start_time',$value); 
            $endTime =  CRM_Utils_Array::value('end_time',$value);
            $clinician_1 =  CRM_Utils_Array::value('clinician_contact_id',$value);
            $clinician_2 =  CRM_Utils_Array::value('attended_clinician_contact_id',$value);

            $isActiviyCreable = false;
            $slotIds = array();
            for($i = 0; $i < $weeksForward; $i++){ 
                $nextSevenDay = strtotime("+". $i + 1 . " week",strtotime($datetime));
                $nd = date('Y-m-d H:i:s', $nextSevenDay);
                $params = array( 1 => array($nd,'String'),
                             2 => array($startTime,'String'),
                             3 => array($endTime,'String'),
                             4 => array($clinician_1,'String'),
                             5 => array($clinician_2,'String'));
                $query = "SELECT id
                           FROM civi_booking_slot s
                           WHERE s.slot_date = %1
                           AND s.start_time = %2
                           AND s.end_time = %4
                           AND s.clinician_contact_id = %4
                           AND s.activity_type = 52
                        ";

                civicrm_initialize( ); 
                require_once('CRM/Core/DAO.php'); 
                $slotId    = CRM_Core_DAO::singleValueQuery( $query, $params );
                if(!$slotId){
                    $isActiviyCreable = false;
                    $slotIds = array();
                    $uncreatableList[] = array('activity_id' => $activityId,
                                               'description' => 'no slots found'
                                               );
                    break;
                }else{
                    $isActiviyCreable = true;
                    $slotIds[$i] = $slotId;
                }
            }

            if($isActiviyCreable && sizeof($slotIds) == 5){
                foreach ($slotIds as $key => $slodId) { civicrm_initialize( );
                   require_once 'CRM/Booking/BAO/Slot.php';
                   CRM_Booking_BAO_Slot::setSlotStatus(array('slot_id' => $slotId ,'status' => 2)); //set status to 2 to reserved 
     
                   $slotResults = CRM_Booking_BAO_Slot::getSlotById($slotId);
                   $slot = $slotResults[0]; //getSlotId
                   
                   $startTime = (CRM_Utils_Array::value('start_time',$slot)); 
                   $endTime   = (CRM_Utils_Array::value('end_time',$slot)); 

                   //Convert date to slot timestamp
                   $slotTimestamp = strtotime(CRM_Utils_Array::value('slot_date',$slot));

                   //Convert timestamp to date formate thst CiviCRM uses.
                   $params['activity_date_time'] =  date('m/d/Y', $slotTimestamp);
                   $params['activity_date_time_time'] = date('g:iA', strtotime($startTime));

                   //process date for proper formate
                   $params['activity_date_time'] = CRM_Utils_Date::processDate( $params['activity_date_time'], $params['activity_date_time_time'] );
                     
                   //set assignee contact ids
                   $params['assignee_contact_id'] = array();
                   $params['assignee_contact_id'][0] = CRM_Utils_Array::value('clinician_contact_id',$slot);
                   if(CRM_Utils_Array::value('attended_clinician_contact_id',$slot)){
                     $params['assignee_contact_id'][1] = CRM_Utils_Array::value('attended_clinician_contact_id',$slot);
                   }
                   
                   //set activity location
                   $params['location'] = CRM_Utils_Array::value('centre',$slot);
                   $params['details'] = 'Room no: ' . CRM_Utils_Array::value('room_no',$slot);
                   $params['duration'] = round(abs(strtotime($endTime) - strtotime($startTime)) / 60,2);               

                      //TODO: add session service
                   //hacked adding session service for custom field'
                   $params['custom_16_-1'] =  CRM_Utils_Array::value('session_service',$slot);
                   $params['custom']['16']['-1']['value'] =  CRM_Utils_Array::value('session_service',$slot);

                   $params['custom_22_-1'] = CRM_Utils_Array::value('centre',$slot);
                   $params['custom']['22']['-1']['value'] = CRM_Utils_Array::value('centre',$slot);
                   
                   
                   //slotId
                   $params['custom_34_-1'] = CRM_Utils_Array::value('id',$slot);
                   $params['custom']['34']['-1']['value'] = CRM_Utils_Array::value('id',$slot);
                
                   $params['subject'] = 'Initial appointment';
                   $params['source_contact_id'] = 102; //TODO: Hook to use contact Id use system admin as default
                   $params['activity_type_id'] = 52;
                   $params['status_id'] = 1;
                   $params['priority_id'] = 2;
                   $params['case_id'] = $caseId;

                   $params['version'] = 3;
                   $params['check_permissions'] = FALSE;

                   require_once 'api/api.php';
                   $results = civicrm_api("Activity","create", $params);

                   if($results['is_error'] == 1){
                      CRM_Booking_BAO_Slot::setSlotStatus(array('slot_id' => $slotId ,'status' => 1)); //set status to 2 to availables 
                      //TODO: Addd activity uncreableList if activity cannot be created with reason
                      $uncreatableList[] = array('activity_id' => $activityId,
                                                 'description' => $results['error_message']
                                                );

                   }
                }
            }
            
          }
         if(sizeof($uncreatableList) != 0){
            $query= db_insert('civi_booking_failure_log')
                  ->fields(array(
                          'entity_id',
                          'created_time',
                          'data',
                          'type'
                         
                ));
              $txn = db_transaction();
              try{
                foreach ($uncreatableList as $obj) {
                $record = array('entity_id' => $obj['activity_id'],
                                  'created_time' => $datetime,
                                  'data' => $obj['description'],
                                  'type' => 'automate_regular_session');
                  $query->values($record);
                }
                $query->execute();
              }catch(Exception $e){
               return $result = array(
                        'is_error' => 1,
                        'messages' => $e->getMessage() );
              }
         }
        return $result = array(
                    'is_error' => 0,
                    'messages' => ts( 'Corn job run successfully' ) );
        
    }

    static function sendActivityReminder($params){
        $dayRange = CRM_Utils_Array::value( 'day_range', $params) ;
        $activityType = CRM_Utils_Array::value( 'activity_type', $params) ;
  
        if($dayRange == null && $activityType == null){
            return $result = array(
                        'is_error' => 1,
                        'messages' => ts('Parameters are required') );
        }
        if($activityType == 'regular_feedback'){
            $day =  date('Y-m-d', strtotime('-'. $dayRange .' months'));            
        }else $day =  date('Y-m-d', strtotime('+'. $dayRange .' days'));
        $now = date('Y-m-d');

        $date1 = new DateTime($day);
        $date2 = new DateTime($now);
        $interval = $date1->diff($date2);
       
        require_once 'CRM/Core/BAO/Domain.php';
        
        $domainValues     = CRM_Core_BAO_Domain::getNameAndEmail( );
        $fromEmailAddress = "$domainValues[0] <$domainValues[1]>";

        if($activityType == 'assessment' || $activityType == 'intake_form'){
             $activityTypeID   = CRM_Core_OptionGroup::getValue( 'activity_type', 
                                                                    'Initial Assessment', 'name' );
             $activityStatusID = CRM_Core_OptionGroup::getValue( 'activity_status', 
                                                                    'Scheduled', 'name' );
             $isFinalReminder = $interval->d == 1 ?  true : false;
        }else if($activityType == 'regular_feedback'){ 
            $activityTypeID   = CRM_Core_OptionGroup::getValue( 'activity_type', 
                                                                    'Regular Session', 'name' );
            $activityStatusID = CRM_Core_OptionGroup::getValue( 'activity_status', 
                                                                    'Completed', 'name' );
        }else{
             return $result = array(
                        'is_error' => 1,
                        'messages' => ts('Activity is not matched') ); 
        }

   




        $query = "
SELECT civicrm_activity.id as activity_id,
       civicrm_activity.activity_date_time as activity_date_time,
       civicrm_contact.sort_name as counsellor
FROM  civicrm_activity 
LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
LEFT JOIN civicrm_case ON civicrm_case.id = civicrm_case_activity.case_id

LEFT JOIN civicrm_activity_assignment ON civicrm_activity_assignment.activity_id = civicrm_activity.id
LEFT JOIN civicrm_contact ON civicrm_contact.id = civicrm_activity_assignment. assignee_contact_id

WHERE civicrm_activity.activity_type_id = {$activityTypeID}
AND civicrm_activity.status_id = {$activityStatusID}
AND civicrm_activity.is_current_revision = 1
AND civicrm_activity.is_deleted = 0
AND DATE_FORMAT(civicrm_activity.activity_date_time, '%Y-%m-%d') = %1";

        $dao = CRM_Core_DAO::executeQuery( $query,  array( 1 => array( $day, 'String' ) ) );
        require_once 'CRM/Core/BAO/MessageTemplates.php';
        require_once 'CRM/Case/BAO/Case.php';
        $isEmailSent = false;
        $isError = 0;
        $errorMsg = '';




        while ( $dao->fetch() ) {


  
            $activityId =  $dao->activity_id;
            $activityTime =  $dao->activity_date_time; // to disply APP date and Apptime 
            $counsellor = $dao->counsellor;
           // print ($counsellor);
            //exit;

            $caseId = CRM_Case_BAO_Case::getCaseIdByActivityId($activityId);
            $contacts = CRM_Case_BAO_Case::getContactNames($caseId);
            $recordActivities= array();
            $emailInfo = array();
            $isEmailSent = false;


            $today = date("m F Y"); /// display current date
   
            
            foreach ($contacts as $contact) {

                    //get contacts from case id 
                    require_once 'api/api.php';
                    $results = civicrm_api("Case","get", array ('version' => '3',
                                                                'q' =>'civicrm/ajax/rest', 
                                                                'sequential' =>'1', 
                                                                'case_id' => $caseId ));

                    $tplParams = array();
                    $tplParams['today'] =  $today;
                    $tplParams['activityTime']=$activityTime; // send value to template
                    $tplParams['counsellor']=$counsellor;

                    //get contacts from array returned 
                    $cons = $results['values'][$caseId]['contacts'];

                    foreach($cons as $con){
                        //check if this contact role is Client
                        if($con['role'] == 'Client'){
                            //check if this contact is not the current contact
                            if($con['contact_id'] != $contact['contact_id']){
                                 //assign the partner contact to template
                                 $tplParams['partnerName'] = $con['sort_name'];
                            }
                        }
                    }   

                    //dump($tplParams);

                    

                    $toEmail = CRM_Utils_Array::value('email', $contact);
                    $displayName = CRM_Utils_Array::value('display_name', $contact);
                    $contactId = CRM_Utils_Array::value('contact_id', $contact);
                    $emailInfo['displayName'] = $displayName;
                    $templateId = null;
                    if($interval->d == '7'){ // Intitial Reminder
                        $templateId = '48'; 
                    }if($interval->d == '1'){ // Final Reminder
                        if($activityType == 'assessment'){
                            $templateId = '49'; 
                        }else if($activityType == 'intake_form'){
                            $templateId = '50'; 
                        }
                    }if($interval->m == '3'){ // Regular Feedback
                        $templateId = '51'; 
                    }
                    if($activityType == 'regular_feedback'){
                         if(self::isRegularFeedbackExist($caseId)){
                          continue 0; //Starts next iteration                          
                        }
                    }
                    if ( $toEmail && $templateId != null ) {
                        list( $sent, $subject, $text, $html)  = CRM_Core_BAO_MessageTemplates::sendTemplate(array(
                            'groupName'         => null,    // option group name of the template
                            'valueName'         => null,    // option value name of the template
                            'messageTemplateID' => $templateId ,    // ID of the template
                            'contactId'         => $contactId,    // contact id if contact tokens are to be replaced
                            'tplParams'         => $tplParams, // additional template params (other than the ones already set in the template singleton)
                            'from'              => $fromEmailAddress,    // the From: header
                            'toName'            => $displayName,    // the recipient’s name
                            'toEmail'           => $toEmail,    // the recipient’s email - mail is sent only if set
                            'cc'                => null,    // the Cc: header
                            'bcc'               => null,    // the Bcc: header
                            'replyTo'           => null,    // the Reply-To: header
                            'attachments'       => null,    // email attachments
                            'isTest'            => false,   // whether this is a test email (and hence should include the test banner)
                            'PDFFilename'       => null,    // filename of optional PDF version to add as attachment (do not include path)
                        ));
        
                        $isEmailSent = $sent ? true : false;
                        if ( !$isEmailSent ) {
                            $isError = 1;
                            $errorMsg =  'Cannot send email';
                        }else{
                            $emailInfo['subject'] = $text;
                            $emailInfo['text'] = $text;
                            $emailInfo['html'] = $html;
                        }
                        $recordActivities[] = $emailInfo;
                    } else {
                        $isError  = 1;
                        $errorMsg = "Couldn\'t find recipient\'s email address.";
                    }
                    exit;
                }
            

        
            if($isEmailSent){
                //use the activity creator as userID
                $userID = CRM_Core_DAO::getFieldValue('CRM_Activity_DAO_Activity', $activityId, 'source_contact_id');

                $activityParams = array( );
                require_once "CRM/Activity/BAO/Activity.php";

                if($activityType == 'assessment'){
                        if($isFinalReminder){
                            $activityParams['activity_type_id']   = CRM_Core_OptionGroup::getValue( 'activity_type', 'Final Initial Assessment Reminder', 'name' );
                            $activitySubject = 'Sent a final reminder to ';
                         }else{
                            $activityParams['activity_type_id']   = CRM_Core_OptionGroup::getValue( 'activity_type', 'Initial Assessment Reminder', 'name' );
                            $activitySubject = 'Sent a reminder to ';
                        }
                }else if($activityType == 'intake_form'){
                    $activityParams['activity_type_id']   = CRM_Core_OptionGroup::getValue( 'activity_type', 'Initial Intake Form', 'name' );
                    $activitySubject = 'Sent an intake form email to ';
                }else if($activityType == 'regular_feedback'){
                    $activityParams['activity_type_id']   = CRM_Core_OptionGroup::getValue( 'activity_type', 'Regular Feedback', 'name' );
                    $activitySubject = 'Sent a regular feedback email to ';
                }


                $activityParams['source_record_id']   = $activityId; 
                $activityParams['source_contact_id']  = $userID; 
                $activityParams['subject']            = $activitySubject . CRM_Utils_Array::value('displayName', $recordActivities[0]) . '; ' .  CRM_Utils_Array::value('displayName', $recordActivities[1]);
                $activityParams['activity_date_time'] = date('YmdHis');
                $activityParams['status_id']          = CRM_Core_OptionGroup::getValue( 'activity_status', 'Completed', 'name' );
                $activityParams['case_id']            = $caseId;
                $activityParams['is_auto']            = 1;
                $htmlVal = CRM_Utils_Array::value('html', $recordActivities[0]);
                if(empty($htmlVal)){
                    $activityParams['details']            = CRM_Utils_Array::value('text', $recordActivities[0]);
                }else{
                    require_once('CRM/Utils/String.php');
                    $activityParams['details']            = CRM_Utils_String::htmlToText(CRM_Utils_Array::value('html', $recordActivities[0]));
                }
                    
                $activity = CRM_Activity_BAO_Activity::create( $activityParams );

                //create case_activity record if its case activity.
                if ( $caseId ) {
                    $caseParams = array( 'activity_id' => $activity->id,
                                         'case_id'     => $caseId );
                    CRM_Case_BAO_Case::processCaseActivity( $caseParams );
                } 
              
            }       
        
        }
        $dao->free();

        if( $isError==1){
            return $result = array(
                        'is_error' => $isError,
                        'messages' => ts($errorMsg) );
        }else{                
            return $result = array(
                        'is_error' => 0,
                        'messages' => ts( 'Sent all scheduled reminders successfully' ) );
        }
    }  

    static function isRegularFeedbackExist($caseId){
        $activityTypeID   = CRM_Core_OptionGroup::getValue( 'activity_type', 
                                                                    'Regular Feedback', 'name' );
        $activityStatusID = CRM_Core_OptionGroup::getValue( 'activity_status', 
                                                                    'Completed', 'name' );
        
       $query =  "
SELECT *
FROM  civicrm_activity 
LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
LEFT JOIN civicrm_case ON civicrm_case.id = civicrm_case_activity.case_id  
WHERE civicrm_case.id = %1
AND civicrm_activity.activity_type_id = {$activityTypeID}
AND civicrm_activity.status_id = {$activityStatusID}
AND civicrm_activity.is_deleted = 0
";
       $isExist = false;
       $dao = CRM_Core_DAO::executeQuery( $query,  array( 1 => array( $caseId, 'Integer' ) ) );
       while ( $dao->fetch() ) {
           $isExist = true;
           break;
       }
       return $isExist;
    } 
 
}
 

?>