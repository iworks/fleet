jQuery( document ).ready(function($) {
    $( function() {
        $('.post-type-iworks_fleet_result #race button').on( 'click', function( event  ) {
            event.preventDefault();
            var data = new FormData();
            var $container = $(this).closest( '.inside' );
            var is_file = false;
            $.each( $('#file_fleet_races')[0].files, function(i, file) {
                is_file = true;
                data.append( 'file', file, file.name );
            });
            if ( ! is_file ) {
                window.alert( iworks_fleet.messages.result.choose_file_first );
                return;
            }
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
            $( '.spinner', $container ).css( 'display', 'block' ).css( 'float', 'left' ).css( 'visibility', 'visible' );
            $( '#file_fleet_races, button', $container ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
            $.ajax(data)
                .success( function( response ) {
                    $( '#file_fleet_races, button', $container ).removeAttr( 'disabled' ).removeClass( 'disabled' );
                    $( '.spinner', $container ).hide();
                });
            return false;
        });
    } );
});
