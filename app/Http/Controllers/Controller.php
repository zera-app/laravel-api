<?php

namespace App\Http\Controllers;

use App\Traits\ControllerHelpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
    use ControllerHelpers;

    /**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        // $this->authorizeResource(static::class);
    }
}
