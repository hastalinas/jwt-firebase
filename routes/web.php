<?php

use App\Http\Controllers\MailController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/key', function () {
    return Str::random(32);
});


$router->get('/mail/send', 'MailController@send');

$router->get('acces', 'AuthController@acces');

$router->post('register', 'AuthController@register');

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->get('me', 'AuthController@me');
    $router->post('reset/sendmail', 'AuthController@sendmail');
    $router->post('reset/password', ['middleware' => 'token', 'uses' => 'AuthController@reset']);
    $router->post('resetsms/sendsms', 'AuthController@sendsms');
    $router->post('resetsms/password', ['middleware' => 'otp', 'uses' => 'AuthController@resetsms']);

    //role untuk mahasiswa dan admin
    $router->post('logout', 'AuthController@logout');
    $router->get('find/{id}','MatkulController@find');
    $router->get('show','MatkulController@show');

    $router->put('updateuser/{id}',['middleware' => 'auth.jwt', 'uses' => 'AuthController@updateuser']);
    //$router->post('create', ['middleware'=> 'auth.jwt', 'uses' => 'MatkulController@create']);

    //role untuk admin
    $router->put('update/{id}', ['middleware' => ['auth.jwt'],'uses' => 'MatkulController@update']);
    $router->delete('destroy/{id}', ['middleware' => ['auth.jwt'], 'uses' => 'MatkulController@destroy']);
    $router->post('create', ['middleware' => ['auth.jwt'], 'uses' => 'MatkulController@create']);
    //$router->get('show', ['middleware' => ['auth.jwt', 'role:admin'], 'uses' => 'MatkulController@show']);
});


// route send message
//$router->get('/sms', function () use ($router) {
    // $basic  = new \Vonage\Client\Credentials\Basic("74ddb9f3", "o0h3oyn8H2dTUV5l");
    // $client = new \Vonage\Client($basic);
    // $response = $client->sms()->send(
    //     new \Vonage\SMS\Message\SMS("6285713493551", 'SMS GATEWAY', 'Percobaan sms gateway')
    // );

    // $message = $response->current();

    // if ($message->getStatus() == 0) {
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'The message was sent successfully',
    //     ], 200);
    // } else {
    //     return response()->json([
    //         'status' => false,
    //         'message' => "The message failed with status: " . $message->getStatus() . "\n"
    //     ], 400);
    // }
//});