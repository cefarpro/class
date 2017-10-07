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

	public static function curl( $from_files = array( ), $to_files = array( ), $filter = array( ) ) {

	  $curly = array( );
	  $info = array( );
	  $ret = array( );

	  $mh = curl_multi_init( );

	  foreach ( $from_files as $id => $f ) {

		$curly[ $id ] = curl_init( $f );
		curl_setopt( $curly[ $id ], CURLOPT_HEADER, 0 );
		curl_setopt( $curly[ $id ], CURLINFO_HEADER_OUT, false );
		curl_setopt( $curly[ $id ], CURLOPT_TIMEOUT, 28800 );
		curl_setopt( $curly[ $id ], CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curly[ $id ], CURLOPT_SSL_VERIFYHOST, false );

		if ( file_exists( $to_files[ $id ] ) ) {
			$ret[ $id ] = "download_file_exist\t" . $to_files[ $id ];
			Registry :: __instance( ) -> logging -> debug( $ret[ $id ] );
			continue;
		}

		curl_setopt( $curly[ $id ], CURLOPT_FILE, fopen( $to_files[ $id ], 'x' ) );
		curl_multi_add_handle( $mh, $curly[ $id ] );
	  }

	  // execute the handles
	  $running = null;
	  do {
		curl_multi_exec( $mh, $running );
	  } while( $running > 0 );


	  // get content and remove handles
	  foreach( $curly as $id => $c ) {
		$info[ $id ] = curl_getinfo( $c );
		curl_multi_remove_handle( $mh, $c );
	  }

	  // all done
	  curl_multi_close( $mh );

	  foreach( $info as $id => $i ) {
		if ( $i[ 'http_code' ] == '200' ) {
			$ret[ $id ] = date( 'Y-m-d h:i:s' ) . ' URL:' . $from_files[ $id ] . ' [' . $i[ "download_content_length" ] . '/' . $i[ "size_download" ] . '] -> "' . $to_files[ $id ] . '" [1]';
		}
		else {
			$ret[ $id ] = "download_code\t" . $i[ 'http_code' ] . "\t" . $from_files[ $id ];
			Registry :: __instance( ) -> logging -> error( $ret[ $id ] );
		}
	  }

	  return $ret;

	}


	public static function smtp( $from = array( ), $to = '/', $connect = array( ) ) {
		return false;
	}
	

}