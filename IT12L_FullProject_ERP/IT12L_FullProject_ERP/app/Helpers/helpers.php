<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('is_admin')) {
    /**
     * Check if current authenticated user is admin
     */
    function is_admin()
    {
        try {
            return Auth::check() && Auth::user() && Auth::user()->is_admin;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('is_customer')) {
    /**
     * Check if current authenticated user is a regular customer
     */
    function is_customer()
    {
        try {
            return Auth::check() && Auth::user() && !Auth::user()->is_admin;
        } catch (\Exception $e) {
            return false;
        }
    }
}