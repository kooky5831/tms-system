<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$result->courseMain->name}}</title>
    <style>
        {{-- @page :first {
            header: html_coverletterheader;
            margin-top: 70mm;
        } --}}
        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body style="font-family: 'firasansfont', sans-serif; font-size: 14px;">
    {{-- <sethtmlpageheader name="coverletterheader" value="on" show-this-page="1" /> --}}
    {{-- <sethtmlpagefooter name="coverletterfooter" value="on" show-this-page="1" /> --}}
    {{-- TO TURN HEADER/FOOTER OFF FOR A NEW PAGE --}}
    {{-- <pagebreak odd-header-value="off" odd-footer-value="off" />  --}}
    {{-- <htmlpageheader name="coverletterheader" style="display:none;">
        <table>
            <tr>
                <td width="20%">
                    <img style="width: 200px;text-align: left;" src="{{ public_path('assets/images/logoicon.png') }}" alt="{{env('APP_NAME')}}" />
                </td>
                <td width="80%" style="vertical-align: top;text-align: center;">
                    <h1>{{config('settings.pdfSignature')}}</h1>
                    <br/>
                    <h2>2010 Winston Park Drive, Suite 200. Oakville, Ontario. L6H 5R7, Canada</h2>
                    <h2>Phone: (877) 251-0077. Fax: (888) 444-0222</h2>
                    <h2>Email: operations@meiracare.com  Web: www.meiracare.com </h2>
                </td>
            </tr>
        </table>
    </htmlpageheader> --}}
    {{-- <htmlpagefooter name="coverletterfooter" style="display:none;">
        <table>
            <tr>
                <td style="vertical-align: bottom;text-align: center;">
                    <h3>Confidential: This communication is intended only for the individual or institution to which it is addressed and should not be distributed, copied or disclosed to anyone else. The documents in this communication may contain personal, confidential or privileged information, which may be subject to the Freedom of information and Protection of Privacy Act, the Health Information Act and other legislation. If you have error, please notify our office immediately and delete the documents from your system. Thank you for your co-operation and assistance.</h3>
                </td>
            </tr>
        </table>
    </htmlpagefooter> --}}
    <table style="width: 100%;border: none;border-collapse: collapse;">
        <tr>
            <td style="width:65%;border: none;text-align:left;">
                <p><strong>Equinet Academy {{$result->courseMain->name}}</strong></p>
                <p>Venue: {{$result->venue->street}} - {{$result->venue->postal_code}}</p>
                <p>Date: {{$courseRunName}}</p>
                <p>Trainer: {{$result->maintrainerUser->name}}</p>
                <p style="color: red;"><strong>Note: Please do not use correction fluid or tape.</strong></p>
            </td>
            <td style="width:35%;border: none;text-align:right;vertical-align: bottom;">
                <table style="border: none;">
                    <tr style="border: none;">
                        <td style="border: none;"><strong>Total Training Hours: </strong></td>
                        <td style="width:50%;min-width: 50%;border: none;text-align: right;">{{$totalHours}}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none;"><strong>Total Head Count: </strong></td>
                        <td style="width:50%;min-width: 50%;border: none;border-bottom: 1px solid #000;"></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none;"><strong>Trainer's Signature: </strong></td>
                        <td style="width:50%;min-width: 50%;border: none;border-bottom: 1px solid #000;"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br />
    <table style="width: 100%;border-collapse: collapse;">
        <tr>
            <thead>
                <th style="padding:5px;text-align: right;border:none;">Sr. No</th>
                <th style="padding:5px;text-align: center;background-color: #FFFFE0;">Name</th>
                <th style="padding:5px;text-align: center;background-color: #FFFFE0;">Company</th>
                <th style="padding:5px;text-align: center;background-color: #FFFFE0;">NRIC</th>
                @foreach ($result->session as $k => $session)
                    <th style="padding:5px;text-align: center;background-color: #aaf0d1;font-size: 13px;">Session {{ $k + 1 }} <br/>{{date('d M Y', strtotime($session->start_date))}} <br/>({{ date('ha', strtotime($session->start_time)) }} - {{ date('ha', strtotime($session->end_time)) }})</th>
                @endforeach
            </thead>
            <tbody>
                <?php $c = 1; ?>
                @foreach ($result->courseActiveEnrolments as $e => $enrolment)
                    <tr>
                        <td style="width: 3%;padding:5px;text-align: right;border:none;border-left: none;">{{ $c++ }}</td>
                        <td style="width: 15%;padding:5px;text-align: center;">{{ $enrolment->student->name }}</td>
                        <td style="width: 15%;padding:5px;text-align: center;">{{ empty($enrolment->company_name) ? 'Nil' : $enrolment->company_name }}</td>
                        <td style="width: 12%;padding:5px;text-align: center;">{{ convertNricToView($enrolment->student->nric) }}</td>
                        @foreach ($result->session as $s => $session)
                            <td style="height: 45px;"></td>
                        @endforeach
                    </tr>
                @endforeach
                @foreach ($result->courseActiveRefreshers as $e => $enrolment)
                    @if( $enrolment->isAttendanceRequired )
                        <tr>
                            <td style="width: 3%;padding:5px;text-align: right;border:none;border-left: none;">{{ $c++ }}</td>
                            <td style="width: 15%;padding:5px;text-align: center;">{{ $enrolment->student->name }}</td>
                            <td style="width: 15%;padding:5px;text-align: center;">Refresher</td>
                            <td style="width: 12%;padding:5px;text-align: center;">{{ convertNricToView($enrolment->student->nric) }}</td>
                            @foreach ($result->session as $s => $session)
                                <td style="height: 45px;"></td>
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </tr>
    </table>
</body>
</html>
