<?php
require_once('gelf.php');

class gelfErrorHandler extends GELFMessage {
	
	
	public function handler($errno, $errstr='', $errfile='', $errline='') {
		
		// called by trigger_error()
		if(func_num_args() == 5) {
			$exception = null;
			list($errno, $errstr, $errfile, $errline) = func_get_args();
			$backtrace = array_reverse(debug_backtrace());
			
		// caught exception
		} else {
			$exc = func_get_arg(0);
			$errno = $exc->getCode();
			$errstr = $exc->getMessage();
			$errfile = $exc->getFile();
			$errline = $exc->getLine();
			$backtrace = $exc->getTrace();
		}
		
		$err = $this->errorType($errno);
		$errShortMsg = "$err: $errstr";
		
		
		$stacktrace = $this->prettifyBackTrace($backtrace);
		
		$errlevel = $this->getSyslogLevel($errno);
		$this->setShortMessage($errShortMsg);
		$this->setFullMessage($stacktrace);
		$this->setHost(gethostname());
		$this->setLevel($errlevel);
		$this->setFile($errfile);
		$this->setLine($errline);
		$this->setAdditional('php_version', PHP_VERSION);
		$this->setAdditional('php_os', PHP_OS);
		
		$this->send();
		return true;
	}
	
	private function getSyslogLevel($errno = E_USER_NOTICE) {
		$errlevel = 0;
		switch ($errno) {
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
			$errlevel = 2;
			break;
			
		case E_ERROR:
		case E_USER_ERROR:
			$errlevel = 3;
			break;
			
		case E_WARNING:
		case E_USER_WARNING:
		case E_CORE_WARNING:
		case E_COMPILE_WARNING:
			$errlevel = 4;
			break;

		case E_NOTICE:
		case E_DEPRECATED:
		case E_USER_DEPRECATED:
		case E_USER_NOTICE:
			$errlevel = 5;
			break;
		
		case E_STRICT:
			$errlevel = 6;
			break;
			
		default:
			$errlevel = 7;
			break;
		}
		return $errlevel;
	}
	
	private function errorType($k) {
		$errorType = array (
			E_ERROR              => 'ERROR',
			E_WARNING            => 'WARNING',
			E_PARSE              => 'PARSING ERROR',
			E_NOTICE             => 'NOTICE',
			E_CORE_ERROR         => 'CORE ERROR',
			E_CORE_WARNING       => 'CORE WARNING',
			E_COMPILE_ERROR      => 'COMPILE ERROR',
			E_COMPILE_WARNING    => 'COMPILE WARNING',
			E_USER_ERROR         => 'USER ERROR',
			E_USER_WARNING       => 'USER WARNING',
			E_USER_NOTICE        => 'USER NOTICE',
			E_STRICT             => 'STRICT NOTICE',
			E_RECOVERABLE_ERROR  => 'RECOVERABLE ERROR'
		);
		return isset($errorType[$k]) ? $errorType[$k] : 'CAUGHT EXCEPTION';
	}
	
	
	private function prettifyBackTrace($backtrace) {
		$trace = '';
		foreach ($backtrace as $v) {
			if (isset($v['class'])) {
				$trace .= 'in class '.$v['class'].'::'.$v['function'].'(';
				if (isset($v['args'])) {
					$separator = '';
					foreach($v['args'] as $arg ) {
						$trace .= $separator . $this->getArgument($arg);
						$separator = ', ';
					}
				}
				$trace .= ')';
			} elseif (isset($v['function']) && empty($trace)) {
				$trace = 'in function '.$v['function'].'(';
				if (!empty($v['args'])) {
					$separator = '';
					foreach($v['args'] as $arg ) {
						$trace .= $separator . $this->getArgument($arg);
						$separator = ', ';
					}
				}
				$trace .= ') ';
			}
			$trace .= "\n";
		}
		return $trace;
	}
	
	
	private function getArgument($arg) {
		$separator = ',';
		switch (strtolower(gettype($arg))) {
		case 'string':
			return( '"'.str_replace( array("\n"), array(''), $arg ).'"' );
		case 'boolean':
            return (bool)$arg;
		case 'object':
			return 'object('.get_class($arg).')';
		case 'array':
			$ret = array();
			foreach ($arg as $k => $v) {
				$ret[] = '$'.$k;
			}
			return 'array(' . implode(', ',$ret) . ')';
		case 'resource':
			return 'resource('.get_resource_type($arg).')';

		default:
			return var_export($arg, true);
		}
	}
}
