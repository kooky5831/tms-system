<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">Remarks List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Student</th>
                                        <th>Remarks</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if( count($records) )
                                        @foreach($records as $data)
                                        <tr>
                                            <td>{{ $data->id}}</td>
                                            <td>{{ $data->studentEnrol->student->name}}</td>
                                            <td>{{ $data->student_exam_remarks }}</td>
                                            <td>{{ Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                        </tr>                                               
                                        @endforeach
                                    @else 
                                        <tr><td colspan="5" align="center">No record found</td></tr>
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
