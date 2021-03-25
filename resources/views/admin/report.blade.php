@extends('adminlte::page')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<style>
    .buttonStat {
        width: 100px;
    }

    .card {
        margin: 0 auto;
        float: none;
        margin-bottom: 20px;
    }

    .zoom:hover {
        -ms-transform: scale(1.5);
        /* IE 9 */
        -webkit-transform: scale(1.5);
        /* Safari 3-8 */
        transform: scale
    }

    #loading {
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: fixed;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
        text-align: center;
        display: none;
    }

    #loading-modal {
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: fixed;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
        text-align: center;
        display: none;
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
            <b>Reports</b>
        </div>
        <div class="card-body">
            <div class="panel panel-default">
                <div class="row">
                    <div class="card col-8">
                        <div class="card-body">
                            <form id="search_form" class="input-horizontal" action="{{ route('index') }}" method="get">
                                <div class="form-inline form-group">
                                    <!-- Name -->
                                    <div class="input-group col-12">
                                        <input type="search" name="name" id="name" placeholder="Name"
                                            value="{{ isset($search_name)? $search_name: '' }}" class="form-control"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-inline form-group">
                                    <!-- Date From -->
                                    <div class="input-group col-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar-day"></i>
                                            </span>
                                        </div>
                                        <input placeholder="Leave From" class="form-control" type="text"
                                            onfocus="(this.type='date')" value="{{ isset($date_from)? $date_from: '' }}"
                                            name="date_from" id="date_from">
                                    </div>
                                    <!-- Leave Type -->
                                    <div class="input-group col-6">
                                        <select class="form-control" name="leave_type" id="leave_type">
                                            <option value="" disabled selected>Select Leave Type</option>
                                            <option value="1">Annual</option>
                                            <option value="2">Calamity</option>
                                            <option value="3">Sick</option>
                                            <option value="4">Hospitalization</option>
                                            <option value="5">Compassionate</option>
                                            <option value="6">Emergency</option>
                                            <option value="7">Marriage</option>
                                            <option value="8">Maternity</option>
                                            <option value="9">Paternity</option>
                                            <option value="10">Traning</option>
                                            <option value="11">Unpaid</option>
                                            <option value="12">Replacement</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-inline form-group">
                                    <!-- Date To -->
                                    <div class="input-group col-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar-day"></i>
                                            </span>
                                        </div>
                                        <input placeholder="Leave To" class="form-control" type="text"
                                            onfocus="(this.type='date')" value="{{ isset($date_to)? $date_to: '' }}"
                                            name="date_to" id="date_to">
                                    </div>
                                    <!-- Status -->
                                    <div class="input-group col-6">
                                        <select class="form-control" name="leave_status" id="leave_status">
                                            <option value="" disabled selected>Select Status</option>
                                            <option value="PENDING">In Progress</option>
                                            <option value="APPROVED">Approved</option>
                                            <option value="CANCELLED">Cancelled</option>
                                            <option value="DENIED">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <div class="d-flex justify-content-end col-12">
                                <button data-toggle="collapse" data-target="#importCard"
                                    class="btn btn-warning mr-1">Import</button>
                                <button data-toggle="collapse" data-target="#exportCard"
                                    class="btn btn-warning mr-1">Export</button>
                                <button form="search_form" type="submit" class="btn btn-primary mr-1">Search</button>
                                <button id="reset_btn" type="button" class="btn btn-secondary">Reset</button>
                            </div>
                            <form id="btnExportBal" action="{{ route('excel_export_bal') }}"
                                enctype="multipart/form-data"></form>
                            <form id="btnExportAll" action="{{ route('excel_export') }}" enctype="multipart/form-data">
                            </form>
                            <form id="btnExportCurrent" action="{{ route('excel_export') }}"
                                enctype="multipart/form-data">
                                <input type="hidden" name="excel_name"
                                    value="{{isset($search_name)? $search_name: ''}}">
                                <input type="hidden" name="excel_date_from"
                                    value="{{isset($date_from)? $date_from: ''}}">
                                <input type="hidden" name="excel_date_to" value="{{isset($date_to)? $date_to: ''}}">
                                <input type="hidden" name="excel_leave_type"
                                    value="{{isset($leave_type)? $leave_type: ''}}">
                                <input type="hidden" name="excel_leave_status"
                                    value="{{isset($leave_status)? $leave_status: ''}}">
                            </form>
                        </div>
                    </div>
                    <div class="card col-4">
                        <div class="card-body">
                            <input type="hidden" id="approve" value="{{ $count_approve }}">
                            <input type="hidden" id="reject" value="{{ $count_reject }}">
                            <input type="hidden" id="pending" value="{{ $count_pending }}">
                            <input type="hidden" id="cancel" value="{{ $count_cancel }}">
                            <div class="zoom" id="piechart"></div>
                        </div>
                    </div>
                </div>
                <div id="importCard" class="collapse">
                    <div class="card col-12">
                        <div class="card-body d-flex justify-content-center">
                            <form class="form-inline" action="{{ route('excel_import') }}" method="post">
                                <div class="form-group">
                                    <input type="file" class="form-control" name="import_file" />
                                </div>
                                <button style="margin-left: 10px;" class="btn btn-success mr-2"
                                    type="submit">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="exportCard" class="collapse">
                    <div class="card col-12">
                        <div class="card-body d-flex justify-content-center">
                            <button form="btnExportCurrent" type="submit" class="btn btn-success mr-1">Export
                                Current</button>
                            <button form="btnExportAll" type="submit" class="btn btn-success mr-1">Export All
                                Applications</button>
                            <button form="btnExportBal" type="submit" class="btn btn-success mr-1">Export Leave
                                Balance</button>
                        </div>
                    </div>
                </div>
                @if ($leave_app->count() > 0)
                <h6><strong>Displaying {{$leave_app->count()}} out of {{$leave_app->total()}} leave
                        applications.</strong></h6>
                <h6><span class="badge badge-info">{{ isset($leave_type)? $leave_type: '' }}</span></h6>
                @endif
                <table class="table table-sm table-bordered table-striped table-hover" id="la_table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Day(s)</th>
                            <th>Type</th>
                            <th width="10%">From Date</th>
                            <th width="10%">To Date</th>
                            <th width="10%">Resume Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th width="10%">Apply Date</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 0;?>
                        @foreach($leave_app as $la)
                        <tr>
                            <td>{{ ++$count }}</td>
                            <td>{{ $la->user->name }}</td>
                            <td>{{ $la->total_days }}</td>
                            @if($la->leaveType->name == "Replacement" && $la->remarks == "Claim")
                            <td>Replacement (Claim)</td>
                            @elseif($la->leaveType->name == "Replacement" && $la->remarks == "Apply")
                            <td>Replacement (Apply)</td>
                            @else
                            <td>{{ $la->leaveType->name }}</td>
                            @endif
                            <td>{{ $la->date_from }}</td>
                            <td>{{ $la->date_to }}</td>
                            <td>{{ $la->date_resume }}</td>
                            <td align="center">
                                <button type="button" class="btn btn-sm btn-info" data-toggle="popover"
                                    data-trigger="focus" title="Details" data-content="{{ $la->reason }}">View</button>
                            </td>
                            <td align="center">
                                @if ($la->status == "APPROVED" )
                                <button type="button" class="btn buttonStat btn-sm btn-success "
                                    disabled>Approved</button>
                                @elseif ($la->status == "CANCELLED")
                                <button type="button" class="btn buttonStat btn-sm btn-warning"
                                    disabled>Cancelled</button>
                                @elseif ($la->status == "DENIED_1" || $la->status == "DENIED_2" || $la->status ==
                                "DENIED_3")
                                <button type="button" class="btn buttonStat btn-sm btn-danger"
                                    disabled>Rejected</button>
                                @elseif($la->status == "TAKEN")
                                <button type="button" class="btn buttonStat btn-sm btn-danger"
                                    disabled>Taken</button>
                                @elseif($la->status == "EXPIRED")
                                <button type="button" class="btn buttonStat btn-sm btn-danger"
                                    disabled>Expired</button>
                                @else
                                <button type="button" class="btn buttonStat btn-sm btn-primary" disabled>In
                                    Progress</button>
                                @endif
                            </td>
                            <td>{{\Carbon\Carbon::parse($la->created_at)->isoFormat('Y-MM-DD')}}</td>
                            <td align="center">
                                <button type="button" id="change_status_btn" class="btn btn-warning btn-sm"
                                    data-toggle="tooltip" data-placement="left" title="Change Status"
                                    value="{{ $la->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" id="view_history_btn" class="btn btn-primary btn-sm"
                                    data-toggle="tooltip" data-placement="left" title="View History"
                                    value="{{ $la->id }}" data-user="{{ $la->user->name }}">
                                    <i class="fas fa-history"></i>
                                </button>
                                <a href="{{ route('view_application', $la->id) }}">
                                    <button type="button" class="btn btn-info btn-sm" title="View Application">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @if ($leave_app->count() == 0)
                        <tr align="center">
                            <td colspan="11"><strong>No records found.</strong></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                {!! $leave_app->appends(\Request::except('page'))->render() !!}
            </div>
        </div>
    </div>
</div>
</div>
</div>

<div id="loading">
    <div id="loading-image">
        <figure>
            <img src="{{url('images/loader.gif')}}" alt="Loading..." />
            <figcaption>Hold on...</figcaption>
        </figure>
    </div>
</div>

<!-- Status Change -->
<div class="modal fade" id="change_status_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Leave Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="leave_id">
                <div class="mb-3">
                    <h6>Hi {{ Auth::user()->name }}, you're about to edit the leave application of <b id="la_user"></b>.
                    </h6>
                    <h6>Current Leave Status : <button id="la_status" type="button" class="btn buttonStat btn-sm"
                            disabled></button>
                </div>
                <div class="mb-3">
                    <h6><span id="show_status" class="d-none badge badge-warning"><b id="approver_name"></b></span>
                        <h6>
                </div>
                <select class="form-control mb-3" id="new_status">
                    <option value="" disabled selected>Select Leave Status</option>
                    <option value="APPROVE" disabled>Approve</option>
                    <option value="REJECT">Reject</option>
                    <option value="CANCEL">Cancel</option>
                </select>
                <textarea class="form-control" name="status_remarks" id="status_remarks"
                    placeholder="Add Remarks"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="save_new_status" class="btn btn-primary">Save changes</button>
            </div>
        </div>
        <div id="loading-modal">
            <div id="loading-image">
                <figure>
                    <img src="{{url('images/loader.gif')}}" alt="Loading..." />
                    <figcaption>Hold on...</figcaption>
                </figure>
            </div>
        </div>
    </div>
</div>


<!-- View History -->
<div class="modal fade" id="history_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="leave_id_history">
                <h6>Leave application's history for <b id="history_user"></b>.<h6>
                        <table class="table table-sm table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Edited By</th>
                                    <th>Edited Date</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="history_table">
                            </tbody>
                        </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>

    <script>
        $("#la_table").on('click', '#change_status_btn', function()
{
    var spinner = $('#loading');
    spinner.show();

    $('#leave_id').val('');

    var mode = 'isView';
    var leave_id = this.value;

    if (leave_id != null) {
        $('#leave_id').val(leave_id);
        console.log(leave_id);
    }

    $('#save_new_status').attr('disabled', true);
    $('#new_status').val('');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: '/change-leave-status',
        dataType: 'json',
        data: {mode:mode, leave_id:leave_id},
        success: function (data) {
            console.log(data, 'data');

            var la_user = data.leave_app.user.name;
            var leave_status = data.leave_app.status;
            var leave_type = data.leave_app.leave_type;
            var user_id = data.leave_app.user_id;

            $('#la_user').html(la_user);
            $('#leave_type').val(leave_type);
            $('#user_id').val(user_id);

            $("#show_status").addClass('d-none');
            $('#la_status').html('').removeClass('btn-primary');
            $('#la_status').html('').removeClass('btn-danger');
            $('#la_status').html('').removeClass('btn-success');
            $('#la_status').html('').removeClass('btn-warning');

            if (data.leave_app.approver_one) { var approver_one = data.leave_app.approver_one.name } else { var approver_one = ''}
            if (data.leave_app.approver_two) { var approver_two = data.leave_app.approver_two.name } else { var approver_two = ''}
            if (data.leave_app.approver_three) { var approver_three = data.leave_app.approver_three.name } else { var approver_three = ''}

            if (leave_status == 'PENDING_1' || leave_status == 'PENDING_2' || leave_status == 'PENDING_3') {
                $('#la_status').html('In Progress').addClass('btn-primary');

                if (leave_status == 'PENDING_1') {
                    $('#approver_name').html('Pending at '+approver_one+'. (Level 1)');
                } else if (leave_status == 'PENDING_2') {
                    $('#approver_name').html('Pending at '+approver_two+'. (Level 2)');
                } else if (leave_status == 'PENDING_3') {
                    $('#approver_name').html('Pending at '+approver_three+'. (Level 3)');
                }

                $('#show_status').removeClass('d-none');
            } else if (leave_status == 'DENIED_1' || leave_status == 'DENIED_2' || leave_status == 'DENIED_3') {
                $('#la_status').html('Rejected').addClass('btn-danger');

                if (leave_status == 'DENIED_1') {
                    $('#approver_name').html('Rejected by '+approver_one+'. (Level 1)');
                } else if (leave_status == 'DENIED_2') {
                    $('#approver_name').html('Rejected by '+approver_two+'. (Level 2)');
                } else if (leave_status == 'DENIED_3') {
                    $('#approver_name').html('Rejected by '+approver_three+'. (Level 3)');
                }

                $('#show_status').removeClass('d-none');
            } else if (leave_status == 'APPROVED') {
                $('#la_status').html('Approved').addClass('btn-success');
            } else if (leave_status == 'CANCELLED') {
                $('#la_status').html('Cancelled').addClass('btn-warning');
            } else if (leave_status == 'TAKEN') {
                $('#la_status').html('Taken').addClass('btn-danger');
            }  else if (leave_status == 'EXPIRED') {
                $('#la_status').html('Expired').addClass('btn-danger');
            }

            // Todo 
            

            $('#change_status_modal').modal('show');
            spinner.hide();
        }
    });
});

$("#change_status_modal").on('change', '#new_status', function()
{
    $('#save_new_status').attr('disabled', false);
});

$("#change_status_modal").on('click', '#save_new_status', function()
{
    var spinner = $('#loading-modal');
    spinner.show();

    var mode = 'isEdit';
    var leave_id = $('#leave_id').val();
    var new_status = $('#new_status').val();
    var status_remarks = $('#status_remarks').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: '/change-leave-status',
        dataType: 'json',
        data: {mode:mode, leave_id:leave_id, new_status:new_status, status_remarks:status_remarks},
        success: function (data) {
            // console.log(data, 'data');
            location.reload();
            spinner.hide();
        }
    });
});

$("#la_table").on('click', '#view_history_btn', function()
{
    var spinner = $('#loading');
    spinner.show();

    $('#leave_id_history').val('');

    var leave_id = this.value;

    if (leave_id != null) {
        $('#leave_id_history').val(leave_id);
    }

    var history_user = $(this).data('user');
    $('#history_user').html(history_user);

    $('#history_table tr').remove();

    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: '/load-history',
        dataType: 'json',
        data: {leave_id:leave_id},
        success: function (data) {
            // console.log(data, "data");
            var history = "";
            var html = "";
            for(var i = 0; i < data.histories.length; i++){
                history = data.histories[i];
                var action = history.action;
                if ( history.remarks != null ) {
                    var remarks = history.remarks;
                } else {
                    var remarks = "N/A";
                }
                var date = history.created_at;
                var created_at = date.substring(0, 10);
                var edited_by = history.editor.name;
                html += '<tr><td>'+action+'</td><td>'+edited_by+'</td><td>'+created_at+'</td><td>'+remarks+'</td></tr>';
            }
            if ( data.histories.length == 0) {
                html += '<tr align="center"><td colspan="4">No history found.</td></tr>';
            }
            $('#history_table').append(html);
            $('#history_modal').modal('show');
            spinner.hide();
        }
    })
});

var route = "{{ url('reports/autocomplete') }}";
$('#name').typeahead({
    source:  function (name, process) {
    return $.get(route, { name: name }, function (data) {
            console.log(data);
            return process(data);
        });
    }
});

// Load google charts
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

// Draw the chart and set the chart values
function drawChart() {

    var approve = $('#approve').val();
    var approve_no = parseInt(approve);

    var reject = $('#reject').val();
    var reject_no = parseInt(reject);

    var pending = $('#pending').val();
    var pending_no = parseInt(pending);

    var cancel = $('#cancel').val();
    var cancel_no = parseInt(cancel);

    var data = google.visualization.arrayToDataTable([
        ['Task', 'Applications'],
        ['Pending', pending_no],
        ['Rejected', reject_no],
        ['Cancelled', cancel_no],
        ['Approved', approve_no],
    ]);

    // Optional; add a title and set the width and height of the chart
    var options = {'width':350, 'height':200, 'pieHole':0.1};

    // Display the chart inside the <div> element with id="piechart"
    var piechart = new google.visualization.PieChart(document.getElementById('piechart'));
    piechart.draw(data, options);
}


$(function () {

    $('[data-toggle="popover"]').popover()
    $('[data-toggle="tooltip"]').tooltip()

})

$('#search_form').submit(function()
{
    var spinner = $('#loading');
    spinner.show();
});

$('#reset_btn').click(function()
{
    var spinner = $('#loading');
    spinner.show();

    $('#name').val('');
    $('#date_from').val('');
    $('#date_to').val('');
    $('#leave_type').val('');
    $('#leave_status').val('');
    window.location.href = "{{ route('index') }}";
});

    </script>
    @endsection
