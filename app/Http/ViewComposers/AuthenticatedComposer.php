<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

use Auth;
use Crypt;

class AuthenticatedComposer
{
    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $view->with('auth', Auth::user())
        	->withEncryptedCsrfToken(Crypt::encrypt(csrf_token()));
    }
}
