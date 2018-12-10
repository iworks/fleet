/*! Fleet Manager - v1.3.0
 * https://iworks.pl/
 * Copyright (c) 2018; * Licensed GPLv2+
 */
var el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType;

registerBlockType( 'fleet/result', {
    title: fleet_result.result.title,

    icon: 'editor-ol',

    category: 'widgets',

    edit: function() {
        return el( 'p', { style: blockStyle }, 'Hello editor.' );
    },

    save: function() {
        return el( 'p', { style: blockStyle }, 'Hello saved content.' );
    },
} );
