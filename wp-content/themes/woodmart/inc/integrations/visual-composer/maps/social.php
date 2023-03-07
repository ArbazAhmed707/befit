<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );
/**
* ------------------------------------------------------------------------------------------------
* Social buttons element map
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'woodmart_vc_shortcode_social' ) ) {
	function woodmart_vc_shortcode_social() {
		if ( ! shortcode_exists( 'social_buttons' ) ) {
			return;
		}

		vc_map( woodmart_get_social_buttons_shortcode_args() );
	}
	add_action( 'vc_before_init', 'woodmart_vc_shortcode_social' );
}

if( ! function_exists( 'woodmart_get_social_buttons_shortcode_args' ) ) {
	function woodmart_get_social_buttons_shortcode_args() {
		return array(
			'name' => esc_html__( 'Social buttons', 'woodmart' ),
			'base' => 'social_buttons',
			'category' => function_exists( 'woodmart_get_tab_title_category_for_wpb' ) ? woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ) : esc_html__( 'Theme elements', 'woodmart' ),
			'description' => esc_html__( 'Follow or share buttons', 'woodmart' ),
        	'icon' => WOODMART_ASSETS . '/images/vc-icon/social-buttons.svg',
			'params' => woodmart_get_social_shortcode_params()
		);
	}
}

if( ! function_exists( 'woodmart_get_social_shortcode_params' ) ) {
	function woodmart_get_social_shortcode_params() {
		$typography = array();

		if ( function_exists( 'woodmart_get_typography_map' ) ) {
			$typography = woodmart_get_typography_map(
				array(
					'key'        => 'label',
					'selector'   => '{{WRAPPER}} .wd-label',
					'dependency' => array(
						'element' => 'show_label',
						'value'   => 'yes',
					),
					'group'      => esc_html__( 'Style', 'js_composer' ),
				)
			);
		}

		return apply_filters( 'woodmart_get_social_shortcode_params', array(
			/**
			* Type
			*/
			array(
				'type' => 'woodmart_title_divider',
				'holder' => 'div',
				'title' => esc_html__( 'General', 'woodmart' ),
				'param_name' => 'type_divider'
			),

			array(
				'type'       => 'woodmart_css_id',
				'param_name' => 'woodmart_css_id',
			),

			array(
				'heading'          => esc_html__( 'Label', 'woodmart' ),
				'type'             => 'woodmart_switch',
				'param_name'       => 'show_label',
				'true_state'       => 'yes',
				'false_state'      => 'no',
				'default'          => 'no',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

			array(
				'heading'          => esc_html__( 'Label text', 'woodmart' ),
				'type'             => 'textfield',
				'param_name'       => 'label_text',
				'value'            => esc_html__( 'Share: ', 'woodmart' ),
				'dependency'       => array(
					'element' => 'show_label',
					'value'   => 'yes',
				),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),

			array(
				'type' => 'woodmart_button_set',
				'heading' => esc_html__( 'Buttons type', 'woodmart' ),
				'param_name' => 'type',
				'value' => array(
					esc_html__( 'Share', 'woodmart' ) => 'share',
					esc_html__( 'Follow', 'woodmart' ) => 'follow'
				),
			),

			/**
			* Extra
			*/
			array(
				'type' => 'woodmart_title_divider',
				'holder' => 'div',
				'title' => esc_html__( 'Extra options', 'woodmart' ),
				'param_name' => 'extra_divider'
			),
			( function_exists( 'vc_map_add_css_animation' ) ) ? vc_map_add_css_animation( true ) : '',
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Extra class name', 'woodmart' ),
				'param_name' => 'el_class',
				'hint' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' )
			),

			/**
			 * Style tab.
			 */
			// General.
			array(
				'type'       => 'woodmart_title_divider',
				'holder'     => 'div',
				'title'      => esc_html__( 'General', 'woodmart' ),
				'param_name' => 'style_general_divider',
				'group'      => esc_html__( 'Style', 'js_composer' ),
			),
			array(
				'heading'          => esc_html__( 'Layout', 'woodmart' ),
				'type'             => 'dropdown',
				'param_name'       => 'layout',
				'value'            => array(
					esc_html__( 'Default', 'woodmart' ) => 'default',
					esc_html__( 'Inline', 'woodmart' )  => 'inline',
					esc_html__( 'Justify', 'woodmart' ) => 'justify',
				),
				'dependency'       => array(
					'element' => 'show_label',
					'value'   => 'yes',
				),
				'group'            => esc_html__( 'Style', 'js_composer' ),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),
			array(
				'type'             => 'woodmart_image_select',
				'heading'          => esc_html__( 'Align ', 'woodmart' ),
				'param_name'       => 'align',
				'value'            => array(
					esc_html__( 'Left', 'woodmart' )   => 'left',
					esc_html__( 'Center', 'woodmart' ) => 'center',
					esc_html__( 'Right', 'woodmart' )  => 'right',
				),
				'images_value'     => array(
					'center' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
					'left'   => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
					'right'  => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
				),
				'std'              => 'center',
				'wood_tooltip'     => true,
				'group'            => esc_html__( 'Style', 'js_composer' ),
				'edit_field_class' => 'vc_col-sm-6 vc_column title-align',
			),
			// Icons.
			array(
				'type'       => 'woodmart_title_divider',
				'holder'     => 'div',
				'title'      => esc_html__( 'Icons', 'woodmart' ),
				'param_name' => 'style_icons_divider',
				'group'      => esc_html__( 'Style', 'js_composer' ),
			),

			array(
				'type'             => 'dropdown',
				'heading'          => esc_html__( 'Buttons size', 'woodmart' ),
				'param_name'       => 'size',
				'value'            => array(
					esc_html__( 'Default (18px)', 'woodmart' ) => '',
					esc_html__( 'Small (14px)', 'woodmart' ) => 'small',
					esc_html__( 'Large (22px)', 'woodmart' ) => 'large',
				),
				'group'            => esc_html__( 'Style', 'js_composer' ),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),
			array(
				'type'             => 'woodmart_button_set',
				'heading'          => esc_html__( 'Color', 'woodmart' ),
				'param_name'       => 'color',
				'value'            => array(
					esc_html__( 'Dark', 'woodmart' )  => 'dark',
					esc_html__( 'Light', 'woodmart' ) => 'light',
				),
				'group'            => esc_html__( 'Style', 'js_composer' ),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),
			array(
				'type'             => 'woodmart_image_select',
				'heading'          => esc_html__( 'Button style', 'woodmart' ),
				'param_name'       => 'style',
				'value'            => array(
					esc_html__( 'Default', 'woodmart' )  => '',
					esc_html__( 'Simple', 'woodmart' )   => 'simple',
					esc_html__( 'Colored', 'woodmart' )  => 'colored',
					esc_html__( 'Colored alternative', 'woodmart' ) => 'colored-alt',
					esc_html__( 'Bordered', 'woodmart' ) => 'bordered',
				),
				'images_value'     => array(
					''            => WOODMART_ASSETS_IMAGES . '/settings/social-buttons/style/default.png',
					'simple'      => WOODMART_ASSETS_IMAGES . '/settings/social-buttons/style/simple.png',
					'colored'     => WOODMART_ASSETS_IMAGES . '/settings/social-buttons/style/colored.png',
					'colored-alt' => WOODMART_ASSETS_IMAGES . '/settings/social-buttons/style/colored-alt.png',
					'bordered'    => WOODMART_ASSETS_IMAGES . '/settings/social-buttons/style/bordered.png',
				),
				'wood_tooltip'     => true,
				'std'              => '',
				'group'            => esc_html__( 'Style', 'js_composer' ),
				'edit_field_class' => 'vc_col-xs-12 vc_column social-style',
			),
			array(
				'type'             => 'woodmart_image_select',
				'heading'          => esc_html__( 'Button form', 'woodmart' ),
				'param_name'       => 'form',
				'value'            => array(
					esc_html__( 'Circle', 'woodmart' ) => 'circle',
					esc_html__( 'Square', 'woodmart' ) => 'square',
				),
				'images_value'     => array(
					'circle' => WOODMART_ASSETS_IMAGES . '/settings/social-buttons/shape/circle.png',
					'square' => WOODMART_ASSETS_IMAGES . '/settings/social-buttons/shape/square.png',
				),
				'wood_tooltip'     => true,
				'std'              => 'circle',
				'group'            => esc_html__( 'Style', 'js_composer' ),
				'edit_field_class' => 'vc_col-xs-12 vc_column social-form',
			),
			// Label.
			array(
				'type'       => 'woodmart_title_divider',
				'holder'     => 'div',
				'title'      => esc_html__( 'Label', 'woodmart' ),
				'param_name' => 'style_label_divider',
				'dependency' => array(
					'element' => 'show_label',
					'value'   => 'yes',
				),
				'group'      => esc_html__( 'Style', 'js_composer' ),
			),
			function_exists( 'woodmart_get_typography_map' ) ? $typography['font_family'] : '',
			function_exists( 'woodmart_get_typography_map' ) ? $typography['font_size'] : '',
			function_exists( 'woodmart_get_typography_map' ) ? $typography['font_weight'] : '',
			function_exists( 'woodmart_get_typography_map' ) ? $typography['text_transform'] : '',
			function_exists( 'woodmart_get_typography_map' ) ? $typography['font_style'] : '',
			function_exists( 'woodmart_get_typography_map' ) ? $typography['line_height'] : '',
			array(
				'heading'          => esc_html__( 'Label color', 'woodmart' ),
				'type'             => 'wd_colorpicker',
				'param_name'       => 'label_color',
				'selectors'        => array(
					'{{WRAPPER}} .wd-label' => array(
						'color: {{VALUE}};',
					),
				),
				'dependency'       => array(
					'element' => 'show_label',
					'value'   => 'yes',
				),
				'group'            => esc_html__( 'Style', 'js_composer' ),
				'edit_field_class' => 'vc_col-sm-6 vc_column',
			),
			/**
			 * Design Options tab.
			 */
			array(
				'type'       => 'css_editor',
				'heading'    => esc_html__( 'CSS box', 'woodmart' ),
				'param_name' => 'css',
				'group'      => esc_html__( 'Design Options', 'js_composer' ),
			),
			function_exists( 'woodmart_get_vc_responsive_spacing_map' ) ? woodmart_get_vc_responsive_spacing_map() : '',

			/**
			 * Advanced tab.
			 */

			// Width option (with dependency Columns option, responsive).
			woodmart_get_responsive_dependency_width_map( 'responsive_tabs' ),
			woodmart_get_responsive_dependency_width_map( 'width_desktop' ),
			woodmart_get_responsive_dependency_width_map( 'custom_width_desktop' ),
			woodmart_get_responsive_dependency_width_map( 'width_tablet' ),
			woodmart_get_responsive_dependency_width_map( 'custom_width_tablet' ),
			woodmart_get_responsive_dependency_width_map( 'width_mobile' ),
			woodmart_get_responsive_dependency_width_map( 'custom_width_mobile' ),
		) );
	}
}
