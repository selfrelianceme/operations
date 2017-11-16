<?php

namespace Selfreliance\WithdrawOrders;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\Models\Users_History;
use App\Models\Payment_System;
use Cookie;
use Illuminate\Cookie\CookieJar;

class WithdrawOrdersController extends Controller
{
	public function index(CookieJar $cookieJar, Request $request)
	{
		$application_id = ($request->input('application_id'))?$request->input('application_id'):"";
		$user_email     = ($request->input('user_email'))?$request->input('user_email'):"";
		$transaction_id = ($request->input('transaction_id'))?$request->input('transaction_id'):"";
		$wallet = ($request->input('wallet'))?$request->input('wallet'):"";
		$payment_system = ($request->input('payment_system'))?$request->input('payment_system'):"";
		$type = ($request->input('type'))?$request->input('type'):"";
		$status = ($request->input('status'))?$request->input('status'):"";

		// $cookieJar->queue(cookie('application_id', $application_id, 45000));

		$history = Users_History::leftJoin('payment__systems', 'payment__systems.id', '=', 'users__histories.payment_system')
			->leftJoin('users', 'users.id', '=', 'users__histories.user_id')
			->orderBy('id', 'asc')
			->where(function($query) use ($application_id, $user_email, $transaction_id, $wallet, $payment_system, $type, $status){
				if($application_id != ''){
					$query->where('users__histories.id', $application_id);
				}
				if($user_email != ''){
					$query->where('users.email', $user_email);
				}
				if($transaction_id != ''){
					$query->where('users__histories.data_info->transaction', $transaction_id);
				}
				if($wallet != ''){
					$query->where('users__histories.data_info->wallet', $wallet);
				}
				if($payment_system != ''){
					$query->where('users__histories.payment_system', $payment_system);
				}
				if($type != ''){
					$query->where('users__histories.type', $type);
				}
				if($status != ''){
					$query->where('users__histories.status', $status);
				}
			})
			->paginate(10, array(
	            'users__histories.*',
	            'payment__systems.currency',
	            'payment__systems.title',
	            'users.email',
	        ));
        $history->appends(['application_id' => $application_id]);
        $history->appends(['user_email' => $user_email]);
        $history->appends(['transaction_id' => $transaction_id]);
        $history->appends(['wallet' => $wallet]);
        $history->appends(['type' => $type]);
        $history->appends(['status' => $status]);

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

			"application_id"  => $application_id,
			"user_email"      => $user_email,
			"transaction_id"  => $transaction_id,
			"wallet"          => $wallet,
			"payment_system"  => $payment_system,
			"type"            => $type,
			"status"          => $status,
		]);
	}
}