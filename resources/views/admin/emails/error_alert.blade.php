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

    <p>There is a <strong>{{ $exception['name'] }}</strong> on Laravel Server.<br><br></p>

    <p><strong>Error</strong>: {{ $exception['message'] }}<br><br></p>

    <p><strong>File</strong>: {{ $exception['file'].":".$exception['line'] }}<br><br></p>

    <p><strong>Time</strong>: {{ date("Y-m-d H:i:s") }}<br><br></p>

    <p>Please do the needful.</p>

    <p>- TMS System</p>
</body>
</html>