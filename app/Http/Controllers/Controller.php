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
     * The title of the page.
     */
    public string|null $title = null;

    /**
     * The description of the page.
     */
    public string|null $description = null;

    /**
     * Name of the route prefix.
     */
    public string|null $routeNamePrefix = null;

    /**
     * Where the view files are located
     */
    public string|null $viewNamePrefix = null;

    /**
     * The permission name of the feature.
     */
    public string|null $permissionName = null;

    /**
     * The policy model of the feature.
     */
    public string|null $policyModel = null;

    /**
     * compact the response
     */
    public function pack(array $arr = [])
    {
        return array_merge([
            ...$arr,
            'title' => $this->title,
            'description' => $this->description,
            'routeNamePrefix' => $this->routeNamePrefix,
            'viewNamePrefix' => $this->viewNamePrefix,
            'permissionName' => $this->permissionName,
            'policyModel' => $this->policyModel,
        ]);
    }
}
