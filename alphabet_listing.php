<?php
/*
Plugin Name: wordpress listing by alphabet
Description: Can be used to list post, page or categories with A to Z listing anywhere on your WordPress site
Version: 1.0.0
Author: Abhijeet
*/

/*  Copyright 2012-2013 Abhijeet Dhadve (email : abhi.dhadve@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//initialize class
if (!class_exists("AplhabetPlugin")) {
	define('AL_PATH', plugin_dir_path( __FILE__ ) ); 
	define('AL_URL', plugin_dir_url( __FILE__ ) ); 
	define('AL_NAME', 'Alphabet Listing');
	define('AL_DIRECTORY', 'a-to-z-listing-by-alphabet');
 	require_once(AL_PATH . '/alphabet_listing_main.php');
	
}

$wp_al_plugin = new AlphabetPlugin();

//Actions and Filters	
if (isset($wp_al_plugin)) {
	//Actions
	add_action( 'wp_enqueue_scripts', array($wp_al_plugin,'inject_css'));
	add_action( 'admin_init', array($wp_al_plugin,'aplhabet_listing_register_settings'));
	add_action( 'admin_menu', array($wp_al_plugin,'alphabet_listing_create_menu'));
	//register hooks
	register_activation_hook(__FILE__, array($wp_al_plugin,'aplhabet_listing_activate'));
	register_deactivation_hook(__FILE__, array($wp_al_plugin,'aplhabet_listing_deactivate'));
	//Filters
	add_shortcode( 'atoz', array($wp_al_plugin,'atoz_shortcode') );
}