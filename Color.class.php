<?php
/**
 * php-ansi-color
 */
 
class Color
{

    /**
     * Whether color codes are enabled or not
     *
     * Valid options:
     *     null - Auto-detected. Color codes will be enabled on all systems except Windows, unless it
     *            has a valid ANSICON environment variable
     *            (indicating that ANSICON is installed and running)
     *     false - will strip all tags and NOT output any ANSI color codes
     *     true - will always output color codes
     */
    public static $enabled = null; // supported ANSI COLOR


    public static $tags = array(
        '<black>'       => "\033[0;30m",
        '<red>'         => "\033[1;31m",
        '<green>'       => "\033[1;32m",
        '<yellow>'      => "\033[1;33m",
        '<blue>'        => "\033[1;34m",
        '<magenta>'     => "\033[1;35m",
        '<cyan>'        => "\033[1;36m",
        '<white>'       => "\033[1;37m",
        '<gray>'        => "\033[0;37m",
        '<darkRed>'     => "\033[0;31m",
        '<darkGreen>'   => "\033[0;32m",
        '<darkYellow>'  => "\033[0;33m",
        '<darkBlue>'    => "\033[0;34m",
        '<darkMagenta>' => "\033[0;35m",
        '<darkCyan>'    => "\033[0;36m",
        '<darkWhite>'   => "\033[0;37m",
        '<darkGray>'    => "\033[1;30m",
        '<bgBlack>'     => "\033[40m",
        '<bgRed>'       => "\033[41m",
        '<bgGreen>'     => "\033[42m",
        '<bgYellow>'    => "\033[43m",
        '<bgBlue>'      => "\033[44m",
        '<bgMagenta>'   => "\033[45m",
        '<bgCyan>'      => "\033[46m",
        '<bgWhite>'     => "\033[47m",
        '<bold>'        => "\033[1m",
        '<italics>'     => "\033[3m",
		'<underline>'	=> "\033[4m",
		'<blink>'		=> "\033[5m",
		'<hidden>'		=> "\033[8m",
		'<reverse>'		=> "\033[7m",
        '<reset>'       => "\033[0m"
    );
	
	
    public static $tags_kernel32 = array(
        '<black>'       => '<c__k32>' . 0 . '</c__k32>',
        '<red>'         => '<c__k32>' . 13 . '</c__k32>',
        '<green>'       => '<c__k32>' . 10 . '</c__k32>',
        '<yellow>'      => '<c__k32>' . 11 . '</c__k32>',
        '<blue>'        => '<c__k32>' . 12 . '</c__k32>',
        '<magenta>'     => '<c__k32>' . 13 . '</c__k32>',
        '<cyan>'        => '<c__k32>' . 14 . '</c__k32>',
        '<white>'       => '<c__k32>' . 15 . '</c__k32>',
        '<gray>'        => '<c__k32>' . 7 . '</c__k32>',
        '<darkRed>'     => '<c__k32>' . 1 . '</c__k32>',
        '<darkGreen>'   => '<c__k32>' . 2 . '</c__k32>',
        '<darkYellow>'  => '<c__k32>' . 3 . '</c__k32>',
        '<darkBlue>'    => '<c__k32>' . 4 . '</c__k32>',
        '<darkMagenta>' => '<c__k32>' . 5 . '</c__k32>',
        '<darkCyan>'    => '<c__k32>' . 6 . '</c__k32>',
        '<darkWhite>'   => '<c__k32>' . 7 . '</c__k32>',
        '<darkGray>'    => '<c__k32>' . 8 . '</c__k32>',
        '<bgBlack>'     => '<b__k32>' . 0 . '</b__k32>',
        '<bgRed>'       => '<b__k32>' . 16 . '</b__k32>',
        '<bgGreen>'     => '<b__k32>' . 32 . '</b__k32>',
        '<bgYellow>'    => '<b__k32>' . 48 . '</b__k32>',
        '<bgBlue>'      => '<b__k32>' . 64 . '</b__k32>',
        '<bgMagenta>'   => '<b__k32>' . 80 . '</b__k32>',
        '<bgCyan>'      => '<b__k32>' . 96 . '</b__k32>',
        '<bgWhite>'     => '<b__k32>' . 240 . '</b__k32>',
        '<bold>'        => '<b__k32>' . null . '</b__k32>',
        '<italics>'     => '<b__k32>' . null . '</b__k32>',
		'<underline>'	=> '<b__k32>' . null . '</b__k32>',
		'<blink>'		=> '<b__k32>' . null . '</b__k32>',
		'<hidden>'		=> '<b__k32>' . null . '</b__k32>',
		'<reverse>'		=> '<b__k32>' . null . '</b__k32>',
        '<reset>'       => '<r__k32>' . 7 . '</r__k32>'
    );
	
	
    /**
     * This is the primary function for converting tags to ANSI color codes
     * (see the class description for the supported tags)
     *
     * For safety, this function always appends a <reset> at the end, otherwise the console may stick
     * permanently in the colors you have used.
     *
     * @param string $string
     * @return string
     */
	public static function __callStatic( $name, $arguments ) {

		$dynwrap = self :: isDynWrap( );
		$tags = self :: $tags;
		if ( $dynwrap ) {
			$tags = self :: $tags_kernel32;
		}
	
	
		if ( empty( self :: $tags[ '<' . $name . '>' ] ) ) {
			throw new Exception( 'static method ' . $name . ' undefined' );
		}

		if ( empty( $arguments[ 0 ] ) ) {
			throw new Exception( 'arg 1 undefined' );
		}

		$string = $arguments[ 0 ];

        if ( self :: $enabled === null ) {
            self :: $enabled = !self :: isWindows() || ( self :: isAnsi( ) || $dynwrap );
        }
		
        if ( !self :: $enabled ) {
            // Strip tags (replace them with an empty string)
            return str_replace( array_keys( $tags ), '', $string );
        }
        // We always add a <reset> at the end of each string so that any output following doesn't continue the same styling
		$string = $tags[ '<' . $name . '>' ] . $string . '<reset>';
		// $com -> SetConsoleTextAttribute( $ch, 7 );

        return str_replace( array_keys( $tags ), $tags, $string );
    }

	// проверяет операционную систему
    public static function isWindows( ) {
        return strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN';
    }

	// проверяет вкличение анси в консоль
    public static function isAnsi( ) {
        return !empty( $_SERVER[ 'ANSICON' ] )
            && substr( $_SERVER[ 'ANSICON'], 0, 1 ) != '0';
    }

	
	public static function isDynWrap( ) {
		return ( file_exists( $_SERVER[ 'windir' ] . '\\System32\\dynwrap.dll' ) && file_exists( $_SERVER[ 'windir' ] . '\\System32\\kernel32.dll' ) );
	}
	
	
	public static function setDynWrap( ) {
		/*
			CALL regsvr32.exe "%systemroot%\system32\dynwrap.dll"
		*/
	}

}