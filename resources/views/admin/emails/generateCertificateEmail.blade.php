<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;"/>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE"/>
<title>TMS</title>
</head>
<body style="padding: 0; margin: 0;">
<p>Hello Admin!,</p>

@foreach($courseData as $course)
{{-- <p>The following trainees currently enrolled in {{$course['course_start_date']}} -  {{$course['course_end_date']}} {{$course['course_name']}} are on their final CDMS module:</p>  --}}
<p>The following trainees currently enrolled in {{$course['course_start_date']}} -  {{$course['course_end_date']}} {{$course['course_name']}} are on their final ({{$course['program_type_name']}}) module:</p>
<table border="1">
    <thead>
		<tr>
			<th>Student Id</th>
			<th>Student Name</th>
			<th>Student Email</th>
            <th>Student NRIC</th>
		</tr>
    </thead>
    <tbody>
        @foreach($course['students'] as $student)
            <tr>
                <td>{{ $student['id'] }}</td>
                <td>{{ $student['name'] }}</td>
                <td>{{ $student['email'] }}</td>
                <td>{{ $student['student_nric'] }}</td>
            </tr>
        @endforeach
    <tbody>
</table>
@endforeach
{{-- <p>Please prepare their CDMS certificate.<p> --}}
<p>Please prepare their course certificate.<p>
<p>- TMS System</p>
</body>
</html>