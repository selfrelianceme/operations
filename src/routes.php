<?php

Route::group(['prefix' => config('adminamazing.path').'/withdraw_orders', 'middleware' => ['web','CheckAccess']], function() {
	Route::any('/', 'Selfreliance\WithdrawOrders\WithdrawOrdersController@index')->name('AdminWithdrawOrders');
});
