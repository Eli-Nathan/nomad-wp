<?php

/**
 * WPML Compatibility Class.
 *
 * @author  ClimaxThemes
 * @package Kata Plus
 * @since   1.1.11
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

if ( ! class_exists( 'Kata_Plus_WPML_Compatibility' ) ) {

	class Kata_Plus_WPML_Compatibility extends Kata_Plus_Compatibility {

		/**
		 * Instance of this class.
		 *
		 * @since   1.1.11
		 * @access  public
		 * @var     Kata_Plus_WPML_Compatibility
		 */
		public static $instance;

		/**
		 * Provides access to a single instance of a module using the singleton pattern.
		 *
		 * @since   1.1.11
		 * @return  object
		 */
		public static function get_instance() {

			if ( self::$instance === null ) {

				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since   1.1.11
		 */
		public function __construct() {

			$this->definitions();
			$this->actions();
		}

		/**
		 * Add actions.
		 *
		 * @since   1.1.11
		 */
		public function actions() {

			add_action( 'init', [$this, 'set_builders_language'], 999 );
			add_action( 'elementor/element/wp-post/document_settings/before_section_start', [$this, 'multi_language'], 10, 2 );
			add_action( 'elementor/element/wp-page/document_settings/before_section_start', [$this, 'multi_language'], 10, 2 );
		}

		/**
		 * Return registred languages.
		 *
		 * @since   1.1.11
		 */
		public function registred_languages() {

			return apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
		}

		/**
		 * get currnet language.
		 *
		 * @since   1.1.11
		 */
		public function get_currnet_language() {
			
			return apply_filters( 'wpml_current_language', NULL );
		}

		/**
		 * get default language.
		 *
		 * @since   1.1.11
		 */
		public function get_default_language() {
			
			return apply_filters( 'wpml_default_language', NULL );
		}

		/**
		 * get translations.
		 *
		 * @since   1.1.11
		 */
		public function get_translations( $id ) {
			
			return apply_filters( 'wpml_get_element_translations', NULL, $id, 'post_kata_plus_builder' );
		}

		/**
		 * Add get language.
		 *
		 * @since   1.1.11
		 */
		public function set_builders_language() {

			$kata_options = get_option( 'kata_options' );
			$run = false;

			if ( ! isset( $kata_options['multilanguage']['wpml'] ) ) {
				
				$args = array(
					'numberposts'      => -1,
					'post_type'        => 'kata_plus_builder',
				);
	
				$builders	= get_posts( $args );
				$languages	= $this->registred_languages();

				if ( ! empty( $languages ) ) {

					foreach ( $languages as $language ) {
						
						if ( $language['code'] != $this->get_default_language() ) {

							foreach ( $builders as $builder ) {
								
								$get_translations = $this->get_translations( $builder->ID );
								
								if ( sizeof( $languages ) != sizeof( $get_translations ) ) {
			
									$new_builder = [
										'post_title'    => $builder->post_title . ' ' . $language['code'],
										'post_content'  => '',
										'post_status'   => 'publish',
										'post_date'     => date( 'Y-m-d H:i:s' ),
										'post_author'   => '',
										'post_type'     => 'kata_plus_builder',
										'post_category' => [0],
									];
			
									$translation = get_page_by_title( $new_builder['post_title'] );
			
									if ( ! $translation ) {
			
										$translation_id = wp_insert_post( $new_builder );
			
										if ( $translation_id && ! is_wp_error ( $translation_id ) ) {
			
											update_post_meta( $translation_id, '_elementor_edit_mode', 'builder' );
											update_post_meta( $translation_id, '_elementor_template_type', 'post' );
											update_post_meta( $translation_id, '_wp_page_template', 'default' );
											update_post_meta( $translation_id, '_edit_lock', time() . ':1' );
											update_post_meta( $translation_id, '_elementor_version', '0.4' );
											update_post_meta( $translation_id, '_elementor_data', get_post_meta( $builder->ID, '_elementor_data', true ) );
											update_post_meta( $translation_id, '_kata_builder_type', get_post_meta( $builder->ID, '_kata_builder_type', true ) );
										}
			
										$wpml_element_type = apply_filters( 'wpml_element_type', 'kata_plus_builder' );
			
										$get_language_args = [
											'element_id'	=> $builder->ID,
											'element_type'	=> 'kata_plus_builder'
										];
			
										$original_post_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args );
			
										$set_language_args = [
											'element_id'    		=> $translation_id,
											'element_type'  		=> $wpml_element_type,
											'trid'					=> $original_post_language_info->trid,
											'language_code'			=> $language['code'],
											'source_language_code'	=> $original_post_language_info->language_code
										];
			
										do_action( 'wpml_set_element_language_details', $set_language_args );
									}
			
								}
			
							}

							$run = true;
						}
		
					}
				}
			}

			if ( $run ) {

				$kata_options['multilanguage']['wpml'] = true;
				$kata_options['multilanguage']['polylang'] = false;

				update_option( 'kata_options', $kata_options );
			}
		}

		/**
		 * Page Options.
		 *
		 * @since   1.0.0
		 */
		public function multi_language_url( $id ) {
			
			if ( class_exists( 'SitePress' ) ) {
				
				$translations = $this->get_translations( $id );

				$out = '';
				
				foreach ( $translations as $key => $translation ) {
					$out .= '<p class="kata-multi-lang-links">';
					$out .= '<a href="' . esc_url( admin_url( 'post.php?post=' . $translation->element_id . '&action=elementor' ) ) . '" target="_blank">' . __( 'Edit', 'kata-plus' ) . ' ' . $key  . ' ' . __( 'version', 'kata-plus' ) .'</a>';
					$out .= '</p>';
				}

				return $out;
			}
		}

		/**
		 * Page Options.
		 *
		 * @since   1.0.0
		 */
		public function multi_language( $page ) {

			$page->start_controls_section(
				'kata_multi_language',
				[
					'label' => esc_html__( 'Multi-Language', 'kata-plus' ),
					'tab'   => Controls_Manager::TAB_SETTINGS,
				]
			);

			$page->add_control(
				'multi_lang_urls',
				[
					'type'				=> \Elementor\Controls_Manager::RAW_HTML,
					'raw'				=> $this->multi_language_url( get_the_ID() ),
					'content_classes'	=> 'your-class',
				]
			);

			$page->end_controls_section();

		}

	}
	Kata_Plus_WPML_Compatibility::get_instance();
}
