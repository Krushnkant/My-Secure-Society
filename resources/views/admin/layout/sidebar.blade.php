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
                        <a href="{{ route('admin.businesscategory.list') }}"><i class="ti-view-list-alt"></i><span class="nav-text">Business Category</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(7)))
                    <li>
                        <a href="{{ route('admin.society.list') }}"><i class="fa fa-building" aria-hidden="true"></i><span class="nav-text">Society</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(12)))
                    <li>
                        <a href="{{ route('admin.company.profile') }}"><i class="ti-id-badge"></i><span class="nav-text">Company Profile</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(11)))
                    <li>
                        <a href="{{ route('admin.subscriptionorder.list') }}"><i class="fa fa-first-order" aria-hidden="true"></i><span class="nav-text">Order</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(4)))
                    <li>
                        <a href="{{ route('admin.emergencycontact.list') }}"><i class="fa fa-address-book" aria-hidden="true"></i><span class="nav-text">Emergency Contact</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(13)))
                    <li>
                        <a href="{{ route('admin.servicevendor.list') }}"><i class="fa fa-wrench" aria-hidden="true"></i><span class="nav-text">Service Vendor</span></a>
                    </li>
                    @endif
                    @if(getUserDesignationId()==1 || (getUserDesignationId()!=1 && is_view(14)))
                    <li>
                        <a href="{{ route('admin.dailyhelpservice.list') }}"><i class="fa fa-question-circle" aria-hidden="true"></i><span class="nav-text">Daily Help Service</span></a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
