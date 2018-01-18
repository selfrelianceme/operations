@extends('adminamazing::teamplate')

@section('pageTitle', 'Создание операции')
@section('content')
    <script>
        var route = '{{ route('home') }}';
        var message = 'Вы точно хотите удалить данное сообщение?';
    </script>
	<div class="row">
	    <div class="col-sm-12">
	        <div class="card">
	            <div class="card-block">
	                <h4 class="card-title">Создание операции</h4>
	                @if(Session::has('error'))
                        <div class="alert alert-danger alert-rounded">{!!Session::get('error')!!}</div>
	                @endif	
	                @if(Session::has('success'))
                        <div class="alert alert-success alert-rounded">{!!Session::get('success')!!}</div>
	                @endif	        
	                <form class="form" method="POST" action="{{route('AdminOperationsStore')}}">
	                    
	                    <div class="form-group m-t-40 row {{ $errors->has('user_email') ? ' error' : '' }}">
	                        <label for="example-text-input" class="col-2 col-form-label">Пользователь (E-mail)</label>
	                        <div class="col-10">
	                            <input class="form-control" type="text" name="user_email" value="{{old('user_email')}}" id="example-text-input">
								@if ($errors->has('user_email'))
                                    <div class="help-block"><ul role="alert"><li>{{ $errors->first('user_email') }}</li></ul></div>
                                @endif
	                        </div>
	                    </div>

						<div class="form-group row {{ $errors->has('operation') ? ' error' : '' }}">
						    <label for="example-month-input" class="col-2 col-form-label">Операция</label>
						    <div class="col-10">
						        <select class="custom-select col-12" id="inlineFormCustomSelect" name="operation">
						            <option selected="" value="0">Выбрать...</option>
						            @foreach($operations as $key => $value)
										<option {{($key==old('operation'))?'selected':NULL}}  value="{{$key}}">{{$value}}</option>
						            @endforeach
						        </select>
								@if ($errors->has('operation'))
                                    <div class="help-block"><ul role="alert"><li>{{ $errors->first('operation') }}</li></ul></div>
                                @endif							        
						    </div>
						</div>

						<div class="form-group row {{ $errors->has('payment_system') ? ' error' : '' }}">
						    <label for="example-month-input" class="col-2 col-form-label">Платежная система</label>
						    <div class="col-10">
						        <select class="custom-select col-12" id="inlineFormCustomSelect" name="payment_system">
						            <option selected="" value="0">Выбрать...</option>
						            @foreach($payment_systems as $row)
										<option {{($row->id==old('payment_system'))?'selected':NULL}}  value="{{$row->id}}">{{$row->title}} ({{$row->currency}})</option>
						            @endforeach
						        </select>
								@if ($errors->has('payment_system'))
                                    <div class="help-block"><ul role="alert"><li>{{ $errors->first('payment_system') }}</li></ul></div>
                                @endif							        
						    </div>
						</div>	

						<div class="form-group row {{ $errors->has('plan_id') ? ' error' : '' }}">
						    <label for="example-month-input" class="col-2 col-form-label">План (необязательно)</label>
						    <div class="col-10">
						        <select class="custom-select col-12" id="inlineFormCustomSelect" name="plan_id">
						            <option value="0">Выбрать...</option>
						            @foreach($plans as $row)
						            	<option {{($row->id==old('plan_id'))?'selected':NULL}} value="{{$row->id}}">{{$row->title}} - {{$row->percent}}% в день на {{$row->accruals}} дней</option>
						            @endforeach
						        </select>
						        @if ($errors->has('plan_id'))
                                    <div class="help-block"><ul role="alert"><li>{{ $errors->first('plan_id') }}</li></ul></div>
                                @endif						        
						    </div>
						</div>	

						<div class="form-group m-t-40 row {{ $errors->has('deposit_id') ? ' error' : '' }}">
	                        <label for="example-text-input" class="col-2 col-form-label">ID депозита</label>
	                        <div class="col-10">
	                            <input class="form-control" type="number" step="any" name="deposit_id" value="{{ old('deposit_id') }}" id="example-text-input">
	                            @if ($errors->has('deposit_id'))
                                    <div class="help-block"><ul role="alert"><li>{{ $errors->first('deposit_id') }}</li></ul></div>
                                @endif		                            
	                        </div>
	                    </div>

	                    <div class="form-group m-t-40 row {{ $errors->has('amount') ? ' error' : '' }}">
	                        <label for="example-text-input" class="col-2 col-form-label">Сумма</label>
	                        <div class="col-10">
	                            <input class="form-control" type="number" step="any" name="amount" value="{{ old('amount') }}" id="example-text-input">
	                            @if ($errors->has('amount'))
                                    <div class="help-block"><ul role="alert"><li>{{ $errors->first('amount') }}</li></ul></div>
                                @endif		                            
	                        </div>
	                    </div>

	                    <div class="form-group m-b-0">
                            <div class="offset-sm-2 col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">Создать операцию</button>
                            </div>
                        </div>
                        {{ csrf_field() }}
	                </form>
	            </div>
	        </div>
	    </div>
	</div>    
@endsection