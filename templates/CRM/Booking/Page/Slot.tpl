{* this template is used for displaying slot calendar *}
<div id="content">
<div>
	<div class="schedule_title">
	</div>
	<div class="schedule_dates">
		{$startDate} - {$endDate}
	</div>
</div>

<div style="text-align: center; margin: auto;">
<div class="legend reservable">Reservable</div>
<div class="legend unreservable">Unreservable</div>
<div class="legend reserved">Reserved</div>
<!-- <div class="legend reserved mine">My Reservation</div>-->
<!-- <div class="legend reserved pending">Pending</div> -->
<div class="legend pasttime">Past</div>
<div class="legend restricted">Restricted</div>
</div>

<div style="height:10px">&nbsp;</div>


{foreach from=$daysOfNextweek item=day}
<table class="reservations" border="1" cellpadding="0" width="100%">
			<tbody><tr>
			<td class="resdate">{$day}</td>
				{$timeRange}
				{foreach from=$timeOptions item=time}
					<td class="reslabel">{$time}</td>
				{/foreach}				
			</tr>
			{foreach from=$rooms item=room}
				{foreach from=$room key=k item=v}
					{if $k eq 'room_no'}
						<tr class="slots">
							<td class="resourcename">{$v}</td>
							{foreach from=$timeOptions key=k item=time}
					        	<td colspan="1" class="reservable slot">
					        		<div class='time hide'>{$k}</div>
							       	<div class='roomNo hide'>{$v}</div>
							       	<div class='roomId hide'></div>
							       	<div class='date hide'>{$day}</div>
					        	</td>
							{/foreach}								
			   			</tr>
					{/if}  
				{/foreach}
			{/foreach}
						
	</tbody>
</table>
{/foreach}
</div>
{literal}

<script type="text/javascript">
	cj(function() {
		cj("td.slot").live('click', function(){
        	var startTime = cj(this).find('div.starTime').text();
        	var date = cj(this).find('div.date').text();
        	var room = cj(this).find('div.roomNo').text();
        	var roomId = cj(this).find('div.roomId').text();

        	cj('#dateHolder').text(date);
        	cj('#roomNo').text(room);
			
			cj('#startSelect option[value=' +startTime+ ']').attr('selected', 'selected');
						cj('#startSelect option[value=' +startTime+ ']').attr('selected', 'selected');


			console.log( cj(this).next().text() );




        	cj( "#slotDialog" ).data('room', room)
            .dialog('open');   
		}).hover(function(){
   			cj(this).css("background","#40d288");
    	},function(){
		    cj(this).css("background","#ffffff");
		});
		
		cj( "#slotDialog" ).dialog({
			    autoOpen: false,
			    resizable: false,
			    draggable: false,
			    height:300,
   			    width:300,
			    modal: true,
			    buttons: {
			    	'Create a slot': function() {
            			alert('slot has been created');
        			},
			        Cancel: function() {
			            cj(this).dialog('close');
			        }			        
			    }});

		});
</script>

{/literal}

<div id="slotDialog" title="Create a slot" class="ui-dialog-content ui-widget-content">
	<form>
			<input type="hidden" value="">
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
					<label for="startSelect">Start Time: </label>
					<select id="startSelect" name="startSelect">
						<option value="">Select Start time</option>
						{foreach from=$timeOptions key=k item=time}
							<option value="{$k}">{$time}</option>
						{/foreach}					
					</select>
				</li>
				<!--
				<li>
					<label for="end">End Time: </label>
					<select name="end">
						<option value="">Select End Time</option>
						{foreach from=$timeOptions key=k item=time}
							<option value="{$k}">{$time}</option>
						{/foreach}
					</select>
				</li>
				-->
				<li>
					<label for="sessionType">Session type: </label>
					<select name="sessionType">
						{foreach from=$sessionType key=k item=session}
							<option value="{$k}">{$session}</option>
						{/foreach}		
					</select>
				</li>
				<li>
					<label for="counsellor">Counsellor: </label>
					<select id="counsellor" name="counsellor">
						<option value="">Select Counsellor</option>
						{foreach from=$contacts key=k item=contact}
							<option value="{$k}">{$contact}</option>
						{/foreach}		
					</select>
				</li>				
			</ul>
		</form>
</div>
