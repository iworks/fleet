<?php
/**
 * Notice displayed for admin.
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

?>
<div class="notice notice-error">
	<h2><?php esc_html_e( 'Fleet: the ranking id is wrong!', 'fleet' ); ?></h2>
<?php
$content = __( 'The shortcode param <code>id</code> param is wrong.', 'fleet' );
echo wpautop( wp_kses_post( $content ) );
?>
</div>
