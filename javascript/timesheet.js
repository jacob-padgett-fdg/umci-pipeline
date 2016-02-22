function populatePhaseList(jobNumber) {
    $("#timesheet_phase_lookup").html("<option value=\"\">-- Phase Lookup --</option>");
    $.ajax({
        url: "getPhaseList.php?jobNumber=" + jobNumber,
        success: function(result) {
                // remove all options
                result = JSON.parse(result);
                $.each(result, function(i, item) {
                    $('#timesheet_phase_lookup').append($('<option/>', { 
                        value: item.Phase,
                        text : item.Phase + item.Description 
                    }));
                 });
            }      
        });
}

$(function() {
    $( "#job_num" ).change(function() {
            populatePhaseList($(this).val());
        });
    
     $("#timesheet_phase_lookup").change(function() {
        $("#phase").val($(this).val().replace("-   -",""));
     });
  });