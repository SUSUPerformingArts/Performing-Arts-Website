$(function(){

	var datePickerOptions = {
        stepping : 5,
        showClear : true,
        allowInputToggle : true,
        useCurrent : false,
        //focusOnShow : false,
        //showClose : true,
        format : "DD/MM/YYYY h:mma"
    };

	// Init the datepickers
    var lastDateUsed,
        defaultDate =  function(){
            var t = $(this);
            if(!t.data("DateTimePicker").date()){
                if(lastDateUsed){
                    $(this).data("DateTimePicker").date(lastDateUsed);
                }else{
                    $(this).data("DateTimePicker").date(moment().hour(19).minute(30));
                }
            }
        },
        defaultDateSet = function(e){
            lastDateUsed = e.date;
        };


	$(".datetimepicker").datetimepicker(datePickerOptions).on("dp.show", defaultDate).on("dp.change", defaultDateSet);


    lastDateUsed = (function(){
        var max = null;
        $(".datetimepicker").each(function(){
            var d = $(this).data("DateTimePicker").date();
            if(d){
                if(max){
                    max = moment.max(max, d);
                }else{
                    max = d;
                }
            }
        });
        return max;
    })();

    // Add new date feild
	var dates_base = '<div class="form-group">' +
					'<div class="input-group date datetimepicker">' +
						'<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>' +
						'<input type="text" class="form-control" autocomplete="off" name="show_dates[]">' +
						'<!--<span class="input-group-btn"><button class="btn btn-danger" type="button"><span class="glyphicon glyphicon-remove"></span></button></span>-->' +
					'</div>' +
				'</div>';
	
    $("#shows-dates-add").on("click", function(e){
    	e.preventDefault();

    	var newDateSelector = $(dates_base);
    	newDateSelector.appendTo("#show-dates-block");
    	newDateSelector.children(".datetimepicker").datetimepicker(datePickerOptions).on("dp.show", defaultDate).on("dp.change", defaultDateSet);

    });

    // Disable URL if ticketsource is used
    var saved_tick_val = "";
    $("#show_ticketsource").on("change", function(){
    	var txtbox = $("#show_ticketURL"),
    		box = $("#show_ticketURL_container");
    	if(this.checked){
    		txtbox.attr("disabled", true);
    		box.hide();

    		saved_tick_val = txtbox.val();
    		txtbox.val("");
    	}else{
    		txtbox.attr("disabled", false);
    		box.show();

    		txtbox.val(saved_tick_val);
    		saved_tick_val = "";
    	}
    });


    $(".show_delete").on("click", function(e){
    	e.preventDefault();

    	if($("#show-edit-form").data("show")){
    		window.location = "../delete/" + $("#show-edit-form").data("show");
    	}
    });



    // Image upload functions
    // Clear new upload
    $("#show_image").parent().find("button").on("click", function(e){
        e.preventDefault();
        var i = $("#show_image");
        i.replaceWith(i.clone(true));

        // Clear the image
        var display = $("#show_image_display");
        display.attr("src", display.data("orgsrc"));
    });


    // Show the new image that is going to be uploaded
    $("#show_image").change(function(e){

        if(this.files && this.files[0] && "FileReader" in window){
            var reader = new FileReader();

            reader.addEventListener("load", function(e){
                $("#show_image_display").attr("src", e.target.result);
            });

            reader.readAsDataURL(this.files[0]);
        }

        // Uncheck the delete button
        document.getElementById("show_image-delete_input").checked = false;
    });


    $("#show_image-delete").click(function(e){
        var inp = document.getElementById("show_image-delete_input");
        inp.checked = (inp.checked)?false:true;

        var img = $("#show_image_display");
        if(inp.checked){
            img.data("presrc", img.attr("src"));
            img.attr("src", "");
        }else{
            img.attr("src", img.data("presrc"));
        }
    });






    // Update the checkbox before submission if you want to move to member editing next
    $('.show_submit-members').click(function(){
        $('#member-next').val("true");
    });
});