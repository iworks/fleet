<table class="iworks-fleet-ranking-table">
	<thead class="iworks-fleet-ranking-table-thead">
		<tr>
			<th rowspan="2">#</th>
			<th rowspan="2"><?php esc_html_e( 'Team', 'fleet' ); ?></th>
<?php
foreach ( $args['data']['events'] as $event_id ) {
	printf(
		'<th colspan="2" class="iworks-fleet-ranking-table-thead-event"><span><a href="%s">%s</a></span></th>',
		esc_url( get_permalink( $event_id ) ),
		wp_kses_post( get_the_title( $event_id ) )
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
			echo esc_html( $i + 1 );
		} else {
			if ( $last_points === $one['sum'] ) {
				if ( $last_number_of_starts > $one['number_of_starts'] ) {
					++$last_place;
				}
				echo esc_html( $last_place );
			} else {
				echo esc_html( $i );
			}
		}
	} elseif ( $last_points < $one['sum'] ) {
		$last_place = $i;
		echo esc_html( $i );
	} else {
		echo esc_html( $last_place );
	}
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-name"><ul>';
	foreach ( $one['sailors'] as $sailor_id => $sailor ) {
		echo '<li>';
		if (
			isset( $sailor['permalink'] )
			&& $sailor['permalink']
		) {
			printf(
				'<a href="%s">%s</a>',
				esc_url( $sailor['permalink'] ),
				esc_html( $sailor['name'] )
			);
		} else {
			echo $sailor['name'];
		}
		echo '</li>';
	}
	echo '</ul></td>';
	foreach ( $one['results'] as $regatta_id => $regatta_data ) {
		$prefix = $sufix = '';
		if ( 'yes' === $regatta_data['discarded'] ) {
			$prefix = '[';
			$sufix  = ']';
		}
		$classes = array(
			'iworks-fleet-ranking-table-points',
			sprintf( 'iworks-fleet-ranking-table-points-%d', strtolower( $regatta_data['points'] ) ),
			sprintf( 'iworks-fleet-ranking-table-points-%s', strtolower( $regatta_data['status'] ) ),
			sprintf( 'iworks-fleet-ranking-table-points-discard-%s', strtolower( $regatta_data['discarded'] ) ),
		);
		if ( 'started' === $regatta_data['status'] ) {
			if ( 3 < $regatta_data['points'] ) {
				printf(
					'<td class="%s">%s%d%s</td>',
					esc_attr( implode( ' ', $classes ) ),
					esc_html( $prefix ),
					esc_html( $regatta_data['place'] ),
					esc_html( $sufix )
				);
			} else {
				printf(
					'<td class="%1$s"><span class="medal medal-%2$d">%3$s%2$d%4$s</span></td>',
					esc_attr( implode( ' ', $classes ) ),
					esc_html( $regatta_data['points'] ),
					esc_html( $prefix ),
					esc_html( $sufix )
				);
			}
			printf(
				'<td class="%s">%s%d%s</td>',
				esc_attr( implode( ' ', $classes ) ),
				esc_html( $prefix ),
				esc_html( $regatta_data['points'] ),
				esc_html( $sufix )
			);
		} else {
			printf(
				'<td class="%s">%s%s%s</td>',
				esc_attr( implode( ' ', $classes ) ),
				esc_html( $prefix ),
				esc_html( $regatta_data['status'] ),
				esc_html( $sufix )
			);
			printf(
				'<td class="%s">%s%d%s</td>',
				esc_attr( implode( ' ', $classes ) ),
				esc_html( $prefix ),
				esc_html( $regatta_data['points'] ),
				esc_html( $sufix )
			);
		}
	}
	echo '<td class="iworks-fleet-ranking-table-number-of-starts">';
	echo esc_html( $one['number_of_starts'] );
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-brutto">';
	echo esc_html( $one['sum'] );
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-netto">';
	echo esc_html( $one['netto'] );
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
	++$i;
}
echo '</tbody>';
echo '</table>';
