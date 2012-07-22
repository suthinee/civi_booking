<?php

class CRM_Case_Utils{	

	static function processCase($caseId){

		if(!$caseId){
			return;
		}

		$params = array( 1 => array( $caseId, 'Integer'));

 
        $query = "
            SELECT a.id,
                 a.activity_type_id 
            FROM civicrm_activity a
            LEFT JOIN civicrm_case_activity ca ON ca.activity_id = a.id
            LEFT JOIN civicrm_case c ON c.id = ca.case_id
            WHERE c.id = %1
            AND a.is_current_revision = 1 
            AND a.activity_type_id IN (50, 57)";
          //require_once('CRM/Core/DAO.php'); 
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        $activity = array ();
        while ( $dao->fetch( ) ) {
            $activity[] = $dao->toArray();          
        }

        if($activity[0]){
            $typeId =  CRM_Utils_Array::value( 'activity_type_id', $activity[0] );
            $id =  CRM_Utils_Array::value( 'id', $activity[0] );
            $params = array('version' => 3,
                            'sequential' => 1,
                            'id' => $id,
                            'status_id' => 1);
            if($typeId == 57){ //if joined waiting list then set status = 2
               $params['status_id'] = 2; 
            }
            $results = civicrm_api("Activity","update", $params);

        }



	}


}