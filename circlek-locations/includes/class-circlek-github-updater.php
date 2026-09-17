<?php
/**
 * GitHub Releases update integration for the Circle K Locations plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CircleK_GitHub_Updater {
	const REPOSITORY    = 'pmunankarmi/circlek';
	const RELEASE_API   = 'https://api.github.com/repos/' . self::REPOSITORY . '/releases/latest';
	const RELEASE_ASSET = 'circlek-locations.zip';
	const CACHE_KEY     = 'ckl_github_release';

	private $plugin_file;
	private $plugin_basename;
	private $current_version;

	public function __construct( $plugin_file, $current_version ) {
		$this->plugin_file      = $plugin_file;
		$this->plugin_basename  = plugin_basename( $plugin_file );
		$this->current_version  = $current_version;

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_information' ), 20, 3 );
		add_action( 'upgrader_process_complete', array( $this, 'clear_cache' ), 10, 2 );
	}

	public function check_for_update( $transient ) {
		if ( ! is_object( $transient ) || empty( $transient->checked ) ) {
			return $transient;
		}

		$release = $this->get_release();
		if ( ! $release || version_compare( $release['version'], $this->current_version, '<=' ) ) {
			return $transient;
		}

		$transient->response[ $this->plugin_basename ] = (object) array(
			'id'           => 'github.com/' . self::REPOSITORY,
			'slug'         => 'circlek-locations',
			'plugin'       => $this->plugin_basename,
			'new_version'  => $release['version'],
			'url'          => $release['html_url'],
			'package'      => $release['package'],
			'tested'       => '7.1',
			'requires_php' => '7.4',
			'icons'        => array(),
			'banners'      => array(),
			'banners_rtl'  => array(),
		);

		return $transient;
	}

	public function plugin_information( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || empty( $args->slug ) || 'circlek-locations' !== $args->slug ) {
			return $result;
		}

		$release = $this->get_release();
		if ( ! $release ) {
			return $result;
		}

		return (object) array(
			'name'          => 'Circle K Locations',
			'slug'          => 'circlek-locations',
			'version'       => $release['version'],
			'author'        => '<a href="https://github.com/pmunankarmi">Circle K</a>',
			'homepage'      => 'https://github.com/' . self::REPOSITORY,
			'requires'      => '6.0',
			'tested'        => '7.1',
			'requires_php'  => '7.4',
			'download_link' => $release['package'],
			'last_updated'  => $release['published_at'],
			'sections'      => array(
				'description' => 'Dynamic, searchable Circle K store directory with editable location fields and Arabic support.',
				'changelog'   => $release['body'] ? wp_kses_post( nl2br( $release['body'] ) ) : 'See the GitHub release notes for details.',
			),
		);
	}

	public function clear_cache( $upgrader, $options ) {
		if ( ! empty( $options['type'] ) && 'plugin' === $options['type'] ) {
			delete_site_transient( self::CACHE_KEY );
		}
	}

	private function get_release() {
		$cached = get_site_transient( self::CACHE_KEY );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$response = wp_remote_get(
			self::RELEASE_API,
			array(
				'timeout' => 12,
				'headers' => array(
					'Accept'     => 'application/vnd.github+json',
					'User-Agent' => 'Circle-K-Locations/' . $this->current_version,
				),
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $data ) || empty( $data['tag_name'] ) || empty( $data['html_url'] ) ) {
			return false;
		}

		$package = '';
		foreach ( (array) $data['assets'] as $asset ) {
			if ( isset( $asset['name'], $asset['browser_download_url'] ) && self::RELEASE_ASSET === $asset['name'] ) {
				$package = esc_url_raw( $asset['browser_download_url'] );
				break;
			}
		}

		if ( ! $package ) {
			return false;
		}

		$release = array(
			'version'      => preg_replace( '/^v/i', '', sanitize_text_field( $data['tag_name'] ) ),
			'html_url'     => esc_url_raw( $data['html_url'] ),
			'package'      => $package,
			'published_at' => isset( $data['published_at'] ) ? sanitize_text_field( $data['published_at'] ) : '',
			'body'         => isset( $data['body'] ) ? sanitize_textarea_field( $data['body'] ) : '',
		);

		set_site_transient( self::CACHE_KEY, $release, HOUR_IN_SECONDS );
		return $release;
	}
}
