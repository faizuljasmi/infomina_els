@extends('adminlte::page')
@section('content')





<section id="leaveapp-create">
  <h4>View Leave Application</h4>
  @cannot('view', $leaveApp)
    <div class="alert alert-danger" role="alert">
        You don't have permission to view this application
        <a href ="/home"><button type="button" class="btn btn-info btn-sm">Back</button></a>
    </div>
  @endcannot
  @can('view', $leaveApp)
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Left Col -->
        <section class="col-lg-6 connectedSortable ui-sortable">
       
          <form method="POST" action="{{route('leaveapp_store')}}">
          @csrf
            <!-- Application Form -->
            <div class="card card-primary">
              <div class="card-header bg-teal">
                <strong>Application Details 
                    @if($leaveApp->user_id == auth()->user()->id)
                    <button type="submit" class="btn btn-danger btn-sm float-right" data-toggle="tooltip" title="Delete. Can only be done while on pending lvl 1"><i class="fa fa-trash-alt"></i></i></button><button type="submit" class="btn btn-primary btn-sm float-right mr-1" data-toggle="tooltip" title="Edit leave application"><i class="fa fa-pencil-alt"></i></button>
                    @else
                    <a href="{{route('deny_application', $leaveApp->id)}}" class="btn btn-danger btn-sm float-right" data-toggle="tooltip" title="Deny Application">Deny</a>
                    <a href="{{route('approve_application', $leaveApp->id)}}" class="btn btn-success btn-sm float-right mr-1" data-toggle="tooltip" title="Approve Application">Approve</a>
                    @endif
                </strong>
              </div>
              <div class="card-body">
              <fieldset disabled>
                  <!-- Applicants Name -->
                <div class="form-group">
                  <label>Applicant Name</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                      <i class="fas fa-user"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" id="type" placeholder="{{$leaveApp->user->name}}">
                  </div>
                </div>
                <!-- Leave Type -->
                <div class="form-group">
                  <label>Leave Type</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-star"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" id="type" placeholder="{{$leaveApp->leaveType->name}}">
                  </div>
                </div>

                <!-- Leave Variation -->
                <div class="form-group">
                  <label>Apply For</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-clock"></i>
                      </span>
                    </div>
                    <select class="form-control" name="apply_for">
                      <option value="full-day">Full Day</option>
                      <option value="half-day-am">Half Day AM</option>
                      <option value="half-day-pm">Half Day PM</option>
                    </select>
                  </div>
                </div>


                <!-- Date From -->
                <div class="form-group">
                  <label>Date From</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-day"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" id="type" placeholder="{{$leaveApp->date_from}}">
                  </div>
                </div>

                <!-- Date From -->
                <div class="form-group">
                  <label>Date To</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-day"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" id="type" placeholder="{{$leaveApp->date_to}}">
                  </div>
                </div>

                <!-- Total Days -->
                <div class="form-group">
                  <label>Total Days</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-check"></i>
                      </span>
                    </div>
                    <input type="number" class="form-control" id="type" placeholder="{{$leaveApp->total_days}}">
                  </div>
                </div>

                <!-- Date Resume -->
                <div class="form-group">
                  <label>Date Resume</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="number" class="form-control" id="type" placeholder="{{$leaveApp->date_resume}}">
                  </div>
                </div>


                <!-- Reason -->
                <div class="form-group">
                  <label>Reason</label>
                  <input type="number" class="form-control" id="type" placeholder="{{$leaveApp->reason}}">
                </div>

                <!-- File Attachment -->
                <div class="form-group">
                  <label>Attachment</label>
                  <div class="input-group">
                    <a href="{{$leaveApp->attachment_url}}" target="_blank">View Attachment</a>
                  </div>
                </div>

                <!-- Relief Personel -->
                <div class="form-group">
                  <label>Relief Personel</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                      <i class="fas fa-user-shield"></i>
                      </span>
                    </div>
                    <select class="form-control" name="relief_personnel_id">
                      <option selected value="{{$leaveApp->relief_personnel_id}}">{{$leaveApp->relief_personnel->name}}</option>
                    </select>

                  </div>
                </div>

                <!-- Emergency Contact Name-->
                <div class="form-group">
                  <label>Emergency Contact Name</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-user"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control float-right" name="emergency_contact_name" value="{{$user->emergency_contact_name}}">
                  </div>
                </div>

                <!-- Emergency Contact No -->
                <div class="form-group">
                  <label>Emergency Contact No</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-phone"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control float-right" name="emergency_contact_no" value="{{$user->emergency_contact_no}}">
                  </div>
                </div>
                <fieldset>
                <!-- $leaveAuth->authority_1_id -->
                <input style="display:none;" type="text" name="approver_id_1" value="{{$leaveAuth->authority_1_id}}" />
                <input style="display:none;" type="text" name="approver_id_2" value="{{$leaveAuth->authority_2_id}}" />
                <input style="display:none;" type="text" name="approver_id_3" value="{{$leaveAuth->authority_3_id}}" />     
              </div>
            </div>
          </form>
        </section>

        <!-- Right Col -->
        <section class="col-lg-5 connectedSortable ui-sortable">

          <div class="row">
            <div class="col-lg-12 connectedSortable ui-sortable">
              <!-- Vanilla Calendar -->
              <div class="card">
                <div class="card-header bg-teal">
                  <strong>Calendar</strong>
                </div>
                <div class="myCalendar vanilla-calendar" style="margin: 20px auto"></div>
              </div>
            </div>
            <div class="col-lg-12 connectedSortable ui-sortable">
                            <!-- Approval Authorities -->
                            <div class="card">
                <div class="card-header bg-teal">
                  <strong>Approval Authorities</strong>
                </div>
                <div id="collapse-leave" class="collapse show" aria-labelledby="heading-leave">
                  <div class="card-body">
                    <table class="table table-bordered">
                      <tr>
                        <th>Level</th>
                        <th>Name</th>
                        <th>Status</th>
                      </tr>
                      <tr>
                        <td>1</td>
                        <td>{{isset($leaveAuth->authority_1_id) ? $leaveAuth->authority_one->name:'NA'}}</td>
                        <td>
                            @if(!isset($leaveAuth->authority_1_id))
                            NA
                            @elseif($leaveApp->status == 'PENDING_1')
                            <span class="badge badge-warning" ><i class="far fa-clock"></i></span>
                            @elseif($leaveApp->status == 'DENIED_1' )
                            <span class="badge badge-danger" ><i class="fas fa-ban"></i></span>
                            @else
                            <span class="badge badge-success" ><i class="far fa-check-circle"></i></span>
                            @endif
                        </td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>{{isset($leaveAuth->authority_2_id) ? $leaveAuth->authority_two->name:'NA'}}</td>
                        <td>
                            @if(!isset($leaveAuth->authority_2_id))
                            NA
                            @elseif($leaveApp->status == 'PENDING_1')
                            <span class="badge badge-warning" ><i class="far fa-clock"></i></span>
                            @elseif($leaveApp->status == 'PENDING_2')
                            <span class="badge badge-warning" ><i class="far fa-clock"></i></span>
                            @elseif($leaveApp->status == 'DENIED_1' )
                            <span class="badge badge-warning" ><i class="far fa-clock"></i></span>
                            @elseif($leaveApp->status == 'DENIED_2' )
                            <span class="badge badge-danger" ><i class="fas fa-ban"></i></span>
                            @else
                            <span class="badge badge-success" ><i class="far fa-check-circle"></i></span>
                            @endif
                        </td>
                      </tr>
                      <tr>
                        <td>3</td>
                        <td>{{isset($leaveAuth->authority_3_id) ? $leaveAuth->authority_three->name:'NA'}}</td>
                        <td>
                            @if(!isset($leaveAuth->authority_3_id))
                            NA
                            @elseif($leaveApp->status == 'PENDING_1')
                            <span class="badge badge-warning" ><i class="far fa-clock"></i></span>
                            @elseif($leaveApp->status == 'PENDING_2')
                            <span class="badge badge-warning" ><i class="far fa-clock"></i></span>
                            @elseif($leaveApp->status == 'PENDING_3')
                            <span class="badge badge-warning" ><i class="far fa-clock"></i></span>
                            @elseif($leaveApp->status == 'DENIED_1' )
                            <span class="badge badge-warning" ><i class="far fa-clock"></i></span>
                            @elseif($leaveApp->status == 'DENIED_2' )
                            <span class="badge badge-danger" ><i class="fas fa-ban"></i></span>
                            @elseif($leaveApp->status == 'DENIED_3' )
                            <span class="badge badge-danger" ><i class="fas fa-ban"></i></span>
                            @else
                            <span class="badge badge-success" ><i class="far fa-check-circle"></i></span>
                            @endif
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
            </div>
            
            <div class="col-lg-6 connectedSortable ui-sortable">
              <!-- Leaves Balance -->
              <div class="card">
                <div class="card-header bg-teal">
                  <strong>Leaves Balance</strong>
                </div>
                <div id="collapse-leave" class="collapse show" aria-labelledby="heading-leave">
                  <div class="card-body">
                    <table class="table table-bordered">
                      @foreach($leaveType as $lt)
                      <tr>
                        <th>{{$lt->name}}</th>
                        <td>-</td>
                      </tr>
                      @endforeach
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>

  </section>
  @endcan

  <!-- Empty Col -->
  <section class="col-lg-1 connectedSortable ui-sortable">
  </section>

  </div>

  </div>
</section>

<script>
  $(document).ready(MainLeaveApplicationCreate);

  function MainLeaveApplicationCreate() {
  
    let calendar = new VanillaCalendar({
        applied : ['20191203'],
        holiday: [
          "20191204","20191205","20191206"
        ],
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
          validation._dateResume(name);
        
      },
      isHalfDayAm : function(){
        return _form.get(FC.apply_for) == "half-day-am";
      },
      isHalfDayPm : function(){
        return _form.get(FC.apply_for) == "half-day-pm";
      },
      isFullDay : function(){
        return _form.get(FC.apply_for) == "full-day";
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
        if(validation.isHalfDayAm() || validation.isHalfDayPm()){
          _form.disabled(FC.date_to);
          _form.copy(FC.date_from, FC.date_to);
        } else if(validation.isFullDay()){
          _form.required(FC.date_to);
          if(name == FC.apply_for.name){
            _form.set(FC.date_to, "");
          }
        }
      },
      _dateResume : function(name){
        if(!_form.isEmpty(FC.date_to)){
          let dateTo = _form.get(FC.date_to);
          let nextWorkingDay = calendar.getNextWorkingDay(dateTo);
          nextWorkingDay = calendar.getDateInput(nextWorkingDay);
          _form.set(FC.date_resume, nextWorkingDay)
        }
      },
      _totalDay : function(name){
        if(validation.isHalfDayAm() || validation.isHalfDayPm()){
          _form.set(FC.total_days, 0.5);
        }else if(validation.isFullDay()){
          if(name == FC.apply_for.name){
            _form.set(FC.total_days, "");
          }

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
    }

    let _form = null;
    let parent_id = "leaveapp-create";
    let FC = {
      leave_type_id : {
        name : "leave_type_id",
        type : MyFormType.SELECT
      },
      apply_for : {
        name : "apply_for",
        type : MyFormType.SELECT
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
      date_resume : {
        name : "date_resume",
        type : MyFormType.DATE
      },
      reason : {
        name : "reason",
        type : MyFormType.TEXTAREA
      },
      attachment : {
        name : "attachment",
        type : MyFormType.FILE
      },
      relief_personnel_id : {
        name : "relief_personnel_id",
        type : MyFormType.SELECT
      },
      emergency_contact_no  : {
        name : "emergency_contact_no",
        type : MyFormType.TEXT
      },
      emergency_contact_name  : {
        name : "emergency_contact_name",
        type : MyFormType.TEXT
      },
      // ####################################
      // ## data generated from controller ##
      // user_id
      // status 
      // approver_id_1
      // approver_id_2
      // approver_id_3
    }

    _form = new MyForm({parent_id : parent_id, items : FC, onchange : validation.onchange});

    _form.required(FC.leave_type_id);
    _form.required(FC.date_from);
    _form.required(FC.date_to);

    _form.disabled(FC.date_resume);
    _form.disabled(FC.total_days);
  

  }

</script>

</section>


@endsection
@section('css')
<link rel="stylesheet" href="{{asset('')}}" @endsection