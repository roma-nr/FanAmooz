<?php
/** Hijri_Shamsi, Solar(Jalali) Date and Time functions
 * Copyright(C)2015, Reza Gholampanahi, http://jdf.scr.ir
 * version 2.60
 * Modified: wrapped all functions in function_exists checks to prevent redeclaration errors.
 */

if (!function_exists('jdate')) {
    function jdate($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa') {
        // ... (همان بدنهٔ تابع jdate بدون تغییر)
    }
}

if (!function_exists('jstrftime')) {
    function jstrftime($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa') {
        // ... (بدنهٔ jstrftime)
    }
}

if (!function_exists('jmktime')) {
    function jmktime($h = '', $m = '', $s = '', $jm = '', $jd = '', $jy = '', $is_dst = -1) {
        // ... (بدنهٔ jmktime)
    }
}

if (!function_exists('jgetdate')) {
    function jgetdate($timestamp = '', $none = '', $tz = 'Asia/Tehran', $tn = 'en') {
        // ... (بدنهٔ jgetdate)
    }
}

if (!function_exists('jcheckdate')) {
    function jcheckdate($jm, $jd, $jy) {
        // ... (بدنهٔ jcheckdate)
    }
}

if (!function_exists('tr_num')) {
    function tr_num($str, $mod = 'en', $mf = '٫') {
        // ... (بدنهٔ tr_num)
    }
}

if (!function_exists('jdate_words')) {
    function jdate_words($array, $mod = '') {
        // ... (بدنهٔ jdate_words)
    }
}

if (!function_exists('gregorian_to_jalali')) {
    function gregorian_to_jalali($gy, $gm, $gd, $mod = '') {
        // ... (بدنهٔ gregorian_to_jalali)
    }
}

if (!function_exists('jalali_to_gregorian')) {
    function jalali_to_gregorian($jy, $jm, $jd, $mod = '') {
        // ... (بدنهٔ jalali_to_gregorian)
    }
}