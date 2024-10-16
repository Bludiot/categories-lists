<?php
/**
 * Categories list options
 *
 * @package    Categories Lists
 * @subpackage Views
 * @category   Forms
 * @since      1.0.0
 */

// Guide page URL.
$guide_page = DOMAIN_ADMIN . 'plugin/Categories_Lists';

// Tags page URL.
$tags_page = DOMAIN_ADMIN . 'configure-plugin/Tags_Lists';

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
</style>
<div class="alert alert-primary alert-cats-list" role="alert">
	<p class="m-0"><?php $L->p( "Go to the <a href='{$guide_page}'>categories lists guide</a> page." ); if ( getPlugin( 'Tags_Lists' ) ) { echo ' '; $L->p( "Go to the <a href='{$tags_page}'>tags settings</a> page." ); } ?></p>
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
			<label class="form-label col-sm-2 col-form-label" for="sort_by"><?php $L->p( 'Sort Order' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" name="sort_by">
					<option value="abc" <?php echo ( $this->getValue( 'sort_by' ) === 'abc' ? 'selected' : '' ); ?>><?php $L->p( 'Alphabetically' ); ?></option>
					<option value="count" <?php echo ( $this->getValue( 'sort_by' ) === 'count' ? 'selected' : '' ); ?>><?php $L->p( 'Post Count' ); ?></option>
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
});
</script>
