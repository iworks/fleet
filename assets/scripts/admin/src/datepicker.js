jQuery( document ).ready(function($) {
    $( function() {
        $( ".iworks-fleet-row .datepicker" ).each( function() {
            var format = $(this).data('date-format') || 'yy-mm-dd';
            $(this).datepicker({ dateFormat: format });
        });
    } );
});
