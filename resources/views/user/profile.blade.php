@extends('adminlte::page')

@section('content')
@if(session()->has('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="icon fa fa-check"></i>
    {{ session()->get('message') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<h4>Profile for {{$user->name}}</h4>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Profile</strong>
            </div>
            <div class="card-body">
                <form>
                    <fieldset disabled>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="name">Name</label>
                                <input type="name" class="form-control" id="name" placeholder="{{$user->name}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="staff_id">Staff ID</label>
                                <input type="text" class="form-control" id="staff_id" placeholder="{{$user->staff_id}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="{{$user->email}}">
                            </div>
                            @if (Gate::forUser(Auth::user())->allows('admin-dashboard'))
                            <div class="form-group col-md-3">
                                <label for="level">User Type</label>
                                <input type="text" class="form-control" id="level" placeholder="{{$user->user_type}}">
                            </div>
                            @endif
                            <div class="form-group col-md-3">
                                <label for="gender">Gender</label>
                                <input type="email" class="form-control" id="email" placeholder="{{$user->gender}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Employee Type</label>
                                <input type="text" class="form-control" id="type" placeholder="{{$empType->name}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Job Title</label>
                                <input type="text" class="form-control" id="type" placeholder="{{$user->job_title}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Join Date</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{ \Carbon\Carbon::parse($user->join_date)->format('d/m/Y')}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{$user->emergency_contact_name}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Emergency Contact No.</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{$user->emergency_contact_no}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Employee Groups</label>
                                <table class="table table-striped table-bordered table-sm">
                                    <tr>
                                        <th>Group No.</th>
                                        <th>Group Name</th>
                                        <th>Role</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>{{isset($empGroup->name) ? $empGroup->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup->group_leader_id))
                                            {{($empGroup->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>{{isset($empGroup2->name) ? $empGroup2->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup2->group_leader_id))
                                            {{($empGroup2->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>{{isset($empGroup3->name) ? $empGroup3->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup3->group_leader_id))
                                            {{($empGroup3->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>{{isset($empGroup4->name) ? $empGroup4->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup4->group_leader_id))
                                            {{($empGroup4->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>{{isset($empGroup5->name) ? $empGroup5->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup5->group_leader_id))
                                            {{($empGroup5->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                </form>
                <div class="float-sm-right"><span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                        title="Edit user profile and leave">
                        <a href="{{route('user_edit', $user->id)}}" class="btn btn-primary">Edit</a>
                    </span></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Approval Authorities</strong>
            </div>
            <div class="card-body">
                @if($empAuth === null)
                <strong>No record found</strong>
                <div class="float-sm-right mt-3"><button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#createAuthority">Create</button></div>
                <div class="modal fade" id="createAuthority" tabindex="-1" role="dialog"
                    aria-labelledby="createAuthorityTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Create Approval Authorities for
                                    {{$user->name}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @include('leaveauth.partials.form', ['action' => route('approval_auth_create', $user),
                                'user' => $user])
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <strong>Record found</strong>
                <table class="table table-bordered">
                    <tr>
                        <th>Level</th>
                        <th>Name</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>{{isset($empAuth->authority_1_id) ? $empAuth->authority_one->name:'NA'}}</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>{{isset($empAuth->authority_2_id) ? $empAuth->authority_two->name:'NA'}}</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>{{isset($empAuth->authority_3_id) ? $empAuth->authority_three->name:'NA'}}</td>
                    </tr>
                </table>
                <div class="float-sm-right mt-3"><button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#editAuthority">Edit</button></div>
                <div class="modal fade" id="editAuthority" tabindex="-1" role="dialog"
                    aria-labelledby="editAuthorityTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Edit Approval Authorities for
                                    {{$user->name}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @include('leaveauth.partials.form', ['action' => route('approval_auth_update',
                                $empAuth), 'empAuth' => $empAuth])
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>





    <!-- Leave Days Form -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Leave Record</strong>
		<div class="float-sm-right"><a href="{{route('apply_for', $user)}}"><button type="button" class="btn btn-primary">Add Leave Record</button></a></div>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr>
                            <th>Leave Name</th>
                            @foreach($leaveTypes as $lt)
                            @if($lt->name != "Replacement")
                            <td><strong>{{$lt->name}}</strong></td>
                            @endif
                            @endforeach
                        </tr>
                        <tr>
                            <th>Entitled</th>
                            @foreach($leaveEnt as $le)
                            @if($le->leave_type_id != '12')
                            <td class="table-primary">{{$le->no_of_days}}</td>
                            @endif
                            @endforeach
                        </tr>
                        <tr>
                            <th>Brought Forward
                                @if ($leaveEarn->count() == 0)<small><a href=""
                                        onclick="return alert('Please set this year\'s\ leave earnings before setting carry forward leaves')">Edit</a></small>
                                @else <small><a href="" data-toggle="modal"
                                        data-target="#setBroughtForward">Edit</a></small>
                                @endif
                            </th>
                            @foreach($broughtFwd as $bf)
                            @if($bf->leave_type_id == '1')
                            <td class="table-success">{{isset($bf->no_of_days) ? $bf->no_of_days:'NA'}}</td>
                            @elseif($bf->leave_type_id != '12')
                            <td class="table-secondary"></td>
                            @endif
                            @endforeach
                        </tr>
                        <tr>
                            <th>Earned <small><a href="" data-toggle="modal" data-target="#setEarnings">Edit</a></small>
                            </th>
                            @foreach($leaveEarn as $le)
                            @foreach($broughtFwd as $bf)
                            @if($le->leave_type_id == $bf->leave_type_id && $le->leave_type_id != '12')
                            <td class="table-success" data-toggle="tooltip"
                                title="{{$le->no_of_days - $bf->no_of_days}} (Earned) + {{$bf->no_of_days}} (Brought Forward)">
                                {{$le->no_of_days}}</td>
                            @endif
                            @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <th>Taken</th>
                            @foreach($leaveTak as $lt)
                            @if($lt->leave_type_id != '12')
                            <td class="table-danger">{{$lt->no_of_days}}</td>
                            @endif
                            @endforeach
                        </tr>
                        <tr>
                            <th>Replacement</th>
                            @foreach($leaveEarn as $le)
                            @if($le->leave_type_id == "12")
                            <td class="table-success">{{$le->no_of_days}}</td>
                            @endif
                            @endforeach
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                        </tr>
                        <tr>
                            <th>Burnt</th>
                            @foreach($leaveEnt as $le)
                            @if($le->leave_type_id != '12')
                            <td class="table-danger">0</td>
                            @endif
                            @endforeach
                        </tr>
                        <tr>
                            <th>Balance</th>
                            @foreach($leaveBal as $lb)
                            @if($lb->leave_type_id != '12')
                            <td class="table-primary">{{$lb->no_of_days}}</td>
                            @endif
                            @endforeach
                        </tr>
                    </tbody>
                </table>

                <!-- MODAL FOR LEAVE EARNINGS SETTINGS -->
                <div class="modal fade" id="setEarnings" tabindex="-1" role="dialog" aria-labelledby="setEarningsTitle"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Set Leave Earnings for
                                    {{$user->name}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @include('leave.partials.form', ['action' => route('earnings_set', $user), 'user' =>
                                $user, 'leave' => $leaveEarn])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL FOR BROUGHT FORWARD LEAVE SETTINGS -->
                <div class="modal fade" id="setBroughtForward" tabindex="-1" role="dialog"
                    aria-labelledby="setBroughtForward" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Set Brought Forward Leaves for
                                    {{$user->name}} <i class="fas fa-info-circle" data-toggle="tooltip"
                                        title="All fields must be filled out, even if the assigned days is 0"></i></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @include('leave.partials.form', ['action' => route('brought_fwd_set', $user), 'user' =>
                                $user, 'leave' => $broughtFwd])
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
