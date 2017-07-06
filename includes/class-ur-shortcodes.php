<?php
/**
 * User Registration Shortcodes.
 *
 * @class    UR_Shortcodes
 * @version  1.4.0
 * @package  UserRegistration/Classes
 * @category Class
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * UR_Shortcodes Class
 */
class UR_Shortcodes {

	/**
	 * Init Shortcodes.
	 */
	public static function init() {


		$shortcodes = array(
			'user_registration_form'       => __CLASS__ . '::form', // change it to user_registration_form ;)
			'user_registration_my_account' => __CLASS__ . '::my_account',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function
	 * @param array    $atts (default: array())
	 * @param array    $wrapper
	 *
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class'  => 'user-registration',
			'before' => null,
			'after'  => null,
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div id="user-registration" class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

	/**
	 * My account page shortcode.
	 *
	 * @param mixed $atts
	 *
	 * @return string
	 */
	public static function my_account( $atts ) {
		return self::shortcode_wrapper( array( 'UR_Shortcode_My_Account', 'output' ), $atts );
	}

	/**
	 * User Registration form shortcode.
	 */
	public static function form( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		if ( ! isset( $atts['id'] ) ) {
			return '';
		}

		$atts = shortcode_atts( array(
			'id' => '',
		), $atts, 'user_registration_form' );

		self::render_form( $atts['id'] );
	}

	/**
	 * Output for registration form .
	 */
	private static function render_form( $form_id ) {
		$args = array(
			'post_type'   => 'user_registration',
			'post_status' => 'publish',
			'post__in'    => array( $form_id ),
		);

		$post_data = get_posts( $args );
		$form_data = '';

		if ( isset( $post_data[0] ) ) {
			$form_data = $post_data[0]->post_content;
		}

		$form_data_array = json_decode( $form_data );

		if ( gettype( $form_data_array ) != 'array' ) {
			$form_data_array = array();
		}

		$is_field_exists = false;

		ur_get_template( 'form-registration.php', array(
			'form_data_array' => $form_data_array,
			'is_field_exists' => $is_field_exists,
			'form_id'         => $form_id,
		) );

	}
}
