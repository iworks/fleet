<?php get_header(); ?>

<main id="site-content" role="main">

	<?php

	$archive_title    = '';
	$archive_subtitle = '';

	if ( is_search() ) {
		global $wp_query;

		$archive_title = sprintf(
			'%1$s %2$s',
			'<span class="color-accent">' . __( 'Search:', 'twentytwenty' ) . '</span>',
			'&ldquo;' . get_search_query() . '&rdquo;'
		);

		if ( $wp_query->found_posts ) {
			$archive_subtitle = sprintf(
				/* translators: %s: Number of search results. */
				_n(
					'We found %s result for your search.',
					'We found %s results for your search.',
					$wp_query->found_posts,
					'twentytwenty'
				),
				number_format_i18n( $wp_query->found_posts )
			);
		} else {
			$archive_subtitle = __( 'We could not find any results for your search. You can give it another try through the search form below.', 'twentytwenty' );
		}
	} elseif ( ! is_home() ) {
		$archive_title    = get_the_archive_title();
		$archive_subtitle = get_the_archive_description();
	}

	if ( $archive_title || $archive_subtitle ) {
		?>

		<header class="archive-header has-text-align-center header-footer-group">

			<div class="archive-header-inner section-inner medium">

				<?php if ( $archive_title ) { ?>
					<h1 class="archive-title"><?php echo wp_kses_post( $archive_title ); ?></h1>
				<?php } ?>

				<?php if ( $archive_subtitle ) { ?>
					<div class="archive-subtitle section-inner thin max-percentage intro-text"><?php echo wp_kses_post( wpautop( $archive_subtitle ) ); ?></div>
				<?php } ?>

			</div><!-- .archive-header-inner -->

		</header><!-- .archive-header -->

		<?php
	}

	if ( have_posts() ) {
		?>
<article>
	<div class="post-inner thin ">
		<div class="entry-content">
			<table>
				<thead>
					<tr>
		<?php
		$is_boat   = is_post_type_archive( 'iworks_fleet_boat' );
		$is_person = is_post_type_archive( 'iworks_fleet_person' );
		$is_result = is_post_type_archive( 'iworks_fleet_result' );

		if ( $is_boat ) {
			?>
	<th><?php esc_html_e( 'Number', 'fleet' ); ?></th>
	<th><?php esc_html_e( 'Hull', 'fleet' ); ?></th>
	<th><?php esc_html_e( 'Last known owner', 'fleet' ); ?></th>
			<?php
		}

		?>
					</tr>
				</thead>
				<tbody>
		<?php
		while ( have_posts() ) {
			the_post();
			?>
<tr <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<?php
			if ( $is_boat ) {
				printf(
					'<td><a href="%s">%s</a></td>',
					esc_url( get_permalink() ),
					apply_filters( 'iworks_fleet_boat_get_flag', get_the_title(), get_the_ID() )
				);
				printf( '<td>%s</td>', apply_filters( 'iworks_fleet_boat_get_hull', '', get_the_ID() ) );
			} else {
				the_title( '<td><a href="' . esc_url( get_permalink() ) . '">', '</a></td>' );
			}
			echo '</tr>';
		}
		?>
				</tbody>
			</table>
		</div>
	</div>
</article>
		<?php
	} elseif ( is_search() ) {
		?>

		<div class="no-search-results-form section-inner thin">

			<?php
			get_search_form(
				array(
					'label' => __( 'search again', 'twentytwenty' ),
				)
			);
			?>

		</div><!-- .no-search-results -->

		<?php
	}
	?>

	<?php get_template_part( 'template-parts/pagination' ); ?>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php
get_footer();
