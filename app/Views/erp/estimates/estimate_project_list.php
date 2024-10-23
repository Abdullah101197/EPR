<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\EstimatesModel;
use App\Models\ConstantsModel;



$session = \Config\Services::session();
$usession = $session->get('sup_username');
$UsersModel = new UsersModel();
$RolesModel = new RolesModel();
$SystemModel = new SystemModel();
$EstimatesModel = new EstimatesModel();
$ConstantsModel = new ConstantsModel();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$xin_system = erp_company_settings();

if ($user_info['user_type'] == 'staff') {
  $get_invoices = $EstimatesModel->where('added_by', $usession['sup_user_id'])
    ->orderBy('created_at', 'DESC')
    ->paginate(8);

  $count_invoices = $EstimatesModel->where('added_by', $usession['sup_user_id'])
    ->countAllResults();
  $pager = $EstimatesModel->pager;
  $company_id = $usession['sup_user_id'];
} else {

  $get_invoices = $EstimatesModel->where('company_id', $usession['sup_user_id'])
    ->orderBy('created_at', 'DESC')
    ->paginate(8);
  $count_invoices = $EstimatesModel->where('company_id', $usession['sup_user_id'])

    ->countAllResults();
  $company_id = $usession['sup_user_id'];
  $pager = $EstimatesModel->pager;
}
$unpaid = $EstimatesModel->where('company_id', $company_id)->where('status', 0)->countAllResults();
$paid = $EstimatesModel->where('company_id', $company_id)->where('status', 1)->countAllResults();
/*
* All Project Estimates View
*/
?>
<?php if (in_array('invoice2', staff_role_resource()) || in_array('invoice_calendar', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
  <div id="smartwizard-2" class="border-bottom smartwizard-example sw-main sw-theme-default mt-2">
    <ul class="nav nav-tabs step-anchor">
      <?php if (in_array('invoice2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
        <li class="nav-item active"> <a href="<?= site_url('erp/estimates-list'); ?>" class="mb-3 nav-link"> <span class="sw-done-icon feather icon-check-circle"></span> <span class="sw-icon feather icon-calendar"></span>
            <?= lang('Dashboard.xin_estimates'); ?>
            <div class="text-muted small">
              <?= lang('Main.xin_set_up'); ?>
              <?= lang('Dashboard.xin_estimates'); ?>
            </div>
          </a> </li>
      <?php } ?>
      <?php if (in_array('estimates_calendar', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
        <li class="nav-item clickable"> <a href="<?= site_url('erp/estimates-calendar'); ?>" class="mb-3 nav-link"> <span class="sw-done-icon feather icon-check-circle"></span> <span class="sw-icon fas fa-calendar-plus"></span>
            <?= lang('Dashboard.xin_quote_calendar'); ?>
            <div class="text-muted small">
              <?= lang('Dashboard.xin_quote_calendar'); ?>
            </div>
          </a> </li>
      <?php } ?>
    </ul>
  </div>
  <hr class="border-light m-0 mb-3">
<?php } ?>
<div class="row">
  <!-- [ invoice-list ] start -->
  <!-- [ right ] start -->
  <div class="col-xl-12 col-lg-12 filter-bar invoice-list">
    <nav class="navbar m-b-30 p-10">
      <ul class="nav">
        <li class="nav-item f-text active">
          <?= lang('Main.xin_list_all'); ?>
          <?= lang('Dashboard.xin_estimates'); ?>
        </li>
      </ul>
      <?php if (in_array('estimate3', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
        <div class="nav-item nav-grid f-view"> <a href="<?= site_url() . 'erp/create-new-estimate'; ?>" class="btn waves-effect waves-light btn-primary btn-sm m-0"> <i data-feather="plus"></i>
            <?= lang('Main.xin_create_new_estimate'); ?>
          </a> </div>
      <?php } ?>
    </nav>
    <div class="card">
        <div class="card-body">
        <div class="box-datatable table-responsive">
          <table class="datatables-demo table table-striped table-bordered" id="xin_estimates_table" style="width:100%;">
            <thead>
              <tr>
                <th>Invoice #</th>
                <th>Title</th>
                <th>Attend To</th>
                <th>Client</th>
                <th>Status</th>
                <th>Created By</th>
                <th><i class="fa fa-calendar"></i> Created AT</th>
                <th> Action</th>
              </tr>
            </thead>
          </table>
        </div>
        </div>
    </div>
  </div>
  <!-- [ invoice-list ] end -->
</div>
<hr>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {    
    var xin_estimates_table = $('#xin_estimates_table').DataTable({
        "bDestroy": true,
        "ajax": {
            url : main_url + "projects/estimates_list",
            type : 'GET'
        },
        "columns": [
            { "data": "invoice_number" },
            { "data": "title" },
            { "data": "attend_to" },
            { "data": "client" },
            { "data": "status" },
            { "data": "created_by" },
            { "data": "created_at" },
            { "data": "action" },
        ],
        "order": [[7, 'desc']],
        "language": {
            "lengthMenu": dt_lengthMenu,
            "zeroRecords": dt_zeroRecords,
            "info": dt_info,
            "infoEmpty": dt_infoEmpty,
            "infoFiltered": dt_infoFiltered,
            "search": dt_search,
            "paginate": {
                "first": dt_first,
                "previous": dt_previous,
                "next": dt_next,
                "last": dt_last
            }
        },
        "fnDrawCallback": function(settings){
            $('[data-toggle="tooltip"]').tooltip();          
        }
    });
});

</script>