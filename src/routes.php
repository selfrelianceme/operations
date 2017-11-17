<?php

Route::group(['prefix' => config('adminamazing.path').'/withdraw_orders', 'middleware' => ['web','CheckAccess']], function() {
	Route::get('/', 'Selfreliance\WithdrawOrders\WithdrawOrdersController@index')->name('AdminWithdrawOrders');
	Route::post('/status/done', 'Selfreliance\WithdrawOrders\WithdrawOrdersController@done')->name('AdminWithdrawOrdersStatusDone');
	Route::post('/status/cancel', 'Selfreliance\WithdrawOrders\WithdrawOrdersController@cancel')->name('AdminWithdrawOrdersCancel');
	Route::post('/status/confirm', 'Selfreliance\WithdrawOrders\WithdrawOrdersController@confirm')->name('AdminWithdrawOrdersConfirm');

});
