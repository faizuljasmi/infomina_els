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
            <form id="search_form" class="input-horizontal" action="{{ route('healthmetric_search') }}" method="get">
                <div class="form-inline form-group">
                    <div class="input-group col-4">
                        <input type="search" name="name" id="name" placeholder="Name"
                            class="form-control"
                            autocomplete="off">
                    </div>
                    <!-- Date From -->
                    <div class="input-group col-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-calendar-day"></i>
                            </span>
                        </div>
                        <input placeholder="Date From" class="form-control" type="text" onfocus="(this.type='date')" value="{{ isset($date_from)? $date_from: '' }}" name="date_from" id="date_from">
                    </div>
                    <!-- Date To -->
                    <div class="input-group col-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-calendar-day"></i>
                            </span>
                        </div>
                        <input placeholder="Date To" class="form-control" type="text" onfocus="(this.type='date')" value="{{ isset($date_to)? $date_to: '' }}" name="date_to" id="date_to">
                    </div>
                    <button form="search_form" type="submit" class="btn btn-primary mr-1">Search</button>
                    <a href="{{ route('healthmetric_index') }}"><button type="button" class="btn btn-secondary mr-1">Reset</button></a>
                    <!-- <button type="button" class="btn btn-success" id="btn_fetch">Fetch</button>
                    <button type="button" class="btn btn-success" id="btn_checkin">Checkin</button> -->
                    <a href="{{ route('healthmetric_mc_index') }}"><button type="button" class="btn btn-secondary mr-1">MC</button></a>
                </div>
            </form>
            <div>
            </div>
            @if ($checkins->count() > 0)
                <h6><strong>Displaying {{$checkins->count()}} out of {{$checkins->total()}} entries.</strong></h6>
            @endif
            <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Checkin Date</th>
                        <th>Checkin Time</th>
                        <th>Clinic</th>
                        <th>MC</th>
                        <th>Leave From</th>
                        <th>Leave To</th>
                        <th>Total Days</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="healthmetrics_table">
                @php $count = ($checkins->currentPage()-1) * $checkins->perPage(); @endphp
                @foreach($checkins as $chk)
                    <tr>
                        @php $leave_id = ($chk->mc != null) ? $chk->mc->application_id : null; @endphp
                        <td class="d-none">{{ $leave_id }}</td>
                        <td>{{ ++$count }}</td>
                        <td>{{ $chk->user->name }}</td>
                        <td>{{ $chk->check_in_date }}</td>
                        <td>{{ $chk->check_in_time }}</td>
                        <td>{{ $chk->clinic_name }}</td>
                        @php $mc = ($chk->mc != null) ? 'YES' : 'NO'; @endphp
                        <td><span class="badge badge-pill bg-indigo">{{ $mc }}</span></td>
                        @if ($chk->mc != null)
                            <td>{{ $chk->mc->leave_from }}</td>
                            <td>{{ $chk->mc->leave_to }}</td>
                            <td>{{ $chk->mc->total_days }}</td>
                            <td>
                                @if ( $chk->mc->status == 'Auto Applied' )
                                    <span class="badge badge-pill badge-success">{{ $chk->mc->status }}</span>
                                @else
                                    <span class="badge badge-pill badge-warning">{{ $chk->mc->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown dropleft">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="{{ route('view_application', $chk->mc->application_id) }}">View Application <i class="fas fa-eye"></i></a>
                                    <a class="dropdown-item" href="{{ $chk->mc->link }}" target="_blank">View MC <i class="fas fa-file-medical-alt"></i></a>
                                    @if ($chk->mc->status == 'Reverted')
                                        <a class="dropdown-item disabled" href="#">Revert Changes <i class="fas fa-undo-alt"></i></a>
                                    @else
                                        <a class="dropdown-item action-revert" href="#">Revert Changes <i class="fas fa-undo-alt"></i></a>
                                    @endif
                                </div>
                                </div>
                            </td>
                        @else 
                            <td>N/A</td>
                            <td>N/A</td>
                            <td>N/A</td>
                            <td>
                                <span class="badge badge-pill badge-info">Pending</span>
                            </td>
                            <td>
                            <div class="dropdown dropleft">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item disabled" href="#">No actions available.</a>
                                </div>
                                </div>
                            </td>
                        @endif
                        
                    </tr>
                @endforeach
                @if ($checkins->count() == 0)
                    <tr align="center">
                        <td colspan="11"><strong>No records found.</strong></td>
                    </tr>
                @endif
                </tbody>
            </table>
            <br>
            {!! $checkins->appends(\Request::except('page'))->render() !!}
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
    var route = "{{ url('reports/autocomplete') }}";
    $('#name').typeahead({
    source:  function (name, process) {
    return $.get(route, { name: name }, function (data) {
            // console.log(data);
            return process(data);
        });
    }
    });

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
                console.log(data, "MC");
                location.reload();
                // spinner.hide();
            }
        })
    });

    $('#btn_checkin').click(function() 
    {
        // var spinner = $('#loading');
        // spinner.show();

        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method: 'POST',
            url: '/fetch-checkins',
            dataType: 'json',
            success: function (data) {
                console.log(data, "check in");
                location.reload();
                // spinner.hide();
            }
        })
    });

    $('.action-revert').click(function() 
    {
        let row = $(this).closest('tr');
        var application_id = $(row).find('td:eq(0)').text();
        console.log(application_id)

        $('#application_id').val(application_id);

        $('#revert_modal').modal('show');
    });

    $('#confirm_revert').click(function() 
    {
        let application_id = $('#application_id').val();

        revert_changes(application_id);
    });

    function revert_changes(application_id) 
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
            data: {application_id:application_id},
            success: function (data) {
                console.log(data);
                $('#application_id').val('');
                location.reload();
            }
        })
    }

});
</script>
@endsection
