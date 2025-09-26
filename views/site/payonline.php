<?php
include('variables.php');
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include('head.php') ?>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    echo json_encode($_POST);
    exit;

    $pay_against = isset($_POST['pay_against']) ? $_POST['pay_against'] : [];
    $strings = is_array($pay_against) ? implode(",", $pay_against) : $pay_against;
    $installments = explode(",", $strings);
    $results = [];
    foreach ($installments as $installment) {
        $parts = explode("-", $installment);
        if (isset($parts[1])) {
            $results[] = trim($parts[1]);
        }
    }
    $resultString = implode(",", $results);
    $membership_id        = $_POST['membership_id'] ?? '';
    $gross_amount         = $_POST['GrossAmount'] ?? 0;
    $net_amount           = $_POST['AmountFeeCalculated'] ?? 0;
    $payment_type         = $_POST['payment_type'] ?? 'default';
    $discount_percentage  = $_POST['discount_percentage'] ?? 0;
    $discount_amount      = $_POST['discount_amount'] ?? 0;
    $paid_amount          = $_POST['Amount'] ?? 0;
    $member_name          = $_POST['member_name'] ?? '';
    $customer_note        = $_POST['customerNote'] ?? '';
    $customer_mobile      = $_POST['CustomerMobileNumber'] ?? '';
    $customer_email       = $_POST['CustomerEmail'] ?? '';
    $nic                  = $_POST['NIC'] ?? '';
    $signature            = $_POST['Signature'] ?? '';
    $orderNumber          = $_POST['orderNumber'] ?? rand(10000, 999999);

    $merchantUser = "FAISALTOWN_api";
    $merchantPass = "F987658";
    $returnUrl    = "https://faisaltown.com.pk/portal/members_portal/payonline.php";

    $amount       = (float)$net_amount * 100;
    $currency     = 586;
    $description  = $customer_note;

    $apiUrl = "https://acquiring.meezanbank.com/payment/rest/register.do?" .
        http_build_query([
            "userName"   => $merchantUser,
            "password"   => $merchantPass,
            "orderNumber" => $orderNumber,
            "amount"     => $amount,
            "currency"   => $currency,
            "returnUrl"  => $returnUrl,
            "description" => $description
        ]);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        echo "<script>alert('cURL Error: $error_msg');</script>";
        exit;
    }
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['errorCode']) && $data['errorCode'] == 0) {
        header("Location: " . $data['formUrl']);
        exit;
    } else {
        $errorMsg  = $data['errorMessage'] ?? $data['message'] ?? 'Payment Unsuccessful!';
        $errorCode = $data['errorCode'] ?? 'Unknown';
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: "error",
        title: "Payment Error (Code: <?= $errorCode ?>)",
        text: "<?= htmlspecialchars($errorMsg) ?>"
    }).then(function() {
        window.location.href = "index.php?r=memberportal/dashboard";
    });
});
</script>
<?php
        exit;
    }
}

?>

<body class="backgroundpic1" style="overflow-y: auto; height: 100%;">
    <?php include('navbar.php') ?>
    <div class="d-flex" style="position: absolute; height: 92%; top: 8%;  overflow-y: auto; width: 100%;">
        <?php include('sidebar.php') ?>

        <div class="payment-container">
            <div class="payment-header">
                ONLINE PAYMENT
                <div class="payment-subtitle">Secure and Convenient Payment Processing</div>
            </div>
            <div class="payment-body">
                <form method="post" style="width:100%;height:100%;margin:0;" id="paymentForm">
                    <input type="hidden" name="GrossAmount" id="GrossAmount" value="0">
                    <input type="hidden" name="TaxAmount" value="0">
                    <input type="hidden" name="Discount" value="1">
                    <input type="hidden" name="PaymentMethod" value="0">
                    <input type="hidden" name="AmountFeeCalculated" id="AmountFeeCalculated" value="0">
                    <input type="hidden" name="membership_id" id="membership_id_val">
                    <input type="hidden" name="discount_percentage" id="discount_percentage" value="0">
                    <input type="hidden" name="discount_amount" id="discount_amount" value="0">

                    <div class="form-layout">
                        <!-- Row 1: Membership Information & Payment Details -->
                        <div class="row-flex">
                            <div class="col-flex">
                                <div class="form-section">
                                    <div class="section-title">Membership Information</div>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <label class="form-label">Membership</label>
                                            <select class="form-select" id="membership_id" required=""
                                                style="border: 1px solid #ced4da;border-radius: 4px;padding: 6px 8px;font-size: 11px;transition: all 0.2s;background: #fff;height: 32px;">
                                                <option value="">Select Membership</option>
                                            </select>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label class="form-label">Payment Type</label>
                                            <select class="form-select" id="payment_type" required=""
                                                style="border: 1px solid #ced4da;border-radius: 4px;padding: 6px 8px;font-size: 11px;transition: all 0.2s;background: #fff;height: 32px;">
                                                <option value="">Select Payment Type</option>
                                                <option value="installments">Installments</option>
                                                <option value="overdue">Overdue</option>
                                                <option value="overall">Overall</option>
                                                <option value="custom">Custom</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-flex">
                                <div class="form-section" id="paymentDetailsSection">
                                    <div class="section-title">Payment Details</div>
                                    <div class="row">
                                        <!-- Left Column: Input Fields -->
                                        <div class="col-md-6">
                                            <div id="installmentsSection" style="display: none;">
                                                <div class="mb-2">
                                                    <label class="form-label">Select Installments</label>
                                                    <select multiple="" class="chosen-select form-control"
                                                        id="pay_against" name="pay_against[]" style="font-size: 11px;">
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="customSection" style="display: none;">
                                                <div class="mb-2">
                                                    <label class="form-label">Enter Amount (PKR)</label>
                                                    <input type="number" min="0" class="form-control" id="custom_amount"
                                                        name="Amount" style="font-size: 11px; height: 32px;">
                                                </div>
                                            </div>
                                            <div id="readonlySection" style="display: none;">
                                                <div class="mb-2">
                                                    <label class="form-label">Amount (PKR)</label>
                                                    <input type="text" class="form-control" id="readonly_amount"
                                                        readonly
                                                        style="font-size: 11px; background-color: #f8f9fa; height: 32px;">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column: Enhanced Amount Breakdown -->
                                        <div class="col-md-6">
                                            <div class="amount-display" id="amountDisplay" style="display: none;">
                                                <div class="amount-header">
                                                    <i class="fas fa-calculator me-2"></i>
                                                    <span>Payment Summary</span>
                                                </div>

                                                <div class="amount-breakdown">
                                                    <div class="amount-row">
                                                        <span class="amount-label">
                                                            <i class="fas fa-receipt me-1"></i>
                                                            Original Amount:
                                                        </span>
                                                        <span class="amount-value" id="originalAmount">PKR 0.00</span>
                                                    </div>

                                                    <div class="amount-row" id="discountRow" style="display: none;">
                                                        <span class="amount-label">
                                                            <i class="fas fa-percentage me-1"></i>
                                                            Discount (<span id="discountPercentage">0</span>%):
                                                        </span>
                                                        <span class="amount-value discount-value"
                                                            id="discountAmount">-PKR 0.00</span>
                                                    </div>

                                                    <div class="amount-row" id="discountReasonRow"
                                                        style="display: none;">
                                                        <span class="amount-label">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Discount Reason:
                                                        </span>
                                                        <span class="amount-value" id="discountReason"
                                                            style="font-size: 10px; color: #6c757d;">-</span>
                                                    </div>

                                                    <div class="amount-row" id="taxRow" style="display: none;">
                                                        <span class="amount-label">
                                                            <i class="fas fa-file-invoice me-1"></i>
                                                            Tax:
                                                        </span>
                                                        <span class="amount-value" id="taxAmount">PKR 0.00</span>
                                                    </div>

                                                    <div class="amount-row" id="feeRow" style="display: none;">
                                                        <span class="amount-label">
                                                            <i class="fas fa-credit-card me-1"></i>
                                                            Processing Fee:
                                                        </span>
                                                        <span class="amount-value" id="feeAmount">PKR 0.00</span>
                                                    </div>
                                                </div>

                                                <div class="total-amount">
                                                    <span class="amount-label">
                                                        <i class="fas fa-money-bill-wave me-2"></i>
                                                        Total Payable:
                                                    </span>
                                                    <span class="amount-value" id="netAmount">PKR 0.00</span>
                                                </div>

                                                <div class="amount-footer">
                                                    <small class="text-muted">
                                                        <i class="fas fa-shield-alt me-1"></i>
                                                        Secure payment processing
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2: Personal Information (full width) -->
                        <div class="form-section" style="margin-top:10px;">
                            <div class="section-title">Personal Information</div>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="member_name" style="font-size: 11px;"
                                        value="<?php echo $_SESSION['user']['name']; ?>" readonly="">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">CNIC</label>
                                    <input type="text" class="form-control" name="NIC" style="font-size: 11px;"
                                        value="<?php echo $_SESSION['user']['cnic']; ?>" readonly="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="CustomerEmail"
                                        style="font-size: 11px;" value="<?php echo $_SESSION['user']['email']; ?>"
                                        required="">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="CustomerMobileNumber"
                                        style="font-size: 11px;" value="<?php echo $_SESSION['user']['phone']; ?>"
                                        required="">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Payment Note</label>
                                <input type="text" class="form-control" name="customerNote" id="customerNote"
                                    style="font-size: 11px;" required="">
                            </div>
                        </div>
                        <!-- Row 3: Security Verification and Pay Secure (full width) -->
                        <div class="form-section" style="margin-top:10px;">
                            <div class="section-title">Security Verification</div>
                            <div class="g-recaptcha" data-sitekey="6LdHsF0rAAAAAMggHZj4MW9w8HwVE_u_ysKSF4rJ">
                                <div style="width: 304px; height: 78px;">
                                    <div><iframe title="reCAPTCHA" width="304" height="78" role="presentation"
                                            name="a-peb05fld4x0m" frameborder="0" scrolling="no"
                                            sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-top-navigation allow-modals allow-popups-to-escape-sandbox allow-storage-access-by-user-activation"
                                            src="https://www.google.com/recaptcha/api2/anchor?ar=1&amp;k=6LdHsF0rAAAAAMggHZj4MW9w8HwVE_u_ysKSF4rJ&amp;co=aHR0cHM6Ly9taXMuZmFpc2FsdG93bi5jb20ucGs6NDQz&amp;hl=en&amp;v=_mscDd1KHr60EWWbt2I_ULP0&amp;size=normal&amp;anchor-ms=20000&amp;execute-ms=15000&amp;cb=cmlujhd3uzj"></iframe>
                                    </div><textarea id="g-recaptcha-response" name="g-recaptcha-response"
                                        class="g-recaptcha-response"
                                        style="width: 250px; height: 40px; border: 1px solid rgb(193, 193, 193); margin: 10px 25px; padding: 0px; resize: none; display: none;"></textarea>
                                </div><iframe style="display: none;"></iframe>
                            </div>
                            <div class="agreement">
                                <input type="checkbox" id="agree_check" required="">
                                <label for="agree_check">
                                    I confirm that all the information provided is accurate and verified. I understand
                                    that
                                    any false information may result in transaction cancellation.
                                </label>
                            </div>
                            <button type="submit" class="btn btn-pay" id="submitBtn" style="margin-top:12px;">
                                <i class="fas fa-lock me-2"></i> Pay Securely
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <style>
        /* Enhanced Amount Display Styling */
        .amount-display {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0;
            margin-top: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .amount-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 13px;
            display: flex;
            align-items: center;
        }

        .amount-breakdown {
            padding: 15px;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 12px;
            padding: 6px 0;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .amount-row:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
        }

        .amount-row:hover {
            background-color: rgba(0, 123, 255, 0.05);
            border-radius: 4px;
            padding: 6px 8px;
        }

        .amount-label {
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
        }

        .amount-value {
            font-weight: bold;
            color: #28a745;
            font-size: 13px;
        }

        .discount-value {
            color: #dc3545 !important;
        }

        .total-amount {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding: 15px;
            border-top: 2px solid #007bff;
            font-size: 14px;
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 86, 179, 0.1) 100%);
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
        }

        .total-amount .amount-label {
            font-size: 14px;
            font-weight: bold;
            color: #212529;
            display: flex;
            align-items: center;
        }

        .total-amount .amount-value {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .amount-footer {
            background: #f8f9fa;
            padding: 8px 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }

        .amount-footer small {
            color: #6c757d;
            font-size: 10px;
        }

        /* Form Section Styling */
        .form-section {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            min-height: 120px;
        }

        /* Equal height for row-flex sections */
        .row-flex {
            display: flex;
            align-items: stretch;
        }

        .col-flex {
            display: flex;
            flex-direction: column;
        }

        .col-flex .form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .section-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #007bff;
        }

        /* Payment Details Layout */
        #paymentDetailsSection .row {
            margin: 0;
        }

        #paymentDetailsSection .col-md-6 {
            padding: 0 10px;
        }

        #paymentDetailsSection .col-md-6:first-child {
            border-right: 1px solid #dee2e6;
        }

        /* Input Fields Styling */
        #paymentDetailsSection .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        #paymentDetailsSection .form-control {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 11px;
            transition: border-color 0.15s ease-in-out;
        }

        #paymentDetailsSection .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Readonly Section Styling */
        #readonlySection {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }

        #readonlySection .form-control {
            background-color: #e9ecef !important;
            color: #495057;
            font-weight: 600;
        }

        /* Chosen Plugin Styling */
        .chosen-container-multi .chosen-choices {
            border: 1px solid #ced4da;
            border-radius: 4px;
            min-height: 32px;
            padding: 2px 8px;
            background: white;
        }

        .chosen-container-multi .chosen-choices li.search-field input[type="text"] {
            height: 24px;
            font-size: 11px;
            color: #495057;
        }

        .chosen-container-multi .chosen-choices li.search-choice {
            background: #007bff;
            color: white;
            border: 1px solid #0056b3;
            border-radius: 3px;
            padding: 2px 6px;
            margin: 2px 4px 2px 0;
            font-size: 10px;
        }

        .chosen-container-multi .chosen-choices li.search-choice .search-choice-close {
            color: white;
            font-size: 12px;
        }

        .chosen-drop {
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chosen-results li {
            font-size: 11px;
            padding: 6px 8px;
        }

        .chosen-results li.highlighted {
            background: #007bff;
            color: white;
        }

        /* Animation for amount updates */
        .amount-value {
            transition: all 0.3s ease;
        }

        .amount-value.updated {
            animation: highlightUpdate 0.6s ease;
        }

        @keyframes highlightUpdate {
            0% {
                background-color: rgba(40, 167, 69, 0.3);
            }

            100% {
                background-color: transparent;
            }
        }
        </style>

        <script>
        $(document).ready(function() {
            let paymentData = null;
            let selectedMembership = null;

            // Initialize the form
            function initializeForm() {
                getMyProperties();
                setupEventListeners();
            }

            // Get membership properties
            function getMyProperties() {
                const formData = new FormData();
                const project = <?= $_SESSION['user']['project'] ?>;
                const member_id = <?= $_SESSION['user']['id'] ?>;
                formData.append('action', 'get_my_plots');
                formData.append('project', project);
                formData.append('member_id', member_id);

                fetch('post_requests/index.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const plots = data.data;
                            const options = plots.map(plot =>
                                `<option value="${plot.ms_id}">${plot.plot_noo} - ${plot.plot_detail_address}</option>`
                            );
                            $('#membership_id').html('<option value="">Select Membership</option>' + options
                                .join(''));
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Unable to fetch plots.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Unable to reach the server.'
                        });
                    });
            }

            // Setup event listeners
            function setupEventListeners() {
                // Membership selection change
                $('#membership_id').on('change', function() {
                    const membershipId = $(this).val();
                    if (membershipId) {
                        selectedMembership = membershipId;
                        getPaymentData(membershipId);
                    } else {
                        hideAllPaymentSections();
                    }
                });

                // Payment type selection change
                $('#payment_type').on('change', function() {
                    const paymentType = $(this).val();
                    if (paymentType && selectedMembership) {
                        // Refresh payment data with new payment type
                        getPaymentData(selectedMembership);
                        showPaymentSection(paymentType);
                    } else {
                        hideAllPaymentSections();
                    }
                });

                // Custom amount input change
                $('#custom_amount').on('input', function() {
                    updateAmountDisplay();
                });

                // Installment selection change
                $('#pay_against').on('change', function() {
                    updateAmountDisplay();
                });

                // Chosen plugin change event
                $(document).on('change', '#pay_against_chosen', function() {
                    updateAmountDisplay();
                });

                // Listen for chosen plugin updates
                $(document).on('chosen:updated', '#pay_against', function() {
                    updateAmountDisplay();
                });
            }

            // Get payment data
            function getPaymentData(membershipId) {
                const formData = new FormData();
                const project = <?= $_SESSION['user']['project'] ?>;
                const member_id = <?= $_SESSION['user']['id'] ?>;
                const paymentType = $('#payment_type').val();
                formData.append('action', 'get_payment_data');
                formData.append('project', project);
                formData.append('membership_id', membershipId);
                formData.append('member_id', member_id);
                formData.append('payment_type', paymentType);

                fetch('post_requests/index.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            paymentData = data.data;
                            if (paymentData) {
                                populateInstallmentsDropdown(paymentData.installments);
                                updateAmountDisplay();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No payment data found for selected membership.'
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Unable to fetch payment data.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Unable to reach the server.'
                        });
                    });
            }

            // Populate installments dropdown
            function populateInstallmentsDropdown(installments) {
                const options = installments.map(inst =>
                    `<option value="${inst.id}">${inst.installment_name} - PKR ${formatCurrency(inst.amount)}</option>`
                );
                $('#pay_against').html(options.join(''));

                // Initialize chosen plugin for multiselect
                if ($.fn.chosen) {
                    $('#pay_against').chosen({
                        width: '100%',
                        placeholder_text_multiple: 'Select Installments',
                        no_results_text: 'No installments found'
                    });
                }
            }

            // Show appropriate payment section based on payment type
            function showPaymentSection(paymentType) {
                hideAllPaymentSections();

                switch (paymentType) {
                    case 'installments':
                        $('#installmentsSection').show();
                        break;
                    case 'custom':
                        $('#customSection').show();
                        break;
                    case 'overdue':
                    case 'overall':
                        showReadonlyAmount(paymentType);
                        break;
                }

                $('#amountDisplay').show();
                updateAmountDisplay();
            }

            // Show readonly amount for overdue/overall
            function showReadonlyAmount(paymentType) {
                if (!paymentData) return;

                let amount = 0;
                if (paymentType === 'overdue') {
                    amount = paymentData.overdue_amount || 0;
                } else if (paymentType === 'overall') {
                    amount = paymentData.remaining_amount || 0;
                }

                // Update the readonly input value and show the section
                $('#readonly_amount').val(formatCurrency(amount));
                $('#readonlySection').show();
            }

            // Hide all payment sections
            function hideAllPaymentSections() {
                $('#installmentsSection').hide();
                $('#customSection').hide();
                $('#readonlySection').hide();
                $('#amountDisplay').hide();
            }

            // Format currency with proper formatting
            function formatCurrency(amount) {
                return parseFloat(amount).toLocaleString('en-PK', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Add highlight animation to amount values
            function highlightAmount(element) {
                $(element).addClass('updated');
                setTimeout(() => {
                    $(element).removeClass('updated');
                }, 600);
            }

            // Enhanced amount display update function
            function updateAmountDisplay() {
                if (!paymentData) return;

                const paymentType = $('#payment_type').val();
                let originalAmount = 0;
                let discountAmount = 0;
                let taxAmount = 0;
                let feeAmount = 0;
                let netAmount = 0;

                switch (paymentType) {
                    case 'installments':
                        const selectedInstallments = $('#pay_against').val();
                        if (selectedInstallments && selectedInstallments.length > 0) {
                            originalAmount = selectedInstallments.reduce((total, instId) => {
                                const inst = paymentData.installments.find(i => i.id == instId);
                                return total + (inst ? parseFloat(inst.amount) : 0);
                            }, 0);
                        }
                        break;
                    case 'custom':
                        originalAmount = parseFloat($('#custom_amount').val()) || 0;
                        break;
                    case 'overdue':
                        originalAmount = paymentData.overdue_amount || 0;
                        break;
                    case 'overall':
                        originalAmount = paymentData.remaining_amount || 0;
                        break;
                }

                // Calculate discount
                if (paymentData.discount_percentage && paymentType != 'custom' && paymentType !=
                    'installments') {
                    discountAmount = (originalAmount * paymentData.discount_percentage) / 100;
                }

                // Calculate tax (if applicable)
                if (paymentData.tax_percentage) {
                    taxAmount = (originalAmount * paymentData.tax_percentage) / 100;
                }

                // Calculate processing fee (if applicable)
                if (paymentData.processing_fee) {
                    feeAmount = parseFloat(paymentData.processing_fee);
                }

                netAmount = originalAmount - discountAmount + taxAmount + feeAmount;

                // Update display with formatted values
                $('#originalAmount').text('PKR ' + formatCurrency(originalAmount));
                $('#discountAmount').text('-PKR ' + formatCurrency(discountAmount));
                $('#discountPercentage').text(paymentData.discount_percentage || 0);
                $('#taxAmount').text('PKR ' + formatCurrency(taxAmount));
                $('#feeAmount').text('PKR ' + formatCurrency(feeAmount));
                $('#netAmount').text('PKR ' + formatCurrency(netAmount));

                // Show/hide discount row
                if (discountAmount > 0) {
                    $('#discountRow').show();
                    $('#discountReasonRow').show();
                    $('#discountReason').text(paymentData.discount_reason || 'Discount Applied');
                } else {
                    $('#discountRow').hide();
                    $('#discountReasonRow').hide();
                }

                // Show/hide tax row
                if (taxAmount > 0) {
                    $('#taxRow').show();
                } else {
                    $('#taxRow').hide();
                }

                // Show/hide fee row
                if (feeAmount > 0) {
                    $('#feeRow').show();
                } else {
                    $('#feeRow').hide();
                }

                // Update hidden form fields
                $('#GrossAmount').val(originalAmount);
                $('#AmountFeeCalculated').val(netAmount);
                $('#discount_percentage').val(paymentData.discount_percentage || 0);
                $('#discount_amount').val(discountAmount);
                $('#membership_id_val').val(selectedMembership);

                // Add highlight animation
                highlightAmount('#originalAmount');
                highlightAmount('#netAmount');
                if (discountAmount > 0) {
                    highlightAmount('#discountAmount');
                }
            }

            // Initialize the form
            initializeForm();
        });
        </script>

</body>

</html>