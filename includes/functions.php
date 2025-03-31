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
 * Plugin instance
 *
 * @since  1.0.0
 * @return object
 */
function plugin() {
	return new \Categories_Lists;
}

/**
 * Categories database
 *
 * @since  1.0.0
 * @global object $categories The Categories class.
 * @return mixed False if no categories in database.
 */
function cats_db() {

	// Access global variables.
	global $categories;

	if ( 0 == count( $categories->db ) ) {
		return false;
	}
	return $categories->getDB();
}

/**
 * Get categories
 *
 * Gets all available categories.
 *
 * @param  string $get `key`, `key_name`, or `name`
 * @return mixed False if no categories in database.
 */
function get_cats( $get = 'key' ) {

	// False if no categories in the database.
	if ( 0 == count_cats() ) {
		return false;
	}

	$cats = [];
	foreach ( cats_db() as $key => $cat ) {

		if ( 'key_name' == $get ) {
			$entry = [ $key => $cat['name'] ];
			$cats  = array_merge( $cats, $entry );
		} elseif ( 'name' == $get ) {
			$cats[] = $cat['name'];
		} else {
			$cats[] = $key;
		}
	}
	return $cats;
}

/**
 * Count categories
 *
 * Total number of categories in the
 * database, including those not used
 * for any post.
 *
 * @since  1.0.0
 * @return integer
 */
function count_cats() {
	return count( cats_db() );
}

/**
 * Get categories by post count
 *
 * @since  1.0.0
 * @global object $categories The Categories class.
 * @return array
 */
function cats_by_count() {

	// Access global variables.
	global $categories;

	if ( 0 == count( $categories->db ) ) {
		return false;
	}
	usort( $categories->db, function( $a, $b ) {
		return count( $a['list'] ) < count( $b['list'] );
	} );
	return $categories->db;
}

/**
 * Selected categories
 *
 * From the checkbox list of categories in the form.
 *
 * @since  1.0.0
 * @return array
 */
function selected_cats() {

	$cats = [];
	foreach ( plugin()->cats_select() as $key ) {

		if ( ! getCategory( $key ) ) {
			continue;
		}

		$cat = getCategory( $key );
		$select = [
			$key => [
				'key'         => $key,
				'name'        => $cat->name(),
				'template'    => $cat->template(),
				'description' => $cat->description(),
				'list'        => $cat->pages()
			]
		];
		$cats = array_merge( $cats, $select );
	}

	if ( 'count' == plugin()->sort_by() ) {
		usort( $cats, function( $a, $b ) {
			return count( $a['list'] ) < count( $b['list'] );
		} );
	}
	return $cats;
}

/**
 * Categories list
 *
 * @since  1.0.0
 * @param  mixed $args Arguments to be passed.
 * @param  array $defaults Default arguments.
 * @return string Returns the list markup.
 */
function cats_list( $args = null, $defaults = [] ) {

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

	// Maybe sort by post count.
	if ( 'count' == $args['sort_by'] ) {
		$cats = cats_by_count();
	} else {
		$cats = cats_db();
	}

	foreach ( $cats as $key => $value ) {

		$get_count = count( $value['list'] );
		$get_name  = $value['name'];

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

	// Selected categories, maybe sort by manual order.
	$cats  = selected_cats();
	$sort  = plugin()->cats_sort();
	$order = [];
	if ( 'select' == plugin()->display() ) {
		if ( 'sort' == plugin()->sort_by() && ! empty( $sort ) ) {
			$order = explode( ',', $sort );
			if ( ! getCategory( $order[0] ) ) {
				$order = [];
			}
			$cats = array_replace( array_flip( $order ), $cats );
		}
	// Sort by post count.
	} elseif ( 'all' == plugin()->display() && 'count' == plugin()->sort_by() ) {
		$cats = cats_by_count();

	// Default to all categories.
	} else {
		$cats = cats_db();
	}

	// Label wrapping elements.
	$get_open  = str_replace( ',', '><', plugin()->label_wrap() );
	$get_close = str_replace( ',', '></', plugin()->label_wrap() );

	$label_el_open  = "<{$get_open}>";
	$label_el_close = "</{$get_close}>";

	// List class.
	$list_class = 'categories-list standard-taxonomy-list';
	if ( 'horz' == plugin()->list_view() ) {
		$list_class = 'categories-list inline-taxonomy-list';
	}

	// List markup.
	$html = '<div class="list-wrap cats-list-wrap-wrap plugin plugin-cats-list">';
	if ( ! empty( plugin()->label() ) ) {
		$html .= sprintf(
			'%1$s%2$s%3$s',
			$label_el_open,
			plugin()->label(),
			$label_el_close
		);
	}
	if ( checkRole( [ 'admin' ], false ) && 'select' == plugin()->display() && ! selected_cats() ) {
		$html .= sprintf(
			'<p>%s</p></div>',
			$L->get( 'No categories selected.' )
		);
		return $html;
	} elseif ( checkRole( [ 'admin' ], false ) && 'select' == plugin()->display() && ! getCategory( $order[0] ) ) {
		$html .= sprintf(
			'<p>%s</p></div>',
			$L->get( 'Sort categories to display.' )
		);
		return $html;
	} elseif ( 'select' == plugin()->display() && ( ! getCategory( $order[0] ) || ! selected_cats() ) ) {
		return;
	}
	$html .= sprintf(
		'<ul class="%s">',
		$list_class
	);

	// List entries.
	foreach ( $cats as $key => $value ) {

		if ( ! array_key_exists( 'name', $value ) ) {
			continue;
		}

		$get_count = count( $value['list'] );
		$get_name  = $value['name'];

		// Hide empty categories.
		if ( $get_count == 0 && plugin()->hide_empty() ) {
			continue;
		}

		$name = $get_name;
		if ( plugin()->post_count() ) {
			$name = sprintf(
				'%s (%s)',
				$get_name,
				$get_count
			);
		}
		$html .= sprintf(
			'<li><a href="%s">%s</a></li>',
			DOMAIN_CATEGORIES . $key,
			$name
		);
	}
	$html .= '</ul></div>';

	return $html;
}
