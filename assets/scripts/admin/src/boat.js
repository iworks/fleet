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
            $( 'select', $( '.iworks-owners-list-container tbody' ) ).each( function() {
                var list = iworks_fleet_people_list;
                $(this).select2({ data: list });
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
    /**
     * boot owner
     */
    if ( true ) {
        var $container = $( '.iworks-owners-list-container tbody' );
        var year = parseInt( $( '#iworks_fleet_boat_build_year').val() );
        var bind_datepicker = function( el ) {
            el.datepicker( {
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                yearRange: 0 < year? year + ':+0': '1955:+0',
                dateFormat:el.data('date-format') || 'yy-mm-dd',
            } );
        };
        var bind_add_person = function( el ) {
            el.on( 'click', function() {
                var $parent = $(this).closest( 'td' );
                var template = wp.template( 'iworks-fleet-boat-owner-user' );
                var id = $(this).closest( 'tr' ).data( 'id' );
                $( '.persons', $parent ).append( template( { id: id } )).ready( function() {
                    var list = iworks_fleet_people_list;
                    $('.select2.empty' ).select2( { data: list } ).removeClass( 'empty' );
                });
                return false;
            });
        };
        var bind_kind = function( el ) {
            el.on( 'change', function() {
                var $parent = $(this).closest( 'tr' );
                if ( 'organization' === el.val() ) {
                    $( 'div.person', $parent ).hide();
                    $( 'div.organization', $parent ).show();
                } else {
                    $( 'div.person', $parent ).show();
                    $( 'div.organization', $parent ).hide();
                }
            });
        };
        /**
         * bind kind
         */
        $( '.boat-kind input', $container ).each( function() {
            bind_kind( $(this) );
        });
        /**
         * bind delete
         */
        $( 'a.iworks-fleet-boat-delete', $container ).on( 'click', function() {
            $(this).closest( 'tr' ).detach();
            return false;
        });
        /**
         * bind datepicker
         */
        $( '.datepicker', $container ).each( function() {
            bind_datepicker( $(this) );
        });
        /**
         * bind add person
         */
        bind_add_person( $( 'a.add-person-selector', $container ) );
        $( '#iworks-owners-list-add' ).on( 'click', function() {
            var template = wp.template( 'iworks-fleet-boat-owner' );
            var id = 'iworks-fleet-boat-owner-' + Date.now();
            var list = iworks_fleet_people_list;
            /**
             * generate
             */
            $container.append(
                template( {
                    id: id,
                    kind: 'person',
                } )
            ).ready( function() {
                var $parent = $( 'tr[data-id="' + id  + '"]' );
                /**
                 * bind kind
                 */
                $( '.boat-kind input', $parent ).each( function() {
                    bind_kind( $(this) );
                });
                /**
                 * set kind
                 */
                $( 'input[type=radio][value=person]', $parent ).attr( 'checked', 'checked' );
                $( '.person', $parent ).show();
                template = wp.template( 'iworks-fleet-boat-owner-user' );
                $( '.persons', $parent ).append( template( { id: id } ) );
                bind_add_person( $( 'a.add-person-selector', $parent ) );
                /**
                 * bind delete
                 */
                $( 'a.iworks-fleet-boat-delete', $parent ).on( 'click', function() {
                    $(this).closest( 'tr' ).detach();
                    return false;
                });
                $( 'select', $parent ).select2({ data: list });
                $( '.datepicker', $parent ).each( function() {
                    bind_datepicker( $(this) );
                });
            });
            return false;
        });
    }
});
