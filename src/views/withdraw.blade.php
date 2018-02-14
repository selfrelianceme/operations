<div class="card text-left">
    <div class="card-block">
        <div class="text-right">
            <h2 class="font-light m-b-0"><i class="ti-arrow-up text-danger"></i> {{$pending}}</h2>
            <span class="text-muted"><a href="{{ route('AdminOperations', ['type[]' => 'WITHDRAW', 'status[0]' => 'pending', 'status[1]' => 'error']) }}">Выплаты в ожидании</a></span>
        </div>
        <span class="text-danger">{{$percent}}%</span>
        <div class="progress">
            <div class="progress-bar bg-danger" role="progressbar" style="width: {{$percent}}%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>