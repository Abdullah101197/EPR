<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\EstimatesModel;
use App\Models\ProjectsModel;
use App\Models\ConstantsModel;
use App\Models\EstimatesModuleModel;
use App\Models\EstimatesitemsModel;
use App\Models\EstimatesProductsModel; // Add this model for product details
error_reporting(E_ALL);
ini_set('display_errors', 1);
$request = \Config\Services::request();
$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$EstimatesModel = new EstimatesModel();
$ProjectsModel = new ProjectsModel();
$ConstantsModel = new ConstantsModel();
$EstimatesitemsModel = new EstimatesitemsModel();
$EstimatesModuleModel = new EstimatesModuleModel();
$EstimatesProductsModel = new EstimatesProductsModel(); // Instantiate the model

/* Company Details view */
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$segment_id = $request->uri->getSegment(3);

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$invoice_id = udecode($segment_id);
$xin_system = erp_company_settings();
$inv_company_info = $UsersModel->where('user_id', $xin_system['company_id'])->first();
$result = $EstimatesModel->where('estimates_id', $invoice_id)->first();
$company_info = $UsersModel->where('user_id', $result['client_id'])->where('user_type', 'customer')->first();

if ($company_info) {
  $company_contact = $company_info['first_name'] . ' ' . $company_info['last_name'];
  $address_1 = $company_info['address_1'] . ' ' . $company_info['address_2'];
  $cemail = $company_info['email'];
  $ccontact_number = $company_info['contact_number'];
  $csz = $company_info['city'] . ', ' . $company_info['state'] . ' ' . $company_info['zipcode'];
} else {
  $csz = '--';
  $cemail = '--';
  $address_1 = '--';
  $ccontact_number = '--';
  $company_contact = '--';
}
$attend_info = $UsersModel->where('user_id', $result['estimate_attend_to'])->where('user_type', 'staff')->first();
if ($attend_info) {
  $attend_name = $attend_info['first_name'] . ' ' . $attend_info['last_name'];
  $attend_email = $attend_info['email'];
  $attend_contact = $attend_info['contact_number'];
} else {
  $attend_name = '--';
  $attend_email = '--';
  $attend_contact = '--';
}
$added_by = $UsersModel->where('user_id', $result['added_by'])->first();
$added_by = $added_by['first_name'] . ' ' . $added_by['last_name'];

$invoice_items = json_decode($result['estimate_module'], true);
$overall_totals = []; // Initialize overall totals

if (isset($invoice_items)) {
  foreach ($invoice_items['overall_totals'] as $machineType => $totals) {
    $overall_totals[$machineType] = [
      'capacity' => isset($totals['capacity']) ? (float)$totals['capacity'] : 0,
      'qty' => isset($totals['qty']) ? (float)$totals['qty'] : 0,
    ];
  }
}
$ci_erp_settings = $SystemModel->where('setting_id', 1)->first();
$address_1 = $company_info['address_1'] . ' ' . $company_info['address_2'];
$csz = $company_info['city'] . ', ' . $company_info['state'] . ' ' . $company_info['zipcode'];
$status = $result['status'] == 0 ? '<span class="badge badge-light-primary">' . lang('Main.xin_estimated') . '</span>' : ($result['status'] == 1 ? '<span class="badge badge-light-success">' . lang('Main.xin_invoiced') . '</span>' :
    '<span class="badge badge-light-danger">' . lang('Projects.xin_project_cancelled') . '</span>');

$_payment_method = $ConstantsModel->where('type', 'payment_method')->where('constants_id', $result['payment_method'])->first();
$ipayment_method = $_payment_method ? $_payment_method['category_name'] : '--';

$total_capacity = 0;
$total_qty = 0;
?>
<style>
  .table {
    width: 100%;
    border-collapse: collapse;
  }

  .table th,
  .table td {
    padding: 8px;
    text-align: left;
    vertical-align: top;
  }

  .table .description-column {
    max-width: 135px;
    /* Adjust as needed */
    word-wrap: break-word;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .table td {
    overflow: hidden;
  }
</style>
<div class="row justify-content-md-center print-invoice">
  <!-- [ basic-alert ] start -->
  <div class="col-md-12">
    <!-- [ Estimate ] start -->
    <div class="container">
      <div>
        <div class="card" id="printTable">
          <div class="card-header">
            <!-- <h5>
              <?= lang('Main.xin_view_estimate') . ' #' . $result['estimate_number']; ?>
            </h5> -->
            <div class="card-header-right">
              <a href="<?= site_url('erp/print-estimate/') . $segment_id; ?>" target="_blank" class="collapsed btn btn-sm waves-effect waves-light btn-success m-0">
                <?= lang('Invoices.xin_print_download_invoice'); ?>
              </a>
              <?php if ($user_info['user_type'] == 'company') { ?>
                <?php if ($result['status'] == 0) { ?>
                  <?php if ($user_info['user_type'] == 'company') { ?>
                    <button type="button" class="btn btn-sm btn-info approve-estimate" data-field_id="<?php echo $result['estimates_id']; ?>">
                      Approve
                    </button>
                  <?php } ?>
                  <?php if ($user_info['user_type'] == 'company') { ?>
                    <!-- <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-field_id="<?php echo $segment_id; ?>" data-target=".view-modal-data">
                      <?= lang('Main.xin_cancel_estimate'); ?>
                    </button> -->
                  <?php } ?>

                <?php } ?>
                <?php if ($user_info['user_type'] == 'company') { ?>
                  <a href="<?= site_url('erp/edit-estimate/') . $segment_id; ?>" class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0">
                    <?= lang('Main.xin_edit'); ?>
                  </a>
                <?php } ?>
              <?php } ?>
              <?php if ($user_info['user_type'] == 'staff') { ?>
                <?php if ($result['status'] == 0) { ?>
                  <a href="<?= site_url('erp/edit-estimate/') . $segment_id; ?>" class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0">
                    <?= lang('Main.xin_edit'); ?>
                  </a>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
          <div class="card-body" style="padding-top: 0;">
            <div class="row ">
              <div class="col-md-12">
                <div class="row">
                  <div class="container my-4">
                    <a href="#!" class="d-flex justify-content-start">
                      <img class="img-fluid mb-3" width="171" height="30" src="<?= base_url(); ?>/public/uploads/logo/other/<?= $ci_erp_settings['other_logo']; ?>" alt="<?= $inv_company_info['company_name']; ?>">
                    </a>
                    <!-- <div class="text-center">
                  <div class="mb-2">
                      <strong><?= $inv_company_info['company_name']; ?></strong>
                  </div>
                  <div class="mb-2">
                      <?= $inv_company_info['address_1']; ?>, <br><?= $inv_company_info['address_2']; ?>
                  </div>
                  <div class="mb-2">
                      <a class="text-secondary" href="mailto:<?= $inv_company_info['email']; ?>" target="_top">
                          <?= $inv_company_info['email']; ?>
                      </a>
                  </div>
                  <div>
                      <?= $inv_company_info['contact_number']; ?>
                  </div>
                  </div> -->

                  </div>
                </div>
              </div>

              <div class="col-md-4"></div>
            </div>
            <hr>
            <div class="row invoive-info d-print-inline-flex">
              <div class="col-sm-6 invoice-client-info">
                <h6>Estimate Information:</h6>
                <table class="table table-responsive invoice-table invoice-order table-borderless">
                  <tbody>
                    <tr>
                      <th>Project Name:</th>
                      <td><?= $result['estimate_title']; ?></td>
                    </tr>
                    <tr>
                      <th>Ref No.:</th>
                      <td><?= $result['ref_no']; ?></td>
                    </tr>
                    <tr>
                      <th><?= lang('Main.xin_e_details_date'); ?>:</th>
                      <td><?= date($result['estimate_date']); ?></td>
                    </tr>
                    <tr>
                      <th><?= lang('Main.dashboard_xin_status'); ?>:</th>
                      <td><?php
                          if ($result['status'] == 1) {
                            echo '<span class="badge-pill badge-success" style="font-size: 12px">Approved</span>';
                          } else {
                            echo '<span class="badge-pill badge-warning" style="font-size: 12px">Pending</span>';
                          }
                          ?></td>
                    </tr>
                    <?php if ($result['status'] == 1) { ?>
                      <tr>
                        <th><?= lang('Invoices.xin_payment'); ?>:</th>
                        <td><?= $ipayment_method; ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
              <div class="col-sm-6 invoice-client-info">
                <h6><?= lang('Main.xin_client_info'); ?>:</h6>


                <table class="table table-responsive invoice-table invoice-order table-borderless">
                  <tbody>
                    <tr>
                      <th>Name:</th>
                      <td><?= $company_contact; ?></td>
                    </tr>
                    <tr>
                      <th>Contact:</th>
                      <td><?= $ccontact_number; ?></td>
                    </tr>

                  </tbody>
                </table>


                <!-- <h6 class="m-0"><?= $company_contact; ?></h6>
                <p class="m-0 m-t-10"><?= $address_1; ?><br><?= $csz; ?></p>
                <p class="m-0"><?= $ccontact_number; ?></p>
                <p><a class="text-secondary" href="mailto:<?= $cemail; ?>" target="_top"><?= $cemail; ?></a></p> -->
              </div>
              <div class="col-sm-6 invoice-client-info">
                <h6>Company Information:</h6>
                <table class="table table-responsive invoice-table invoice-order table-borderless">
                  <tbody>
                    <tr>
                      <th>Company Name:</th>
                      <td>Al-Rawnaq ICP</td>
                    </tr>
                    <tr>
                      <th>Contact:</th>
                      <td><?= $attend_contact; ?></td>
                    </tr>
                    <tr>
                      <th>Email:</th>
                      <td><?= $attend_email; ?></td>
                    </tr>

                  </tbody>
                </table>
              </div>
              <div class="col-sm-6 invoice-client-info">
                <h6>Other Information:</h6>
                <table class="table table-responsive invoice-table invoice-order table-borderless">
                  <tbody>
                    <tr>
                      <th>Prepared By:</th>
                      <td><?= $added_by; ?></td>
                    </tr>
                    <tr>
                      <th>Followed By:</th>
                      <td><?= $attend_name; ?></td>
                    </tr>

                  </tbody>
                </table>
              </div>

            </div>

            <div class="">
              <hr>
              <h3>Gentlemen</h3>
              <p><strong>We are thankful for your enquiry and we are pleased to quote our best price as follow. Cooling Calculation is Mention below</strong></p>
              <div class="d-flex" style="direction:rtl;">
                <h3>السادة الافاضل :</h3>
              </div>
              <div class="d-flex" style="direction:rtl;">
                <p><strong> نحن ممتنون لاستفساركم , و يسعدنا ان نقدم لكم افضل الاسعار مع حصر الكميات و المعدات حسب ما هو مبيين ادناه .</strong></p>
              </div>

              <hr>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="table-responsive">
                  <table class="table invoice-detail-table">
                    <thead>
                      <tr class="thead-default">
                        <th><?= lang('Main.xin_location'); ?></th>
                        <th>Area</th>
                        <th>Brand</th>
                        <th><?= lang('Main.xin_model_code'); ?></th>
                        <th>Price</th>
                        <th><?= lang('Main.xin_qty'); ?></th>
                        <th><?= lang('Main.xin_capacity'); ?></th> <!-- Added Capacity -->
                        <th class="description-column">Description</th> <!-- Added class -->
                        <th><?= lang('Main.xin_machine_type'); ?> Type</th> <!-- Added Machine Type -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $total_capacity = 0;
                      $total_qty = 0;
                      $grand_total = 0;
                      $overall_totals = [];
                      $brand_names = [];
                      foreach ($invoice_items as $module):
                        if (isset($module['items']) && is_array($module['items'])):
                          $module_totals_by_type = [];

                          echo '<tr><td colspan="9" class="font-weight-bold text-center">' . htmlspecialchars($module['module_name'] ?? '') . '</td></tr>';

                          foreach ($module['items'] as $item):
                            $brand = $ConstantsModel->where('constants_id', $item['brand'])->first();
                            $brand_name = $brand ? $brand['category_name'] : '';
                            $product_info = $EstimatesProductsModel->find($item['model_code']);
                            $machine_info = $ConstantsModel->find($item['machine_type_id']);

                            if ($brand_name) {
                              $brand_names[] = $brand_name;
                            }

                            $item_total = isset($item['qty']) && isset($item['price']) ? $item['qty'] * $item['price'] : 0;

                            if (!isset($module_totals_by_type[$machine_info['category_name']])) {
                              $module_totals_by_type[$machine_info['category_name']] = [
                                'capacity' => 0,
                                'qty' => 0
                              ];
                            }

                            $module_totals_by_type[$machine_info['category_name']]['capacity'] += $product_info['capacity'];
                            $module_totals_by_type[$machine_info['category_name']]['qty'] += $item['qty'];

                            $total_capacity += $product_info['capacity'];
                            $total_qty += $item['qty'];
                            $grand_total += $item_total;

                            // Accumulate overall totals by machine type
                            if (!isset($overall_totals[$machine_info['category_name']])) {
                              $overall_totals[$machine_info['category_name']] = [
                                'capacity' => 0,
                                'qty' => 0
                              ];
                            }

                            $overall_totals[$machine_info['category_name']]['capacity'] += $product_info['capacity'];
                            $overall_totals[$machine_info['category_name']]['qty'] += $item['qty'];
                      ?>
                            <tr>
                              <td><?= htmlspecialchars($item['location']); ?></td>
                              <td><?= htmlspecialchars($item['area']); ?></td>
                              <td><?= htmlspecialchars($brand_name); ?></td>
                              <td><?= htmlspecialchars($product_info['model_code']); ?></td>
                              <td><?= number_to_currency($item['price'], $xin_system['default_currency'], null, 2); ?></td>
                              <td><?= htmlspecialchars($item['qty']); ?></td>
                              <td><?= htmlspecialchars($product_info['capacity']); ?></td>
                              <td class="description-column">
                                <a href="#" class="view-description" data-description="<?= html_entity_decode($item['description']); ?>" data-toggle="modal" data-target="#descriptionModal">
                                  <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                                <?= html_entity_decode($item['description']); ?>
                              </td>
                              <td><?= htmlspecialchars($machine_info['category_name']); ?></td>
                            </tr>
                          <?php
                          endforeach;
                          ?>

                          <?php foreach ($module_totals_by_type as $machineType => $totals): ?>
                            <tr>
                              <td colspan="5" class="text-center"><strong><?= htmlspecialchars($machineType); ?></strong></td>
                              <td><strong><?= htmlspecialchars($totals['qty']); ?></strong></td>
                              <td><strong><?= htmlspecialchars($totals['capacity']); ?></strong></td>
                              <td colspan="2"></td>
                            </tr>
                          <?php endforeach; ?>

                      <?php
                        endif;
                      endforeach;
                      ?>

                      <!-- Overall Machine Type Totals at the end of the table -->
                      <tr class="text-center">
                        <td colspan="9"><strong>Totals Machine Types </strong></td>
                      </tr>
                      <?php foreach ($overall_totals as $machineType => $totals): ?>
                        <tr>
                          <td colspan="5" class="text-center"><strong><?= htmlspecialchars($machineType); ?></strong></td>
                          <td><strong><?= htmlspecialchars($totals['qty']); ?></strong></td>
                          <td><strong><?= htmlspecialchars($totals['capacity']); ?></strong></td>
                          <td colspan="2"></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>




                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">

                <p><?= isset($result['terms_conditions']) ? html_entity_decode($result['terms_conditions']) : ''; ?></p>
                <div class="d-flex pt-2 pb-2" style="justify-content: space-between;">
                <h2>Price</h2>
                <h2>جدول السعر</h2>
                </div>
                
                <table style="width: 100%;">
                <?php
                  $unique_brand_names = array_unique($brand_names);

                  $all_brands = implode(', ', $unique_brand_names);

                  ?>

                  <?php
                  $project_cost = $result['project_cost']; // Assuming project cost is available in this variable
                  $discount_percentage = $result['discount']; // Assuming discount percentage is available in this variable

                  // Calculate the discount amount
                  $discount_amount = ($discount_percentage / 100) * $project_cost;

                  // Calculate the total price after applying the discount
                  $total_price = $project_cost - $discount_amount;
                  ?>

                  <tbody>
                      <tr style="height:23.15pt;">
                          <td style="width:50%;border-width:1px;border-style:solid;border-color:#000000;text-align:center;vertical-align:middle;"><?= $all_brands; ?></td>
                          <td style="width:50%;border-width:1px;border-style:solid;border-color:#000000;text-align:center;vertical-align:middle;">البراند</td>
                      </tr>
                      <tr style="height:29.95pt;">
                          <td style="width:50%;border-width:1px;border-style:solid;border-color:#000000;text-align:center;vertical-align:middle;"><?= number_to_currency($project_cost, $xin_system['default_currency'], null, 2); ?></td>
                          <td style="width:50%;border-width:1px;border-style:solid;border-color:#000000;text-align:center;vertical-align:middle;">المجموع</td>
                      </tr>
                      <tr style="height:44.95pt;">
                          <td style="width:50%;border-width:1px;border-style:solid;border-color:#000000;text-align:center;vertical-align:middle;"><?= htmlspecialchars($discount_percentage); ?>%</td>
                          <td style="width:50%;border-width:1px;border-style:solid;border-color:#000000;text-align:center;vertical-align:middle;">الخصم</td>
                      </tr>
                      <tr style="height:44.95pt;">
                          <td style="width:50%;border-width:1px;border-style:solid;border-color:#000000;text-align:center;vertical-align:middle;">
                              <?= number_to_currency($total_price, $xin_system['default_currency'], null, 2); ?>
                          </td>
                          <td style="width:50%;border-width:1px;border-style:solid;border-color:#000000;text-align:center;vertical-align:middle;">السعر النهائي</td>
                      </tr>
                  </tbody>

                </table>
                <p><?= isset($result['general_terms_conditions']) ? html_entity_decode($result['general_terms_conditions']) : ''; ?></p>
              </div>
                      
              <div class="col-md-6" style="margin-top: 40px;">
                <h4 class="text-center">Signature</h4>
                <h4 class="text-center" style="direction:rtl;">امضاء</h4>
              </div>
              <div class="col-md-6" style="margin-top: 40px;">
                <h4 class="text-center">Client Signature</h4>
                <h4 class="text-center" style="direction:rtl;">امضاء العميل</h4>
              </div>
              <div class="col-md-12 text-center pt-4">
              <p class="text-center"><strong>Email</strong> <a href="mailto:info@alrawnaqicp.com">info@alrawnaqicp.com</a>, <strong>CR:</strong> 65278 <strong>website. <a href="https://alrawnaqicp.com/">www.alrawnaqicp.com </a></strong><strong>Post box</strong> 96525, <strong>Phone:</strong> <a href="tel:+974 4444 061">+974 4444 061</a></p>
                <p>ALRAWNAQ INTERNATIONAL COMMERCIAL PROJECTS <small>LLC</small> is a limited liability company</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- [ Estimate ] end -->
    </div>
  </div>
  <!-- Description Modal -->
  <div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="descriptionModalLabel">Description</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="fullDescription"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <script>
    $(document).ready(function() {
      $('.view-description').on('click', function(e) {
        e.preventDefault();
        var description = $(this).data('description');
        $('#fullDescription').text(description);
      });
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Handle click on Approve button
      document.querySelectorAll('.approve-estimate').forEach(function(button) {
        button.addEventListener('click', function() {
          var estimateId = this.getAttribute('data-field_id');
          approveEstimate(estimateId);
        });
      });

      function approveEstimate(estimateId) {
        Swal.fire({
          title: 'Are you sure?',
          text: "Do you really want to approve this estimate?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Approve',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: 'approve-estimate/' + estimateId,
              method: 'GET',
              success: function(response) {
                Swal.fire(
                  'Approved!',
                  'The estimate has been approved.',
                  'success'
                );
                location.reload();
              },
              error: function(xhr, status, error) {
                Swal.fire(
                  'Error!',
                  'There was a problem approving the estimate.',
                  'error'
                );
              }
            });
          }
        });
      }

    });
  </script>

  <script>
    document.querySelectorAll('table').forEach(table => {
      table.style.width = '100%';
    });
  </script>