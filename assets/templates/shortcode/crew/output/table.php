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
			<th rowspan="2"><?php esc_html_e( 'Sailed', 'fleet' ); ?></th>
			<th colspan="2"><?php esc_html_e( 'Points', 'fleet' ); ?></th>
		</tr>
		<tr>
<?php foreach ( $args['data']['events'] as $event_id ) { ?>
			<th class="small"><?php esc_html_e( 'Place', 'fleet' ); ?></th>
			<th class="small"><?php esc_html_e( 'Points', 'fleet' ); ?></th>
<?php } ?>
			<th rowspan="2"><?php esc_html_e( 'Total', 'fleet' ); ?></th>
			<th rowspan="2"><?php esc_html_e( 'Netto', 'fleet' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
$i                     = 1;
$last_points           = 0;
$last_place            = $i;
$last_number_of_starts = 0;
foreach ( $args['data']['teams'] as $one ) {
	echo '<tr>';
	echo '<td class="iworks-fleet-ranking-table-place">';
	if ( $last_points === $one['sum'] ) {
		if ( $last_number_of_starts < $one['number_of_starts'] ) {
			$last_place = $i + 1;
			echo $i + 1;
		} else {
			if ( $last_points === $one['sum'] ) {
				if ( $last_number_of_starts > $one['number_of_starts'] ) {
					$last_place++;
				}
				echo $last_place;
			} else {
				echo $i;
			}
		}
	} elseif ( $last_points < $one['sum'] ) {
		$last_place = $i;
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
	foreach ( $one['results'] as $regatta_id => $regatta_data) {
		$classes = array(
			'iworks-fleet-ranking-table-points',
			sprintf( 'iworks-fleet-ranking-table-points-%d', strtolower( $regatta_data['points'] ) ),
			sprintf( 'iworks-fleet-ranking-table-points-%s', strtolower( $regatta_data['status'] ) ),
			sprintf( 'iworks-fleet-ranking-table-points-discard-%s', strtolower( $regatta_data['discarded'] ) ),
		);
		if ( 'started'  === $regatta_data['status'] ) {
			printf(
				'<td class="%s">%d</td>',
				esc_attr( implode( ' ', $classes ) ),
				$regatta_data['points']
			);
			printf(
				'<td class="%s">%d</td>',
				esc_attr( implode( ' ', $classes ) ),
				$regatta_data['points']
			);
		} else {
			printf(
				'<td class="%s"><span>%s</span></td>',
				esc_attr( implode( ' ', $classes ) ),
				esc_html( $regatta_data['status'] )
			);
			printf(
				'<td class="%s"><span>%d</span></td>',
				esc_attr( implode( ' ', $classes ) ),
				$regatta_data['points']
			);
		}
	}
	echo '<td class="iworks-fleet-ranking-table-number-of-starts">';
	echo $one['number_of_starts'];
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-brutto">';
	echo $one['sum'];
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-netto">';
	echo $one['netto'];
	echo '</td>';
	echo '</tr>';
	/**
	 * update
	 */
	$last_number_of_starts = $one['number_of_starts'];
	$last_points           = $one['sum'];
	/**
	 * increment
	 */
	$i++;
}
?>
	</tbody>
</table>
