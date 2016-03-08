function hideToolTip() {
    //code
    $( "#job_num" ).tooltip().mouseout();
    $( "#job_num" ).attr( "title", "" );
    $( "#job_num" ).tooltip();
}

function putPhaseInField(phase) {
    //code
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
       
        $.ajax({
                url: "jobNumberValidate.php?jobNumber=" + $(this).val(),
                success: function(result) {
                     $( "#job_num" ).tooltip();
                    // remove all options
                    result = JSON.parse(result);
                    if (result.job == false) {
                        //code
                        $( "#job_num" ).attr( "title", $("#job_num").val() + " job number does not exist." );
                        $( "#job_num" ).tooltip().mouseover();
                        $( "#job_num" ).val("");
                        $( "#job_num" ).focus();
                        window.setTimeout(hideToolTip,4000);
                    }
                    else if (result.open == false) {
                        //code
                        $( "#job_num" ).attr( "title", $("#job_num").val() + " job is closed." );
                        $( "#job_num" ).tooltip().mouseover();
                        $( "#job_num" ).val("");
                        $( "#job_num" ).focus();
                        window.setTimeout(hideToolTip,4000);
                    }
                }  
            });
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