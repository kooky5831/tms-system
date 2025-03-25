<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">{{$student->name}} - {{convertNricToView($student->nric)}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">{{$student->name}} - {{convertNricToView($student->nric)}} Activity List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table" id="activity-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if( count($revisions) )
                                        @foreach ($revisions as $k => $revision)
                                            <tr>
                                                <td>{{date('d M Y', strtotime($revision->created_at))}}</td>
                                                {{-- <td>{{getModuleNameByType($revision->revisionable_type)}}</td> --}}
                                                <td>
                                                @if($revision->key =='created_at' && !$revision->old_value)
                                                    @php echo 'Student Added' @endphp
                                                    @else
                                                        

                                                        @if(!empty($revision->old_value))
                                                            @php echo str_replace("_"," ",ucfirst($revision->key)).' of '.$student->name .' was changed by '.getAdminNameById($revision->user_id).' from '.$revision->old_value. ' to ' .$revision->new_value; @endphp
                                                        @else
                                                            @php echo str_replace("_"," ",ucfirst($revision->key)).' of '.$student->name .' was set to '.$revision->new_value.' by '.getAdminNameById($revision->user_id); @endphp
                                                        @endif

                                                @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @else
                                            <tr><td colspan="5" align="center">No Activities found</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
    </div>
</div>
