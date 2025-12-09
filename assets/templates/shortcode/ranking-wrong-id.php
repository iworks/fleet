<?php
/**
 * Notice displayed for admin.
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

echo '<div class="notice notice-error">';
echo '<h2>';
esc_html_e( 'Fleet: the ranking id is wrong!', 'fleet' );
echo '</h2>';
echo '<p>';
printf(
	// translators: %1$s is: <code>id</code>
	esc_html__( 'The shortcode attribute %1$s is invalid. Please use a correct %1$s value that matches an existing item.', 'fleet' ),
	'<code>id</code>'
);
echo '</p>';
echo '</div>';
