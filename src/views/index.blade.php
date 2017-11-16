@extends('adminamazing::teamplate')

@section('pageTitle', 'Заявки на вывод')
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
	                <section>
	                    <div class="row">
	                        <div class="col-md-3">
	                            <div class="form-group">
	                                <label for="firstName1">ID заявки :</label>
	                                <input type="text" class="form-control" id="firstName1"> </div>
	                        </div>
	                        <div class="col-md-3">
	                            <div class="form-group">
	                                <label for="lastName1">Пользователь (Email) :</label>
	                                <input type="text" class="form-control" id="lastName1"> </div>
	                        </div>
	                        <div class="col-md-3">
	                            <div class="form-group">
	                                <label for="emailAddress1">Транзакция :</label>
	                                <input type="email" class="form-control" id="emailAddress1"> </div>
	                        </div>
	                        <div class="col-md-3">
	                            <div class="form-group">
	                                <label for="phoneNumber1">Кошелек :</label>
	                                <input type="tel" class="form-control" id="phoneNumber1"> </div>
	                        </div>
	                    </div>
	                    <div class="row">
	                        <div class="col-md-4">
	                            <div class="form-group">
	                                <label for="location1">Платежная система :</label>
	                                <select class="custom-select form-control" id="location1" name="location">
	                                    <option value="">Select City</option>
	                                    @foreach($payment_systems as $row)
	                                    	<option value="{{$row->id}}">{{$row->title}}, {{$row->currency}}</option>
	                                    @endforeach
	                                </select>
	                            </div>
	                        </div>
	                        <div class="col-md-4">
	                            <div class="form-group">
	                                <label for="location1">Операция :</label>
	                                <select class="custom-select form-control" id="location1" name="location">
	                                    <option value="">Выбрать операцию</option>
	                                    @foreach($operations as $key=>$value)
	                                    	<option value="{{$key}}">{{$value}}</option>
	                                    @endforeach
	                                </select>
	                            </div>
	                        </div>
	                        <div class="col-md-4">
	                            <div class="form-group">
	                                <label for="location1">Статус :</label>
	                                <select class="custom-select form-control" id="location1" name="location">
	                                    <option value="">Выбрать статус</option>
	                                    @foreach($statuses as $value)
	                                    	<option value="{{$value->status}}">{{$value->status}}</option>
	                                    @endforeach
	                                </select>
	                            </div>
	                        </div>
	                    </div>
	                </section>

	            </div>
	        </div>
            
            <!-- Row -->

            <div class="card">
                <div class="card-block">
                	<form method="POST" action="">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                    	<button type="button" id="SelectAllPaymentSystem" class="btn btn-sm btn-success">Select all</button>
                                    </th>
                                    <th>#</th>
                                    <th>Пользователь</th>
                                    <th>Сумма</th>
                                    <th>Дата</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $row)
	                                <tr class="active">
	                                	<td class="text-center">
	                                		<input id="checkbox0" name="application[]" type="checkbox">
	                                	</td>
	                                    <td scope="row">{{$row->id}}</td>
	                                    <td>
	                                    	<a target="_blank" href="{{route('AdminUsersEdit', $row->user_id)}}">{{$row->email}}</a><br/>
	                                    	{{$row->data_info->wallet}}
	                                    </td>
	                                    <td>{{$row->title}}<br/>{{$row->amount}} {{$row->currency}}</td>
	                                    <td>{{$row->created_at}}<br/>{{$row->updated_at}}</td> 
	                                    <td>{{$row->status}}</td> 
	                                </tr>
                                @endforeach
                            </tbody>
                        </table>
						<div class="form-actions">
							<div class="row">
								<div class="col-4"><button type="submit" class="btn btn-block btn-success">Выплатить</button></div>
								<div class="col-4"><button type="submit" class="btn btn-block btn-info">Готово</button></div>
								<div class="col-4"><button type="button" class="btn btn-block btn-danger">Отменить заявку</button></div>
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