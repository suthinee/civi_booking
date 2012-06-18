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
	<legend onclick="toggleFieldset(this);">Fillters</legend>	
	<tabl id="filterTable">
		<tr>
			<td>
				<label for="roomFilter">Room: </label>
				<select id="roomFilter" name="roomFilter">
				    <option value="">-- select room --</option>
				 	{foreach from=$rooms key=k item=r}
						<option value="{$r.room_id}">{$r.room_no}</option>
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
			<td class="reslabel">{$time}</td>
			{/foreach}				
		</tr>
			{foreach from=$day.rooms key=roomKey item=room}
				<tr class="slots">
					<td class="resourcename {$room.room_id}">{$room.room_no}</td>
					{foreach from=$room.tdVals key=key item=value}
		        	<td id="{$value.tdataId}" colspan="1" class="slot {$value.className}">
			        	<div style="display:none">
			        	<span class='time'>{$value.timeKey}</span>
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
	cj(window).load(function(){

		cj("#roomFilter").change(function() {
			alert('//TODO: Implement filter');
		});


		cj("td.slot").live('click', function(){
			if(cj(this).hasClass('reservable')){
	        	startTime = cj(this).find('span.time').text();
	        	date = cj(this).find('span.date').text();
	        	unixDate = cj(this).find('span.unixDate').text();
	        	roomNo = cj(this).find('span.roomNo').text();
				
	        	cj('#dateHolder').text(date);
	        	cj('#roomNo').text(roomNo);
				cj('#startSelect option[value=' +startTime+ ']').attr('selected', 'selected');

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
		
		cj( "#slotDialog" ).dialog({
			    autoOpen: false,
			    resizable: false,
			    draggable: false,
   			    width:800,
			    height:600,
			    modal: true,
			    buttons: {
			    	'Create a slot': function() {
			    		var contactId = cj('select[name="counsellor"]').val();    // get the value from a dropdown select
			    		var startTime = cj('select[name="startSelect"]').val(); 
			    		var endTime = cj('select[name="endSelect"]').val(); 
			    		var sessionService = cj('select[name="sessionService"]').val(); 
			    		var activityType = cj('select[name="activityType"]').val(); 
 		
			    	    cj().crmAPI ('slot','create',{'version' :'3', 
			    	    							  'sequential' :'1',
			    	    							  'contact_id' : contactId, 
			    	    							  'date' : unixDate, 
			    	    							  'start_time' : startTime, 
			    	    							  'end_time' : endTime, 
			    	    							  'session_service' :sessionService, 
			    	    							  'room_no' : roomNo, 
			    	    							  'activity_type' : activityType},{
				           ajaxURL: crmajaxURL,
				           success:function (data){ 
				            if(data.count != 0){
				              cj.each(data.values, function(key, value) {
				           		
					    		var slotId = value.slot_id;
						        var selectedStartTime = value.start_time;
						        var selectedEndTime = value.end_time;
						        var selectedDate = value.slot_date;
						        var selectedRoom = value.room_no;
	        
						        var timeRange = value.time_range;

						        console.log(slotId);
   						        console.log(selectedStartTime);
						        console.log(selectedEndTime);
						        console.log(selectedDate);
						        console.log(selectedRoom);

						        
						        for(time in timeRange){
						        	var elementId = '#' + selectedDate + selectedRoom + timeRange[time];
						        	console.log(elementId);
						        	cj(elementId).removeClass("reservable").addClass("reserved");
		    				    }
    				            cj("#slotDialog").dialog('close');
    				            return;
				              });
				            }  
				          }
				        });
        			},
			        Cancel: function() {
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
	<form>
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
							<option value="{$k}">{$time}</option>
						{/foreach}					
					</select>
				</li>
				<li>
					<label for="endSelect">End Time: </label>
					<select id="endSelect" name="endSelect">
						<option value="">Select End time</option>
						{foreach from=$timeOptions key=k item=time}
							<option value="{$k}">{$time}</option>
						{/foreach}					
					</select>
				</li>
				<li>
					<label for="activityType">Activity type: </label>
					<select id="activitySelect" name="activityType">
						{foreach from=$activityTypes key=k item=activityType}
							<option value="{$k}">{$activityType}</option>
						{/foreach}		
					</select>
				</li>
				<li>
					<label for="sessionService">Session service: </label>
					<select id="sessionSelect" name="sessionService">
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
					<label for="counsellor">Counsellor 2: </label>
					<select id="counsellor" name="counsellor">
						<option value="">Select Counsellor</option>
						{foreach from=$contacts key=k item=contact}
							<option value="{$k}">{$contact}</option>
						{/foreach}		
					</select>
				</li>	
				<li>
					<label for="decription">Description</label>
					<textarea rows="3" cols="30" name="description">

					</textarea>
				</li>				
			</ul>
		</form>
</div>
