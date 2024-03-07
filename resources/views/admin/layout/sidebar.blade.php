   <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="quixnav">
            <div class="quixnav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label first">Menu</li>
                    <li>
                        <a href="{{ route('admin.dashboard') }}"><i class="icon icon-single-04"></i><span class="nav-text">Dashboard</span></a>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false"><i class="icon icon-app-store"></i><span class="nav-text">Pages</span></a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('admin.products') }}">Product</a></li>
                            <li><a href="{{ route('admin.login') }}">Login</a></li>
                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Email</a>
                                <ul aria-expanded="false">
                                    <li><a href="./email-compose.html">Compose</a></li>
                                    <li><a href="./email-inbox.html">Inbox</a></li>
                                    <li><a href="./email-read.html">Read</a></li>
                                </ul>
                            </li>
                            <li><a href="./app-calender.html">Calendar</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('admin.businesscategory.list') }}"><i class="icon icon-single-04"></i><span class="nav-text">Business Category</span></a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.list') }}"><i class="icon icon-single-04"></i><span class="nav-text">Users</span></a>
                    </li>
                    <li>
                        <a href="{{ route('admin.designation.list') }}"><i class="icon icon-single-04"></i><span class="nav-text">Designation</span></a>
                    </li>
                </ul>
            </div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->