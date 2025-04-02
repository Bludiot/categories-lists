<?php
/**
 * Categories list options
 *
 * @package    Categories Lists
 * @subpackage Views
 * @category   Forms
 * @since      1.0.0
 */

// Access namespaced functions.
use function CatLists\{
	get_cats,
	selected_cats,
	count_cats
};

// Guide page URL.
$guide_page = DOMAIN_ADMIN . 'plugin/' . $this->className();

if ( count_cats() == 0 ) {
	printf(
		'<p>%s</p><p><a href="%s">%s</a></p>',
		$L->get( 'You must create at least one category to access this options form.' ),
		DOMAIN_ADMIN . 'categories',
		$L->get( 'Go to the Categories page.' )
	);
	return;
}

?>
<style>
.form-control-has-button {
	display: flex;
	align-items: center;
	flex-wrap: nowrap;
	gap: 0.25em;
	width: 100%;
	margin: 0;
	padding: 0;
}
.checkbox-group {
	display: flex;
	gap: 0.125em 0.75em;
	flex-wrap: wrap;
	margin-top: 1rem;
}
.check-label-wrap {
	display: inline-block;
	cursor: pointer;
}
.screen-reader-text {
	border: 0;
	clip: rect( 1px, 1px, 1px, 1px );
	-webkit-clip-path: inset(50%);
	        clip-path: inset(50%);
	height: 1px;
	margin: -1px;
	overflow: hidden;
	padding: 0;
	position: absolute !important;
	width: 1px;
	word-wrap: normal !important;
}
</style>
<div class="alert alert-primary alert-cats-list" role="alert">
	<p class="m-0"><?php $L->p( "Go to the <a href='{$guide_page}'>categories lists guide</a> page." ); ?></p>
</div>

<fieldset class="mt-4">
	<legend class="screen-reader-text mb-3"><?php $L->p( 'Sidebar List Options' ) ?></legend>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="in_sidebar"><?php echo ucwords( $L->get( 'Sidebar List' ) ); ?></label>
		<div class="col-sm-10">
			<select class="form-select" id="in_sidebar" name="in_sidebar">
				<option value="true" <?php echo ( $this->getValue( 'in_sidebar' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>

				<option value="false" <?php echo ( $this->getValue( 'in_sidebar' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>
			</select>
			<small class="form-text"><?php $L->p( 'Display a categories list in the sidebar ( <code>siteSidebar</code> hook required in the theme ).' ); ?></small>
		</div>
	</div>

	<div id="categories-lists-options" style="display: <?php echo ( $this->getValue( 'in_sidebar' ) == true ? 'block' : 'none' ); ?>;">

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for=""><?php $L->p( 'List Label' ); ?></label>
			<div class="col-sm-10">
				<div class="form-control-has-button">
					<input type="text" id="label" name="label" value="<?php echo $this->getValue( 'label' ); ?>" placeholder="<?php echo $this->dbFields['label']; ?>" />
					<span class="btn btn-secondary btn-md button hide-if-no-js" onClick="$('#label').val('<?php echo $this->dbFields['label']; ?>');"><?php $L->p( 'Default' ); ?></span>
				</div>
				<small class="form-text text-muted"><?php $L->p( 'List title in the sidebar. Save as empty for no title.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="label_wrap"><?php $L->p( 'Label Wrap' ); ?></label>
			<div class="col-sm-10">
				<div class="form-control-has-button">
					<input type="text" id="label_wrap" name="label_wrap" value="<?php echo $this->getValue( 'label_wrap' ); ?>" placeholder="<?php $L->p( 'h2' ); ?>" />
					<span class="btn btn-secondary btn-md button hide-if-no-js" onClick="$('#label_wrap').val('<?php echo $this->dbFields['label_wrap']; ?>');"><?php $L->p( 'Default' ); ?></span>
				</div>
				<small class="form-text text-muted"><?php $L->p( 'Wrap the label in an element, such as a heading. Accepts HTML tags without brackets (e.g. h3), and comma-separated tags (e.g. span,strong,em). Save as blank for no wrapping element.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="display"><?php $L->p( 'Display' ); ?></label>
			<div class="col-sm-10">
				<select id="display" class="form-select" name="display">
					<option value="all" <?php echo ( $this->getValue( 'display' ) === 'all' ? 'selected' : '' ); ?>><?php $L->p( 'All Categories' ); ?></option>
					<option value="select" <?php echo ( $this->getValue( 'display' ) === 'select' ? 'selected' : '' ); ?>><?php $L->p( 'Select Categories' ); ?></option>
				</select>
				<small class="form-text text-muted"><?php $L->p( 'Show select categories or all categories.' ); ?></small>
			</div>
		</div>

		<div id="cats-select" class="form-field form-group row" style="display: <?php echo ( $this->getValue( 'display' ) === 'select' ? 'flex' : 'none' ); ?>;">
			<label class="form-label col-sm-2 col-form-label" for="cats_select"><?php $L->p( 'Categories' ); ?></label>

			<div class="col-sm-10">
				<p><?php $L->p( 'Which categories shall display in the sidebar list.' ); ?></p>
				<div class="checkbox-group">
				<?php
				foreach ( get_cats( 'key_name' ) as $cat => $name ) {
					printf(
						'<label class="check-label-wrap" for="cat-%s" title="%s"><input type="checkbox" name="cats_select[]" id="cat-%s" value="%s" %s /> %s</label>',
						$cat,
						$title,
						$cat,
						$cat,
						( is_array( $this->cats_select() ) && in_array( $cat, $this->cats_select() ) ? 'checked' : '' ),
						$name
					);
				} ?>
				<label style="display: none;" class="check-label-wrap" for="foobar"><input type="checkbox" name="cats_select[]" id="cat-%s" value="foobar" checked /></label>
				</div>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="sort_by"><?php $L->p( 'Sort Order' ); ?></label>
			<div class="col-sm-10">
				<select id="sort-by" class="form-select" name="sort_by">
					<option id="sort-by-abc-option" value="abc" <?php echo ( $this->getValue( 'sort_by' ) === 'abc' ? 'selected' : '' ); ?> style="display: <?php echo ( $this->display() == 'all' ? 'block' : 'none' ); ?>;"><?php $L->p( 'Alphabetically' ); ?></option>

					<option id="sort-by-count-option" value="count" <?php echo ( $this->getValue( 'sort_by' ) === 'count' ? 'selected' : '' ); ?>><?php $L->p( 'Post Count' ); ?></option>

					<option id="sort-by-order-option" value="sort" <?php echo ( $this->getValue( 'sort_by' ) === 'sort' && $this->display() == 'select' ? 'selected' : '' ); ?> style="display: <?php echo ( $this->display() == 'select' ? 'block' : 'none' ); ?>;"><?php $L->p( 'Sort Order' ); ?></option>
				</select>
				<small class="form-text text-muted"><?php $L->p( 'Order of the categories list display.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="hide_empty"><?php $L->p( 'Hide Empty' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" name="hide_empty">
					<option value="true" <?php echo ( $this->getValue( 'hide_empty' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Hide Empty' ); ?></option>
					<option value="false" <?php echo ( $this->getValue( 'hide_empty' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Show Empty' ); ?></option>
				</select>
				<small class="form-text text-muted"><?php $L->p( 'Hide categories with no posts or pages attached.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="post_count"><?php $L->p( 'Post Count' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" name="post_count">
					<option value="true" <?php echo ( $this->getValue( 'post_count' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
					<option value="false" <?php echo ( $this->getValue( 'post_count' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>
				</select>
				<small class="form-text text-muted"><?php $L->p( 'Display the number of posts and pages attached to the category.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="list_view"><?php $L->p( 'List Direction' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" name="list_view">
					<option value="vert" <?php echo ( $this->getValue( 'list_view' ) === 'vert' ? 'selected' : '' ); ?>><?php $L->p( 'Vertical' ); ?></option>
					<option value="horz" <?php echo ( $this->getValue( 'list_view' ) === 'horz' ? 'selected' : '' ); ?>><?php $L->p( 'Horizontal' ); ?></option>
				</select>
				<small class="form-text text-muted"><?php $L->p( 'How to display the categories list.' ); ?></small>
			</div>
		</div>
	</div>

	<div id="sort-wrap" class="<?php echo ( $this->display() == 'select' ? '' : 'screen-reader-text' ); ?>">
		<h3 class="tab-section-heading"><?php $L->p( 'Sidebar List Order' ); ?></h3>

		<?php
		$order = explode( ',', $this->cats_sort() );
		if ( ! selected_cats() ) : ?>
		<p class="tab-section-description text-danger"><strong><?php $L->p( 'Select categories then save the form to sort.' ); ?></strong></p>

		<?php
		elseif ( ! getCategory( $order[0] ) ) : ?>
		<p class="tab-section-description text-danger"><strong><?php $L->p( 'Save the form before setting the sort order!' ); ?></strong></p>
		<?php endif; ?>

		<p class="tab-section-description"><?php $L->p( 'Sort the order of categories in the sidebar list.' ); ?></p>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="cats_sort"><?php $L->p( 'Sort Categories' ); ?></label>
			<div class="col-sm-10">
				<?php
				$all  = get_cats();
				$cats = selected_cats();
				$sort = $this->cats_sort();
				if ( ! empty( $sort ) ) {
					$order = explode( ',', $sort );
					$list  = array_replace( array_flip( $order ), $cats );
				} else {
					$list = $cats;
				}

				if ( getCategory( $sort[0] ) ) {
					$sort_val = $sort;
				} else {
					$sort_val = $all[0];
				}
				?>
				<input type="hidden" id="cats_sort" name="cats_sort" value="<?php echo $sort_val; ?>" />

				<small><?php $L->p( 'Drag & drop to set the list order of categories, then save the form.' ); ?></small>

				<ul id="cats-sort" class="list-group list-group-sortable">
				<?php
				foreach ( $list as $selected => $cat ) {
					if ( ! array_key_exists( $selected, $cats ) ) {
						continue;
					}
					echo '<li class="list-group-item" data-cat="' . $selected . '"><span class="fa fa-arrows-v"></span> ' . $cat['name'] . '</li>';
				} ?>
				</ul>
			</div>
		</div>
		<script>
		$(document).ready( function() {
			$( '.list-group-sortable' ).sortable({
				placeholderClass: 'list-group-item'
			});

			$( '#jsform button[type="submit"]' ).on( 'click', function() {
				var tmp = [];

				$( 'li.list-group-item' ).each( function() {
					tmp.push( $(this).attr( 'data-cat' ) );
				});
				$( '#cats_sort' ).attr( 'value', tmp.join( ',' ) );
				$( '#jsform' ).submit();
			});
		});
		</script>
	</div>
</fieldset>

<script>
jQuery(document).ready( function($) {
	$( '#in_sidebar' ).on( 'change', function() {
		var show = $(this).val();
		if ( show == 'true' ) {
			$( "#categories-lists-options" ).fadeIn( 250 );
		} else if ( show == 'false' ) {
			$( "#categories-lists-options" ).fadeOut( 250 );
		}
	});

	$( '#display' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'select' == show ) {
			$( '#sort-by-abc-option' ).css( 'display', 'none' );
			$( '#sort-by-order-option' ).css( 'display', 'block' );
			$( '#cats-select' ).css( 'display', 'flex' );
			$( '#sort-wrap' ).css( 'display', 'block' );
		} else {
			$( '#sort-by-abc-option' ).css( 'display', 'block' );
			$( '#sort-by-order-option' ).css( 'display', 'none' );
			$( '#cats-select' ).css( 'display', 'none' );
			$( '#sort-wrap' ).css( 'display', 'none' );
		}
	});
	$( '#sort-by' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'sort' == show ) {
			$( '#sort-wrap' ).removeClass( 'screen-reader-text' );
		} else {
			$( '#sort-wrap' ).addClass( 'screen-reader-text' );
		}
	});
});
</script>
