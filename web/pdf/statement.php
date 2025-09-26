<?php
// , $_SESSION['print_info_expires']
    
session_start();
if (isset($_SESSION['print_info'])) {
//   if (time() > $_SESSION['print_info_expires']) {
//     unset($_SESSION['print_info']);
//     // unset($_SESSION['print_info_expires']);
//     // header("Location: https://faisaltown.com.pk/members_portal/index.php");
//     echo "Invalid Request!";
//     exit;
//   }
  
  // Not expired: safe to use
  $print_info = $_SESSION['print_info'];
//   echo json_encode($print_info);exit;

  // Avoid breaking PDF output with notices
  error_reporting(E_ALL);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
  ini_set('error_log', '/var/log/php_errors.log');

  require_once('tcpdf.php');
  require_once('tcpdf_include.php');
  class MYPDF extends TCPDF
  {
    public function Header()
    {
      global $print_info;
      $this->Image(str_replace(' ', '', trim($print_info['logo'])), 15, 3, 19, '', 'PNG', '', 'T', false, 300);

      $this->SetFont('dejavusans', 'B', 15);
      $this->SetXY(40, 8);
      $this->Cell(0, 0, $print_info['companyname']);

      $this->SetFont('dejavusans', 'B', 14);
      $this->SetXY(40, 15);
      $this->Cell(0, 0, $print_info['project_name']);

      $this->SetFont('dejavusans', 'B', 14);
      $this->SetXY(140, 8);
      $this->Cell(0, 0, 'Account Statement');

      $this->SetXY(0, 23);
      $style = ['width' => 0.2, 'color' => [0, 0, 0]];
      $this->SetLineStyle($style);
      $this->Line(15, $this->y, $this->w - 10, $this->y);
    }
  }

  $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
  $pdf->SetCreator(PDF_CREATOR);
  $pdf->SetAuthor('Faisal Town Developers');
  $pdf->SetTitle('Account Statement');
  $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
  $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->SetFont('dejavusans', '', 8);
  $pdf->AddPage();


// Queries Applied



// Applied Queries End



//   // Helper function to clean numbers
  function safe_number($num, $decimals = 0) {
      
      return number_format((float)str_replace(',', '', $num), $decimals);
    if ($num === null || $num === '' || !is_numeric(str_replace(',', '', $num))) {
      return '0';   
    }
    return number_format((float)str_replace(',', '', $num), $decimals);
  }

  // Paid % calculation
  $paid_amount = (float)preg_replace('/[^0-9.]/', '', $print_info['paid_amount']);
  $net_price   = (float)preg_replace('/[^0-9.]/', '', $print_info['net_price']);
  $paid_percent = ($net_price > 0) ? round(($paid_amount / $net_price) * 100, 0) : 0;
  

  // ================= MEMBER DETAILS ==================
  $htmlmembers = '
  <table style="background-color:#000; padding:3px;color:#fff;">
    <tr><td><h3>Member Details</h3></td></tr>
  </table><br><br>

  <table>
    <tr><td><b>Name :</b></td><td><span style="color:#696666c4;">' . $print_info['name'] . '</span>
    </td>
     <td><b>' . $print_info['RelationShip'] . ':</b></td><td><span style="color:#696666c4;">' . $print_info['relation'] . '</span>

    </td>

    </tr>


    <tr>
      <td><b>Phone No :</b></td>
      <td><span style="color:#696666c4;">' . $print_info['phone'] . '</span></td>
      <td><b>Email :</b></td>
      <td><span style="color:#696666c4;">' . $print_info['email'] . '</span></td>
    </tr>

    <tr>
      <td><b>CNIC :</b></td>
      <td><span style="color:#696666c4;">' . $print_info['cnic'] . '</span></td>
    </tr>

    <tr>
      <td><b>Present Address :</b></td>
      <td><span style="color:#696666c4;">' . $print_info['address'] . '</span></td>
      <td><b>Permanent Address :</b></td>
      <td><span style="color:#696666c4;">' . $print_info['address2'] . '</span></td>
    </td></tr>';
    
    $share = 100;
$totalShareUsed = 0;
if (!empty($result_details)) {
    // Store all rows into an array for reuse
    $all_results = [];
    while ($row = $result_details->fetch_assoc()) {
        $all_results[] = $row;
        $totalShareUsed += $row['share'];
    }
}
$remainingShare = $share - $totalShareUsed; 
$htmlmembers .= '
<tr>
    <td><b>Shares:</b></td>
    <td><span style="color:#696666c4;">' . $remainingShare . '%</span></td>
</tr>
  </table>';



  // Add nominees if available
  if (!empty($print_info['nominees'])) {
    foreach ($print_info['nominees'] as $nominee) {
      $htmlmembers .= '
      <tr>
        <td><b>Nominee :</b></td>
        <td><span style="color:#696666c4;">' . $nominee['name'] . '</span></td>
        <td><b>Nominee CNIC :</b></td>
        <td><span style="color:#696666c4;">' . $nominee['cnic'] . '</span></td>
      </tr>
      <tr>
        <td><b>Nominee Relation:</b></td>
        <td><span style="color:#696666c4;">' . $nominee['relation'] . '</span></td>
      </tr>';
    }
  }
  
  $htmlmembers .= '</table><br><br>';
  $pdf->writeHTML($htmlmembers, false, false, true, false, '');

  // ================= FILE & FINANCIAL DETAILS ==================
  $html1 = '<table style="background-color:#000; padding:3px;color:#fff;">
    <tr>
      <td style="width:50%;"><h3>File Details</h3></td>
      <td style="width:50%;"><h3>Financial Details</h3></td>
    </tr>
  </table><br><br>';

  $pdf->writeHTML($html1, false, false, true, false, '');

  $htmldetails = '<table>
    <tr>
      <td style="width:50%; vertical-align:top;">
        <table>
          <tr><td><b>Project Name :</b></td><td><span style="color:#696666c4;">' . $print_info['project_name'] . '</span></td></tr>
          <tr><td><b>Membership No:</b></td><td><span style="color:#696666c4;">' . $print_info['membership_no'] . '</span></td></tr>
          <tr><td><b>Size :</b></td><td><span style="color:#696666c4;">' . $print_info['size'] . '</span></td></tr>
          <tr><td><b>Measurement :</b></td><td><span style="color:#696666c4;">' . $print_info['measurement'] . ' Sq.Yds</span></td></tr>
          <tr><td><b>Block :</b></td><td><span style="color:#696666c4;">' . $print_info['block'] . '</span></td></tr>';

  if ($print_info['plot_details_address'] != 0) {
    $htmldetails .= '<tr><td><b>' . $print_info['type'] . ' No :</b></td><td><span style="color:#696666c4;">' . $print_info['plot_details_address'] . '</span></td></tr>';
  }

  $htmldetails .= '</table>
      </td>
      <td style="width:50%; vertical-align:top;">
        <table>';

  // Add category charges if available
  if (!empty($print_info['category_charges'])) {
    foreach ($print_info['category_charges'] as $charge) {
      $htmldetails .= '
      <tr>
        <td><b>' . $charge['name'] . ' Charges:</b></td>
        <td><span style="color:#696666c4;">' . safe_number($charge['amount']) . ' (' . $charge['percentage'] . '%)</span></td>
      </tr>';
    }
  }

  $htmldetails .= '
      <tr><td><b>Cost of Plot</b></td><td><span style="color:#696666c4;">' . safe_number($print_info['cost_of_plot']) . '</span></td></tr>
      <tr><td><b>Discount Price :</b></td><td><span style="color:#696666c4;">' . safe_number($print_info['discount_price']) . '</span></td></tr>
      <tr><td><b>Discount on Installments :</b></td><td><span style="color:#696666c4;">' . safe_number($print_info['discount_installments']) . '</span></td></tr>
      <tr><td><b>Net Price :</b></td><td><span style="color:#696666c4;">' . safe_number($print_info['net_price']) . '</span></td></tr>
      <tr><td><b>Other Charges :</b></td><td><span style="color:#696666c4;">' . safe_number($print_info['other_charges']) . '</span></td></tr>
      <tr><td><b>Paid Amount :</b></td><td><span style="color:#696666c4;">' . safe_number($print_info['paid_amount']) . '</span></td></tr>
      <tr><td><b>Overdue Amount :</b></td><td><span style="color:#696666c4;">' . safe_number($print_info['overdue_amount']) . '</span></td></tr>
      <tr><td><b>Adjusted Amount:</b></td><td><span style="color:#696666c4;">' . safe_number($print_info['adjusted_amount']) . '</span></td></tr>
      <tr><td><b>Remaining Amount :</b></td><td><span style="color:#696666c4;">' . safe_number($print_info['remaining_amount']) . '</span></td></tr>
      </table>
      </td>
    </tr>
  </table><br><br>';


    
    // Start writing other sections
    $pdf->writeHTML($htmldetails, false, false, true, false, '');
    
    // ================ INSTALLMENT DETAILS ================
    if (!empty($print_info['installment_details'])) {
        // Heading
        $html = '<table style="background-color:#000; padding:3px; color:#fff;">
                    <tr><td><h3>Installment Details</h3></td></tr>
                 </table><br><br>';
    
        // Table header
        $html .= '<table style="border-collapse: collapse;" cellspacing="0" cellpadding="4">
                    <thead style="background:#666; border-color:#ccc; color:#fff;">
                      <tr>
                        <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="6%">S No.</th>
                        <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="19%">Narration</th>
                        <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="12%">Due Date</th>
                        <th style="text-align:right; border:1px solid #696666c4; font-weight:bold;" width="12%">Due Amount</th>
                        <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="12%">Paid Date</th>
                        <th style="text-align:right; border:1px solid #696666c4; font-weight:bold;" width="12%">Paid Amount</th>
                        <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="15%">Receipt No</th>
                        <th style="text-align:right; border:1px solid #696666c4; font-weight:bold;" width="12%">Balance</th>
                      </tr>
                    </thead>
                    <tbody>';
    
        $total_due = 0;
        $total_paid = 0;
        $serial = 1;
        foreach ($print_info['installment_details'] as $installment) {
            
            
            //{"installment_number":1,"installment_name":"Down Payment","installment_date":"29-08-2025","installment_amount":"1,600,000",
            //"installment_paid_date":["29-08-2025"],"installment_paid_amount":"1,600,000","installment_paid_receipt":["BRV - 259479 "]}
            // {"installment_number":1,"installment_name":"Down Payment","installment_date":"29-08-2025","installment_amount":"1,600,000",
            // "installment_paid_date":["29-08-2025"],"installment_paid_amount":"1,600,000","installment_paid_receipt":"BRV - 259479"}
            $amt_due = floatval(str_replace(',', '', $installment['installment_amount']));
            $amt_paid = floatval(str_replace(',', '', $installment['installment_paid_amount']));
            $balance = $amt_due - $amt_paid;
    
            $total_due += $amt_due;
            $total_paid += $amt_paid;
    
            // Combine dates and receipts
            $paid_dates = !empty($installment['installment_paid_date']) 
                            ? implode(', ', $installment['installment_paid_date']) 
                            : '';
            $receipts = !empty($installment['installment_paid_receipt']) 
                            ? implode(', ', $installment['installment_paid_receipt']) 
                            : '';
    
            $html .= '<tr>
                        <td style="text-align:left; border:1px solid #696666c4;" width="6%">' . $serial . '</td>
                        <td style="text-align:left; border:1px solid #696666c4;" width="19%">' . htmlspecialchars($installment['installment_name']) . '</td>
                        <td style="text-align:center; border:1px solid #696666c4;" width="12%">' . htmlspecialchars($installment['installment_date']) . '</td>
                        <td style="text-align:right; border:1px solid #696666c4;" width="12%">' . safe_number($amt_due) . '</td>
                        <td style="text-align:left; border:1px solid #696666c4;" width="12%">' . htmlspecialchars($paid_dates) . '</td>
                        <td style="text-align:right; border:1px solid #696666c4;" width="12%">' . safe_number($amt_paid) . '</td>
                        <td style="text-align:left; border:1px solid #696666c4;" width="15%">' . htmlspecialchars($receipts) . '</td>
                        <td style="text-align:right; border:1px solid #696666c4;" width="12%">' . safe_number($balance) . '</td>
                      </tr>';
    
            $serial++;
        }
    
        // Totals row: if fully paid, green; otherwise red
        $total_balance = $total_due - $total_paid;
        $totals_style = ($total_balance == 0) 
                        ? 'background-color: #d4edda;'   // light greenish
                        : 'background-color: #f8d7da;';  // light reddish
    
        $html .= '<tr style="' . $totals_style . '">
                    <td colspan="3" style="border:1px solid #696666c4; text-align:right; font-weight:bold;">Total</td>
                    <td style="border:1px solid #696666c4; text-align:right; font-weight:bold;">' . safe_number($total_due) . '</td>
                    <td style="border:1px solid #696666c4;"></td>
                    <td style="border:1px solid #696666c4; text-align:right; font-weight:bold;">' . safe_number($total_paid) . '</td>
                    <td style="border:1px solid #696666c4;"></td>
                    <td style="border:1px solid #696666c4; text-align:right; font-weight:bold;">' . safe_number($total_balance) . '</td>
                  </tr>';
    
        $html .= '</tbody></table><br><br>';
    
        $pdf->writeHTML($html, false, false, true, false, '');
    }
    
    
    // ================ ESCALATION CHARGES ================
    
    $html = '<table style="background-color:#000; padding:3px; color:#fff;">
                <tr><td><h3>Escalation Charges</h3></td></tr>
             </table><br><br>
             <table style="border-collapse: collapse;" cellspacing="0" cellpadding="4">
               <thead style="background:#666; border-color:#ccc; color:#fff;">
                 <tr>
                   <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="10%">S No.</th>
                   <th style="text-align:right; border:1px solid #696666c4; font-weight:bold;" width="13%">Due Date</th>
                   <th style="text-align:right; border:1px solid #696666c4; font-weight:bold;" width="15%">Due Amount</th>
                   <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="13%">Paid Date(s)</th>
                   <th style="text-align:right; border:1px solid #696666c4; font-weight:bold;" width="15%">Paid Amount</th>
                   <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="12%">Receipt No(s)</th>
                   <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="22%">Narration</th>
                 </tr>
               </thead>
               <tbody>';

    $esc_due = 0;
    $esc_paid = 0;
    $serial = 1;

    foreach ($print_info['charges1'] as $charge) {
        $due = floatval(str_replace(',', '', $charge['due_amount']));
        $paid = floatval(str_replace(',', '', $charge['paid_amount'] ?? 0));
        $esc_due += $due;
        $esc_paid += $paid;

        $paid_dates = !empty($charge['paid_dates']) 
                        ? implode(', ', (array)$charge['paid_dates']) 
                        : '-';
        $receipts = !empty($charge['receipt_nos']) 
                        ? implode(', ', (array)$charge['receipt_nos']) 
                        : '-';
        $narration = htmlspecialchars($charge['narration']);

        $html .= '<tr>
                    <td style="border:1px solid #696666c4;">' . $serial . '</td>
                    <td style="text-align:right; border:1px solid #696666c4;">' . htmlspecialchars($charge['due_date']) . '</td>
                    <td style="text-align:right; border:1px solid #696666c4;">' . safe_number($due) . '</td>
                    <td style="text-align:left; border:1px solid #696666c4;">' . $paid_dates . '</td>
                    <td style="text-align:right; border:1px solid #696666c4;">' . safe_number($paid) . '</td>
                    <td style="text-align:left; border:1px solid #696666c4;">' . $receipts . '</td>
                    <td style="text-align:left; border:1px solid #696666c4;">' . $narration . '</td>
                  </tr>';
        $serial++;
    }

    $esc_balance = $esc_due - $esc_paid;
    $style = ($esc_balance == 0) 
             ? 'background-color: #d4edda;' 
             : 'background-color: #f8d7da;';

    $html .= '<tr style="' . $style . '">
                <td colspan="2" style="border:1px solid #696666c4; text-align:right; font-weight:bold;">Total Due</td>
                <td style="border:1px solid #696666c4; text-align:right; font-weight:bold;">' . safe_number($esc_due) . '</td>
                <td style="border:1px solid #696666c4;"></td>
                <td style="border:1px solid #696666c4; text-align:right; font-weight:bold;">' . safe_number($esc_paid) . '</td>
                <td style="border:1px solid #696666c4;"></td>
                <td style="border:1px solid #696666c4; text-align:right; font-weight:bold;">' . safe_number($esc_balance) . '</td>
              </tr>';

    $html .= '</tbody></table><br><br>';

    $pdf->writeHTML($html, false, false, true, false, '');
    
    
    $html = '
         <table style="border-collapse: collapse; border: 1px solid black;" cellspacing="0" cellpadding="4">
               <thead style="background:#666; border-color:#ccc; color:#fff;">
                 <tr>
                   <th style="text-align:left; border:1px solid black; font-weight:bold;" width="50%">Total Cost of Unit Paid Including Escalation.</th>
                   <th style="text-align:left; border:1px solid black; font-weight:bold;" width="50%">&nbsp;&nbsp;' .  safe_number($total_paid+$esc_paid) . '</th>
                 </tr>
               </thead>
               
         </table><br><br>';
    
    $pdf->writeHTML($html, false, false, true, false, '');

    // ================ OTHER CHARGES ================
    
    $html = '<table style="background-color:#000; padding:3px; color:#fff;">
            <tr><td><h3>Other Charges</h3></td></tr>
         </table><br><br>
         <table style="border-collapse: collapse;" cellspacing="0" cellpadding="4">
           <thead style="background:#666; border-color:#ccc; color:#fff;">
             <tr>
               <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="8%">S No.</th>
               <th style="text-align:right; border:1px solid #696666c4; font-weight:bold;" width="13%">Due Date</th>
               <th style="text-align:right; border:1px solid #696666c4; font-weight:bold;" width="15%">Due Amount</th>
               <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="13%">Paid Date(s)</th>
               <th style="text-align:right; border:1px solid #696666c4; font-weight:bold;" width="15%">Paid Amount</th>
               <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="13%">Receipt No(s)</th>
               <th style="text-align:left; border:1px solid #696666c4; font-weight:bold;" width="22%">Narration</th>
             </tr>
           </thead>
           <tbody>';

    $other_due = 0;
    $other_paid = 0;
    $serial = 1;
    
    foreach ($print_info['charges2'] as $charge) {
        $due = floatval(str_replace(',', '', $charge['charges_amount']));
        
        // Sum of all paid amounts
        $paid_amounts = array_map(function($amt) {
            return floatval(str_replace(',', '', $amt));
        }, $charge['charges_paid_amount'] ?? []);
        $paid = array_sum($paid_amounts);
    
        $other_due += $due;
        $other_paid += $paid;
    
        // Format dates and receipts
        $paid_dates = !empty($charge['charges_paid_date']) 
                        ? implode(', ', (array)$charge['charges_paid_date']) 
                        : '-';
        $receipts = !empty($charge['charges_paid_receipt']) 
                        ? implode(', ', (array)$charge['charges_paid_receipt']) 
                        : '-';
        $narration = htmlspecialchars($charge['charges_narration']);
    
        $html .= '<tr>
                    <td style="border:1px solid #696666c4;" width="8%">' . $serial . '</td>
                    <td style="text-align:right; border:1px solid #696666c4;" width="13%">' . htmlspecialchars($charge['charges_date']) . '</td>
                    <td style="text-align:right; border:1px solid #696666c4;" width="15%">' . safe_number($due) . '</td>
                    <td style="text-align:left; border:1px solid #696666c4;" width="13%">' . $paid_dates . '</td>
                    <td style="text-align:right; border:1px solid #696666c4;" width="15%">' . safe_number($paid) . '</td>
                    <td style="text-align:left; border:1px solid #696666c4;" width="13%">' . $receipts . '</td>
                    <td style="text-align:left; border:1px solid #696666c4;" width="22%">' . $narration . '</td>
                  </tr>';
        $serial++;
    }
    
    
    // Calculate balance
    $other_balance = $other_due - $other_paid;
    $style = ($other_balance == 0) 
                ? 'background-color: #d4edda;' // light green
                : 'background-color: #f8d7da;'; // light red
    
    // Totals row
    $html .= '<tr style="' . $style . '">
                <td colspan="2" style="border:1px solid #696666c4; text-align:right; font-weight:bold;">Total Due</td>
                <td style="border:1px solid #696666c4; text-align:right; font-weight:bold;">' . safe_number($other_due) . '</td>
                <td style="border:1px solid #696666c4;font-weight:bold;">Total Due</td>
                <td style="border:1px solid #696666c4; text-align:right; font-weight:bold;">' . safe_number($other_paid) . '</td>
                <td style="border:1px solid #696666c4;font-weight:bold;">Balance</td>
                <td style="border:1px solid #696666c4; text-align:right; font-weight:bold;">' . safe_number($other_balance) . '</td>
              </tr>';
    
    $html .= '</tbody></table><br><br>';
    
    // Output to PDF
    $pdf->writeHTML($html, false, false, true, false, '');

    

  

  $pdf->lastPage();
  $pdf->Output('Account_Statement_' . $print_info['membership_no'] . '.pdf', 'I');

//   unset($_SESSION['print_info']);
//   unset($_SESSION['print_info_expires']);
} else {
  header("Location: https://faisaltown.com.pk/portal/members_portal/index.php");
  die("No valid session found. Please go back and generate the statement again.");
}