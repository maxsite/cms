<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Функции замены mbstring 
 */


if ( !function_exists('mb_strlen') )
{
	function mb_strlen($str, $encoding = '')
	{
		return strlen($str);
	}
}

if ( !function_exists('mb_substr') )
{
	function mb_substr($str, $start, $length = 0, $encoding = '')
	{
		return substr($str, $start, $length);
	}
}

if ( !function_exists('mb_strwidth') )
{
	function mb_strwidth($str, $encoding = '')
	{
		return 0;
	}
}

if ( !function_exists('mb_strtolower') )
{
	function mb_strtolower($str, $encoding = '')
	{
		return strtolower($str);
	}
}

if ( !function_exists('mb_strtoupper') )
{
	function mb_strtoupper($str, $encoding = '')
	{
		return strtoupper($str);
	}
}

if ( !function_exists('mb_convert_encoding') )
{
	function mb_convert_encoding($str, $to_encoding, $from_encoding = '')
	{
		return $str;
	}
}

if ( !function_exists('mb_stristr') )
{
	function mb_stristr($haystack, $needle = '', $part = '', $encoding = '')
	{
		return stristr ($haystack, $needle);
	}
}

if ( !function_exists('mb_strpos') )
{
	function mb_strpos($haystack, $needle, $offset = 0, $encoding = '')
	{
		return strpos ($haystack, $needle, $offset);
	}
}

if ( !function_exists('mb_stripos') )
{
	function mb_stripos($haystack, $needle, $offset = 0, $encoding = '')
	{
		return stripos ($haystack, $needle, $offset);
	}
}


# end file