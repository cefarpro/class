<?php
/**
 * Download Class
 *
 * @version 0.1.0
 * @author Startsev Pavel <cefar@mail.ru>
 * @package server_name
 * @category server identyfy
 * @todo -
 * @example -
 * @copyright Copyright (c) 2017, Startsev Pavel
 */

class Download {

	public static function wget( $from_file, $to_file ) {
		if ( Os :: is_windows( ) ) $stdout = ' 2>&1'; // > NUL
		else $stdout = ' 2>&1'; // > /dev/null
		exec( 'wget -nv --no-check-certificate --no-clobber -O ' . $to_file . ' ' . $from_file . $stdout, $download_strout );
		return $download_strout;
	}

	public static function curl( $from_file, $to_file ) {
		$stat_url = @get_headers( $from_file, 1 );
		// Что то не так
		if ( !$stat_url || empty( $stat_url[ 0 ] ) || $stat_url[ 0 ] != 'HTTP/1.1 200 OK' ) {
			$stat_url[ 0 ] = ( isset( $stat_url[ 0 ] ) ) ? $stat_url[ 0 ] : 'null';
			Registry :: __instance( ) -> logging -> error( "http_code " . $stat_url[ 0 ] . "\t" . $from_file );
			return false;
		}
		$options = array(
			CURLINFO_HEADER_OUT    => false,
			CURLOPT_TIMEOUT        => 28800, // set this to 8 hours so we dont timeout on big files
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
		);
		if ( $to_file ) {
			$file_exists = file_exists( $to_file );
			if ( $file_exists ) {
				$stat_file = stat( $to_file );
				// Файл уже скачан
				if ( $stat_url[ 'Content-Length' ] == $stat_file[ 'size' ] ) {
					Registry :: __instance( ) -> logging -> debug( "file_exist\t[" . $stat_url[ 'Content-Length' ] . " == " . $stat_file[ 'size' ] . "]" );
					return true;
				}
			}
			$fp = fopen( $to_file, "w" );
			$options[ CURLOPT_FILE ] = $fp;
		}
		else {
			$options[ CURLOPT_RETURNTRANSFER ] = true;
		}
		$ch = curl_init( $from_file );
		curl_setopt_array( $ch, $options );
		$result = curl_exec( $ch );
		$info = curl_getinfo( $ch );
		curl_close( $ch );

		if ( $info[ 'http_code' ] == '200' ) {
			return array( date( 'Y-m-d h:i:s' ) . ' URL:' . $from_file . ' [' . $info[ "download_content_length" ] . '/' . $info[ "size_download" ] . '] -> "' . $to_file . '" [1]' );
		}
		else {
			Registry :: __instance( ) -> logging -> error( "http_code " . $info[ 'http_code' ] . "\t" . $from_file );
			return false;
		}
	}



	public static function smtp( $from = array( ), $to = '/', $connect = array( ) ) {
		return false;
	}
	

}