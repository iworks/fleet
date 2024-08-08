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
	echo $one['place'];
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-name">';
	if ( isset( $one['url'] ) ) {
		printf( '<a href="%s">%s</a>', $one['url'], esc_html( $one['name'] ) );
	} else {
		echo $one['name'];
	}
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-years">';
	echo $one['years'];
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-number-of-gold">';
	echo $one['medals']['gold'];
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-number-of-silver">';
	echo $one['medals']['silver'];
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-number-of-bronze">';
	echo $one['medals']['bronze'];
	echo '</td>';
	echo '<td class="iworks-fleet-ranking-table-number-of-starts">';
	echo $one['number_of_starts'];
	echo '</td>';
	echo '</tr>';
}
?>
	</tbody>
</table>
