function populatePhaseList(jobNumber) {
    $("#phase").html("");
    $.ajax({
        url: "getPhaseList.php?jobNumber=" + jobNumber,
        success: function(result) {
                // remove all options
                result = JSON.parse(result);
                $.each(result, function(i, item) {
                    $('#phase').append($('<option/>', { 
                        value: item.Phase,
                        text : item.Phase + item.Description 
                    }));
                 });
            }      
        });
}

$(function() {
    $( "#phase" ).combobox();
    
    $( "#job_num" ).change(function() {
            populatePhaseList($(this).val());
        });
    
     $("#timesheet_phase_lookup").change(function() {
        $("#phase").val($(this).val().replace("-   -",""));
     });
     
     $( "#job_num" ).autocomplete({
      source: "jobNumberAutoComplete.php",
      select: function( event, ui ) {
        $("#job_num_hidden").val( ui.item.id );
        $("#job_description_hidden").val( ui.item.label );
        $("#job_num").val( ui.item.id );
        populatePhaseList( ui.item.id );
      }
    });
  });