   <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="quixnav">
            <div class="quixnav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label first">Menu</li>
                    <li>
                        <a href="{{ route('admin.dashboard') }}"><i class="ti-home"></i><span class="nav-text">Dashboard</span></a>
                    </li>
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(1)))
                    <li>
                        <a href="{{ route('admin.designation.list') }}"><i class="ti-crown"></i><span class="nav-text">Designation</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(3)))
                    <li>
                        <a href="{{ route('admin.users.list') }}"><i class="ti-user"></i><span class="nav-text">Users</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(5)))
                    <li>
                        <a href="{{ route('admin.businesscategory.list') }}"><i class="ti-align-justify"></i><span class="nav-text">Business Category</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(7)))
                    <li>
                        <a href="{{ route('admin.society.list') }}"><i class="ti-align-justify"></i><span class="nav-text">Society</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(12)))
                    <li>
                        <a href="{{ route('admin.company.profile') }}"><i class="ti-align-justify"></i><span class="nav-text">Company Profile</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(12)))
                    <li>
                        <a href="{{ route('admin.subscriptionorder.list') }}"><i class="ti-align-justify"></i><span class="nav-text">Order</span></a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
