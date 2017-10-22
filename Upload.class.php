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
		$connect = array(
			'target_url' => 'http://127.0.0.1/uploader.php',
			'options' = array(
				'CURL_OPTION1', 
				'CURL_OPTION2', 
				'CURL_OPTION3',
				...
			),
			'headers' = array(
				'HTTP(s) HEADER1',
				'HTTP(s) HEADER2',
				'HTTP(s) HEADER3',
				...
			)
		);
		/////////////////////////////////
		// RETURN
		array(
			'success' => true||false, // true - no errors || false - one ore more errors
			'from' => array(
				'/path/from/file1/',
				'/path/from/file2/',
				'/path/from/file3/',
				...
			),
			'to' => array(
				'/path/to/file1/',
				'/path/to/file2/',
				'/path/to/file3/',
				...
			),
			'status' => array(
				'NOEXIST', // local file not exist
				'UNREADABLE', // local file unreadable
				'UPLOAD', // file has been uploaded
				'FAIL', // file is not uploaded
			),
			'data' => array(
				'content1' ( string ), // curl_get_content( )
				'content2' ( string ), // curl_get_content( )
				'content3' ( string ), // curl_get_content( )
				...
			),
			'info' => array(
				'request1' ( array ), // curl_getinfo( ) || stat( )
				'request2' ( array ), // curl_getinfo( ) || stat( )
				'request3' ( array ), // curl_getinfo( ) || stat( )
				...
			),
		);

		/////////////////////////////////
		// FILE uploader.php :
		/////////////////////////////////

<?php

	set_time_limit( 0 );
	// allowed filter preg match name of file
	// $allow_filter_name = array( '^map[A-z0-9]{1,}', '^p[A-z0-9]{1,}', '^im[A-z0-9]{1,}' );
	$allow_filter_name = array( );
	// allowed filter type of file
	$allow_filter_type = array( 'image/jpeg', 'image/jpg', 'image/png' );
	// upload directory path
	if ( !isset( $_GET[ 'uploaddir' ] ) ) {
		$uploaddir = realpath( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'upload';
	} else $uploaddir = $_GET[ 'uploaddir' ];

	$r = array(
		's' => true,
		'code' => 200,
		'errors' => array( ),
		'message' => 'OK',
		'data' => array( ),
		'size' => 0,
	);
	// переменные
	if ( $_POST ) {
		$fo = fopen( realpath( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . __FILE__ . '.log', 'a+' );
		fwrite( $fo, date( 'm-d-Y H:i:s.' ) . "\n", 3000 );
		fwrite( $fo, var_export( $_POST, true ) . "\n", 3000 );
		fwrite( $fo, "\n\n\n", 8500 );
		fclose( $fo );
	}
	// файлы
	if ( $_FILES ) {
		$array_files = array( );
		$i = 0;
		foreach( $_FILES as $keyname => $file ) {
			if ( !is_array( $file[ 'name' ] ) || !count( $file[ 'name' ] ) ) {
				////////////////////////////////////////// FILTER
				if ( count( $allow_filter_type ) && array_search( finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $file[ 'tmp_name' ] ), $allow_filter_type ) === false ) continue; // filtering type
				if ( count( $allow_filter_name ) && !preg_match( "/(" . implode( '|', $allow_filter_name ) . ")/", $file[ 'name' ] ) ) continue; // filtering name
				//////////////////////////////////////////
				foreach( $file as $key => $f ) {
					$array_files[ $i ][ $key ] = $file[ $key ];
				}
				unset( $keyname, $file, $key, $f );
				++$i;
			}
			else {
				$i2 = 0;
				foreach( $file as $key => $f ) {
					foreach ( $f as $k => $v ) {
						////////////////////////////////////////// FILTER
						// filtering type
						if ( count( $allow_filter_type ) && array_search( finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $file[ 'tmp_name' ][ $k ] ), $allow_filter_type ) === false ) {
							unset( $array_files[ $i + $k ][ $key ] );
							continue; 
						}
						// filtering name
						if ( count( $allow_filter_name ) && !preg_match( "/(" . implode( '|', $allow_filter_name ) . ")/i", $file[ 'name' ][ $k ] ) ) {
							unset( $array_files[ $i + $k ][ $key ] );
							continue; 
						}
						/////////////////////////////////////////
						$array_files[ $i + $k ][ $key ] = $v;
					}
				}
				++$i;
				$i += $k;
				unset( $keyname, $file, $key, $f, $k, $v );
			}
		}
		$error = false;
		foreach( $array_files as $key => $file ) {
			$uploadfile = $uploaddir . DIRECTORY_SEPARATOR . basename( $file[ 'name' ] );
			if ( file_exists( $uploadfile ) ) {
				if ( $stat = stat( $uploadfile ) ) {
					$r[ 'data' ][ $file[ 'name' ] ] = 'file exist[ ' . $file[ 'size' ] . ' / ' . $stat[ 'size' ] . ' ]';
					continue;
				}
				else {
					$r[ 'errors' ][ $file[ 'name' ] ] = 'file dont uploaded';
					$error = true;
				}
			}
			if ( move_uploaded_file( $file[ 'tmp_name' ], $uploadfile ) ) {
				$r[ 'data' ][ $file[ 'name' ] ] = 'file uploaded [ ' . $file[ 'size' ] . ' ]';
				$r[ 'size' ] += $file[ 'size' ];
			} else {
				$r[ 'errors' ][ $file[ 'name' ] ] = 'file dont uploaded';
				$error = true;
			}
		}
		ob_start( 'ob_gzhandler' );
		if ( $error ) {
			$r[ 's' ] = false;
			$r[ 'code' ] = 400;
			$r[ 'message' ] = 'Ошибка выполнения';
			header( 'HTTP/1.1 400 Bad Request' );
		}
		header( 'Content-type: application/json' );
		echo json_encode( $r );
		ob_end_flush( );
		exit( ( $error ) ? 1 : 0 );
	}

?>
	*/
	public static function post( $from = array( ), $to = '', $connect = array( ) ) {

	  $headers = array( 'Content-Type: multipart/form-data' ); // cURL headers for file uploading
	  $curly = array( );
	  $info = array( );

	  $ret = array(
			'success' => true,
			'from' => array( ),
			'to' => array( ),
			'status' => array( ),
			'data' => array( ),
			'info' => array( ),
	  );

	  $mh = curl_multi_init( );

	  if ( !count( $from ) ) return false;
	  $filesize = 0;
	  foreach( $from as $id => $f ) {

		if ( !file_exists( $f ) ) {
			$ret[ 'success' ] = false;
			$ret[ 'from' ][ $id ] = $from[ $id ];
			$ret[ 'to' ][ $id ] = $connect[ 'target_url' ] . ( ( $to ) ? '?uploaddir=' . $to : ' < @POST ' ) . '' . basename( $from[ $id ] );
			$ret[ 'status' ][ $id ] = 'NOEXIST';
			$ret[ 'info' ][ $id ] = false;
			continue;
		}
		else if ( !is_readable( $f ) ) {
			$ret[ 'success' ] = false;
			$ret[ 'from' ][ $id ] = $from[ $id ];
			$ret[ 'to' ][ $id ] = $connect[ 'target_url' ] . ( ( $to ) ? '?uploaddir=' . $to : ' < @POST ' ) . '' . basename( $from[ $id ] );
			$ret[ 'status' ][ $id ] = 'UNREADABLE';
			$ret[ 'info' ][ $id ] = stat( $f );
			for( $n = 0; $n <= 12; $n++ ) unset( $ret[ 'info' ][ $id ][ $n ] );
			continue;
		}

		$post = array( );

		$curly[ $id ] = curl_init( );

		$mime = finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $f );
		$filename = basename( $f );
		if ( ( version_compare( PHP_VERSION, '5.5' ) >= 0 ) ) {
			$post[ 'file' . $k ] = new CURLFile( $f, $mime, $filename );
			curl_setopt( $curly[ $id ], CURLOPT_SAFE_UPLOAD, true );
		} else {
			$post[ 'file' . $k ] = '@' . $f;
		}

		if ( isset( $connect[ 'headers' ] ) && is_array( $connect[ 'headers' ] ) && count( $connect[ 'headers' ] ) ) $headers = array_merge( $headers, $connect[ 'headers' ] );
		curl_setopt( $curly[ $id ], CURLOPT_URL, $connect[ 'target_url' ] . ( ( $to ) ? '?uploaddir=' . $to : '' ) );
		curl_setopt( $curly[ $id ], CURLOPT_TIMEOUT, 28800 ); // 8 hour
		curl_setopt( $curly[ $id ], CURLOPT_POST, true );
		curl_setopt( $curly[ $id ], CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $curly[ $id ], CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curly[ $id ], CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curly[ $id ], CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $curly[ $id ], CURLOPT_ENCODING, 'gzip' );
		curl_setopt( $curly[ $id ], CURLOPT_POSTFIELDS, $post );
		curl_setopt( $curly[ $id ], CURLOPT_RETURNTRANSFER, true );
		//curl_setopt( $ch, CURLINFO_HEADER_OUT, true );

		// extra options?
		if ( isset( $connect[ 'options' ] ) ) curl_setopt_array( $curly[ $id ], $connect[ 'options' ] );
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
		if ( $info[ $id ][ 'http_code'  ] == 200 ) {
			$ret[ 'status' ][ $id ] = 'UPLOAD';
		}
		else {
			$ret[ 'status' ][ $id ] = 'FAIL';
			$ret[ 'success' ] = false;
		}
		$ret[ 'from' ][ $id ] = $from[ $id ];
		$ret[ 'to' ][ $id ] = $connect[ 'target_url' ] . ( ( $to ) ? '?uploaddir=' . $to : ' < @POST ' ) . '' . basename( $from[ $id ] );
		$ret[ 'data' ][ $id ] = curl_multi_getcontent( $c );
		$ret[ 'info' ][ $id ] = $info[ $id ];
		curl_multi_remove_handle( $mh, $c );
	  }

	  // all done
	  curl_multi_close( $mh );

	  return $ret;

	}


	/**
	SLOWLY 1.11
	*/
	public static function get_contents( $from = array( ), $to = '', $connect = array( ) ) {
		$post = array( );
		if ( !count( $from ) ) return false;
		$filesize = 0;
		$delimiter = '-------------' . uniqid( );
		$post = '';
		foreach( $from as $k => $f ) {
			if ( !is_readable( $f ) ) continue;
			$post .= '--' . $delimiter. "\r\n";
			$post .= 'Content-Disposition: form-data; name="file[' . $k . ']"';
			$post .= '; filename="' . basename( $f ) . '"' . "\r\n";
			$post .= 'Content-Type: ' . ( finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $f ) )  . "\r\n\r\n";
			$post .= file_get_contents( $f ) . "\r\n";
			$post .= "--" . $delimiter . "--\r\n";
		}
		$headers = array(
			'Content-Type: multipart/form-data; boundary=' . $delimiter,
			'Content-Length: ' . strlen( $post )
		);
		$context = stream_context_create(array(
			'http' => array(
				  'method' => 'POST',
				  'header' => $headers,
				  'content' => $post,
			)
		));
		$res = file_get_contents( $connect[ 'target_url' ] . ( ( $to ) ? '?uploaddir=' . $to : '' ), false, $context );
		return $res;
	}


	/**
	SLOWLY 1.03
	*/
	public static function socket( $from = array( ), $to = '', $connect = array( ) ) {
		try{
			$post = array( );
			if ( !count( $from ) ) return false;
			$filesize = 0;
			$delimiter = '-------------' . uniqid( );
			$post = '';

			foreach( $from as $k => $f ) {
				if ( !is_readable( $f ) ) continue;
				$post .= '--' . $delimiter. "\r\n";
				$post .= 'Content-Disposition: form-data; name="file[' . $k . ']"';
				$post .= '; filename="' . basename( $f ) . '"' . "\r\n";
				$post .= 'Content-Type: ' . ( finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $f ) )  . "\r\n\r\n";
				$post .= file_get_contents( $f ) . "\r\n";
				$post .= "--" . $delimiter . "--\r\n";
			}

			$url = $connect[ 'target_url' ] . ( ( $to ) ? '?uploaddir=' . $to : '' );
			$parse_url = parse_url( $url );
			$uri = str_replace( $parse_url[ 'scheme' ] . '://' . $parse_url[ 'host' ], '', $url );
			$port = ( $parse_url[ 'scheme' ] == 'http' ) ? 80 : 443;

			$response = '';
			if ( $fp = fsockopen( $parse_url[ 'host' ], $port, $errno, $errstr, 20 ) ) {
				$write = "POST " . $uri . " HTTP/1.1\r\n"
					. "Host: " . $parse_url[ 'host' ] . "\r\n"
					. "Content-Type: multipart/form-data; boundary=" . $delimiter . "\r\n"
					. "Content-Length: " . strlen( $post ) . "\r\n"
					. "Connection: Close\r\n\r\n"
					. $post;

				fwrite( $fp, $write );

				while ( $line = fgets( $fp ) ) {
					if ( $line !== false ) {
						$response .= $line;
					}
				}
				fclose( $fp );
				$response = explode( "\r\n\r\n", $response );
				$response = end( $response );
				return $response;
			}
			else {
				throw new Exception( "$errstr ($errno)" );
			}
		}
		catch ( Exception $e ) {
			echo 'Error: ' . $e -> getMessage( );
		}
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


	// parameters
	public static function ydisk( $from = array( ), $to = '/', $connect = array( ) ) {
		return false;
	}








	function multiCurlRequest( $data, $options = array( ) ) {

	  $curly = array( );
	  $result = array( );

	  // multi handle
	  $mh = curl_multi_init( );

	  // loop through $data and create curl handles
	  // then add them to the multi-handle
	  foreach ( $data as $id => $d ) {
	 
		$curly[ $id ] = curl_init( );
	 
		$url = ( is_array( $d ) && !empty( $d[ 'url' ] ) ) ? $d[ 'url' ] : $d;
		curl_setopt( $curly[ $id ], CURLOPT_URL,            $url );
		curl_setopt( $curly[ $id ], CURLOPT_HEADER,         0 );
		curl_setopt( $curly[ $id ], CURLOPT_RETURNTRANSFER, 1 );
	 
		// post?
		if ( is_array( $d ) ) {
		  if ( !empty( $d[ 'post' ] ) ) {
			curl_setopt( $curly[ $id ], CURLOPT_POST,       1 );
			curl_setopt( $curly[ $id ], CURLOPT_POSTFIELDS, $d[ 'post' ] );
		  }
		}
	 
		// extra options?
		if ( !empty( $options ) ) {
		  curl_setopt_array( $curly[ $id ], $options );
		}
	 
		curl_multi_add_handle( $mh, $curly[ $id ] );
	  }
	 
	  // execute the handles
	  $running = null;
	  do {
		curl_multi_exec( $mh, $running );
	  } while( $running > 0 );
	 
	 
	  // get content and remove handles
	  foreach( $curly as $id => $c ) {
		$result[ $id ] = curl_multi_getcontent( $c );
		curl_multi_remove_handle( $mh, $c );
	  }

	  // all done
	  curl_multi_close( $mh );

	  return $result;
	}





	
	
	
	
	
	
	
	
	
	
	
	
	
	
}