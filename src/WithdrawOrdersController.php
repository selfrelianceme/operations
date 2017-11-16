<?php

namespace Selfreliance\WithdrawOrders;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\Models\Users_History;
use App\Models\Payment_System;
class WithdrawOrdersController extends Controller
{
	public function index()
	{
		$history = Users_History::where('type', 'WITHDRAW')
		->leftJoin('payment__systems', 'payment__systems.id', '=', 'users__histories.payment_system')
		->leftJoin('users', 'users.id', '=', 'users__histories.user_id')
		->orderBy('id', 'asc')
		->paginate(2, array(
            'users__histories.*',
            'payment__systems.currency',
            'payment__systems.title',
            'users.email',
        ));
        foreach($history as $row){
        	$row->data_info = json_decode($row->data_info);
        }

        $oClass = new \ReflectionClass ('App\Models\Users_History');
		$operations = $oClass->getConstants ();
		unset($operations['CREATED_AT'], $operations['UPDATED_AT']);

		foreach($operations as $key=>$value){
			switch ($key) {
				case 'CREATE_DEPOSIT':
					$operations[$key] = "Создание депозита";
					break;
				case 'ACCRUALS':
					$operations[$key] = "Начисление";
					break;
				case 'REFFERAL':
					$operations[$key] = "Реферальные";
					break;
				case 'WITHDRAW':
					$operations[$key] = "Вывод средств";
					break;
			}
		}

		$statuses = Users_History::selectRaw('DISTINCT status')->get();

		$payment_systems = Payment_System::orderBy('sort', 'asc')->get();

		return view('withdraw_orders::index')->with([
			"history"         => $history,
			"operations"      => $operations,
			"statuses"        => $statuses,
			"payment_systems" => $payment_systems,
		]);
	}
}