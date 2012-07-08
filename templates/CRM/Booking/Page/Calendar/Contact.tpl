<script type="text/javascript" src="{$config->resourceBase}packages/jquery/jquery.min.js"></script>
<script type="text/javascript" src="{$basePath}sites/all/modules/civi_booking/js/fullcalendar.js"></script>

<style type="text/css" media="print">@import url("{$basePath}sites/all/modules/civi_booking/css/fullcalendar.print.css");</style>

<style type="text/css">@import url("{$basePath}sites/all/modules/civi_booking/css/fullcalendar.css");</style>
<style type="text/css">@import url("{$basePath}sites/all/modules/civi_booking/css/booking.css");</style>


<div style="text-align: center; margin: auto;">
<div class="contact-reserved">Reserved</div>
<div class="contact-appointment">Appointment</div>
<div class="contact-unavailable">Unavailable</div>
</div>
<div id='calendar'></div>
{literal}
<script type="text/javascript">var cj = jQuery.noConflict(); $ = cj;</script> 

<script type="text/javascript">
  var crmajaxURL = '{/literal}{php} print base_path(); {/php}{literal}civicrm/ajax/rest';
 
(function ($) {
  $(document).ready(function () { 

    jQuery('#calendar').fullCalendar({
      header: {
        left: 'prev,next, today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      defaultView: 'agendaWeek',
      firstDay: 1,
      allDaySlot: false,
      firstHour: 8,
      minTime: '8:00',
      maxTime: '21:00',
      slotMinutes: 15,
      events: function(start, end, callback) {
        $.ajax({
            cache: false,
            url: crmajaxURL,
            dataType: 'json',
            data: {
                // our hypothetical feed requires UNIX timestamps
                start: Math.round(start.getTime() / 1000),
                end: Math.round(end.getTime() / 1000),
                cid: {/literal}{$contactId}{literal},
                entity: 'slot',
                action: 'get_by_contact',
                json: 1,
                sequential: 1
            },
            success: function(data) {
              var events = new Array();
              for(index in data.results){    
                e = data.results[index];
                console.log(event);
                events.push({
                      title: e.title,
                      start: e.start,
                      end: e.end,
                      allDay: false,
                      color: e.color
                });      
              }
              callback(events);
            }
        });
      } 
      
    }); 
  }); //end ready function
    
})(jQuery);


</script>
{/literal}
