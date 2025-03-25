<!-- Left Sidenav -->
<div class="left-sidenav">
    <div class="main-menu-inner">
        <div class="menu-body slimscroll">
            <div id="MetricaCRM" class="main-icon-menu-pane active">
                <h5 class="gray-title">Dashboard</h5>
                <ul class="nav metismenu" id="main_menu_side_nav">
                    <li class="nav-item"><a class="nav-link {{ request()->is('assessment/dashboard*') ? 'active' : '' }}" href="{{route('student.assessment.dashboard')}}"><i class="dripicons-pamphlet"></i>My Exams</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('assessment/course-resources*') ? 'active' : '' }}" href="{{route('student.course-resources.assessment.courseresource')}}"><i class="dripicons-pamphlet"></i>Course Resource</a></li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('assessment/feedback*') ? 'active' : '' }}" href="{{route('student.assessment.feedback')}}"><i class="fas fa-comment-dots"></i>Feedback</a>
                    </li>
                    <div class="gray-line"></div>
                </ul>
            </div><!-- end CRM -->
        </div><!--end menu-body-->
    </div><!-- end main-menu-inner-->
</div>
<!-- end left-sidenav-->
