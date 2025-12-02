<style>
    [class*=sidebar-dark-] {

        background-color: #303030;
    }
    [class*=sidebar-dark] .brand-link, [class*=sidebar-dark] .brand-link .pushmenu {
        background: #dc3545!important;
    }
    [class*=sidebar-dark] .brand-link {
        border-bottom: 0px;
    }

</style>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-cyan elevation-4" data-code-pos="ppp1681392990292">


    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="/adminlte/dist/img/AdminLTELogo.png" alt="GLX Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light" data-code-pos="ppp1679890174525">
            <?php
            echo \App\Models\SiteMng::getAppName()
            ?>
        </span>
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
        <nav data-code-pos="ppp1679323461186" class="mt-2">
            <ul class="nav nav-pills nav-sidebar text-sm  flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->


                <?php

                if(isAdminACP_()){
                    ?>
                <li class="nav-item">
                    <a href="/admin" class="nav-link" style="color: white">
                        <i class="nav-icon fa fa-info"></i>
                        <p style="">ACP</p>
                    </a>
                </li>
                    <?php
                }
                ?>


                <li class="nav-item menu-open">
                <?php
                \App\Models\MenuTree::showMenuAdminLte(4);

                if(0)
                if(getGidCurrent_() == 1){
                ?>

                <!-- MongoDB CRUD Menu -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            MongoDB CRUD
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('mongocrud.dashboard') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('mongocrud.demo01.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Demo01 Records</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('mongocrud.demo01.create') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Create Record</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/api/mongo-crud/test" target="_blank" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>API Test</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{route("admin.demo.index")}}" class="nav-link">
                        <p>============</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route("admin.demo.index")}}" class="nav-link">
                        <p>Demo table</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route("admin.demo-api.index")}}" class="nav-link">
                        <p>Demo table - api - grid </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route("admin.demo-tag.index")}}" class="nav-link">
                        <p>Demo and Tags</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route("admin.demo-folder.index")}}" class="nav-link">
                        <p>Demo Folder </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route("admin.demo-folder.index")}}" class="nav-link">
                        <p>Demo Tree Folder </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route("admin.user.index")}}" class="nav-link">
                        <p>Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route("admin.user-api.index")}}" class="nav-link">
                        <p>Users Api Grid</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route("admin.role.index")}}" class="nav-link">

                        <p>Roles</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route("admin.menu.index")}}" class="nav-link">
                        <p>Menu</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route("admin.menu-tree.tree")}}" class="nav-link">
                        <p>Menu Tree</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route("admin.product.index")}}" class="nav-link">

                        <p>Product</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route("tool.auto-insert-route-permission")}}" class="nav-link">
                        <p>Tool - Insert permission</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{route("admin.site-mng.index")}}" class="nav-link">
                        <p>Site Managerment</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{route("admin.db-permission")}}" class="nav-link">
                        <p>DB-Permission</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{route("admin.categories.index")}}" class="nav-link">

                        <p>Catetory</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route("admin.demogate.index")}}" class="nav-link">

                        <p>Demo-Gate</p>
                    </a>
                </li>


                <?php
                }
                ?>
                <li class="nav-item">
                    <a href="/logout" class="nav-link">
                        <p> &nbsp; [Logout]</p>
                    </a>
                </li>
            </ul>
            </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<script>

    window.addEventListener('load',function (){

        var uri = window.location.pathname;
        var uriWithoutParams = uri.split('?')[0];

        console.log("uriWithoutParams = ", uriWithoutParams);

        console.log("window.onload...........  ");
        setTimeout(function (){
            document.getElementById('_menu_' + uriWithoutParams)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },1000)

    });

</script>
