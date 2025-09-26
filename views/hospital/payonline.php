<?php

$session = Yii::$app->session;
$membersId = $session->get('members_id');
// echo $membersId;exit;
?>

<html>

<head>
    <title>Pay Online</title>
    <link rel="icon" type="image/x-icon" href="img/icons/payonline.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>
    <script src="https://use.fontawesome.com/162583bb50.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @font-face {
            font-family: 'Raleway';
            src: url('mpassets/fonts/Raleway.ttf');
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-line-pack: center;
            align-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            min-height: 100vh;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            font-family: 'Raleway';
        }

        .payment-title {

            width: 100%;

            text-align: center;
            background-color: #2C3E50;
            margin-bottom: 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
        }

        .title-main {
            font-family: serif;
            color: #9E7D32;
            font-size: 30px;
            font-weight: 100;
            margin-left: 30px;
        }

        .title-sub {
            color: white;
            font-size: 25px;
            font-weight: 80;
        }

        .form-container .field-container:first-of-type {
            grid-area: name;
        }

        .form-container .field-container:nth-of-type(2) {
            grid-area: number;
        }

        .form-container .field-container:nth-of-type(3) {
            grid-area: expiration;
        }

        .form-container .field-container:nth-of-type(4) {
            grid-area: security;
        }

        .field-container input {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .field-container {
            position: relative;
        }

        .form-container {
            display: grid;
            grid-column-gap: 10px;
            /*grid-template-columns: auto auto;*/
            grid-template-rows: 230px 90px 90px;
            /*grid-template-areas: "name name""number number""expiration security";*/
            max-width: 800px;
            width: 800px;
            padding: 20px;
            color: #707070;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            border-radius: 8px;
            padding: 40px;
        }

        label {
            padding-bottom: 5px;
            font-size: 13px;
        }

        input {
            margin-top: 3px;
            padding: 15px;
            font-size: 16px;
            width: 100%;
            border-radius: 3px;
            border: 1px solid #dcdcdc;
        }

        .ccicon {
            height: 38px;
            position: absolute;
            right: 6px;
            top: calc(50% - 17px);
            width: 60px;
        }

        /* CREDIT CARD IMAGE STYLING */
        .preload * {
            -webkit-transition: none !important;
            -moz-transition: none !important;
            -ms-transition: none !important;
            -o-transition: none !important;
        }

        .container {
            width: 100%;
            max-width: 400px;
            max-height: 251px;
            height: 54vw;
            padding: 20px;
        }

        #ccsingle {
            position: absolute;
            right: 15px;
            top: 20px;
        }

        #ccsingle svg {
            width: 100px;
            max-height: 60px;
        }

        /* FLIP ANIMATION */
        .container {
            perspective: 1000px;
        }

        #generatecard {
            cursor: pointer;
            float: right;
            font-size: 12px;
            color: #fff;
            padding: 2px 4px;
            background-color: #909090;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }

        @media only screen and (max-width: 600px) {
            .main-logo {
                margin-top: 0px !important;
            }

            .form-container {
                display: flex !important;
                margin: 10px !important;
            }
        }

        @media only screen and (max-width: 768px) {
            .main-logo {
                margin-top: 0px !important;
            }

            .form-container {
                display: flex !important;
                margin: 10px !important;
            }
        }

        @media only screen and (min-width: 768px) {
            .main-logo {
                margin-top: -100px !important;
            }
        }

        .fixed-message {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            background-color: #ffcc00;
            color: #000;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            z-index: 1000;
            /* ensures it stays above other content */
            width: auto;
        }

        /* Make the radio buttons smaller */
        .small-radio {
            width: 18px;
            /* Adjust the size of the radio button */
            height: 18px;
            /* Adjust the size of the radio button */
            padding: 0;
            /* Remove padding */
        }

        /* Align text with the bottom of the radio button */
        .form-check {
            display: flex;
            align-items: flex-end;
            /* Align text to the bottom of the radio button */
        }

        /* Optional: Adjust the font size of the label for smaller text */
        .form-check-label {
            font-size: 14px;
            /* Adjust the font size as needed */
        }
    </style>
</head>

<body style="background: transparent;">
    <script>
        $(document).ready(function() {
            $(".chosen-select").chosen({
                no_results_text: "Oops, nothing found!",
                width: "100%" // Adjust as needed
            });
        });
    </script>
    <?php
    $conn = Yii::$app->db;
    $mp_sql = "SELECT  acc.id,mp.id as mpid,mp.plotno FROM memberplot mp
    LEFT JOIN accounts acc ON (acc.ref = mp.id AND acc.type = 1)
    WHERE mp.member_id = '" . $membersId . "'";
    $mp_res = $conn->createCommand($mp_sql)->queryAll();

    $mem_sql = "SELECT m.name,m.cnic,m.email,m.cnic,m.phone  FROM members m
    WHERE id = '" . $membersId . "'";
    $mem_res = $conn->createCommand($mem_sql)->queryOne();

    $get_sys_info_sql = "SELECT companyname, logo FROM config";
    $get_sys_info_res = $conn->createCommand($get_sys_info_sql)->queryOne();

    // $merchantid = "04760";
    // $secret = "xWX+A8qbYkLgHf3e/pu6PZiycOGc0C/YXOr3XislvxI=";
    // $secret = "DAC/IRuouNz02phq5/wrdGRfQLgRbAvrv0mvR29URmc=";

    $merchantid = "06690";
    // $secret = "xWX+A8qbYkLgHf3e/pu6PZiycOGc0C/YXOr3XislvxI=";
    $secret = "xhcS3IJn6W0fZvJiWmyEA739PWFbbFwL3oIFZpwnOCI=";

    $ResOrderID = $_GET['OrderId'];
    $TransactionId = $_GET['TransactionId'];
    $ResponseCode = $_GET['ResponseCode'];
    $Signature = $_GET['Signature'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // print_r($_REQUEST);
        // exit;
        $strings = implode(",", $_REQUEST['pay_against']);
        $installments = explode(",", $strings);
        // print_r( $installments);


        // exit;
        // $string = explode("-", $installments);
        $results = []; // Initialize an array to hold the results

        foreach ($installments as $installment) {
            // Split each installment by the hyphen
            $parts = explode("-", $installment);

            // Check if there is a part after the hyphen and trim it
            if (isset($parts[1])) {
                $results[] = trim($parts[1]); // Add the trimmed part to results
            }
        }

        // Convert results to a comma-separated string if needed
        $resultString = implode(",", $results);

        // print_r( $resultString);

        if (!isset($_SESSION)) {
            session_start();
        }

        $orderNumber = $_POST['orderNumber'] ?? '';
        $SuccessUrl = 'https://mis1.faisaltown.com.pk/faisal_town1/web/index.php?r=memberportal';
        $amount = (!empty($_POST['net_amount'])) ? $_POST['net_amount'] : ($_POST['Amount'] ?? '');
        //echo $amount; exit;


        //&currency=586

        $apiUrl = 'https://acquiring.meezanbank.com/payment/rest/register.do?userName=FAISALTOWN_api&password=F987658&orderNumber=' . $orderNumber . '&amount=' . $amount . '&returnUrl=' . $SuccessUrl;
        $postData = $_POST;

        //echo $apiUrl ; exit;

        // Merge Additional Form Data (if any)
        $postData = array_merge($postData, $_POST);
        $pp_filePath = $_FILES['file']['tmp_name'];
        $pp_fileName = $_FILES['file']['name'];
        $pp_fileMimeType = $_FILES['file']['type'];
        //     print_r($apiUrl);
        $ch = curl_init($apiUrl);
        //   exit;
        //   $file = ['file' => new CURLFile($pp_filePath, $pp_fileMimeType, $pp_fileName)];
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            array_merge($postData)
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);


        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }
        curl_close($ch);
        $data = json_decode($response, true);

        if (!isset($_SESSION)) {
            session_start();
        }

        if ($data['errorCode'] == 0) {

            $mp_sql123 = "SELECT * FROM `payment` where vid='70937' and remarks = '" . $resultString . "' and pfor=2;";
            $mp_res123 = $conn->createCommand($mp_sql123)->queryOne();

            $mem_sql = "SELECT mp.plotno as plotno FROM memberplot mp
            WHERE mp.id = '" . $_REQUEST['membership_id'] . "'";
            $mem_result = $conn->createCommand($mem_sql)->queryOne();

            $note = $_REQUEST['customerNote'] ?? '';
            preg_match('/(\d+)%/', $note, $matches);

            $discount_percentage = isset($matches[1]) ? $matches[1] : 0;

            $insert_sql = "INSERT INTO online_payment_new SET payment_id = '" . $membersId . "', pay_against = '" . $mp_res123['id'] . "',ms_id = '" . $_REQUEST['membership_id'] . "',
            original_amount = '" . strtok($_REQUEST['GrossAmount'], '?') . "', paid_amount = '" . $_REQUEST['Amount'] . "', order_id = '" . $orderNumber . "',
            transaction_id = '" . $data['orderId'] . "', discount_amount = '" . $_REQUEST['discount_amount'] . "', net_amount = '" . $_REQUEST['net_amount'] . "', response_code = '" . $data['errorCode'] . "',
            signature = '" . $_REQUEST['Signature'] . "', response_message = 'Unpaid', note = '" . $_REQUEST['customerNote'] . "',percentage='" . $discount_percentage . "'";

            $insert_cmd = $conn->createCommand($insert_sql)->execute();

            if (!empty($_POST['CustomerMobileNumber'])) {
                $mb_array = array();
                $mb_array = explode(",", $_REQUEST['CustomerMobileNumber']);

                for ($i = 0; $i < count($mb_array); $i++) {
                    $mpid = $_SESSION["user_array"]["id"];
                    $mb_array_i = 0;
                    if (strlen($mb_array[$i]) > 9 && strlen($mb_array[$i]) <= 15) {
                        $mb_array_i = str_replace(["-", "–"], '', $mb_array[$i]);
                        $mobile = '+92' . (substr($mb_array_i, -10));
                        $message = 'Dear Member,
Payment of Rs:' . number_format($_REQUEST['Amount'], 0) . '/- is received against registration no. ' . $mem_result['plotno'] . ' on ' . $model->create_date . '. Thank you';
                        // Yii::$app->mycomponent->Send_text_msg($mobile,$message,$mpid);
                    }
                }
            }

    ?>

            <script>
                window.location.href = "<?php echo $data['formUrl']; ?>";
                // Swal.fire({
                //         icon: "success",
                //         title: "Success",
                //         text: "Payment Successfull!"
                //     }).then(function() {
                //         // window.location.reload();
                //         window.location.href = "<?php echo $SuccessUrl; ?>";
                //     });
            </script>
        <?php


        } else { ?>
            <script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Payment Unusccessfull!"
                }).then(function() {
                    // window.location.reload();
                });
            </script>
    <?php  }


        // echo json_encode($_POST);
    }




    // set post fields
    $post = [
        'institutionID' => $merchantid,
        'kuickpaySecuredKey' => $secret
    ];
    $postData = json_encode($post);


    $ch = curl_init('https://acquiring.meezanbank.com/payment/rest/register.do?userName=FAISALTOWN_api&password=F987658&orderNumber=' . $orderNumber . '&amount=' . $amount . '&currency=586&returnUrl=' . $SuccessUrl . '');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

    // execute!
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        echo curl_error($ch);
    }

    // close the connection, release resources used
    curl_close($ch);
    $token_info = json_decode($response);



    // do anything you want with your response
    if (isset($token_info->auth_token)) {
        $token = $token_info->auth_token;
    }

    $conn = Yii::$app->db;
    $order_sql = "SELECT * FROM online_payment_new Order by order_id desc";
    $order_res = $conn->createCommand($order_sql)->queryOne();
    $OrderID = $order_res['order_id'] + 1;
    $Amount = 5135.12;
    $AmountFeeCalculated = 5000;
    $hash = $merchantid . $OrderID . $Amount . $secret;
    $Signature = md5($hash);
    ?>
    <!--<div class="fixed-message">-->
    <!--       Page is Under Maintenance... -->
    <!--   </div>-->
    <!-- Chosen CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Chosen JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>


    <!--<div class="main-logo" style="text-align: center;">-->
    <!--    <img src="img/<?php //echo $get_sys_info_res['logo']; 
                            ?>" alt="" style="width: 150px;">-->
    <!--</div>-->
    <div class="payment-title" style="margin-bottom: 20px;">
        <span class="title-sub">ONLINE</span> &nbsp;
        <span class="title-sub">PAYMENT</span>
    </div>
    <div>
        <!--<form method="post" action="https://testcheckout.kuickpay.com/api/Redirection">-->
        <form method="post" id="main_form" class="main_form" enctype="multipart/form-data"
            action="index.php?r=member-portal/pay-online">

            <input type="hidden" name="InstitutionID" id="InstitutionID" Value="<?php echo $merchantid; ?>">
            <input type="hidden" name="SecritKey" id="SecritKey" Value="<?php echo $secret; ?>">
            <input type="hidden" name="orderNumber" id="orderNumber" Value="<?php echo $OrderID; ?>">
            <input type="hidden" name="MerchantName" Value="FAISAL TOWN">
            <input type="hidden" name="TransactionDescription" Value="Invoice">
            <input type="hidden" name="url" id="url"
                Value="https://mis1.faisaltown.com.pk/faisal_town1/web/index.php?r=memberportal/pay_online&id=<?php echo $_REQUEST['id']; ?>">
            <input type="hidden" name="OrderDate" Value="<?php echo date('Y-m-d'); ?>">
            <input type="hidden" name="Token" Value="<?php echo $token; ?>">
            <input type="hidden" name="GrossAmount" id="GrossAmount" Value="0">
            <input type="hidden" name="TaxAmount" Value="0">
            <input type="hidden" name="Discount" Value="1">
            <input type="hidden" name="PaymentMethod" Value="0">
            <input type="hidden" name="AmountFeeCalculated" id="AmountFeeCalculated" Value="0"> <?php //echo $AmountFeeCalculated;
                                                                                                ?>
            <input type="hidden" name="Signature" id="Signature" Value="">


            <div class="row">
                <div class="col-sm-5">
                    <label for="membership">Membership</label>
                    <select class="form-select" name="membership_id" id="membership_id"
                        aria-label="Default select example" onchange="get_due_payments(this.value);" required>
                        <option value="">Select on of Memberships</option>
                        <?php foreach ($mp_res as $mp_row) { ?>
                            <option value="<?php echo $mp_row['mpid']; ?>"><?php echo $mp_row['plotno']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-sm-7">
                    <div class="col-sm-8">
                        <label for="payment_type">Payment Type</label>
                        <div class="d-flex align-items-end">
                            <div class="form-check mr-3">
                                <input type="radio" id="installments" name="payment_type" value="installments"
                                    class="form-check-input small-radio" onchange="handlePaymentTypeChange(this.value)">
                                <label class="form-check-label" for="installments"
                                    style="padding: 0px 8px; margin-top: 5px">Installments</label>
                            </div>
                            <div class="form-check mr-3">
                                <input type="radio" id="overdue" name="payment_type" value="overdue"
                                    class="form-check-input small-radio" onchange="handlePaymentTypeChange(this.value)">
                                <label class="form-check-label" for="overdue"
                                    style="padding: 0px 8px; margin-top: 5px">Overdue</label>
                            </div>
                            <div class="form-check mr-3">
                                <input type="radio" id="overall" name="payment_type" value="overall"
                                    class="form-check-input small-radio" onchange="handlePaymentTypeChange(this.value)">
                                <label class="form-check-label" for="overall"
                                    style="padding: 0px 8px; margin-top: 5px">Overall</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="custom" name="payment_type" value="custom"
                                    class="form-check-input small-radio" onchange="handlePaymentTypeChange(this.value)">
                                <label class="form-check-label" for="custom"
                                    style="padding: 0px 8px; margin-top: 5px">Custom</label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row" id="div1">
                <div class="col-sm-3" id="pay_div">
                    <label for="pay_against">Payment Against</label>
                    <select multiple class="chosen-select form-control" name="pay_against[]" id="pay_against"
                        aria-label="Default select example" onchange="get_installment_amount(this.value);" required>


                        <!--<option value="">Select Payment Type</option>-->
                    </select>
                </div>
                <div class="col-sm-3">
                    <!-- <span id="generatecard">Test Amount: <span style="font-family: revert;">5135.12</span></span> -->
                    <label for="amount">Amount</label>
                    <input type="number" min="0" class="form-control" name="Amount" id="amount"
                        style="font-family: revert; width: 100%;" required
                        onchange="again_generate_signature(this.value);">
                </div>
                <div class="col-sm-3" id="discount_div" style="display: none;">
                    <label for="netamount">Net Amount</label>
                    <input type="text" id="net_amount" name="net_amount" placeholder="Discounted Amount" readonly
                        style="font-family: revert; width: 100%;" class="form-control">
                </div>
                <div class="col-sm-3" id="discount_amount_div" style="display: none;">
                    <label for="discountAmount">Discount Amount</label>
                    <input type="text" id="discount_amount" name="discount_amount" placeholder="Discount Amount"
                        readonly class="form-control">
                </div>

            </div>



            <div class="row" style="margin-top: 15px;">
                <div class="col-sm-6">
                    <label for="member_name">Name</label>
                    <input type="text" class="form-control" name="member_name" id="member_name"
                        value="<?= $mem_res['name'] ?>" style="width: 100%;" readonly>
                </div>
                <div class="col-sm-6">
                    <label for="NIC">CNIC</label>
                    <input type="text" class="form-control" name="NIC" id="member_cnic" value="<?= $mem_res['cnic'] ?>"
                        style="font-family: revert; width: 100%;" readonly>
                </div>
            </div>

            <div class="row" style="margin-top: 15px;">
                <div class="col-sm-6">
                    <label for="CustomerEmail">Email</label>
                    <input type="email" class="form-control" name="CustomerEmail" value="<?= $mem_res['email'] ?>"
                        style="width: 100%;" id="member_email" readonly>
                </div>
                <div class="col-sm-6">
                    <label for="CustomerMobileNumber">Phone No.</label>
                    <input type="text" class="form-control" name="CustomerMobileNumber" id="member_phone"
                        style="width: 100%;" value="<?= $mem_res['phone'] ?>" readonly style="font-family: revert;">
                </div>
            </div>
            <div class="row" style="margin-top: 15px;">
                <div class="col-sm-12">
                    <label for="CustomerNote">Note</label>
                    <input type="text" class="form-control" name="customerNote" id="customerNote" style="width: 100%;"
                        required>
                </div>

            </div>

            <div class="row" style="margin-top: 15px;">
                <div class="search-form" style="display: flex; justify-content: center; margin-left:22px">
                    <div class="g-recaptcha" data-sitekey="6LdHsF0rAAAAAMggHZj4MW9w8HwVE_u_ysKSF4rJ"></div>
                </div>
            </div>
            <div class="row" style="margin-top: 15px;">
                <div class="form-check d-flex align-items-center" style="font-size: 12px; margin-top: 10px;">
                    <input class="form-check-input" type="checkbox" id="agree_check" required
                        style="width: 18px; height: 18px; margin-right: 10px;" onchange="toggleSubmitButton()">
                    <label class="form-check-label mb-0" for="agree_check" style="font-size: 12px;">
                        I Agree that all these credentials mentioned above are correct and verified by me
                    </label>
                </div>
            </div>

            <div style="margin-top: 15px;">
                <button id="submitBtn" class="btn btn-sm btn-primary float-end"
                    style="color: white;background: #2d3e51;border: none;" type="submit" disabled>
                    Pay Now &nbsp;<i class="fa fa-arrow-right"></i>
                </button>
            </div>

        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#pay_against').chosen({
                width: '100%',
                placeholder_text_multiple: "Select payment types"
            });
        });
    </script>

    <script>
        function toggleSubmitButton() {
            const checkbox = document.getElementById('agree_check');
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = !checkbox.checked;
        }
    </script>
    <script>
        function handlePaymentTypeChange(paymentType) {
            var id = $("#membership_id").val();
            if (id) {
                $('#div1').show();
                if (paymentType === "installments") {
                    get_due_payments(id);
                    $("#pay_div").show();
                    $("#amount").show();
                    $('#amount').val('');
                    $('#amount').prop('readonly', true);
                    // Show discount div
                    $('#discount_div').hide();
                    $('#discount_amount_div').hide();
                } else if (paymentType === "overdue") {
                    get_overdue(id)
                    $("#pay_div").hide();
                    $("#amount").show();
                    $('#pay_against').prop('required', false);
                    $('#amount').prop('readonly', true);
                    $('#discount_div').hide();
                    $('#discount_amount_div').hide();
                } else if (paymentType === "overall") {
                    Swal.fire({
                        title: "Discount Offer!",
                        text: "Avail 15% discount on full payment.",
                        icon: "info",
                        confirmButtonText: "OK"
                    }).then(() => {

                        // 1. Set text in customerNote input
                        $('#customerNote').val("Avail 15% discount on full payment.");
                        // After SweetAlert is confirmed, proceed with logic
                        get_overall_receivables(id); // Make sure `id` is defined in your scope
                        $("#pay_div").hide();
                        $("#amount").show();
                        $('#pay_against').prop('required', false);
                        $('#amount').prop('readonly', true);
                        $('#discount_div').show();
                        $('#discount_amount_div').show();

                        setTimeout(function() {
                            var amount = parseFloat($('#amount').val());
                            if (!isNaN(amount)) {
                                var discount = amount * 0.15;
                                var discountedAmount = amount - discount;
                                $('#net_amount').val(discountedAmount);
                                $('#discount_amount').val(discount); // Set discount amount
                            } else {
                                $('#net_amount').val('');
                                $('#discount_amount').val('');
                            }
                        }, 500);
                    });
                } else if (paymentType === "custom") {
                    $('#pay_against').prop('required', false);
                    $('#amount').prop('readonly', false);
                    $("#amount").show();
                    $('#amount').val('');
                    $('#pay_div').hide();
                    $('#discount_div').hide();
                    $('#discount_amount_div').hide();

                }
            } else {
                $('#div1').hide();
                $("#membership_id").focus();
            }
        }

        function get_due_payments(id) {
            var membership_id = $('#membership_id').val();
            if (id > 0) {
                $.ajax({
                    type: "POST",
                    url: "index.php?r=member-portal/get-due-payments&id=" + id + "&membership_id=" + membership_id,
                    cache: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#pay_against').empty();
                        $.each(response, function(index, item) {
                            var newOption = $('<option></option>')
                                .val(item.value + ' - ' + item.lab)
                                .text(item.lab);
                            $('#pay_against').append(newOption);
                        });
                        $(".chosen-select").trigger("chosen:updated");
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX request failed:", status, error);
                    }
                });

                var original_url =
                    "https://mis1.faisaltown.com.pk/faisal_town1/web/index.php?r=memberportal/pay_online&id=<?php echo $_REQUEST['id']; ?>";
                var new_surl = original_url + '&ms_id=' + id;

                $('#SuccessUrl').val(new_surl);
            } else {
                $('#pay_against').empty();
                var original_url =
                    "https://mis1.faisaltown.com.pk/faisal_town1/web/index.php?r=memberportal/pay_online&id=<?php echo $_REQUEST['id']; ?>";

                $('#SuccessUrl').val(original_url);
            }
        }


        function get_overall_receivables(id) {
            if (id > 0) {
                $.ajax({
                    type: "POST",
                    url: "index.php?r=member-portal/get-overall-receivables&id=" + id,
                    cache: false,
                    success: function(response) {
                        var amount = parseFloat(response);
                        $('#amount').val(amount);
                    }
                });

                var original_url =
                    "https://cgrems.com/forest_town/web/index.php?r=memberportal/pay_online&id=<?php echo $_REQUEST['id']; ?>";
                var new_surl = original_url + '&ms_id=' + id;

                $('#SuccessUrl').val(new_surl);
            } else {
                $('#pay_against').empty();
                var original_url =
                    "https://cgrems.com/forest_town/web/index.php?r=memberportal/pay_online&id=<?php echo $_REQUEST['id']; ?>";
                $('#SuccessUrl').val(original_url);
            }
        }

        function get_overdue(id) {
            if (id > 0) {
                $.ajax({
                    type: "POST",
                    url: "index.php?r=member-portal/get-overdue&id=" + id,
                    cache: false,
                    success: function(response) {
                        var amount = parseFloat(response);
                        $('#amount').val(amount);
                    }
                });
            } else {
                $('#pay_against').empty();
                var original_url =
                    "https://cgrems.com/forest_town/web/index.php?r=memberportal/pay_online&id=<?php echo $_REQUEST['id']; ?>";
                $('#SuccessUrl').val(original_url);
            }
        }


        function get_installment_amount(id) {
            var selectedValues = $('#pay_against').val(); // Get selected values as an array
            var sum = 0;

            // If there are selected values, calculate the sum
            if (selectedValues) {
                sum = selectedValues.reduce(function(total, value) {
                    return total + parseFloat(value); // Convert each value to a number and sum them
                }, 0); // Initial total is 0
            }

            // Set the calculated sum in the #amount input field
            $('#amount').val(sum);

            // if ( id > 0 )
            // {
            //     var installment_amount = 0;
            //     $.ajax({
            //         type: "POST",
            //         url: "index.php?r=memberportal/get_installment_amount&id="+ id,
            //         cache: false,
            //         success: function(response)
            //         {
            //             $('#amount').val(response);
            //             installment_amount = response;
            //             $('#GrossAmount').val(response);
            //             $('#AmountFeeCalculated').val(response);

            //             var ms_id = $('#membership_id').val();
            //             var original_url = "https://mis.faisaltown.com.pk/faisal_town/web/index.php?r=memberportal/pay_online&id=<?php echo $_REQUEST['id']; ?>";
            //             var new_surl = original_url + '&ms_id=' + ms_id + '&payment_id=' + id + '&installment_amount=' + response;

            //             var newSignature = '';
            //             var merchant_id = $('#InstitutionID').val();
            //             var order_id = $('#OrderID').val();
            //             var secrit = $('#SecritKey').val();
            //             newSignature = merchant_id + order_id + response + secrit;

            //             var md5HashSignature = CryptoJS.MD5(newSignature).toString();

            //             $('#Signature').val(md5HashSignature);

            //             $('#SuccessUrl').val(new_surl);
            //         }
            //     });
            // }
            // else
            // {
            //     $('#amount').val('');
            //     $('#GrossAmount').val('');
            //     $('#AmountFeeCalculated').val('');

            //     var original_url = "https://mis.faisaltown.com.pk/faisal_town/web/index.php?r=memberportal/pay_online&id=<?php echo $_REQUEST['id']; ?>";

            //     $('#SuccessUrl').val(original_url);
            // }
        }

        function again_generate_signature(data) {
            var newSignature = '';
            var merchant_id = $('#InstitutionID').val();
            var order_id = $('#OrderID').val();
            var secrit = $('#SecritKey').val();
            newSignature = merchant_id + order_id + data + secrit;

            var md5HashSignature = CryptoJS.MD5(newSignature).toString();

            $('#Signature').val(md5HashSignature);
        }
    </script>

</body>

</html>