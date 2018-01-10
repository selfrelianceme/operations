<?php

namespace Selfreliance\Operations;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\Models\Users_History;
use App\Models\Payment_System;
use Cookie;
use Illuminate\Cookie\CookieJar;
use Withdraw;
use App\Libraries\Deposit;
use App\Jobs\ProcessWithdraw;
use Carbon\Carbon;
use DepositService;
class OperationsController extends Controller
{
	function registerBlock(){
		$pending = Withdraw::getOperationsPending();
		$compleated = Withdraw::getOperationsCompleated();
		if($pending == 0){
			$percent = 0;
		}else{
			$percent = 100*$compleated/$pending;
		}
		return view('operations::withdraw')->with(
			compact('pending', 'compleated', 'percent')
		)->render();
	}
	public function index(CookieJar $cookieJar, Request $request)
	{
		$application_id = ($request->input('application_id'))?$request->input('application_id'):"";
		$user_email     = ($request->input('user_email'))?$request->input('user_email'):"";
		$transaction_id = ($request->input('transaction_id'))?$request->input('transaction_id'):"";
		$wallet = ($request->input('wallet'))?$request->input('wallet'):"";
		$payment_system = ($request->input('payment_system'))?$request->input('payment_system'):[];
		$type = ($request->input('type'))?$request->input('type'):[];
		$status = ($request->input('status'))?$request->input('status'):[];
		$per_page = ($request->input('per_page'))?$request->input('per_page'):10;
		$address_pay = ($request->input('address_pay'))?$request->input('address_pay'):"";
		// $cookieJar->queue(cookie('application_id', $application_id, 45000));

		$sort = "id";
		$order = "asc";
		if($request->input('sort') && $request->input('order')){
			$sort = $request->input('sort');
			$order = $request->input('order');
			$cookieJar->queue(cookie('sort', $sort, 45000));
			$cookieJar->queue(cookie('order', $order, 45000));
		}else{
			if(Cookie::get('sort') && Cookie::get('order')){
				$sort = Cookie::get('sort');
				$order = Cookie::get('order');
			}
		}
		// dd(count($type));
		$history = Users_History::leftJoin('payment__systems', 'payment__systems.id', '=', 'users__histories.payment_system')
			->leftJoin('users', 'users.id', '=', 'users__histories.user_id')
			->orderBy("users__histories.".$sort, $order)
			->where(function($query) use ($application_id, $user_email, $transaction_id, $wallet, $payment_system, $type, $status, $address_pay){
				if($application_id != ''){
					$tmp = explode(",", $application_id);
					if(count($tmp) > 0){
						$application_id = $tmp;
					}
					$query->whereIn('users__histories.id', $application_id);
				}
				if($user_email != ''){
					$tmp = explode(",", $user_email);
					if(count($tmp) > 0){
						$user_email = $tmp;
					}
					$query->whereIn('users.email', $user_email);
				}
				if($transaction_id != ''){
					$tmp = explode(",", $transaction_id);
					if(count($tmp) > 0){
						$transaction_id = $tmp;
					}
					$query->whereIn('users__histories.transaction', $transaction_id);
				}
				if($address_pay != ''){
					$tmp = explode(",", $address_pay);
					if(count($tmp) > 0){
						$address_pay_id = $tmp;
					}
					$query->whereIn('users__histories.data_info->address', $address_pay_id);
				}
				if($wallet != ''){
					$query->where('users__histories.data_info->wallet', $wallet);
				}
				if(count($payment_system) > 0){
					if($payment_system[0] == null) unset($payment_system[0]);
					if(count($payment_system) > 0){
						$query->whereIn('users__histories.payment_system', $payment_system);
					}
				}
				if(count($type) > 0){
					if($type[0] == null) unset($type[0]);
					if(count($type) > 0){
						$query->whereIn('users__histories.type', $type);
					}
				}
				if(count($status) > 0){
					if($status[0] == null) unset($status[0]);
					if(count($status) > 0){
						$query->whereIn('users__histories.status', $status);
					}
				}
			})
			->paginate($per_page, array(
	            'users__histories.*',
	            'payment__systems.currency',
	            'payment__systems.title',
	            'users.email',
	        ));
        $history->appends(['application_id' => $application_id]);
        $history->appends(['user_email' => $user_email]);
        $history->appends(['payment_system' => $payment_system]);
        $history->appends(['transaction_id' => $transaction_id]);
        $history->appends(['wallet' => $wallet]);
        $history->appends(['type' => $type]);
        $history->appends(['status' => $status]);
        $history->appends(['per_page' => $per_page]);
        $history->appends(['address_pay' => $address_pay]);

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
				case 'REFUND_DEPOSIT':
					$operations[$key] = "Возврат депозита";
					break;
			}
		}

		$statuses = Users_History::selectRaw('DISTINCT status')->get();


		$payment_systems = Payment_System::orderBy('sort', 'asc')->get();

		return view('operations::index')->with([
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
			"per_page"        => $per_page,
			"address_pay"     => $address_pay,
			
			"sort"            => $sort,
			"order"           => $order,
		]);
	}

	public function done(Request $request){
		if(count($request->input('application')) > 0){
			foreach($request->input('application') as $row){
				$history = Users_History::where('id', $row)->where('status', '<>','completed')->first();
				if($history){
					$history->status = 'completed';
					$history->save();
				}
	    	}	

			\Session::flash('success','Статус изменен на completed');
		}
		return redirect()->back();    		
	}

	public function cancel(Request $request){
		if(count($request->input('application')) > 0){
			foreach($request->input('application') as $row){
				$history = Users_History::where('id', $row)->first();
				if($history){
					if ($history->type == 'WITHDRAW') {
						try{
							Withdraw::history($history)->cancel();
						}catch(\Exception $e){
							\Session::flash('error',$e->getMessage());
							return redirect()->back();
						}
					}elseif ($history->type == 'CREATE_DEPOSIT' && $history->status == 'pending') {
						$history->status = 'cancel';
						$history->save();
					}
				}
	    	}
	    	\Session::flash('success','Заявки были успешно отменены');
		}

		return redirect()->back();    			
	}

	public function confirm(Request $request){
		if(count($request->input('application')) > 0){
			foreach($request->input('application') as $row){
				$history = Users_History::where('id', $row)->first();
				if($history){
					if ($history->type == 'WITHDRAW' && in_array($history->status, ['pending', 'error', 'in_queue'])) {
						$history->status = 'in_queue';
						$history->save();
						if(env('USE_QUEUE_WITHDRAW')){
							$wallet = Withdraw::get_wallet($history->user_id, $history->payment_system);
							ProcessWithdraw::dispatch($history, $wallet);
						}else{
							Withdraw::history($history)->done_withdraw();
						}
					}elseif ($history->type == 'CREATE_DEPOSIT' && $history->status != 'completed') {
						try{
							$resultP = DepositService::
			                    amount($history->amount)
			                    ->payment_id($history->id)
			                    ->plan_seach_history(true)
			                    ->transaction('by-admin_'.Carbon::now())
			                    ->create();

						}catch(\Exception $e){
							\Session::flash('error',$e->getMessage());
							return redirect()->back();					            
				        }													
					}
				}
	    	}
	    	\Session::flash('success','Операции были(а) отпралена в очередь или(и) выполнены');
		}

		return redirect()->back();    					
	}

	public function multi_pay(Request $request){
		$data_for_multi_send = [];
		if(count($request->input('application')) > 0){
			foreach($request->input('application') as $row){
				$history = Users_History::
						where('id', $row)->
						where('type', 'WITHDRAW')->
						whereIn('status', ['pending', 'error'])->
						whereIn('payment_system', [1,2])->
						first();
				if($history){
					$wallet = Withdraw::get_wallet($history->user_id, $history->payment_system);
					if($wallet){
						$data_for_multi_send[] = [
							'id'   => $history->id,
							'data' => [
								$wallet, 
								$history->amount
							]
						];
					}
				}
	    	}
		}
		if(count($data_for_multi_send) > 0){
			Withdraw::done_multi_send($data_for_multi_send);
			\Session::flash('success','Операции были отправлены на выполнения (смотри статус в истории)');
		}
		return redirect()->back();    					
	}
}