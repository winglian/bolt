<?php

namespace Bolt\Helpers;

class String
{
    /**
     * Returns a "safe" version of the given string - basically only US-ASCII and
     * numbers. Needed because filenames and titles and such, can't use all characters.
     *
     * @param  string  $str
     * @param  boolean $strict
     * @param  string  $extrachars
     * @return string
     */
    public static function makeSafe($str, $strict = false, $extrachars = "")
    {
        $str = \URLify::downcode($str);
        $str = str_replace("&amp;", "", $str);

        $delim = '/';
        if ($extrachars != "") {
            $extrachars = preg_quote($extrachars, $delim);
        }
        if ($strict) {
            $str = strtolower(str_replace(" ", "-", $str));
            $regex = "[^a-zA-Z0-9_" . $extrachars . "-]";
        } else {
            $regex = "[^a-zA-Z0-9 _.," . $extrachars . "-]";
        }

        $str = preg_replace($delim . $regex . $delim, '', $str);

        return $str;
    }

    /**
     * Modify a string, so that we can use it for slugs. Like
     * safeString, but using hyphens instead of underscores.
     *
     * @param  string $str
     * @param  int    $length
     * @return string
     */
    public static function slug($str, $length = 64)
    {
        if (is_array($str)) {
            $str = implode(" ", $str);
        }

        // Strip out timestamps like "00:00:00". We don't want timestamps in slugs.
        $str = preg_replace("/[0-2][0-9]:[0-5][0-9]:[0-5][0-9]/", "", $str);

        $str = static::makeSafe(strip_tags($str));

        $str = str_replace(" ", "-", $str);
        $str = strtolower(preg_replace("/[^a-zA-Z0-9_-]/i", "", $str));
        $str = preg_replace("/[-]+/i", "-", $str);

        if ($length > 0) {
            $str = substr($str, 0, $length);
        }

        $str = trim($str, " -");

        return $str;
    }

    /**
     * Replace the first occurence of a string only. Behaves like str_replace, but
     * replaces _only_ the _first_ occurence.
     *
     * @see http://stackoverflow.com/a/2606638
     *
     * @param  string $search
     * @param  string $replace
     * @param  string $subject
     * @return string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    /**
     * Add 'soft hyphens' &shy; to a string, so that it won't break layout in HTML when
     * using strings without spaces or dashes.
     *
     * @param string $str
     * @return string
     */
    public static function shyphenate($str)
    {
        $str = preg_replace("/[a-z0-9_-]/i", "$0&shy;", $str);

        return $str;
    }
}
