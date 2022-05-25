<?php

declare(strict_types = 1);

namespace App\Http\Common;

/**
 * Internal error code.
 */
enum ErrorCode: int
{
    // Is ok.
    case None       = 0;

    // 10000-19999 Client error.
    case ArgMissing  = 10001;
    case ArgTypeErr  = 10002;
    case ArgInvalid  = 10003;

    // 20000-29999 Server error.
    case Unknown = 20001;
}
