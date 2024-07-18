<?php
/**
 * Notice displayed for admin.
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

?>
<div class="notice notice-error">
	<h2><?php esc_html_e( 'Fleet: the ranking id is required!', 'fleet' ); ?></h2>
<?php
$content  = __( 'The shortcode require <code>id</code> param to work.', 'fleet' );
echo wpautop( wp_kses_post( $content ) );
?>
</div>
