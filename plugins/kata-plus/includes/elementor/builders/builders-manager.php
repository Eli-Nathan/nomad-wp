<?php

/**
 * Widget Base Class.
 *
 * @author  ClimaxThemes
 * @package Kata Plus
 * @since   1.2.0
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Plugin;
use Elementor\Post_CSS_File;
use Elementor\Core\Files\CSS\Post;


if ( ! class_exists( 'Kata_Plus_Builders_Manager' ) ) {

	class Kata_Plus_Builders_Manager extends Kata_Plus_Builders_Base {

		/**
		 * Instance of this class.
		 *
		 * @since   1.2.0
		 * @access  public
		 * @var     Kata
		 */
		public static $instance;

		/**
		 * Provides access to a single instance of a module using the singleton pattern.
		 *
		 * @since   1.2.0
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
		 * @since    1.2.0
		 */
		public function __construct() {

			if ( ! class_exists( 'Elementor\Plugin' ) ) {

				return;
			}
			
			$this->definitions();
			$this->actions();
		}

		/**
		 * Definitions.
		 *
		 * @since   1.2.0
		 */
		public function definitions() {

		}

		/**
		 * Actions.
		 *
		 * @since     1.2.0
		 */
		public function actions() {

			add_filter( 'manage_edit-kata_plus_builder_columns', [$this, 'builder_type_column'], 99, 1 );
			add_action( 'manage_kata_plus_builder_posts_custom_column', [$this, 'manage_builder_type_column'], 99, 1 );
			add_action( 'restrict_manage_posts', [$this, 'filter_kata_plus_builder_select_options'] );
			add_filter( 'parse_query', [$this, 'builders_filters'], 99, 1 );
			add_action( 'admin_enqueue_scripts', [$this, 'admin_script'] );
			add_action( 'save_post_kata_plus_builder', [$this, 'add_new_builder'], 99, 3 );
			add_action( 'admin_init', [$this, 'redirect'], 99, 3 );
			add_action( 'views_edit-kata_plus_builder',  [$this, 'manage_views'], 10, 1 );
			add_action( 'wp_ajax_set_primary_builder', [$this, 'set_primary_builder'] );
			add_action( 'wp_ajax_update_builders', [$this, 'update_builders'] );
			add_action( 'admin_notices', [$this, 'update_builders_notice'] );
		}

		/**
		 * Builder Type Column.
		 *
		 * @since   1.2.0
		 */
		public function builder_type_column( $columns ) {

			$columns['type'] = esc_html__( 'Type', 'kata-plus' );
			$columns['primary'] = esc_html__( 'Primary Builder', 'kata-plus' );

			return $columns;
		}

		/**
		 * Manage Builder type column.
		 *
		 * @since   1.2.0
		 */
		public function manage_builder_type_column( $columns ) {

			global $post;

			$type = get_post_meta( $post->ID, '_kata_builder_type', true );

			if ( $columns ==  'type') {

				switch ( $type ) {

					case 'kata_404':
						echo __( '404', 'kata-plus' );
						break;
					case 'kata_archive':
						echo __( 'Archive', 'kata-plus' );
						break;
					case 'kata_author':
						echo __( 'Author', 'kata-plus' );
						break;
					case 'kata_single_course':
						echo __( 'Single Course', 'kata-plus' );
						break;
					case 'kata_archive_portfolio':
						echo __( 'Archive Portfolio', 'kata-plus' );
						break;
					case 'kata_search':
						echo __( 'Search', 'kata-plus' );
						break;
					case 'kata_single_post':
						echo __( 'Single Post', 'kata-plus' );
						break;
					case 'kata_single_portfolio':
						echo __( 'Single Portfolio', 'kata-plus' );
						break;
					case 'kata_sticky_header':
						echo __( 'Sticky Header', 'kata-plus' );
						break;
					case 'kata_blog':
						echo __( 'Blog', 'kata-plus' );
						break;
					case 'kata_footer':
						echo __( 'Footer', 'kata-plus' );
						break;
					case 'kata_header':
						echo __( 'Header', 'kata-plus' );
						break;

				}
			}

			if ( $columns ==  'primary') {

				$primery = get_post_meta( $post->ID, '_' . $type . '_primary', true );
				$checked = $primery == 'true' ? 'checked' : '';

				?>
					<input
					id="<?php echo esc_attr( $type . '-' . $post->ID ); ?>"
					class="primary-kata-builder"
					type="radio"
					name="<?php echo esc_attr( $type ); ?>"
					value="<?php echo esc_attr( $post->ID ); ?>"
					<?php echo esc_attr( $checked ) ?>
					>
				<?php
			}
		}

		/**
		 * Builders Filter.
		 *
		 * @since   1.2.0
		 */
		public function builders_filters( $query ) {

			global $pagenow;
			$current_page	= isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
			$builder		= isset( $_GET['builder'] ) ? sanitize_text_field( $_GET['builder'] ) : 'kata_header';
			
			if ( is_admin() && 'kata_plus_builder' == $current_page && 'edit.php' == $pagenow && isset( $_GET['builder'] ) && $_GET['builder'] != '' && $query->query_vars['post_type'] == 'kata_plus_builder' ) {
			
				$query->set( 'meta_query', [
					[
						'key'       => '_kata_builder_type',
						'value'     => $builder,
						'compare'   => '='
					]
				]);
			}

		}

		/**
		 * Builders Filter select html.
		 *
		 * @since   1.2.0
		 */
		public function filter_kata_plus_builder_select_options() {
			
			global $pagenow;
			$type		= 'kata_plus_builder';
			$post_type	= isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
			$builder	= isset( $_GET['builder'] ) ? sanitize_text_field( $_GET['builder'] ) : '';
		
			if ( $post_type == $type && is_admin() && $pagenow == 'edit.php' ) {

				?>
				<label for="builder" class="screen-reader-text"><?php _e( 'Filter by builders', 'kata-plus' ); ?></label>
				<select name="builder" id="builder">

					<?php if ( in_array( 'kata-plus-pro/kata-plus-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
						<option value="kata_header" <?php isset( $builder ) ? selected( $builder, 'kata_header' ) : ''; ?>>
							<?php _e( 'Header', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_blog" <?php isset( $builder ) ? selected( $builder, 'kata_blog' ) : ''; ?>>
							<?php _e( 'Blog', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_footer" <?php isset( $builder ) ? selected( $builder, 'kata_footer' ) : ''; ?>>
							<?php _e( 'Footer', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_404" <?php isset( $builder ) ? selected( $builder, 'kata_404' ) : ''; ?>>
							<?php _e( '404', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_archive" <?php isset( $builder ) ? selected( $builder, 'kata_archive' ) : ''; ?>>
							<?php _e( 'Archive', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_author" <?php isset( $builder ) ? selected( $builder, 'kata_author' ) : ''; ?>>
							<?php _e( 'Author', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_single_course" <?php isset( $builder ) ? selected( $builder, 'kata_single_course' ) : ''; ?>>
							<?php _e( 'Single Course', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_archive_portfolio" <?php isset( $builder ) ? selected( $builder, 'kata_archive_portfolio' ) : ''; ?>>
							<?php _e( 'Archive Portfolio', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_search" <?php isset( $builder ) ? selected( $builder, 'kata_search' ) : ''; ?>>
							<?php _e( 'Search', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_single_post" <?php isset( $builder ) ? selected( $builder, 'kata_single_post' ) : ''; ?>>
							<?php _e( 'Single Post', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_single_portfolio" <?php isset( $builder ) ? selected( $builder, 'kata_single_portfolio' ) : ''; ?>>
							<?php _e( 'Single Portfolio', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_sticky_header" <?php isset( $builder ) ? selected( $builder, 'kata_sticky_header' ) : ''; ?>>
							<?php _e( 'Sticky Header', 'kata-plus' ); ?>
						</option>
					<?php } else { ?>
						<option value="kata_header" <?php isset( $builder ) ? selected( $builder, 'kata_header' ) : ''; ?>>
							<?php _e( 'Header', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_blog" <?php isset( $builder ) ? selected( $builder, 'kata_blog' ) : ''; ?>>
							<?php _e( 'Blog', 'kata-plus' ); ?>
						</option>
	
						<option value="kata_footer" <?php isset( $builder ) ? selected( $builder, 'kata_footer' ) : ''; ?>>
							<?php _e( 'Footer', 'kata-plus' ); ?>
						</option>
					<?php } ?>

				</select>
				<?php
			}
		}

		/**
		 * Regist Builder scripts.
		 *
		 * @since   1.2.0
		 */
		public function admin_script() {

			global $pagenow;
			$current_page	= isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
			$builder		= isset( $_GET['builder'] ) ? sanitize_text_field( $_GET['builder'] ) : 'kata_header';

			if ( is_admin() && 'kata_plus_builder' == $current_page && 'edit.php' == $pagenow ) {
				wp_enqueue_script( 'kata-builders', Kata_Plus::$assets . 'js/backend/builders.js', ['jquery'], Kata_Plus::$version, true );
				
				wp_localize_script( 'kata-builders', 'kata_builders_localize', [
					'ajax' => [
						'url' => admin_url( 'admin-ajax.php' ),
						'nonce' => wp_create_nonce( 'kata_builder_nonce' ),
					],
					'builder_branch' => $builder
				] );

			}

		}

		/**
		 * Add new builder.
		 *
		 * @since   1.2.0
		 * @param Return $post_ID int
		 * @param Return $post object
		 * @param Return $update boolean
		 */
		public function add_new_builder( $post_ID, $post, $update ) {

			if ( $update ) {
				return;
			}

			$builder = isset( $_GET['builder'] ) ? sanitize_text_field( $_GET['builder'] ) : 'kata_header';

			switch ( $builder ) {

				case 'kata_404':
					$content = class_exists( 'Kata_Plus_Pro_404_Builder' ) ? Kata_Plus_Pro_404_Builder::get_instance()->default_content : '';
					break;
				case 'kata_archive':
					$content = class_exists( 'Kata_Plus_Pro_Archive_Builder' ) ? Kata_Plus_Pro_Archive_Builder::get_instance()->default_content : '';
					break;
				case 'kata_author':
					$content = class_exists( 'Kata_Plus_Pro_Author_Builder' ) ? Kata_Plus_Pro_Author_Builder::get_instance()->default_content : '';
					break;
				case 'kata_single_course':
					$content = class_exists( 'Kata_Plus_Pro_Single_Course_Builder' ) ? Kata_Plus_Pro_Single_Course_Builder::get_instance()->default_content : '';
					break;
				case 'kata_archive_portfolio':
					$content = class_exists( 'Kata_Plus_Pro_Portfolio_Archive_Builder' ) ? Kata_Plus_Pro_Portfolio_Archive_Builder::get_instance()->default_content : '';
					break;
				case 'kata_search':
					$content = class_exists( 'Kata_Plus_Pro_Search_Builder' ) ? Kata_Plus_Pro_Search_Builder::get_instance()->default_content : '';
					break;
				case 'kata_single_post':
					$content = class_exists( 'Kata_Plus_Pro_Single_Builder' ) ? Kata_Plus_Pro_Single_Builder::get_instance()->default_content : '';
					break;
				case 'kata_single_portfolio':
					$content = class_exists( 'Kata_Plus_Pro_Single_Portfolio_Builder' ) ? Kata_Plus_Pro_Single_Portfolio_Builder::get_instance()->default_content : '';
					break;
				case 'kata_sticky_header':
					$content = class_exists( 'Kata_Plus_Pro_Sticky_Header_Builder' ) ? Kata_Plus_Pro_Sticky_Header_Builder::get_instance()->default_content : '';
					break;
				case 'kata_blog':
					$content = class_exists( 'Kata_Plus_Blog_Builder' ) ? Kata_Plus_Blog_Builder::get_instance()->default_content : '';
					break;
				case 'kata_footer':
					$content = class_exists( 'Kata_Plus_Footer_Builder' ) ? Kata_Plus_Footer_Builder::get_instance()->default_content : '';
					break;
				case 'kata_header':
					$content = class_exists( 'Kata_Plus_Header_Builder' ) ? Kata_Plus_Header_Builder::get_instance()->default_content : '';
					break;
			}

			update_post_meta( $post_ID, '_elementor_edit_mode', 'builder' );
			update_post_meta( $post_ID, '_elementor_template_type', 'post' );
			update_post_meta( $post_ID, '_wp_page_template', 'default' );
			update_post_meta( $post_ID, '_edit_lock', time() . ':1' );
			update_post_meta( $post_ID, '_elementor_version', '0.4' );
			update_post_meta( $post_ID, '_elementor_data', $content );
			update_post_meta( $post_ID, '_kata_builder_type', $builder );

		}

		/**
		 * Redirect builder.
		 *
		 * @since   1.2.0
		 */
		public function redirect() {

			global $pagenow;
			$current_page	= isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
			$builder		= isset( $_GET['builder'] ) ? sanitize_text_field( $_GET['builder'] ) : 'kata_header';
			$http_query		= http_build_query( $_GET ) . '&builder=kata_header';

			if ( is_admin() && 'kata_plus_builder' == $current_page && 'edit.php' == $pagenow && ! isset( $_GET['builder'] ) ) {

				exit( wp_redirect( admin_url( sprintf( 'edit.php?%s', $http_query ) ) ) );
			}
		}

		/**
		 * Add new builder.
		 *
		 * @since   1.2.0
		 * @param Return $views array.
		 */
		public function manage_views( $views ) {

			unset( $views['all'] );
			return $views;
		}

		/**
		 * Ajax Set Primary Builder.
		 *
		 * @since   1.2.0
		 * @param Return object.
		 */
		public function set_primary_builder() {
			
			check_ajax_referer( 'kata_builder_nonce', 'nonce' );

			$builder_id		= isset( $_POST['builder_id'] ) ? sanitize_text_field( $_POST['builder_id'] ) : '';
			$builder_type	= isset( $_POST['builder_type'] ) ? sanitize_text_field( $_POST['builder_type'] ) : '';

			if ( $builder_id && $builder_type ) {

				// reset primery builder
				$args = [
					'post_type'  => 'kata_plus_builder',
					'meta_query' => [
						[
							'key'       => '_kata_builder_type',
							'value'     => $builder_type,
							'compare'   => '='
						]
					]
				];

				$builders = get_posts( $args );

				foreach ( $builders as $builder ) {

					update_post_meta( $builder->ID, '_' . $builder_type . '_primary', 'false' );
				}
				
				// set primery builder
				if ( $builder_id ) {

					$update = update_post_meta( $builder_id, '_' . $builder_type . '_primary', 'true' );

					if ( ! is_wp_error( $update ) ) {

						$primery_builder = get_post( $builder_id, OBJECT );

						wp_send_json( wp_sprintf( '%s, %l', get_the_title( $builder_id ), __( 'selected as the primary builder', 'kata-plus' ) ), 200 );
					} else {
						
						wp_send_json( __( 'An error occurred while performing the operation', 'kata-plus' ), 400 );
					}
				}

			}

			wp_die();
		}

		/**
		 * Ajax Update Builders.
		 *
		 * @since   1.2.0
		 * @param Return object.
		 */
		public function update_builders() {
			
			check_ajax_referer( 'kata_builder_nonce', 'nonce' );

			$kata_options = get_option( 'kata_options' );
			$update_builders = isset( $_POST['update_builders'] ) ? sanitize_text_field( $_POST['update_builders'] ) : false;

			if ( $update_builders && $kata_options['updates']['builders']['primary'] != 'updated' ) {

				$ids = [
					'kata_blog'					=> Kata_Plus_Blog_Builder::get_instance()->get_post_by_title( 'Kata Blog' ),
					'kata_footer'				=> Kata_Plus_Footer_Builder::get_instance()->get_post_by_title( 'Kata Footer' ),
					'kata_header' 				=> Kata_Plus_Header_Builder::get_instance()->get_post_by_title( 'Kata Header' ),
				];

				if ( in_array( 'kata-plus-pro/kata-plus-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

					$ids = [
						'kata_404'				 	=> Kata_Plus_Pro_404_Builder::get_instance()->get_post_by_title( 'Kata 404' ),
						'kata_archive'			 	=> Kata_Plus_Pro_Archive_Builder::get_instance()->get_post_by_title( 'Kata Archive' ),
						'kata_author'			 	=> Kata_Plus_Pro_Author_Builder::get_instance()->get_post_by_title( 'Kata Author' ),
						'kata_single_course'		=> Kata_Plus_Pro_Single_Course_Builder::get_instance()->get_post_by_title( 'Single Course' ),
						'kata_archive_portfolio'	=> Kata_Plus_Pro_Portfolio_Archive_Builder::get_instance()->get_post_by_title( 'Kata Portfolio Archive' ),
						'kata_search'				=> Kata_Plus_Pro_Search_Builder::get_instance()->get_post_by_title( 'Kata Search' ),
						'kata_single_post'			=> Kata_Plus_Pro_Single_Builder::get_instance()->get_post_by_title( 'Kata Single' ),
						'kata_single_portfolio'		=> Kata_Plus_Pro_Single_Portfolio_Builder::get_instance()->get_post_by_title( 'Kata Single Portfolio' ),
						'kata_sticky_header'		=> Kata_Plus_Pro_Sticky_Header_Builder::get_instance()->get_post_by_title( 'Kata Sticky Header' ),
						'kata_blog'					=> Kata_Plus_Blog_Builder::get_instance()->get_post_by_title( 'Kata Blog' ),
						'kata_footer'				=> Kata_Plus_Footer_Builder::get_instance()->get_post_by_title( 'Single Footer' ),
						'kata_header' 				=> Kata_Plus_Header_Builder::get_instance()->get_post_by_title( 'Kata Header' ),
					];
				}

				$result = [];

				foreach ( $ids as $key => $id ) {
					$result[$key] = update_post_meta( $id, '_' . $key . '_primary', 'true' );
					$result[$key] = update_post_meta( $id, '_kata_builder_type', $key );
				}

				$kata_options['updates']['builders']['primary'] = 'updated';

				update_option( 'kata_options', $kata_options );

				return wp_send_json( [
					'message' => __( 'Updated', 'kata-plus' ),
					'results' => $result
				], 200 );

			}
			

			wp_die();
		}

		/**
		 * Builders update notification.
		 *
		 * @since   1.2.0
		 * @param Return HTML.
		 */
		public function update_builders_notice() {
			
			$kata_options = get_option( 'kata_options' );

			if ( ! isset( $kata_options['updates']['builders']['primary'] ) && version_compare( Kata_Plus::$version, '1.2.0', '>=' ) ) {
				global $pagenow;
				?>
				<style>
					.update-kata-builders p:first-of-type:before {
						display: none;
					}
				</style>
				<div class="notice notice-success is-dismissible update-kata-builders">
					<h2><?php _e( 'Update the kata builders', 'kata-plus' ); ?></h2>
					
					<?php if ( 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'kata_plus_builder' ) { ?>
						<p style="max-width: 70%;"><?php _e( 'Please click on the update button.', 'kata-plus' ); ?></p>
					<?php } else { ?>
						<p style="max-width: 70%;"><?php _e( 'From version 1.2.0 onwards, a new feature has been added to the kata theme. Some values need to be updated in the "Kata Builders", this update is required and if you do not do it the header, sticker header, footer, blog and other "Kata Builders" will face problems. and will not display on your site front, to update them.', 'kata-plus' ); ?></p>
					<?php } ?>
					
					<?php if ( 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'kata_plus_builder' ) { ?>
						<p><a href="<?php echo admin_url( 'edit.php?post_type=kata_plus_builder&builder=kata_header' )?>" class="button button-primary"><?php _e( 'Update', 'kata-plus' ); ?></a></p>
					<?php } else { ?>
						<p><a href="<?php echo admin_url( 'edit.php?post_type=kata_plus_builder&builder=kata_header' )?>" class="button button-primary"><?php _e( 'Go to update page', 'kata-plus' ); ?></a></p>
					<?php } ?>
				</div>
				<?php

			}

		}

	} // class

	Kata_Plus_Builders_Manager::get_instance();
}
