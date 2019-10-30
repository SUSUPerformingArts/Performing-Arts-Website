$(function(){
    // Role area tabs
    $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    var currentTab = $('.nav-tabs li.active a').data('area');
    $('.nav-tabs').on('show.bs.tab', function(e){
        currentTab = $(e.target).data('area');
    });



    var counter = 1;
    function addMember(m){
        var oldsrId,
            currTab = currentTab;

        // Deal with old members that are put in on page load
        if(m.old && (m.showRoleId || m.showRoleId === 0)){
            oldsrId = $(document.createElement("input"));
            oldsrId.attr("name", "show_member-old_showrole_id[" + counter + "]")
                .attr("type", "hidden")
                .val(m.showRoleId);

            // Change the list
            currTab = m.roleAreaName.toLowerCase().replace(' ', '');
        }


        // Create the elements used for construction
        var list         = $("#tab_" + currTab + " .list-group"),
            li           = $(document.createElement("li")),
            name         = $(document.createElement("span")),
            hid          = $(document.createElement("input")),
            del          = $(document.createElement("button")),
            ldiv         = $(document.createElement("div")),
            notesDiv     = $(document.createElement("div")),
            notes        = $(document.createElement("input")),
            roleDiv      = $(document.createElement("div")),
            roleSelector = "#show-edit-member-base .member-edit-base-area." + currTab + " select",
            role         = $(roleSelector).clone(),
            oldsrId;


        li.addClass("list-group-item");

        name.html(m.fullName)
            .css("font-weight", "bold");



        if(m.suggested){
            hid.attr("name", "show_member_suggested[" + counter +"]");
        }else{
            hid.attr("name", "show_member[" + counter +"]");
        }
        hid.attr("type", "hidden")
            .val(m.id);

        ldiv.addClass("row")
            .css("margin-top", "15px");


        roleDiv.addClass("col-md-6")
            .append(role);

        role.attr("name", "show_member-role[" + counter + "]");
        if(m.roleId){
            role.val(m.roleId);
        }


        notesDiv.addClass("col-md-6")
            .append(notes);

        notes.attr("type", "text")
            .attr("placeholder", "Other info (character, instrument, etc)")
            .attr("name", "show_member-notes[" + counter + "]")
            .addClass("form-control");

        if(m.notes){
            notes.val(m.notes);
        }

        ldiv.append(roleDiv, notesDiv);


        del.attr("type", "button")
            .addClass("btn btn-sm btn-danger pull-right")
            .html("<span class='glyphicon glyphicon-remove'></span>")
            .css("position", "relative")
            .css("bottom", "4px")

            .on("click", function(e){
                e.preventDefault();

                li.remove();

                if(m.old && (m.showRoleId || m.showRoleId === 0)){
                    var n = (!m.suggested)?"show_member_delete[]":"show_member_suggested_delete[]",

                        el = $(document.createElement("input"));
                    el.attr("name", n)
                        .val(m.showRoleId)
                        .attr("type", "hidden")
                    list.parent().append(el);
                }
            });

        //blah blah

        if(oldsrId){
            li.append(oldsrId);
        }

        li.append(hid);
        li.append(name);
        li.append(del);
        li.append(ldiv);

        // Put at top of the list
        list.prepend(li);

        counter++;
    }


    if(oldMembers){
        oldMembers.reverse().forEach(addMember);
    }

    memberSearch_hook(addMember);

});