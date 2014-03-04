<?php

if ( ! function_exists('success'))
{
	function success($message = 'success')
	{
		return Result::success($message);
	}
}

if ( ! function_exists('error'))
{
	function error($message = 'error')
	{
		return Result::error($message);
	}
}

if ( ! function_exists('warning'))
{
	function warning($message = 'warning')
	{
		return Result::warning($message);
	}
}

if ( ! function_exists('info'))
{
	function info($message = 'info')
	{
		return Result::add($message);
	}
}
