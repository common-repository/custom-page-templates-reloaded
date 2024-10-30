<?php
/*
Plugin Name: Custom Page Templates Reloaded
Description: Page Templates, ignited.
Version: 0.1.1
Author: Hassan Derakhshandeh

		* 	Copyright (C) 2011  Hassan Derakhshandeh
		*	http://tween.ir/
		*	hassan.derakhshandeh@gmail.com

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

class Custom_Page_Templates_Reloaded {

	private $textdomain;

	function Custom_Page_Templates_Reloaded() {
		add_action( 'admin_head-post-new.php', array( &$this, 'add_custom_option' ) );
		add_action( 'admin_head-post.php', array( &$this, 'add_custom_option' ) );
		add_action( 'edit_page_form', array( &$this, 'custom_fields' ) );
		add_action( 'admin_print_styles-post.php', array( &$this, 'admin_queue' ) );
		add_action( 'admin_print_styles-post-new.php', array( &$this, 'admin_queue' ) );
		add_action( 'save_post', array( &$this, 'save_post' ) );
		add_action( 'template_redirect', array( &$this, 'do_template' ) );
	}

	/**
	 * Checks if current page has a custom template and renders it.
	 *
	 * @since 0.1
	 * @return void
	 */
	function do_template() {
		global $post;

		if( is_page() && ( get_post_meta( $post->ID, '_is_custom_template', true ) == 1 ) ) {
			$template = trim( get_post_meta( $post->ID, '_custom_template', true ) );
			$template = trim( $template, '<?php' );
			eval( $template );
			die();
		}
	}

	function custom_fields() {
		global $post; ?>
		<input type="hidden" name="custom_template" id="custom-template" value="<?php echo esc_html( get_post_meta( $post->ID, '_custom_template', true ) ); ?>" />
	<?php }

	function add_custom_option() {
		global $post;
	?>
		<script>
			jQuery(function($){
				var editor;

				function open_editor() {
					$.get( "<?php echo plugins_url( 'screens/edit-template.php?post_id=' . $post->ID, __FILE__ ) ?>", function(b) {
						$("#edit-template").remove();
						$(b).find('textarea').val($('#custom-template').val()).end().hide().appendTo('body');
						var width = $(window).width(),
							height = $(window).height();
						width = 720 < width ? 720 : width;
						width -= 80;
						height -= 84;
						tb_show( "<?php _e( 'Custom Template', $this->textdomain ) ?>", "#TB_inline?width=" + width + "&height=" + height + "&inlineId=edit-template");
						editor = CodeMirror.fromTextArea($('#TB_window textarea')[0], {
							lineNumbers: true,
							matchBrackets: true,
							mode: "application/x-httpd-php",
							indentUnit: 4,
							indentWithTabs: true,
							enterMode: "keep",
							tabMode: "shift",
						});
						$('div.CodeMirror-scroll, div.CodeMirror-gutter').height(height-100);
					});
				}

				<?php /* Add the Page Template option if it doesn't exists */ ?>
				if( $('#page_template').length < 1 ) {
					$('#parent_id').after('<p><strong><?php _e('Template') ?></strong></p><label class="screen-reader-text" for="page_template"><?php _e('Page Template') ?></label><select name="page_template" id="page_template"><option value="default"><?php _e('Default Template'); ?></option></select>');
				}
				$('#page_template')
					.after('<a style="display: none" href="#"><img style="vertical-align: middle;" src="<?php echo plugins_url( 'images/edit.png', __FILE__ ) ?>" width="20" height="20" alt="<?php _e( 'Edit Template', $this->textdomain ) ?>" /></a>')
					.append('<option value="custom" <?php if( 1 == get_post_meta( $post->ID, '_is_custom_template', true ) ) echo 'selected="selected"' ?>><?php _e('Custom') ?></option>')
					.change(function(){
						if( $(this).val() == 'custom' ) {
							$(this).next().fadeIn();
						} else {
							$(this).next().fadeOut();
						}
					})
					.trigger('change')
					.next()
						.click(function(){
							open_editor();
							return false;
						});
				$('#edit-template-save').live('click', function(){
					$('#custom-template').val( editor.getValue() );
					tb_remove();
				});
				$('#edit-template-cancel').live('click', function(){
					tb_remove();
				});
			});
		</script>
	<?php }

	/**
	 * Gets called on save_post hook to save custom_template metas
	 *
	 * @since 0.1
	 * @return void
	 */
	function save_post( $post_id ) {
		if( $_POST['page_template'] == 'custom' ) {
			update_post_meta( $post_id, '_is_custom_template', 1 );
			update_post_meta( $post_id, '_custom_template', stripcslashes( $_POST['custom_template'] ) );
		} else {
			delete_post_meta( $post_id, '_is_custom_template' );
			/* we don't delete the _custom_template meta, in case user wants to use it later */
		}
	}

	/**
	 * Queues CodeMirror JavaScript library
	 *
	 * @since 0.1
	 * @return void
	 */
	function admin_queue() {
		wp_enqueue_style( 'codemirror', plugins_url( 'css/codemirror.css', __FILE__ ), array(), '2.15' );
		wp_enqueue_style( 'codemirror-theme', plugins_url( 'css/default.css', __FILE__ ) );
		wp_enqueue_style( 'custom-page-templates', plugins_url( 'css/admin.css', __FILE__ ) );
		wp_enqueue_script( 'codemirror', plugins_url( 'js/codemirror.js', __FILE__ ), array(), '2.15' );
		wp_enqueue_script( 'codemirror-xml', plugins_url( 'js/xml.js', __FILE__ ), array(), '2.15' );
		wp_enqueue_script( 'codemirror-javascript', plugins_url( 'js/javascript.js', __FILE__ ), array(), '2.15' );
		wp_enqueue_script( 'codemirror-css', plugins_url( 'js/css.js', __FILE__ ), array(), '2.15' );
		wp_enqueue_script( 'codemirror-clike', plugins_url( 'js/clike.js', __FILE__ ), array(), '2.15' );
		wp_enqueue_script( 'codemirror-php', plugins_url( 'js/php.js', __FILE__ ), array(), '2.15' );
	}
}
new Custom_Page_Templates_Reloaded();