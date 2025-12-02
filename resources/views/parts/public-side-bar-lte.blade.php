<style>
    .layout-fixed .brand-link {

        /*background-color: #052584;*/
        background-color: #303030;
    }
    [class*=sidebar-dark] .brand-link, [class*=sidebar-dark] .brand-link .pushmenu {
        background: royalblue;
    }
    [class*=sidebar-dark] .brand-link {
        border-bottom: 0px;
    }

</style>
<!-- Main Sidebar Container -->
<aside data-code-pos="ppp1681393121314" class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link" style="padding: 8px">
        <div style="">

        <img src="<?php echo getLogoDomain() ?>" alt="GLX Logo" class="" style="opacity: .8; height: 40px">

            <?php
            echo \App\Models\SiteMng::getAppName()
            ?>

        </div>
{{--        <span class="brand-text font-weight-light">--}}
{{--            abc--}}
{{--        </span>--}}
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->


{{--        <br>--}}
{{--        <!-- SidebarSearch Form -->--}}
{{--        <div class="form-inline">--}}
{{--            <div class="input-group" data-widget="sidebar-search">--}}
{{--                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">--}}
{{--                <div class="input-group-append">--}}
{{--                    <button class="btn btn-sidebar">--}}
{{--                        <i class="fas fa-search fa-fw"></i>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <!-- Sidebar Menu -->
        <nav class="mt-2" data-code-pos="ppp16765864318592">
            <ul class="nav nav-pills nav-sidebar text-sm flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <?php

                if(0)
                if(isAdminACP_()){
                ?>
                <li class="nav-item">
                    <a href="/admin" class="nav-link">
                        <i class="nav-icon fas fa-arrow-right"></i>
                        <p style="color: red">ACP</p>
                    </a>
                </li>
                <?php
                }
                ?>

                <li class="nav-item menu-open" data-code-pos="ppp16768641316081">

                        <?php
                        \App\Models\MenuTree::showMenuAdminLte(3);
                        ?>
                </li>

                <li class="nav-item">
                    <a href="/logout" class="nav-link">
                        <i class="nav-icon fas fa-arrow-left"></i>
                        <p>
                        Logout
                        </p>
{{--                        ({{ auth()->user() ? auth()->user()->email : '' }})--}}
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
