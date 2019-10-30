<?php

    // Homepage mate
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts Awards Winners");

?>

<!--
<iframe id="g_form" style="width: 100%; height: 3700px; border-style: none; position: relative; top: -5em;" src="https://docs.google.com/a/susuperformingarts.org/forms/d/158iC4axecetQxluIm5L5n37wh8j1WmlPKrWDRPQPQaY/viewform"></iframe>

<a style="text-align: right; display: block; position: relative; top: -5em;" href="#">Go to top (see submission result)</a>
-->

    <div class="well well-pa">
        <h1>Performing Arts Award Winners!</h1>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="well well-pa">
                <h2 class="h3">Outstanding Society<br>&nbsp;</h2>
                <hr>
                <p class="h4"><a class="white-link" href="/societies/society/?id=comedy-soc">Comedy Society</a></p>
                <p>(Highly commended <a class="white-link" href="/societies/society/?id=afrodynamix">Afrodynamix</a>)</p>
            </div>
        </div>


        <div class="col-sm-4">
            <div class="well well-pa">
                <h2 class="h3">Dedication to the Performing Arts</h2>
                <hr>
                <p class="h4"><a class="white-link" href="/archive/members/3">Immy Tantam</a></p>
                <p>(Highly commended <a class="white-link" href="/archive/members/113">Jeremy Hunt</a>)</p>
            </div>
        </div>


        <div class="col-sm-4">
            <div class="well well-pa">
                <h2 class="h3">Commitment to a Performing Arts Committee</h2>
                <hr>
                <p class="h4"><a class="white-link" href="/archive/members/13">Charlie House</a></p>
                <p>(Highly commended <a class="white-link">Maddie Ell</a>)</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="well well-pa">
                <h2 class="h3">Outstanding Performer in a Theatrical Society</h2>
                <hr>
                <p class="h4"><a class="white-link">Joe Hand</a></p>
                <p>(Highly commended <a class="white-link" href="/archive/members/36">Ieuan Harrild</a>)</p>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="well well-pa">
                <h2 class="h3">Outstanding Performer in a Musical Society</h2>
                <hr>
                <p class="h4"><a class="white-link" href="/archive/members/138">Kath Roberts</a></p>
                <p>(Highly commended <a class="white-link" href="/archive/members/180">Gemma Wills</a>)</p>
            </div>
        </div>


        <div class="col-sm-4">
            <div class="well well-pa">
                <h2 class="h3">Outstanding Performer in a Dance Society</h2>
                <hr>
                <p class="h4"><a class="white-link">Claire Sommerville</a></p>
                <p>(Highly commended <a class="white-link">Lauren Westie</a>)</p>
            </div>
        </div>

    </div>




        <div class="row">
            <div class="col-sm-4">
                <div class="well well-pa">
                    <h2 class="h3">Best for ‘Behind the Scenes’ in a Theatrical Society</h2>
                    <hr>
                    <p class="h4"><a class="white-link" href="/archive/members/14">Phoebe Lewis</a></p>
                    <p>(Highly commended <a class="white-link" href="/archive/members/96">Will Cook</a>)</p>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="well well-pa">
                    <h2 class="h3">Best for ‘Behind the Scenes’ in a Musical Society</h2>
                    <hr>
                    <p class="h4"><a class="white-link" href="/archive/members/157">Meg Holgate</a></p>
                    <p>(Highly commended <a class="white-link">Jonathan Sandman</a> and <a class="white-link" href="/archive/members/2">Becky Griffin</a>)</p>
                </div>
            </div>


            <div class="col-sm-4">
                <div class="well well-pa">
                    <h2 class="h3">Best for ‘Behind the Scenes’ in a Dance Society</h2>
                    <hr>
                    <p class="h4"><a class="white-link" href="/archive/members/332">Jithin Mullappillil</a></p>
                    <p>(Highly commended <a class="white-link">Jessica Leach</a>)</p>
                </div>
            </div>



    </div>



<?php
    \PA\Snippets\Footer::printFooter();
