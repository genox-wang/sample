<?php

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help','StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('/signup', 'UsersController@create')->name('signup');
resource('users', 'UsersController');

get('/login', 'SessionsController@create')->name('login');
post('/login', 'SessionsController@store')->name('login');
delete('/logout', 'SessionsController@destroy')->name('logout');


get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');
