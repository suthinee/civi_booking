{* *}
<input id="addRoom" type="button" value="Add Room"/>
<table id="rooms">
    <thead>
        <tr>
            <th>Room No</th>
            <th>Room Type</th>
            <th>Room Size</th>
            <th>Floor</th>
            <th>Building</th>
            <th>Phone extension</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    	{foreach from=$rooms key=key item=room}
        <tr>
  			<td>{$room.room_no}</td>
            <td>{$room.type}</td>
            <td>{$room.size}</td>
            <td>{$room.floor}</td>
            <td>{$room.building}</td>
            <td>{$room.phone_extension_no}</td>
            <td><a href="{php} print base_path(); {/php}civicrm/booking/room/edit/?roomId={$room.id}&reset=1&action=update">Edit</a></td>
            <td><a href="{php} print base_path(); {/php}civicrm/booking/room/delete/?roomId={$room.id}&reset=1&action=delete">Delete</a></td>
        </tr>
        {/foreach}	
    </tbody>
</table>
{literal}
<script type="text/javascript">
    var createRoomURL = '{/literal}{php} print base_path(); {/php}{literal}civicrm/booking/room/add/?reset=1&action=add';
	var crmajaxURL = '{/literal}{php} print base_path(); {/php}{literal}civicrm/ajax/rest';

	cj(document).ready(function() {
    	cj('#rooms').dataTable({
    		"bPaginate": false,
	        "bLengthChange": false,
	        "bFilter": true,
	        "bSort": true,
	        "bInfo": false,
	        "bAutoWidth": false
    	});

        cj('#addRoom').click(function(){
            window.location.href = createRoomURL;
        });
	
	});

</script>
{/literal}
