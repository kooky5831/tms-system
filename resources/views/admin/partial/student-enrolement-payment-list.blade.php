<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">{{$records->student->name}} - {{convertNricToView($records->student->nric)}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">{{$records->student->name}} - {{convertNricToView($records->student->nric)}} Payments List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Fees Amount</th>
                                        <th>Mode</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if( count($records->payments) )
                                    @foreach ($records->payments as $k => $d)
                                        <tr>
                                            <td>{{++$k}}</td>
                                            <td>{{$d->fee_amount}}</td>
                                            <td>{{getModeOfPayment($d->payment_mode)}}</td>
                                            <td>{{$d->payment_date}}</td>
                                            <td>
                                                @if( $d->status == 1 )
                                                <span class="badge badge-soft-danger">Cancelled</span>
                                                @else
                                                <span class="badge badge-soft-success">Paid</span>
                                                @endif
                                            </td>
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
