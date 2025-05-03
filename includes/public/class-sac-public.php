<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class SAC_Public {

	public function __construct() {
		add_action( 'sac_service_availability_saved', array( $this, 'handle_service_availability_saved' ), 10, 3 );
		add_shortcode( 'service_availability', array( $this, 'service_availability_shortcode' ) );
		add_shortcode( 'service_price', array( $this, 'service_price_shortcode' ) );
	}

	public function handle_service_availability_saved( $post_id, $is_available, $availability_date ) {
		// No need to create a new post. Just update the existing post meta.
		// The shortcode function will fetch the post meta.
	}

	public function service_availability_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'service_availability'
		);

		$post_id = intval( $atts['id'] );

		if ( ! $post_id ) {
			return esc_html__( 'Invalid Service Availability ID', 'service-availability-calendar' );
		}

		$is_available    = get_post_meta( $post_id, '_sac_is_available', true );
		$availability_date = get_post_meta( $post_id, '_sac_availability_date', true );

		if ( 'yes' === $is_available && $availability_date ) {
			$formatted_date = date( 'F j, Y', strtotime( $availability_date ) );
			return sprintf( __( 'Available from %s', 'service-availability-calendar' ), $formatted_date );
		} else {
			return esc_html__( 'Available Now', 'service-availability-calendar' );
		}
	}


	public function service_price_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'service_price'
		);

		$post_id = intval( $atts['id'] );
		$price = get_post_meta( $post_id, '_sac_property_price', true );

		if ( $price ) {
			return '$' . number_format( $price) . '/mo';
		}

		return esc_html__( 'TBD', 'service-availability-calendar' );
	}

}