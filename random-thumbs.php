<?php
/*
Plugin Name: Random Thumbs
Plugin URI: http://webdesasters.com.br/
Description: Este plugin cria blocos com o conte&uacute;do de alguns posts capturados aleatoriamente e os exibe tamb&eacute;m aleatoriamente.<br />Uma forma de chamar a aten&ccedil;&atilde;o dos seus visitantes para mat&eacute;rias antigas abordadas no site.<br />No admin &eacute; poss&iacute;vel configurar 3 op&ccedil;&otilde;es:<br />- N&uacute;mero m&aacute;ximo de thumbnails a exibir;<br />- Quantos thumbnails por linha;<br />- Dimens&atilde;o do thumbnail.<br />Apenas insira [randomthumbs] em seu POST e est&aacute; feito.
Version: 1.0
Author: Gabriel Lucas
Author URI: http://webdesasters.com.br/
*/

/*  Copyright 2010 Gabriel Lucas (email: eu at gabriellucas.com.br)

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('RANDOMTHUMBS_VERSION', '1.0.0');
define('RANDOMTHUMBS_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)).'/' );

if ( ! defined( 'WPCRT_PLUGIN_BASENAME' ) )
	define( 'WPRT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );


if ( ! defined( 'WPRT_PLUGIN_NAME' ) )
	define( 'WPRT_PLUGIN_NAME', trim( dirname( WPRT_PLUGIN_BASENAME ), '/' ) );


if ( ! defined( 'WPRT_PLUGIN_DIR' ) )
	define( 'WPRT_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPRT_PLUGIN_NAME );

add_action('wp_head', 'RTcss', 1);
add_filter('the_content', 'buscaRTtags', 10);
add_action('admin_head', 'wprt_header');

function wprt_header() {
	global $wp_version;
	echo "\n".'<meta name="RandomThumbs" content="'.RANDOMTHUMBS_VERSION.'" />';
	echo "\n".'<meta http-equiv="pragma" content="no-cache" />'."\n";
	echo "\n".'<link rel="stylesheet" href="'.RANDOMTHUMBS_URLPATH.'admin/admin.css" type="text/css" media="screen" />'."\n";
}

function RTcss() {
	echo '<meta name="RandomThumbs" content="'.RANDOMTHUMBS_VERSION.'" />
<link rel="stylesheet" href="'.RANDOMTHUMBS_URLPATH.'css/style.css" type="text/css" media="screen" />
';
}

function buscaRTtags($conteudo) {
	
	if ( stristr( $conteudo, '[randomthumbs]' )) {
		$conteudo = str_replace('[randomthumbs]', exibeThumbnails(), $conteudo);
	}
	return $conteudo;
	
}

function formataConteudo($cont) {
	while(strpos($cont,'<a')) {
		$aIni = strpos($cont,'<a');
		$aFim = strpos($cont,'>',$aIni);
		$aFim = $aFim - $aIni;
		if($aFim > 0) {
			$link = substr($cont,$aIni,$aFim);
			$cont = str_replace($link,'',$cont);
			$cont = str_replace('</a>','',$cont);
		}
	}
	if(strpos($cont,'<img')) {
		$imgIni = strpos($cont,'<img');
		$imgFim = strpos($cont,'>',$imgIni);
		$imgFim = $imgFim - $imgIni;
		if($imgFim > 0) {
			$img = substr($cont,$imgIni,$imgFim);
			$cont = str_replace($link,'',$cont);
			$cont = $img.$cont;
		}
	}
	$cont = str_replace('<img','<img width="150" height="150"',$cont);
	return $cont;
}

function exibeThumbnails() {
	
	global $wpdb;

	if(get_option('wprt_maxPosts')) {
		$larguraPost = get_option('wprt_larguraPost');
		$postsPorLinha = get_option('wprt_postsPorLinha');
		$unid = 'px';
		$larguraCampo = ($larguraPost*$postsPorLinha)+(24*$postsPorLinha).$unid;
		$maxPosts = get_option('wprt_maxPosts');
	}
	else {
		$larguraCampo = '100%';
		$larguraPost = '150';
		$maxPosts = 9;
	}
	
	$query = mysql_query("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post'");
	for($i=0;$postsID[$i] = mysql_fetch_assoc($query);$i++) {  }
	array_pop($postsID);
	
	$numposts = count($postsID);
	
	$RTcode = '<div id="randPosts" style="width: '.$larguraCampo.'">';
	
	if($numposts < $maxPosts) { $maxPosts = $numposts; }
	
	for($i=0;$i<$maxPosts;$i++) {
		
		if(is_array($num)) {
			if(!in_array(($rand = rand(0,($numposts-1))),$num)) {
				$num[$i] = $rand;
				$post = get_post($postsID[$num[$i]]['ID']);
				$content = formataConteudo($post->post_content);
				$RTcode .= '<a href="?p='.$post->ID.'" title="'.$post->post_title.'"><div class="randPost" style="width: '.$larguraPost.'px; height: '.$larguraPost.'px;">'.$content.'</div></a>';
			}
			else {
				$i--;
			}
		}
		else {
			$num[$i] = rand(0,($numposts-1));
			$post = get_post($postsID[$num[$i]]['ID']);
			$content = formataConteudo($post->post_content);
			$RTcode .= '<a href="?p='.$post->ID.'" title="'.$post->post_title.'"><div class="randPost" style="width: '.$larguraPost.'px; height: '.$larguraPost.'px;">'.$content.'</div></a>';
		}
		
	}
	
	$RTcode .= '</div>';
	
	return $RTcode;
	
}

add_action('admin_menu','wprt_plugin_menu');

function wprt_plugin_menu() {

  add_options_page('Random Thumbs Options', 'Random Thumbs', 'manage_options', 'wprt', 'wprt_plugin_options');

}

function wprt_plugin_options() {

  	if (!current_user_can('manage_options'))  {
    	wp_die( __('You do not have sufficient permissions to access this page.') );
  	}
	require_once(WPRT_PLUGIN_DIR . '/admin/admin.php');
	exibeRandomOpcoes();
	
}

?>