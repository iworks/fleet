<table class="iworks-fleet-ranking-table">
	<thead class="iworks-fleet-ranking-table-thead">
		<tr>
			<th>#</th>
			<th><?php esc_html_e( 'Name', 'fleet' ); ?></th>
			<th><?php esc_html_e( 'Years', 'fleet' ); ?></th>
			<th><?php esc_html_e( 'Gold', 'fleet' ); ?></th>
			<th><?php esc_html_e( 'Silver', 'fleet' ); ?></th>
			<th><?php esc_html_e( 'Bronze', 'fleet' ); ?></th>
			<th><?php esc_html_e( 'Total', 'fleet' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ( $args['data']['teams'] as $one ) {
	echo '<tr>';
	echo '<td class="iworks-fleet-ranking-table-place">';
	echo wp_kses_post( $one['place'] );
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-name">';
	if ( isset( $one['url'] ) ) {
		printf(
			'<a href="%s">%s</a>',
			esc_url( $one['url'] ),
			wp_kses_post( $one['name'] )
		);
	} else {
		echo wp_kses_post( $one['name'] );
	}
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-years">';
	echo wp_kses_post( $one['years'] );
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-number-of-gold">';
	echo wp_kses_post( $one['medals']['gold'] );
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-number-of-silver">';
	echo wp_kses_post( $one['medals']['silver'] );
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-number-of-bronze">';
	echo wp_kses_post( $one['medals']['bronze'] );
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-number-of-starts">';
	echo wp_kses_post( $one['number_of_starts'] );
	echo '</td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';
