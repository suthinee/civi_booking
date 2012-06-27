{* this template is used for displaying slot calendar *}
<div id="content">
<div>
	<div class="schedule_title">

	</div>
	<div class="schedule_dates">
		<a href="{$lastWeekUrl}" ><span class="arrow-left">&nbsp;&nbsp;&nbsp;</span></a>
		 {$startDate} - {$endDate}<a href="{$nextWeekUrl}"><span class="arrow-right">&nbsp;&nbsp;&nbsp;</span></a>
	</div>
</div>

<div style="text-align: center; margin: auto;">
<div class="legend reservable">Reservable</div>
<!--<div class="legend unreservable">Unreservable</div>-->
<div class="legend reserved">Reserved</div>
<!-- <div class="legend reserved mine">My Reservation</div>-->
<!-- <div class="legend reserved pending">Pending</div> -->
<div class="legend pasttime">Past</div>
<!-- <div class="legend restricted">Restricted</div> -->
</div>

<div style="height:10px">&nbsp;</div>
<form id="filterForm"> 
	<fieldset>
	<legend onclick="toggleFieldset(this);">Filters</legend>	
	<tabl id="filterTable">
		<tr>
			<td>
				<label for="dateFilter">Date: </label>
				<select id="dateFilter" name="dateFilter">
				    <option value="all">-- All dates --</option>
					{foreach from=$slots key=k item=day}
						<option value="{$k}">{$day.date}</option>
					{/foreach}	
				</select>
			</td>
			<td>
				<label for="roomFilter">Room: </label>
				<select id="roomFilter" name="roomFilter">
				    <option value="all">-- All rooms --</option>
				 	{foreach from=$rooms key=k item=r}
						<option value="{$r.room_no}">{$r.room_no}</option>
					{/foreach}	
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
				<td class="reslabel">{$time.time}</td>
				{else}
				<td class="reslabel"></td>
				{/if}
			{/foreach}				
		</tr>
			{foreach from=$day.rooms key=roomKey item=room}
				<tr class="slots">
					<td class="resourcename {$room.room_id}">{$room.room_no}</td>
					{foreach from=$room.tdVals key=key item=value}
		        	<td id="{$value.tdataId}" colspan="1" class="slot {$value.className}" title="{$value.title}">
			        	<div style="display:none">
			        	<span class='time'>{$value.timeKey}</span>
			        	<span class='defaultEndtime'>{$value.defaultEndTime}</span>
						<span class='roomNo'>{$room.room_no}</span>
						<span class='roomId'>{$roomKey}</span>
						<span class='date'>{$day.date}</span>
						<span class='unixDate'>{$dayKey}</span>
						</div>
					</td>	
					{/foreach}								
	  			</tr>
			{/foreach}
						
	</tbody>
</table>
{/foreach}
</div>
{literal}
<script type="text/javascript">
	var crmajaxURL = '{/literal}{php} print base_path(); {/php}{literal}civicrm/ajax/rest';
  	var startTime = null;
    var unixDate = null;
    var roomNo = null;

    //set noConflict for tooltips to work
    var jq = jQuery.noConflict();

	cj(window).load(function(){

		cj("#roomFilter").change(function(event) {
			var room = cj('select[name="roomFilter"]').val(); 
			console.log(room);
    	    cj('tbody tr td.resourcename').each(function() { 
    	    	if(room != cj(this).text()){
    	    		cj(this).parent().hide();    	    	
    	    	}else{
    	    		cj(this).parent().show();
    	    	}

    	    	//TODO: change this method to an appropriate way as this is expensive.
    	    	if(room.localeCompare('all') == 0){
    	    		cj(this).parent().show();
    	    	}

   			 //($(this).text().search(new RegExp(query, "i")) < 0) ? $(this).hide().removeClass('visible') : $(this).show().addClass('visible');  
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
    	    	//TODO: change this method to an appropriate way as this is expensive.
    	    	if(date.localeCompare('all') == 0){
    	    		cj(this).show();
    	    	}    	    	

  			});
		});


		cj("td.slot").live('click', function(){
			if(cj(this).hasClass('reservable')){
	        	startTime = cj(this).find('span.time').text();
	        	date = cj(this).find('span.date').text();
	        	unixDate = cj(this).find('span.unixDate').text();
	        	roomNo = cj(this).find('span.roomNo').text();
	        	defaultEndtime = cj(this).find('span.defaultEndtime').text();

	        	cj('#dateHolder').text(date);
	        	cj('#roomNo').text(roomNo);
				cj('#startSelect option[value=' +startTime+ ']').attr('selected', 'selected');
				cj('#endSelect option[value=' +defaultEndtime+ ']').attr('selected', 'selected');

	        	cj( "#slotDialog" ).data('obj', cj(this))
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

        var validator = cj("#dialogForm").validate({
       	  rules: {
		    startSelect: "required",
		    endSelect:  {
		    	"greaterThan" : true,
		    	"required" : true
		    },
		    activitySelect: "required",
		    counsellor: "required",
		    sessionSelect: "required",
		    counsellor2: "notEqaulTo"		  
		  }
		});
		
		cj( "#slotDialog" ).dialog({				
			    autoOpen: false,
			    resizable: false,
			    draggable: false,
   			    width:450,
			    height:600,
			    modal: true,
			    buttons: {
			    	'Create a slot': function() {
			    		var contactId = cj('select[name="counsellor"]').val();    // get the value from a dropdown select
 					    var contactId2 = cj('select[name="counsellor2"]').val(); 
			    		var startTime = cj('select[name="startSelect"]').val(); 
			    		var endTime = cj('select[name="endSelect"]').val(); 
			    		var sessionService = cj('select[name="sessionSelect"]').val(); 
			    		var activityType = cj('select[name="activitySelect"]').val(); 
			    		var description = cj('#description').val();
			    		
			    		if(cj("#dialogForm").valid()){			    			
				    	    
				    	    cj().crmAPI ('slot','create',{'version' :'3', 
				    	    							  'sequential' :'1',
				    	    							  'contact_id' : contactId, 
	  			    	    							  'contact_id_2' : contactId, 
				    	    							  'date' : unixDate, 
				    	    							  'start_time' : startTime, 
				    	    							  'end_time' : endTime, 
				    	    							  'session_service' :sessionService, 
				    	    							  'room_no' : roomNo, 
				    	    							  'activity_type' : activityType,
				    	    							  'description' : description},{
					           ajaxURL: crmajaxURL,
					           success:function (data){ 
					           	if(data.values[0].is_created == 1){
					           		window.location.reload(true);
					           		/*
						            if(data.count != 0){
						             
											
						              cj.each(data.values, function(key, value) {
						           		
							    		var slotId = value.slot_id;
								        var selectedStartTime = value.start_time;
								        var selectedEndTime = value.end_time;
								        var selectedDate = value.slot_date;
								        var selectedRoom = value.room_no;
			        
								        var timeRange = value.time_range;
								      							        
								        for(time in timeRange){
								        	var elementId = '#' + selectedDate + selectedRoom + timeRange[time];
								        	console.log(elementId);
								        	cj(elementId).removeClass("reservable").addClass("reserved");
				    				    }
		    				            cj("#slotDialog").dialog('close');
		    				            return;
						              });
									  
						            } */
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
			        cj(this).dialog('close');
			    }			        
			}});

		});

	function toggleFieldset(obj){
		cj('#filterTable').toggle();
	}

</script>
{/literal}

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
					<span id="roomId"class="hide"></span> 
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
				<li>
					<label for="decription">Description</label>
					<textarea rows="4" cols="50" id="description" name="description" style="resize: none;" class="required" ></textarea>
				</li>
			</ul>
		</form>
</div>
