@extends('adminlte::page')

@section('content')
    <h4>Profile for {{$user->name}}</h4>

<div class="row">
    <div class="col-md-12">
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
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="{{$user->email}}">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="level">User Level</label>
                        <input type="text" class="form-control" id="level" placeholder="{{$user->user_type}}">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="type">Employee Type</label>
                        <input type="text" class="form-control" id="type" placeholder="{{$empType->name}}">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="type">Employee Group</label>
                        <input type="text" class="form-control" id="type" placeholder="{{$empGroup->name}}">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="type">Authority One</label>
                        <input type="text" class="form-control" id="type" placeholder="{{$empAuth->authority_1_id}}">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="type">Job Title</label>
                        <input type="text" class="form-control" id="type" placeholder="{{$user->job_title}}">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="type">Join Date</label>
                        <input type="text" class="form-control" id="type" placeholder="{{$user->join_date}}">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="type">Emergency Contact Name</label>
                        <input type="text" class="form-control" id="type" placeholder="{{$user->emergency_contact_name}}">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="type">Emergency Contact No.</label>
                        <input type="text" class="form-control" id="type" placeholder="{{$user->emergency_contact_no}}">
                        </div>
                    </div>
                    </fieldset>
                </form>
                <div class="float-sm-right"><span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Edit user profile and leave">
                                <a href="{{route('user_edit', $user->id)}}" class="btn btn-info btn-sm">Edit</a>
                            </span></div>
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
                    <th>Brought Forward</th>
                    @foreach($leaveEnt as $le)
                        <td>0</td>
                        @endforeach
                    </tr>
                    <tr>
                    <th>Earned</th>
                    @foreach($leaveEnt as $le)
                        <td>0</td>
                        @endforeach
                    </tr>
                    <tr>
                    <th>Taken</th>
                    @foreach($leaveEnt as $le)
                        <td>0</td>
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
                    @foreach($leaveEnt as $le)
                        <td>0</td>
                        @endforeach
                    </tr>
                 </tbody>
                </table>
                <div class="float-sm-right mt-3"><button type="button" class="btn btn-primary" >Edit</button></div>
            </div>
        </div>
    </div>
</div>
@endsection
