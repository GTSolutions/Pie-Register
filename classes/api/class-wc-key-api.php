<?php

/**
 * WooCommerce API Manager API Key Class
 *
 * @package Update API Manager/Key Handler
 * @author Todd Lahman LLC
 * @copyright   Copyright (c) 2011-2013, Todd Lahman LLC
 * @since 1.1.1
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Api_Manager_Example_Key {

	// API Key URL
	public function create_software_api_url( $args ) {
		global $piereg_api_manager;
		$api_url = add_query_arg( 'wc-api', 'am-software-api', $piereg_api_manager->upgrade_url );

		return $api_url . '&' . http_build_query( $args );
	}

	public function activate( $args, $addon = false ) {
		$platform = site_url();
		
		if($addon){
			$addon_id = get_option( 'piereg_api_manager_'.$addon['is_addon'].'_id' );
			$instance = get_option( 'piereg_api_manager_'.$addon['is_addon'].'_instance' );
		
			$defaults = array(
				'request' => 'activation',
				'product_id' => $addon_id,
				'instance' => $instance,
				'platform' => $platform,
				'is_addon' => $addon['is_addon'],
				'is_addon_version' => $addon['is_addon_version']
				);
		}else{
			$product_id = get_option( 'piereg_api_manager_product_id' );
			$instance = get_option( 'piereg_api_manager_instance' );
		
			$defaults = array(
			'request' => 'activation',
			'product_id' => $product_id,
			'instance' => $instance,
			'platform' => $platform
			);
		}
				
		$args = wp_parse_args( $defaults, $args );
		$target_url = self::create_software_api_url( $args );
		$request = wp_remote_get( $target_url );
		
		if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		// Request failed
			return false;
		}

		$response = wp_remote_retrieve_body( $request );
		
		return $response;
	}

	public function deactivate( $args, $addon = false  ) {
		// instance required
		
		$platform = site_url();
		
		if($addon){
			$addon_id = get_option( 'piereg_api_manager_'.$addon['is_addon'].'_id' );
			$instance = get_option( 'piereg_api_manager_'.$addon['is_addon'].'_instance' );
			
			$defaults = array(
				'request' => 'deactivation',
				'product_id' => $addon_id,
				'instance' => $instance,
				'platform' => $platform,
				'is_addon' => $addon['is_addon'],
				'is_addon_version' => $addon['is_addon_version']
				);
		}else{
			$product_id = get_option( 'piereg_api_manager_product_id' );
			$instance = get_option( 'piereg_api_manager_instance' );
		
			$defaults = array(
				'request' => 'deactivation',
				'product_id' => $product_id,
				'instance' => $instance,
				'platform' => $platform
				);
		}
		$instance = get_option( 'piereg_api_manager_instance' );
		$args = wp_parse_args( $defaults, $args );
		
		$target_url = self::create_software_api_url( $args );
		
		$request = wp_remote_get( $target_url );
				
		if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		// Request failed
			return false;
		}
		
		$response = wp_remote_retrieve_body( $request );
		
		return $response;
	}

	public function check( $args ) {

		$product_id = get_option( 'piereg_api_manager_product_id' );

		$defaults = array(
			'request'     => 'check',
			'product_id' => $product_id,
			);

		$args = wp_parse_args( $defaults, $args );

		$target_url = self::create_software_api_url( $args );

		$request = wp_remote_get( $target_url );

		if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		// Request failed
			return false;
		}

		$response = wp_remote_retrieve_body( $request );

		return $response;
	}

}

// Class is instantiated as an object by other classes on-demand