@extends('adminlte::page')

@section('content')
<style>
    .buttonStat {width: 100px;}
    .card {margin: 0 auto; float: none; margin-bottom: 20px;}
</style>
<div class="mt-2 col-md-12">
    <div class="card">
        <div class="card-header bg-teal">
            Reports
        </div>
        <div class="card-body">
            <div class="panel panel-default">
                <div class="row">
                    <div class="card col-8">
                        <div class="card-body">
                            <form id="btnSearch" class="input-horizontal" action="{{ route('search') }}" method="get">
                                <div class="form-inline form-group">
                                    <!-- Name -->
                                    <div class="input-group col-12">
                                        <input type="search" name="name" id="name" placeholder="Name" value="{{ isset($search_name)? $search_name: '' }}" class="form-control">
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
                                        <input placeholder="Leave From" class="form-control" type="text" onfocus="(this.type='date')" value="{{ isset($date_from)? $date_from: '' }}" name="date_from" id="date_from">
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
                                        <input placeholder="Leave To" class="form-control" type="text" onfocus="(this.type='date')" value="{{ isset($date_to)? $date_to: '' }}" name="date_to" id="date_to">
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
                                <button data-toggle="collapse" data-target="#importCard" class="btn btn-warning mr-1">Import</button>
                                <button data-toggle="collapse" data-target="#exportCard" class="btn btn-warning mr-1">Export</button>
                                <button onclick="resetForm()" type="button" class="btn btn-primary mr-1">Reset</button>
                                <button form="btnSearch" type="submit" class="btn btn-primary">Search</button>
                            </div>
                            <form id="btnExportBal" action="{{ route('excel_export_bal') }}" enctype="multipart/form-data"></form>
                            <form id="btnExportAll" action="{{ route('excel_export_all') }}" enctype="multipart/form-data"></form>
                            <form id="btnExportSearch" action="{{ route('excel_export_search') }}" enctype="multipart/form-data">
                                <input type="hidden" name="excel_name" value="{{isset($search_name)? $search_name: ''}}">
                                <input type="hidden" name="excel_date_from" value="{{isset($date_from)? $date_from: ''}}">
                                <input type="hidden" name="excel_date_to" value="{{isset($date_to)? $date_to: ''}}">
                                <input type="hidden" name="excel_leave_type" value="{{isset($leave_type)? $leave_type: ''}}">
                                <input type="hidden" name="excel_leave_status" value="{{isset($leave_status)? $leave_status: ''}}">
                            </form>
                        </div>
                    </div>
                    <div class="card col-4">
                        <div class="card-body">
                        <table class="table table-sm table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th colspan="2">Leave Applications</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Approved</td>
                                <td>{{ $count_approve }}</td>
                            </tr>
                            <tr>
                                <td>Pending</td>
                                <td>{{ $count_pending }}</td>
                            </tr>
                            <tr>
                                <td>Rejected</td>
                                <td>{{ $count_reject }}</td>
                            </tr>
                            <tr>
                                <td>Cancelled</td>
                                <td>{{ $count_cancel }}</td>
                            </tr>
                            <tr>
                                <td><b>Total</b></td>
                                <td><b>{{ $count_all }}</b></td>
                            </tr>
                            </tbody>
                            </table>
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
                            <button style="margin-left: 10px;" class="btn btn-success mr-2" type="submit">Upload</button>
                        </form>
                    </div>
                </div>
                </div>
                <div id="exportCard" class="collapse">
                <div class="card col-12">
                    <div class="card-body d-flex justify-content-center">
                        <button form="btnExportSearch" type="submit" class="btn btn-success mr-1">Export Current</button>
                        <button form="btnExportAll" type="submit" class="btn btn-success mr-1">Export All Applications</button>
                        <button form="btnExportBal" type="submit" class="btn btn-success mr-1">Export Leave Balance</button>
                    </div>
                </div>
                </div>
                @if ($users->count() > 0)
                    <h6><strong>Displaying {{$users->count()}} out of {{$users->total()}} leave applications.</strong></h6>
                @endif
                <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>@sortablelink('name', 'Name')</th>
                    <th>Day(s)</th>
                    <th>Type</th>
                    <th width="10%">@sortablelink('date_from', 'From Date')</th>
                    <th width="10%">@sortablelink('date_from', 'To Date')</th>
                    <th width="10%">@sortablelink('date_from', 'Resume Date')</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th width="10%">@sortablelink('created_at', 'Apply Date')</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 0;?>
                @foreach($users as $row)
                <tr>
                    <td>{{ ++$count }}</td>
                    <td class="user_name">{{ $row->name }}</td>
                    <td>{{ $row->total_days }}</td>
                    <td>{{ $row->leave_type_name }}</td>
                    <td>{{ $row->date_from }}</td>
                    <td>{{ $row->date_to }}</td>
                    <td>{{ $row->date_resume }}</td>
                    <td align="center">
                        <button type="button" class="btn btn-sm btn-info" data-toggle="popover" data-trigger="focus" title="Details" 
                        data-content="{{ $row->reason }}">View</button>
                    </td>
                    <td align="center">
                        @if ($row->status == "APPROVED" )
                            <button type="button" class="btn buttonStat btn-sm btn-success" disabled>Approved</button>
                        @elseif ($row->status == "CANCELLED")
                            <button type="button" class="btn buttonStat btn-sm btn-warning" disabled>Cancelled</button>
                        @elseif ($row->status == "DENIED_1" || $row->status == "DENIED_2" || $row->status == "DENIED_3")
                            <button type="button" class="btn buttonStat btn-sm btn-danger" disabled>Rejected</button>
                        @else
                            <button type="button" class="btn buttonStat btn-sm btn-primary" disabled>In Progress</button>
                        @endif
                    </td>
                    <td>{{\Carbon\Carbon::parse($row->created_at)->isoFormat('Y-MM-DD')}}</td>
                    <td align="center">
                        <span data-toggle="modal" data-target="#change_status_modal">
                            <button type="button" class="btn btn-primary btn-sm use-this" data-toggle="tooltip" data-placement="left" title="Change Status">
                                <i class="fas fa-edit"></i>
                            </button>
                        </span>
                        <span data-toggle="modal" data-target="#history_modal">
                            <button type="button" class="btn btn-primary btn-sm use-this" data-toggle="tooltip" data-placement="left" title="View History">
                                <i class="fas fa-history"></i>
                            </button>
                        </span>
                    </td>
                    <td class="d-none user_leave_status">{{ $row->status }}</td>
                    <td class="d-none user_id">{{ $row->user_id }}</td>
                    <td class="d-none leave_app_id">{{ $row->leave_app_id }}</td>
                </tr>
                @endforeach
                @if ($users->count() == 0)
                    <tr align="center">
                        <td colspan="10"><strong>No records found.</strong></td>
                    </tr>
                @endif
                </tbody>
                </table>
                {!! $users->appends(\Request::except('page'))->render() !!}
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Change -->
<div class="modal fade" id="change_status_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Change Status</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="change_status_form" action="{{ route('change_status') }}" method="get">
                <h6>Hi {{ $edited_by->name }}, you are about to edit leave application status for <b id="status_user_name"></b>.</h6>
                <h6>Current Leave Status : <b id="status_leave_status"></b><br><br>
                <!-- <label for="status_leave_status">Current Leave Status :</label>
                <input class="form-control" type="text" id="status_leave_status" readonly><br> -->
                <!-- <label for="change_status">Change Leave Status To :</label> -->
                <select class="form-control" name="change_status" id="change_status">
                    <option value="" disabled selected>Change Leave Status To</option>
                    <option value="1">PENDING_1</option>
                    <option value="2">PENDING_2</option>
                    <option value="3">PENDING_3</option>
                    <option value="4">APPROVED</option>
                    <option value="5">DENIED_1</option>
                    <option value="6">DENIED_2</option>
                    <option value="7">DENIED_3</option>
                    <option value="8">CANCELLED</option>
                </select>
                <input type="hidden" id="status_user_id" name="status_user_id">
                <input type="hidden" id="status_app_id" name="status_app_id">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button form="change_status_form" type="submit" class="btn btn-primary">Save changes</button>
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
            <div>
            <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Remarks</th>
                        <th>Date Modified</th>
                    </tr>
                    </thead>
                <tbody>
                    @foreach($history as $list)
                    <tr>
                        <td>{{ $list->action }}</td>
                        <td>{{ $list->remarks }}</td>
                        <td>{{\Carbon\Carbon::parse($list->created_at)->isoFormat('Y-MM-DD')}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>

<script>

$(function () {
  $('[data-toggle="popover"]').popover()
  $('[data-toggle="tooltip"]').tooltip()
})

$(".use-this").click(function() {
    var $row = $(this).closest("tr");    // Find the row
    var $name = $row.find(".user_name").html(); // Find the text
    var $status = $row.find(".user_leave_status").html(); // Find the text
    var $id = $row.find(".user_id").html(); // Find the text
    var $app_id = $row.find(".leave_app_id").html(); // Find the text
    // alert($id);
    $("#status_user_name").html($name);
    $("#status_leave_status").html($status);
    $("#status_user_id").val($id);
    $("#status_app_id").val($app_id);
});

function resetForm() {
    document.getElementById("name").value = '';
    document.getElementById("date_from").value = '';
    document.getElementById("date_to").value = '';
    document.getElementById("leave_type").value = '';
    document.getElementById("leave_status").value = '';
    window.location.href = "{{ route('search') }}";
}

</script>
@endsection
