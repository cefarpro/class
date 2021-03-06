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
        '<black>'       => 0,
        '<red>'         => 13,
        '<green>'       => 10,
        '<yellow>'      => 11,
        '<blue>'        => 12,
        '<magenta>'     => 13,
        '<cyan>'        => 14,
        '<white>'       => 15,
        '<gray>'        => 7,
        '<darkRed>'     => 1,
        '<darkGreen>'   => 2,
        '<darkYellow>'  => 3,
        '<darkBlue>'    => 4,
        '<darkMagenta>' => 5,
        '<darkCyan>'    => 6,
        '<darkWhite>'   => 7,
        '<darkGray>'    => 8,
        '<bgBlack>'     => 0,
        '<bgRed>'       => 16,
        '<bgGreen>'     => 32,
        '<bgYellow>'    => 48,
        '<bgBlue>'      => 64,
        '<bgMagenta>'   => 80,
        '<bgCyan>'      => 96,
        '<bgWhite>'     => 240,
        '<bold>'        => 7,
        '<italics>'     => 7,
		'<underline>'	=> 7,
		'<blink>'		=> 7,
		'<hidden>'		=> 7,
		'<reverse>'		=> 7,
        '<reset>'       => '<__k32_reset>'
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

		if ( empty( self :: $tags[ '<' . $name . '>' ] ) ) {
			throw new Exception( 'static method ' . $name . ' undefined' );
		}

		if ( empty( $arguments[ 0 ] ) ) {
			throw new Exception( 'arg 1 undefined' );
		}

		$string = $arguments[ 0 ];

        if ( self :: $enabled === null ) {
            self :: $enabled = !self :: isWindows() || ( self :: isAnsi( ) );
        }
		
        if ( !self :: $enabled ) {
            // Strip tags (replace them with an empty string)
            return str_replace( array_keys( self :: $tags ), '', $string );
        }
        // We always add a <reset> at the end of each string so that any output following doesn't continue the same styling
		$string = self :: $tags[ '<' . $name . '>' ] . $string . '<reset>';
		// $com -> SetConsoleTextAttribute( $ch, 7 );

        return str_replace( array_keys( self :: $tags ), self :: $tags, $string );
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
		return ( isset( $_SERVER[ 'windir' ] ) && file_exists( $_SERVER[ 'windir' ] . '\\System32\\dynwrap.dll' ) && file_exists( $_SERVER[ 'windir' ] . '\\System32\\kernel32.dll' ) );
	}
	
	
	public static function setDynWrap( ) {
		/*
			CALL regsvr32.exe "%systemroot%\system32\dynwrap.dll"
		*/
	}

}