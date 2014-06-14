<?php

class HttpStatus{
	
	public static $HTTP_STATUS_CONTINUE = 100;
	public static $HTTP_STATUS_SWITCHING_PROTOCOLS = 101;
	public static $HTTP_STATUS_PROCESSING = 102;
	
	public static $HTTP_STATUS_OK = 200;
	public static $HTTP_STATUS_CREATED = 201;
	public static $HTTP_STATUS_ACCEPTED = 202;
	public static $HTTP_STATUS_NON_AUTHORITIVE_INFORMATION = 203;
	public static $HTTP_STATUS_NO_CONTENT = 204;
	public static $HTTP_STATUS_RESET_CONTENT = 205;
	public static $HTTP_STATUS_PARTIAL_CONTENT = 206;
	
	public static $HTTP_STATUS_MULTIPLE_CHOICES = 300;
	public static $HTTP_STATUS_MULTIPLE_MOVED_PERMANENTLY = 301;
	public static $HTTP_STATUS_MULTIPLE_FOUND = 302;
	public static $HTTP_STATUS_MULTIPLE_SEE_OTHER = 303;
	public static $HTTP_STATUS_MULTIPLE_NOT_MODIFIED = 304;
	public static $HTTP_STATUS_MULTIPLE_USE_PROXY = 305;
	public static $HTTP_STATUS_MULTIPLE_SWITCH_PROXY = 306;
	public static $HTTP_STATUS_MULTIPLE_TEMP_REDIRECT = 307;
	public static $HTTP_STATUS_MULTIPLE_PERMANENT_REDIRECT = 308;
	
	public static $HTTP_STATUS_BAD_REQUEST = 400;
	public static $HTTP_STATUS_UNAUTHORIZED = 401;
	public static $HTTP_STATUS_PAYMENT_REQUIRED = 402;
	public static $HTTP_STATUS_FORBIDDEN = 403;
	public static $HTTP_STATUS_NOT_FOUND = 404;
	public static $HTTP_STATUS_METHOD_NOT_ALLOWED = 405;
	public static $HTTP_STATUS_NOT_ACCEPTABLE = 406;
	public static $HTTP_STATUS_PROXY_AUTH_REQUIRED = 407;
	public static $HTTP_STATUS_REQUEST_TIMEOUT = 408;
	public static $HTTP_STATUS_CONFLICT = 409;
	public static $HTTP_STATUS_GONE = 410;
	// todo add the others
	
	public static $HTTP_STATUS_INTERNAL_SERVER_ERROR = 500;
	public static $HTTP_STATUS_NOT_IMPLEMENTED = 501;
	public static $HTTP_STATUS_BAD_GATEWAY = 502;
	public static $HTTP_STATUS_SERVICE_UNAVALABLE = 503;
	public static $HTTP_STATUS_GATEWAY_TIMEOUT = 504;
	public static $HTTP_STATUS_HTTP_VERSION_NOT_SUPPORTED = 505;
	// todo add the others
	
	public static function isClientError($status){
		if($status >= $this::$HTTP_STATUS_BAD_REQUEST && $status < $this::$HTTP_STATUS_INTERNAL_SERVER_ERROR)
			return true;
		else
			return false;
	}
}

?>