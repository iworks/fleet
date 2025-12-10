<?php
/**
 * Notice displayed for admin.
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

echo '<div class="notice notice-error">';
echo '<h2>';
esc_html_e( 'Fleet: the ranking id is required!', 'fleet' );
echo '</h2>';
echo '<p>';
printf(
	// translators: %s is: <code>id</code>
	esc_html__( 'The %s parameter is required for this shortcode to work.', 'fleet' ),
	'<code>id</code>'
);
echo '</p>';
echo '</div>';
