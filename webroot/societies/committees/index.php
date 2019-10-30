<?php

error_reporting(-1);
ini_set('display_errors', 'On');

//echo 'error, please return <a href="/">home</a>';exit;

    // About page
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("Please tell us your committee");

    // Society IDs to exclude from the list
    $exclude = [41];


    $db = \PA\Database::getArchiveDatabase();


    /** Get all the socs **/
    $query = "SELECT socs.`id`, socs.`name`, type.`name` AS 'type'
            FROM `Society` socs LEFT JOIN `SocietyType` type ON socs.`type` = type.`id`
            WHERE socs.`id` NOT IN (" . implode(",", $exclude) . ")
            AND socs.`type` != 5";


    $result = $db->query($query); // Controlled input

    $societies = [];
    $curr = null;
    while($row = $result->fetch_assoc()){
        if($curr !== $row["type"]){
            $curr = $row["type"];
            if(!isset($societies[$curr])){
                $societies[$curr] = [];
            }
        }

        $societies[$curr][] = $row;
    }


    /** Read from the JSON file ***/
    $dir = @scandir("results", SCANDIR_SORT_DESCENDING);
    $json = null;
    if(isset($dir[0])){
        $json = json_decode(@file_get_contents("results/" . $dir[0]), true);
    }


    function printFormGroup($position = "", $name = "", $username = ""){
        ?>
            <div class="form-group form-inline">
                <button class="btn btn-danger remove-soc">-</button>
                <input class="form-control" required style="margin-left: 20px;" type="text" placeholder="Position" value="<?php echo $position; ?>" name="position[]">
                <input class="form-control" required style="margin-left: 20px;" type="text" placeholder="Name" value="<?php echo $name; ?>" name="name[]">
                <input class="form-control" required style="margin-left: 20px; text-align: right;" type="text" placeholder="Email ID" value="<?php echo $username; ?>" name="username[]">@soton.ac.uk
            </div>
        <?php
    }
?>

<div class="well well-pa">
    <form action="submit.php" method="POST" autocomplete="off">

        <div class="form-group">
            <label for="society">Society</label>
            <select class="form-control" id="society" name="society" required>
                <option value="" disabled selected style="display: none;">Society</option>
                <?php foreach ($societies as $type => $socs) { ?>
                    <optgroup label="<?php echo $type; ?>">
                        <?php foreach($socs as $soc){ ?>
                            <option value="<?php echo $soc["id"] ?>"><?php echo $soc["name"] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>
        </div>

        <hr>


        <div id="boiler_holder">
            <?php 
                printFormGroup();
                printFormGroup();
                printFormGroup();
                printFormGroup();
                printFormGroup();
            ?>
        </div>

        <button id="new_member" class="btn btn-primary">Add new member</button>
        <input type="submit" class="btn btn-success pull-right" value="Submit">
    </form>
</div>


<div id="group_boiler" class="hidden">
<?php printFormGroup(); ?>
</div>


<div id="soclists">
<?php
if(!is_null($json)){
    foreach ($json as $socid => $soc){
?>
    <div id="soc_<?php echo $socid; ?>" class="hidden">
        <?php
            foreach($soc["members"] as $member){
                printFormGroup($member["position"], $member["name"], $member["username"]);
            }
        ?>
    </div>
<?php
    }
}
?>
</div>

<script>
window.addEventListener("load", function(){
    $("#new_member").on("click", function(e){
        e.preventDefault();
        var n = $("#group_boiler div").clone();
        $("#boiler_holder").append(n);
    });


    $(document).on("click", ".remove-soc",  function(e){
        e.preventDefault();
        $(this).parent().remove();
    });


    var old = $("#boiler_holder div"),
        w = false;
    $("#society").on("change", function(){
        var socl = $("#soc_" + $(this).val());
        if(socl.length){
            if(!w){
                old = $("#boiler_holder div")
            }
            $("#boiler_holder").empty();
            $("#boiler_holder").append(socl.children("div").clone());
            w = true;
        }else{
            if(w){
                $("#boiler_holder").empty();
                $("#boiler_holder").append(old);
            }
            w = false;
        }
    });
});
</script>


<?php
    \PA\Snippets\Footer::printFooter();
