<?php

namespace Samrat415\LaravelDdd\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Samrat415\LaravelDdd\LaravelDdd
 */
class LaravelDdd extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Samrat415\LaravelDdd\LaravelDdd::class;
    }
}
