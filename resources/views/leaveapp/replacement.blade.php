@extends('adminlte::page')

@section('content_header')
@if(session()->has('message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="icon fa fa-check"></i>
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <h1 class="m-0 text-dark">Claim Replacement Leave</h1>
@stop

@section('content')

<section id="leaveapp-create">
  <section class="content">
    <div class="container-fluid">

      <div class="row">
        <!-- Left Col -->
        <section class="col-lg-6 connectedSortable ui-sortable">
          <form class="needs-validation" novalidate method="POST" action="{{route('leaveapp_store')}}" enctype="multipart/form-data">
          @csrf
            <!-- Application Form -->
            <div class="card card-primary">
              <div class="card-header bg-teal">
                <strong>Application Form</strong>
              </div>
              <div class="card-body">

                <!-- Working Hour -->
                <div class="form-group" style="display:none;">
                  <div class="input-group">
                    <input type="text" class="form-control float-right" id="working_hour" value="{{$user->working_hour_id}}">
                  </div>
                </div>

                <!-- Leave Type -->
                <div class="form-group" style="display:none;">
                  <div class="input-group">
                    <input type="text" class="form-control float-right" name="leave_type_id" value="12">
                  </div>
                </div>

                <!-- Leave Variation -->
                <div class="form-group" style="display:none;">
                  <div class="input-group">
                    <input type="text" class="form-control float-right" name="apply_for" value="full-day">
                  </div>
                </div>

                <!-- Date From -->
                <div class="form-group">
                  <label>Start Date & Time <font color="red">*</font></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-day"></i>
                      </span>
                    </div>
                    <input type="datetime-local" class="form-control float-right" name="date_from" id="FromDate">
                  </div>
                </div>

                <!-- Date To -->
                <div class="form-group">
                  <label>End Date & Time <font color="red">*</font></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-day"></i>
                      </span>
                    </div>
                    <input type="datetime-local" class="form-control float-right" name="date_to" id="ToDate">
                  </div>
                </div>

                <!-- Total Days -->
                <div class="form-group">
                  <label>Total Leaves Entitled</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-check"></i>
                      </span>
                    </div>
                    <input type="number" class="form-control float-right" name="total_days">
                  </div>
                </div>

                <!-- Reason -->
                <div class="form-group">
                  <label>Replacement Reason <font color="red">*</font></label>
                  <textarea class="form-control" rows="5" name="reason" required></textarea>
                  <h6 class="float-right" id="count_reason"></h6>
                </div>

                <!-- File Attachment -->
                <div class="form-group">
                  <label>Attachment <small class="text-muted">Optional. Format: jpg,jpeg,png,pdf. Max size: 2MB</small></label>
                  <div class="input-group">
                    <input type="file" class="form-control-file" name="attachment" id="attachment">
                    <span class="text-danger"> {{ $errors->first('attachment') }}</span>
                  </div>
                </div>

                <!-- Approval Authority -->
                <div class="form-group">
                  <label>Approval Authority <font color="red">*</font></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-user"></i>
                      </span>
                    </div>
                    <select class="form-control" name="approver_id_1" required>
                      <option selected value="">Choose Person</option>
                      @foreach($leaveAuth as $la)
                      <option value="{{$la->id}}">{{$la->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <input style="display:none;" type="text" class="form-control float-right" name="emergency_contact_name" value="{{isset($user->emergency_contact_name) ? $user->emergency_contact_name:'NA'}}">
                <input style="display:none;" type="text" class="form-control float-right" name="emergency_contact_no" value="{{isset($user->emergency_contact_no) ? $user->emergency_contact_no :'NA'}}">
                <!-- CHANGE TO HR ADMIN ID -->
                <input style="display:none;" type="text" name="approver_id_2" value="111" />
                <input style="display:none;" type="text" name="relief_personnel_id" value="" />
                <input style="display:none;" type="text" name="replacement_action" value="Claim" />

                <!-- Submit Button -->
                <button type="submit" class="btn btn-success float-right">Submit</button>
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
          </div>
      </div>
      <div id="loading">
        <div id="loading-image">
            <figure>
                <img src="{{url('images/loader.gif')}}" alt="Loading..." />
                <figcaption>Submitting your application...</figcaption>
            </figure>
        </div>
    </div>
  </section>
</section>

<script>
  $(document).ready(MainLeaveApplicationCreate);

  $("#FromDate").change(function() {
    var from = $("#FromDate").val();
      $("#ToDate").val("");
      $("#ToDate").attr({
        "min" : from
      });
  });
  var text_max = 5;
  $('#count_reason').html(text_max + ' remaining');

  $('#reason').keyup(function() {
    var text_length = $('#reason').val().length;
    var text_remaining = text_max - text_length;
      if(text_remaining < 0){
          $('#count_reason').html('Looks good!');
      }
      else{
    $('#count_reason').html(text_remaining + ' remaining');
      }
  });

  (function() {
    'use strict';
    window.addEventListener('load', function() {
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.getElementsByClassName('needs-validation');
      var spinner = $('#loading');
      // Loop over them and prevent submission
      var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          else{
              spinner.show();
          }
          form.classList.add('was-validated');
        }, false);
      });
    }, false);
  })();

  function MainLeaveApplicationCreate() {

    if(navigator.userAgent.indexOf("Chrome") == -1 ) {
      alert('Kindly claim your replacement leave by using Google Chrome browser.');
      window.history.back();
    }

    // Default working hours for employees based on workingHourGroup ID
    function setWorkingHours(workingHourGroup) {
      switch(workingHourGroup) {
        case '1':
          workStartTime = 730;
          workEndTime = 1630;
          break;
        case '2':
          workStartTime = 800;
          workEndTime = 1700;
          break;
        case '3':
          workStartTime = 830;
          workEndTime = 1730;
          break;
        case '4':
          workStartTime = 900;
          workEndTime = 1800;
          break;
      }
    }

    var workingHourGroup = $('#working_hour').val();
    let workStartTime = '';
    let workEndTime = '';

    setWorkingHours(workingHourGroup);

    console.log(workStartTime, workEndTime, 'workTime')

    var dates = {!! json_encode($all_dates, JSON_HEX_TAG) !!};
    var myapplications= {!! json_encode($myApplication, JSON_HEX_TAG) !!};
    var applied = {!! json_encode($applied_dates, JSON_HEX_TAG) !!};
    var approved = {!! json_encode($approved_dates, JSON_HEX_TAG) !!};

    let calendar = new VanillaCalendar({
      holiday: dates,
      applied: applied,
      approved: approved,
      selector: ".myCalendar",
      onSelect: (data, elem) => {
          // console.log(data, elem)
      }
    });

    const validation = {
      onchange : function(v, e, fc){
        console.log("onchange", "v", v, "e", e, "fc" ,fc);
        console.log("FC.date_from.name", FC);
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
        validation._totalDayHours();
      },
      validateDateFromAndTo : function(name){
        let from = _form.get(FC.date_from);
        let to = _form.get(FC.date_to);

        // Format to date only
        let dateFrom = from.substring(0,10);
        let dateTo = to.substring(0,10);

        // To ensure the claim submitted within 7 working days from the day of event
        let prev7 = calendar.getPrevWeekWorkingDay(calendar.today());
        prev7 = calendar.getDateDb(prev7);
        if(calendar.isDateSmaller(dateFrom, prev7) || calendar.isDateEqual(dateFrom, prev7)){
          return "Attention: Claim must be submitted within 7 working days from the day of event.";
        }

        // Check existing applications
        for (index = 0; index < myapplications.length; index++) {
            if( myapplications[index] == calendar.getDateDb(dateFrom) || myapplications[index] == calendar.getDateDb(dateTo)){
                return "You already have a Pending/Approved application during this date.";
            }
        }

        // To get OT hours
        if(!_form.isEmpty(FC.date_from) && !_form.isEmpty(FC.date_to)) {
          let timeRawF = from.substring(11,16);
          let timeF =  timeRawF.replace(':', '');
          let timeToIntF = parseInt(timeF);

          let timeRawT = to.substring(11,16);
          let timeT =  timeRawT.replace(':', '');
          let timeToIntT = parseInt(timeT);

          let totalHours = calendar.getTotalHours(from, to);
          let totalDays = calendar.getTotalDays(dateFrom, dateTo);

          let totalRLEarned = 0;

          function getMinuteDiffs(A, B) {
            let totalMinutesA = Math.floor(A / 100) * 60 + (A % 100);
            let totalMinutesB = Math.floor(B / 100) * 60 + (B % 100);

            return totalMinutesB - totalMinutesA;
          }

          function getHours(minutes) {
            return Math.floor(minutes / 60);
          }

          function getDayCalc(day) {
            // Only applicaple when employee selects more than one day to claim RL
            let minuteDiffs = '';
            let hours = '';

            if (day == 'FIRST') {
              // Calculate hours worked for the first day
              // End time is auto set to 11.59pm
              minuteDiffs = getMinuteDiffs(timeToIntF, 2359);
              hours = getHours(minuteDiffs + 1);
            } else if (day == 'LAST') {
              // Calculate hours worked for the last day
              // Start time is auto set to 12.00am
              minuteDiffs = getMinuteDiffs(0, timeToIntT);
              hours = getHours(minuteDiffs);
            }

            return hours;
          }

          function setClaimDays(day, hours) {
            // Set total RL earned based on extra hours worked
            if (day == 'WORKING') {
              if (hours >= 4 && hours < 6) {
                totalRLEarned = totalRLEarned + 0.5;
              }
  
              if (hours >= 6) {
                totalRLEarned = totalRLEarned + 1;
              }
            } else if (day == 'HOLIDAY') {
              if (hours >= 1 && hours < 5) {
                totalRLEarned = totalRLEarned + 0.5;
              }

              if (hours >= 5) {
                totalRLEarned = totalRLEarned + 1;
              }
            }
          }

          if (totalDays > 1) {
            let date = dateFrom;
            let i = 0;

            while (i < totalDays) {
              let isWorkingDay = calendar.isWorkingDay(date);
            
              if (i == 0) {
                // First day of claim
                let hours = getDayCalc('FIRST');
                if (isWorkingDay) {
                  setClaimDays('WORKING', hours);
                } else {
                  setClaimDays('HOLIDAY', hours);
                }
              } else if (i == totalDays - 1) {
                // Last day of claim
                let hours = getDayCalc('LAST');
                if (isWorkingDay) {
                  setClaimDays('WORKING', hours);
                } else {
                  setClaimDays('HOLIDAY', hours);
                }
              } else {
                // Assume days in between , the employee worked for the whole day
                totalRLEarned = totalRLEarned + 1;
              }
              date = calendar.nextDayStr(date);
              i++;
            }

            if (calendar.isWorkingDay(dateFrom) && totalRLEarned == 0) {
              _form.set(FC.total_days, "");
              return "You need to work at least 4 hours to claim a replacement leave. (Working Day)"
            }

            if (!calendar.isWorkingDay(dateFrom) && totalRLEarned == 0) {
              _form.set(FC.total_days, "");
              return "You need to work at least 1 hour to claim a replacement leave. (Weekend/Public Holiday)"
            } 
          } else {
            // Claim in made within the same day
            let isWorkingDay = calendar.isWorkingDay(dateFrom);
            let minutes = 0;

            if (workStartTime && workEndTime) {
              // If have working hours
              // Check start time with default working hours
              if (timeToIntF < workStartTime) {
                minutes = minutes + getMinuteDiffs(timeToIntF, workStartTime)
              }
              // Check end time with default working hours
              if (timeToIntT > workEndTime) {
                minutes = minutes + getMinuteDiffs(workEndTime, timeToIntT)
              }
            } else {
              minutes = minutes + getMinuteDiffs(timeToIntF, timeToIntT)
            }
            
            let hours = getHours(minutes);

            if (isWorkingDay) {
              if (hours < 4) {
                _form.set(FC.total_days, "");
                return "You need to work at least 4 hours to claim a replacement leave. (Working Day)"
              }
              setClaimDays('WORKING', hours);
            } else {
              if (hours < 1) {
                _form.set(FC.total_days, "");
                return "You need to work at least 1 hour to claim a replacement leave. (Weekend/Public Holiday)"
              }
              setClaimDays('HOLIDAY', hours);
            }
          }
          
          _form.set(FC.total_days, totalRLEarned);
          console.log(totalRLEarned);

          // Group 1 = 7.30am - 4.30pm
          // Group 2 = 8.00am - 5.00pm
          // Group 3 = 8.30am - 5.30pm
          // Group 4 = 9.00am - 6.00pm
          // Group 5 = Open
        }

        if(calendar.isDateBigger(dateFrom,calendar.today())){
            return "Attention: Replacement leave cannot be claimed in advance."
        }

        return null;
      },
      // #########################################
      // specific to field
      _dateFrom : function(name){
      },
      _dateTo : function(name){
        _form.required(FC.date_to);
        if(name == FC.apply_for.name){
          _form.set(FC.date_to, "");
        }
      },
      _totalDayHours : function(){
        if(_form.isEmpty(FC.date_from) && _form.isEmpty(FC.date_to)){
          _form.set(FC.total_hours, "");
          _form.set(FC.total_days, "");
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
    _form.disabled(FC.total_hours);
  }

</script>

<style type="text/css">
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

    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        /* bring your own prefixes */
        transform: translate(-50%, -50%);
        z-index: 100;
    }
</style>

</section>


@endsection
