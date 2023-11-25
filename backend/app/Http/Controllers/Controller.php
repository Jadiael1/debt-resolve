<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(
 *   title="DebtResolve API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="jadiael@hotmail.com.br"
 *   ),
 *   @OA\License(
 *     name="CC-BY-NC",
 *     url="https://creativecommons.org/licenses/by-nc/4.0/"
 *   )
 * )
 * @OA\Components(
 *   @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="Bearer"
 *   ),
 *   @OA\Attachable
 * )
 */

class OpenApi
{
}

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
