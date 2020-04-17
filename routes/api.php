<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//petugas
Route::post('register','petugasc@register');
Route::post('login','petugasc@login');
Route::put('update/{id}','petugasc@update')->middleware('jwt.verify');
Route::post('get','petugasc@get')->middleware('jwt.verify');
//mobil
Route::post('addmobil','mobilc@add')->middleware('jwt.verify');
Route::put('updatemobil/{id}','mobilc@update')->middleware('jwt.verify');
Route::post('getmobil','mobilc@get')->middleware('jwt.verify');
Route::delete('delmobil/{id}','mobilc@delete')->middleware('jwt.verify');
//jenis
Route::post('addjenis','jenisc@add')->middleware('jwt.verify');
Route::post('updatejenis','jenisc@update')->middleware('jwt.verify');
Route::get('getjenis','jenisc@get')->middleware('jwt.verify');
Route::post('deljenis','jenisc@delete')->middleware('jwt.verify');
//pelanggan
Route::post('addpelanggan','pelangganc@add')->middleware('jwt.verify');
Route::post('updatepelanggan','pelangganc@update')->middleware('jwt.verify');
Route::post('getpelanggan','pelangganc@get')->middleware('jwt.verify');
Route::post('delpelanggan','pelangganc@delete')->middleware('jwt.verify');
//transaksi
Route::post('addtrans','transaksic@add')->middleware('jwt.verify');
Route::post('updatetrans','transaksic@update')->middleware('jwt.verify');
Route::post('gettrans','transaksic@get')->middleware('jwt.verify');
Route::delete('deltrans','transaksic@delete')->middleware('jwt.verify');
Route::post('kembali','transaksic@kembali')->middleware('jwt.verify');