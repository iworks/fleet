<table class="iworks-fleet-ranking-table">
	<thead class="iworks-fleet-ranking-table-thead">
		<tr>
			<th rowspan="2">#</th>
			<th rowspan="2"><?php esc_html_e( 'Name', 'fleet' ); ?></th>
<?php
foreach ( $args['data']['events'] as $event_id ) {
	printf(
		'<th colspan="2" class="iworks-fleet-ranking-table-thead-event"><span><a href="%s">%s</a></span></th>',
		get_permalink( $event_id ),
		get_the_title( $event_id )
	);
}
?>
			<th rowspan="2"><?php esc_html_e( 'Total', 'fleet' ); ?></th>
		</tr>
		<tr>
<?php foreach ( $args['data']['events'] as $event_id ) { ?>
			<th class="small"><?php esc_html_e( 'Place', 'fleet' ); ?></th>
			<th class="small"><?php esc_html_e( 'Points', 'fleet' ); ?></th>
<?php } ?>
		</tr>
	</thead>
	<tbody>
<?php
$i           = 1;
$last_points = 0;
$last_place  = $i;
foreach ( $args['data']['teams'] as $one ) {
	echo '<tr>';
	echo '<td class="iworks-fleet-ranking-table-place">';
	if ( $last_points < $one['sum'] ) {
		$last_points = $one['sum'];
		$last_place  = $i;
		echo $i;
	} else {
		echo $last_place;
	}
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-name">';
	if ( isset( $one['url'] ) ) {
		printf( '<a href="%s">%s</a>', $one['url'], esc_html( $one['name'] ) );
	} else {
		echo $one['name'];
	}
	echo '</td>';
	foreach ( $one['results'] as $regatta_id => $points ) {
		$classes = array(
			'iworks-fleet-ranking-table-points',
			sprintf( 'iworks-fleet-ranking-table-points-%s', strtolower( $points ) ),
		);
		printf(
			'<td class="%s">%s</td>',
			esc_attr( implode( ' ', $classes ) ),
			esc_html( $points )
		);
		printf(
			'<td class="%s">%d</td>',
			esc_attr( implode( ' ', $classes ) ),
			'DNS' === $points ? $args['data']['max'] : $points
		);
	}
	echo '<td class="iworks-fleet-ranking-table-total">';
	echo $one['sum'];
	echo '</td>';
	echo '</tr>';
	$i++;
}
?>
	</tbody>
</table>
