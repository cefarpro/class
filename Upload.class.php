<?php
/**
 * Upload Class
 *
 * @version 0.1.0
 * @author Startsev Pavel <cefar@mail.ru>
 * @package server_name
 * @category server identyfy
 * @todo -
 * @example -
 * @copyright Copyright (c) 2017, Startsev Pavel
 */

class Upload {


	/**
	$connect = array(
		'host' => 'localhost',
		'port' => 22,
		'user' => 'user',
		'pass' => 'password'
	)
	*/
	public static function ssh( $from = array( ), $to = '/', $connect = array( ) ) {
		
	}


	/**
	$connect = array(
		'host' => 'localhost',
		'port' => 22,
		'user' => 'user',
		'pass' => 'password'
	)
	*/
	public static function rsynk( $from = array( ), $to = '/', $connect = array( ) ) {
		
	}


	// 
	public static function ydisk( $from = array( ), $to = '/', $connect = array( ) ) {
		
	}


	/**
	$connect = array(
		'target_url' => 'http://127.0.0.1/uploader.php',
	)
	*/
	public static function post( $from = array( ), $to = '/', $connect = array( ) ) {

		$post = array( );

		$ch = curl_init( );


		if ( ( version_compare( PHP_VERSION, '5.5' ) >= 0 ) ) {
			$post[ 'file' ] = new CURLFile( $from );
			curl_setopt( $ch, CURLOPT_SAFE_UPLOAD, true );
		} else {
			$post[ 'file '] = '@' . $from;
		}


		curl_setopt( $ch, CURLOPT_URL, $target_url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 120 );
		curl_setopt( $ch, CURLOPT_BUFFERSIZE, 5356800 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$sResponse = curl_exec( $ch );
		
	}



}