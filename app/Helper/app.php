<?php
use Creativeorange\Gravatar\Facades\Gravatar;

if (!function_exists('gravatar_url')) {
    function gravatar_url($email, $size = 150, $default = 'wavatar')
    {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/$hash?s=$size&d=$default";

    }
}