<!-- Sidebar -->
<div id="sidebar-container" class="sidebar-collapsed vspt-sidebar-container col">
    <!-- d-* hiddens the Sidebar in smaller devices. Its itens can be kept on the Navbar 'Menu' -->
    <!-- Bootstrap List Group -->
    <ul class="list-group">

        <a href="#top" data-toggle="sidebar-colapse"
           class="bg-dark list-group-item list-group-item-action d-flex align-items-center">
            <div class="d-flex w-100 justify-content-start align-items-center">
                <span id="collapse-icon" class="fa fa-2x mr-3"></span>
                <span id="collapse-text" class="menu-collapsed d-none">Collapse</span>
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
                                <span class="menu-collapsed d-none">Accounts</span>
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


           case INTERNAL_PAGES::LANDING:
               echo ' <li class="list-group-item sidebar-separator-title text-muted align-items-center menu-collapsed d-none">
                            <small>Actions</small>
                    </li>
                    <!-- /END Separator -->
                    
                       <a href="#" id="changeRoleBtn" class="bg-dark list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-start align-items-center">
                            <span class="fas fa-wrench fa-fw mr-3"></span>
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
               break;

       }

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

<script type="text/javascript">
    // Hide submenus
    $('#body-row .collapse').collapse('hide');

    // Collapse/Expand icon
    $('#collapse-icon').addClass('fa-angle-double-right');

    // Collapse click
    $('[data-toggle=sidebar-colapse]').click(function() {
        SidebarCollapse();
    });

    function SidebarCollapse () {
        $('.menu-collapsed').toggleClass('d-none');
        $('.sidebar-submenu').toggleClass('d-none');
        $('.submenu-icon').toggleClass('d-none');
        $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed col-2 col');
        // $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed col-2 col-auto');
        $(".vspt-page-container").toggleClass("col-10 vspt-col-auto-fix");

        // Treating d-flex/d-none on separators with title
        var SeparatorTitle = $('.sidebar-separator-title');
        if ( SeparatorTitle.hasClass('d-flex') ) {
            SeparatorTitle.removeClass('d-flex');
        } else {
            SeparatorTitle.addClass('d-flex');
        }

        // Collapse/Expand icon
        $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
    }
</script>