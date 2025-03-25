<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('assets/css/invoice.css')}}">
</head>
<body>
    <table class="invoice-header">
        <tr class="invoice-header-details">
            <td class="invoice-header-logo">
                <img src="storage/invoice-image/@php echo $invoice_setting['invoice_logo'] @endphp" alt="logo" height="100"/>
            </td>
            
            <td rowspan="3"></td>
            <td class="invoice-header-address">
                {!! $invoice_setting['invoice_address'] !!}
            </td>
        </tr>
        <tr>
            <td rowspan="1" style="padding:10px 0;"></td>
        </tr>
    </table>
    <table style="width: 100%; float:left;">
        <tr>
            <td style="width:25%; vertical-align: baseline; text-align:left; font-size: 13px; line-height: 20px; font-family: 'Poppins', sans-serif; padding: 10px;">
                <strong style="color: #2f4686;">Invoice Date :</strong> {{$dates['invoice_date']}} <br> 
                <strong style="color: #375278;">Due Date :</strong> {{$dates['due_date']}}  <br> 
                <strong style="color: #2f4686;">Invoice Number :</strong> {{$invoice_data['invoice_number']}}</td>
            <td style="width:50%; vertical-align: baseline; text-align:left; font-size: 13px; line-height: 20px; font-family: 'Poppins', sans-serif; padding: 10px;">
                <strong style="color: #2f4686;">Bill To:</strong> 
                {{$comapany['comapany_uen']}}<br>
                {{$comapany['comapany_name']}}<br>
                {{$comapany['billing_address']}}
            </td>
            <td style="width:25%; vertical-align: baseline; text-align:left; font-size: 13px; line-height: 20px; font-family: 'Poppins', sans-serif; padding: 10px;">
                <strong style="color: #2f4686;">Equinet Academy Private Limited GST Registration Number:</strong> <br>201708981D</td>
        </tr>
    </table>
    <table style="width: 100%; float:left; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #2f4686;">
                <th style="font-size: 14px; text-align: left; color: #FFFFFF; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">Description</th>
                <th style="font-size: 14px; text-align: left; color: #FFFFFF; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">Quantity</th>
                <th style="font-size: 14px; text-align: left; color: #FFFFFF; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">Unit Price</th>
                <th style="font-size: 14px; text-align: left; color: #FFFFFF; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">Tax</th>
                <th style="font-size: 14px; text-align: left; color: #FFFFFF; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($itemlist as $values)
            <tr>
                <td style="text-align: left; font-size: 14px; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">{{$values['description']}}</td>
                <td style="text-align: left; font-size: 14px; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">{{$values['quantity']}}</td>
                <td style="text-align: left; font-size: 14px; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">@money($values['unit_amount'])</td>
                <td style="text-align: left; font-size: 14px; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">@money($values['tax_amount'])</td>
                <td style="text-align: left; font-size: 14px; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;">@money($values['line_amount'])</td>
            </tr>
        @endforeach
            <tr>
                <td colspan="3" style="text-align: left; padding: 10px;"></td>
                <td style="text-align: left; color: #2f4686; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>Sub Total</b></td>
                <td style="text-align: left; color: #2f4686; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>@money($amounts['sub_total'])</b></td>
            </tr>
            <tr>
                <th colspan="3" style="text-align: left; padding: 10px;"></th>
                <td style="text-align: left; color: #2f4686; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>Total GST</b></td>
                <td style="text-align: left; color: #2f4686; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>@money($amounts['total_tax'])</b></td>
            </tr>
            <tr class="bg-dark text-white">
                <th colspan="3" style="text-align: left; padding: 10px;"></th>
                <td style="text-align: left; color: #2f4686; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>Invoice Total SGD</b></td>
                <td style="text-align: left; color: #2f4686; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>@money($amounts['invoice_total_sgd'])</b></td>
            </tr>
            <tr class="bg-dark text-white">
                <th colspan="3" style="text-align: left; padding: 10px;"></th>
                <td style="text-align: left; color: #2f4686; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>Total Net Payment SGD</b></td>
                <td style="text-align: left; color: #2f4686; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>@money($amounts['total_net_payment_sgd'])</b></td>
            </tr>
            <tr style="background-color: #2f4686;">
                <th colspan="3" style="text-align: left; padding: 10px;"></th>
                <td style="font-size: 14px; text-align: left; color: #FFFFFF; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>Amount Due SGD</b></td>
                <td style="font-size: 14px; text-align: left; color: #FFFFFF; border: 1px solid #eaf0f7; font-family: 'Poppins', sans-serif; padding: 10px;"><b>@money($amounts['amount_sgd'])</b></td>
            </tr>
        </tbody>
    </table>
    @if(!empty($invoice_setting['payment_terms']))
        {!! $invoice_setting['payment_terms'] !!}
    @endif
    @if(!empty($invoice_setting['payment_methods']))
        {!! $invoice_setting['payment_methods'] !!}
    @endif

    <div class="payment-barcode">
        <img src="storage/invoice-image/@php echo $invoice_setting['invoice_qr'] @endphp" alt="qr_code" height="200"/>
    </div>
</body>
</html>
