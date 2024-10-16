<?php
/**
 * Functions
 *
 * @package    Categories Lists
 * @subpackage Core
 * @category   Functions
 * @since      1.0.0
 */

namespace CatLists;

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed direct access to this file.' );
}

/**
 * Categories list
 *
 * @since  1.0.0
 * @param  mixed $args Arguments to be passed.
 * @param  array $defaults Default arguments.
 * @global object $categories The Categories class.
 * @return string Returns the list markup.
 */
function cats_list( $args = null, $defaults = [] ) {

	// Access global variables.
	global $categories;

	// Default arguments.
	$defaults = [
		'wrap'       => false,
		'wrap_class' => 'list-wrap cats-list-wrap',
		'direction'  => 'vert', // horz or vert
		'list_class' => 'categories-list standard-taxonomy-list',
		'label'      => false,
		'label_el'   => 'h2',
		'links'      => true,
		'sort_by'    => 'abc', // abc or count
		'show_count' => false,
		'hide_empty' => true
	];

	// Maybe override defaults.
	if ( is_array( $args ) && $args ) {
		if ( isset( $args['direction'] ) && 'horz' == $args['direction'] && ! isset( $args['list_class'] ) ) {
			$defaults['list_class'] = 'categories-list inline-taxonomy-list';
		}
		$args = array_merge( $defaults, $args );
	} else {
		$args = $defaults;
	}

	// Label wrapping elements.
	$get_open  = str_replace( ',', '><', $args['label_el'] );
	$get_close = str_replace( ',', '></', $args['label_el'] );

	$label_el_open  = "<{$get_open}>";
	$label_el_close = "</{$get_close}>";

	// List markup.
	$html = '';
	if ( $args['wrap'] ) {
		$html = sprintf(
			'<div class="%s">',
			$args['wrap_class']
		);
	}
	if ( $args['label'] ) {
		$html .= sprintf(
			'%1$s%2$s%3$s',
			$label_el_open,
			$args['label'],
			$label_el_close
		);
	}
	$html .= sprintf(
		'<ul class="%s">',
		$args['list_class']
	);

	// Alias categories database array.
	$cats_db = $categories->db;

	// Maybe sort by post count.
	if ( 'count' == $args['sort_by'] ) {
		usort( $cats_db, function( $a, $b ) {
			return count( $a['list'] ) < count( $b['list'] );
		} );
	}

	foreach ( $cats_db as $key => $fields ) {

		$get_count = count( $fields['list'] );
		$get_name  = $fields['name'];

		// Hide empty categories.
		if ( $get_count == 0 && $args['hide_empty'] ) {
			continue;
		}

		$name = $get_name;
		if ( $args['show_count'] ) {
			$name = sprintf(
				'%s (%s)',
				$get_name,
				$get_count
			);
		}
		$html .= '<li>';
		if ( $args['links'] ) {
			$html .= '<a href="' . DOMAIN_CATEGORIES . $key . '">';
		}
		$html .= $name;
		if ( $args['links'] ) {
			$html .= '</a>';
		}
		$html .= '</li>';
	}
	$html .= '</ul>';

	if ( $args['wrap'] ) {
		$html .= '</div>';
	}

	return $html;
}

/**
 * Sidebar list
 *
 * @since  1.0.0
 * @global object $L The Language class.
 * @return string Returns the list markup.
 */
function sidebar_list() {

	// Access global variables.
	global $L;

	// Get the plugin object.
	$plugin = new \Categories_Lists;

	// Override default function arguments.
	$args = [
		'wrap'       => true,
		'wrap_class' => 'list-wrap cats-list-wrap-wrap plugin plugin-cats-list'
	];

	$args['label_el'] = $plugin->label_wrap();

	if ( ! empty( $plugin->label() ) ) {
		$args['label'] = $plugin->label();
	}

	if ( 'horz' == $plugin->list_view() ) {
		$args['direction'] = 'horz';
	}

	if ( 'count' == $plugin->sort_by() ) {
		$args['sort_by'] = 'count';
	}

	if ( $plugin->post_count() ) {
		$args['show_count'] = true;
	}

	if ( ! $plugin->hide_empty() ) {
		$args['hide_empty'] = false;
	}

	// Return a modified list.
	return cats_list( $args );
}
