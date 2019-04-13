$(function() {

  $options = {
    timePicker: true,
    minDate: moment().startOf('hour'),
    locale: {
      format: 'M/DD hh:mm A'
    }
  };

  if($('#event_start').val()) {
    $options.startDate = moment.unix($('#event_start').val()*1);
  }

  if($('#event_end').val()) {
    $options.endDate = moment.unix($('#event_end').val()*1);
  }

  $('#eventdaterange').daterangepicker($options);
  $('#eventdaterange').on('apply.daterangepicker', function(ev, picker) {
    $('#event_start').val(picker.startDate.unix());
    $('#event_end').val(picker.endDate.unix());
  });
});
