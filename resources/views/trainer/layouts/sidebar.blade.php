<!-- Left Sidenav -->
<div class="left-sidenav">

    <div class="main-menu-inner">
        <div class="menu-body slimscroll">
            <div id="MetricaCRM" class="main-icon-menu-pane active">
                <h5 class="gray-title">Main Menu</h5>
                <ul class="nav metismenu" id="main_menu_side_nav">
                    <li class="nav-item"><a class="nav-link {{ request()->is('trainer/dashboard*') ? 'active' : '' }}" href="{{ route('trainer.dashboard') }}"><i class="dripicons-device-desktop"></i>Exam Settings</a></li>

                    <li class="nav-item"><a class="nav-link {{ request()->is('trainer/exam-settings*') ? 'active' : '' }}" href="{{ route('trainer.exam-settings.list')}}"><i class="dripicons-document-edit"></i>Exam Course Runs</a></li>

                    <li class="nav-item"><a class="nav-link {{ request()->is('trainer/course-resources*') ? 'active' : '' }}" href="{{ route('trainer.course-resources.index')}}"><i class="dripicons-folder-open"></i>Course Resources</a></li>
                </ul>
            </div><!-- end CRM -->

        </div><!--end menu-body-->
    </div><!-- end main-menu-inner-->
</div>
<!-- end left-sidenav-->
