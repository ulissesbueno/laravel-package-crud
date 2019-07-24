<?php

/*
|--------------------------------------------------------------------------
| Credencials Routes
|--------------------------------------------------------------------------
|
| Rotas do modulo de Credenciais
|
*/

$nameController = "{model_name}Controller";

// POST
Route::post('save', $nameController.'@save')->name('save');

// GET
Route::get('index', $nameController.'@index')->name('index');
Route::get('form/{id?}', $nameController.'@form')->name('form');
Route::get('delete/{id}', $nameController.'@delete')->name('delete');