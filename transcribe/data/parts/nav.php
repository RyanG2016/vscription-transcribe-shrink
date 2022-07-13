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
<div id="sidebar-container" class="sidebar-expanded vspt-sidebar-container d-flex-inline">
    <!-- d-* hides the Sidebar in smaller devices. Its items can be kept on the Navbar 'Menu' -->

    <div class="branding ml-auto mr-auto">
        <a class="navbar-brand  w-100 m-0 text-center" href="/">
            <img src="/data/images/Logo_only.png" width="40" height="40" class="vtex-skip d-inline-flex align-top ml-auto mr-auto"
                 alt="">
        </a></div>
    <!-- Bootstrap List Group -->
    <ul class="list-group">

        <!-- Separator with title -->
        <!-- <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-collapsed d-none">
            <small>MAIN</small>
        </li> -->
        <!-- /END Separator -->
        <!-- Menu with submenu -->

        <?php
         // home page calculation
        $homePage = "settings.php"; // default

        if(isset($_SESSION["role"]))
        {
            switch ($_SESSION["role"])
            {
                case 1:
                case 2:
                case 5:
                    $homePage = "main.php";
                break;
                case 3:
                    $homePage = "transcribe.php";
                break;
            }
        }
        ?>

        <a href="/<?php echo $homePage ?>" id="home-nav" class="bg-dark list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-start align-items-center">
                <span class="fas fa-home fa-fw mr-3"></span>
                <span class="menu-expanded">Home</span>
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
                                <i class="fas fa-user-tie fa-fw mr-3"></i>
                                <span class="menu-collapsed">Admin Panel</span>
                                <span class="submenu-icon ml-auto"></span>
                            </div>
                        </a>
                        <!-- Submenu content -->
                        <div id="adminmenu" class="collapse sidebar-submenu">
                            <a href="/panel/" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-user-shield fa-fw mr-3"></span>
                                <span class="menu-collapsed">Panel</span>
                            </a><a href="/panel/users.php" id="users-nav" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-users fa-fw mr-3"></span>
                                <span class="menu-collapsed">Users</span>
                            </a>
                            <a href="/panel/accounts.php" id="accounts-nav" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-id-card fa-fw mr-3"></span>
                                <span class="menu-collapsed">Organizations</span>
                            </a>
                
                            <a href="/panel/admin_tools.php" id="admin-tools-nav" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-toolbox fa-fw mr-3"></span>
                                <span class="menu-collapsed">Admin Tools</span>
                            </a>
                            <a href="/panel/billing_report.php" id="billing-report-nav" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-dollar-sign fa-fw mr-3"></span>
                                <span class="menu-collapsed">Billing Reports</span>
                            </a>
                
                            <a href="/panel/typist_report.php" id="typist-report-nav" class="list-group-item list-group-item-action bg-dark text-white">
                                <span class="fas fa-keyboard fa-fw mr-3"></span>
                                <span class="menu-collapsed">Typist Reports</span>
                            </a>
                        </div>
                        
                        <a href="/main.php" id="main-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-list-alt fa-fw mr-3"></span>
                                <span class="menu-expanded">Job List</span>
                            </div>
                        </a>
                        
                        
                        
                        <a href="/jobupload.php" id="upload-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-cloud-upload-alt fa-fw mr-3"></span>
                                <span class="menu-expanded">Upload Jobs</span>
                            </div>
                        </a>
                        
                        <a href="/transcribe.php" id="transcribe-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-keyboard fa-fw mr-3"></span>
                                <span class="menu-expanded">Transcribe</span>
                            </div>
                        </a>
                        
                          <a href="/manage_users.php" id="manage-users-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fad fa-users fa-fw mr-3"></span>
                                <span class="menu-expanded">Manage Users</span>
                            </div>
                        </a>
';
               break;
           case 2:
               echo '
                        <a href="/main.php" id="main-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-list-alt fa-fw mr-3"></span>
                                <span class="menu-expanded">Job List</span>
                            </div>
                        </a>
                        
                        <a href="/jobupload.php" id="upload-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-cloud-upload-alt fa-fw mr-3"></span>
                                <span class="menu-expanded">Upload Jobs</span>
                            </div>
                        </a>
                        
                          <a href="/transcribe.php" id="transcribe-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-keyboard fa-fw mr-3"></span>
                                <span class="menu-expanded">Transcribe</span>
                            </div>
                        </a>
                        
                          <a href="/manage_users.php" id="manage-users-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fad fa-users fa-fw mr-3"></span>
                                <span class="menu-expanded">Manage Users</span>
                            </div>
                        </a>';
               break;

           case 3:
            if ($_SESSION['defaultCompactView'] == 1) {
                echo '
                        <a href="/jobupload.php" id="upload-nav" class="bg-dark list-group-item list-group-item-action">
                             <div class="d-flex w-100 justify-content-start align-items-center">
                                 <span class="fas fa-cloud-upload-alt fa-fw mr-3"></span>
                                 <span class="menu-expanded">Upload Jobs</span>
                             </div>
                         </a>
                         <a href="#" onClick="window.open(\'/popup.php\', \'modalPlayer\', \'toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=530,height=262\'); return false;" target="_blank" id="transcribe-nav" class="bg-dark list-group-item list-group-item-action">
                             <div class="d-flex w-100 justify-content-start align-items-center">
                                 <span class="fas fa-keyboard fa-fw mr-3"></span>
                                 <span class="menu-expanded">Transcribe</span>
                             </div>
                         </a>
                        ';
            } else {
                echo '
                <a href="/jobupload.php" id="upload-nav" class="bg-dark list-group-item list-group-item-action">
                     <div class="d-flex w-100 justify-content-start align-items-center">
                         <span class="fas fa-cloud-upload-alt fa-fw mr-3"></span>
                         <span class="menu-expanded">Upload Jobs</span>
                     </div>
                 </a>
                 <a href="/transcribe.php" id="transcribe-nav" class="bg-dark list-group-item list-group-item-action">
                     <div class="d-flex w-100 justify-content-start align-items-center">
                         <span class="fas fa-keyboard fa-fw mr-3"></span>
                         <span class="menu-expanded">Transcribe</span>
                     </div>
                 </a>
                ';
            }
               break;


           case \Src\Enums\ROLES::AUTHOR:
               echo '
                        <a href="/main.php" id="main-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-list-alt fa-fw mr-3"></span>
                                <span class="menu-expanded">Job List</span>
                            </div>
                        </a>
                        
                        <a href="/jobupload.php" id="upload-nav" class="bg-dark list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-cloud-upload-alt fa-fw mr-3"></span>
                                <span class="menu-expanded">Upload Jobs</span>
                            </div>
                        </a>';
               break;
       }

       switch($vtex_page)
       {
           case INTERNAL_PAGES::USERS:
               echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-expanded">
                            <small>Actions</small>
                        </li>
                        <!-- /END Separator -->
                        
                           <a href="#" id="createAcc" class="bg-dark list-group-item list-group-item-action vspt-actions">
                            <div class="d-flex w-100 justify-content-start align-items-center">
                                <span class="fas fa-user-plus fa-fw mr-3"></span>
                                <span class="menu-expanded">Add User</span>
                            </div>
                        </a>';
               break;

           case INTERNAL_PAGES::ACCOUNTS:
               echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-expanded">
                            <small>Actions</small>
                    </li>
                    <!-- /END Separator -->
                    
                       <a href="#" id="createAcc" class="bg-dark list-group-item list-group-item-action vspt-actions">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-user-plus fa-fw mr-3"></span>
                            <span class="menu-expanded">Create Organization</span>
                        </div>
                    </a>';
               break;

           case INTERNAL_PAGES::MANAGE_USER_ACCESS:
               echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-expanded">
                            <small>Actions</small>
                    </li>
                    <!-- /END Separator -->
                    
                       <a href="#" id="createAcc" class="bg-dark list-group-item list-group-item-action vspt-actions">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-plus-circle fa-fw mr-3"></span>
                            <span class="menu-expanded">Add Permission</span>
                        </div>
                    </a>';

               break;


           case INTERNAL_PAGES::MANAGE_USERS:
               echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-expanded">
                            <small>Actions</small>
                    </li>
                    <!-- /END Separator -->
                    
                       <a href="#" id="createAcc" class="bg-dark list-group-item list-group-item-action vspt-actions">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-envelope fa-fw mr-3"></span>
                            <span class="menu-expanded">Invite User</span>
                        </div>
                    </a>';

               break;
/*

           case INTERNAL_PAGES::LANDING:

               break;*/

       }

       echo ' <!-- <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-collapsed d-none">
                            <small>Manage Role</small>
                    </li> -->
                    <!-- /END Separator -->
                    
                       <a href="#" id="changeRoleBtn" class="bg-dark list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-exchange-alt fa-fw mr-3"></span>
                            <span class="menu-expanded">Switch Org/Role</span>
                        </div>
                    </a>
                       <a href="#" id="setDefaultRoleBtn" class="bg-dark list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-key fa-fw mr-3"></span>
                            <span class="menu-expanded">Set Default</span>
                        </div>
                    </a>
                    ';
       ?>


        <a href="/settings.php" id="settings-nav" class="bg-dark list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-start align-items-center">
                <span class="fad fa-cogs fa-fw mr-3"></span>
                <span class="menu-expanded">Settings</span>
            </div>
        </a>

        <a href="/downloads.php" id="downloads-nav" class="bg-dark list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-start align-items-center">
                <span class="fad fa-download fa-fw mr-3"></span>
                <span class="menu-expanded">Downloads</span>
            </div>
        </a>

        <!-- Separator without title -->
        <li class="list-group-item sidebar-separator menu-collapsed d-none"></li>
        <!-- /END Separator -->
        <a href="/logout.php" class="bg-dark list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-start align-items-center">
                <span class="fa fa-sign-out fa-fw mr-3"></span>
                <span class="menu-expanded">Logout</span>
            </div>
        </a>
        <a href="#top" data-toggle="sidebar-collapse-toggle" id="collapse-nav"
           class="bg-dark list-group-item list-group-item-action d-flex align-items-center pin-expand-div justify-content-end">
            <div class="d-flex w-100 justify-content-start align-items-center">
                <span id="collapse-icon" class="fa fa-fw mr-3"></span>
                <span id="collapse-text" class="menu-expanded">Collapse</span>
                <!-- <span class="menu-collapsed ml-auto">
                    <button id="pinBtn" type="button" class="btn btn-primary pin-button" data-toggle="button" aria-pressed="false">
                        <i id="pinIcon" class="fas fa-thumbtack fa-rotate-315 pin-icon"></i>
                    </button>
                </span> -->
            </div>
            </a>
        </div>
    </ul><!-- List Group END-->

    <div>
</div><!-- sidebar-container END -->

<div class="modal" tabindex="-1" id="changeRole">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="color: #1e79be" id="navModalHeaderTitle">
                    <i class="fas fa-wrench"></i>&nbsp;Switch Org/Role
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <input id="uidInNav" name="uid" value="<?php echo $_SESSION['uid'] ?>" style="display: none">

<!--                <form method="post" id="createAccForm" class="createAccForm" target="_self">-->

                    <div class="account text-center w-100">
                        <div><h4 class="font-weight-light">Organization</h4></div>
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