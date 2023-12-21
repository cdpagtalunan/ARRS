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
            <strong>List of requested reconciliation for addition</strong>
            @if ($type == 'Addition')
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
            @endif
        </div>
    </div>
    
  

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>
</html>