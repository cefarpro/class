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
	$from = array(
		'/path/to/file1',
		'/path/to/file2',
		'/path/to/file3',
		...
	),
	$to = '/path/to/server/dir/',
	$connect = array(
		'host' => 'localhost',
		'port' => 22,
		'user' => 'user',
		'pass' => 'password'
	)
	*/
	public static function ssh( $from = array( ), $to = '/', $connect = array( ) ) {
		return false;
	}


	/**
	$from = array(
		'/path/to/file1',
		'/path/to/file2',
		'/path/to/file3',
		...
	),
	$to = '/path/to/server/dir/',
	$connect = array(
		'host' => 'localhost',
		'port' => 22,
		'user' => 'user',
		'pass' => 'password'
	)
	*/
	public static function rsynk( $from = array( ), $to = '/', $connect = array( ) ) {
		return false;
	}


	// 
	public static function ydisk( $from = array( ), $to = '/', $connect = array( ) ) {
		return false;
	}


	/**
	$from = array(
		'/path/to/file1',
		'/path/to/file2',
		'/path/to/file3',
		...
	),
	$to = '/path/to/server/dir/',
	$connect = array(
		'target_url' => 'http://127.0.0.1/uploader.php',
		'options' = array(
			'CURL_OPTION1', 
			'CURL_OPTION2', 
			'CURL_OPTION3',
			...
		)
	)
	*/
	public static function post( $from = array( ), $to = '/', $connect = array( ) ) {
		$post = array( );
		if ( !count( $from ) ) return false;
		$ch = curl_init( $from_file );
		foreach( $from as $k => $f ) {
			if ( ( version_compare( PHP_VERSION, '5.5' ) >= 0 ) ) {
				$post[ 'file' ] = new CURLFile( $f );
				curl_setopt( $ch, CURLOPT_SAFE_UPLOAD, true );
			} else {
				$post[ 'file '] = '@' . $f;
			}
		}
		curl_setopt( $ch, CURLOPT_URL, $target_url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 28800 ); // 8 hour
		curl_setopt( $ch, CURLINFO_HEADER_OUT, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
		// curl_setopt( $ch, CURLOPT_BUFFERSIZE, 5356800 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		if ( isset( $connect[ 'options' ] ) ) curl_setopt_array( $ch, $connect[ 'options' ] );

		$result = curl_exec( $ch );
		$info = curl_getinfo( $ch );
		curl_close( $ch );
		return $result;
	}



	public static function smtp( $from = array( ), $to = '/', $connect = array( ) ) {
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}