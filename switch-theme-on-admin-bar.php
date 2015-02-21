<?php
/*
 * Plugin Name: Switch theme on admin bar
 * Plugin URI: http://xxxx7.com/switch-theme-on-admin-bar
 * Description: This plugin adds Theme menus in the admin bar. 
 * You can switch and activate the theme on the admin bar.
 * Version: 1.1
 * Author: xxxx7
 * Author URL: http://xxxx7.com/
 */

// This plugin is valid on the admin bar of blog
if ( is_admin() ) { return false; }

// To use wp_prepare_themes_for_js()
if ( ! file_exists( ABSPATH . 'wp-admin/includes/theme.php' ) ) { return false; }
require_once( ABSPATH . 'wp-admin/includes/theme.php' );
if ( ! function_exists( 'wp_prepare_themes_for_js' ) ) { return false; }


// to use ajax
// stoab is 'switch theme on admin bar', this is for avoiding name confliction
add_action( 'init', 'stoab_load_script' );
function stoab_load_script() {
    wp_enqueue_script( 'jquery' );
}


// add theme menu on admin bar of blog
add_action( 'admin_bar_menu', 'wp_admin_bar_theme_menu', 100 );
function wp_admin_bar_theme_menu($wp_admin_bar) {
	
	if ( ! is_admin_bar_showing() ) { return false; }
	wp_admin_bar_theme_top_menu( $wp_admin_bar );
	wp_admin_bar_theme_sub_menu( $wp_admin_bar );
	
}

// add theme top menu 
function wp_admin_bar_theme_top_menu($wp_admin_bar) {
	
	$style = '<style>#wp-admin-bar-theme .dashicons-before::before{top:3px;}</style>';
	$icon  = '<span class="ab-item dashicons-before dashicons-admin-appearance"></span>';
  
	$wp_admin_bar->add_menu( array(
		'id'    => 'theme',
		'title' => $style . $icon . 'theme',
		'href'  => admin_url( 'themes.php' ),
	) );
  
	$wp_admin_bar->add_group( array(
		'parent' => 'theme' ,
		'id'     => 'select-theme',
	) );
	
}

// add theme sub menu 
// this function uses wp_prepare_themes_for_js, so needs wordpress ver 3.8.0.
function wp_admin_bar_theme_sub_menu($wp_admin_bar){
	
	$themes = wp_prepare_themes_for_js();
	
	foreach ( $themes as $theme ) {
		$onclick = 'jQuery.get("' . $theme['actions']['activate'] . '","",';
		$onclick .= 'function(){location.reload();});';
		$wp_admin_bar->add_menu(
			array(
				'parent' => 'select-theme',
				'id'     => $theme['name'],
				'title'  => $theme['name'],
				'href'   => '#', 
				'meta'   => array(
					'onclick' => $onclick,
				),
			)
		);
	}

}