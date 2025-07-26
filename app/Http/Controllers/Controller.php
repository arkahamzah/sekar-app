<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    /**
     * Helper method to check if current user is admin
     */
    protected function isAdmin()
    {
        return \App\Http\Middleware\CheckAdmin::isCurrentUserAdmin();
    }
    
    /**
     * Helper method to get current user's admin role
     */
    protected function getAdminRole()
    {
        return \App\Http\Middleware\CheckAdmin::getCurrentAdminRole();
    }
}