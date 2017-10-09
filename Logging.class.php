<?php

class Logging {

	public $to_file; // в какой файл будем писать
	public $source; // сервис в котором ведется логирование
	public $user;
	public $separator = "\t";

	public $__list = array( );

	public $_levels = array(

									'CRITICAL'	=> array(

											'lv'		=> 0,
											'color'		=> 'black',
											'bgcolor'	=> 'while'

										),

									'ERROR'		=> array(

											'lv'		=> 1,
											'color'		=> 'gray',
											'bgcolor'	=> 'red'

										),

									'WARNING'	=> array(

											'lv'		=> 2,
											'color'		=> 'yellow',
											'bgcolor'	=> 'black'

										),

									'INFO'		=> array(

											'lv'		=> 3

									),

									'SUCCESS'	=> array(

											'lv'		=> 4,
											'color'		=> 'green'

										),

									'DEBUG'		=>  array(

											'lv'		=> 5,
											'color'		=> 'cyan'

										),
								);


	protected $_charset = 'UTF-8';
	protected $_os;
	protected $_mydir;
	protected $_session;
	protected $_filename; // название файла логов

	protected $_level_log = 'DEBUG';
	protected $_level_stdout = 'SUCCESS';

	protected $_fp; // дескриптор

	protected $_last_time_log = 0;
	protected $_last_time_display = 0;
	

	public function __construct( $config ) {

		$this -> _last_time_log = microtime( true );
		$this -> _last_time_display = microtime( true );
	
		$this -> _os = Os :: get_os( PHP_OS );
		$this -> _mydir = realpath( dirname( __FILE__ ) );
		$path_parts = pathinfo( __FILE__ );


		if ( isset( $config[ 'to_file' ] ) ) {
			Os :: create_dirs_form_path( $config[ 'to_file' ], PATH_DB );
			$this -> _filename = $config[ 'to_file' ];
		}
		else {
			$this -> _session = $this -> _date( 'log' );
			$path_parts = str_replace( '.' . $path_parts[ 'extension' ], '', $path_parts[ 'basename' ] );
			$filename = $path_parts . '.log';
			$f_exist = file_exists( $filename );
			if ( $f_exist ) {
				$uniq = $this -> _uniqid( $this -> _session );
				$filename = $path_parts . '_' . $uniq . '.log';
			}
			else {
				$filename = $path_parts . '.log';
			}
			$this -> _filename = $filename;
		}

		if ( isset( $config[ 'level_log' ] ) ) {
			$this -> _level_log = $config[ 'level_log' ];
		}

		if ( isset( $config[ 'level_stdout' ] ) ) {
			$this -> _level_stdout = $config[ 'level_stdout' ];
		}

		if ( !$this -> _fp = fopen( $this -> _filename, "a+" ) ) {
			throw new Exception( 'DONT_OPEN_FILE -> ' . $this -> _filename );
		}

		if ( isset( $config[ 'source' ] ) ) {
			$this -> source = $config[ 'source' ];
		}

		if ( isset( $config[ 'user' ] ) ) {
			$this -> user = $config[ 'user' ];
		}

		if ( isset( $config[ 'levels' ] ) ) {
			$this -> _levels = $config[ 'levels' ];
		}

		if ( isset( $config[ 'separator' ] ) ) {
			$this -> separator = $config[ 'separator' ];
		}

		return true;
	}


	public function __destruct( ) {
		fclose( $this -> _fp );
	}


	public function __call( $name, $arguments ) {
	
		if ( empty( $this -> _levels[ strtoupper( $name ) ] ) ) {
			throw new Exception( 'method ' . $name . ' undefined' );
		}

		if ( empty( $arguments[ 0 ] ) ) {
			$arguments[ 0 ] = '';
		}

		if ( empty( $arguments[ 1 ] ) ) {
			$arguments[ 1 ] = false;
		}

		$this -> write( $arguments[ 0 ], strtoupper( $name ), $arguments[ 1 ] );

    }


	public function read( ) {
		return true;
	}


	public function display( $str='', $level ) {

		$ex_str = explode( $this -> separator, $str );
		$date = $ex_str[ 0 ];
		$date = str_replace( date( "m-d-Y " ), '', $date );
		$ex_str[ 0 ] = $date;
		if ( Color :: isAnsi( ) ) unset( $ex_str[ 2 ] );
		else {
			$ex_str[2] = trim( $ex_str[2] );
		}

		$str = implode( "    ", $ex_str );

		//////////////////////////1111111111111111111111
		$str = $this -> style( $str, $level );

		if ( Os :: is_windows( ) && Os :: is_console( ) ) {
			$str = iconv( $this -> _charset, 'CP866', $str );
		}
		////////////////////////////11111111111111111111

		return $str;
	}


	public function write( $str='', $level='SUCCESS', $display=false ) {

		$input_level = $this -> _levels[ $level ][ 'lv' ];
		$log_allow_level = $this -> _levels[ $this -> _level_log ][ 'lv' ];

		$nstr = '';
		$nstr .= $this -> separator;

		$nstr .= $this -> _alignment( $level );
		$nstr .= $this -> separator;

		$nstr .= ( isset( $this -> source ) ) ? $this -> source . $this -> separator : '';
		$nstr .= ( isset( $this -> user ) ) ? $this -> user . $this -> separator : '';
		$nstr .= $str;
		$nstr .= PHP_EOL;

		$stdout_allow_level = $this -> _levels[ $this -> _level_stdout ][ 'lv' ];
		
		if ( $display || $input_level <= $stdout_allow_level ) {
		//////////////////////////1111111111111111111111
			echo $this -> display( $this -> _date( 'display' ) . $nstr, $level );
		//////////////////////////1111111111111111111111
		}

		if ( $input_level <= $log_allow_level ) {
			$write = $this -> _date( 'log' ) . $nstr;
			$this -> __list[ ] = $write;
			fwrite( $this -> _fp, $write, 8500 );
		}

	}

	// Стили уровней
	public function style( $str='', $level='SUCCESS' ) {

		if ( isset( $this -> _levels[ $level ] ) ) {
		
			foreach ( $this -> _levels[ $level ] as $key => $style ) {
				if ( $key == 'lv' ) continue;
				else if ( $key == 'bgcolor' ) {
					$method = 'bg' . strtoupper( substr( $style, 0, 1 ) ) . substr( $style, 1 );
					$str = Color :: $method( $str );
				}
				else if ( $key == 'color' ) {
					$str = Color :: $style( $str );
				}
				else {
					if ( $style ) $str = Color :: $key( $str );
				}
			}
		}

		return $str;
	}
	
	
	
	public function alignment( $str='', $maxlen ) {
		return $str . sprintf( '%-' . ( $maxlen - mb_strlen( $str, 'UTF-8' ) ) . 's', ' ' );
	}
	
	
	
	
	protected function _alignment( $str='' ) {
	
		$max_level = 0;

		foreach ( $this -> _levels as $key => $val ) {
			$key_strlen = mb_strlen( $key );
			if ( $max_level < $key_strlen ) {
				$max_level = $key_strlen;
			}
		}

		return $str . sprintf( '%-' . $max_level . 's', ' ' );
	}


	protected function _date( $type='log' ) {
		$now_time = microtime( true );
		list( $ts, $ms ) = explode( ".", $now_time );
		$period_time = $now_time - $this -> { '_last_time_' . $type };
		$period_time = substr( $period_time, 0, 6 );
		if ( $period_time <= 0 ) $period_time = '0.0000';
		// . ' ['.str_replace('.',',',$now_time).' - '.str_replace('.',',',$this -> { '_last_time_' . $type }) . ']' 
		$ret = date( 'm-d-Y H:i:s.', $ts ) . str_pad ( $ms, 4, '0' )  . $this -> separator . $period_time;
		$this -> { '_last_time_' . $type } = $now_time;
		return $ret;
	}


	// генерируем уникальный ID
	protected function _uniqid( $prefix='', $length=10 ) {
		// генерируем id и зашифровываем его
		$rnd_id = crypt( uniqid( rand( ), 1 ) ); 
		// убираем слэши
		$rnd_id = strip_tags( stripslashes( $rnd_id ) ); 
		// убираем точки и переворачиваем строку задом наперед
		$rnd_id = str_replace( ".", "", $rnd_id ); 
		$rnd_id = strrev( str_replace( "/", "", $rnd_id ) ); 
		// берем первые $length значений
		$rnd_id = substr( $rnd_id, 0, $length ); 
		return $rnd_id;
	}



}
