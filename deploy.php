<?php
	
	error_reporting( 0 );
	
	/**
	 *	Load a file and perform
	 *	adequate string replacements
	 */

	 define( 'REMOTE_IMG_PATH', 'images/' );
	 define( 'DEPLOY_FILE', 'deploy.html' );
	 
	 // Load html and css //
	 $file			= file_get_contents( 'src.html' );
	 $boilerplt		= file_get_contents( 'css/boilerplate.css' );
	 $css			= file_get_contents( 'css/style.css' );
	 
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
		 '<td',
		 // Overwrite the valign if set //
		 '<td valign="top" valign',
		 
	 ), array(
		 
		 REMOTE_IMG_PATH,
		 '<style type="text/css">' . $boilerplt . $css . '</style>',
		 '',
		 '<table cellpadding="0" cellspacing="0"',
		 '<td valign="top"',
		 '<td valign'
		 
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
		
		$mobileCss		= file_get_contents( 'css/mobile.css' );
		
		// Restore media queries inline //
		$html = str_replace(array(
			'</body>',
			
			// Inline images //
			"url('images/"
		), array(
			'<style type="text/css">' . $mobileCss . '</style></body>',
			"url('" . REMOTE_IMG_PATH
		), $html );

		// Output the file //
		file_put_contents( DEPLOY_FILE , $html );
		
		$file = $html;
	}
	
	echo $file;