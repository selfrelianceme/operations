<?php

Route::group(['prefix' => config('adminamazing.path').'/operations', 'middleware' => ['web','CheckAccess']], function() {
	Route::get('/', 'Selfreliance\Operations\OperationsController@index')->name('AdminOperations');
	Route::post('/status/done', 'Selfreliance\Operations\OperationsController@done')->name('AdminOperationsStatusDone');
	Route::post('/status/cancel', 'Selfreliance\Operations\OperationsController@cancel')->name('AdminOperationsCancel');
	Route::post('/status/confirm', 'Selfreliance\Operations\OperationsController@confirm')->name('AdminOperationsConfirm');
	Route::post('/status/multi_pay', 'Selfreliance\Operations\OperationsController@multi_pay')->name('AdminOperationsMultiPay');

	Route::get('/create', 'Selfreliance\Operations\OperationsController@create')->name('AdminOperationsCreate');
	Route::post('/create', 'Selfreliance\Operations\OperationsController@store')->name('AdminOperationsStore');
});
