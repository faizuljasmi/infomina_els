@extends('adminlte::page')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<style>
    i.fa {
        display: inline-block;
    }
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
                        <th>No.</th>
                        <th>Name</th>
                        <th>Day(s)</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="healthmetrics_table">
                @php $count = ($medical_certs->currentPage()-1) * $medical_certs->perPage(); @endphp
                @foreach($medical_certs as $mc)
                    <tr>
                        <td class="d-none">{{ $mc->application_id }}</td>
                        <td>{{ ++$count }}</td>
                        <td>{{ $mc->user->name }}</td>
                        <td>{{ $mc->total_days }}</td>
                        <td>{{ $mc->leave_from }}</td>
                        <td>{{ $mc->leave_to }}</td>
                        <td>
                            @if ( $mc->status == 'Auto Applied' )
                                <span class="badge badge-pill badge-success">{{ $mc->status }}</span>
                            @else
                                <span class="badge badge-pill badge-warning">{{ $mc->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('view_application', $mc->application_id) }}">
                                <button type="button" class="btn btn-info btn-sm" title="View Application">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </a>
                            <a href="{{ $mc->link }}" target="_blank">
                                <button type="button" class="btn btn-sm btn-danger" title="View MC">
                                    <i class="fas fa-file-medical-alt"></i>
                                </button>
                            </a>
                            @if ( $mc->status == 'Reverted' )
                                <button type="button" class="btn btn-sm btn-warning action-revert" title="Revert Changes" disabled><i class="fas fa-undo-alt"></i></button>
                            @else
                                <button type="button" class="btn btn-sm btn-warning action-revert" title="Revert Changes"><i class="fas fa-undo-alt"></i></button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <br>
            {!! $medical_certs->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
</div>

<div id="loading">
    <div id="loading-image">
        <figure>
            <img src="{{url('images/loader.gif')}}" alt="Loading..." />
            <figcaption>Retrieving data from HealthMetrics</figcaption>
        </figure>
    </div>
</div>

<!-- Confirm Revert -->
<div class="modal fade" id="revert_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Confirmation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            Are you sure you want to revert the changes made? 
            <a data-toggle="popover" data-placement="top" data-trigger="hover" data-content="Note : This leave application will be cancelled and the employee may need to submit a new application manually. Changes can't be undone."><i class="fa fa-info-circle"></i></a> 
            <input type="hidden" id="application_id" value=""/>
            <input type="hidden" id="total_days" value=""/>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id="confirm_revert">Yes</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        </div>
    </div>
</div>

<script>
$( document ).ready(function() 
{

    $('[data-toggle="popover"]').popover();

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
                // console.log(data, "MC");
                spinner.hide();
                location.reload();
            }
        })
    });

    $('.action-revert').click(function() 
    {
        let row = $(this).closest('tr');
        var application_id = $(row).find('td:eq(0)').text();
        var total_days = $(row).find('td:eq(3)').text();

        $('#application_id').val(application_id);
        $('#total_days').val(total_days);

        $('#revert_modal').modal('show');
    });

    $('#confirm_revert').click(function() 
    {
        let application_id = $('#application_id').val();
        let total_days = $('#total_days').val();

        revert_changes(application_id, total_days);
    });

    function revert_changes(application_id, total_days) 
    {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method: 'POST',
            url: '/revert-healthmetrics',
            dataType: 'json',
            data: {application_id:application_id, total_days:total_days},
            success: function (data) {
                console.log(data);
                $('#application_id').val('');
                $('#total_days').val('');
                location.reload();
            }
        })
    }

});
</script>
@endsection
