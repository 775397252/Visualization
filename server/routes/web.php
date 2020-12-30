<?php

use App\Http\Controllers\VisController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $api=<<<EOT
欢迎访问首页接口：<br>
vis/getCompany :公司信息<br>
vis/hotPosition :最热职业（不带参数看全部）<br>
vis/workYear :经验要求<br>
vis/city :地域分布<br>
EOT;
    return $api;
});


Route::get('vis/getCompany', [VisController::class, 'getCompany']);
Route::get('vis/hotPosition', [VisController::class, 'hotPosition']);
Route::get('vis/workYear', [VisController::class, 'workYear']);
Route::get('vis/city', [VisController::class, 'city']);
Route::get('vis/education', [VisController::class, 'education']);
