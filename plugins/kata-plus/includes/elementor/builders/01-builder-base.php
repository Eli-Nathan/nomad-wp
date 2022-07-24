<?php

/**
 * Widget Base Class.
 *
 * @author  ClimaxThemes
 * @package Kata Plus
 * @since   1.0.0
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Plugin;
use Elementor\Post_CSS_File;
use Elementor\Core\Files\CSS\Post;

if ( class_exists( 'Elementor\Plugin' ) ) {

	if ( ! class_exists( 'Kata_Plus_Builders_Base' ) ) {

		class Kata_Plus_Builders_Base extends \Elementor\Core\Base\Document {

			/**
			 * An array of arguments.
			 *
			 * @access  protected
			 * @var     array
			 */
			protected $args;

			/**
			 * Builder action.
			 *
			 * @access  protected
			 * @var     String
			 */
			protected $action;

			/**
			 * Builder before content.
			 *
			 * @access  protected
			 * @var     String
			 */
			protected $before;

			/**
			 * Builder after content.
			 *
			 * @access  protected
			 * @var     String
			 */
			protected $after;

			/**
			 * Builder name.
			 *
			 * @access  protected
			 * @var     String
			 */
			public $name;

			/**
			 * Builder default content.
			 *
			 * @access  protected
			 * @var     String
			 */
			protected $default_content;

			/**
			 * Builder default type.
			 *
			 * @access  protected
			 * @var     String
			 */
			protected $builder_type;

			/**
			 * Instance of this class.
			 *
			 * @since   1.0.0
			 * @access  public
			 * @var     Kata
			 */
			public static $instance;

			/**
			 * Provides access to a single instance of a module using the singleton pattern.
			 *
			 * @since   1.0.0
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
			 * @since    1.0.0
			 */
			public function __construct() {

				$this->definitions();
				$this->builder_definitions();
				$this->actions();
			}

			/**
			 * Definitions.
			 *
			 * @since   1.0.0
			 */
			public function definitions() {}

			/**
			 * Header definitions.
			 *
			 * @since   1.0.0
			 */
			public function builder_definitions() {
				
				/**
				 * Define the arguments for the post type in $args array. A full list
				 * of the available arguments can be found here:
				 * https://codex.wordpress.org/Function_Reference/register_post_type
				 */

				$builder = isset( $_GET['builder'] ) ? sanitize_text_field( $_GET['builder'] ) : 'kata_header';

				switch ( $builder ) {

					case 'kata_404':
						$builder = '404';
						break;
					case 'kata_archive':
						$builder = 'Archive';
						break;
					case 'kata_author':
						$builder = 'Author';
						break;
					case 'kata_single_course':
						$builder = 'Single Course';
						break;
					case 'kata_archive_portfolio':
						$builder = 'Archive Portfolio';
						break;
					case 'kata_search':
						$builder = 'Search';
						break;
					case 'kata_single_post':
						$builder = 'Single Post';
						break;
					case 'kata_single_portfolio':
						$builder = 'Single Portfolio';
						break;
					case 'kata_sticky_header':
						$builder = 'Sticky Header';
						break;
					case 'kata_blog':
						$builder = 'Blog';
						break;
					case 'kata_footer':
						$builder = 'Footer';
						break;
					case 'kata_header':
					default:
						$builder = 'Header';
						break;
				}
				

				$this->args = [
					'description'         => esc_html__( 'Description.', 'kata-plus' ),
					'public'              => true,
					'publicly_queryable'  => true,
					'show_ui'             => true,
					'show_in_menu'        => false,
					'query_var'           => true,
					'rewrite'             => ['slug' => 'kata-plus-builder'],
					'has_archive'         => true,
					'hierarchical'        => true,
					'exclude_from_search' => true,
					'supports'            => ['title', 'editor', 'elementor'],
					'labels'              => [
						'name'               => _x( 'Builders', 'post type general name', 'kata-plus' ),
						'singular_name'      => _x( 'Builder', 'post type singular name', 'kata-plus' ),
						'menu_name'          => _x( 'Builders', 'admin menu', 'kata-plus' ),
						'name_admin_bar'     => _x( 'Builders', 'add new on admin bar', 'kata-plus' ),
						'add_new'            => _x( 'Add New ' . $builder, 'Builder', 'kata-plus' ),
						'add_new_item'       => esc_html__( 'Add New ' . $builder, 'kata-plus' ),
						'new_item'           => esc_html__( 'New Builder', 'kata-plus '),
						'edit_item'          => esc_html__( 'Edit Builder', 'kata-plus '),
						'view_item'          => esc_html__( 'View Builder', 'kata-plus '),
						'all_items'          => esc_html__( 'All Builders', 'kata-plus '),
						'search_items'       => esc_html__( 'Search Builders', 'kata-plus '),
						'parent_item_colon'  => esc_html__( 'Parent Builders:', 'kata-plus '),
						'not_found'          => esc_html__( 'No Builder found.', 'kata-plus '),
						'not_found_in_trash' => esc_html__( 'No Builder found in Trash.', 'kata-plus '),
					],
				];
			}

			/**
			 * Builder Name.
			 *
			 * @since     1.0.0
			 */
			public function get_name() {

				return $this->name;
			}

			/**
			 * Actions.
			 *
			 * @since     1.0.0
			 */
			public function actions() {

				add_action( 'init', [$this, 'setup'], 99 );
				add_action( 'init', [$this, 'create_builder_post_type'], 99 );
				add_action( $this->action, [$this, 'builder_render'], 99 );
				add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
				add_action( 'admin_notices', [$this, 'notice'], 99 );
				add_action( 'elementor/editor/after_enqueue_scripts', [$this, 'define_builder_name'], 99 );
			}

			/**
			 * Get Builder ID.
			 *
			 * @since   1.0.0
			 */
			public function get_builder_id() {

				$args = [
					'post_type'  => 'kata_plus_builder',
					'meta_query' => [
						[
							'key'       => '_' . $this->action . '_primary',
							'value'     => 'true',
							'compare'   => '='
						]
					]
				];

				$builders = get_posts( $args );
				
				return $builders[0]->ID;
			}

			/**
			 * Get Builder ID.
			 *
			 * @since   1.0.0
			 */
			public static function builder_url( $post_title = '' ) {

				$post_title = $post_title ? $post_title : $this->name;
				$id = self::get_post_by_title( $post_title );
				$url = admin_url( 'post.php?post=' . $id . '&action=elementor' );
				
				/**
				 * Polylang Compatibility
				 */
				if ( function_exists( 'PLL' ) ) {

					$currnet_lang = pll_current_language() ? pll_current_language() : pll_default_language();
					$ids = pll_get_post_translations( $id );
					$url = admin_url( 'post.php?post=' . $ids[$currnet_lang] . '&action=elementor' );
				}

				/**
				 * WPML Compatibility
				 */
				if ( class_exists( 'SitePress' ) ) {

					$currnet_lang		= Kata_Plus_WPML_Compatibility::get_instance()->get_currnet_language() == 'all' ? Kata_Plus_WPML_Compatibility::get_instance()->get_default_language() : Kata_Plus_WPML_Compatibility::get_instance()->get_currnet_language();
					$translations_id	= apply_filters( 'wpml_object_id', $id, 'kata_plus_builder', false, $currnet_lang );
					$url				= admin_url( 'post.php?post=' . $translations_id . '&action=elementor' );
				}

				return $url;
			}

			/**
			 * Setup.
			 *
			 * @since   1.0.0
			 */
			public function setup() {

				$this->setup_post( $this->name );
			}

			/**
			 * Define Builder Name.
			 *
			 * @since   1.0.0
			 */
			public function define_builder_name() {

				if ( isset( $_GET ) && isset( $_GET['action'] ) && isset( $_GET['post'] ) && $_GET['post'] == $this->get_builder_id() ) {

					$name = preg_replace('/.*?_(.*?)/', '$1', strtolower($this->action));
					$name = $name == 'post' ? 'single' : $name;
					echo ('<script>var kata_plus_this_page_name = "' . $name . '";</script>');
				}
			}

			/**
			 * Setup Post.
			 *
			 * @since   1.0.0
			 */
			private function setup_post( $post_title ) {

				global $wpdb;
				$query = $wpdb->prepare(
					'SELECT * FROM ' . $wpdb->posts . '
					WHERE post_title = %s
					AND post_type = \'kata_plus_builder\' order by \'publish_date\' desc',
					$post_title
				);
				$wpdb->query( $query );

				if ( $wpdb->num_rows ) {

					$post = $wpdb->get_row( $query );
					return $post->ID;
				} else {

					$new_post   = [
						'post_title'    => $post_title,
						'post_content'  => '',
						'post_status'   => 'publish',
						'post_date'     => date('Y-m-d H:i:s'),
						'post_author'   => '',
						'post_type'     => 'kata_plus_builder',
						'post_category' => [0],
					];

					$post_id    = wp_insert_post( $new_post );
					$this->post = get_post( $post_id );

					update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
					update_post_meta( $post_id, '_elementor_template_type', 'post' );
					update_post_meta( $post_id, '_wp_page_template', 'default' );
					update_post_meta( $post_id, '_edit_lock', time() . ':1' );
					update_post_meta( $post_id, '_elementor_version', '0.4' );
					update_post_meta( $post_id, '_elementor_data', $this->default_content );
					update_post_meta( $post_id, '_' . $this->builder_type . '_primary', 'true' );
					update_post_meta( $post_id, '_kata_builder_type', $this->builder_type );

					return $post_id;
				}

				return false;
			}

			/**
			 * Load The Header
			 *
			 * @since     1.0.0
			 */
			public function builder_render() {

				if ( ! class_exists( '\Elementor\Plugin' ) ) {

					return;
				}

				switch ( $this->action ) {

					case 'kata_404':
						$id = Kata_Plus_Pro_404_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_archive':
						$id = Kata_Plus_Pro_Archive_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_author':
						$id = Kata_Plus_Pro_Author_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_single_course':
						$id = Kata_Plus_Pro_Single_Course_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_archive_portfolio':
						$id = Kata_Plus_Pro_Portfolio_Archive_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_search':
						$id = Kata_Plus_Pro_Search_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_single_post':
						$id = Kata_Plus_Pro_Single_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_single_portfolio':
						$id = Kata_Plus_Pro_Single_Portfolio_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_sticky_header':
						$id = Kata_Plus_Pro_Sticky_Header_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_blog':
						$id = Kata_Plus_Blog_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_footer':
						$id = Kata_Plus_Footer_Builder::get_instance()->get_builder_id();
						break;
					case 'kata_header':
						$id = Kata_Plus_Header_Builder::get_instance()->get_builder_id();
						break;

				}

				if ( Kata_Plus_helpers::is_blog_pages() || is_404() ) {

					$kata_options = get_option( 'kata_options' );
					
					if ( $this->builder_type == 'kata_header' && is_search() && ! get_theme_mod( 'kata_search_show_header', true ) ) {
						return;
					}
					if ( $this->builder_type == 'kata_header' && is_404() && ! get_theme_mod( 'kata_404_show_header', true ) ) {
						return;
					}
					if ( $this->builder_type == 'kata_header' && is_author() && ! get_theme_mod( 'kata_author_show_header', true ) ) {
						return;
					}
					if ( $this->builder_type == 'kata_header' && is_archive() && ! get_theme_mod( 'kata_archive_show_header', true ) ) {
						return;
					}
					if ( $this->builder_type == 'kata_header' && Kata_Plus_helpers::is_blog() && ! get_theme_mod( 'kata_blog_show_header', true ) ) {
						return;
					}
				}

				/**
				 * Polylang Compatibility
				 */
				if ( function_exists( 'PLL' ) ) {
					$currnet_lang = pll_current_language() ? pll_current_language() : pll_default_language();
					$ids = pll_get_post_translations( $id );
					$id = $ids[$currnet_lang];
				}

				/**
				 * WPML Compatibility
				 */
				if ( class_exists( 'SitePress' ) ) {
					$currnet_lang	= Kata_Plus_WPML_Compatibility::get_instance()->get_currnet_language() == 'all' ? Kata_Plus_WPML_Compatibility::get_instance()->get_default_language() : Kata_Plus_WPML_Compatibility::get_instance()->get_currnet_language();
					$id				= apply_filters( 'wpml_object_id', $id, 'kata_plus_builder', false, $currnet_lang );
				}

				/**
				 * Setup Post Meta for old users
				 */
				if ( ! get_post_meta( $id, '_kata_builder_type', true ) ) {
					update_post_meta( $id, '_kata_builder_type', $this->builder_type );
				}

				if ( isset( $_REQUEST['elementor-preview'] ) && \Elementor\Plugin::$instance->preview->is_preview_mode() && get_post_type() == 'kata_plus_builder' ) {
					$id = get_the_ID();
				}

				$builder_type = get_post_meta( $id, '_kata_builder_type', true );

				if ( isset( $_REQUEST['elementor-preview'] ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {

					if ( $builder_type == 'kata_header' || $builder_type == 'kata_sticky_header' || $builder_type == 'kata_footer' ) {

						if ( $builder_type != $this->builder_type ) {
							return;
						}
					}
				}

				echo ! empty( $this->before ) ? $this->before : '';
				echo '<div class="kata-builder-wrap ' . esc_attr( sanitize_title( $this->name ) ) . '">';
				
				/**
				 * Sticky Header
				 */
				if ( $builder_type == 'kata_sticky_header' ) {

					echo '<div class="kata-sticky-box" id="box-1" data-pos-des="' . get_theme_mod( 'kata_sticky_box_position', 'top' ) . '" data-pos-tablet="' . get_theme_mod( 'kata_sticky_box_position_tablet', 'top' ) . '" data-pos-mobile="' . get_theme_mod( 'kata_sticky_box_position_mobile', 'top' ) . '" data-sec="' . get_theme_mod( 'kata_sticky_just_in_parent', 'no' ) . '">';
					echo Plugin::instance()->frontend->get_builder_content_for_display( $id, false );
					echo '</div>';
				} else {

					echo Plugin::instance()->frontend->get_builder_content_for_display( $id, false );
				}

				echo '</div>'; // kata-builder-wrap

				echo ! empty( $this->after ) ? $this->after : '';

				if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
					$css_file = new Post( $id );
				} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
					$css_file = new Post_CSS_File( $id );
				}

				$css_file->enqueue();

			}

			/**
			 * Get Post.
			 *
			 * @since   1.0.0
			 */
			public static function get_post_by_title( $post_title ) {

				if ( isset( $post_title ) && ! empty( $post_title ) ) {

					$post_id = get_page_by_title( $post_title, ARRAY_A, $post_type = 'kata_plus_builder' );

					if ( isset( $post_id['ID'] ) ) {

						return $post_id['ID'];
					} else {

						return false;
					}
				}
			}

			/**
			 * Create post type.
			 *
			 * @since   1.0.0
			 */
			public function create_builder_post_type() {

				register_post_type( 'kata_plus_builder', $this->args );
			}

			/**
			 * Enqueue Scripts
			 *
			 * @since     1.0.0
			 */
			public function enqueue_scripts() {

				if ( class_exists( '\Elementor\Plugin' ) ) {

					$elementor = \Elementor\Plugin::instance();
					$elementor->frontend->enqueue_styles();
				}

				if ( class_exists( '\ElementorPro\Plugin' ) ) {

					$elementor_pro = \ElementorPro\Plugin::instance();
					$elementor_pro->enqueue_styles();
				}
			}

			/**
			 * Trash Notice
			 *
			 * @since     1.0.0
			 */
			public function notice() {

				$id = self::get_post_by_title( $this->name );

				if ( get_post_status( $id ) == 'trash' ) {

					$restore_link = wp_nonce_url(
						"post.php?action=untrash&amp;post=$id",
						"untrash-post_$id"
					); ?>

					<div id="message" class="error notice is-dismissible">
						<p><?php echo esc_html( $this->name ) . ' ' . __( 'is in the Trash.', 'kata-plus' ); ?> <a href="<?php echo esc_url($restore_link); ?>"><?php echo __( 'Please restore it.', 'kata-plus' ); ?></a></p>
						<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo __('Dismiss this notice.', 'kata-plus'); ?></span></button>
					</div>
					<?php
				}
			}

		} // class
	}
}
