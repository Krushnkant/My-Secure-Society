<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::currentPageResolver(function ($pageName = 'page_no') {
            $page = request()->page_no;
            return $page > 0 ? $page : 1;
        });
    }

    public function register()
    {
        //
    }
}