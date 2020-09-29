<?php

namespace Src;

function app($key = null)
{
    if($key && APP[$key] != '') {
        return APP[$key];
    }

    return APP['root'];
}