$(function(){

    $("#btn_add_soc").on("click", function(e){
    	e.preventDefault();

    	var socBox = $("#socs_add_base").children().first().clone();
    	socBox.appendTo("#socs_add_block");

    });

    $(".btn_delete_soc").on("click", function(e){
    	e.preventDefault();

    	var span = $(this).parent(),
    		inp = span.parent().find("input");

    	span.remove();
    	inp.val("true");
    });


    // Clear new upload
    $("#user_image").parent().find("button").on("click", function(e){
        e.preventDefault();
        var i = $("#user_image");
        i.replaceWith(i.clone(true));

        // Clear the image
        var display = $("#user_image_display");
        display.attr("src", display.data("orgsrc"));
    });


    // Show the new image that is going to be uploaded
    $("#user_image").change(function(e){

        if(this.files && this.files[0] && "FileReader" in window){
            var reader = new FileReader();

            reader.addEventListener("load", function(e){
                $("#user_image_display").attr("src", e.target.result);
            });

            reader.readAsDataURL(this.files[0]);
        }
    });

    // Remove the current image when checked and replace when unchecked
    $("#user_image-delete").on("change", function(e){
        var img = $("#user_image_display");
        if(this.checked){
            img.data("presrc", img.attr("src"));
            img.attr("src", "");
        }else{
            img.attr("src", img.data("presrc"));
        }
    });


});
