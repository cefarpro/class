<?php

class Ssh_execute {

    private $host;

    private $user;

    private $pass;

    private $port;

    private $conn = false;

    private $error;

    private $stream;

    private $stream_timeout = 100;

	private $out_steam = array();
	
	
    private $log;

    private $lastLog;

    public function __construct ( $host, $user, $pass, $port, $serverLog ) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
        $this->sLog = $serverLog;

        if ( $this->connect ()->authenticate () ) {
            return true;
        }
    }

    public function isConnected () {
        return ( boolean ) $this->conn;
    }

    public function __get ( $name ) {
        return $this->$name;
    }

    public function connect () {
        $this->logAction ( "Connecting to {$this->host}" );
        if ( $this->conn = ssh2_connect ( $this->host, $this->port ) ) {
            return $this;
        }
        $this->logAction ( "Connection to {$this->host} failed" );
        throw new Exception ( "Unable to connect to {$this->host}" );
    }

    public function authenticate () {
        $this->logAction ( "Authenticating to {$this->host}" );
        if ( ssh2_auth_password ( $this->conn, $this->user, $this->pass ) ) {
            return $this;
        }
        $this->logAction ( "Authentication to {$this->host} failed" );
        throw new Exception ( "Unable to authenticate to {$this->host}" );
    }

    public function sendFile ( $localFile, $remoteFile, $permision = 0644 ) {
        if ( ! is_file ( $localFile ) ) throw new Exception ( "Local file {$localFile} does not exist" );
        $this->logAction ( "Sending file $localFile as $remoteFile" );

        $sftp = @ssh2_sftp ( $this->conn );
        $sftpStream = @fopen ( 'ssh2.sftp://' . $sftp . $remoteFile, 'w' );
        if ( ! $sftpStream ) {
            //  if 1 method failes try the other one
            if ( ! @ssh2_scp_send ( $this->conn, $localFile, $remoteFile, $permision ) ) {
                throw new Exception ( "Could not open remote file: $remoteFile" );
            }
            else {
                return true;
            }
        }

        $data_to_send = @file_get_contents ( $localFile );

        if ( @fwrite ( $sftpStream, $data_to_send ) === false ) {
            throw new Exception ( "Could not send data from file: $localFile." );
        }

        fclose ( $sftpStream );

        $this->logAction ( "Sending file $localFile as $remoteFile succeeded" );
        return true;
    }

    public function getFile ( $remoteFile, $localFile ) {
        $this->logAction ( "Receiving file $remoteFile as $localFile" );
        if ( ssh2_scp_recv ( $this->conn, $remoteFile, $localFile ) ) {
            return true;
        }
        $this->logAction ( "Receiving file $remoteFile as $localFile failed" );
        throw new Exception ( "Unable to get file to {$remoteFile}" );
    }

    public function cmd ( $cmd, $returnOutput = false ) {
        $this->logAction ( "Executing command $cmd" );
        $this->stream = ssh2_exec ( $this->conn, $cmd );
		$this->error = ssh2_fetch_stream( $this->stream, SSH2_STREAM_STDERR );

        if ( FALSE === $this->stream ) {
            $this->logAction ( "Unable to execute command $cmd" );
            throw new Exception ( "Unable to execute command '$cmd'" );
        }
        $this->logAction ( "$cmd was executed" );

        stream_set_blocking ( $this->error, true );
        stream_set_blocking ( $this->stream, true );
        $this->lastLog = stream_get_contents ( $this->stream );
        $this->lastLog .= stream_get_contents ( $this->error );
		
        $this->logAction ( "$cmd output: {$this->lastLog}" );
        fclose ( $this->stream );
        fclose ( $this->error );
        $this->log .= $this->lastLog;
        return ( $returnOutput ) ? $this->lastLog : $this;

    }

    public function shellCmd ( $cmds = array(), $returnOutput = false ) {
        $this->logAction ( "Openning ssh2 shell" );
        $this->shellStream = ssh2_shell ( $this->conn );

        sleep ( 1 );
        $out = '';
		$this -> out_steam = array( );
        while ( $line = fgets ( $this->shellStream ) ) {
            $out .= $line;
        }

        $this->logAction ( "ssh2 shell output: $out" );

        foreach ( $cmds as $cmd ) {
            $out = '';
            $this->logAction ( "Writing ssh2 shell command: $cmd" );
            fwrite ( $this->shellStream, "$cmd" . PHP_EOL );
            sleep ( 1 );
            while ( $line = fgets ( $this->shellStream ) ) {
                $out .= $line;
                //sleep ( 1 );
            }
			$this->logAction ( "Executing ssh2 shell command: $cmd end" );
			$this -> out_steam[ ] = substr( $out, strlen( $cmd . PHP_EOL ) );
        }

        $this->logAction ( "Closing shell stream" );
        fclose ( $this->shellStream );
		if ( $returnOutput ) return $this -> out_steam;
    }

    public function getLastOutput () {
        return $this->lastLog;
    }

    public function getOutput () {
        return $this->log;
    }

    public function disconnect () {
        $this->logAction ( "Disconnecting from {$this->host}" );
        // if disconnect function is available call it..
        if ( function_exists ( 'ssh2_disconnect' ) ) {
            ssh2_disconnect ( $this->conn );
        }
        else { // if no disconnect func is available, close conn, unset var
            @fclose ( $this->conn );
            $this->conn = false;
        }
        // return null always
        return NULL;
    }

    public function fileExists ( $path ) {
        $output = $this->cmd ( "[ -f $path ] && echo 1 || echo 0", true );
        return ( bool ) trim ( $output );
    }
	
	
	public function logAction( $str ) {
		echo $str . "\n";
	}
}





