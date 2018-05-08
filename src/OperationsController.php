<?php

namespace Selfreliance\Operations;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\Models\Users_History;
use Cookie;
use Illuminate\Cookie\CookieJar;
use Withdraw;
use App\Libraries\Deposit;
use App\Jobs\ProcessWithdraw;
use Carbon\Carbon;
use DepositService;
use PaymentSystem;
use Balance;
use DB;
class OperationsController extends Controller
{
	function registerBlock(){
		$pending = Withdraw::getOperationsPending();
		$compleated = Withdraw::getOperationsCompleated();
		if($compleated == 0){
			$percent = 100;
		}else{
			if($pending == 0){
				$percent = 0;
			}else{
				$percent = 100*$pending/$compleated;
			}
		}
		$percent = number($percent);
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
		$per_page = ($request->input('per_page'))?$request->input('per_page'):20;
		$address_pay = ($request->input('address_pay'))?$request->input('address_pay'):"";
		$amount_where = ($request->input('amount_where'))?$request->input('amount_where'):"";
		$amount = ($request->input('amount'))?$request->input('amount'):"";
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
			->where(function($query) use ($application_id, $user_email, $transaction_id, $wallet, $payment_system, $type, $status, $address_pay, $amount_where, $amount){
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

				if($amount_where != '' && $amount){
					$query->where('users__histories.amount', $amount_where, $amount);
				}
			})
			->with('from_user')
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
        $history->appends(['amount_where' => $amount_where]);
        $history->appends(['amount' => $amount]);

        foreach($history as $row){
        	if(is_string($row->data_info)){
        		$row->data_info = json_decode($row->data_info);
        	}
        	$row->amount = number($row->amount, 7);
        }

		$operations = Balance::get_operations();

		// $statuses = Users_History::selectRaw('DISTINCT status')->get();
		$statuses = [
			'pending', 'completed', 'error', 'cancel', 'in_queue', 'underpayment'
		];


		$payment_systems = PaymentSystem::getAll('asc');

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
			"amount_where"    => $amount_where,
			"amount"          => $amount,
			
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


	public function create(){
		$payment_systems = PaymentSystem::getAll('asc');
		$operations = Balance::get_operations(['REFFERAL', 'REFUND_DEPOSIT']);
        // $plans = DepositService::getPlansModel();
        return view('operations::create', compact('payment_systems', 'operations'));
    }

    public function store(Request $request){
    	$answer = [
			'msg'     => 'Server error'
    	];
    	$operations = Balance::get_operations(['REFFERAL', 'REFUND_DEPOSIT']);
    	$operations = implode(",", array_keys($operations));
        $rules = [
			'user_email' => 'required|exists:users,email',
			'amount'     => 'required',
			'operation'  => 'required|in:'.$operations
        ];

        switch ($request['operation']) {
            case 'CREATE_DEPOSIT':
            	$rules['plan_id'] = 'required|exists:deposits__plans,id';
            	$rules['payment_system'] = 'required|exists:payment__systems,id';
            	break;
            case 'WITHDRAW':
        	case 'ADD_FUNDS':
        	case 'SELL_FUNDS':
            	$rules['payment_system'] = 'required|exists:payment__systems,id';
            	break;
            case 'ACCRUALS':
            	$rules['deposit_id'] = 'required|exists:deposits,id';
            	break;
        }
        $this->validate($request, $rules);
        DB::beginTransaction();
    	try{
			$user = User::where('email', $request['user_email'])->first();
			switch ($request['operation']) {
				case 'CREATE_DEPOSIT':
						$result = DepositService::
			                user($user->id)
			                ->amount($request->input('amount'))
			                ->payment_system($request->input('payment_system'))
			                ->plan($request->input('plan_id'))
			                ->make_purchase();
		               	if($result->created_purchase){
		               		$answer['msg'] = 'Операция по создания депозита успешно созадана, перейти в <a href="'.route('AdminOperations', ['application_id' => $result->history->id]).'">операции</a> ?';
		               	}
					break;
				
				case 'ACCRUALS':
					$deposit_id = $request['deposit_id'];
					$payment_system = DepositService::get_info_about_id($deposit_id);
					
					$History = Balance::add_and_history('ACCRUALS', $deposit_id, $user->id, $payment_system->payment_system, $request->input('amount'), ["deposit_id"   => (int)$deposit_id]);

					$answer['msg'] = 'Операция начисления создана и зачислена на баланс, перейти в <a href="'.route('AdminOperations', ['application_id' => $History->id]).'">операцию</a> ?';
					break;
				
				case 'WITHDRAW':
					list($buy, $history) = Withdraw::create_withdraw($user, $request->input('payment_system'), $request->input('amount'));
	                $answer['msg'] = 'Операция вывода создана перейти в <a href="'.route('AdminOperations', ['application_id' => $history->id]).'">операцию</a> ?';
					break;
				
				case 'ADD_FUNDS':
					list($buy, $history) = Balance::actionBalance([
			            'type'           => 'ADD_FUNDS',
			            'user_id'        => $user->id,
			            'payment_system' => $request->input('payment_system'),
			            'amount'         => $request->input('amount'),
			            'status'         => 'completed',
			            'transaction'    => '',
			            'is_balance'     => 1,
			        ], 'buy');
					$answer['msg'] = 'Операция пополнения баланса создана и зачислена на баланс, перейти в <a href="'.route('AdminOperations', ['application_id' => $history->id]).'">операцию</a> ?';
					break;
				
				case 'SELL_FUNDS':
					list($balance, $total) = Balance::getByPaymentSystem($user->id);
					$find_balance = $balance->where('id', $request->input('payment_system'))->first();
					
					if($request->input('amount') > $find_balance->balance){
						throw new \Exception("На балансе недостаточно средств для списание данной суммы");
					}
					list($buy, $history) = Balance::actionBalance([
			            'type'           => 'SELL_FUNDS',
			            'user_id'        => $user->id,
			            'payment_system' => $request->input('payment_system'),
			            'amount'         => $request->input('amount'),
			            'status'         => 'completed',
			            'transaction'    => '',
			            'is_balance'     => 1,
			        ], 'sell');

					$answer['msg'] = 'Операция снятия средств с баланса создана и выполнена, перейти в <a href="'.route('AdminOperations', ['application_id' => $history->id]).'">операцию</a> ?';
					break;
			}
			DB::commit();
			\Session::flash('success', $answer['msg']);
            return redirect()->back();
    	}catch(\Exception $e){
			DB::rollBack();
			\Session::flash('error', $e->getMessage());
			return redirect()->back()->withInput();
		}
    }
}