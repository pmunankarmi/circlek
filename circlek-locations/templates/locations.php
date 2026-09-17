<?php
/**
 * Front-end locations directory.
 *
 * Available variables: $locations, $context and $settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$counts = $context['counts'];
$ui = $context['ui'];
$is_arabic = $context['is_rtl'];
$plural = static function( $count, $word = 'store' ) use ( $is_arabic ) {
	if ( $is_arabic ) {
		$count = (int) $count;
		if ( 1 === $count ) { return 'متجر'; }
		if ( 2 === $count ) { return 'متجران'; }
		if ( $count >= 3 && $count <= 10 ) { return 'متاجر'; }
		return 'متجرًا';
	}
	return 1 === (int) $count ? $word : $word . 's';
};

$strategy_items = array(
	array( 'airport.png', $ui['strategy_items'][0] ),
	array( 'gas-station.png', $ui['strategy_items'][1] ),
	array( 'streets.png', $ui['strategy_items'][2] ),
	array( 'hospital.png', $ui['strategy_items'][3] ),
	array( 'skyline.png', $ui['strategy_items'][4] ),
	array( 'architect.png', $ui['strategy_items'][5] ),
);
?>
<div class="ckl ckl--b<?php echo $context['is_rtl'] ? ' ckl--rtl' : ''; ?>" dir="<?php echo $context['is_rtl'] ? 'rtl' : 'ltr'; ?>">
	<script type="application/json" id="ckl-meta"><?php echo wp_json_encode( $context['meta'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); ?></script>

	<section class="ckb-hero">
		<img class="ckb-hero__bg" src="<?php echo esc_url( $settings['hero_image_url'] ); ?>" width="1920" height="800" alt="" fetchpriority="high" />
		<div class="ckb-hero__veil" aria-hidden="true"></div>
		<div class="ckb-hero__inner">
			<h1><?php echo esc_html( $settings['hero_title'] ); ?></h1>
			<p class="ckb-hero__sub" dir="<?php echo $is_arabic ? 'rtl' : 'ltr'; ?>">
				<b><?php echo esc_html( $counts['total'] ); ?></b> <?php echo esc_html( $plural( $counts['total'] ) ); ?>
				<?php foreach ( $context['countries'] as $country_key => $country_label ) : ?>
					<?php if ( ! empty( $counts['country'][ $country_key ] ) ) : ?>
						<?php $hero_country_label = $ui['hero_countries'][ $country_key ]; ?>
						&middot; <?php echo esc_html( $counts['country'][ $country_key ] ); ?> <?php echo $is_arabic ? 'في' : 'in'; ?> <?php echo esc_html( $hero_country_label ); ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</p>
		</div>
	</section>

	<div class="ckb-shell">
		<aside class="ckb-side" aria-label="<?php echo esc_attr( $ui['filter_stores'] ); ?>">
			<div class="ckb-side__inner">
				<div class="ckl-search">
					<svg class="ckl-search__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10.5 3a7.5 7.5 0 1 1-4.7 13.35l-2.6 2.6a1.2 1.2 0 0 1-1.7-1.7l2.6-2.6A7.5 7.5 0 0 1 10.5 3Zm0 2.4a5.1 5.1 0 1 0 0 10.2 5.1 5.1 0 0 0 0-10.2Z"/></svg>
					<label class="ckl-sr" for="ckl-q"><?php echo esc_html( $ui['search_label'] ); ?></label>
					<input id="ckl-q" type="search" dir="auto" autocomplete="off" spellcheck="false" placeholder="<?php echo esc_attr( $ui['search'] ); ?>" />
					<button type="button" class="ckl-search__clear" hidden aria-label="<?php echo esc_attr( $ui['clear_search'] ); ?>"><svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="m10 8.6 3.9-3.9 1.4 1.4-3.9 3.9 3.9 3.9-1.4 1.4-3.9-3.9-3.9 3.9-1.4-1.4 3.9-3.9-3.9-3.9 1.4-1.4z"/></svg></button>
					<kbd class="ckl-search__kbd" aria-hidden="true">/</kbd>
				</div>

				<details class="ckb-filters" data-desktop-open>
					<summary class="ckb-filters__sum">
						<span><?php echo esc_html( $ui['filters'] ); ?></span>
						<span class="ckb-filters__badge" data-filter-badge hidden></span>
						<svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M10 13.2 4.4 7.6 5.8 6.2 10 10.4l4.2-4.2 1.4 1.4z"/></svg>
					</summary>
					<div class="ckb-filters__body">
						<div class="ckb-facet" data-facet>
							<h3 class="ckb-facet__title"><?php echo esc_html( $ui['country'] ); ?></h3>
							<ul class="ckb-facet__list">
								<li><button type="button" class="ckb-facet__btn" data-country-tab="all" aria-pressed="true"><span><?php echo esc_html( $ui['all_countries'] ); ?></span><span class="ckb-facet__n"><?php echo esc_html( $counts['total'] ); ?></span></button></li>
								<?php foreach ( $context['countries'] as $country_key => $country_label ) : ?>
									<?php if ( ! empty( $counts['country'][ $country_key ] ) ) : ?>
										<li><button type="button" class="ckb-facet__btn" data-country-tab="<?php echo esc_attr( $country_key ); ?>" aria-pressed="false"><span><?php echo esc_html( $country_label ); ?></span><span class="ckb-facet__n"><?php echo esc_html( $counts['country'][ $country_key ] ); ?></span></button></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</div>

						<div class="ckb-facet" data-facet>
							<h3 class="ckb-facet__title"><?php echo esc_html( $ui['type'] ); ?></h3>
							<ul class="ckb-facet__list">
								<?php foreach ( $context['types'] as $type_key => $type_label ) : ?>
									<?php if ( ! empty( $counts['type'][ $type_key ] ) ) : ?>
										<li><button type="button" class="ckb-facet__btn" data-type-chip="<?php echo esc_attr( $type_key ); ?>" aria-pressed="false"><span><?php echo esc_html( $type_label ); ?></span><span class="ckb-facet__n"><?php echo esc_html( $counts['type'][ $type_key ] ); ?></span></button></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</div>

						<div class="ckb-facet" data-facet>
							<h3 class="ckb-facet__title"><?php echo esc_html( $ui['region'] ); ?></h3>
							<ul class="ckb-facet__list">
								<?php foreach ( $context['regions'] as $region_key => $region_label ) : ?>
									<?php if ( in_array( $region_key, array( 'dubai', 'abudhabi' ), true ) ) { continue; } ?>
									<?php if ( ! empty( $counts['region'][ $region_key ] ) ) : ?>
										<?php $region_owner = in_array( $region_key, array( 'dubai', 'abudhabi' ), true ) ? 'uae' : 'ksa'; ?>
										<?php $wp_region = array_search( $region_key, $context['meta']['wp'], true ); ?>
										<li><button type="button" class="ckb-facet__btn" data-region-chip="<?php echo esc_attr( $region_key ); ?>" data-owner="<?php echo esc_attr( $region_owner ); ?>" data-wp="<?php echo esc_attr( false === $wp_region ? '' : $wp_region ); ?>" aria-pressed="false"><span><?php echo esc_html( $region_label ); ?></span><span class="ckb-facet__n"><?php echo esc_html( $counts['region'][ $region_key ] ); ?></span></button></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</div>

						<div class="ckb-facet" data-facet>
							<h3 class="ckb-facet__title"><?php echo esc_html( $ui['city'] ); ?></h3>
							<ul class="ckb-facet__list">
								<?php foreach ( $context['city_labels'] as $city_key => $city_label ) : ?>
									<?php if ( ! empty( $counts['city'][ $city_key ] ) ) : ?>
										<li><button type="button" class="ckb-facet__btn" data-city-chip="<?php echo esc_attr( $city_key ); ?>" data-owner="<?php echo esc_attr( $context['city_countries'][ $city_key ] ); ?>" aria-pressed="false"><span><?php echo esc_html( $city_label ); ?></span><span class="ckb-facet__n"><?php echo esc_html( $counts['city'][ $city_key ] ); ?></span></button></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</div>

						<button type="button" class="ckb-reset ckl-reset" hidden><?php echo esc_html( $ui['reset_filters'] ); ?></button>
					</div>
				</details>
			</div>
		</aside>

		<div class="ckb-main">
			<div class="ckb-bar">
				<p class="ckb-bar__count" dir="<?php echo $is_arabic ? 'rtl' : 'ltr'; ?>" role="status" aria-live="polite"><?php echo esc_html( $ui['showing'] ); ?> <b id="ckl-shown"><?php echo esc_html( $counts['total'] ); ?></b> <?php echo esc_html( $ui['of'] ); ?> <?php echo esc_html( $counts['total'] ); ?> <?php echo esc_html( $plural( $counts['total'] ) ); ?></p>
			</div>

			<div class="ckl-results">
				<?php foreach ( $context['countries'] as $country_key => $country_label ) : ?>
					<?php if ( empty( $context['groups'][ $country_key ] ) ) { continue; } ?>
					<section class="ckb-country" data-groupable aria-labelledby="h-<?php echo esc_attr( $country_key ); ?>">
						<div class="ckb-country__head">
							<h2 id="h-<?php echo esc_attr( $country_key ); ?>"><?php echo esc_html( $country_label ); ?></h2>
							<span class="ckb-country__n" dir="<?php echo $is_arabic ? 'rtl' : 'ltr'; ?>"><b data-group-count><?php echo esc_html( $counts['country'][ $country_key ] ); ?></b> <span data-plural="store"><?php echo esc_html( $plural( $counts['country'][ $country_key ] ) ); ?></span></span>
						</div>

						<?php foreach ( $context['types'] as $type_key => $type_label ) : ?>
							<?php if ( empty( $context['groups'][ $country_key ][ $type_key ] ) ) { continue; } ?>
							<?php $type_count = array_sum( array_map( 'count', $context['groups'][ $country_key ][ $type_key ] ) ); ?>
							<section class="ckb-type" data-groupable data-typeblock="<?php echo esc_attr( $type_key ); ?>" aria-labelledby="t-<?php echo esc_attr( $country_key . '-' . $type_key ); ?>">
								<header class="ckb-type__head">
									<span class="ckb-type__ico">
										<?php if ( 'fuel' === $type_key ) : ?>
											<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 3.6A1.6 1.6 0 0 1 5.6 2h6.3a1.6 1.6 0 0 1 1.6 1.6V20H15v1.8H2.5V20H4V3.6Zm2 1.2v4.4h5.5V4.8H6Zm12.9.5 2.5 2.5c.4.4.6.9.6 1.4v7.9a2 2 0 0 1-4 0v-4h-1.2V5.5l2.1-.2Z"/></svg>
										<?php else : ?>
											<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3.4 3h17.2l1.3 4.6a3 3 0 0 1-2.9 3.8 3 3 0 0 1-2.5-1.3 3 3 0 0 1-2.5 1.3 3 3 0 0 1-2.5-1.3 3 3 0 0 1-2.5 1.3 3 3 0 0 1-2.9-3.8L3.4 3Zm1.1 9.6c.5.1 1 .2 1.4.2.6 0 1.2-.1 1.8-.4V21H4.5v-8.4ZM19.5 21h-3.2v-8.6c.6.3 1.2.4 1.8.4.5 0 .9 0 1.4-.2V21Zm-9.9-8.4h4.8V17H9.6v-4.4Z"/></svg>
										<?php endif; ?>
									</span>
									<h3 id="t-<?php echo esc_attr( $country_key . '-' . $type_key ); ?>"><?php echo esc_html( $type_label ); ?></h3>
									<span class="ckb-type__n" dir="<?php echo $is_arabic ? 'rtl' : 'ltr'; ?>"><b data-group-count><?php echo esc_html( $type_count ); ?></b> <span data-plural="store"><?php echo esc_html( $plural( $type_count ) ); ?></span></span>
								</header>

								<?php foreach ( $context['groups'][ $country_key ][ $type_key ] as $city_key => $city_locations ) : ?>
									<?php $city_id = $country_key . '-' . $type_key . '-' . $city_key; ?>
									<section class="ckb-city" data-groupable aria-labelledby="c-<?php echo esc_attr( $city_id ); ?>">
										<header class="ckb-city__head">
											<h3 id="c-<?php echo esc_attr( $city_id ); ?>"><?php echo esc_html( $context['city_labels'][ $city_key ] ); ?></h3>
											<span class="ckb-city__meta" dir="<?php echo $is_arabic ? 'rtl' : 'ltr'; ?>"><?php echo esc_html( $context['regions'][ $city_locations[0]['region'] ] ); ?> &middot; <b data-group-count><?php echo esc_html( count( $city_locations ) ); ?></b> <span data-plural="store"><?php echo esc_html( $plural( count( $city_locations ) ) ); ?></span></span>
										</header>
										<ul class="ckb-rows">
											<?php foreach ( $city_locations as $location ) : ?>
												<?php
												$haystack = strtolower( implode( ' ', array( $location['number'], $location['code'], $location['name'], $location['city'], $location['address'], $context['regions'][ $location['region'] ], $context['types'][ $location['type'] ] ) ) );
												$directions = $location['directions_url'];
												if ( ! $directions ) {
													$directions = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( 'Circle K ' . $location['name'] . ', ' . $location['address'] );
												}
												?>
												<li class="ckb-row" data-store data-primary data-country="<?php echo esc_attr( $location['country'] ); ?>" data-region="<?php echo esc_attr( $location['region'] ); ?>" data-city="<?php echo esc_attr( $location['city_slug'] ); ?>" data-type="<?php echo esc_attr( $location['type'] ); ?>" data-hay="<?php echo esc_attr( $haystack ); ?>">
													<span class="ckb-row__no" aria-hidden="true"><?php echo esc_html( $location['number'] ); ?></span>
													<div class="ckb-row__main">
														<h4 class="ckb-row__name" dir="<?php echo esc_attr( $location['name_dir'] ); ?>"><?php echo esc_html( $location['name'] ); ?></h4>
														<p class="ckb-row__addr" dir="<?php echo esc_attr( $location['address_dir'] ); ?>"><?php echo nl2br( esc_html( $location['address'] ) ); ?></p>
													</div>
													<span class="ckb-row__city" dir="<?php echo esc_attr( $location['city_dir'] ); ?>"><?php echo esc_html( $location['city'] ); ?></span>
													<?php $directions_sr = $is_arabic ? sprintf( 'إلى سيركل كي %1$s، %2$s', $location['name'], $location['city'] ) : sprintf( 'to Circle K %1$s, %2$s', $location['name'], $location['city'] ); ?>
													<a class="ckb-row__dir" href="<?php echo esc_url( $directions ); ?>" target="_blank" rel="noopener noreferrer"><span><?php echo esc_html( $ui['directions'] ); ?></span><svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M4 10h10.2l-3.9-3.9L11.7 4.7 18 11l-6.3 6.3-1.4-1.4 3.9-3.9H4z"/></svg><span class="ckl-sr"> <?php echo esc_html( $directions_sr ); ?></span></a>
												</li>
											<?php endforeach; ?>
										</ul>
									</section>
								<?php endforeach; ?>
							</section>
						<?php endforeach; ?>
					</section>
				<?php endforeach; ?>

				<p class="ckl-empty" hidden>
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10.5 3a7.5 7.5 0 1 1-4.7 13.35l-2.6 2.6a1.2 1.2 0 0 1-1.7-1.7l2.6-2.6A7.5 7.5 0 0 1 10.5 3Zm0 2.4a5.1 5.1 0 1 0 0 10.2 5.1 5.1 0 0 0 0-10.2Z"/></svg>
					<?php echo esc_html( $ui['no_results'] ); ?> <button type="button" class="ckl-empty__reset"><?php echo esc_html( $ui['clear_filters'] ); ?></button>
				</p>
			</div>
		</div>
	</div>

	<section class="ckb-strategy">
		<div class="ckb-strategy__inner">
			<div class="ckb-strategy__head">
				<h2><?php echo esc_html( $settings['strategy_title'] ); ?></h2>
				<p><?php echo esc_html( $settings['strategy_text'] ); ?></p>
			</div>
			<ul class="ckb-strip">
				<?php foreach ( $strategy_items as $item ) : ?>
					<li><span><img src="<?php echo esc_url( CKL_URL . 'assets/img/' . $item[0] ); ?>" width="256" height="256" alt="" loading="lazy" /></span><h3><?php echo esc_html( $item[1] ); ?></h3></li>
				<?php endforeach; ?>
			</ul>
			<a href="<?php echo esc_url( $settings['strategy_link_url'] ); ?>" class="ckl-btn ckb-btn"><?php echo esc_html( $settings['strategy_link_text'] ); ?></a>
		</div>
	</section>
</div>
