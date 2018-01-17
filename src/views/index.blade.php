@extends('adminamazing::teamplate')

@section('pageTitle', 'Операции')
@section('content')
	@push('display')
    	<a href="http://localhost:3001/admin/deposits/create" class="btn hidden-sm-down btn-success"><i class="mdi mdi-plus-circle"></i> Создать операцию</a>
    @endpush

	<div class="modal fade bs-example-modal-lg" id="informationTransaction" aria-hidden="true">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
                <div class="modal-header">Информация об операции</div>
                <div class="modal-body">
                    <p class="transaction hide">Транзакция: <span></span></p>
                    <p class="amount hide">Сумма: <span></span></p>
                    <p class="amount_default hide">Сумма в USD: <span></span></p>
                </div>
	        </div>
	    </div>
	</div>


    <div class="row">
        <!-- Column -->
        <div class="col-12">
	    	<div class="card">
	        	<div class="card-block wizard-content">
	                <form method="GET" action="{{route('AdminOperations')}}">
		                {{ csrf_field() }}
		                <section>
		                    <div class="row">
		                        <div class="col-md-4">
		                            <div class="form-group">
		                                <label for="application_id">ID заявки :</label>
		                                <input type="text" class="form-control" value="{{$application_id}}" name="application_id" id="application_id"> </div>
		                        </div>
		                        <div class="col-md-4">
		                            <div class="form-group">
		                                <label for="user_email">Пользователи (Email) :</label>
		                                <input type="text" class="form-control" value="{{$user_email}}" name="user_email" id="user_email">
		                            </div>
		                        </div>
		                        <div class="col-md-4">
		                            <div class="form-group">
		                                <label for="transaction_id">Транзакция :</label>
		                                <input type="text" class="form-control" value="{{$transaction_id}}" name="transaction_id" id="transaction_id"> </div>
		                        </div>
		                        <div class="col-md-4">
		                            <div class="form-group">
		                                <label for="wallet">Кошелек :</label>
		                                <input type="text" class="form-control" value="{{$wallet}}" name="wallet" id="wallet"> </div>
		                        </div>
		                        <div class="col-md-4">
		                            <div class="form-group">
		                                <label for="address_pay">Адрес для оплаты :</label>
		                                <input type="text" class="form-control" value="{{$address_pay}}" name="address_pay" id="address_pay"> </div>
		                        </div>
		                        <div class="col-md-4">
		                            <div class="row">
		                            	<div class="col-md-4">
		                            		<div class="form-group">
				                                <label for="amount_where">где</label>
				                                <select class="custom-select form-control" id="amount_where" name="amount_where">
			                                    	<option value=""></option>
			                                    	<option {{($amount_where=='=')?'selected':NULL}} value="=">=</option>
			                                    	<option {{($amount_where=='>')?'selected':NULL}} value=">">></option>
			                                    	<option {{($amount_where=='<')?'selected':NULL}} value="<"><</option>
				                                </select>
				                            </div>
		                            	</div>
		                            	<div class="col-md-8">
		                            		<div class="form-group">
			                                <label for="amount">Сумма :</label>
			                                <input type="text" class="form-control" value="{{$amount}}" name="amount" id="amount"> </div>
		                            	</div>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="row">
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="payment_system">Платежная система :</label>
		                                <select style="height: 200px;" multiple="{{count($payment_systems)}}" class="custom-select form-control" id="payment_system" name="payment_system[]">
		                                    <option value="">Выбрать платежную систему</option>
		                                    @foreach($payment_systems as $row)
		                                    	<option {{(in_array($row->id, $payment_system))?'selected':NULL}} value="{{$row->id}}">{{$row->title}}, {{$row->currency}}</option>
		                                    @endforeach
		                                </select>
		                            </div>
		                        </div>
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="type">Операция :</label>
		                                <select style="height: 200px;" multiple="{{count($operations)}}" class="custom-select form-control" id="type" name="type[]">
		                                    <option value="">Выбрать операцию</option>
		                                    @foreach($operations as $key=>$value)
		                                    	<option {{(in_array($key, $type))?'selected':NULL}} value="{{$key}}">{{$value}}</option>
		                                    @endforeach
		                                </select>
		                            </div>
		                        </div>
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="status">Статус :</label>
		                                <select style="height: 200px;" multiple="{{count($statuses)}}" class="custom-select form-control" id="status" name="status[]">
		                                    <option value="">Выбрать статус</option>
		                                    @foreach($statuses as $value)
		                                    	<option {{(in_array($value->status, $status))?'selected':NULL}} value="{{$value->status}}">{{$value->status}}</option>
		                                    @endforeach
		                                </select>
		                            </div>
		                        </div>
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="per_page">Результатов на страницу:</label>
		                                <select class="custom-select form-control" id="per_page" name="per_page">
	                                    	<option {{($per_page==10)?'selected':NULL}} value="10">10</option>
	                                    	<option {{($per_page==20)?'selected':NULL}} value="20">20</option>
	                                    	<option {{($per_page==50)?'selected':NULL}} value="50">50</option>
	                                    	<option {{($per_page==100)?'selected':NULL}} value="100">100</option>
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
		                    		<button type="button" class="btn btn-block btn-info ResetForm">Сбросить</button>
		                    	</div>
		                    </div>
		                </section>
					</form>
	            </div>
	        </div>
            
            <!-- Row -->

            <div class="card">
                <div class="card-block">
					@if(Session::has('success'))
                        <div class="alert alert-important alert-success alert-rounded">{{Session::get('success')}}</div>     	
	                @endif   
	                @if(Session::has('error'))
                        <div class="alert alert-important alert-danger alert-rounded">{{Session::get('error')}}</div>     	
	                @endif                	
                	<form class="FormOperations" method="POST" action="">
                        {{ csrf_field() }}
                        <div class="table-responsive">
	                        <table class="nowrap table table-hover table-striped table-bordered dataTable">
	                            <thead>
	                                <tr>
	                                    <th class="text-center">
	                                    	@if(!$history->isEmpty())
	                                    		<button type="button" id="SelectAll" class="btn btn-sm btn-success">Select all</button>
	                                    	@endif
	                                    </th>
	                                    <th>
	                                    	<a class="do_sorting sorting{{($sort=='id')?'_'.$order:NULL}}" 
	                                    	data-route="{{route('AdminOperations', ['sort' => 'id', 'order' => ($sort=='id' && $order=='asc')?'desc':'asc'])}}" href="#">#</a>
	                                    </th>
	                                    <th>Операция</th>
	                                    <th>Пользователь</th>
	                                    <th><a class="do_sorting sorting{{($sort=='amount')?'_'.$order:NULL}}" data-route="{{route('AdminOperations', ['sort' => 'amount', 'order' => ($sort=='amount' && $order=='asc')?'desc':'asc'])}}" href="#">Сумма</a></th>
	                                    <th><a class="do_sorting sorting{{($sort=='created_at')?'_'.$order:NULL}}" data-route="{{route('AdminOperations', ['sort' => 'created_at', 'order' => ($sort=='created_at' && $order=='asc')?'desc':'asc'])}}" href="#">Дата</a></th>
	                                    <th>Статус</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                            	@if(!$history->isEmpty())
		                                @foreach($history as $row)
			                                <tr class="active">
			                                	<td class="text-center">
			                                		<input value="{{$row->id}}" id="checkbox0" name="application[]" type="checkbox">
			                                	</td>
			                                    <td scope="row">{{$row->id}}</td>
			                                    <td>
			                                    	{{$operations[$row->type]}}
			                                    	<br/><a href="#informationTransaction" class="show_info_transaction" data-transaction="{{$row->transaction}}" 
													@if($row->data_info)
														@foreach($row->data_info as $key=>$val)
															@if(isset($key) && isset($val))
																@if($key == 'full_data_ipn')
																	data-{{$key}}='{{print_r(json_decode($val, true), true)}}'
																@else
																	data-{{$key}}='@if(!is_object($val)) {!!$val!!} @endif'
																@endif
															@endif
														@endforeach
													@endif
			                                    	data-toggle="modal" href="">Данные операции</a>
			                                    </td>
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
	                                		<td colspan="7">
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
								<div class="col-md-3"><button data-action="{{route('AdminOperationsConfirm')}}" type="button" class="btn btn-block btn-success MyAction">Выполнить</button></div>
								<div class="col-md-3"><button data-action="{{route('AdminOperationsMultiPay')}}" type="button" class="btn btn-block btn-warning MyAction">Сделать массовую выплату</button></div>
								<div class="col-md-3"><button data-action="{{route('AdminOperationsStatusDone')}}" type="button" class="btn btn-block btn-info MyAction">Изменить статус на готово</button></div>
								<div class="col-md-3"><button data-action="{{route('AdminOperationsCancel')}}" type="button" class="btn btn-block btn-danger MyAction">Отменить операцию</button></div>
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


    @push('scripts')
	    <script>
	        var route = '{{ route('home') }}';
	        var message = 'Вы точно хотите удалить данное сообщение?';
	        $(function(){
	        	var state = 1;
				$(document).on('click', '#SelectAll', function(){
					var form = $(this).closest('form');
					if(state == 1){
						form.find('input[type=checkbox]').not(":disabled").attr( "checked" , true)
						state = 0;
					}else{
						form.find('input[type=checkbox]').not(":disabled").attr( "checked" , false)
						state = 1;			
					}
				});

				$(document).on('click', '.MyAction', function(){
					var action = $(this).data('action');
					var form = $(this).closest('form');
					form.attr('action', action);
					form.submit();
				});

				$(document).on('click', '.ResetForm', function(){
					var form = $(this).closest('form');
					form.trigger("reset");
					clearForm(form[0]);
					form.find("option:selected").prop("selected", false);
					// form.find("select").multiselect('refresh');
					form.submit();
				});

				$(document).on('click', '.do_sorting', function(){
					var route = $(this).data('route');
					window.location.href = route;
					return false;
				});

				$(document).on('click', '.show_info_transaction', function(){
					var tr = $(this).data('transaction');
					var data = $(this).data();
					$('.modal-body').html('');
					$.each(data, function(i, el){
						var title = i;
						switch(i){
							case 'full_data_ipn':
								title = 'Данные из платежной системы';
							break;

							case 'deposit_id':
								title = 'Идентификатор депозита';
							break;

							case 'plan_id':
								title = 'Идентификатор плана';
							break;

							case 'address':
								title = 'Адресс для платежа';
							break;

							case 'transaction':
								title = 'Транзакция';
							break;

							default:

							break;
						}
						if(i != 'toggle' && el != ''){
							if(i == 'full_data_ipn'){
								$('.modal-body').append('<div class="form-group"><label>'+title+'</label><textarea style="height: 250px;" class="form-control">'+data[i]+'</textarea></div>');
							}else{
								$('.modal-body').append('<p>'+title+': '+data[i]+'</p>');	
							}
							
						}
					});
				});
	        });

	        function clearForm(myFormElement) {
				var elements = myFormElement.elements;
				myFormElement.reset();
				for(i=0; i<elements.length; i++) {
					field_type = elements[i].type.toLowerCase();
					switch(field_type) {
						case "text":
						case "password":
						case "textarea":
						case "hidden":
							elements[i].value = "";
						break;
						case "radio":
						case "checkbox":
						if (elements[i].checked) {
							elements[i].checked = false;
						}
						break;
						case "select-one":
						case "select-multi":
							elements[i].selectedIndex = -1;
						break;
						default:
						break;
					}
				}
			}
	    </script>
	@endpush
@endsection