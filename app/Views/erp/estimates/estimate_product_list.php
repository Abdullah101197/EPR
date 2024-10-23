
<?php 
use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\ProductsModel;
use App\Models\WarehouseModel;
use App\Models\ConstantsModel;
use App\Models\MachineTypeModel;
use App\Models\EstimatesModelModel;

$session = \Config\Services::session();
$usession = $session->get('sup_username');

$UsersModel = new UsersModel();
$RolesModel = new RolesModel();		
$WarehouseModel = new WarehouseModel();
$ConstantsModel = new ConstantsModel();
$MachineTypeModel = new MachineTypeModel();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if($user_info['user_type'] == 'staff'){
	$category_info = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type','product_category')->findAll();
} else {
	$category_info = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type','estimate_brand')->findAll();
}

if($user_info['user_type'] == 'staff'){
	$machine_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type','estimate_category')->findAll();
} else {

  $machine_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type','estimate_category')->findAll();
}
/* Products view
*/
$get_animate = '';
?>

<div class="row m-b-1 animated fadeInRight">
  <div class="col-md-12">
    <?php if(in_array('product2',staff_role_resource()) || $user_info['user_type'] == 'company') {?>
    <div id="add_form" class="collapse add-form <?= $get_animate;?>" data-parent="#accordion" style="">
      <?php $attributes = array('name' => 'add_estimate_product', 'id' => 'xin-form', 'autocomplete' => 'off', 'class' => 'form');?>
      <?php $hidden = array('user_id' => 0);?>
      <?= form_open_multipart('erp/estimates/add_estimate_product', $attributes, $hidden);?>
      <div class="row">
        <div class="col-md-12">
          <div class="card mb-2">
            <div id="accordion">
              <div class="card-header">
                <h5>
                  <?= lang('Main.xin_add_new');?>
                  <?= lang('Inventory.xin_product');?>
                </h5>
                <div class="card-header-right"> <a  data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0"> <i data-feather="minus"></i>
                  <?= lang('Main.xin_hide');?>
                  </a> </div>
              </div>
              <div class="card-body">
                <div class="form-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="asset_name" class="control-label">
                          <?= lang('Inventory.xin_product_name');?>
                          <span class="text-danger">*</span> </label>
                        <input class="form-control" placeholder="<?= lang('Inventory.xin_product_name');?>" name="name" type="text" value="">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="asset_name" class="control-label">
                          <?= lang('Inventory.xin_model_code');?>
                          <span class="text-danger">*</span> </label>
                        <input class="form-control" placeholder="<?= lang('Inventory.xin_model_code');?>" name="model_code" type="text" value="">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="first_name">
                          <?= lang('Dashboard.xin_estimate_brand_single');?>
                          <span class="text-danger">*</span> </label>
                        <select class="form-control" name="category" data-plugin="select_hrm" data-placeholder="<?= lang('Dashboard.xin_estimate_brand_single');?>">
                          <option value=""></option>
                          <?php foreach($category_info as $assets_category) {?>
                          <option value="<?= $assets_category['constants_id']?>">
                          <?= $assets_category['category_name']?>
                          </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="first_name">
                          <?= lang('Inventory.xin_machine_type');?>
                          <span class="text-danger">*</span> </label>
                        <select class="form-control" name="machine_type_id" data-plugin="select_hrm" data-placeholder="<?= lang('Dashboard.xin_machine_type');?>">
                          <option value=""></option>
                          <?php foreach($machine_types as $assets_category) {?>
                          <option value="<?= $assets_category['constants_id']?>">
                          <?= $assets_category['category_name']?>
                          </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                   
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="product_qty">
                          <?= lang('Inventory.xin_product_qty_initial');?> <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" placeholder="<?= lang('Inventory.xin_product_qty_initial');?>" name="qty" type="number" value="">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="product_qty">
                          <?= lang('Inventory.xin_product_capacity');?> <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" placeholder="<?= lang('Inventory.xin_product_capacity');?>" name="capacity" type="text" value="">
                      </div>
                    </div>
                   
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="purchase_price">
                          <?= lang('Inventory.xin_product_purchase_price');?> <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" placeholder="<?= lang('Inventory.xin_product_purchase_price');?>" name="purchase_price" type="text" value="">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="selling_price">
                          <?= lang('Inventory.xin_product_selling_price');?> <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" placeholder="<?= lang('Inventory.xin_product_selling_price');?>" name="selling_price" type="text" value="">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="product_details">
                          <?= lang('Inventory.xin_product_details');?>
                        </label>
                        <textarea class="form-control editor" placeholder="<?= lang('Inventory.xin_product_details');?>" name="product_description" cols="30" rows="2"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type="reset" class="btn btn-light" href="#add_form" data-toggle="collapse" aria-expanded="false">
                <?= lang('Main.xin_reset');?>
                </button>
                &nbsp;
                <button type="submit" class="btn btn-primary">
                <?= lang('Main.xin_save');?>
                </button>
              </div>
            </div>
          </div>
        </div>
       
      </div>
      <?= form_close(); ?>
    </div>
    <?php } ?>
    <div class="card user-profile-list">
      <div class="card-header">
        <h5>
          <?= lang('Main.xin_list_all');?>
          <?= lang('Inventory.xin_products');?>
        </h5>
        <?php if(in_array('product2',staff_role_resource()) || $user_info['user_type'] == 'company') {?>
        <div class="card-header-right"> <a  data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0"> <i data-feather="plus"></i>
          <?= lang('Main.xin_add_new');?>
          </a> </div>
        <?php } ?>
      </div>
      <div class="card-body">
        <div class="box-datatable table-responsive">
          <table class="datatables-demo table table-striped table-bordered" id="xin_table">
            <thead>
              <tr>
                <th width="220"><?= lang('Inventory.xin_product_name');?></th>
                <th>Model Code</th>
                <th><?= lang('Dashboard.xin_brand');?></th>  
                <th><?= lang('Dashboard.xin_machine_type');?></th>  
                <th><?= lang('Inventory.xin_qty');?></th>
                <th><?= lang('Inventory.xin_product_capacity');?></th>
                <th><?= lang('Inventory.xin_product_purchase_price');?></th>
                <th><?= lang('Inventory.xin_product_selling_price');?></th>
                <th><i class="far fa-calendar-alt small"></i>
                  <?= lang('Main.xin_created_at');?></th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
