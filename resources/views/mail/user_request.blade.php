<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


</head>
<style>
    #tableAdd td, th{
        border:1px solid black;
    }
    table {
        /* width: 100%; */
        border-collapse: collapse;
    }
</style>
<body>
    <div class="row">
        <div class="col-sm-12">
            Good Day!
            <br>
            Please be informed that you have reconciliation for <strong>{{ $type }}</strong>.
            <br>
            Below are the reconciliation information:
            <hr>
           <strong><i>Reconciliation Details:</i></strong>
            <table style="width: 40%;">
                <tbody>
                    <tr>
                        <td><strong>ARRS Control Number:</strong></td>
                        <td>{{ $control }}</td>
                    </tr>
                    <tr>
                        <td><strong>Requestor remarks:</strong></td>
                        <td>{{ $user_remarks }}</td>
                    </tr>
                    <tr>
                        <td><strong>Requestor:</strong></td>
                        <td>{{ $requestor }}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            {{-- Addition Email Template --}}
            @if ($type == 'Addition') 
                <strong>List of requested reconciliation for addition</strong>

                <table id="tableAdd" class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>PR Number</th>
                            <th>Invoice Number</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Supplier</th>
                            <th>Classification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($x = 0; $x < count($add_request_data); $x++)
                            @php
                                $data = json_decode($add_request_data[$x]);
                            @endphp
                            <tr>
                                <td>{{ $data->reference_po_number }}</td>
                                <td>{{ $data->po_number }}</td>
                                <td>{{ $data->other_reference }}</td>
                                <td>{{ $data->item_code }}</td>
                                <td>{{ $data->item_name }}</td>
                                <td>{{ $data->description }}</td>
                                <td>{{ $data->supplier_name }}</td>
                                <td>{{ $data->classification_code }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            {{-- Removal Email Template --}}
            @else
                <i>Reconciliation data for Removal:</i>
                <table  style="width: 100%;">
                    <tr>
                        <td><strong>PO Date:</strong></td>
                        <td>{{ $remove_request_data->po_date }}</td>
                        <td><strong>PO Number:</strong></td>
                        <td>{{ $remove_request_data->po_num }}</td>
                    </tr>
                    <tr>
                        <td><strong>PR Number:</strong></td>
                        <td>{{ $remove_request_data->pr_num }}</td>
                        <td><strong>Received Number:</strong></td>
                        <td>{{ $remove_request_data->rcv_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>Code:</strong></td>
                        <td>{{ $remove_request_data->prod_code }}</td>
                        <td><strong>Name:</strong></td>
                        <td>{{ $remove_request_data->prod_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Supplier:</strong></td>
                        <td>{{ $remove_request_data->supplier }}</td>
                        <td><strong>Description:</strong></td>
                        <td>{{ $remove_request_data->prod_desc }}</td>
                    </tr>
                    <tr>
                        <td><strong>Unit Price:</strong></td>
                        <td>{{ $remove_request_data->unit_price }}</td>
                        <td><strong>Currency:</strong></td>
                        <td>{{ $remove_request_data->currency }}</td>
                    </tr>
                    <tr>
                        <td><strong>Received Qty:</strong></td>
                        <td>{{ $remove_request_data->received_qty }}</td>
                        <td><strong>UOM:</strong></td>
                        <td>{{ $remove_request_data->uom }}</td>
                    </tr>
                    <tr>
                        <td><strong>PO Balance:</strong></td>
                        <td>{{ $remove_request_data->po_balance }}</td>
                        <td><strong>Delivery Date:</strong></td>
                        <td>{{ $remove_request_data->delivery_date }}</td>
                    </tr>
                    <tr>
                        <td><strong>Received Date:</strong></td>
                        <td>{{ $remove_request_data->received_date }}</td>
                        <td><strong>Person-in-charge:</strong></td>
                        <td>{{ $remove_request_data->pic }}</td>
                    </tr>
                    <tr>
                        <td><strong>Received By:</strong></td>
                        <td>{{ $remove_request_data->received_by }}</td>
                        <td><strong>Invoice Number:</strong></td>
                        <td>{{ $remove_request_data->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>Classification:</strong></td>
                        <td>{{ $remove_request_data->classification }}</td>
                        <td><strong>Allocation:</strong></td>
                        <td>{{ $remove_request_data->allocation }}</td>
                    </tr>
                </table>
            @endif

            <hr>
            <br>
            <div class="col-sm-12">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label">For more info, please log-in to your Rapidx account. Go to http://rapidx/ and ARRS </label>
                </div>
            </div>

            <br>
            <br>

            <div class="col-sm-12">
                <div class="form-group row">
                    <label class="col-sm-12 col-form-label"><b> Notice of Disclaimer: </b></label>
                    <br>
                    <label class="col-sm-12 col-form-label"> <i> This message contains confidential information intended for a specific individual and purpose. If you are not the intended recipient, you should delete this message. Any disclosure,copying, or distribution of this message, or the taking of any action based on it, is strictly prohibited.</i></label>
                </div>
            </div>

            <div class="col-sm-12">
                <br><br>
                <label style="font-size: 18px;"><b>For concerns on using the form, please contact ISS at local numbers 205, 206, or 208. You may send us e-mail at <a href="mailto: servicerequest@pricon.ph">servicerequest@pricon.ph</a></b></label>
            </div>

        </div>
    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>
</html>