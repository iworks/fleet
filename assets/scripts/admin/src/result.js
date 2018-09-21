jQuery( document ).ready(function($) {
    $( function() {
        $('.post-type-iworks_fleet_result #race button').on( 'click', function( event  ) {
            event.preventDefault();
            var data = new FormData();
            $.each( $('#file_fleet_races')[0].files, function(i, file) {
                data.append('file', file, file.name );
            });
            data.append( 'action', 'iworks_fleet_upload_races' );
            data.append( 'id', $('#post_ID' ).val() );
            data.append( '_wpnonce', $('#iworks_fleet_posttypes_result').val() );
            var data = {
                url: ajaxurl,
                method: 'POST',
                type: 'POST',
                cache: false,
                data: data,
                contentType: false,
                processData: false
            };
            $.ajax(data);
            return false;
        });
    } );
});
