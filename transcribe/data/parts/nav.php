<!-- BOOTSTRAP -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"
        integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg=="
        crossorigin="anonymous"></script>
<link rel="stylesheet" href="/data/css/custom-bootstrap-select.css" />


<script type="text/javascript">
    <?php
    $roleIsSet = (!isset($_SESSION['role']) && !isset($_SESSION['accID']))?0:true;
    ?>
    var roleIsset = <?php echo $roleIsSet ?>;
    var redirectID = <?php echo $roleIsSet? $_SESSION['role']:0 ?>;
</script>

<!-- Sidebar -->
<div id="sidebar-container" class="sidebar-collapsed vspt-sidebar-container col">
    <!-- d-* hiddens the Sidebar in smaller devices. Its itens can be kept on the Navbar 'Menu' -->

    <div class="branding ml-auto mr-auto">
        <a class="navbar-brand  w-100 m-0 text-center" href="/">
            <img src="/data/images/Logo_only.png" width="40" height="40" class="vtex-skip d-inline-flex align-top ml-auto mr-auto"
                 alt="">
        </a></div>
    <!-- Bootstrap List Group -->
    <ul class="list-group">

        <a href="#top" data-toggle="sidebar-colapse"
           class="bg-dark list-group-item list-group-item-action d-flex align-items-center">
            <div class="d-flex w-100 justify-content-start align-items-center">
                <span id="collapse-icon" class="fa fa-2x mr-3"></span>
                <span id="collapse-text" class="menu-collapsed d-none">Expand</span>
            </div>
        </a>

        <!-- Separator with title -->
        <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-collapsed d-none">
            <small>MAIN</small>
        </li>
        <!-- /END Separator -->
        <!-- Menu with submenu -->
        <a href="/landing.php" class="bg-dark list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-start align-items-center">
                <span class="fas fa-home fa-fw mr-3"></span>
                <span class="menu-collapsed d-none">Home</span>
            </div>
        </a>


       <?php

       use Src\Enums\INTERNAL_PAGES;

       switch(isset($_SESSION["role"])? $_SESSION["role"] :0)
       {
           case 1:
               echo ' <a href="#adminmenu" data-toggle="collapse" aria-expanded="false"
                           class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-user-shield fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Admin Panel</span>
                                <span class="submenu-icon d-none ml-auto"></span>
                            </div>
                        </a>
                        <!-- Submenu content -->
                        <div id="adminmenu" class="collapse sidebar-submenu d-none">
                            <a href="/panel/" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-user-shield fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Panel</span>
                            </a><a href="/panel/users.php" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-users fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Users</span>
                            </a>
                            <a href="/panel/accounts.php" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-id-card fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Organizations</span>
                            </a>
                
                            <a href="/panel/admin_tools.php" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-toolbox fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Admin Tools</span>
                            </a>
                            <a href="/panel/billing_report.php" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-dollar-sign fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Billing Reports</span>
                            </a>
                
                            <a href="/panel/typist_report.php" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-keyboard fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Typist Reports</span>
                            </a>
                        </div>
                        
                        <a href="/main.php" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-list-alt fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Job Lister</span>
                            </div>
                        </a>
                        
                        
                        
                        <a href="/jobupload.php" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-cloud-upload-alt fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Upload Jobs</span>
                            </div>
                        </a>
                        
                        <a href="/transcribe.php" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-keyboard fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Transcribe</span>
                            </div>
                        </a>
';
               break;
           case 2:
               echo '
                        <a href="/main.php" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-list-alt fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Job Lister</span>
                            </div>
                        </a>
                        
                        <a href="/manage_typists.php" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-keyboard fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Manage Typists</span>
                            </div>
                        </a>
                        
                        <a href="/jobupload.php" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-cloud-upload-alt fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Upload Jobs</span>
                            </div>
                        </a>';
               break;

           case 3:
               echo '<a href="/transcribe.php" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-keyboard fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Transcribe</span>
                            </div>
                        </a>
                       ';
               break;
       }

       switch($vtex_page)
       {
           case INTERNAL_PAGES::USERS:
               echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-collapsed d-none">
                            <small>Actions</small>
                        </li>
                        <!-- /END Separator -->
                        
                           <a href="#" id="createAcc" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-user-plus fa-fw mr-3"></span>
                                <span class="menu-collapsed d-none">Add User</span>
                            </div>
                        </a>';
               break;

           case INTERNAL_PAGES::ACCOUNTS:
               echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-collapsed d-none">
                            <small>Actions</small>
                    </li>
                    <!-- /END Separator -->
                    
                       <a href="#" id="createAcc" class="bg-dark list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-user-plus fa-fw mr-3"></span>
                            <span class="menu-collapsed d-none">Create Org</span>
                        </div>
                    </a>';
               break;

           case INTERNAL_PAGES::MANAGE_USER_ACCESS:
               echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-collapsed d-none">
                            <small>Actions</small>
                    </li>
                    <!-- /END Separator -->
                    
                       <a href="#" id="createAcc" class="bg-dark list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-key fa-fw mr-3"></span>
                            <span class="menu-collapsed d-none">Add Permission</span>
                        </div>
                    </a>';

               break;


           case INTERNAL_PAGES::MANAGE_TYPISTS:
               echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-collapsed d-none">
                            <small>Actions</small>
                    </li>
                    <!-- /END Separator -->
                    
                       <a href="#" id="createAcc" class="bg-dark list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-envelope fa-fw mr-3"></span>
                            <span class="menu-collapsed d-none">Invite Typist</span>
                        </div>
                    </a>';

               break;
/*

           case INTERNAL_PAGES::LANDING:

               break;*/

       }

       echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-collapsed d-none">
                            <small>Actions</small>
                    </li>
                    <!-- /END Separator -->
                    
                       <a href="#" id="changeRoleBtn" class="bg-dark list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-exchange-alt fa-fw mr-3"></span>
                            <span class="menu-collapsed d-none">Switch Org/Role</span>
                        </div>
                    </a>
                       <a href="#" id="setDefaultRoleBtn" class="bg-dark list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-wrench fa-fw mr-3"></span>
                            <span class="menu-collapsed d-none">Set Default</span>
                        </div>
                    </a>
                    ';

       ?>

        <!-- Separator without title -->
        <li class="list-group-item sidebar-separator menu-collapsed d-none"></li>
        <!-- /END Separator -->
        <a href="/logout.php" class="bg-dark list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-start align-items-center">
                <span class="fa fa-sign-out fa-fw mr-3"></span>
                <span class="menu-collapsed d-none">Logout</span>
            </div>
        </a>

    </ul><!-- List Group END-->
</div><!-- sidebar-container END -->

<div class="modal" tabindex="-1" id="changeRole">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="color: #1e79be" id="modalHeaderTitle">
                    <i class="fas fa-wrench"></i>&nbsp;Change Role
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <input id="uidInNav" name="uid" value="<?php echo $_SESSION['uid'] ?>" style="display: none">

<!--                <form method="post" id="createAccForm" class="createAccForm" target="_self">-->

                    <div class="account text-center w-100">
                        <div><h4 class="font-weight-light">Account</h4></div>
                        <select id="accountBoxNav" name="acc_id" class="w-100 m-t-7" data-width="250px">
                        </select>
                    </div>
                    <br>

                    <!--===================================================-->
                    <div class="role text-center w-100">
                        <div><h4 class="font-weight-light">Role</h4></div>
                        <select id="roleBoxNav" class="w-100" name="acc_role" data-width="250px">
                        </select>
                        </label>
                    </div>
                    <!--===================================================-->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp; Cancel</button>
                <button type="button" class="btn btn-primary" id="updateRoleBtn"><i class="fas fa-user-edit"></i> &nbsp;Set</button>
            </div>
        </div>
    </div>
</div>

<div class="overlay" id="navOverlay" style="display: none">
    <div class="loading-overlay-text" id="navOverlayText">Please wait..</div>
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>

<script type="text/javascript" src="/data/scripts/nav.min.js"></script>