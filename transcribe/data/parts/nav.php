<nav class="navbar navbar-dark bg-dark shadow-lg">

    <a class="navbar-brand" href="#">
        <img src="/data/images/Logo_only.png" width="30" height="30" class="vtex-skip d-inline-flex align-top" alt="">
    </a>

    <span class="navbar-text">
    <?php echo ucfirst($_SESSION['uEmail']);
    echo isset($_SESSION["acc_name"]) ?  " - " .  $_SESSION["acc_name"] : "";
    echo isset($_SESSION["role_desc"]) ? " - " . $_SESSION["role_desc"] : "";


    ?>
  </span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample01" aria-controls="navbarsExample01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExample01">
        <ul class="navbar-nav mr-auto">

            <li class="nav-item <?php if($vtex_page == 0) echo 'active' ?>">
                <a class="nav-link" href="/landing.php">Home</a>
            </li>

            <!--<li class="nav-item <?php /*if($vtex_page == 1) echo 'active' */?>">
                <a class="nav-link" href="/transcribe.php">
                    <i class="fas fa-angle-double-right"></i>
                    Transcribe
                </a>
            </li>

            <li class="nav-item <?php /*if($vtex_page == 2) echo 'active' */?>">
                <a class="nav-link" href="/main.php">
                    <i class="fas fa-angle-double-right"></i>
                    Job Lister
                </a>
            </li>


            <li class="nav-item <?php /*if($vtex_page == 3) echo 'active' */?>">
                <a class="nav-link" href="/panel/">
                    <i class="fas fa-angle-double-right"></i>
                    Admin Panel
                </a>
            </li>-->

            <?php
            if (isset($_SESSION["role"])) {
                $rl = $_SESSION["role"];
                // todo recopy this
                if ($rl == 3) {
                    echo "<li class=\"nav-item ";
                    if ($vtex_page == 1) echo 'active';
                    echo " \">
                            <a class=\"nav-link\" href=\"/transcribe.php\">
                                <i class=\"fas fa-angle-double-right\"></i>
                                Transcribe
                            </a>
                        </li>";
                }

                else if ($rl == 2) {
                    echo "<li class=\"nav-item ";
                    if ($vtex_page == 2) echo 'active';
                    echo " \">
                            <a class=\"nav-link\" href=\"/main.php\">
                                <i class=\"fas fa-angle-double-right\"></i>
                                Job Lister
                            </a>
                        </li>";

                }

                else if ($rl == 1) {
                    echo "<li class=\"nav-item ";
                    if ($vtex_page == 1) echo 'active';
                    echo " \">
                            <a class=\"nav-link\" href=\"/transcribe.php\">
                                <i class=\"fas fa-angle-double-right\"></i>
                                Transcribe
                            </a>
                        </li>";

                    echo "<li class=\"nav-item ";
                    if ($vtex_page == 2) echo 'active';
                    echo " \">
                            <a class=\"nav-link\" href=\"/main.php\">
                                <i class=\"fas fa-angle-double-right\"></i>
                                Job Lister
                            </a>
                        </li>";

                    echo "<li class=\"nav-item ";
                    if ($vtex_page == 3) echo 'active';
                    echo " \">
                            <a class=\"nav-link\" href=\"/panel/\">
                                <i class=\"fas fa-angle-double-right\"></i>
                                Admin Panel
                            </a>
                        </li>";
                }
            }
            ?>


            <li class="nav-item">
                <a class="nav-link" href="/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </li>



            <!--<li class="nav-item">
                <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
            </li>-->
            <!--<li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                <div class="dropdown-menu" aria-labelledby="dropdown01">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>-->
        </ul>
        <!--<form class="form-inline my-2 my-md-0">
            <input class="form-control" type="text" placeholder="Search" aria-label="Search">
        </form>-->
    </div>
</nav>