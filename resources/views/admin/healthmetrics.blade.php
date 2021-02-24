@extends('adminlte::page')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<style>
    #loading {
        width: 100%; height: 100%; top: 0; left: 0; position: fixed; opacity: 0.7; background-color: #fff; z-index: 99; text-align: center; display: none;
    }
    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        /* bring your own prefixes */
        transform: translate(-50%, -50%);
        z-index: 100;
    }
</style>
@if(session()->has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="icon fa fa-times"></i>
    {{ session()->get('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="mt-2 col-md-12">
    <div class="card">
        <div class="card-header bg-teal">
            <b>Health Metrics Records</b>
        </div>
        <div class="card-body">
            <div>
                <button type="button" class="btn btn-sm btn-success mb-2" id="btn_fetch">Fetch <span class="far fa-envelope"></span></button>
            </div>
            @if ($medical_certs->count() > 0)
                <h6><strong>Displaying {{$medical_certs->count()}} out of {{$medical_certs->total()}} entries.</strong></h6>
            @endif
            <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Name</th>
                        <th scope="col">Day(s)</th>
                        <th scope="col">From Date</th>
                        <th scope="col">To Date</th>
                        <th scope="col">MC</th>
                    </tr>
                </thead>
                <tbody id="healthmetrics_table">
                <?php $count = 0;?>
                @foreach($medical_certs as $mc)
                    <tr>
                        <td>{{ ++$count }}</td>
                        <td>{{ $mc->user->name }}</td>
                        <td>{{ $mc->total_days }}</td>
                        <td>{{ $mc->leave_from }}</td>
                        <td>{{ $mc->leave_to }}</td>
                        <td><a href="{{ $mc->link }}" target="_blank"><button type="button" class="btn btn-sm btn-info">View</button></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $medical_certs->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
</div>

<div id="loading">
    <div id="loading-image">
        <figure>
            <img src="{{url('images/loader.gif')}}" alt="Loading..." />
            <figcaption>Retrieving data from HealthMetrics...</figcaption>
        </figure>
    </div>
</div>

<script>
$( document ).ready(function() 
{
    $('#btn_fetch').click(function() 
    {
        var spinner = $('#loading');
        spinner.show();

        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method: 'POST',
            url: '/fetch-healthmetrics',
            dataType: 'json',
            success: function (data) {
                console.log(data, "MC");
                location.reload();
            }
        })
    });

});
</script>
@endsection
