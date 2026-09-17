<?php
/**
 * Main plugin controller.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CircleK_Locations {
	const POST_TYPE = 'ck_location';
	const AREA_TAXONOMY = 'ckl_area';
	const TYPE_TAXONOMY = 'ckl_location_type';

	private static $instance;

	private $countries = array(
		'ksa' => 'Kingdom of Saudi Arabia',
		'uae' => 'United Arab Emirates',
	);

	private $regions = array(
		'central'   => 'Central Region',
		'western'   => 'Western Region',
		'eastern'   => 'Eastern Region',
		'southern'  => 'Southern Region',
		'dubai'     => 'Dubai',
		'abudhabi'  => 'Abu Dhabi',
	);

	private $types = array(
		'fuel'  => 'Fuel Stations',
		'store' => 'Convenience Stores',
	);

	private $countries_ar = array(
		'ksa' => 'المملكة العربية السعودية',
		'uae' => 'الإمارات العربية المتحدة',
	);

	private $regions_ar = array(
		'central'  => 'المنطقة الوسطى',
		'western'  => 'المنطقة الغربية',
		'eastern'  => 'المنطقة الشرقية',
		'southern' => 'المنطقة الجنوبية',
		'dubai'    => 'دبي',
		'abudhabi' => 'أبوظبي',
	);

	private $types_ar = array(
		'fuel'  => 'محطات الوقود',
		'store' => 'متاجر الخدمة السريعة',
	);

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ), 5 );
		add_action( 'init', array( $this, 'register_meta_fields' ) );
		add_action( 'init', array( $this, 'maybe_upgrade_data' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_location_fields' ) );
		add_action( 'save_post_page', array( $this, 'save_page_fields' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'admin_columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'admin_column_value' ), 10, 2 );
		add_filter( 'the_content', array( $this, 'replace_locations_page_content' ), 20 );
		add_filter( 'document_title_parts', array( $this, 'filter_document_title' ) );
		add_filter( 'pre_get_document_title', array( $this, 'filter_full_document_title' ), 20 );
		add_filter( 'wpseo_title', array( $this, 'filter_full_document_title' ), 20 );
		add_filter( 'rank_math/frontend/title', array( $this, 'filter_full_document_title' ), 20 );
		add_shortcode( 'circlek_locations', array( $this, 'shortcode' ) );
	}

	public static function activate() {
		$plugin = self::instance();
		$plugin->register_post_type();
		$plugin->register_taxonomies();
		$plugin->register_meta_fields();
		$plugin->seed_locations();
		$plugin->backfill_arabic_fields();
		$plugin->migrate_location_taxonomies();
		flush_rewrite_rules();
	}

	public function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels' => array(
					'name'               => __( 'Locations', 'circlek-locations' ),
					'singular_name'      => __( 'Location', 'circlek-locations' ),
					'add_new_item'       => __( 'Add New Location', 'circlek-locations' ),
					'edit_item'          => __( 'Edit Location', 'circlek-locations' ),
					'new_item'           => __( 'New Location', 'circlek-locations' ),
					'view_item'          => __( 'View Location', 'circlek-locations' ),
					'search_items'       => __( 'Search Locations', 'circlek-locations' ),
					'not_found'          => __( 'No locations found.', 'circlek-locations' ),
					'not_found_in_trash' => __( 'No locations found in Trash.', 'circlek-locations' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'menu_icon'           => 'dashicons-location-alt',
				'menu_position'       => 21,
				'supports'            => array( 'title', 'custom-fields' ),
				'has_archive'         => false,
				'rewrite'             => false,
				'exclude_from_search' => true,
			)
		);
	}

	public function register_taxonomies() {
		register_taxonomy(
			self::AREA_TAXONOMY,
			array( self::POST_TYPE ),
			array(
				'labels' => array(
					'name'          => __( 'Location Areas', 'circlek-locations' ),
					'singular_name' => __( 'Location Area', 'circlek-locations' ),
					'menu_name'     => __( 'Location Areas', 'circlek-locations' ),
					'all_items'     => __( 'All Location Areas', 'circlek-locations' ),
					'edit_item'     => __( 'Edit Location Area', 'circlek-locations' ),
					'add_new_item'  => __( 'Add Location Area', 'circlek-locations' ),
				),
				'public'            => false,
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => false,
				'show_in_rest'      => true,
				'meta_box_cb'       => false,
				'rewrite'           => false,
				'query_var'         => false,
			)
		);

		register_taxonomy(
			self::TYPE_TAXONOMY,
			array( self::POST_TYPE ),
			array(
				'labels' => array(
					'name'          => __( 'Location Types', 'circlek-locations' ),
					'singular_name' => __( 'Location Type', 'circlek-locations' ),
					'menu_name'     => __( 'Location Types', 'circlek-locations' ),
					'all_items'     => __( 'All Location Types', 'circlek-locations' ),
					'edit_item'     => __( 'Edit Location Type', 'circlek-locations' ),
					'add_new_item'  => __( 'Add Location Type', 'circlek-locations' ),
				),
				'public'            => false,
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_admin_column' => false,
				'show_in_rest'      => true,
				'meta_box_cb'       => false,
				'rewrite'           => false,
				'query_var'         => false,
			)
		);
	}

	public function register_meta_fields() {
		$location_fields = array(
			'ckl_store_code'     => array( 'string', 'sanitize_text_field' ),
			'ckl_country'        => array( 'string', 'sanitize_key' ),
			'ckl_region'         => array( 'string', 'sanitize_key' ),
			'ckl_city'           => array( 'string', 'sanitize_text_field' ),
			'ckl_name_ar'        => array( 'string', 'sanitize_text_field' ),
			'ckl_city_ar'        => array( 'string', 'sanitize_text_field' ),
			'ckl_location_type'  => array( 'string', 'sanitize_key' ),
			'ckl_address'        => array( 'string', 'sanitize_textarea_field' ),
			'ckl_address_ar'     => array( 'string', 'sanitize_textarea_field' ),
			'ckl_directions_url' => array( 'string', 'esc_url_raw' ),
		);

		foreach ( $location_fields as $key => $field ) {
			register_post_meta(
				self::POST_TYPE,
				$key,
				array(
					'single'            => true,
					'type'              => $field[0],
					'show_in_rest'      => true,
					'sanitize_callback' => $field[1],
					'auth_callback'     => function() {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}

		$page_fields = array(
			'ckl_hero_title',
			'ckl_hero_image_url',
			'ckl_strategy_title',
			'ckl_strategy_text',
			'ckl_strategy_link_text',
			'ckl_strategy_link_url',
		);

		foreach ( $page_fields as $key ) {
			register_post_meta(
				'page',
				$key,
				array(
					'single'            => true,
					'type'              => 'string',
					'show_in_rest'      => true,
					'sanitize_callback' => false !== strpos( $key, '_url' ) ? 'esc_url_raw' : 'sanitize_text_field',
					'auth_callback'     => function() {
						return current_user_can( 'edit_pages' );
					},
				)
			);
		}
	}

	public function add_meta_boxes( $post_type, $post ) {
		if ( self::POST_TYPE === $post_type ) {
			remove_meta_box( 'postcustom', self::POST_TYPE, 'normal' );
			add_meta_box(
				'ckl-location-details',
				__( 'Location Details', 'circlek-locations' ),
				array( $this, 'render_location_meta_box' ),
				self::POST_TYPE,
				'normal',
				'high'
			);
		}

		if ( 'page' === $post_type && $post && ( 'locations' === $post->post_name || has_shortcode( $post->post_content, 'circlek_locations' ) ) ) {
			add_meta_box(
				'ckl-page-settings',
				__( 'Locations Page Settings', 'circlek-locations' ),
				array( $this, 'render_page_meta_box' ),
				'page',
				'normal',
				'default'
			);
		}
	}

	public function render_location_meta_box( $post ) {
		wp_nonce_field( 'ckl_save_location', 'ckl_location_nonce' );
		$classification = $this->get_location_classification( $post->ID );
		$values = array(
			'code'       => get_post_meta( $post->ID, 'ckl_store_code', true ),
			'country'    => $classification['country'] ? $classification['country'] : get_post_meta( $post->ID, 'ckl_country', true ),
			'region'     => $classification['region'] ? $classification['region'] : get_post_meta( $post->ID, 'ckl_region', true ),
			'city'       => $classification['city'] ? $classification['city'] : get_post_meta( $post->ID, 'ckl_city', true ),
			'name_ar'    => get_post_meta( $post->ID, 'ckl_name_ar', true ),
			'city_ar'    => get_post_meta( $post->ID, 'ckl_city_ar', true ),
			'type'       => $classification['type'] ? $classification['type'] : get_post_meta( $post->ID, 'ckl_location_type', true ),
			'address'    => get_post_meta( $post->ID, 'ckl_address', true ),
			'address_ar' => get_post_meta( $post->ID, 'ckl_address_ar', true ),
			'directions' => get_post_meta( $post->ID, 'ckl_directions_url', true ),
		);
		?>
		<style>.ckl-admin-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 22px}.ckl-admin-field label{display:block;font-weight:600;margin-bottom:6px}.ckl-admin-field input,.ckl-admin-field select,.ckl-admin-field textarea{width:100%}.ckl-admin-field--wide{grid-column:1/-1}.ckl-admin-section{grid-column:1/-1;margin:8px 0 -4px;padding-top:18px;border-top:1px solid #dcdcde}.ckl-admin-section h3{margin:0 0 5px}.ckl-admin-section p,.ckl-admin-help{color:#646970;font-size:12px;margin:5px 0 0}@media(max-width:782px){.ckl-admin-grid{grid-template-columns:1fr}}</style>
		<div class="ckl-admin-grid">
			<?php $this->text_field( 'ckl_store_code', __( 'Source store code', 'circlek-locations' ), $values['code'] ); ?>
			<?php $this->select_field( 'ckl_country', __( 'Country', 'circlek-locations' ), $values['country'], $this->countries ); ?>
			<?php $this->select_field( 'ckl_region', __( 'Region', 'circlek-locations' ), $values['region'], $this->regions ); ?>
			<?php $this->text_field( 'ckl_city', __( 'City', 'circlek-locations' ), $values['city'] ); ?>
			<?php $this->select_field( 'ckl_location_type', __( 'Location type', 'circlek-locations' ), $values['type'], $this->types ); ?>
			<div class="ckl-admin-field ckl-admin-field--wide">
				<label for="ckl_address"><?php esc_html_e( 'Address', 'circlek-locations' ); ?></label>
				<textarea id="ckl_address" name="ckl_address" rows="4" required><?php echo esc_textarea( $values['address'] ); ?></textarea>
			</div>
			<div class="ckl-admin-field ckl-admin-field--wide">
				<label for="ckl_directions_url"><?php esc_html_e( 'Directions URL (optional)', 'circlek-locations' ); ?></label>
				<input id="ckl_directions_url" name="ckl_directions_url" type="url" value="<?php echo esc_attr( $values['directions'] ); ?>" placeholder="https://maps.google.com/..." />
				<p class="ckl-admin-help"><?php esc_html_e( 'When empty, the page generates a Google Maps search from the store name and address.', 'circlek-locations' ); ?></p>
			</div>
			<div class="ckl-admin-section">
				<h3><?php esc_html_e( 'Arabic content', 'circlek-locations' ); ?></h3>
				<p><?php esc_html_e( 'English values are used automatically until these fields are translated.', 'circlek-locations' ); ?></p>
			</div>
			<div class="ckl-admin-field" dir="rtl">
				<label for="ckl_name_ar"><?php esc_html_e( 'Store name (Arabic)', 'circlek-locations' ); ?></label>
				<input id="ckl_name_ar" name="ckl_name_ar" type="text" dir="rtl" value="<?php echo esc_attr( $values['name_ar'] ); ?>" />
			</div>
			<div class="ckl-admin-field" dir="rtl">
				<label for="ckl_city_ar"><?php esc_html_e( 'City (Arabic)', 'circlek-locations' ); ?></label>
				<input id="ckl_city_ar" name="ckl_city_ar" type="text" dir="rtl" value="<?php echo esc_attr( $values['city_ar'] ); ?>" />
			</div>
			<div class="ckl-admin-field ckl-admin-field--wide" dir="rtl">
				<label for="ckl_address_ar"><?php esc_html_e( 'Address (Arabic)', 'circlek-locations' ); ?></label>
				<textarea id="ckl_address_ar" name="ckl_address_ar" rows="4" dir="rtl"><?php echo esc_textarea( $values['address_ar'] ); ?></textarea>
			</div>
		</div>
		<?php
	}

	public function render_page_meta_box( $post ) {
		wp_nonce_field( 'ckl_save_page', 'ckl_page_nonce' );
		$fields = $this->get_page_settings( $post->ID );
		?>
		<style>.ckl-admin-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 22px}.ckl-admin-field label{display:block;font-weight:600;margin-bottom:6px}.ckl-admin-field input,.ckl-admin-field select,.ckl-admin-field textarea{width:100%}.ckl-admin-field--wide{grid-column:1/-1}.ckl-admin-help{color:#646970;font-size:12px;margin:5px 0 0}@media(max-width:782px){.ckl-admin-grid{grid-template-columns:1fr}}</style>
		<div class="ckl-admin-grid">
			<?php $this->text_field( 'ckl_hero_title', __( 'Hero title', 'circlek-locations' ), $fields['hero_title'] ); ?>
			<?php $this->text_field( 'ckl_hero_image_url', __( 'Hero image URL', 'circlek-locations' ), $fields['hero_image_url'], 'url' ); ?>
			<?php $this->text_field( 'ckl_strategy_title', __( 'Strategy title', 'circlek-locations' ), $fields['strategy_title'] ); ?>
			<?php $this->text_field( 'ckl_strategy_link_text', __( 'Button label', 'circlek-locations' ), $fields['strategy_link_text'] ); ?>
			<div class="ckl-admin-field ckl-admin-field--wide">
				<label for="ckl_strategy_text"><?php esc_html_e( 'Strategy description', 'circlek-locations' ); ?></label>
				<textarea id="ckl_strategy_text" name="ckl_strategy_text" rows="3"><?php echo esc_textarea( $fields['strategy_text'] ); ?></textarea>
			</div>
			<div class="ckl-admin-field ckl-admin-field--wide">
				<?php $this->text_field( 'ckl_strategy_link_url', __( 'Button URL', 'circlek-locations' ), $fields['strategy_link_url'], 'url' ); ?>
			</div>
		</div>
		<?php
	}

	private function text_field( $name, $label, $value, $type = 'text' ) {
		?>
		<div class="ckl-admin-field">
			<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
			<input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		</div>
		<?php
	}

	private function select_field( $name, $label, $value, $options ) {
		?>
		<div class="ckl-admin-field">
			<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
			<select id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" required>
				<option value=""><?php esc_html_e( 'Select…', 'circlek-locations' ); ?></option>
				<?php foreach ( $options as $key => $option_label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	public function save_location_fields( $post_id ) {
		if ( ! $this->can_save( $post_id, 'ckl_location_nonce', 'ckl_save_location' ) ) {
			return;
		}

		$country = isset( $_POST['ckl_country'] ) ? sanitize_key( wp_unslash( $_POST['ckl_country'] ) ) : '';
		$region  = isset( $_POST['ckl_region'] ) ? sanitize_key( wp_unslash( $_POST['ckl_region'] ) ) : '';
		$type    = isset( $_POST['ckl_location_type'] ) ? sanitize_key( wp_unslash( $_POST['ckl_location_type'] ) ) : '';
		$city    = isset( $_POST['ckl_city'] ) ? sanitize_text_field( wp_unslash( $_POST['ckl_city'] ) ) : '';

		update_post_meta( $post_id, 'ckl_store_code', isset( $_POST['ckl_store_code'] ) ? sanitize_text_field( wp_unslash( $_POST['ckl_store_code'] ) ) : '' );
		update_post_meta( $post_id, 'ckl_country', isset( $this->countries[ $country ] ) ? $country : '' );
		update_post_meta( $post_id, 'ckl_region', isset( $this->regions[ $region ] ) ? $region : '' );
		update_post_meta( $post_id, 'ckl_location_type', isset( $this->types[ $type ] ) ? $type : '' );
		update_post_meta( $post_id, 'ckl_city', $city );
		update_post_meta( $post_id, 'ckl_address', isset( $_POST['ckl_address'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ckl_address'] ) ) : '' );
		update_post_meta( $post_id, 'ckl_name_ar', isset( $_POST['ckl_name_ar'] ) ? sanitize_text_field( wp_unslash( $_POST['ckl_name_ar'] ) ) : '' );
		update_post_meta( $post_id, 'ckl_city_ar', isset( $_POST['ckl_city_ar'] ) ? sanitize_text_field( wp_unslash( $_POST['ckl_city_ar'] ) ) : '' );
		update_post_meta( $post_id, 'ckl_address_ar', isset( $_POST['ckl_address_ar'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ckl_address_ar'] ) ) : '' );
		update_post_meta( $post_id, 'ckl_directions_url', isset( $_POST['ckl_directions_url'] ) ? esc_url_raw( wp_unslash( $_POST['ckl_directions_url'] ) ) : '' );

		$this->sync_location_taxonomies( $post_id, $country, $region, $city, $type );
	}

	public function save_page_fields( $post_id ) {
		if ( ! $this->can_save( $post_id, 'ckl_page_nonce', 'ckl_save_page' ) ) {
			return;
		}

		$text_fields = array( 'ckl_hero_title', 'ckl_strategy_title', 'ckl_strategy_text', 'ckl_strategy_link_text' );
		$url_fields  = array( 'ckl_hero_image_url', 'ckl_strategy_link_url' );

		foreach ( $text_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}

		foreach ( $url_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, esc_url_raw( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
	}

	private function can_save( $post_id, $nonce_name, $action ) {
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return false;
		}

		if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), $action ) ) {
			return false;
		}

		return current_user_can( 'edit_post', $post_id );
	}

	public function enqueue_assets() {
		if ( ! $this->is_locations_request() ) {
			return;
		}

		wp_enqueue_style( 'circlek-locations', CKL_URL . 'assets/css/locations.css', array(), CKL_VERSION );
		wp_enqueue_script( 'circlek-locations', CKL_URL . 'assets/js/locations.js', array(), CKL_VERSION, true );
	}

	private function is_locations_request() {
		if ( ! is_singular( 'page' ) ) {
			return false;
		}

		$post = get_queried_object();
		return $post instanceof WP_Post && ( 'locations' === $post->post_name || has_shortcode( $post->post_content, 'circlek_locations' ) );
	}

	public function replace_locations_page_content( $content ) {
		if ( is_admin() || ! in_the_loop() || ! is_main_query() || ! is_page( 'locations' ) ) {
			return $content;
		}

		if ( has_shortcode( $content, 'circlek_locations' ) ) {
			return $content;
		}

		if ( ! apply_filters( 'circlek_locations_auto_replace', true ) ) {
			return $content;
		}

		return $this->shortcode( array() );
	}

	public function shortcode( $atts = array() ) {
		wp_enqueue_style( 'circlek-locations', CKL_URL . 'assets/css/locations.css', array(), CKL_VERSION );
		wp_enqueue_script( 'circlek-locations', CKL_URL . 'assets/js/locations.js', array(), CKL_VERSION, true );

		$locations = $this->get_locations();
		$context   = $this->build_context( $locations );
		$page_id   = get_queried_object_id();
		$settings  = $this->get_page_settings( $page_id );

		ob_start();
		include CKL_DIR . 'templates/locations.php';
		return ob_get_clean();
	}

	private function get_locations() {
		$is_arabic = $this->is_arabic_request();
		$posts = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
				'suppress_filters' => true,
			)
		);

		$locations = array();
		foreach ( $posts as $post ) {
			$classification = $this->get_location_classification( $post->ID );
			$country = $classification['country'] ? $classification['country'] : get_post_meta( $post->ID, 'ckl_country', true );
			$region  = $classification['region'] ? $classification['region'] : get_post_meta( $post->ID, 'ckl_region', true );
			$city_en = $classification['city'] ? $classification['city'] : get_post_meta( $post->ID, 'ckl_city', true );
			$type    = $classification['type'] ? $classification['type'] : get_post_meta( $post->ID, 'ckl_location_type', true );
			$address_en = get_post_meta( $post->ID, 'ckl_address', true );
			$name_en = get_the_title( $post );
			$name_ar = get_post_meta( $post->ID, 'ckl_name_ar', true );
			$city_ar = get_post_meta( $post->ID, 'ckl_city_ar', true );
			$address_ar = get_post_meta( $post->ID, 'ckl_address_ar', true );
			$name    = $is_arabic && $name_ar ? $name_ar : $name_en;
			$city    = $is_arabic && $city_ar ? $city_ar : $city_en;
			$address = $is_arabic && $address_ar ? $address_ar : $address_en;

			if ( ! isset( $this->countries[ $country ], $this->regions[ $region ], $this->types[ $type ] ) || ! $city_en || ! $address_en ) {
				continue;
			}

			$locations[] = array(
				'id'             => $post->ID,
				'name'           => $name,
				'code'           => get_post_meta( $post->ID, 'ckl_store_code', true ),
				'country'        => $country,
				'region'         => $region,
				'city'           => $city,
				'city_slug'      => sanitize_title( $city_en ),
				'type'           => $type,
				'address'        => $address,
				'directions_url' => get_post_meta( $post->ID, 'ckl_directions_url', true ),
				'display_order'  => absint( get_post_meta( $post->ID, 'ckl_display_order', true ) ),
				'name_dir'       => $this->text_direction( $name ),
				'city_dir'       => $this->text_direction( $city ),
				'address_dir'    => $this->text_direction( $address ),
			);
		}

		usort(
			$locations,
			function( $a, $b ) {
				$order_a = $a['display_order'] ? $a['display_order'] : 9999;
				$order_b = $b['display_order'] ? $b['display_order'] : 9999;
				if ( $order_a === $order_b ) {
					return strcasecmp( $a['name'], $b['name'] );
				}
				return $order_a <=> $order_b;
			}
		);

		return $locations;
	}

	private function is_arabic_request() {
		if ( function_exists( 'pll_current_language' ) ) {
			return 'ar' === pll_current_language( 'slug' );
		}

		return is_rtl();
	}

	private function text_direction( $text ) {
		return preg_match( '/\p{Arabic}/u', (string) $text ) ? 'rtl' : 'ltr';
	}

	private function build_context( $locations ) {
		$is_arabic = $this->is_arabic_request();
		$countries = $is_arabic ? $this->countries_ar : $this->countries;
		$regions   = $is_arabic ? $this->regions_ar : $this->regions;
		$types     = $is_arabic ? $this->types_ar : $this->types;
		$groups = array();
		$counts = array(
			'total'   => count( $locations ),
			'country' => array(),
			'region'  => array(),
			'city'    => array(),
			'type'    => array(),
		);
		$city_labels = array();
		$city_countries = array();

		foreach ( $locations as $location ) {
			$country = $location['country'];
			$type    = $location['type'];
			$city    = $location['city_slug'];

			if ( ! isset( $groups[ $country ] ) ) {
				$groups[ $country ] = array();
			}
			if ( ! isset( $groups[ $country ][ $type ] ) ) {
				$groups[ $country ][ $type ] = array();
			}
			if ( ! isset( $groups[ $country ][ $type ][ $city ] ) ) {
				$groups[ $country ][ $type ][ $city ] = array();
			}

			$groups[ $country ][ $type ][ $city ][] = $location;
			$city_labels[ $city ] = $location['city'];
			$city_countries[ $city ] = $country;

			foreach ( array( 'country', 'region', 'city', 'type' ) as $facet ) {
				$key = 'city' === $facet ? $location['city_slug'] : $location[ $facet ];
				$counts[ $facet ][ $key ] = isset( $counts[ $facet ][ $key ] ) ? $counts[ $facet ][ $key ] + 1 : 1;
			}
		}

		$preferred_cities = array(
			'riyadh'    => 'Riyadh',
			'jeddah'    => 'Jeddah',
			'makkah'    => 'Makkah',
			'dammam'    => 'Dammam',
			'al-khobar' => 'Al Khobar',
			'dhahran'   => 'Dhahran',
			'jubail'    => 'Jubail',
			'khamis'    => 'Khamis',
			'dubai'     => 'Dubai',
			'abu-dhabi' => 'Abu Dhabi',
		);
		$city_labels = array_replace( array_intersect_key( $preferred_cities, $city_labels ), $city_labels );

		return array(
			'groups'      => $groups,
			'counts'      => $counts,
			'countries'   => $countries,
			'regions'     => $regions,
			'types'       => $types,
			'city_labels' => $city_labels,
			'city_countries' => $city_countries,
			'is_rtl'      => $is_arabic,
			'ui'          => $this->get_ui_labels( $is_arabic ),
			'meta'        => array(
				'region' => $regions,
				'city'   => $city_labels,
				'wp'     => array( '34' => 'western', '35' => 'central', '36' => 'eastern' ),
				'ui'     => array( 'remove_filter' => $is_arabic ? 'إزالة التصفية' : 'Remove filter' ),
			),
		);
	}

	private function get_ui_labels( $is_arabic ) {
		if ( ! $is_arabic ) {
			return array(
				'search' => 'Search stores', 'search_label' => 'Search stores by name, city or address', 'clear_search' => 'Clear search',
				'filter_stores' => 'Filter stores', 'filters' => 'Filters', 'country' => 'Country', 'all_countries' => 'All countries',
				'type' => 'Type', 'region' => 'Region', 'city' => 'City', 'reset_filters' => 'Reset filters', 'showing' => 'Showing',
				'of' => 'of', 'store_code' => 'Store code', 'directions' => 'Directions', 'no_results' => 'No stores match your search.', 'clear_filters' => 'Clear filters',
				'hero_countries' => array( 'ksa' => 'Saudi Arabia', 'uae' => 'United Arab Emirates' ),
				'strategy_items' => array( 'Airports', 'Gas Station', 'Highway between cities', 'Hospitals', 'Large business complexes', 'Large Governmental Cities Projects' ),
			);
		}

		return array(
			'search' => 'ابحث عن متجر', 'search_label' => 'ابحث عن متجر بالاسم أو المدينة أو العنوان', 'clear_search' => 'مسح البحث',
			'filter_stores' => 'تصفية المتاجر', 'filters' => 'التصفية', 'country' => 'الدولة', 'all_countries' => 'كل الدول',
			'type' => 'النوع', 'region' => 'المنطقة', 'city' => 'المدينة', 'reset_filters' => 'إعادة التصفية', 'showing' => 'عرض',
			'of' => 'من', 'store_code' => 'رمز المتجر', 'directions' => 'الاتجاهات', 'no_results' => 'لا توجد متاجر مطابقة لبحثك.', 'clear_filters' => 'مسح التصفية',
			'hero_countries' => array( 'ksa' => 'السعودية', 'uae' => 'الإمارات' ),
			'strategy_items' => array( 'المطارات', 'محطة البترول', 'الطريق السريع بين المدن', 'المستشفيات', 'مجمعات تجارية كبيرة', 'مشاريع المدن الحكومية الكبيرة' ),
		);
	}

	private function get_page_settings( $page_id ) {
		$is_arabic = $this->is_arabic_request();
		$defaults = $is_arabic ? array(
			'hero_title'         => 'المواقع',
			'hero_image_url'     => CKL_URL . 'assets/img/LOCATION.jpg',
			'strategy_title'     => 'استراتيجية مواقع المتاجر',
			'strategy_text'      => 'سينصب تركيز AGT على تجربتنا الفريدة في مجال المأكولات والمشروبات في متاجر الرفاهية مما يجعلها الوجهة الشاملة',
			'strategy_link_text' => 'عرض الكل',
			'strategy_link_url'  => $page_id ? get_permalink( $page_id ) : home_url( '/ar/locations/' ),
		) : array(
			'hero_title'        => __( 'Locations', 'circlek-locations' ),
			'hero_image_url'    => CKL_URL . 'assets/img/LOCATION.jpg',
			'strategy_title'    => __( 'Stores Locations Strategy', 'circlek-locations' ),
			'strategy_text'     => __( 'AGT focus will be on our F&B unique experience in convenience stores making it the “One-Stop” Destination', 'circlek-locations' ),
			'strategy_link_text'=> __( 'View All', 'circlek-locations' ),
			'strategy_link_url' => $page_id ? get_permalink( $page_id ) : home_url( '/locations/' ),
		);

		if ( ! $page_id ) {
			return $defaults;
		}

		foreach ( array_keys( $defaults ) as $key ) {
			$value = get_post_meta( $page_id, 'ckl_' . $key, true );
			if ( '' !== $value ) {
				$defaults[ $key ] = $value;
			}
		}

		return $defaults;
	}

	public function filter_document_title( $parts ) {
		if ( $this->is_locations_request() && $this->is_arabic_request() ) {
			$parts['title'] = 'المواقع';
		}

		return $parts;
	}

	public function filter_full_document_title( $title ) {
		if ( $this->is_locations_request() && $this->is_arabic_request() ) {
			return 'المواقع | سيركل كي - متجر خدمة سريعة ومحطة وقود';
		}

		return $title;
	}

	public function admin_columns( $columns ) {
		return array(
			'cb'          => isset( $columns['cb'] ) ? $columns['cb'] : '<input type="checkbox" />',
			'title'       => __( 'Store Name', 'circlek-locations' ),
			'ckl_code'    => __( 'Store Code', 'circlek-locations' ),
			'ckl_country' => __( 'Country', 'circlek-locations' ),
			'ckl_region'  => __( 'Region', 'circlek-locations' ),
			'ckl_city'    => __( 'City', 'circlek-locations' ),
			'ckl_type'    => __( 'Type', 'circlek-locations' ),
			'date'        => isset( $columns['date'] ) ? $columns['date'] : __( 'Date', 'circlek-locations' ),
		);
	}

	public function admin_column_value( $column, $post_id ) {
		if ( 'ckl_code' === $column ) {
			echo esc_html( get_post_meta( $post_id, 'ckl_store_code', true ) );
			return;
		}

		if ( ! in_array( $column, array( 'ckl_country', 'ckl_region', 'ckl_city', 'ckl_type' ), true ) ) {
			return;
		}

		$classification = $this->get_location_classification( $post_id );
		$key = str_replace( 'ckl_', '', $column );
		$value = $classification[ $key ];
		if ( '' === $value ) {
			$meta_key = 'ckl_type' === $column ? 'ckl_location_type' : $column;
			$value = get_post_meta( $post_id, $meta_key, true );
		}
		if ( 'ckl_country' === $column && isset( $this->countries[ $value ] ) ) {
			$value = $this->countries[ $value ];
		} elseif ( 'ckl_region' === $column && isset( $this->regions[ $value ] ) ) {
			$value = $this->regions[ $value ];
		} elseif ( 'ckl_type' === $column && isset( $this->types[ $value ] ) ) {
			$value = $this->types[ $value ];
		}

		echo esc_html( $value );
	}

	private function seed_locations() {
		if ( get_option( 'ckl_seeded_version' ) ) {
			return;
		}

		$existing = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $existing ) {
			update_option( 'ckl_seeded_version', CKL_VERSION, false );
			return;
		}

		$this->sync_location_data();
		update_option( 'ckl_seeded_version', CKL_VERSION, false );
	}

	public function maybe_upgrade_data() {
		if ( '5' !== (string) get_option( 'ckl_location_data_version', '' ) ) {
			$this->sync_location_data( true );
		}

		if ( '5' !== (string) get_option( 'ckl_ar_data_version', '' ) ) {
			$this->backfill_arabic_fields();
		}

		if ( '1' !== (string) get_option( 'ckl_taxonomy_data_version', '' ) ) {
			$this->migrate_location_taxonomies();
		}
	}

	private function sync_location_data( $replace_existing = false ) {
		$locations = require CKL_DIR . 'data/locations.php';
		$arabic    = require CKL_DIR . 'data/locations-ar.php';
		$post_ids  = get_posts(
			array(
				'post_type'        => self::POST_TYPE,
				'post_status'      => 'any',
				'posts_per_page'   => -1,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => true,
			)
		);

		if ( $replace_existing ) {
			foreach ( $post_ids as $post_id ) {
				wp_delete_post( $post_id, true );
			}
			$post_ids = array();
		}

		$posts_by_code   = array();
		$posts_by_legacy = array();
		foreach ( $post_ids as $post_id ) {
			$code = (string) get_post_meta( $post_id, 'ckl_store_code', true );
			if ( '' !== $code ) {
				$posts_by_code[ $code ] = $post_id;
			}

			$legacy_key = (string) get_post_meta( $post_id, 'ckl_legacy_key', true );
			if ( '' === $legacy_key ) {
				$legacy_key = get_post_meta( $post_id, 'ckl_country', true ) . ':' . absint( get_post_meta( $post_id, 'ckl_store_number', true ) );
			}
			$posts_by_legacy[ $legacy_key ] = $post_id;
		}

		$active_ids   = array();
		$active_codes = array();
		foreach ( $locations as $index => $location ) {
			$post_id = 0;
			if ( isset( $posts_by_code[ $location['code'] ] ) ) {
				$post_id = $posts_by_code[ $location['code'] ];
			} elseif ( $location['legacy_key'] && isset( $posts_by_legacy[ $location['legacy_key'] ] ) ) {
				$post_id = $posts_by_legacy[ $location['legacy_key'] ];
			}

			$post_data = array(
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => $location['name'],
				'post_name'   => sanitize_title( $location['country'] . '-' . $location['code'] . '-' . $location['name'] ),
			);

			if ( $post_id ) {
				$post_data['ID'] = $post_id;
				$post_id = wp_update_post( $post_data, true );
			} else {
				$post_id = wp_insert_post( $post_data, true );
			}

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			$ar = $location['legacy_key'] && isset( $arabic[ $location['legacy_key'] ] )
				? $arabic[ $location['legacy_key'] ]
				: array( $location['name'], $location['city'], $location['address'] );

			update_post_meta( $post_id, 'ckl_store_number', $location['number'] );
			update_post_meta( $post_id, 'ckl_store_code', $location['code'] );
			update_post_meta( $post_id, 'ckl_legacy_key', $location['legacy_key'] );
			update_post_meta( $post_id, 'ckl_country', $location['country'] );
			update_post_meta( $post_id, 'ckl_region', $location['region'] );
			update_post_meta( $post_id, 'ckl_city', $location['city'] );
			update_post_meta( $post_id, 'ckl_name_ar', $ar[0] );
			update_post_meta( $post_id, 'ckl_city_ar', $ar[1] );
			update_post_meta( $post_id, 'ckl_location_type', $location['type'] );
			update_post_meta( $post_id, 'ckl_address', $location['address'] );
			update_post_meta( $post_id, 'ckl_address_ar', $ar[2] );
			update_post_meta( $post_id, 'ckl_directions_url', $location['directions_url'] );
			update_post_meta( $post_id, 'ckl_display_order', $index + 1 );
			$this->sync_location_taxonomies( $post_id, $location['country'], $location['region'], $location['city'], $location['type'] );

			$active_ids[]   = (int) $post_id;
			$active_codes[] = (string) $location['code'];
		}

		$legacy_seed_keys = array(
			'ksa:1', 'ksa:2', 'ksa:3', 'ksa:4', 'ksa:5', 'ksa:6', 'ksa:7', 'ksa:8', 'ksa:9', 'ksa:10', 'ksa:11', 'ksa:12',
			'ksa:13', 'ksa:14', 'ksa:15', 'ksa:16', 'ksa:17', 'ksa:18', 'ksa:19', 'ksa:20', 'ksa:21', 'ksa:22', 'ksa:23',
			'uae:1', 'uae:2', 'uae:3', 'uae:4', 'uae:5', 'uae:6', 'uae:7', 'uae:8', 'uae:9', 'uae:10',
		);
		$previous_codes = (array) get_option( 'ckl_managed_source_codes', array() );

		foreach ( $post_ids as $post_id ) {
			if ( in_array( (int) $post_id, $active_ids, true ) ) {
				continue;
			}

			$code = (string) get_post_meta( $post_id, 'ckl_store_code', true );
			$legacy_key = (string) get_post_meta( $post_id, 'ckl_legacy_key', true );
			if ( '' === $legacy_key ) {
				$legacy_key = get_post_meta( $post_id, 'ckl_country', true ) . ':' . absint( get_post_meta( $post_id, 'ckl_store_number', true ) );
			}

			if ( in_array( $code, $previous_codes, true ) || in_array( $legacy_key, $legacy_seed_keys, true ) ) {
				wp_update_post(
					array(
						'ID'          => $post_id,
						'post_status' => 'draft',
					)
				);
			}
		}

		update_option( 'ckl_managed_source_codes', $active_codes, false );
		update_option( 'ckl_location_data_version', '5', false );
	}

	public function migrate_location_taxonomies() {
		$post_ids = get_posts(
			array(
				'post_type'        => self::POST_TYPE,
				'post_status'      => 'any',
				'posts_per_page'   => -1,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => true,
			)
		);

		foreach ( $post_ids as $post_id ) {
			$this->sync_location_taxonomies(
				$post_id,
				get_post_meta( $post_id, 'ckl_country', true ),
				get_post_meta( $post_id, 'ckl_region', true ),
				get_post_meta( $post_id, 'ckl_city', true ),
				get_post_meta( $post_id, 'ckl_location_type', true )
			);
		}

		update_option( 'ckl_taxonomy_data_version', '1', false );
	}

	private function sync_location_taxonomies( $post_id, $country, $region, $city, $type ) {
		$country = sanitize_key( $country );
		$region  = sanitize_key( $region );
		$city    = sanitize_text_field( $city );
		$type    = sanitize_key( $type );

		if ( isset( $this->countries[ $country ], $this->regions[ $region ] ) && '' !== $city ) {
			$country_id = $this->get_or_create_term( $this->countries[ $country ], self::AREA_TAXONOMY, $country );
			$region_id  = $country_id ? $this->get_or_create_term( $this->regions[ $region ], self::AREA_TAXONOMY, $region, $country_id ) : 0;
			$city_id    = $region_id ? $this->get_or_create_term( $city, self::AREA_TAXONOMY, sanitize_title( $city ), $region_id ) : 0;

			if ( $country_id && $region_id && $city_id ) {
				wp_set_object_terms( $post_id, array( $country_id, $region_id, $city_id ), self::AREA_TAXONOMY, false );
			}
		}

		if ( isset( $this->types[ $type ] ) ) {
			$type_id = $this->get_or_create_term( $this->types[ $type ], self::TYPE_TAXONOMY, $type );
			if ( $type_id ) {
				wp_set_object_terms( $post_id, array( $type_id ), self::TYPE_TAXONOMY, false );
			}
		}
	}

	private function get_or_create_term( $name, $taxonomy, $slug, $parent = 0 ) {
		$term = term_exists( $slug, $taxonomy, $parent );
		if ( $term ) {
			return (int) ( is_array( $term ) ? $term['term_id'] : $term );
		}

		$term = wp_insert_term(
			$name,
			$taxonomy,
			array(
				'slug'   => $slug,
				'parent' => (int) $parent,
			)
		);

		return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
	}

	private function get_location_classification( $post_id ) {
		$result = array( 'country' => '', 'region' => '', 'city' => '', 'type' => '' );
		$areas  = wp_get_post_terms( $post_id, self::AREA_TAXONOMY );

		if ( ! is_wp_error( $areas ) && $areas ) {
			$by_id = array();
			foreach ( $areas as $term ) {
				$by_id[ $term->term_id ] = $term;
				if ( 0 === (int) $term->parent && isset( $this->countries[ $term->slug ] ) ) {
					$result['country'] = $term->slug;
				}
			}

			foreach ( $areas as $term ) {
				$parent = isset( $by_id[ $term->parent ] ) ? $by_id[ $term->parent ] : null;
				if ( $parent && isset( $this->countries[ $parent->slug ], $this->regions[ $term->slug ] ) ) {
					$result['region'] = $term->slug;
				}
			}

			foreach ( $areas as $term ) {
				$parent = isset( $by_id[ $term->parent ] ) ? $by_id[ $term->parent ] : null;
				if ( $parent && isset( $this->regions[ $parent->slug ] ) ) {
					$result['city'] = $term->name;
				}
			}
		}

		$type_terms = wp_get_post_terms( $post_id, self::TYPE_TAXONOMY );
		if ( ! is_wp_error( $type_terms ) ) {
			foreach ( $type_terms as $term ) {
				if ( isset( $this->types[ $term->slug ] ) ) {
					$result['type'] = $term->slug;
					break;
				}
			}
		}

		return $result;
	}

	public function backfill_arabic_fields() {
		$arabic = require CKL_DIR . 'data/locations-ar.php';
		$post_ids = get_posts(
			array(
				'post_type'        => self::POST_TYPE,
				'post_status'      => 'any',
				'posts_per_page'   => -1,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => true,
			)
		);

		foreach ( $post_ids as $post_id ) {
			$key = (string) get_post_meta( $post_id, 'ckl_legacy_key', true );
			if ( '' === $key ) {
				$key = get_post_meta( $post_id, 'ckl_country', true ) . ':' . absint( get_post_meta( $post_id, 'ckl_store_number', true ) );
			}
			if ( isset( $arabic[ $key ] ) ) {
				update_post_meta( $post_id, 'ckl_name_ar', $arabic[ $key ][0] );
				update_post_meta( $post_id, 'ckl_city_ar', $arabic[ $key ][1] );
				update_post_meta( $post_id, 'ckl_address_ar', $arabic[ $key ][2] );
			} else {
				if ( '' === (string) get_post_meta( $post_id, 'ckl_name_ar', true ) ) {
					update_post_meta( $post_id, 'ckl_name_ar', get_the_title( $post_id ) );
				}
				if ( '' === (string) get_post_meta( $post_id, 'ckl_city_ar', true ) ) {
					update_post_meta( $post_id, 'ckl_city_ar', get_post_meta( $post_id, 'ckl_city', true ) );
				}
				if ( '' === (string) get_post_meta( $post_id, 'ckl_address_ar', true ) ) {
					update_post_meta( $post_id, 'ckl_address_ar', get_post_meta( $post_id, 'ckl_address', true ) );
				}
			}
		}

		update_option( 'ckl_ar_data_version', '5', false );
	}
}
