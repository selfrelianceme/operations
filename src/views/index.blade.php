@extends('adminamazing::teamplate')

@section('pageTitle', 'Операции')
@section('content')
    <script>
        var route = '{{ route('home') }}';
        var message = 'Вы точно хотите удалить данное сообщение?';
    </script>
    <div class="row">
        <!-- Column -->
        <div class="col-12">

	    	<div class="card">
	        	<div class="card-block wizard-content">
	                <form method="GET" action="{{route('AdminWithdrawOrders')}}">
		                {{ csrf_field() }}
		                <section>
		                    <div class="row">
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="application_id">ID заявки :</label>
		                                <input type="text" class="form-control" value="{{$application_id}}" name="application_id" id="application_id"> </div>
		                        </div>
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="user_email">Пользователь (Email) :</label>
		                                <input type="text" class="form-control" value="{{$user_email}}" name="user_email" id="user_email">
		                            </div>
		                        </div>
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="transaction_id">Транзакция :</label>
		                                <input type="text" class="form-control" value="{{$transaction_id}}" name="transaction_id" id="transaction_id"> </div>
		                        </div>
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="wallet">Кошелек :</label>
		                                <input type="tel" class="form-control" value="{{$wallet}}" name="wallet" id="wallet"> </div>
		                        </div>
		                    </div>
		                    <div class="row">
		                        <div class="col-md-4">
		                            <div class="form-group">
		                                <label for="payment_system">Платежная система :</label>
		                                <select class="custom-select form-control" id="payment_system" name="payment_system">
		                                    <option value="">Выбрать платежную систему</option>
		                                    @foreach($payment_systems as $row)
		                                    	<option {{($row->id == $payment_system)?'selected':NULL}} value="{{$row->id}}">{{$row->title}}, {{$row->currency}}</option>
		                                    @endforeach
		                                </select>
		                            </div>
		                        </div>
		                        <div class="col-md-4">
		                            <div class="form-group">
		                                <label for="type">Операция :</label>
		                                <select class="custom-select form-control" id="type" name="type">
		                                    <option value="">Выбрать операцию</option>
		                                    @foreach($operations as $key=>$value)
		                                    	<option {{($key == $type)?'selected':NULL}} value="{{$key}}">{{$value}}</option>
		                                    @endforeach
		                                </select>
		                            </div>
		                        </div>
		                        <div class="col-md-4">
		                            <div class="form-group">
		                                <label for="status">Статус :</label>
		                                <select class="custom-select form-control" id="status" name="status">
		                                    <option value="">Выбрать статус</option>
		                                    @foreach($statuses as $value)
		                                    	<option {{($value->status == $status)?'selected':NULL}} value="{{$value->status}}">{{$value->status}}</option>
		                                    @endforeach
		                                </select>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="row">
		                    	<div class="col-md-3"></div>
		                    	<div class="col-md-3">
		                    		<button type="submit" class="btn btn-block btn-success">Отобразить</button>
		                    	</div>
		                    	<div class="col-md-3">
		                    		<button type="reset" class="btn btn-block btn-info">Сбросить</button>
		                    	</div>
		                    </div>
		                </section>
					</form>
	            </div>
	        </div>
            
            <!-- Row -->

            <div class="card">
                <div class="card-block">
                	<form method="POST" action="">
                        <div class="table-responsive">
	                        <table class="table">
	                            <thead>
	                                <tr>
	                                    <th class="text-center">
	                                    	@if(!$history->isEmpty())
	                                    		<button type="button" id="SelectAllPaymentSystem" class="btn btn-sm btn-success">Select all</button>
	                                    	@endif
	                                    </th>
	                                    <th>#</th>
	                                    <th>Пользователь</th>
	                                    <th>Сумма</th>
	                                    <th>Дата</th>
	                                    <th>Статус</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                            	@if(!$history->isEmpty())
		                                @foreach($history as $row)
			                                <tr class="active">
			                                	<td class="text-center">
			                                		<input id="checkbox0" name="application[]" type="checkbox">
			                                	</td>
			                                    <td scope="row">{{$row->id}}</td>
			                                    <td>
			                                    	<a target="_blank" href="{{route('AdminUsersEdit', $row->user_id)}}">{{$row->email}}</a><br/>
			                                    	@if(isset($row->data_info->wallet))
			                                    		{{$row->data_info->wallet}}
			                                    	@endif
			                                    </td>
			                                    <td>{{$row->title}}<br/>{{$row->amount}} {{$row->currency}}</td>
			                                    <td>{{$row->created_at}}<br/>{{$row->updated_at}}</td> 
			                                    <td>{{$row->status}}</td> 
			                                </tr>
		                                @endforeach
	                                @else
	                                	<tr>
	                                		<td colspan="6">
	                                			<div class="alert alert-important alert-warning text-center">
							                        <h4>Операций не найдено</h4>
							                    </div>
	                                		</td>
	                                	</tr>
	                                @endif
	                            </tbody>
	                        </table>
                        </div>
						<div class="form-actions">
							<div class="row">
								<div class="col-md-4"><button type="submit" class="btn btn-block btn-success">Выполнить</button></div>
								<div class="col-md-4"><button type="submit" class="btn btn-block btn-info">Изменить статус на готово</button></div>
								<div class="col-md-4"><button type="button" class="btn btn-block btn-danger">Отменить операцию</button></div>
							</div>
                        </div>  
                    </form>                      
                </div>
            </div>

            
            <!-- End Row -->

            <nav aria-label="Page navigation example" class="m-t-40">
                {{ $history->links('vendor.pagination.bootstrap-4') }}
            </nav>            
        </div>
        <!-- Column -->    
    </div>
@endsection