<?php
/*
Author: Gabriel Lucas
Author URI: http://www.webdesasters.com.br
Description: Op&ccedil;&otilde;es administrativas do plugin Random Thumbs.
*/

function exibeRandomOpcoes() {
	$location = get_option('siteurl') . '/wp-admin/options-general.php?page=wprt';
	
	add_option('wprt_postsPorLinha', __($postsPorLinha, 'wprt'));
	add_option('wprt_maxPosts', __($maxPosts, 'wprt'));
	add_option('wprt_larguraPost', __($maxPosts, 'wprt'));
	
	if(isset($_POST['RandomThumbs'])) {
		update_option('wprt_postsPorLinha', $_POST['wprt_postsPorLinha']);
		update_option('wprt_maxPosts', $_POST['wprt_maxPosts']);
		update_option('wprt_larguraPost', $_POST['wprt_larguraPost']);
	}
	
	?>
<div id="wprt_admin">	
	<form name="form1" method="post" id="wprt_form" action="<?php echo $location ?>">
		<input type="hidden" name="RandomThumbs" />
		<label>M&aacute;ximo de Posts</label>
		<input name="wprt_maxPosts" type="text" id="wprt_maxPosts" value="<?php echo $_POST['wprt_maxPosts']; ?>" size="4" /><br />
		<label>Posts por Linha</label>
		<input name="wprt_postsPorLinha" type="text" id="wprt_postsPorLinha" value="<?php echo $_POST['wprt_postsPorLinha']; ?>" size="4" /><br />
        <label>Largura do Quadro</label>
		<input name="wprt_larguraPost" type="text" id="wprt_larguraPost" value="<?php echo $_POST['wprt_larguraPost']; ?>" size="5" /><br />
		<input type="submit" name="Submit" value="<?php _e('Update Options', 'wpcf') ?> &raquo;" />
	</form>
</div>
<?php } ?>