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
    <h4>Edit Profile for {{$user->name}}</h4>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Profile</strong>
            </div>
            <div class="card-body">
                @include('user.partials.form', ['action' => route('user_update', $user), 'user' => $user])
            </div>
        </div>
    </div> 

    <div class="col-md-4">
        <div class="card">
            <div class = "card-header bg-teal">
                <strong>Approval Authorities</strong>
            </div>
            <div class="card-body">
            @if($empAuth === null)
            <strong>No record found</strong>
            <div class="float-sm-right mt-3"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createAuthority">Create</button></div>
                <div class="modal fade" id="createAuthority" tabindex="-1" role="dialog" aria-labelledby="createAuthorityTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Create Approval Authorities for {{$user->name}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('leaveauth.partials.form', ['action' => route('approval_auth_create', $user), 'user' => $user])
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
                <div class="float-sm-right mt-3"><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAuthority">Edit</button></div>
                <div class="modal fade" id="editAuthority" tabindex="-1" role="dialog" aria-labelledby="editAuthorityTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Edit Approval Authorities for {{$user->name}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('leaveauth.partials.form', ['action' => route('approval_auth_update', $empAuth), 'empAuth' => $empAuth])
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
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                <tbody>
                    <tr>
                    <th>Leave Name</th>
                        @foreach($leaveTypes as $lt)
                        <td>{{$lt->name}}</td>
                        @endforeach
                    </tr>
                    <tr>
                    <th>Entitled</th>
                    @foreach($leaveEnt as $le)
                        <td>{{$le->no_of_days}}</td>
                        @endforeach
                    </tr>
                    <tr>
                    <th>Brought Forward 
                    @if ($leaveEarn->count() == 0)<small><a href="" onclick="return alert('Please set this years leave earnings before setting carry forward leaves')">Edit</a></small>
                    @else <small><a href="" data-toggle="modal" data-target="#setBroughtForward">Edit</a></small>
                    @endif
                    </th>
                    @foreach($broughtFwd as $bf)
                        <td>{{isset($bf->no_of_days) ? $bf->no_of_days:'NA'}}</td>
                        @endforeach
                    </tr>
                    <tr>
                    <th>Earned <small><a href="" data-toggle="modal" data-target="#setEarnings">Edit</a></small></th>
                    @foreach($leaveEarn as $le)
                        @foreach($broughtFwd as $bf)
                            @if($le->leave_type_id == $bf->leave_type_id)
                            <td data-toggle="tooltip" title="{{$le->no_of_days - $bf->no_of_days}} (Earned) + {{$bf->no_of_days}} (Brought Forward)">{{$le->no_of_days}}</td>
                            @endif
                        @endforeach
                    @endforeach
                    </tr>
                    <tr>
                    <th>Taken</th>
                    @foreach($leaveTak as $lt)
                        <td>{{$lt->no_of_days}}</td>
                        @endforeach
                    </tr>
                    <tr>
                    <th>Burnt</th>
                    @foreach($leaveEnt as $le)
                        <td>0</td>
                        @endforeach
                    </tr>
                    <tr>
                    <th>Balance</th>
                    @foreach($leaveBal as $lb)
                        <td>{{$lb->no_of_days}}</td>
                        @endforeach
                    </tr>
                 </tbody>
                </table>
                
                <!-- MODAL FOR LEAVE EARNINGS SETTINGS -->
                <div class="modal fade" id="setEarnings" tabindex="-1" role="dialog" aria-labelledby="setEarningsTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Set Leave Earnings for {{$user->name}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @include('leave.partials.form', ['action' => route('earnings_set', $user), 'user' => $user, 'leave' => $leaveEarn])
                        </div>
                    </div>
                </div>
                </div>

                <!-- MODAL FOR BROUGHT FORWARD LEAVE SETTINGS -->
                <div class="modal fade" id="setBroughtForward" tabindex="-1" role="dialog" aria-labelledby="setBroughtForward" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Set Brought Forward Leaves for {{$user->name}} <i class="fas fa-info-circle" data-toggle="tooltip" title="All fields must be filled out, even if the assigned days is 0"></i></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @include('leave.partials.form', ['action' => route('brought_fwd_set', $user), 'user' => $user, 'leave' => $broughtFwd])
                        </div>
                    </div>
                </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
