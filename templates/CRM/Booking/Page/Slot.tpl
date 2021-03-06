{* this template is used for displaying slot calendar *}
<div id="content">
<div>
	<div class="schedule_title">

	</div>
	<div class="schedule_dates">
		<a href="{$lastWeekUrl}" ><span class="arrow-left">&nbsp;&nbsp;&nbsp;</span></a>
		 {$startDate} - {$endDate}<a href="{$nextWeekUrl}"><span class="arrow-right">&nbsp;&nbsp;&nbsp;&nbsp</span></a>
		 <div style="text-align:right"></div>
	</div>
</div>

<div style="text-align: right; margin: auto;">  
<div class="legend reservable">Reservable</div>
<div class="legend pasttime">Past</div><br>
</div>

<div style="text-align: left; margin: auto;">  
<div class="legend initial-assessment">Initial Assessment</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<div class="legend initial-assessment-counselling">Counselling</div>
<div class="legend initial-assessment-psychotherapy">Psychotherapy</div>
<div class="legend initial-assessment-psychosexual">Psychosexual</div>
<div class="legend initial-assessment-parenting">Parenting Together</div> 
<div class="legend initial-assessment-wellbeing">Wellbeing</div>
<div class="legend initial-assessment-dsu">DSU</div> 
<div class="legend initial-assessment-unknown">Unknown</div><br><br>
</div>

<div style="text-align: left; margin: auto;"> 
<div class="legend supplementary-assessment">Supplementary Aessessment</div>&nbsp;
<div class="legend supplementary-assessment-counselling">Counselling</div>
<div class="legend supplementary-assessment-psychotherapy">Psychotherapy</div>
<div class="legend supplementary-assessment-psychosexual">Psychosexual</div>
<div class="legend supplementary-assessment-parenting">Parenting Together</div> 
<div class="legend supplementary-assessment-wellbeing">Wellbeing</div>
<div class="legend supplementary-assessment-dsu">DSU</div>
<div class="legend supplementary-assessment-unknown">Unknown</div><br><br> 
</div>

<div style="text-align: left; margin: auto;"> 
<div class="legend regular-session">Regular session </div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!--<div class="legend unreservable">Unreservable</div>-->
<div class="legend regularsession-counselling">Counselling</div>
<div class="legend regularsession-psychotherapy">Psychotherapy</div>
<div class="legend regularsession-psychosexual">Psychosexual</div>
<div class="legend regularsession-parenting">Parenting Together</div> 
<div class="legend regularsession-wellbeing">Wellbeing</div>
<div class="legend regularsession-dsu">DSU</div>  <!-- <div class="legend reserved pending">Pending</div> -->
</div>
<!-- <div class="legend restricted">Restricted</div> -->

<div style="text-align: right; margin: auto;"><a href="{$copySlotsURL}">Copy slots</a></div>
<div style="height:10px">&nbsp;</div>
<form id="filterForm"> 
	<fieldset class="filters">
	<legend onclick="toggleFieldset(this);">Filters</legend>	
	<tabl id="filterTable" style="display: none;">
		<tr>
			<td>
				<label for="dateFilter">Date: </label>
				<select class="dateFilter" name="dateFilter">
				    <option value="all">-- All dates --</option>
					{foreach from=$slots key=k item=day}
						<option value="{$k}">{$day.date}</option>
					{/foreach}	
				</select>
			</td>
			<!-- for room filter nee modifie -->
			<td>
				<label for="roomFilter">Room: </label>
				<select id="roomFilter" name="roomFilter">
				    <option value="all">-- All rooms --</option>
				 	{foreach from=$rooms key=k item=r}
						<option value="{$r.room_no}">{$r.room_no}</option>
					{/foreach}	
				</select>
			</td>
			<!-- for floor filter -->
			<td>
				<label for="floorFilter">Floor: </label>
				<select id="floorFilter" name="floorFilter">
				    <option value="all">-- All Floor --</option>
				 	{foreach from=$floors key=k item=floors}
						<option value="{$floors.floor}">{$floors.floor}</option>
					{/foreach}	
				</select>
			</td>
			<!-- for centre filter -->
			<td>
				<!--<label for="centreFilter">Centre: </label>-->
				<span class="centreFilter">Centre:</span>
				<select id="centreFilter" name="centreFilter">
				    <option value="all">-- All centre --</option>
				    <option value="AL"> Artillery Lane </option>
				    <option value="WS"> Warren Street </option>
				 	<!-- {foreach from=$rooms key=k item=c}
						<option value="{$c.room_centre}">{$c.room_centre}</option>
					{/foreach}	-->
				</select>
			</td>
		</tr>

	</table>
	
	</fieldset>
</form>
<div style="height:10px">&nbsp;</div>

{foreach from=$slots key=dayKey item=day}
<table id={$dayKey} class="reservations" border="0" cellpadding="0" width="100%">
	<tbody>
		<tr>
			<td class="resdate">{$day.date}</td>
			{foreach from=$day.timeOptions item=time}
				{if $time.isDisplay eq 1}
				<td class="reslabel"><span class="textAlignVer">{$time.time}</span></td>
				{else}
				<td class="reslabel"></td>
				{/if}
			{/foreach}				
		</tr>

			{foreach from=$day.rooms key=roomKey item=room}
				<tr class="slots">
					<td class="resourcename {$room.room_id}">
						<span class="roomText">{$room.room_no}</span>,
						<span class="floorText">{$room.room_floor}</span>,
						<span class="centreText"> {$room.room_centre}</span> </td>
					{foreach from=$room.tdVals key=key item=value}
		        	<td id="{$value.tdataId}" colspan="1" class="slot {$value.className}" slotId="{$value.slotId}">
						{$value.text}	        
						<div style="display:none">
			        	<div style="display:none">
			        	<span class='time'>{$value.timeKey}</span>
			        	<span class='defaultEndtime'>{$value.defaultEndTime}</span>
								<span class='roomNo'>{$room.room_no}</span>
								<span class='roomId'>{$roomKey}</span>
								<span class='date'>{$day.date}</span>
								<span class='unixDate'>{$dayKey}</span>
								<span class='slotId'>{$value.slotId}</span>
						</div>
					</td>	
					{/foreach}								
	  			</tr>
			{/foreach}
						
	</tbody>
</table>
{/foreach}
</div>

<div id="confirm-dialog">Are you sure you wish to continue? This cannot be undone.</div>
<div id="slotDetailDialog"> 
	<div id="slotDetailsError" class="creatError"> </div>

	<div id="activity-content">
		
	</div >
</div>

<div id="slotDialog" title="Create a slot" class="ui-dialog-content ui-widget-content">
	<div id="creatError" class="creatError"> </div>
	<form  class="cmxform" id="dialogForm">
			<ul>
				<li>
					<span class="label_text">Date: </span>
					<span id="dateHolder"></span> 
				</li>
				<li>
					<span class="label_text">Room: </span>
					<span id="roomNo"></span> 
					<span id="hiddenRoomId" style="display:none;"></span> 
				</li>
				<li>
					<label for="startSelect" >Start Time: </label>
					<select id="startSelect" name="startSelect">
						<option value="">Select Start time</option>
						{foreach from=$timeOptions key=k item=time}
							<option value="{$k}">{$time.time}</option>
						{/foreach}					
					</select>
				</li>
				<li>
					<label for="endSelect">End Time: </label>
					<select id="endSelect" name="endSelect">
						<option value="">Select End time</option>
						{foreach from=$timeOptions key=k item=time}
							<option value="{$k}">{$time.time}</option>
						{/foreach}					
					</select>
				</li>
				<li>
					<label for="slotType">Slot type: </label>
					<input id="cCheck"  type="radio" name="slotType" value="1" checked> Counselling slot<br>
					<input id="gCheck" type="radio" name="slotType" value="2" > Meeting/Other<br>
				</li>
				<div id="counsellingGroup">
					<li>
						<label for="activitySelect">Activity type: </label>			
						<select id="activitySelect" name="activitySelect">
							{foreach from=$activityTypes key=k item=activityType}
								<option value="{$k}">{$activityType}</option>
							{/foreach}		
						</select>
					</li>
					<li>
						<label for="sessionSelect">Session service: </label>
						<select id="sessionSelect" name="sessionSelect">
							{foreach from=$sessionServices key=k item=sessionService}
								<option value="{$k}">{$sessionService}</option>
							{/foreach}		
						</select>	
					</li>
					<li>				
						<label for="counsellor">Counsellor 1: </label>
						<select id="counsellor" name="counsellor">
							<option value="">Select Counsellor</option>
							{foreach from=$contacts key=k item=contact}
								<option value="{$k}">{$contact}</option>
							{/foreach}		
						</select>
					</li>
					<li>					
						<label for="counsellor2">Counsellor 2: </label>
						<select id="counsellor2" name="counsellor2">
							<option value="">Select Counsellor</option>
							{foreach from=$contacts key=k item=contact}
								<option value="{$k}">{$contact}</option>
							{/foreach}		
						</select>
					</li>
				</div>
				<div id="generalGroup">
					<li>				
						<label for="contacts">Participants: </label>
						<input type="text" name="contacts" />
					</li>
					<li>					
						<div id="participantList">
				   		<ul id="ulParticipants">
			
						</ul>
						</div>
					</li>
				</div>
				<li>
					<label for="decription">Description</label>
					<textarea rows="4" cols="50" id="description" name="description" style="resize: none;" class="required" ></textarea>
				</li>
			</ul>
		</form>
</div>

{literal}
<script type="text/javascript">
  var crmajaxURL = '{/literal}{php} print base_path(); {/php}{literal}civicrm/ajax/rest';
  //var startTime = null;
  var unixDate = null;
  //var participantList = new Array();
  //var roomId = null;

	cj(window).load(function(){

	cj('#generalGroup').hide();

		cj("#roomFilter").change(function(event) {
			var room_no = cj('select[name="roomFilter"]').val(); 
    	  cj('tbody tr td.resourcename').each(function() { 
    	  		var r = cj(this).find('span.roomText').text();
    	  		if (room_no != r) {
					cj(this).parent().hide(); 
				}else{
    	    		cj(this).parent().show();    	    	
    	    	}
    	    	if(room_no.localeCompare('all') == 0){
    	    		cj(this).parent().show();
    	    	}

  			});
		});

		cj("#dateFilter").change(function(event) {
			var date = cj('select[name="dateFilter"]').val(); 
			cj('table.reservations').each(function() { 
    	    	if(date != cj(this).attr('id')){
    	    		cj(this).hide();    	    	
    	    	}else{
    	    		cj(this).show();
    	    	}
    	    	if(date.localeCompare('all') == 0){
    	    		cj(this).show();
    	    	}    	    	

  			});
		});

		cj("#floorFilter").change(function(event) {
			var floor = cj('select[name="floorFilter"]').val(); 
			cj('tbody tr td.resourcename').each(function() { 
    	    	var f = cj(this).find('span.floorText').text();
    	    	if(floor != f){
    	    		cj(this).parent().hide();    	    	
    	    	}else{
    	    		cj(this).parent().show();    	    	
    	    	}
    	    	if(floor.localeCompare('all') == 0){
    	    		cj(this).parent().show();
    	    	}   
  			});
		});

		cj("#centreFilter").change(function(event) {
			var centre = cj.trim(cj('select[name="centreFilter"]').val()); 
			cj('tbody tr td.resourcename').each(function() { 
    	    	var c = cj.trim(cj(this).find('span.centreText').text());
    	    	if(centre != c){
    	    		cj(this).parent().hide();    	    	
    	    	}else{
    	    		cj(this).parent().show();    	    	
    	    	}
    	    	if(centre.localeCompare('all') == 0){
    	    		cj(this).parent().show();
    	    	}   
  			});
		});



	cj.validator.addMethod("greaterThan", function(value, element) {
        var sTime = parseInt(cj('select[name="startSelect"]').val());
  		var eTime =	parseInt(cj('select[name="endSelect"]').val());
  		var val = sTime < eTime || value == "";
  		return val; 
     }, "End time must be after start time");

    cj.validator.addMethod("notEqaulTo", function(value, element) {
      	var contactId = cj('select[name="counsellor"]').val();    // get the value from a dropdown select
 		var contactId2 = cj('select[name="counsellor2"]').val(); 
  		var val = contactId != contactId2 || value == "";
  		 return val; 
    }, "Counsellor 2 must not be same as Counsellor 1");
    
    /*
    cj.validator.addMethod("hasPaticipant", function(value, element) {
    	var type = cj('input[name="slotType"]:checked').val();
    	console.log(type);
    	if(type == 1){
    		return false;
    	}else{
    		var has = false;
	    	cj('#ulParticipants').find('li').each(function(){
	    		has = true;
	    		return;
	    	});
	    	return has;
    	}
  		//return cj('#ulParticipants').has('li')?true:false; 
    }, "At leat one participant required"); */

    var validator = cj("#dialogForm").validate({
       	  rules: {
		    	startSelect: "required",
		   		endSelect:  {
		    		"greaterThan" : true,
		    		"required" : true
		    	},
				activitySelect: {"required": "#cCheck:checked"},
				counsellor: {"required": "#cCheck:checked"},
				sessionSelect: {"required": "#cCheck:checked"},
				counsellor2: "notEqaulTo"/*,
				contacts: "hasPaticipant"*/	  
		 	},
		 	onfocusout: false 
		});

    cj('input[name="slotType"]').change(function() {
		if (cj(this).val() === '1') {
			cj('#generalGroup').hide();
			cj('#counsellingGroup').show();
			/*
			cj('#activitySelect').rules("add", "required");
			cj('#counsellor').rules("add",  "required");
			cj('#sessionSelect').rules("add", "required");
			cj('#counsellor2').rules("add", "notEqaulTo");*/


		 } else if (cj(this).val() === '2') {
		    cj('#generalGroup').show();
			cj('#counsellingGroup').hide();
			/*
			cj('#activitySelect').rules("remove");
			cj('#counsellor').rules("remove");
			cj('#sessionSelect').rules("remove");
			cj('#counsellor2').rules("remove");*/
		} 
	});

	var contactUrl = crmajaxURL + "?fnName=civicrm/contact/search&json=1&contact_type=Individual&return[sort_name]=1";

	cj('input[name="contacts"]').autocomplete( contactUrl, {
		dataType:"json",
		extraParams:{sort_name:function (){ //extra % to force looking to the data typed anywhere in the name
			return "%"+cj('input[name="contacts"]').val();
			}
		},
		formatItem: function(data,i,max,value,term){
			return value;
		}, 
		parse: function(data){ //reformat to something the plugin expects
			var acd = new Array();
			for(cid in data){
				acd[acd.length] = { data:data[cid], value:data[cid].sort_name, result:data[cid].sort_name };
			}
			return acd;
		},
		width: 500,
		selectFirst: true,
		mustMatch: true

		
	}).result( function( event, data, formatted ) {
		cj('#ulParticipants').append('<li id="'+ data.contact_id+'"><a href="#" class="delParticipant"><span class="user-minus">&nbsp;</span></a>' + data.sort_name + '</li>');
		cj('input[name="contacts"]').val('').focus();
    });


	cj(".delParticipant").live('click', function(){
		cj(this).parent().remove();
	});
	
    // added slot id *****
	cj("td.slot").live('click', function(){
			if(cj(this).hasClass('reservable')){
	        	startTime = cj(this).find('span.time').text();
	        	date = cj(this).find('span.date').text();
	        	unixDate = cj(this).find('span.unixDate').text();
	        	roomNo = cj(this).find('span.roomNo').text();
	        	defaultEndtime = cj(this).find('span.defaultEndtime').text();
	        	roomId = cj(this).find('span.roomId').text();
	        	slotId = cj(this).find('span.slotId').text(); //slot id show type name ********

	        	cj('#dateHolder').text(date);
	        	cj('#roomNo').text(roomNo);
	        	cj('#slotId').text(slotId); // just added 24/07/2012 ********
	        	cj('#hiddenRoomId').text(roomId);
				cj('#startSelect option[value=' +startTime+ ']').attr('selected', 'selected');
				cj('#endSelect option[value=' +defaultEndtime+ ']').attr('selected', 'selected');

				cj( "#slotDialog" ).dialog({				
					autoOpen: false,
					resizable: false,
					draggable: false,
		   			width:480,
					height:680,
					modal: true,
					open : function(){

				    cj('#cCheck').attr('checked', 'checked');

					cj('#generalGroup').hide();
					cj('#counsellingGroup').show();	
					//Removed disable attribute
             		cj('#startSelect').removeAttr('disabled');''
             		cj('#endSelect').removeAttr('disabled');
             		cj('#activitySelect').removeAttr('disabled');
             		cj('#sessionSelect').removeAttr('disabled');
             		cj('#counsellor').removeAttr('disabled');
             		cj('#counsellor2').removeAttr('disabled');
					    },
					    buttons: {
					    	'Create a slot': function() {
					    	    		
					    		if(cj("#dialogForm").valid()){	
					    			var startTime = cj('select[name="startSelect"]').val(); 
							        var endTime = cj('select[name="endSelect"]').val(); 
							        var description = cj('#description').val();		
							    	var roomId = jQuery.trim(cj('#hiddenRoomId').text());
							    	var slotId = jQuery.trim(cj('#slotId').text()); // slotId just added 24/07  
							      
					    			var type = cj('input[name="slotType"]:checked').val();
					    			if(type == 1){
						    			var contactId = cj('select[name="counsellor"]').val();    // get the value from a dropdown select
				 					    var contactId2 = cj('select[name="counsellor2"]').val(); 
							    		
							    		var sessionService = cj('select[name="sessionSelect"]').val(); 
							    		var activityType = cj('select[name="activitySelect"]').val(); 
							    		
							    	    cj().crmAPI ('slot','create',{'version' :'3', 
							    	    							  'sequential' :'1',
							    	    							  'contact_id' : contactId, 
				  			    	    							  'contact_id_2' : contactId2, 
							    	    							  'date' : unixDate, 
							    	    							  'start_time' : startTime, 
							    	    							  'end_time' : endTime, 
							    	    							  'session_service' :sessionService, 
							    	    							  'room_id' : roomId, 
							    	    							  'activity_type' : activityType,
							    	    							  'description' : description,
							    	    							  'type' : type},{
								           ajaxURL: crmajaxURL,
								           success:function (data){ 
								           	if(data.values[0].is_created == 1){
								           		window.location.reload(true);
								           }else {
								            	var errorMessage = data.values[0].error_message;
								           		 cj('#creatError').html('' + errorMessage.toString());
								            } 
								          }
								        });
						    		}else if(type == 2){
						    			var contacts = new Array();
						    			cj('#ulParticipants').find('li').each(function(){
								    		contacts.push(cj(this).attr('id'));
								    	});

								    	console.log(contacts);
						    			cj().crmAPI ('slot','create',{'version' :'3', 
							    	    							  'sequential' :'1',
							    	    							  'date' : unixDate, 
							    	    							  'start_time' : startTime, 
							    	    							  'end_time' : endTime, 
							    	    							  'room_id' : roomId, 
							    	    							  'description' : description,
							    	    							  'contacts' : contacts,
							    	    							  'type': type},{
								           ajaxURL: crmajaxURL,
								           success:function (data){ 
								           	if(data.values[0].is_created == 1){
								           		window.location.reload(true);
								           }else {
								            	var errorMessage = data.values[0].error_message;
								           		 cj('#creatError').html('' + errorMessage.toString());
								            } 
								          }
								        });

						    		}
								}
		        		},
					    Cancel: function() {
					    		validator.resetForm();
		    					cj("#dialogForm")[0].reset();
		    					cj('#ulParticipants').html('');
					      	    cj(this).dialog('destroy');
					    	}			        
						}
						});
				cj( "#slotDialog" ).dialog('open'); 

        	}else if(!cj(this).hasClass('pasttime')){
        		slotId = cj(this).find('span.slotId').text();
        		cj( "#slotDetailDialog" ).data('obj', slotId)
	        					   .dialog('open');   

        	} 
		}).hover(function(){
			if(cj(this).hasClass('reservable')){
   				cj(this).css("background","#40d288");
   			}
    	},function(){
			if(cj(this).hasClass('reservable')){
			    cj(this).css("background","#ffffff");
			}
		});

		
		cj("#delButton").live('click', function(event){
			var sid = cj('#viewSlotId').text();
			cj( "#confirm-dialog" ).data('obj', sid)
	        					  			 .dialog('open');  
    });

    cj("#confirm-dialog").dialog({
    	  stack: true,
    	  autoOpen: false,
				modal: true,
			  resizable: false,
			  draggable: false,
	      buttons : {      
	        "Confirm" : function() {
	        	 var sid = cj(this).data('obj'); // Get the stored result
	        	 cj().crmAPI ('slot','delete',{'version' :'3','id' : sid},{
	        	 	 ajaxURL: crmajaxURL,
					     success:function (data){
					     		window.location.reload(true);
					     }});				    
	        },
	        "Cancel" : function() {
	          cj(this).dialog("close");
	        }
	      }				
    	});

		cj( "#slotDetailDialog" ).dialog({				
			    autoOpen: false,
			    resizable: false,
			    draggable: false,
   			  	width:450,
			    height:400,
			    modal: true,
			    title: "View slot" ,
   				open: function(event, ui) { 
   					var sid = cj(this).data('obj'); // Get the stored result
   					cj().crmAPI ('slot','get_by_id',{'version' :'3', 'sequential' :'1','sid' : sid},{
					           ajaxURL: crmajaxURL,
					           success:function (data){ 
					           	var slot = data.values[0];

					           	console.log(slot);
					           //	var date = new Date(slot.slot_date);
					           //	console.log(slot.slot_date);
					           
					           	var status = (slot.status == 1) ? 'Avalibale' :'Appointment';
					           	var slotHtml = '<table class="crm-info-panel" id="crm-activity-view-table"> <!-- reused activity css -->';
					           	slotHtml += '<tr><td class="label">Slot reference Id</td><td id="viewSlotId">' + slot.id + '</td></tr>';
					           	//slotHtml += '<tr><td class="label">Slot date</td><td id="viewSlotDate">' + cj.datepicker.formatDate('DD d/MM/yy', date); + '</td></tr>';
					           	slotHtml += '<tr><td class="label">Slot date</td><td id="viewSlotDate">' + slot.slot_date + '</td></tr>';
					           	slotHtml += '<tr><td class="label">Start time</td><td id="viewStartTime">' + slot.start_time + '</td></tr>';
					           	slotHtml += '<tr><td class="label">End time</td><td id="viewEndTime">' + slot.end_time + '</td></tr>';
					           	if(slot.activity_type != 0){
					           		var counsellor2 = (slot.attended_clinician_contact_display_name == null || slot.attended_clinician_contact_display_name == '' ) ? '-' : slot.attended_clinician_contact_display_name
						           	slotHtml += '<tr><td class="label">Counsellor 1</td><td id="viewCounsellor1">' + slot.clinician_contact_display_name + '</td></tr>';
						           	slotHtml += '<tr><td class="label">Counsellor 2</td><td id="viewCounsellor2">' + counsellor2 + '</td></tr>';
						           	slotHtml += '<tr><td class="label">Activity type</td><td id="viewActivityType">' + slot.activity_type + '</td></tr>'; 	
						           	slotHtml += '<tr><td class="label">Session service</td><td id="viewSessionService">' + slot.session_service + '</td></tr>';
					            }else{
						           	//slotHtml += '<tr><td class="label">Attendee</td><td id="viewAttendee">Attendee</td></tr>';
						           	for (i in slot.attendee) {  
						           		if(i == 0){
						           			slotHtml += '<tr><td class="label">Attendee</td><td id="viewAttendee">'+ slot.attendee[i].sort_name; +'</td></tr>';
						           		}else{
						           			slotHtml += '<tr><td class="label"></td><td>'+ slot.attendee[i].sort_name; +'</td></tr>';
						           		}
						           	}
					            }
					           	slotHtml += '<tr><td class="label">Location</td><td id="viewLocation">' + slot.centre + '</td></tr>';
					           	slotHtml += '<tr><td class="label">Status</td><td id="viewStatus">' + status + '</td></tr>';
					           	slotHtml += '<tr><td class="label">Description</td><td id="viewDesc">' + slot.description + '</td></tr>';
					           	slotHtml += '</table>';
					           	slotHtml += '<input type="hidden" id="dummyRoomNo" value="'+  slot.room_no +'"/>';
					           	slotHtml += '<input type="hidden" id="dummyRoomId" value="'+  slot.room_id +'"/>';
					           	slotHtml += '<input type="hidden" id="dummyStatus" value="'+  slot.status +'"/>';
					           	if(slot.status == 1){
					           		slotHtml += '<div><button id="delButton" type="button" class="deleteButton"><span class="">Delete slot</span></button></div>';
					            }
					            if(slot.activity_type == 0){
					            	cj(".ui-dialog-buttonpane button:contains('Edit a slot')").button("disable");
					        	}

					           	cj('#activity-content').html(slotHtml);
					         	
					          }
					  }); 
   				},
			    buttons:{
			    	'Edit a slot': function() {
			    		cj('#slotDetailDialog').dialog('close');
			    		cj( "#slotDialog" ).dialog({	
			    			title: 'Edit a slot',			
						    autoOpen: false,
						    resizable: false,
						    draggable: false,
			   			  width:450,
						    height:600,
						    modal: true,
						    open: function(event, ui) { 
						    	cj('select[name="startSelect"] option').each(function () {
						    		if(cj('#viewStartTime').text() == cj(this).text()){
										 cj('#startSelect option[value=' + cj(this).val()+ ']').attr('selected', 'selected');
										 return;
									}
		             			});
		             			cj('select[name="endSelect"] option').each(function () {
								    if(cj('#viewEndTime').text() == cj(this).text()){
										cj('#endSelect option[value=' + cj(this).val()+ ']').attr('selected', 'selected');
										return;
									}
		             			});
		             			cj('select[name="activitySelect"] option').each(function () {
								    if(cj('#viewActivityType').text() == cj(this).text()){
										cj('#activitySelect option[value=' + cj(this).val()+ ']').attr('selected', 'selected');
										return;
									}
		             			});
		             			cj('select[name="sessionSelect"] option').each(function () {
								    if(cj('#viewSessionService').text() == cj(this).text()){
										cj('#sessionSelect option[value="' + cj(this).val()+ '"]').attr('selected', 'selected');
										return;
									}
		             			});
		             			cj('select[name="counsellor"] option').each(function () {
								    if(cj('#viewCounsellor1').text() == cj(this).text()){
										cj('#counsellor option[value=' + cj(this).val()+ ']').attr('selected', 'selected');
										return;
									}
		             			});
		             			cj('select[name="counsellor2"] option').each(function () {
								    if(cj('#viewCounsellor2').text() == cj(this).text()){
										cj('#counsellor2 option[value="' + cj(this).val()+ '"]').attr('selected', 'selected');
										return;
									}
		             			});

             			if(jQuery.trim(cj('#dummyStatus').val()) == 2){
             				 cj('#startSelect').attr('disabled', 'disabled');
             				 cj('#endSelect').attr('disabled', 'disabled');
             				 cj('#activitySelect').attr('disabled', 'disabled');
             				 cj('#sessionSelect').attr('disabled', 'disabled');
             				 cj('#counsellor').attr('disabled', 'disabled');
             				 cj('#counsellor2').attr('disabled', 'disabled');
             				 cj('#creatError').html('The selected slot is linked to an activity. <br/>The description can only be updated.');
             			}

						    	//var slot = cj('#viewSlotId').text();
						      cj('#description').val(cj('#viewDesc').text());
						      cj('#dateHolder').text(cj('#viewSlotDate').text());
	        				  cj('#roomNo').text(cj('#dummyRoomNo').val());
	        				//cj('#hiddenRoomId').text(cj('#dummyRoomId').val())
								},
						    buttons: {
						    	'Save a slot': function() {
						    			if(cj("#dialogForm").valid()){	
						    			var contactId = cj('select[name="counsellor"]').val();    // get the value from a dropdown select
				 					    var contactId2 = cj('select[name="counsellor2"]').val(); 
							    		var startTime = cj('select[name="startSelect"]').val(); 
							    		var endTime = cj('select[name="endSelect"]').val(); 
							    		var sessionService = cj('select[name="sessionSelect"]').val(); 
							    		var activityType = cj('select[name="activitySelect"]').val(); 
							    		var description = cj('#description').val();	
							    		var roomId = cj('#dummyRoomId').val();
							    		var date =  cj('#viewSlotDate').text(); 			
							    		var slotId = jQuery.trim(cj('#viewSlotId').text());	//**********************24/07

						    	    cj().crmAPI ('slot','update',{'version' :'3', 
						    	    							  'sequential' :'1',
						    	    							  'slot_id' : slotId,
						    	    							  'contact_id' : contactId, 
			  			    	    							  'contact_id_2' : contactId2, 
						    	    							  'date' : date, 
						    	    							  'start_time' : startTime, 
						    	    							  'end_time' : endTime, 
						    	    							  'session_service' :sessionService, 
						    	    							  'room_id' : roomId, 
						    	    							  'activity_type' : activityType,
						    	    							  'description' : description},{
						    	    	cache:false,
							          ajaxURL: crmajaxURL,
												success:function (data){ 
							           	if(data.values[0].is_updated == 1){
					     							window.location.reload(true);
							           }else {
							            	var errorMessage = data.values[0].error_message;
							           		 cj('#creatError').html('' + errorMessage.toString());
							            } 
							          }
							        });
									}
						    	
			        		},
						    	Cancel: function() {
						    		validator.resetForm();
										cj("#dialogForm")[0].reset();
										//Force to clear 
										cj('#counsellor2 option[value=""]').attr('selected', 'selected');
					      		cj(this).dialog('destroy');
									}			        
								}
							});
							cj( "#slotDialog" ).dialog('open'); 
	        	},
			    	Close: function() {
			        cj(this).dialog('close');
			    	}	
			    }
			        
		});
		
		cj( "#slotDialog" ).dialog({ autoOpen: false });

	});

	function toggleFieldset(obj){
		cj('#filterTable').toggle();
	}




</script>
{/literal}
