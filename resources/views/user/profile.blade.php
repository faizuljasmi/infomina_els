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
                        <label for="type">Job Title</label>
                        <input type="text" class="form-control" id="type" placeholder="Software Engineer">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="type">Join Date</label>
                        <input type="text" class="form-control" id="type" placeholder="2 Dec 2019">
                        </div>
                    </div>
                    </fieldset>
                </form>
                <div class="float-sm-right"><button type="button" class="btn btn-primary" >Edit</button></div>
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
