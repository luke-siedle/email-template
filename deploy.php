<?php
	
	error_reporting( 0 );
	
	/**
	 *	Load a file and perform
	 *	adequate string replacements
	 */

	 define( 'REMOTE_IMG_PATH', 'http://secure.lab19digital.com/stage/' );
	 define( 'DEPLOY_FILE', 'deploy.html' );
	 
	 // Load html and css //
	 $file			= file_get_contents( 'src.html' );
	 $boilerplt		= file_get_contents( 'css/boilerplate.css' );
	 $css			= file_get_contents( 'css/style.css' )
					. file_get_contents( 'css/mobile.css' );
	 
	 // Replacements //
	 $file = str_replace(array(
		 
		 // Remote image paths //
		 $_GET['remote'] ? 'images/' : '*nothing*',
		 // Replace styles inline //
		 '{css}',
		 
		 !$_GET['premailer'] ? '{mobile}' : '*nothing*',
		 
		 // Add table attributes //
		 '<table',
		 // Always set td valign to top //
		 '<td'
		 
	 ), array(
		 
		 REMOTE_IMG_PATH,
		 '<style type="text/css">' . $boilerplt . $css . '</style>',
		 '',
		 '<table cellpadding="0" cellspacing="0"',
		 '<td valign="top"'
		 
	 ), $file );
	
	if( $_GET['premailer'] ){
		
		// Remove links and script //
		$file = preg_replace(array(
			"/<\\/?script(\\s+.*?>|>)/",
			"/<\\/?link(\\s+.*?>|>)/"
		), "", $file ); 
		
		// Run Premailer API to build html inline //
		require_once('lib/premailer.class.php');

		$pre = Premailer::html( $file );
		$html = $pre['html'];
		$plain = $pre['plain'];
		
		// Restore media queries inline //
		$file = str_replace(array(
			'mobile'
		), array(
			'<style type="text/css">' . $mobileCss . '</style>'
		), $file );

		// Output the file //
		file_put_contents( DEPLOY_FILE , $file );
	}
	
	echo $file;