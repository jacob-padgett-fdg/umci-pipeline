function hideToolTip() {
    //code
    $( "#job_num" ).tooltip().mouseout();
    $( "#job_num" ).attr( "title", "" );
    $( "#job_num" ).tooltip();
}

function putPhaseInField(phase) {
    //code
    $('#phase').append($('<option/>', { 
            value: phase,
            text : phase 
        }));
    $('#phase').val( phase );
    $("#phase").next().find("input").val(phase);
}

function validatePhase(jobNumber, phase) {
    //code
    $.ajax({
        url: "phaseValidate.php?jobNumber=" + jobNumber + "&phase=" + phase,
        success: function(result) {
                if (result != 1) {
                    //code
                    $( "#error-message" ).dialog( "open" );
                }
                else
                {
                    $("#phase_value").val( phase );
                    $("#add_time").submit();
                }
            }      
        });
}

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

function populatePhaseListValue(jobNumber,phase) {
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
                putPhaseInField(phase);
            }      
        });
}

function validateForm() {
    //code
     
    var phase = $("#phase").next().find("input").val();
    
    var dashPosition = phase.indexOf("-");
    if (dashPosition > -1) {
        //code
        phase = phase.substring(0, dashPosition);
    }
    validatePhase($( "#job_num" ).val(), phase);
    
}

$(function() {
    $( "#phase" ).combobox();
    $( "#shift" ).combobox();
    $( "#job_num" ).tooltip();
    
    $( "#error-message" ).dialog({
      autoOpen: false,
      modal: true,
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
        }
      }
    });
    
    $( "#job_num" ).change(function() {
        populatePhaseList($(this).val());
    });
    
     $("#timesheet_phase_lookup").change(function() {
        $("#phase").val($(this).val().replace("-   -",""));
     });
     
     /*$( "#job_num" ).autocomplete({
      source: "jobNumberAutoComplete.php",
      select: function( event, ui ) {
        $("#job_num_hidden").val( ui.item.id );
        $("#job_description_hidden").val( ui.item.label );
        $("#job_num").val( ui.item.id );
        populatePhaseList( ui.item.id );
      }
    });*/
     
    $("input").keydown(function(event) {
        if (event.keyCode == 9) { 
            event.preventDefault();
            
            if ($(this).parent().prev().attr("name") == "phase") {
                if(event.shiftKey) {
                    $("#job_num").focus(); //Focus previous input
                 } else {
                    $("[name=hours]").focus();
                 }
            }
            else if ($(this).parent().prev().attr("name") == "shift") {
                if(event.shiftKey) {
                    $("[tabindex=7]").focus(); //Focus previous input
                 } else {
                    $("#job_num").focus();
                 }
            }
            else {
                var currentTabIndex = parseInt($(this).attr("tabindex"));
                var nextTabIndex = 1;
                
                if (event.shiftKey)
                {
                    if (currentTabIndex == 1)
                    {
                        nextTabIndex = 7;
                    }
                    else
                    {
                        nextTabIndex = currentTabIndex - 1;
                    }
                }
                else
                {
                    if (currentTabIndex < 7)
                    {
                        nextTabIndex = currentTabIndex + 1;
                    }    
                }
                
                
                
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