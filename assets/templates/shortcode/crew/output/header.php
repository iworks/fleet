aaa<div class="<?php echo esc_attr( implode( ' ', $args['classes'] ) ); ?>">
<?php
if (
	isset( $args['header'] )
	&& ! empty( $args['header'] )
	&& isset( $args['title'] )
	&& 'show' === $args['title']
) {
	printf(
		'<%1$s class="iworks-fleet-ranking-title">%2$s</%1$s>',
		$args['header'],
		$args['post']['post_title']
	);
}
if (
	isset( $args['content'] )
	&& 'show' === $args['content']
	&& $args['post']['post_content']
) {
	printf(
		'<%1$s class="iworks-fleet-ranking-content">%2$s</%1$s>',
		'div',
		apply_filters( 'the_content', $args['post']['post_content'] )
	);
}
?>
