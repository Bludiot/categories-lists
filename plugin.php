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
	sidebar_list
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
			'in_sidebar' => true,
			'label'      => $L->get( 'Categories' ),
			'label_wrap' => 'h2',
			'hide_empty' => true,
			'sort_by'    => 'abc',
			'post_count' => true,
			'list_view'  => 'vert'
		];

		if ( ! $this->installed() ) {
			$Tmp = new dbJSON( $this->filenameDb );
			$this->db = $Tmp->db;
			$this->prepare();
		}
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

		$html = '<style>';
		$html .= '.inline-taxonomy-list { list-style: none; display: inline-flex; flex-direction: row; flex-wrap: wrap; gap: 0 0.5em; }';
		$html .= '</style>';

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
	public function sort_by() {
		return $this->getValue( 'sort_by' );
	}

	// @return boolean
	public function post_count() {
		return $this->getValue( 'post_count' );
	}

	// @return string
	public function list_view() {
		return $this->getValue( 'list_view' );
	}
}
