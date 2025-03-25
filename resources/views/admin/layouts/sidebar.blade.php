<!-- Left Sidenav -->
<div class="left-sidenav">

    <div class="main-menu-inner">
        <div class="menu-body slimscroll">
            <div id="MetricaCRM" class="main-icon-menu-pane active">
                <h5 class="gray-title">Main Menu</h5>
                <ul class="nav metismenu" id="main_menu_side_nav">
                    <li class="nav-item"><a class="nav-link {{ request()->is('admin') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="dripicons-home"></i>Dashboard</a></li>
                    
                    <div class="gray-line"></div>
                    <h5 class="gray-title">Courses</h5>

                    <li class="nav-item {{ request()->is('admin/coursemain*') ? 'mm-active' : '' }} {{ request()->is('admin/program-type/*') ? 'mm-active' : '' }} {{ request()->is('admin/coursetrigger/*') ? 'mm-active' : '' }}">
                        <a class="nav-link {{ request()->is('admin/coursemain*') ? 'active' : '' }}" href="javascript:void(0)"><i class="dripicons-pamphlet"></i><span class="w-100">Course Mains</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            @can('coursemain-list')
                            <li><a class="{{ request()->is('admin/coursemain') ? 'active' : '' }} {{ request()->is('admin/coursemain/edit/*') ? 'active' : '' }}" href="{{ route('admin.coursemain.list') }}">All Courses</a></li>
                            @can('programtype-list')
                            <li><a class="{{ request()->is('admin/program-type/*') ? 'active' : '' }}" href="{{ route('admin.programtype.list') }}">Program Type</a></li>
                            @endcan
                            <li><a class="{{ request()->is('admin/coursemain/add') ? 'active' : '' }}" href="{{ route('admin.coursemain.add') }}">Add Course</a></li>
                            @endcan
                            @can('coursetriggers-list')
                            <li><a class="{{ request()->is('admin/coursetrigger/*') ? 'active' : '' }}" href="{{ route('admin.coursetrigger.list') }}">All Triggers</a></li>
                            @endcan
                        </ul>
                    </li>
                    <li class="nav-item {{ request()->is('admin/courserun*') ? 'mm-active' : '' }}">
                        <a class="nav-link {{ request()->is('admin/courserun*') ? 'active' : '' }}" href="javascript:void(0)"><i class="dripicons-pamphlet"></i><span class="w-100">Course Runs</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            @can('course-list')
                            <li><a class="{{ request()->is('admin/courserun') ? 'active' : '' }}" href="{{ route('admin.course.listall') }}">All Course Runs</a></li>
                            <li><a class="{{ request()->is('admin/courserun/completed') ? 'active' : '' }}" href="{{ route('admin.course.listallcompleted') }}">Completed Course Runs</a></li>
                            @endcan
                            @can('course-add')
                            <li><a class="{{ request()->is('admin/courserun/add*') ? 'active' : '' }}" href="javascript:void(0)" id="add_course_run_menu">Add Course Runs</a></li>
                            @endcan
                            @can('softbooking-list')
                            <li><a class="{{ request()->is('admin/softbooking*') ? 'active' : '' }}" href="{{ route('admin.softbooking.list') }}">Soft Bookings</a></li>
                            @endcan
                            @can('waitinglist-list')
                            <li><a class="{{ request()->is('admin/waiting-list*') ? 'active' : '' }}" href="{{ route('admin.waitinglist.list') }}">Waiting List</a></li>
                            @endcan
                        </ul>
                    </li>

                    <div class="gray-line"></div>

                    @if( Auth::user()->can('manage-staff-users') || Auth::user()->can('trainer-list'))
                    <li class="nav-item {{ request()->is('admin/user*') ? 'mm-active' : '' }}">
                        <a class="nav-link {{ request()->is('admin/user*') ? 'active' : '' }}" href="javascript:void(0)"><i class="dripicons-user-group"></i><span class="w-100">Users</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            @if( Auth::user()->role == 'superadmin' )
                            <li><a class="{{ request()->is('admin/user/superadmin*') ? 'active' : '' }}" href="{{ route('admin.user.superadmin') }}">Managers</a></li>
                            @endif
                            @can('manage-staff-users')
                            <li><a class="{{ request()->is('admin/user/staff*') ? 'active' : '' }}" href="{{ route('admin.user.admin') }}">Staff</a></li>
                            @endcan
                            @can('trainer-list')
                            <li><a class="{{ request()->is('admin/user/trainer*') ? 'active' : '' }}" href="{{ route('admin.user.trainer') }}">Trainer</a></li>
                            @endcan
                        </ul>
                    </li>
                    @can('venue-list')
                    <li class="nav-item"><a class="nav-link {{ request()->is('admin/venue*') ? 'active' : '' }}" href="{{ route('admin.venue.list') }}"><i class="dripicons-location"></i>Venue</a></li>
                    @endcan

                    @can('studentenrolment-list')
                    <li class="nav-item"><a class="nav-link {{ request()->is('admin/studentenrolment*') ? 'active' : '' }}" href="{{ route('admin.studentenrolment.list') }}"><i class="dripicons-to-do"></i>Student Enrolment</a></li>
                    @endcan

                    @can('students-list')
                    <li class="nav-item"><a class="nav-link {{ request()->is('admin/students*') ? 'active' : '' }}" href="{{ route('admin.students.list') }}"><i class="dripicons-user"></i>Students</a></li>
                    @endcan

                    @can('payment-list')
                    <li class="nav-item"><a class="nav-link {{ request()->is('admin/payment*') ? 'active' : '' }}" href="{{ route('admin.payment.list') }}"><i class="dripicons-card"></i>Payments</a></li>
                    @endcan

                    @can('reports')
                    <li class="nav-item {{ request()->is('admin/reports/*') ? 'mm-active' : '' }}">
                        <a class="nav-link {{ request()->is('admin/reports/*') ? 'active' : '' }}" href="javascript:void(0)"><i class="mdi mdi-file-document-box-multiple"></i><span class="w-100">Reports</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a class="{{ request()->is('admin/reports/course-registration') ? 'active' : '' }}" href="{{ route('admin.reports.courseregistration') }}">Course Registration</a></li>
                            <li><a class="{{ request()->is('admin/reports/student-details') ? 'active' : '' }}" href="{{ route('admin.reports.studentdetails') }}">Student Details</a></li>
                            <li><a class="{{ request()->is('admin/reports/refresher-details') ? 'active' : '' }}" href="{{ route('admin.reports.refresherdetails') }}">Refresher</a></li>
                            <li><a class="{{ request()->is('admin/reports/course-signups') ? 'active' : '' }}" href="{{ route('admin.reports.courseSignups') }}">Course Signups Monthly</a></li>
                            @if( Auth::user()->role == 'superadmin' )
                            <li><a class="{{ request()->is('admin/reports/course-run-exports') ? 'active' : '' }}" href="{{ route('admin.reports.courserunexports') }}">Course Run Exports</a></li>
                            @endif
                            <li><a class="{{ request()->is('admin/reports/payment-report-list') ? 'active' : '' }}" href="{{ route('admin.reports.paymentreport') }}">Payment Tracker</a></li>

                            <!-- Grant Menu Item -->
                            <li><a class="{{ request()->is('admin/grants') ? 'active' : '' }}" href="{{ route('admin.grants.list') }}">Grants Details</a></li>
                            <!-- Grant Menu Item -->

                        </ul>
                    </li>
                    @endcan
                    
                    @can('exam-settings')
                    <li class="nav-item {{ request()->is('admin/assessments/*') ? 'mm-active' : '' }}">
                        <a class="nav-link {{ request()->is('admin/assessments/*') ? 'active' : '' }}" href="javascript:void(0)">
                            <i class="dripicons-device-desktop"></i><span class="w-100">Assessments</span>
                            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a class="{{ request()->is('admin/assessments/exam-settings*') ? 'active' : '' }}" href="{{ route('admin.assessments.exam-settings.list')}}">Exam Settings</a></li>
                            <li><a class="{{ request()->is('admin/assessments/examdashboard*') ? 'active' : '' }}" href="{{ route('admin.assessments.examdashboard.examdashboard')}}">Exam Course Runs</a></li>
                        </ul>
                    </li>
                    @endcan

                    @can('course-resources')
                        <li class="nav-item"><a class="nav-link {{ request()->is('admin/course-resources*') ? 'active' : '' }}" href="{{ route('admin.course-resources.index')}}">
                        <i class="dripicons-folder-open"></i>Course Resources</a></li>
                    @endcan

                    @if( Auth::user()->role == 'superadmin' )
                    <li class="nav-item {{ request()->is('admin/data-import/*') ? 'mm-active' : '' }}">
                        <a class="nav-link {{ request()->is('admin/data-import/*') ? 'active' : '' }}" href="javascript:void(0)"><i class="mdi mdi-import"></i><span class="w-100">Data Import</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a class="{{ request()->is('admin/data-import/course-runs*') ? 'active' : '' }}" href="{{ route('admin.dataImport.courseRun') }}">Course Run</a></li>
                            <li><a class="{{ request()->is('admin/data-import/student-enrolment*') ? 'active' : '' }}" href="{{ route('admin.dataImport.studentEnrolment') }}">Student Enrolment</a></li>
                            <li><a class="{{ request()->is('admin/data-import/sync-tpg-courseruns') ? 'active' : '' }}" href="{{ route('admin.dataImport.syncTpgCourseRuns') }}">Sync Course Run - TPG</a></li>
                            <li><a class="{{ request()->is('admin/data-import/sync-tpg-studentenrolment') ? 'active' : '' }}" href="{{ route('admin.dataImport.syncTpgStudentEnrolment') }}">Sync Student Enrolment - TPG</a></li>
                        </ul>
                    </li>
                    @endif

                    @if( Auth::user()->role == 'superadmin' )
                    <li class="nav-item {{ request()->is('admin/staff-permission*') ? 'mm-active' : '' }} {{ request()->is('admin/email-templates*') ? 'mm-active' : '' }} {{ request()->is('admin/sms-templates*') ? 'mm-active' : '' }} {{ request()->is('admin/course-tags/*') ? 'mm-active' : '' }} {{ request()->is('admin/course-feedback/*') ? 'mm-active' : '' }}">
                        <a class="nav-link {{ request()->is('admin/staff-permission*') ? 'active' : '' }}" href="javascript:void(0)"><i class="dripicons-gear"></i><span class="w-100">Settings</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a class="{{ request()->is('admin/staff-permission*') ? 'active' : '' }}" href="{{ route('admin.user.admin.permission') }}">Staff Permissions</a></li>
                            <li><a class="{{ request()->is('admin/email-templates/*') ? 'active' : '' }}" href="{{ route('admin.emailtemplates.list') }}">Email Templates</a></li>
                            <li><a class="{{ request()->is('admin/sms-templates/*') ? 'active' : '' }}" href="{{ route('admin.smstemplates.list') }}">SMS Templates</a></li>
                            <li><a class="{{ request()->is('admin/course-tags/*') ? 'active' : '' }}" href="{{ route('admin.coursetags.list') }}">Course Tags</a></li>
                            <li><a class="{{ request()->is('admin/manage/xero*') ? 'active' : '' }}" href="{{ route('admin.xero.auth.success') }}">Xero Connection</a></li>
                            <li><a class="{{ request()->is('admin/invoice-settings*') ? 'active' : '' }}" href="{{route('admin.invoicesettings.get.settings')}}">Invoice Settings</a></li>
                            <li><a class="{{ request()->is('admin/course-feedback*') ? 'active' : '' }}" href="{{route('admin.course-feedback.edit.settings')}}">Course Feedback Setting</a></li>
                            <!-- Xero theme setting -->
                            @can('xero-theme-setting')
                            <li><a class="{{ request()->is('admin/xero/set-xero-theme') ? 'active' : '' }}" href="{{ route('admin.xero.set-xero-theme') }}">Xero Theme Settings</a></li>
                            @endcan
                            <!-- Xero theme setting -->
                        </ul>
                    </li>
                    @endif
                    @endif
                    <li class="nav-item {{ request()->is('admin/activities') ? 'mm-active' : '' }} {{ request()->is('admin/tasks') ? 'mm-active' : '' }}">
                        <a class="nav-link {{ request()->is('admin/activities') ? 'active' : '' }} {{ request()->is('admin/tasks') ? 'active' : '' }}" href="javascript:void(0)"><i class="dripicons-lightbulb"></i><span class="w-100">Logs</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            @can('activities')
                            <li><a class="{{ request()->is('admin/activities') ? 'active' : '' }}" href="{{ route('admin.activities.list') }}">Activities</a></li>
                            @endcan
                            <li><a class="{{ request()->is('admin/tasks') ? 'active' : '' }}" href="{{ route('admin.tasks.list') }}">Tasks</a></li>
                            <li><a class="{{ request()->is('admin/maillogs') ? 'active' : '' }}" href="{{ route('admin.maillogs.list') }}">Email Logs</a></li>
                            {{-- <li><a class="{{ request()->is('admin/errors') ? 'active' : '' }}" href="{{ route('admin.errors.list') }}">Errors</a></li> --}}
                            <li><a class="{{ request()->is('admin/grant') ? 'active' : '' }}" href="{{ route('admin.grant.list') }}">Grant Activities</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- end CRM -->

        </div><!--end menu-body-->
    </div><!-- end main-menu-inner-->
</div>
<!-- end left-sidenav-->
