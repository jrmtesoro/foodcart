$(document).ajaxStart(function(e){
    $("#loading_screen").removeClass("d-none");
});
$(document).ajaxStop(function(e){
    $("#loading_screen").addClass("d-none");
});
