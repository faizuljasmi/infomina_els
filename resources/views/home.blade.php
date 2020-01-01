@extends('adminlte::page')

@section('title', 'Infomina/ELS')

@section('content_header')
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="icon fa fa-check"></i>
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <h6 class="float-right">Hello, <strong>{{$user->name}}</strong> <span class="badge badge-info">{{$user->user_type}}</span></h6>
    <h1 class="m-0 text-dark">Dashboard</h1>
    <div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
    </div>
@stop

@section('content')

@if($user->password_changed == 'No')
<script type="text/javascript">
    $(window).on('load',function(){
        $('#exampleModalCenter').modal('show');
    });
</script>
@endif

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Welcome to your new account, <strong>{{$user->name}}</strong>!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <strong>First things first</strong> </br>
        Let's change your default password to your own unique password to secure your account!
      </div>
      <div class="modal-body">
      <strong>And,</strong> </br>
        <p>Don't forget to do the one-time setup of your emergency contact as well. You can do that at <strong>Edit My Profile</strong></p>
      </div>
      <div class="modal-footer">
        <a href="/change-password"><button type="button" class="btn btn-primary">Change Password</button><a>
      </div>
    </div>
  </div>
</div>
    
<section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <h5>Balance Overview:</h5>
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{isset($leaveBal[0]->no_of_days) ? $leaveBal[0]->no_of_days: '0'}}</h3>

                <p>Annual Leave</p>
              </div>
              <div class="icon">
              <i class="fas fa-calendar-day"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{isset($leaveBal[2]->no_of_days) ? $leaveBal[2]->no_of_days: '0'}}<sup style="font-size: 20px"></sup></h3>

                <p>Medical Leave</p>
              </div>
              <div class="icon">
              <i class="fas fa-notes-medical"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{isset($leaveBal[5]->no_of_days) ? $leaveBal[5]->no_of_days: '0'}}</h3>

                <p>Emergency Leave</p>
              </div>
              <div class="icon">
              <i class="fas fa-exclamation-circle"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <?php $rl = $user->taken_leaves()->where('leave_type_id',12)->first();?>
                <h3>{{isset($rl->no_of_days) ? $rl->no_of_days: '0'}}</h3>

                <p>Replacement Leave</p>
              </div>
              <div class="icon">
              <i class="fas fa-user-clock"></i>
              </div>
            </div>
          </div>
          
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-8 connectedSortable ui-sortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
                <div class="card-header bg-teal">
                <strong>Pending Applications</strong>          
                <button type="button" class="btn btn-box-tool float-right" data-toggle="collapse" href="#collapse-leave" aria-expanded="true" aria-controls="collapse-leave" id="heading-leave" class="d-block"><i class="fa fa-minus"></i>
                    <i class="fa fa-plus"></i>
                </button>           
            </div>
        <div id="collapse-leave" class="collapse show" aria-labelledby="heading-leave">

        <div class="card-body">
        <table class="table table-bordered">
        @if($pendLeaves->count() > 0)
          <thead>
            <tr>
              <th scope="col">No.</th>
              <th scope="col">Leave Type</th>
              <th scope="col">Duration</th>
              <th scope="col">From</th>
              <th scope="col">To</th>
              <th scope="col">Date Submitted</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            @php $count = ($pendLeaves->currentPage()-1) * $pendLeaves->perPage(); @endphp
              @foreach($pendLeaves as $la)
            <tr>
              <td>{{++$count}}</td>
              <td>{{$la->leaveType->name}}</td>
              <td>{{$la->total_days}} day(s)</td>
              <td>{{ \Carbon\Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YY')}}</td>
              <td>{{ \Carbon\Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YY')}}</td>
              <td>{{ \Carbon\Carbon::parse($la->created_at)->diffForHumans()}}</td>
              <td>
                @if($la->status == 'PENDING_1')
                <span class="badge badge-warning" data-toggle="tooltip" title="Your application is pending on lvl 1"><i class="far fa-clock"></i> Lvl 1</span>
                @elseif($la->status == 'PENDING_2')
                <span class="badge badge-warning" data-toggle="tooltip" title="Your application is pending on lvl 2"><i class="far fa-clock"></i> Lvl 2</span>
                @elseif($la->status == 'PENDING_3')
                <span class="badge badge-warning" data-toggle="tooltip" title="Your application is pending on lvl 3"><i class="far fa-clock"></i> Lvl 3</span>
                @elseif($la->status == 'APPROVED')
                <span class="badge badge-success" data-toggle="tooltip" title="Your application has been approved"><i class="far fa-check-circle"></i></span>
                @elseif($la->status == 'DENIED_1')
                <span class="badge badge-danger" data-toggle="tooltip" title="Your application has been denied by lvl 1"><i class="fas fa-ban"></i> Lvl 1</span>
                @elseif($la->status == 'DENIED_2')
                <span class="badge badge-danger" data-toggle="tooltip" title="Your application has been denied by lvl 2"><i class="fas fa-ban"></i> Lvl 2</span>
                @elseif($la->status == 'DENIED_3')
                <span class="badge badge-danger" data-toggle="tooltip" title="Your application has been denied by lvl 3"><i class="fas fa-ban"></i> Lvl 3</span>
                @elseif($la->status == 'CANCELLED')
                <span class="badge badge-secondary" data-toggle="tooltip" title="This application has been cancelled">Cancelled</span>
                @endif
              </td>
              <td><a href="{{route('view_application', $la->id)}}" class="btn btn-success btn-sm" data-toggle="tooltip" title="View leave application"><i class="fa fa-eye"></i></a></td>
            </tr>
            @endforeach
            {{$pendLeaves->links()}}
            @else
            <th>No Record Found</th>
            @endif

          </tbody>
        </table>
            </div>
            <!-- /.card -->

          </section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <section class="col-lg-4 connectedSortable ui-sortable">
            <!-- Calendar -->
             <!-- Vanilla Calendar -->
            <div class="card">
              <div class="card-header bg-teal">
                <strong>Calendar </strong><i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="For your reference, this calendar shows your applied & approved leaves."></i>
              </div>
            <div class="myCalendar vanilla-calendar" style="margin: 20px auto"></div>
            <div class="card-header bg-teal">
                <strong>Holidays</strong>
            </div>
            <div class="card-body">
              <table class = "table table-sm table-bordered">
                <tr>
                  <th>Holiday Name</th>
                  <th>From</th>
                  <th>To</th>
                </tr>
                
                @foreach($holsPaginated as $hl)
                <tr>
                  <td>{{$hl->name}}</td>
                  <td>{{ \Carbon\Carbon::parse($hl->date_from)->isoFormat('ddd, D MMM \'YY')}}</td>
                  <td>{{ \Carbon\Carbon::parse($hl->date_to)->isoFormat('ddd, D MMM \'YY')}}</td>
                  </tr>
                @endforeach
              </table>
              {{$holsPaginated->links()}}
            </div>
            </div>
            <!-- /.card -->
          </section>
          <!-- right col -->
        </div>
        <!-- Leave Days Form -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Applications History</strong>
            </div>
            <div class="card-body">
            <table class="table table-bordered">
        @if($leaveHist->count() > 0)
          <thead>
         
            <tr>
              <th scope="col">No.</th>
              <th scope="col">Leave Type</th>
              <th scope="col">Duration</th>
              <th scope="col">From</th>
              <th scope="col">To</th>
              <th scope="col">Date Submitted</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            @php $count = ($leaveHist->currentPage()-1) * $leaveHist->perPage(); @endphp
              @foreach($leaveHist as $la)
             
            <tr>
              <td>{{++$count}}</td>
              <td>{{$la->leaveType->name}}</td>
              <td>{{$la->total_days}} day(s)</td>
              <td>{{ \Carbon\Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YY')}}</td>
              <td>{{ \Carbon\Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YY')}}</td>
              <td>{{ \Carbon\Carbon::parse($la->created_at)->diffForHumans()}}</td>
              <td>
                @if($la->status == 'PENDING_1')
                <span class="badge badge-warning" data-toggle="tooltip" title="Your application is pending on lvl 1"><i class="far fa-clock"></i> Lvl 1</span>
                @elseif($la->status == 'PENDING_2')
                <span class="badge badge-warning" data-toggle="tooltip" title="Your application is pending on lvl 2"><i class="far fa-clock"></i> Lvl 2</span>
                @elseif($la->status == 'PENDING_3')
                <span class="badge badge-warning" data-toggle="tooltip" title="Your application is pending on lvl 3"><i class="far fa-clock"></i> Lvl 3</span>
                @elseif($la->status == 'APPROVED')
                <span class="badge badge-success" data-toggle="tooltip" title="Your application has been approved"><i class="far fa-check-circle"></i></span>
                @elseif($la->status == 'DENIED_1')
                <span class="badge badge-danger" data-toggle="tooltip" title="Your application has been denied by lvl 1"><i class="fas fa-ban"></i> Lvl 1</span>
                @elseif($la->status == 'DENIED_2')
                <span class="badge badge-danger" data-toggle="tooltip" title="Your application has been denied by lvl 2"><i class="fas fa-ban"></i> Lvl 2</span>
                @elseif($la->status == 'DENIED_3')
                <span class="badge badge-danger" data-toggle="tooltip" title="Your application has been denied by lvl 3"><i class="fas fa-ban"></i> Lvl 3</span>
                @elseif($la->status == 'CANCELLED')
                <span class="badge badge-secondary" data-toggle="tooltip" title="This application has been cancelled">Cancelled</span>
                @endif
              </td>
              <td><a href="{{route('view_application', $la->id)}}" class="btn btn-success btn-sm" data-toggle="tooltip" title="View leave application"><i class="fa fa-eye"></i></a></td>
            </tr>

            @endforeach
            {{$leaveHist->links()}}
            @else
            <th>No Record Found</th>
            @endif

          </tbody>
        </table>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Leave Record for 2020</strong>
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
                    @foreach($leaveEnts as $le)
                      @if($le->leave_type_id != '12')
                        <td class="table-primary">{{$le->no_of_days}}</td>
                      @endif
                    @endforeach
                    </tr>
                    <tr>
                    <th>Brought Forward 
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
                    <th>Earned <small></th>
                    @foreach($leaveEarns as $le)
                        @foreach($broughtFwd as $bf)
                            @if($le->leave_type_id == $bf->leave_type_id && $le->leave_type_id != '12')
                            <td class="table-success" data-toggle="tooltip" title="{{$le->no_of_days - $bf->no_of_days}} (Earned) + {{$bf->no_of_days}} (Brought Forward)">{{$le->no_of_days}}</td>
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
                    @foreach($leaveEarns as $le)
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
                    @foreach($leaveEnts as $le)
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

            </div>
        </div>
    </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>


<script>

$(document).ready(HolidayCreate);
function HolidayCreate() {

  var holiday_dates = {!! json_encode($all_dates, JSON_HEX_TAG) !!};
  var applied_dates = {!! json_encode($applied_dates, JSON_HEX_TAG) !!};
  var approved_dates = {!! json_encode($approved_dates, JSON_HEX_TAG) !!};

  let calendar = new VanillaCalendar({
      holiday : holiday_dates,
      applied : applied_dates,
      approved: approved_dates,
      selector: ".myCalendar",
      onSelect: (data, elem) => {
          // console.log(data, elem)
      }
  });

 
  const validation = {
    
    onchange : function(v, e, fc){
        console.log("onchange", v, e, fc);
        let name = fc.name;

        if(name == FC.date_from.name || name == FC.date_to.name){
          let error = validation.validateDateFromAndTo(name);
          if(error != null){
            alert(error);
            _form.set(fc, "");
            return;
          }
        }

        validation._dateFrom(name);
        validation._dateTo(name);

        validation._totalDay(name);
      
    },
    validateDateFromAndTo : function(name){
     
      let date_from = _form.get(FC.date_from);
      let date_to = _form.get(FC.date_to);


    
      if(
        (name == FC.date_from.name && calendar.isWeekend(date_from)) 
        || 
        (name == FC.date_to.name && calendar.isWeekend(date_to))
      ){
        return `Selected date is a WEEKEND. Please select another date.`;
      }
      if(
        (name == FC.date_from.name && calendar.isHoliday(date_from)) 
        || 
        (name == FC.date_to.name && calendar.isHoliday(date_to))
      ){
        return `Selected date is a HOLIDAY. Please select another date.`;
      }

      if(!_form.isEmpty(FC.date_from) && !_form.isEmpty(FC.date_to)){
        if(calendar.isDateSmaller(date_to, date_from)){
          if(name == FC.date_from.name){
            return "[Date From] cannot be bigger than [Date To]";
          } else if(name == FC.date_to.name){
            return "[Date To] cannot be smaller than [Date From]";
          }
        }
      }
      return null;
    },
    // #########################################
    // specific to field
    _dateFrom : function(name){
    },
    _dateTo : function(name){
     
  
    },
    _totalDay : function(name){
 

        if(!_form.isEmpty(FC.date_from) && !_form.isEmpty(FC.date_to)){
          let from = _form.get(FC.date_from);
          let to = _form.get(FC.date_to);
          let total = calendar.getTotalWorkingDay(from, to);
          console.log("total",total)
          _form.set(FC.total_days, total);
        } else{
          _form.set(FC.total_days, "");
        }
      }
  }

  let _form = null;
  let parent_id = "holiday-create";
  let FC = {
    holiday_name : {
      name : "holiday_name",
      type : MyFormType.TEXT
    },
    date_from : {
      name : "date_from",
      type : MyFormType.DATE
    },
    date_to : {
      name : "date_to",
      type : MyFormType.DATE
    },
    total_days : {
      name : "total_days",
      type : MyFormType.NUMBER
    },
    
  }

  _form = new MyForm({parent_id : parent_id, items : FC, onchange : validation.onchange});

  _form.required(FC.holiday_name);
  _form.required(FC.date_from);
  _form.required(FC.date_to);

  _form.disabled(FC.total_days);


}

</script>




  <style type="text/css">
    button[aria-expanded=true] .fa-plus {
    display: none;
    }
    button[aria-expanded=false] .fa-minus {
    display: none;
    }
  </style>
@stop
