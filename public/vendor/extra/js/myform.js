var MyFormType = {
    TEXT: "text",
    SELECT: "select",
    TEXTAREA: "textarea",
    NUMBER: "number",
    DATE_RANGE: "date_range",
    DATE: "date"
};

var MyForm = function({ parent_id, items }) {
    this.parent = $("#" + parent_id);
};

MyForm.prototype.elByName = function(name) {
    return this.parent.find(`[name=${name}]`);
};

MyForm.prototype.initDateRangePicker = function(listNames) {
    for (var i in listNames) {
        let el = listNames[i];
        el.daterangepicker();
    }
};

MyForm.prototype.submit = function() {
    console.log("submit");
};

//Initialize Select2 Elements
// $(".select2").select2();

// //Initialize Select2 Elements
// $(".select2bs4").select2({
//     theme: "bootstrap4"
// });

//Datemask dd/mm/yyyy
// $("#datemask").inputmask("dd/mm/yyyy", { placeholder: "dd/mm/yyyy" });
// //Datemask2 mm/dd/yyyy
// $("#datemask2").inputmask("mm/dd/yyyy", { placeholder: "mm/dd/yyyy" });
// //Money Euro
// $("[data-mask]").inputmask();

//Date range picker
// $("#reservation").daterangepicker();
// //Date range picker with time picker
// $("#reservationtime").daterangepicker({
//     timePicker: true,
//     timePickerIncrement: 30,
//     locale: {
//         format: "MM/DD/YYYY hh:mm A"
//     }
// });
// //Date range as a button
// $("#daterange-btn").daterangepicker(
//     {
//         ranges: {
//             Today: [moment(), moment()],
//             Yesterday: [
//                 moment().subtract(1, "days"),
//                 moment().subtract(1, "days")
//             ],
//             "Last 7 Days": [moment().subtract(6, "days"), moment()],
//             "Last 30 Days": [moment().subtract(29, "days"), moment()],
//             "This Month": [
//                 moment().startOf("month"),
//                 moment().endOf("month")
//             ],
//             "Last Month": [
//                 moment()
//                     .subtract(1, "month")
//                     .startOf("month"),
//                 moment()
//                     .subtract(1, "month")
//                     .endOf("month")
//             ]
//         },
//         startDate: moment().subtract(29, "days"),
//         endDate: moment()
//     },
//     function(start, end) {
//         $("#reportrange span").html(
//             start.format("MMMM D, YYYY") +
//                 " - " +
//                 end.format("MMMM D, YYYY")
//         );
//     }
// );

//Timepicker
// $("#timepicker").datetimepicker({
//     format: "LT"
// });

// //Bootstrap Duallistbox
// $(".duallistbox").bootstrapDualListbox();

// //Colorpicker
// $(".my-colorpicker1").colorpicker();
// //color picker with addon
// $(".my-colorpicker2").colorpicker();

// $(".my-colorpicker2").on("colorpickerChange", function(event) {
//     $(".my-colorpicker2 .fa-square").css("color", event.color.toString());
// });

// $("input[data-bootstrap-switch]").each(function() {
//     $(this).bootstrapSwitch("state", $(this).prop("checked"));
// });
