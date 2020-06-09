/*! Fleet Manager - v1.2.8
 * https://iworks.pl/
 * Copyright (c) 2020; * Licensed GPLv2+
 */
jQuery( document ).ready(function($) {
    $( function() {
        $( ".iworks-fleet-row .datepicker" ).each( function() {
            var format = $(this).data('date-format') || 'yy-mm-dd';
            $(this).datepicker({ dateFormat: format });
        });
    } );
});

jQuery( document ).ready(function($) {
    var iworks_fleet_people_list = [];
    var data = {
        action: 'iworks_fleet_persons_list',
        _wpnonce: iworks_fleet.nonces.iworks_fleet_persons_list_nonce,
        user_id: iworks_fleet.user_id
    };
    $.post(ajaxurl, data, function(response) {
        if ( response.success ) {
            iworks_fleet_people_list = response.data;
            $('select', $('#iworks-crews-list') ).select2({
                data: iworks_fleet_people_list
            });
        }
    });
    $( function() {
        $('.iworks-add-crew').on( 'click', function() {
            var $el = $('#iworks-crews-list');
            var id = Date.now();
            var template = wp.template( 'iworks-boat-crew' );
            $el.append( template( {
                id: id
            } ) ).ready( function() {
                var parent = $('#iworks-crew-'+id);
                $('select', parent).select2({
                    data: iworks_fleet_people_list
                });
            });
            return false;
        });
    } );
});

jQuery( document ).ready(function($) {
});

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

jQuery( document ).ready(function($) {
    $('select.iworks-select2').select2();
});
