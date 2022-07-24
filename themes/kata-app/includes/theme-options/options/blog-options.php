<?php
/**
 * Blog Options.
 *
 * @author  ClimaxThemes
 * @package Kata Plus
 * @since   1.0.0
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Kata_Theme_Options_Blog' ) ) {
	class Kata_Theme_Options_Blog extends Kata_Theme_Options {
		/**
		 * Set Options.
		 *
		 * @since   1.0.0
		 */
		public static function set_options() {
			// Blog panel
			Kirki::add_panel(
				'kata_blog_panel',
				[
					'title'      => esc_html__( 'Blog', 'kata-app' ),
					'icon'       => 'ti-pencil-alt',
					'type' 		 => 'kirki-nested',
					'capability' => kata_Helpers::capability(),
					'priority'   => 4,
				]
			);
			Kirki::add_panel(
				'kata_blog_and_archive_panel',
				[
					'title'      => esc_html__( 'Blog', 'kata-app' ),
					'capability' => kata_Helpers::capability(),
					'icon' 		 => 'ti-layout-list-post',
					'panel'      => 'kata_blog_panel',
					'priority'   => 4,
				]
			);
			Kirki::add_panel(
				'kata_blog_post_single_panel',
				[
					'title'      => esc_html__( 'Single', 'kata-app' ),
					'capability' => kata_Helpers::capability(),
					'icon' 		 => 'ti-layout-list-post',
					'panel'      => 'kata_blog_panel',
					'priority'   => 4,
				]
			);

			// First Posts
			Kirki::add_section(
				'kata_blog_first_post_section',
				[
					'panel'      => 'kata_blog_and_archive_panel',
					'title'      => esc_html__( 'First Post', 'kata-app' ),
					'capability' => kata_Helpers::capability(),
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'sortable',
					'section'     => 'kata_blog_first_post_section',
					'settings'    => 'kata_blog_first_post_sortable_setting',
					'label'       => esc_html__( 'Post Structure', 'kata-app' ),
					'default'     => [
						'kata_post_thumbnail',
						'kata_post_content',
					],
					'choices'     => [
						'kata_post_thumbnail'		=> esc_html__( 'Thumbnail', 'kata-app' ),
						'kata_post_content'			=> esc_html__( 'Content', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'sortable',
					'section'     => 'kata_blog_first_post_section',
					'settings'    => 'kata_blog_first_post_metadata_sortable_setting',
					'label'       => esc_html__( 'Metadata Structure', 'kata-app' ),
					'default'     => [
						'kata_post_date',
						'kata_post_author',
					],
					'choices'     => [
						'kata_post_date'	=> esc_html__( 'Date', 'kata-app' ),
						'kata_post_author' 	=> esc_html__( 'Author', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'settings'        => 'kata_blog_first_posts_excerpt_length',
					'section'         => 'kata_blog_first_post_section',
					'label'           => esc_html__('Excerpt length', 'kata-app'),
					'description'     => esc_html__('Sets the post excerpt length size', 'kata-app'),
					'type'            => 'slider',
					'default'         => 15,
					'choices'         => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				]
			);

			// Posts
			Kirki::add_section(
				'kata_blog_posts_section',
				[
					'panel'      => 'kata_blog_and_archive_panel',
					'title'      => esc_html__( 'Posts', 'kata-app' ),
					'capability' => kata_Helpers::capability(),
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'select',
					'section'     => 'kata_blog_posts_section',
					'settings'    => 'kata_blog_posts_thumbnail_pos',
					'label'       => esc_html__( 'Thumbnail Position', 'kata-app' ),
					'default'     => 'left',
					'choices'     => [
						'left'	=> esc_html__( 'Left', 'kata-app' ),
						'right'	=> esc_html__( 'Right', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'sortable',
					'section'     => 'kata_blog_posts_section',
					'settings'    => 'kata_blog_posts_sortable_setting',
					'label'       => esc_html__( 'Post Structure', 'kata-app' ),
					'default'     => [
						'kata_post_categories',
						'kata_post_title',
						'kata_post_post_excerpt',
					],
					'choices'     => [
						'kata_post_categories'		=> esc_html__( 'Category', 'kata-app' ),
						'kata_post_title'			=> esc_html__( 'Title', 'kata-app' ),
						'kata_post_post_excerpt'	=> esc_html__( 'Post Excerpt', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'sortable',
					'section'     => 'kata_blog_posts_section',
					'settings'    => 'kata_blog_posts_metadata_sortable_setting',
					'label'       => esc_html__( 'Metadata Structure', 'kata-app' ),
					'default'     => [
						'kata_post_date',
						'kata_post_author',
					],
					'choices'     => [
						'kata_post_date'	=> esc_html__( 'Date', 'kata-app' ),
						'kata_post_author' 	=> esc_html__( 'Author', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'settings'        => 'kata_blog_posts_excerpt_length',
					'section'         => 'kata_blog_posts_section',
					'label'           => esc_html__('Excerpt length', 'kata-app'),
					'description'     => esc_html__('Sets the post excerpt length size', 'kata-app'),
					'type'            => 'slider',
					'default'         => 15,
					'choices'         => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				]
			);

			// Single
			Kirki::add_section(
				'kata_post_single_section',
				[
					'panel'      => 'kata_blog_panel',
					'icon'       => 'ti-pencil-alt',
					'title'      => esc_html__( 'Post Single', 'kata-app' ),
					'capability' => kata_Helpers::capability(),
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'switch',
					'section'     => 'kata_post_single_section',
					'settings'    => 'kata_post_single_thumbnail',
					'label'       => esc_html__( 'Post Thumbnail', 'kata-app' ),
					'default'     => '1',
					'priority'    => 10,
					'choices'     => [
						'on'  => esc_html__( 'Enable', 'kata-app' ),
						'off' => esc_html__( 'Disable', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'switch',
					'section'     => 'kata_post_single_section',
					'settings'    => 'kata_post_single_categories',
					'label'       => esc_html__( 'Post Categories', 'kata-app' ),
					'default'     => '1',
					'priority'    => 10,
					'choices'     => [
						'on'  => esc_html__( 'Enable', 'kata-app' ),
						'off' => esc_html__( 'Disable', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'switch',
					'section'     => 'kata_post_single_section',
					'settings'    => 'kata_post_single_title',
					'label'       => esc_html__( 'Post Title', 'kata-app' ),
					'default'     => '1',
					'priority'    => 10,
					'choices'     => [
						'on'  => esc_html__( 'Enable', 'kata-app' ),
						'off' => esc_html__( 'Disable', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'switch',
					'section'     => 'kata_post_single_section',
					'settings'    => 'kata_post_single_date',
					'label'       => esc_html__( 'Post Date', 'kata-app' ),
					'default'     => '1',
					'priority'    => 10,
					'choices'     => [
						'on'  => esc_html__( 'Enable', 'kata-app' ),
						'off' => esc_html__( 'Disable', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'switch',
					'section'     => 'kata_post_single_section',
					'settings'    => 'kata_post_single_author',
					'label'       => esc_html__( 'Post Author', 'kata-app' ),
					'default'     => '1',
					'priority'    => 10,
					'choices'     => [
						'on'  => esc_html__( 'Enable', 'kata-app' ),
						'off' => esc_html__( 'Disable', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'switch',
					'section'     => 'kata_post_single_section',
					'settings'    => 'kata_post_single_tags',
					'label'       => esc_html__( 'Post Tags', 'kata-app' ),
					'default'     => '1',
					'priority'    => 10,
					'choices'     => [
						'on'  => esc_html__( 'Enable', 'kata-app' ),
						'off' => esc_html__( 'Disable', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
			Kirki::add_field(
				self::$opt_name,
				[
					'type'        => 'switch',
					'section'     => 'kata_post_single_section',
					'settings'    => 'kata_post_single_socials',
					'label'       => esc_html__( 'Post Socials', 'kata-app' ),
					'default'     => '1',
					'priority'    => 10,
					'choices'     => [
						'on'  => esc_html__( 'Enable', 'kata-app' ),
						'off' => esc_html__( 'Disable', 'kata-app' ),
					],
					'priority'    => 10,
				]
			);
		}
	} // class

	Kata_Theme_Options_Blog::set_options();
}
