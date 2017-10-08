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


	/**
		//////////////////////////////////
		// ARGUMENTS
		$from_files = array(
			'/path/to/file1',
			'/path/to/file2',
			'/path/to/file3',
			...
		),
		$to_files = array(
			'/path/to/server/file1',
			'/path/to/server/file2',
			'/path/to/server/file3',
			...
		),
		$merge = true||false // true - if you want replace exist files (default = false)
		$filter = array(
			'file1',
			'file2',
			...
		)
		/////////////////////////////////
		// RETURN
		array(
			'success' => true||false, // true - no errors || false - one ore more errors
			'files' => array(
				'/path/from/file1/' => '/path/to/file1/',
				'/path/from/file2/' => '/path/to/file2/',
				'/path/from/file3/' => '/path/to/file3/',
				...
			),
			'status' => array(
				'EXIST', // file exist ( parameter merge )
				'DOWNLOAD', // file has been downloaded
				'FAIL', // file is not downloaded
			),
			'info' => array(
				'request1' = array(), // curl_getinfo( ) || stat( )
				'request2' = array(), // curl_getinfo( ) || stat( )
				'request3' = array(), // curl_getinfo( ) || stat( )
				...
			)
		)
	*/
	public static function get( $from_files = array( ), $to_files = array( ), $merge = false, $filter = array( ) ) {

	  $curly = array( );
	  $info = array( );

	  $ret = array(
			'success' => true,
			'files' => array( ),
			'status' => array( ),
			'info' => array( )
	  );

	  $mh = curl_multi_init( );

	  foreach ( $from_files as $id => $f ) {

		if ( file_exists( $to_files[ $id ] ) ) {
			$ret[ 'files' ][ $id ][ $from_files[ $id ] ] = $to_files[ $id ];
			$ret[ 'status' ][ $id ] = 'EXIST';
			$ret[ 'info' ][ $id ] = stat( $to_files[ $id ] );
			for( $n = 0; $n <= 12; $n++ ) unset( $ret[ 'info' ][ $id ][ $n ] );
			continue;
		}

		$curly[ $id ] = curl_init( $f );
		curl_setopt( $curly[ $id ], CURLOPT_HEADER, 0 );
		curl_setopt( $curly[ $id ], CURLINFO_HEADER_OUT, false );
		curl_setopt( $curly[ $id ], CURLOPT_TIMEOUT, 28800 );
		curl_setopt( $curly[ $id ], CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curly[ $id ], CURLOPT_SSL_VERIFYHOST, false );
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
			$ret[ 'files' ][ $id ][ $from_files[ $id ] ] = $to_files[ $id ];
			$ret[ 'status' ][ $id ] = 'DOWNLOAD';
			$ret[ 'info' ][ $id ] = $info[ $id ];
		}
		else {
			$ret[ 'files' ][ $id ][ $from_files[ $id ] ] = $to_files[ $id ];
			$ret[ 'status' ][ $id ] = 'FAIL';
			$ret[ 'info' ][ $id ] = $info[ $id ];
		}
	  }

	  return $ret;

	}


	public static function smtp( $from = array( ), $to = '/', $connect = array( ) ) {
		return false;
	}
	

}