<?php
/**
 * Categories Lists
 *
 * Plugin core class, do not namespace.
 *
 * Improved categories plugin for Bludit CMS.
 *
 * @package    Categories Lists
 * @subpackage Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed direct access to this file.' );
}

// Access namespaced functions.
use function CatLists\{
	sidebar_list,
	count_cats
};

class Categories_Lists extends Plugin {

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Run parent constructor.
		parent :: __construct();

		// Include functionality.
		if ( $this->installed() ) {
			$this->get_files();
		}
	}

	/**
	 * Prepare plugin for installation
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function prepare() {
		$this->get_files();
	}

	/**
	 * Include functionality
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_files() {

		// Plugin path.
		$path = PATH_PLUGINS . 'categories-lists' . DS;

		// Get plugin functions.
		foreach ( glob( $path . 'includes/*.php' ) as $filename ) {
			require_once $filename;
		}
	}

	/**
	 * Initiate plugin
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @return void
	 */
	public function init() {

		// Access global variables.
		global $L;

		$this->dbFields = [
			'in_sidebar'  => true,
			'label'       => $L->get( 'Categories' ),
			'label_wrap'  => 'h2',
			'hide_empty'  => true,
			'display'     => 'all',
			'sort_by'     => 'abc',
			'post_count'  => true,
			'cats_select' => ['foobar'],
			'cats_sort'   => 'foobar',
			'list_view'   => 'vert'
		];

		// Array of custom hooks.
		$this->customHooks = [
			'categories_list'
		];

		if ( ! $this->installed() ) {
			$Tmp = new dbJSON( $this->filenameDb );
			$this->db = $Tmp->db;
			$this->prepare();
		}
	}

	/**
	 * Install plugin
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $position
	 * @return boolean Return true if the installation is successful.
	 */
	public function install( $position = 100 ) {

		// Create plugin directory for the database
		mkdir( PATH_PLUGINS_DATABASES . $this->directoryName, DIR_PERMISSIONS, true );

		$this->dbFields['position'] = $position;

		// Sanitize default values to store in the file.
		foreach ( $this->dbFields as $key => $value ) {

			if ( is_array( $value ) ) {
				$final_value = $value;
			} else {
				$value = Sanitize :: html( $value );
			}
			settype( $value, gettype( $this->dbFields[$key] ) );
			$this->db[$key] = $value;
		}

		// Create the database.
		return $this->save();
	}

	/**
	 * Form post
	 *
	 * The form `$_POST` method.
	 *
	 * Essentially the same as the parent method
	 * except that it allows for array field values.
	 *
	 * This was implemented to handle multi-checkbox
	 * and radio button fields. If strings are used
	 * in an array option then be sure to sanitize
	 * the string values.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function post() {

		$args = $_POST;

		foreach ( $this->dbFields as $field => $value ) {

			if ( isset( $args[$field] ) ) {

				// @todo Look into sanitizing array values.
				if ( is_array( $args[$field] ) ) {
					$final_value = $args[$field];
				} else {
					$final_value = Sanitize :: html( $args[$field] );
				}

				if ( $final_value === 'false' ) {
					$final_value = false;
				} elseif ( $final_value === 'true' ) {
					$final_value = true;
				}

				settype( $final_value, gettype( $value ) );
				$this->db[$field] = $final_value;
			}
		}

		return $this->save();
	}

	/**
	 * Admin settings form
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @global object $plugin Plugin class.
	 * @global object $site Site class.
	 * @return string Returns the markup of the form.
	 */
	public function form() {

		// Access global variables.
		global $L, $plugin, $site;

		$html  = '';
		ob_start();
		include( $this->phpPath() . '/views/page-form.php' );
		$html .= ob_get_clean();

		return $html;
	}

	/**
	 * Admin controller
	 *
	 * Change the text of the `<title>` tag.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L The Language class.
	 * @global array $layout
	 * @return string Returns the head content.
	 */
	public function adminController() {
		global $L, $layout, $site;
		$layout['title'] = $L->get( 'Categories Lists Guide' ) . ' | ' . $site->title();
	}

	/**
	 * Admin info pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @global object $site Site class.
	 * @return string Returns the markup of the page.
	 */
	public function adminView() {

		// Access global variables.
		global $L, $site;

		$html  = '';
		ob_start();
		include( $this->phpPath() . '/views/page-guide.php' );
		$html .= ob_get_clean();

		return $html;
	}

	/**
	 * Head section
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string Returns the head content.
	 */
	public function adminBodyEnd() {

		// Access global variables.
		global $L, $url;

		// Settings page URL.
		$settings = DOMAIN_ADMIN . 'configure-plugin/' . $this->className() . '#options';

		if ( checkRole( [ 'admin' ], false ) && 'categories' == $url->slug() ) {
			return sprintf(
				'<script>$( "table" ).before( "<div class=\'alert alert-primary alert-search-forms\' role=\'alert\'><p class=\'m-0\'><a href=\'%s\'>%s</a></p></div>");</script>',
				$settings,
				$L->get( 'Categories widget options' )
			);
		}
	}

	/**
	 * Head section
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string Returns the head content.
	 */
	public function siteHead() {

		$html = '';
		if ( 'horz' == $this->list_view() ) {
			$html .= '<style>';
			$html .= '.inline-taxonomy-list { list-style: none; display: inline-flex; flex-direction: row; flex-wrap: wrap; gap: 0 0.5em; }';
			$html .= '</style>';
		}
		return $html;
	}

	/**
	 * Sidebar list
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string Returns the list markup.
	 */
	public function siteSidebar() {

		// No list if no categories created.
		if ( count_cats() == 0 ) {
			return false;
		}

		// Print the list if the option is set.
		if ( $this->in_sidebar() ) {
			return sidebar_list();
		}
		return false;
	}

	/**
	 * Option return functions
	 *
	 * @since  1.0.0
	 * @access public
	 */

	// @return boolean
	public function in_sidebar() {
		return $this->getValue( 'in_sidebar' );
	}

	// @return string
	public function label() {
		return $this->getValue( 'label' );
	}

	// @return string
	public function label_wrap() {
		return $this->getValue( 'label_wrap' );
	}

	// @return boolean
	public function hide_empty() {
		return $this->getValue( 'hide_empty' );
	}

	// @return string
	public function display() {
		return $this->getValue( 'display' );
	}

	// @return string
	public function sort_by() {
		return $this->getValue( 'sort_by' );
	}

	// @return boolean
	public function post_count() {
		return $this->getValue( 'post_count' );
	}

	// @return array
	public function cats_select() {
		return $this->getValue( 'cats_select' );
	}

	// @return string
	public function cats_sort() {
		return $this->getValue( 'cats_sort' );
	}

	// @return string
	public function list_view() {
		return $this->getValue( 'list_view' );
	}

	/**
	 * Custom hook
	 *
	 * Prints the sidebar default list by
	 * calling the `categories_list' hook.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return string Returns the form markup.
	 */
	public function categories_list() {
		return sidebar_list();
	}
}
