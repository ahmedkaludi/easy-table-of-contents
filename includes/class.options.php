<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC_Option' ) ) {

	/**
	 * Class ezTOC_Option
	 */
	final class ezTOC_Option {

		public static function register() {

			if ( FALSE === get_option( 'ez-toc-settings' ) ) {

				add_option( 'ez-toc-settings', self::getDefaults() );
			}

			foreach ( self::getRegistered() as $section => $settings ) {

				add_settings_section(
					'ez_toc_settings_' . $section,
					__return_null(),
					'__return_false',
					'ez_toc_settings_' . $section
				);

				foreach ( $settings as $option ) {

					$name = isset( $option['name'] ) ? $option['name'] : '';

					add_settings_field(
						'ez-toc-settings[' . $option['id'] . ']',
						$name,
						method_exists( __CLASS__, $option['type'] ) ? array( __CLASS__, $option['type'] ) : array( __CLASS__, 'missingCallback' ),
						'ez_toc_settings_' . $section,
						'ez_toc_settings_' . $section,
						array(
							'section'     => $section,
							'id'          => isset( $option['id'] ) ? $option['id'] : NULL,
							'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
							'name'        => isset( $option['name'] ) ? $option['name'] : NULL,
							'size'        => isset( $option['size'] ) ? $option['size'] : NULL,
							'options'     => isset( $option['options'] ) ? $option['options'] : '',
							'default'     => isset( $option['default'] ) ? $option['default'] : '',
							'min'         => isset( $option['min'] ) ? $option['min'] : NULL,
							'max'         => isset( $option['max'] ) ? $option['max'] : NULL,
							'step'        => isset( $option['step'] ) ? $option['step'] : NULL,
							'chosen'      => isset( $option['chosen'] ) ? $option['chosen'] : NULL,
							'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : NULL,
							'allow_blank' => isset( $option['allow_blank'] ) ? $option['allow_blank'] : TRUE,
							'readonly'    => isset( $option['readonly'] ) ? $option['readonly'] : FALSE,
							'faux'        => isset( $option['faux'] ) ? $option['faux'] : FALSE,
						)
					);
				}

			}

			// Creates our settings in the options table
			register_setting( 'ez-toc-settings', 'ez-toc-settings', array( __CLASS__, 'sanitize' ) );
		}

		/**
		 * Settings Sanitization
		 *
		 * Adds a settings error (for the updated message)
		 * At some point this will validate input
		 *
		 * @since 1.0
		 *
		 * @param array $input The value inputted in the field
		 *
		 * @return string $input Sanitized value
		 */
		public static function sanitize( $input = array() ) {

			$options = self::getOptions();

			if ( empty( $_POST['_wp_http_referer'] ) ) {

				return $input;
			}

			$registered = self::getRegistered();

			foreach ( $registered as $sectionID => $sectionOptions ) {

				$input = $input ? $input : array();
				$input = apply_filters( 'ez_toc_settings_' . $sectionID . '_sanitize', $input );

				// Loop through each setting being saved and pass it through a sanitization filter
				foreach ( $input as $key => $value ) {

					// Get the setting type (checkbox, select, etc)
					$type = isset( $registered[ $sectionID ][ $key ]['type'] ) ? $registered[ $sectionID ][ $key ]['type'] : FALSE;

					if ( $type ) {

						// Field type specific filter
						$input[ $key ] = apply_filters( 'ez_toc_settings_sanitize_' . $type, $value, $key );
					}

					// General filter
					$input[ $key ] = apply_filters( 'ez_toc_settings_sanitize', $input[ $key ], $key );
				}

				// Loop through the registered options.
				foreach ( $sectionOptions as $optionID => $optionProperties ) {

					// Unset any that are empty for the section being saved.
					if ( empty( $input[ $optionID ] ) ) {

						unset( $options[ $optionID ] );
					}

					// Check for the checkbox option type.
					if ( array_key_exists( 'type', $optionProperties ) && 'checkbox' == $optionProperties['type'] ) {

						// If it does not exist in the options values being saved, add the option ID and set its value to `0`.
						// This matches WP core behavior for saving checkbox option values.
						if ( ! array_key_exists( $optionID, $input ) ) {

							$input[ $optionID ] = '0';
						}
					}
				}

			}

			// Merge our new settings with the existing
			$output = array_merge( $options, $input );

			return $output;
		}

		private static function getRegistered() {

			$options = array(
				'general' => apply_filters(
					'ez_toc_settings_general',
					array(
						'enabled_post_types' => array(
							'id' => 'enabled_post_types',
							'name' => __( 'Enable Support', 'ez_toc' ),
							'desc' => __( 'Select the post types to enable the support for table of contents.', 'ez_toc' ),
							'type' => 'checkboxgroup',
							'options' => self::getPostTypes(),
							'default' => array(),
						),
						'auto_insert_post_types' => array(
							'id' => 'auto_insert_post_types',
							'name' => __( 'Auto Insert', 'ez_toc' ),
							'desc' => __( 'Select the post types which will have the table of contents automatically inserted.', 'ez_toc' ),
							'type' => 'checkboxgroup',
							'options' => self::getPostTypes(),
							'default' => array(),
						),
						'position' => array(
							'id' => 'position',
							'name' => __( 'Position', 'ez_toc' ),
							'desc' => __( 'Choose where where you want to display the table of contents.', 'ez_toc' ),
							'type' => 'select',
							'options' => array(
								'before' => __( 'Before first heading (default)', 'ez_toc' ),
								'after' => __( 'After first heading', 'ez_toc' ),
								'top' => __( 'Top', 'ez_toc' ),
								'bottom' => __( 'Bottom', 'ez_toc' ),
							),
							'default' => 1,
						),
						'start' => array(
							'id' => 'start',
							'name' => __( 'Show when', 'ez_toc' ),
							'desc' => __( 'or more headings are present', 'ez_toc' ),
							'type' => 'select',
							'options' => array_combine( range( 2, 10 ), range( 2, 10 ) ),
							'default' => 4,
						),
						'show_heading_text' => array(
							'id' => 'show_heading_text',
							'name' => __( 'Display Header Label', 'ez_toc' ),
							'desc' => __( 'Show header text above the table of contents.', 'ez_toc' ),
							'type' => 'checkbox',
							'default' => TRUE,
						),
						'heading_text' => array(
							'id' => 'heading_text',
							'name' => __( 'Header Label', 'ez_toc' ),
							'desc' => __( 'Eg: Contents, Table of Contents, Page Contents', 'ez_toc' ),
							'type' => 'text',
							'default' => __( 'Contents', 'ez_toc' ),
						),
						'visibility' => array(
							'id' => 'visibility',
							'name' => __( 'Toggle View', 'ez_toc' ),
							'desc' => __( 'Allow the user to toggle the visibility of the table of contents.', 'connection_toc' ),
							'type' => 'checkbox',
							'default' => TRUE,
						),
						//'visibility_show' => array(
						//	'id' => 'visibility_show',
						//	'name' => __( 'Show Label', 'ez_toc' ),
						//	'desc' => __( 'Eg: show', 'ez_toc' ),
						//	'type' => 'text',
						//	'default' => __( 'show', 'ez_toc' ),
						//),
						//'visibility_hide' => array(
						//	'id' => 'visibility_hide',
						//	'name' => __( 'Hide Label', 'ez_toc' ),
						//	'desc' => __( 'Eg: hide', 'ez_toc' ),
						//	'type' => 'text',
						//	'default' => __( 'hide', 'ez_toc' ),
						//),
						'visibility_hide_by_default' => array(
							'id' => 'visibility_hide_by_default',
							'name' => __( 'Initial View', 'ez_toc' ),
							'desc' => __( 'Initially hide the table of contents.', 'connection_toc' ),
							'type' => 'checkbox',
							'default' => FALSE,
						),
						'show_hierarchy' => array(
							'id' => 'show_hierarchy',
							'name' => __( 'Show as Hierarchy', 'ez_toc' ),
							'desc' => '',
							'type' => 'checkbox',
							'default' => TRUE,
						),
						'counter' => array(
							'id' => 'counter',
							'name' => __( 'Counter', 'ez_toc' ),
							'desc' => '',
							'type' => 'select',
							'options' => array(
								'decimal' => __( 'Decimal (default)', 'ez_toc' ),
								'numeric' => __( 'Numeric', 'ez_toc' ),
								'roman' => __( 'Roman', 'ez_toc' ),
								'none' => __( 'None', 'ez_toc' ),
							),
							'default' => 'decimal',
						),
						'smooth_scroll' => array(
							'id' => 'smooth_scroll',
							'name' => __( 'Smooth Scroll', 'ez_toc' ),
							'desc' => '',
							'type' => 'checkbox',
							'default' => TRUE,
						),
					)
				),
				'appearance' => apply_filters(
					'ez_toc_settings_appearance',
					array(
						'width' => array(
							'id' => 'width',
							'name' => __( 'Width', 'ez_toc' ),
							'desc' => '',
							'type' => 'selectgroup',
							'options' => array(
								'fixed' => array(
									'name' => __( 'Fixed', 'ez_toc' ),
									'options' => array(
										'200px' => '200px',
										'225px' => '225px',
										'250px' => '250px',
										'275px' => '275px',
										'300px' => '300px',
										'325px' => '325px',
										'350px' => '350px',
										'375px' => '375px',
										'400px' => '400px',
									),
								),
								'relative' => array(
									'name' => __( 'Relative', 'ez_toc' ),
									'options' => array(
										'auto' => 'Auto',
										'25%' => '25%',
										'33%' => '33%',
										'50%' => '50%',
										'66%' => '66%',
										'75%' => '75%',
										'100%' => '100%',
									),
								),
								'other' => array(
									'name' => __( 'Custom', 'ez_toc' ),
									'options' => array(
										'custom' => __( 'User Defined', 'ez_toc' ),
									),
								),
							),
							'default' => 'auto',
						),
						'width_custom' => array(
							'id' => 'width_custom',
							'name' => __( 'Custom Width', 'ez_toc' ),
							'desc' => __( 'Select the User Defined option from the Width option to utilitze the custom width.', 'ez_toc' ),
							'type' => 'custom_width',
							'default' => 275,
						),
						'wrapping' => array(
							'id' => 'wrapping',
							'name' => __( 'Float', 'ez_toc' ),
							'desc' => '',
							'type' => 'select',
							'options' => array(
								'none' => __( 'None (Default)', 'ez_toc' ),
								'left' => __( 'Left', 'ez_toc' ),
								'right' => __( 'Right', 'ez_toc' ),
							),
							'default' => 'none',
						),
						'font_size' => array(
							'id' => 'font_size',
							'name' => __( 'Font Size', 'ez_toc' ),
							'desc' => '',
							'type' => 'font_size',
							'default' => 95,
						),
						'theme' => array(
							'id' => 'theme',
							'name' => __( 'Theme', 'ez_toc' ),
							'desc' => __( 'The theme is only applied to the table of contents which is auto inserted into the post. The Table of Contents widget will inherit the theme widget styles.', 'ez_toc' ),
							'type' => 'radio',
							'options' => array(
								'grey' => __( 'Grey', 'ez_toc' ),
								'light-blue' => __( 'Light Blue', 'ez_toc' ),
								'white' => __( 'White', 'ez_toc' ),
								'black' => __( 'Black', 'ez_toc' ),
								'transparent' => __( 'Transparent', 'ez_toc' ),
								'custom' => __( 'Custom', 'ez_toc' ),
							),
							'default' => 'grey',
						),
						'custom_theme_header' => array(
							'id' => 'custom_theme_header',
							'name' => '<strong>' . __( 'Custom Theme', 'ez_toc' ) . '</strong>',
							'desc' => __( 'For the following settings to apply, select the Custom Theme option.', 'ez_toc' ),
							'type' => 'header',
						),
						'custom_background_colour' => array(
							'id' => 'custom_background_colour',
							'name' => __( 'Background Color', 'ez_toc' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#fff',
						),
						'custom_border_colour' => array(
							'id' => 'custom_border_colour',
							'name' => __( 'Border Color', 'ez_toc' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#ddd',
						),
						'custom_title_colour' => array(
							'id' => 'custom_title_colour',
							'name' => __( 'Title Color', 'ez_toc' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#999',
						),
						'custom_link_colour' => array(
							'id' => 'custom_link_colour',
							'name' => __( 'Link Color', 'ez_toc' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#428bca',
						),
						'custom_link_hover_colour' => array(
							'id' => 'custom_link_hover_colour',
							'name' => __( 'Link Hover Color', 'ez_toc' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#2a6496',
						),
						'custom_link_visited_colour' => array(
							'id' => 'custom_link_visited_colour',
							'name' => __( 'Link Visited Color', 'ez_toc' ),
							'desc' => '',
							'type' => 'color',
							'default' => '#428bca',
						),
					)
				),
				'advanced' => apply_filters(
					'ez_toc_settings_advanced',
					array(
						'lowercase' => array(
							'id' => 'lowercase',
							'name' => __( 'Lowercase', 'ez_toc' ),
							'desc' => __( 'Ensure anchors are in lowercase.', 'ez_toc' ),
							'type' => 'checkbox',
							'default' => FALSE,
						),
						'hyphenate' => array(
							'id' => 'hyphenate',
							'name' => __( 'Hyphenate', 'ez_toc' ),
							'desc' => __( 'Use - rather than _ in anchors.', 'ez_toc' ),
							'type' => 'checkbox',
							'default' => FALSE,
						),
						'include_homepage' => array(
							'id' => 'include_homepage',
							'name' => __( 'Homepage', 'ez_toc' ),
							'desc' => __( 'Show the table of contents for qualifying items on the homepage.', 'ez_toc' ),
							'type' => 'checkbox',
							'default' => FALSE,
						),
						'exclude_css' => array(
							'id' => 'exclude_css',
							'name' => __( 'CSS', 'ez_toc' ),
							'desc' => __( "Prevent the loading the core CSS styles. When selected, the appearance options from above will be ignored.", 'ez_toc' ),
							'type' => 'checkbox',
							'default' => FALSE,
						),
						//'bullet_spacing' => array(
						//	'id' => 'bullet_spacing',
						//	'name' => __( 'Theme Bullets', 'ez_toc' ),
						//	'desc' => __( 'If your theme includes background images for unordered list elements, enable this option to support them.', 'ez_toc' ),
						//	'type' => 'checkbox',
						//	'default' => FALSE,
						//),
						'heading_levels' => array(
							'id' => 'heading_levels',
							'name' => __( 'Headings:', 'ez_toc' ),
							'desc' => __( 'Select the heading to consider when generating the table of contents. Deselecting a heading will exclude it.', 'ez_toc' ),
							'type' => 'checkboxgroup',
							'options' => array(
								'1' => __( 'Heading 1 (h1)', 'ez_toc' ),
								'2' => __( 'Heading 2 (h2)', 'ez_toc' ),
								'3' => __( 'Heading 3 (h3)', 'ez_toc' ),
								'4' => __( 'Heading 4 (h4)', 'ez_toc' ),
								'5' => __( 'Heading 5 (h5)', 'ez_toc' ),
								'6' => __( 'Heading 6 (h6)', 'ez_toc' ),
							),
							'default' => array( '1', '2', '3', '4', '5', '6' ),
						),
						'exclude' => array(
							'id' => 'exclude',
							'name' => __( 'Exclude Headings', 'ez_toc' ),
							'desc' => __( 'Specify headings to be excluded from appearing in the table of contents. Separate multiple headings with a pipe <code>|</code>. Use an asterisk <code>*</code> as a wildcard to match other text.', 'ez_toc' ),
							'type' => 'text',
							'size' => 'large',
							'default' => '',
						),
						'exclude_desc' => array(
							'id' => 'exclude_desc',
							'name' => '',
							'desc' => '<p><strong>' . __( 'Examples:', 'ez_toc' ) . '</strong></p>' .
							          '<ul>' .
							          '<li>' . __( '<code>Fruit*</code> Ignore headings starting with "Fruit".', 'ez_toc' ) . '</li>' .
							          '<li>' . __( '<code>*Fruit Diet*</code> Ignore headings with "Fruit Diet" somewhere in the heading.', 'ez_toc' ) . '</li>' .
							          '<li>' . __( '<code>Apple Tree|Oranges|Yellow Bananas</code> Ignore headings that are exactly "Apple Tree", "Oranges" or "Yellow Bananas".', 'ez_toc' ) . '</li>' .
							          '</ul>' .
							          '<p>' . __( '<strong>Note:</strong> This is not case sensitive.', 'ez_toc' ) . '</p>',
							'type' => 'descriptive_text',
						),
						'smooth_scroll_offset' => array(
							'id' => 'smooth_scroll_offset',
							'name' => __( 'Smooth Scroll Offset', 'ez_toc' ),
							'desc' => 'px<br/>' . __( 'If you have a consistent menu across the top of your site, you can adjust the top offset to stop the headings from appearing underneath the top menu. A setting of 30 accommodates the WordPress admin bar. This setting only has an effect after you have enabled Smooth Scroll option.', 'ez_toc' ),
							'type' => 'number',
							'size' => 'small',
							'default' => 30
						),
						'restrict_path' => array(
							'id' => 'restrict_path',
							'name' => __( 'Limit Path', 'ez_toc' ),
							'desc' => '<br/>' . __( 'Restrict generation of the table of contents to pages that match the required path. This path is from the root of your site and always begins with a forward slash.', 'ez_toc' ) .
							          '<br/><span class="description">' . __( 'Eg: /wiki/, /corporate/annual-reports/', 'ez_toc' ) . '</span>',
							'type' => 'text',
						),
						'fragment_prefix' => array(
							'id' => 'fragment_prefix',
							'name' => __( 'Default Anchor Prefix', 'ez_toc' ),
							'desc' => '<br/>' . __( 'Anchor targets are restricted to alphanumeric characters as per HTML specification (see readme for more detail). The default anchor prefix will be used when no characters qualify. When left blank, a number will be used instead.', 'ez_toc' ) .
							          '<br/>' . __( 'This option normally applies to content written in character sets other than ASCII.', 'ez_toc' ) .
							          '<br/><span class="description">' . __( 'Eg: i, toc_index, index, _', 'ez_toc' ) . '</span>',
							'type' => 'text',
							'default' => 'i',
						),
						'widget_affix_selector' => array(
							'id' => 'widget_affix_selector',
							'name' => __( 'Widget Affix Selector', 'ez_toc' ),
							'desc' => '<br/>' . __( 'To enable the option to affix or pin the Table of Contents widget enter the theme\'s sidebar class or id.', 'ez_toc' ) .
							          '<br/>' . __( 'Since every theme is different, this can not be determined automatically. If you are unsure how to find the sidebar\'s class or id, please ask the theme\'s support persons.', 'ez_toc' ) .
							          '<br/><span class="description">' . __( 'Eg: .widget-area or #sidebar', 'ez_toc' ) . '</span>',
							'type' => 'text',
							'default' => '',
						),
					)
				),
			);

			return apply_filters( 'ez_toc_registered_settings', $options );
		}

		private static function getDefaults() {

			$defaults = array(
				'fragment_prefix'                    => 'i',
				'position'                           => 'before',
				'start'                              => 4,
				'show_heading_text'                  => TRUE,
				'heading_text'                       => 'Contents',
				'enabled_post_types'                 => array( 'page' ),
				'auto_insert_post_types'             => array(),
				'show_hierarchy'                     => TRUE,
				'counter'                            => 'decimal',
				'smooth_scroll'                      => TRUE,
				'smooth_scroll_offset'               => 30,
				'visibility'                         => TRUE,
				//'visibility_show'                    => 'show',
				//'visibility_hide'                    => 'hide',
				'visibility_hide_by_default'         => FALSE,
				'width'                              => 'auto',
				'width_custom'                       => 275,
				'width_custom_units'                 => 'px',
				'wrapping'                           => 'none',
				'font_size'                          => 95,
				'font_size_units'                    => '%',
				'theme'                              => 'grey',
				'custom_background_colour'           => '#fff',
				'custom_border_colour'               => '#ddd',
				'custom_title_colour'                => '#999',
				'custom_link_colour'                 => '#428bca',
				'custom_link_hover_colour'           => '#2a6496',
				'custom_link_visited_colour'         => '#428bca',
				'lowercase'                          => FALSE,
				'hyphenate'                          => FALSE,
				//'bullet_spacing'                     => FALSE,
				'include_homepage'                   => FALSE,
				'exclude_css'                        => FALSE,
				'exclude'                            => '',
				'heading_levels'                     => array( '1', '2', '3', '4', '5', '6' ),
				'restrict_path'                      => '',
				'css_container_class'                => '',
				//'show_toc_in_widget_only'            => FALSE,
				//'show_toc_in_widget_only_post_types' => array(),
				'widget_affix_selector'              => '',
			);

			return apply_filters( 'ez_toc_get_default_options', $defaults );
		}

		private static function getOptions() {

			$defaults = self::getDefaults();
			$options  = get_option( 'ez-toc-settings', $defaults );

			//return apply_filters( 'ez_toc_get_options', wp_parse_args( $options, $defaults ) );
			return apply_filters( 'ez_toc_get_options', $options );
		}

		public static function get( $key, $default = FALSE ) {

			$options = self::getOptions();

			$value = array_key_exists( $key, $options ) ? $options[ $key ] : $default;
			$value = apply_filters( 'ez_toc_get_option', $value, $key, $default );

			return apply_filters( 'ez_toc_get_option_' . $key, $value, $key, $default );
		}

		public static function set( $key, $value = FALSE ) {

			if ( empty( $value ) ) {

				$remove_option = self::delete( $key );

				return $remove_option;
			}

			$options = self::getOptions();

			$options[ $key ] = apply_filters( 'ez_toc_update_option', $value, $key );

			return update_option( 'ez-toc-settings', $options );
		}

		public static function delete( $key ) {

			// First let's grab the current settings
			$options = get_option( 'ez-toc-settings' );

			// Next let's try to update the value
			if ( array_key_exists( $key, $options ) ) {

				unset( $options[ $key ] );
			}

			return update_option( 'ez-toc-settings', $options );
		}

		/**
		 * Tries to convert $string into a valid hex colour.
		 * Returns $default if $string is not a hex value, otherwise returns verified hex.
		 */
		private static function hex_value( $string = '', $default = '#' ) {

			$return = $default;

			if ( $string ) {
				// strip out non hex chars
				$return = preg_replace( '/[^a-fA-F0-9]*/', '', $string );

				switch ( strlen( $return ) ) {
					case 3:    // do next
					case 6:
						$return = '#' . $return;
						break;

					default:
						if ( strlen( $return ) > 6 ) {
							$return = '#' . substr( $return, 0, 6 );
						}    // if > 6 chars, then take the first 6
						elseif ( strlen( $return ) > 3 && strlen( $return ) < 6 ) {
							$return = '#' . substr( $return, 0, 3 );
						}    // if between 3 and 6, then take first 3
						else {
							$return = $default;
						}                        // not valid, return $default
				}
			}

			return $return;
		}

		public static function getPostTypes() {

			$exclude = apply_filters( 'ez_toc_exclude_post_types', array( 'attachment', 'revision', 'nav_menu_item', 'safecss' ) );
			$types   = get_post_types();

			return array_diff( $types, $exclude );
		}

		/**
		 * Missing Callback
		 *
		 * If a function is missing for settings callbacks alert the user.
		 *
		 * @since 1.0
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function missingCallback( $args ) {

			printf(
				__( 'The callback function used for the <strong>%s</strong> setting is missing.', 'ez_toc' ),
				$args['id']
			);
		}

		/**
		 * Text Callback
		 *
		 * Renders text fields.
		 *
		 * @since 1.0
		 *
		 * @param array $args Arguments passed by the setting
		 * @param null  $value
		 */
		public static function text( $args, $value = NULL ) {

			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( isset( $args['faux'] ) && TRUE === $args['faux'] ) {

				$args['readonly'] = TRUE;
				$value            = isset( $args['default'] ) ? $args['default'] : '';
				$name             = '';

			} else {

				$name = 'name="ez-toc-settings[' . $args['id'] . ']"';
			}

			$readonly = isset( $args['readonly'] ) && $args['readonly'] === TRUE ? ' readonly="readonly"' : '';
			$size     = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = '<input type="text" class="' . $size . '-text" id="ez-toc-settings[' . $args['id'] . ']"' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . '/>';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Number Callback
		 *
		 * Renders number fields.
		 *
		 * @since 1.0
		 *
		 * @param array $args        Arguments passed by the setting
		 */
		public static function number( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			if ( isset( $args['faux'] ) && TRUE === $args['faux'] ) {

				$args['readonly'] = TRUE;
				$value            = isset( $args['default'] ) ? $args['default'] : '';
				$name             = '';

			} else {

				$name = 'name="ez-toc-settings[' . $args['id'] . ']"';
			}

			$readonly = isset( $args['readonly'] ) && $args['readonly'] === TRUE ? ' readonly="readonly"' : '';
			$size     = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = '<input type="number" class="' . $size . '-text" id="ez-toc-settings[' . $args['id'] . ']"' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . '/>';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Checkbox Callback
		 *
		 * Renders checkboxes.
		 *
		 * @since 1.0
		 *
		 * @param array $args Arguments passed by the setting
		 * @param null  $value
		 */
		public static function checkbox( $args, $value = NULL ) {

			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( isset( $args['faux'] ) && TRUE === $args['faux'] ) {

				$name = '';

			} else {

				$name = 'name="ez-toc-settings[' . $args['id'] . ']"';
			}

			$checked = $value ? checked( 1, $value, FALSE ) : '';

			$html = '<input type="checkbox" id="ez-toc-settings[' . $args['id'] . ']"' . $name . ' value="1" ' . $checked . '/>';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Multicheck Callback
		 *
		 * Renders multiple checkboxes.
		 *
		 * @since 1.0
		 *
		 * @param array $args Arguments passed by the setting
		 * @param null  $value
		 */
		public static function checkboxgroup( $args, $value = NULL ) {

			if ( is_null( $value ) ) {

				$value = self::get( $args['id'], $args['default'] );
			}

			if ( ! empty( $args['options'] ) ) {

				foreach ( $args['options'] as $key => $option ):

					if ( in_array( $key, $value ) ) {

						$enabled = $option;

					} else {

						$enabled = NULL;
					}

					echo '<input name="ez-toc-settings[' . $args['id'] . '][' . $key . ']" id="ez-toc-settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $key . '" ' . checked( $option, $enabled, FALSE ) . '/>&nbsp;';
					echo '<label for="ez-toc-settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';

				endforeach;

				if ( 0 < strlen( $args['desc'] ) ) {

					echo '<p class="description">' . $args['desc'] . '</p>';
				}
			}
		}

		/**
		 * Radio Callback
		 *
		 * Renders radio groups.
		 *
		 * @since 1.3.3
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function radio( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			foreach ( $args['options'] as $key => $option ) {

				echo '<input name="ez-toc-settings[' . $args['id'] . ']"" id="ez-toc-settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( $key, $value, FALSE ) . '/>&nbsp;';
				echo '<label for="ez-toc-settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
			}

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<p class="description">' . $args['desc'] . '</p>';
			}
		}

		/**
		 * Select Callback
		 *
		 * Renders select fields.
		 *
		 * @since 1.0
		 *
		 * @param array $args Arguments passed by the setting.
		 */
		public static function select( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			if ( isset( $args['placeholder'] ) ) {
				$placeholder = $args['placeholder'];
			} else {
				$placeholder = '';
			}

			if ( isset( $args['chosen'] ) ) {
				$chosen = 'class="enhanced"';
			} else {
				$chosen = '';
			}

			$html = '<select id="ez-toc-settings[' . $args['id'] . ']" name="ez-toc-settings[' . $args['id'] . ']" ' . $chosen . 'data-placeholder="' . $placeholder . '" />';

			foreach ( $args['options'] as $option => $name ) {
				$selected = selected( $option, $value, FALSE );
				$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
			}

			$html .= '</select>';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Select Group Callback
		 *
		 * Renders select with option group fields.
		 *
		 * @since 1.0
		 *
		 * @param array $args Arguments passed by the setting.
		 */
		public static function selectgroup( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			if ( isset( $args['placeholder'] ) ) {
				$placeholder = $args['placeholder'];
			} else {
				$placeholder = '';
			}

			if ( isset( $args['chosen'] ) ) {
				$chosen = 'class="enhanced"';
			} else {
				$chosen = '';
			}

			$html = '<select id="ez-toc-settings[' . $args['id'] . ']" name="ez-toc-settings[' . $args['id'] . ']" ' . $chosen . 'data-placeholder="' . $placeholder . '" />';

			foreach ( $args['options'] as $group ) {

				$html .= sprintf( '<optgroup label="%1$s">', $group['name'] );

				foreach ( $group['options'] as $option => $name ) {

					$selected = selected( $option, $value, FALSE );
					$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
				}

				$html .= '</optgroup>';
			}

			$html .= '</select>';

			if ( 0 < strlen( $args['desc'] ) ) {

				$html .= '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		/**
		 * Header Callback
		 *
		 * Renders the header.
		 *
		 * @since 1.0
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function header( $args ) {

			echo '<hr/>';

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<p>' . wp_kses_post( $args['desc'] ) . '</p>';
			}
		}

		/**
		 * Descriptive text callback.
		 *
		 * Renders descriptive text onto the settings field.
		 *
		 * @since 1.0
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function descriptive_text( $args ) {

			echo wp_kses_post( $args['desc'] );
		}

		/**
		 * Color picker Callback
		 *
		 * Renders color picker fields.
		 *
		 * @since 1.0
		 *
		 * @param array $args Arguments passed by the setting
		 */
		public static function color( $args ) {

			$value = self::get( $args['id'], $args['default'] );

			$default = isset( $args['default'] ) ? $args['default'] : '';

			$html  = '<input type="text" class="ez-toc-color-picker" id="ez-toc-settings[' . $args['id'] . ']" name="ez-toc-settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}

			echo $html;
		}

		public static function custom_width( $args ) {

			//$value = self::get( $args['id'], $args['default'] );

			self::text(
				array(
					'id'      => $args['id'],
					'desc'    => '',
					'size'    => 'small',
					'default' => $args['default'],
				)
			);

			self::select(
				array(
					'id'      => $args['id'] . '_units',
					'desc'    => '',
					'options' => array(
						'px' => 'px',
						'%'  => '%',
						'em' => 'em',
					),
					'default' => 'px',
				)
			);

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}
		}

		public static function font_size( $args ) {

			//$value = self::get( $args['id'], $args['default'] );

			self::text(
				array(
					'id'      => $args['id'],
					'desc'    => '',
					'size'    => 'small',
					'default' => $args['default'],
				)
			);

			self::select(
				array(
					'id'      => $args['id'] . '_units',
					'desc'    => '',
					'options' => array(
						'pt' => 'pt',
						'%'  => '%',
						'em' => 'em',
					),
					'default' => 'px',
				)
			);

			if ( 0 < strlen( $args['desc'] ) ) {

				echo '<label for="ez-toc-settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';
			}
		}
	}

	add_action( 'admin_init', array( 'ezTOC_Option', 'register' ) );
}
