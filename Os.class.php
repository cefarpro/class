<?php
/**
 * Operation System Class
 *
 * @version 0.1.0
 * @author Startsev Pavel <cefar@mail.ru>
 * @package server_name
 * @category server identyfy
 * @todo -
 * @example -
 * @copyright Copyright (c) 2017, Startsev Pavel
 */
 
class Os {


	protected static $_logging;

	protected static function logging( ) {
		if ( !self :: $_logging ) {
			if ( Registry :: __instance( ) -> logging ) self :: $_logging = Registry :: __instance( ) -> logging;
			else {
				// CREATE LOG OBJECT
				$to_dir = realpath( dirname( __FILE__ ) );
				$log_info = pathinfo( __FILE__ );
				$source = $log_info[ 'filename' ];
				$log_name = $log_info[ 'filename' ] . '.log';
				$log_path = $to_dir . DS . DIR_LOG . DS . $log_name;
				self :: create_dirs_form_path( $log_path );
				$log_conf = array(
					'to_file' => $log_path,
					'source' => $source,
				);
				self :: $_logging = new Logging( $log_conf );
				Registry :: __instance( ) -> logging = self :: $_logging;
			}
		}
	}





	public static $oses = array (

		// Mircrosoft Windows Operating Systems
		'Windows 3.11' => '(Win16)',
		'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
		'Windows 98' => '(Windows 98)|(Win98)',
		'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
		'Windows 2000 Service Pack 1' => '(Windows NT 5.01)',
		'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
		'Windows Server 2003' => '(Windows NT 5.2)',
		'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
		'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
		'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
		'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
		'Windows ME' => '(Windows ME)|(Windows 98; Win 9x 4.90 )',
		'Windows CE' => '(Windows CE)',

		// UNIX Like Operating Systems
		'Mac OS X Kodiak (beta)' => '(Mac OS X beta)',
		'Mac OS X Cheetah' => '(Mac OS X 10.0)',
		'Mac OS X Puma' => '(Mac OS X 10.1)',
		'Mac OS X Jaguar' => '(Mac OS X 10.2)',
		'Mac OS X Panther' => '(Mac OS X 10.3)',
		'Mac OS X Tiger' => '(Mac OS X 10.4)',
		'Mac OS X Leopard' => '(Mac OS X 10.5)',
		'Mac OS X Snow Leopard' => '(Mac OS X 10.6)',
		'Mac OS X Lion' => '(Mac OS X 10.7)',
		'Mac OS X' => '(Mac OS X)',
		'Mac OS' => '(Mac_PowerPC)|(PowerPC)|(Macintosh)',
		'Open BSD' => '(OpenBSD)',
		'SunOS' => '(SunOS)',
		'Solaris 11' => '(Solaris\/11)|(Solaris11)',
		'Solaris 10' => '((Solaris\/10)|(Solaris10))',
		'Solaris 9' => '((Solaris\/9)|(Solaris9))',
		'CentOS' => '(CentOS)',
		'QNX' => '(QNX)',

		// Kernels
		'UNIX' => '(UNIX)',

		// Linux Operating Systems
		'Ubuntu 12.10' => '(Ubuntu\/12.10)|(Ubuntu 12.10)',
		'Ubuntu 12.04 LTS' => '(Ubuntu\/12.04)|(Ubuntu 12.04)',
		'Ubuntu 11.10' => '(Ubuntu\/11.10)|(Ubuntu 11.10)',
		'Ubuntu 11.04' => '(Ubuntu\/11.04)|(Ubuntu 11.04)',
		'Ubuntu 10.10' => '(Ubuntu\/10.10)|(Ubuntu 10.10)',
		'Ubuntu 10.04 LTS' => '(Ubuntu\/10.04)|(Ubuntu 10.04)',
		'Ubuntu 9.10' => '(Ubuntu\/9.10)|(Ubuntu 9.10)',
		'Ubuntu 9.04' => '(Ubuntu\/9.04)|(Ubuntu 9.04)',
		'Ubuntu 8.10' => '(Ubuntu\/8.10)|(Ubuntu 8.10)',
		'Ubuntu 8.04 LTS' => '(Ubuntu\/8.04)|(Ubuntu 8.04)',
		'Ubuntu 6.06 LTS' => '(Ubuntu\/6.06)|(Ubuntu 6.06)',
		'Red Hat Linux' => '(Red Hat)',
		'Red Hat Enterprise Linux' => '(Red Hat Enterprise)',
		'Fedora 17' => '(Fedora\/17)|(Fedora 17)',
		'Fedora 16' => '(Fedora\/16)|(Fedora 16)',
		'Fedora 15' => '(Fedor\/15)|(Fedora 15)',
		'Fedora 14' => '(Fedora\/14)|(Fedora 14)',
		'Chromium OS' => '(ChromiumOS)',
		'Google Chrome OS' => '(ChromeOS)',

		// Kernel
		'Linux' => '(Linux)|(X11)',

		// BSD Operating Systems
		'OpenBSD' => '(OpenBSD)',
		'FreeBSD' => '(FreeBSD)',
		'NetBSD' => '(NetBSD)',

		// Mobile Devices
		'Android' => '(Android)',
		'iPod' => '(iPod)',
		'iPhone' => '(iPhone)',
		'iPad' => '(iPad)',

		// DEC Operating Systems
		'OS/8' => '(OS/8)|(OS8)',
		'Older DEC OS' => '(DEC)|(RSTS)|(RSTS/E)',
		'WPS-8' => '(WPS-8)|(WPS8)',

		// BeOS Like Operating Systems
		'BeOS' => '(BeOS)|(BeOS r5)',
		'BeIA' => '(BeIA)',

		// OS/2 Operating Systems
		'OS/2 2.0' => '(OS/220)|(OS/2 2.0)',
		'OS/2' => '(OS/2)|(OS2)',

		// Search engines
		'Search engine or robot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(msnbot)|(Ask Jeeves/Teoma)|(ia_archiver)'
	);










	/////////////////////////////////////////////////////////
	// $url					- url где лежит файл
	// $to_file				- путь куда ложить файл, если не задан, функция вернет содержимое файла
	// $other_options		- другие CURL заголовки
	// timeout				- таймаут запроса
	static function get_file( $source, $to_file=false, $other_options=array(), $timeout=28800 ) {

		self :: logging( );
		$microtime_start = microtime( true );

		///////////////////////////////////////////// 
		// РАБОТА С ФАЙЛАМИ

		if ( !self :: is_url( $source ) ) {
			if ( !$to_file ) return file_get_contents( $source );
			else {
				if ( !copy( $source, $to_file ) ) {
					// Registry :: __instance( ) -> logging -> error( "Не удалось скопировать файл " . $source . " в " . $to_file );
					return false;
				}
				else {
					// Registry :: __instance( ) -> logging -> success( "Файл успешно скопирован " . $source );
					return $info;
				}
			}
		}

		////////////////////////////////////////////
		// РАБОТА С URL
		// информация об удаленном файле
		$stat_url = @get_headers( $source, 1 );

		// Что то не так
		if ( !$stat_url || empty( $stat_url[ 0 ] ) || $stat_url[ 0 ] != 'HTTP/1.1 200 OK' ) {
			$stat_url[ 0 ] = ( isset( $stat_url[ 0 ] ) ) ? $stat_url[ 0 ] : 'null';
			// Registry :: __instance( ) -> logging -> error( "Сервер вернул " . $stat_url[ 0 ], true );
			return false;
		}

		$options = array(
			CURLINFO_HEADER_OUT				=>	false,
			CURLOPT_TIMEOUT					=>	$timeout, // set this to 8 hours so we dont timeout on big files
			// https | ssl
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
		);

		///////////////////////////////////////////////////////
		if ( $to_file ) {
			$file_exists = file_exists( $to_file );
			if ( $file_exists ) {
				// информация о локальном файле
				$stat_file = stat( $to_file );
				// Файл уже скачан
				if ( $stat_url[ 'Content-Length' ] == $stat_file[ 'size' ] ) {
					// Registry :: __instance( ) -> logging -> debug( "Файл не был загружен, т.к. локальная копия файла актуальна [" . $stat_url[ 'Content-Length' ] . " == " . $stat_file[ 'size' ] . "]", true );
					return true;
				}
			}
			$fp = fopen( $to_file, "w" );
			$options[ CURLOPT_FILE ] = $fp;
		}
		else {
			$options[ CURLOPT_RETURNTRANSFER ] = true;
		}
		///////////////////////////////////////////////////////

		//$options = array_merge( $options, $other_options );

		$ch = curl_init( $source );
		curl_setopt_array( $ch, $options );
		$result = curl_exec( $ch );
		$info = curl_getinfo( $ch );
		curl_close( $ch );

		// исполняемое время | размер файла на сервере | размер слитого файла
		// echo ( microtime( true ) - $microtime_start ) . " | " . $info[ "download_content_length" ] . " | " . $info[ "size_download" ] . " \n";

		if ( $info[ 'http_code' ] == '200' ) {

			$url_info = parse_url( $source );

			// копирует файл
			if ( $to_file ) {
				// Registry :: __instance( ) -> logging -> success( "Файл успешно загружен " . $source );
				return $info;
			}
			// читаем
			else {
				// Registry :: __instance( ) -> logging -> success( "Файл успешно прочитан " . $source );
				return $result;
			}
		}
		else {
			// Registry :: __instance( ) -> logging -> error( "Что то пошло не так " . $source );
			return false;
		}
	}





	
	

	// ищет в файле needle до первого вхождения и возвращает строку
	public static function search_string_in_file( $haystack, $needle ) {

		self :: logging( );

		// Registry :: __instance( ) -> logging -> debug( "Ищем в файле [" . $haystack . "] \"" . $needle . "\"", true );

		$return = false;
		$fo = @fopen( $haystack, 'r' );
		if ( !$fo ) {
			// Registry :: __instance( ) -> logging -> error( "Не возможно открыть файл " . $haystack, true );
			return false;
		}
		while ( !feof( $fo ) ) {
			$line = fgets( $fo, 4096 );
			if ( strpos( $line, $needle	) !== false ) {
				$return = $line;
				// Registry :: __instance( ) -> logging -> success( "Нашли!", true );
				break;
			}
		}
		if ( !$return ) {
			// Registry :: __instance( ) -> logging -> debug( "Не нашли!", true );
		}
		fclose( $fo );
		return $return;
	}
	
	
	// ищет в файле needle до первого вхождения и возвращает строку
	public static function replace_string_in_file( $haystack, $needle, $replace ) {

		self :: logging( );

		$return = false;
		$tmp = false;

		$fo = @fopen( $haystack, 'r' );
		if ( !$fo ) {
			// Registry :: __instance( ) -> logging -> error( "Не возможно открыть файл " . $haystack, true );
			return false;
		}
		while ( !feof( $fo ) ) {
			$line = fgets( $fo, 4096 );

				$tmp = @file_put_contents( $haystack . '_tmp', str_replace( $needle, $replace, $line ), FILE_APPEND | LOCK_EX );

		}
		fclose( $fo );

		unlink( $haystack );
		if ( !rename( $haystack . '_tmp', $haystack ) ) {
			// Registry :: __instance( ) -> logging -> error( "Не могу переименовать файл! [" . $haystack . '_tmp] на без "_tmp"', true );
			return false;
		}

		return true;
	}



	
	
	// копирует данные из одного места в дугое
	public static function copy( $from_path, $to_path ) {

		self :: create_dirs_form_path( $to_path );

		if ( is_dir( $from_path ) ) {

			  chdir( $from_path ); 

			  $handle = opendir( '.' ); 

			  while ( ( $file = readdir( $handle ) ) !== false ) {
				/** то что не нужно копировать */
				if (  ( $file != "." ) &&
					  ( $file != ".." ) ) {
				  if ( is_dir( $file ) ) {
					self :: copy( $from_path . $file . DS, $to_path . $file . DS );
					chdir( $from_path );
				  }
				  /** копирование файла */
				  if ( is_file( $file ) ) {
					@copy( $from_path . $file, $to_path . $file );
				  }
				}
			  }

			closedir( $handle ); 

		}
		else {
			@copy( $from_path, $to_path );
		}
	}



	public static function create_dirs( $source='', $to_file='' ) {
		if ( self :: is_url( $source ) ) {
			return self :: create_dirs_form_url( $source, $to_file );
		}
		else return self :: create_dirs_form_path( $source, $to_file );
	}



	// создаем структуру папок
	public static function create_dirs_form_url( $url='', $to_file='' ) {
		
		$url_info = parse_url( $url );
		$url = str_replace( $url_info[ 'scheme' ] . '://' . $url_info[ 'host' ] . '/', '', $url );
		$url = str_replace( '/', DIRECTORY_SEPARATOR, $url );

		$ex_url = explode( DIRECTORY_SEPARATOR, $url );
		$file_name = end( $ex_url );

		$create_dir = $to_file;
		foreach ( $ex_url as $dir ) {
			if ( !$dir || $dir == $file_name ) continue;
			$create_dir .= $dir;
			@mkdir( $create_dir, 0777, true );
			$create_dir .= DIRECTORY_SEPARATOR;
		}
		return $create_dir . $file_name;
	}


	// удаляем директорию и файлы в ней рекурсивно (Остарожнее с ней)
	public static function rmdir( $src ) {

			if ( !file_exists( $src ) ) return false;
	
			$dir = opendir( $src );
			while( false !== ( $file = readdir( $dir ) ) ) {
				if ( ( $file != '.' ) && ( $file != '..' ) ) {
					$full = $src . DIRECTORY_SEPARATOR . $file;
					if ( is_dir( $full ) ) {
						self :: rmdir( $full );
					}
					else {
						unlink( $full );
					}
				}
			}
			closedir( $dir );
			rmdir( $src );

	}

	/////////////////////////////
	// создаем структуру папок
	// $to_file
	public static function create_dirs_form_path( $to_file, $path='' ) {

		$to_file = str_replace( $path, '', $to_file );
		$ex_to_file = explode( DIRECTORY_SEPARATOR, $to_file );
		$file_name = end( $ex_to_file );
		if ( $path == '' && count( $ex_to_file ) == 1 && !file_exists( $to_file ) ) {
			@mkdir( $to_file, 0777, true );
		}

		$create_dir = $path;

		foreach ( $ex_to_file as $dir ) {
			if ( !$dir || $file_name != '' && $dir == $file_name ) continue;
			$create_dir .= $dir;
			@mkdir( $create_dir, 0777, true );
			$create_dir .= DIRECTORY_SEPARATOR;
		}
		return $create_dir . $file_name;
	}




	/**
	* detect operation system from header_info
	*
	* @header_info string $header_info - text infomation PC
	* @return string - operation system name
	* @return bool false - dont is detected
	*/
	public static function get_os( $header_info ) {
		foreach( self :: $oses as $os => $pattern ) {
			if ( preg_match( '/' . $pattern . '/i', $header_info ) ) {
				return $os;
			}
		}

		return false; 

	}


	/**
	* detect cli mode
	*
	* @return bool true - run console application
	* @return bool false - run other application
	*/
	public static function is_console( ) {
		if ( PHP_SAPI === 'cli' ) return true;
		return false;
	}




	/**
	* detect operation system is Windows
	*
	* @return bool true - is windows
	* @return bool false - other operation system
	*/
	public static function is_windows( ) {
		$os = self :: get_os( php_uname( ) );
		if ( strtoupper( substr( $os, 0, 3 ) ) === 'WIN' ) {
			return true;
		}
		return false;
	}




	/**
	* determines source is URL (URI)
	*
	* @source string $source - any string
	* @return bool true - source is URL
	* @return bool false - source dont URL
	*/
	public static function is_url( $source ) {
		return preg_match( '/^((http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}((:[0-9]{1,5})?\\/.*)?)$/i', $source );
	}



	/**
	* cycle for short variable names
	*
	* @iteration int $iteration - begin position
	* @prefix string $prefix - prefix
	* @postfix string $postfix - postfix
	* @return string - short variable names
	*/
	public static function short_vars( $iteration=0, $prefix=null, $postfix=null, $string=null ) {
		if ( !$string ) $string = 'a';
		for( $i = 0; $i < $iteration; $i++ ) {
			//echo $string . " ";
			//if ( ( $i + 1 ) % 52 == 0 ) echo "\n";
			self :: _increment_short_vars( $string );
		}
		return $prefix . $string . $postfix;
	}


	/**
	* generates short variable names
	*
	* @string string &$string - link self :: short_vars method
	*/
	public static function _increment_short_vars( &$string ) {

		$lenght = strlen( $string );
		$last_char = substr( $string, -1 );
		$rest = substr( $string, 0, -1 );

		switch ( $last_char ) {
			case '':
				$next = 'a';
				break;
			case 'z':
				$next = 'A';
				break;
			case 'Z':
				if ( $lenght > 1 ) $next = '0';
				else {
					self :: _increment_short_vars( $rest );
					$next = 'a';
				}
				break;
			case '9':
					self :: _increment_short_vars( $rest );
				$next = 'a';
				break;
			default:
				$next = ++$last_char;

		}

		$string = $rest . $next;

	}


	/**
	* rename system file if him exist
	*
	* @file string $file - path to file
	* @pref string $pref - prefix for save many copies file
	* @iteration int $iteration - recursive iteration
	* @return string - result path new file name
	*/
	public static function rename_if_file_exist( $file, $pref='_c_', $iteration=1 ) {
		if ( file_exists( $file ) ) {
			$file_path = explode( DIRECTORY_SEPARATOR, $file );
			$file_name = array_pop( $file_path );
			$file_path = implode( DIRECTORY_SEPARATOR, $file_path );
			$file_name = explode( '.', $file_name );
			$file_ext = array_pop( $file_name );
			$file_name = implode( '.', $file_name );
			$copy_position = mb_strpos( $file_name, $pref, 0, 'UTF-8' );
			if ( $copy_position !== false ) {
				$iteration = explode( $pref, $file_name );
				$iteration = end( $iteration );
				$iteration = ( int ) $iteration;
				++$iteration;
				$file_name = mb_substr( $file_name, 0, $copy_position, 'UTF-8' );
			}
			$file = $file_path . DIRECTORY_SEPARATOR . $file_name . $pref . $iteration . '.' . $file_ext;
			return self :: rename_if_file_exist( $file, $pref, $iteration );
		}
		else return $file;
	}


	/**
	* funtion return generated name from you PC uses two library files
	*
	* @file_adjective string $file_adjective - path to library adjectives words 
	* @file_noun string $file_noun - path to library nouns words 
	* @return array
	*/
	public static function generateName( $file_adjective, $file_noun ) {
		if ( !file_exists( $file_adjective ) ) throw new Exception( 'file adjective not found' );
		if ( !file_exists( $file_noun ) ) throw new Exception( 'file noun not found' );
		$uid = self :: computerUid( );
		$count_uid = strlen( $uid );
		$adjective = file( $file_adjective );
		$noun = file( $file_noun );
		$count_adjective = count( $adjective );
		$count_noun = count( $noun );
		$num_uid = ceil( $count_uid / 2 );
		$one_uid = substr( $uid, 0, $num_uid );
		$two_uid = substr( $uid, $num_uid, $count_uid );
		$one = self :: _fractionNum( $one_uid, $count_adjective - 1 );
		$two = self :: _fractionNum( $two_uid, $count_noun - 1 );
		$adjective = mb_strtoupper( trim( $adjective[ $one ] ), 'UTF-8' );
		$noun = mb_strtoupper( trim( $noun[ $two ] ), 'UTF-8' );
		return array( 'adjective' => $adjective, 'noun' => $noun );
	}


	/**
	* math fraction function
	*
	* @num int $num - numbper need fraction
	* @keys string $keys - system command
	* @return int - result
	*/
	public static function _fractionNum( $num, $max = 10000 ) {
		$num = (int) $num;
		$max = (int) $max;
		$res = $num % $max;
		if ( $res > $max ) {
			$res = self :: _fractionNum( $res, $max );
		}
		return $res;
	}



	/**
	* return PC UID
	*
	* @return array
	*/
	public static function computerUid( ) {
		$info = self :: computerInfo( );
		$uid = var_export( $info, TRUE );
		//var_dump( $uid );
		$uid = base64_encode( $uid );
		//$uid = md5( $uid );
		return sprintf( '%u', crc32( $uid ) );
	}


	/**
	* return hardware UID
	*
	* @return array
	*/
	public static function hardvareUid( ) {
		$info = self :: computerInfo( );
		unset( $info[ 'OS' ] );
		unset( $info[ 'USER' ] );
		unset( $info[ 'HOMEPATH' ] );
		unset( $info[ 'PROCESSOR_ARCHITECTURE' ] );
		unset( $info[ 'COMPUTERNAME' ] );
		$uid = var_export( $info, TRUE );
		$uid = base64_encode( $uid );
		return sprintf( '%u', crc32( $uid ) );
	}


	/**
	* return hardware info of PC
	*
	* @return array
	 *		'OS'                                   - operation system
	 *		'USER'                                 - user PC
	 *		'HOMEPATH'                             - homepath user
	 *		'PROCESSOR_ARCHITECTURE'               - PROCESSOR ARCHITECTURE
	 *		'PROCESSOR_IDENTIFIER'                 - PROCESSOR IDENTIFIER
	 *		'PROCESSOR_LEVEL'                      - PROCESSOR_LEVEL
	 *		'NUMBER_OF_PROCESSORS'				   - NUMBER OF PROCESSORS CORE
	*/
	public static function computerInfo( ) {

		$info = array(
			'OS' => null,
			'USER' => null,
			'HOMEPATH' => null,
			'COMPUTERNAME' => null,
			'PROCESSOR_ARCHITECTURE' => null,
			'PROCESSOR_IDENTIFIER' => null,
			'PROCESSOR_LEVEL' => null,
			'NUMBER_OF_PROCESSORS' => null,
		);

		if ( self :: is_windows( ) ) {
			exec( 'ver', $os );
			$os = end( $os );
			$info[ 'OS' ] = $os;
			$info[ 'COMPUTERNAME' ] = $_SERVER[ 'COMPUTERNAME' ];
			$info[ 'USER' ] = $_SERVER[ 'USERNAME' ];
			$info[ 'HOMEPATH' ] = $_SERVER[ 'HOMEPATH' ];
			$info[ 'PROCESSOR_ARCHITECTURE' ] = $_SERVER[ 'PROCESSOR_ARCHITECTURE' ];
			$info[ 'PROCESSOR_IDENTIFIER' ] = $_SERVER[ 'PROCESSOR_IDENTIFIER' ];
			$info[ 'PROCESSOR_LEVEL' ] = $_SERVER[ 'PROCESSOR_LEVEL' ];
			$info[ 'NUMBER_OF_PROCESSORS' ] = $_SERVER[ 'NUMBER_OF_PROCESSORS' ];
		}
		else {

			/////////////////// OS
			exec( 'cat /proc/version', $os );
			$os = end( $os );
			$info[ 'OS' ] = $os;

			//////////////////// COMPUTERNAME
			exec( 'uname --nodename', $computername );
			$computername = end( $computername );
			$info[ 'COMPUTERNAME' ] = $computername;

			//////////////////// USER, HOMEPATH
			$info[ 'USER' ] = $_SERVER[ 'USER' ];
			$info[ 'HOMEPATH' ] = $_SERVER[ 'HOME' ];

			//////////////////// PROCESSOR...
			exec( 'getconf LONG_BIT', $architecture );
			$architecture = end( $architecture );
			$architecture = ( $architecture == '32' ) ? 'x86' : $architecture;
			$architecture = ( $architecture == '64' ) ? 'x64' : $architecture;

			$cpu = array( 'cpu family' => true, 'model' => true, 'stepping' => true, 'vendor_id' => true, 'cpu cores' => true );
			exec( 'cat /proc/cpuinfo', $strings );
			foreach ( $strings as $string ) {
				if ( strpos( $string, ':' ) === false ) continue;
				$ex_string = explode( ':', $string );
				$key = trim( $ex_string[ 0 ] );
				$value = trim( $ex_string[ 1 ] );
				if ( !isset( $cpu[ $key ] ) ) continue;
				$cpu[ $key ] = $value;
			}
			$info[ 'PROCESSOR_ARCHITECTURE' ] = $architecture;
			$info[ 'PROCESSOR_IDENTIFIER' ] =	$architecture . " Family " . 
												$cpu[ 'cpu family' ] . " Model " .
												$cpu[ 'model' ] . " Stepping " . 
												$cpu[ 'stepping' ] . ", " . $cpu[ 'vendor_id' ];

			$info[ 'PROCESSOR_LEVEL' ] = $cpu[ 'cpu family' ];
			$info[ 'NUMBER_OF_PROCESSORS' ] = $cpu[ 'cpu cores' ];

		}
		return $info;
	}


	
	/**
	* parse cli custom param
	*
	* @cmd string $cmd - system command
	* @keys string $keys - system command
	* @return array - params cli
	*/
	public static function _get_params_from_cmd( $cmd, $keys=array(), $separator_key_value=':' ) {
		exec( $cmd, $strings );
		foreach ( $strings as $string ) {
			if ( strpos( $string, ':' ) === false ) continue;
			$ex_string = explode( ':', $string );
			$key = trim( $ex_string[ 0 ] );
			$value = trim( $ex_string[ 1 ] );
			if ( !isset( $keys[ $key ] ) ) continue;
			$keys[ $key ] = $value;
		}
		return $keys;
	}



	/**
	* implode parse_url array
	*
	* @parsed array $parsed - parse_url array
	* @return string parse_url array
	*/
	public static function glue_url( $parsed ) {
		if ( !is_array( $parsed ) ) return false;
		$uri = isset( $parsed[ 'scheme' ] ) ? $parsed[ 'scheme' ] . ':' . ( ( strtolower( $parsed[ 'scheme' ] ) == 'mailto' ) ? '' : '//' ) : '';
		$uri .= isset( $parsed[ 'user' ] ) ? $parsed[ 'user' ] . ( $parsed[ 'pass' ] ? ':' . $parsed[ 'pass' ] : '' ) . '@' : '';
		$uri .= isset( $parsed[ 'host' ]) ? $parsed[ 'host' ] : '';
		$uri .= isset( $parsed[ 'port' ] ) ? ':' . $parsed[ 'port' ] : '';
		if ( isset( $parsed[ 'path' ] ) ) {
			$uri .= ( substr( $parsed[ 'path' ], 0, 1) == '/' ) ? $parsed[ 'path' ] : '/' . $parsed[ 'path' ];
		}
		$uri .= isset( $parsed[ 'query' ] ) ? '?' . $parsed[ 'query' ] : '';
		$uri .= isset( $parsed[ 'fragment' ] ) ? '#' . $parsed[ 'fragment' ] : '';
		return $uri;
	}


	/**
	* implements resolving a relative URL according to RFC 2396 section 5.2. 
	*
	* @param string $base базовый URI (можно без "http://")
	* @param string $url ссылка (абсолютный URI, абсолютный путь на сайте, относительный путь)
	* @return string relative URL
	*/
	public static function resolve_url( $base, $url ) {
		if ( !strlen( $base ) ) return $url;
		// Step 2
		if ( !strlen( $url ) ) return $base;
		// Step 3
		if ( preg_match( '!^[a-z]+:!i', $url ) ) return $url;
		$base = parse_url( $base );
		if ( $url{0} == "#" ) {
				// Step 2 (fragment)
				$base[ 'fragment' ] = substr( $url, 1 );
				return self :: glue_url( $base );
		}
		unset( $base[ 'fragment' ] );
		unset( $base[ 'query' ] );
		if ( substr( $url, 0, 2 ) == "//" ) {
			// Step 4
			return self :: glue_url( array(
					'scheme' => $base['scheme'],
					'path' => $url,
			));
		}
		else if ( $url{0} == "/" ) {
			// Step 5
			$base['path'] = $url;
		}
		else {
			// Step 6
			$path = explode( '/', $base[ 'path' ] );
			$url_path = explode( '/', $url );
			// Step 6a: drop file from base
			array_pop( $path );
			// Step 6b, 6c, 6e: append url while removing "." and ".." from
			// the directory portion
			$end = array_pop( $url_path );
			foreach ( $url_path as $segment ) {
					if ( $segment == '.' ) {
						// skip
					}
					else if ( $segment == '..' && $path && $path[ sizeof( $path ) - 1 ] != '..' ) {
						array_pop( $path );
					}
					else {
						$path[ ] = $segment;
					}
			}
			// Step 6d, 6f: remove "." and ".." from file portion
			if ( $end == '.' ) {
					$path[ ] = '';
			}
			else if ( $end == '..' && $path && $path[ sizeof( $path ) - 1 ] != '..' ) {
				$path[ sizeof( $path )-1 ] = '';
			}
			else {
				$path[ ] = $end;
			}
			// Step 6h
			$base[ 'path' ] = join( '/', $path );
		}
		
		// Step 7
		return self :: glue_url( $base );

	}





	/**
	 * Перевод текста из кириллицы в транслит
	 *
	 * @param string $string входная строка
	 * @sep string $string на что меняем пробелы и табуляции
	 * @return string транслитированная строка
	 */
	public static function translit( $string, $sep='-' ) {

		$table = array(

			'А' => 'A',	'Б' => 'B',	'В' => 'V', 'Г' => 'G',	'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO',
			'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M',
			'Н' => 'N', 'О' => 'O',	'П' => 'P', 'Р' => 'R',	'С' => 'S',	'Т' => 'T',	'У' => 'U',
			'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',	'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'CSH',	'Ь' => '',
			'Ы' => 'Y',	'Ъ' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',

			'а' => 'a',	'б' => 'b',	'в' => 'v',	'г' => 'g',	'д' => 'd',	'е' => 'e',	'ё' => 'yo',
			'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm',
			'н' => 'n',	'о' => 'o',	'п' => 'p', 'р' => 'r',	'с' => 's',	'т' => 't',	'у' => 'u',
			'ф' => 'f',	'х' => 'h',	'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'csh',
			'ь' => '', 'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', '«' => '', '»' => ''

		);

		$output = str_replace(
			array_keys( $table ),
			array_values( $table ),
			$string
		);

		$output = strtolower( trim( $output ) );
		$output = preg_replace( "/\s+/", $sep, $output );
		$output = preg_replace( "/[^A-Za-z0-9_\-]/", "", $output );

		return $output;
	}


	//////////////////////////////////////////////////////////////////////
	// STD OUT
	public static function out( $str ) {
		if ( self :: is_windows( ) && self :: is_console( ) ){
			$str = iconv( 'UTF-8', 'CP866', $str );
		}
		return $str;
	}


	//////////////////////////////////////////////////////////////////////
	// TAR & GZIP


	public static function gzip( $from_path, $to_path=null, $rm=true ) {
		if ( !file_exists( $from_path ) ) return false;
		$to_path = ( $to_path ) ? ' ' . $to_path : '';
		exec( 'tar cvzf' . $to_path . ' ' . $from_path, $out );
		if ( $rm ) self :: rmdir( $from_path );
		return $out;
	}


	public static function gzip_list( $arch_name ) {
		if ( !file_exists( $arch_name ) ) return false;
		exec( 'tar -tvf ' . $arch_name, $out );
		return $out;
	}

	
	public static function ungzip( $from_path, $to_path=null, $rm=true ) {
		if ( !file_exists( $from_path ) ) return false;
		self :: create_dirs_form_path( $to_path );
		$to_path = ( $to_path ) ? ' -C ' . $to_path : '';
		exec( 'tar -xvzf ' . $from_path . '' . $to_path, $out );
		if ( $rm ) unlink( $from_path );
		return $out;
	}


}
