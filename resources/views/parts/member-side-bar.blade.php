<style>
    .layout-fixed .brand-link {

        /*background-color: #052584;*/
        background-color: #303030!important;
    }
    [class*=sidebar-dark] .brand-link, [class*=sidebar-dark] .brand-link .pushmenu {
        background: royalblue!important;
    }
    [class*=sidebar-dark] .brand-link {
        border-bottom: 0px;
    }

</style>
<!-- Main Sidebar Container -->
<aside data-code-pos="ppp1681393121314" class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link" style="">

        <img src="<?php echo getLogoDomain() ?>" alt="GLX Logo" class="brand-image img-circle elevation-3" style="opacity: .8; height: 40px">

        <span class="brand-text font-weight-light" data-code-pos="ppp1679890525">
            <?php
            echo \App\Models\SiteMng::getAppName()
            ?>
        </span>
{{--        <span class="brand-text font-weight-light">--}}
{{--            abc--}}
{{--        </span>--}}
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->


{{--        <br>--}}
        <!-- SidebarSearch Form -->
        <?php
        if(\App\Models\SiteMng::enable4sLink()){
        ?>

        <div class="form-inline mt-2">
            <form action="/search-file?&exactly=1&sort_by=new" target="_blank">

                <input
                    style="width: 193px; display: inline;background-color: #ccc"
                    class="form-control form-control-sm" type="text" name="search_string" placeholder="Tìm file chia sẻ..." aria-label="Search">
                    <button class="btn btn-sm" style="background-color: #ccc">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
            </form>
        </div>
        <?php
        }
        ?>

        <!-- Sidebar Menu -->
        <nav class="mt-2" data-code-pos="ppp16768624318592">



            <ul class="nav nav-pills nav-sidebar text-sm flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <?php

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

                <li class="nav-item menu-open" data-code-pos="ppp1676864316081">

                        <?php
                        \App\Models\MenuTree::showMenuAdminLte(1);
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

