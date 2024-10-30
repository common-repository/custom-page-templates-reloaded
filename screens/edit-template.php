<?php

$paths = array(
    "../../..",
    "../../../..",
    "../../../../..",
    "../../../../../..",
    "../../../../../../..",
    "../../../../../../../..",
    "../../../../../../../../..",
    "../../../../../../../../../..",
    "../../../../../../../../../../..",
    "../../../../../../../../../../../..",
    "../../../../../../../../../../../../.."
);

/* include wordpress, make sure its available in one of the higher folders */
foreach( $paths as $path ) {
   if( @include_once( $path . '/wp-load.php' ) ) break;
}

?><!DOCTYPE html>
<html>
<head>

</head>
<body>
	<div id="edit-template">
		<div style="background: whiteSmoke; border: 1px solid #eee; margin-top: 15px; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; padding-right: 15px">
			<p class="submit" style="text-align: right">
				<input type="button" class="button-secondary" value="Cencel" id="edit-template-cancel" />
				<input type="button" class="button-primary" value="Save Settings" id="edit-template-save" />
			</p>
		</div>
		<br class="clear" />
		<textarea dir="ltr"><?php echo esc_html( get_post_meta( $_GET['post_id'], '_custom_template', true ) ); ?></textarea>
	</div>
</body>
</html>