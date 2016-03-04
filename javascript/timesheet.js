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
    $( "#shift" ).combobox();
    
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
     
    $("input").keydown(function(event) {
        if (event.keyCode == 9) { 
            event.preventDefault();
            
            if ($(this).parent().prev().attr("name") == "phase") {
                $("[name=hours]").focus();
            }
            else if ($(this).parent().prev().attr("name") == "shift") {
                $("#job_num").focus()
            }
            else {
                var currentTabIndex = parseInt($(this).attr("tabindex"));
                var nextTabIndex = currentTabIndex + 1;
                
                if (nextTabIndex == 1) {
                    $('#shift').next().find('input').focus();
                }
                else if (nextTabIndex == 3) {
                    $('#phase').next().find('input').focus()
                }
                else {
                    var tabIndexString = "[tabindex="+nextTabIndex+"]"; 
                    var count = $(tabIndexString).length;
                    if (count == 0) {
                        $("[tabindex=1]").focus();
                    }
                    else {
                        $(tabIndexString).focus();
                    }
                }
            }
        } 
    });
     
  });