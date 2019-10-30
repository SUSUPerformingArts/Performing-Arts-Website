<?php

    $root = $_SERVER["DOCUMENT_ROOT"];
    require $root . "/compose.php";

    use \PA\Auth\Auth;

    \PA\Snippets\Header::printHeader("SUSU Performing Arts :: Submit a Nuffield pitch");

    $now = new DateTime("now", new DateTimeZone("Europe/London"));
    $deadline = new DateTime("2016/05/30", new DateTimeZone("Europe/London"));

?>

    <div class="well well-pa">
        <h1>Nuffield Pitch Submission</h1>
    </div>



    <div class="well well-pa">
        <p class="lead">
            Interested in putting on a performance in a professional theatre setting? This is your chance!
        </p>
        <p>
            Once a year Performing Arts has the chance to put on a show in the <a href="http://www.nuffieldtheatre.co.uk">Nuffield Theatre</a>, and Performing Arts Committee open the opportunity up for every member of the performing arts to pitch!
        </p>
        <p>
            Next year we have been given a slot on the week commencing <em>13th February 2017</em>, with either 4 or 5 performances over that week.
        </p>
        <p style="font-weight: bold;">
            For more information please see the pitching document <a href="https://docs.google.com/document/d/1JXmwzey8nifb-HNdec5mFr1CqbOW1J0UJZK-WTR3_iU/edit" target="_blank">here</a>.
        </p>

    </div>

    <div class="well well-pa">
        <h2>Submit a pitch</h2>

        <?php 
            if($now > $deadline){
                echo "<p>";
                echo "Sorry the deadline for submitting Nuffield pitches has passed, please contact <a href='secretary@susuperformingarts.org'>secretary@susuperformingarts.org</a> for more information.";
                echo "</p>";
            }else{


                // Must be logged in to submit
                if(Auth::isLoggedIn()){
                    $userId = Auth::getUserId();
                    $path = 'submissions/' . $userId;
                    if(is_dir($path)){
                        echo "<p class='h4'>";
                        echo "You have already submitted a pitch, new submissions will overwrite previous ones";
                        echo "<br><small>";
                        echo "If you wish to submit a second pitch with a different team, please get another member to fill this form out, or email <a href='secretary@susuperformingarts.org'>secretary@susuperformingarts.org</a>.";
                        echo "</small></p>";
                    }
            ?>
                <form autocomplete="off" action="submit.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="input-written">
                                Written pitch
                            </label>
                            <p>
                                This should be between 1000 and 3000 words long, but that there can be as many appendices as desired, and it should include:
                                <ul>
                                    <li>An explanation the show</li>
                                    <li>An outline of the marketing scheme</li>
                                    <li>Technical Requirements</li>
                                </ul>
                            </p>
                            <em>Please upload as <strong>pdf</strong> if possible (doc, docx and txt files also accepted)</em>
                            <input type="file" name="written" id="input-written" class="form-control" required>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label for="input-budget">
                                Provisional Budget
                            </label>
                            <p>
                                Please download and fill in the template <a href="https://drive.google.com/a/susuperformingarts.org/file/d/0B8QnE0Kdupy-Z2tuZ0JxcjBBeFU/view" target="_blank">here</a>.
                                <br>
                                See the <a href="https://docs.google.com/document/d/1JXmwzey8nifb-HNdec5mFr1CqbOW1J0UJZK-WTR3_iU/edit" target="_blank">pitching document</a> for some guidance on this.
                            </p>
                            <em>Please upload as <strong>xlsx</strong> if possible (pdf and xls files also accepted)</em>
                            <input type="file" name="budget" id="input-budget" class="form-control" required>
                        </div>
                    </div>


                    <div class="form-group pull-right">
                        <input type="submit" class="btn btn-pa" value="Submit">
                    </div>
                    <div class="clearfix"></div>
                </form>


            <?php
                }else{
            ?>

                <p>
                    You must be logged in to submit a pitch. Please click <a href="/archive/login?continue=/nuffield/pitches/">here</a> to log in.
                </p>

            <?php
                }
            }
            ?>

    </div>







<?php
    \PA\Snippets\Footer::printFooter();
