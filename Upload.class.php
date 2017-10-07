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
	----------------------------------------------
	FILE uploader.php :

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
	if ( $_FILES ) {
		$array_files = array( );
		$i = 0;
		foreach( $_FILES as $keyname => $file ) {
			if ( !is_array( $file[ 'name' ] ) || !count( $file[ 'name' ] ) ) {
				//////////////////////////////////////////
				if ( count( $allow_filter_type ) && array_search( $file[ 'type' ], $allow_filter_type ) === false ) continue; // filtering type
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
						//////////////////////////////////////////
						// filtering type
						if ( count( $allow_filter_type ) && array_search( $file[ 'type' ][ $k ], $allow_filter_type ) === false ) {
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
		foreach( $array_files as $key => $filename ) {
			$uploadfile = $uploaddir . DIRECTORY_SEPARATOR . basename( $filename[ 'name' ] );
			if ( file_exists( $uploadfile ) ) {
				if ( $stat = stat( $uploadfile ) ) {
					$r[ 'data' ][ $filename[ 'name' ] ] = 'file exist[ ' . $filename[ 'size' ] . ' / ' . $stat[ 'size' ] . ' ]';
					continue;
				}
				else {
					$r[ 'errors' ][ $filename[ 'name' ] ] = 'file dont uploaded';
					$error = true;
				}
			}
			if ( move_uploaded_file( $filename[ 'tmp_name' ], $uploadfile ) ) {
				$r[ 'data' ][ $filename[ 'name' ] ] = 'file uploaded [ ' . $filename[ 'size' ] . ' ]';
				$r[ 'size' ] += $filename[ 'size' ];
			} else {
				$r[ 'errors' ][ $filename[ 'name' ] ] = 'file dont uploaded';
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