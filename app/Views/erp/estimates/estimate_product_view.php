<?php
use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\WarehouseModel;
use App\Models\ConstantsModel;
use App\Models\ProductsModel;
use App\Models\EstimatesModel;
use App\Models\EstimatesProductsModel;
use App\Models\Moduleattributes;
use App\Models\Moduleattributesval;
use App\Models\Moduleattributesvalsel;
use App\Models\MachineTypeModel;


$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$UsersModel = new UsersModel();		
$WarehouseModel = new WarehouseModel();	
$SystemModel = new SystemModel();
$ConstantsModel = new ConstantsModel();
$ProductsModel = new ProductsModel();
$EstimatesModel = new EstimatesModel();
$EstimatesProductsModel = new EstimatesProductsModel();
$Moduleattributes = new Moduleattributes();
$Moduleattributesval = new Moduleattributesval();
$Moduleattributesvalsel = new Moduleattributesvalsel();
$MachineTypeModel = new MachineTypeModel();


$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if($user_info['user_type'] == 'staff'){
	$icategory_info = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type','estimate_brand')->findAll();
	$result = $EstimatesProductsModel->where('company_id',$user_info['company_id'])->orderBy('estimates_products_id', 'ASC')->findAll();
	
} else {
	$icategory_info = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type','estimate_brand')->findAll();
	$result = $EstimatesProductsModel->where('company_id',$usession['sup_user_id'])->orderBy('estimates_products_id', 'ASC')->findAll();
}
if($user_info['user_type'] == 'staff'){
	$machine_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type','estimate_category')->findAll();
} else {

  $machine_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type','estimate_category')->findAll();
}

$xin_system = erp_company_settings();

$segment_id = $request->uri->getSegment(3);
$estimates_id = udecode($segment_id);
$result = $EstimatesProductsModel->where('estimates_products_id', $estimates_id)->first();	

?>

<div class="row"> 
  
  <!-- [ trackgoal-detail-left ] end --> 
  <!-- [ trackgoal-detail-right ] start -->
  <div class="col-lg-12">
   <div class="bg-light card mb-2">
      <div class="card-body">
        <ul class="nav nav-pills mb-0">
          <li class="nav-item m-r-5"> <a href="#pills-edit" data-toggle="tab" aria-expanded="false" class="">
            <button type="button" class="btn btn-shadow btn-secondary text-uppercase">
            <?= lang('Main.xin_edit');?>
            </button>
            </a> </li>
          
        </ul>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h5><i class="feather icon-package mr-1"></i><?php echo lang('Inventory.xin_product_details');?>
        </h5>
      </div>
      <div class="tab-content" id="pills-tabContent">
        <?php if(in_array('tracking3',staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
        <div class="tab-pane fade show active" id="pills-edit" role="tabpanel" aria-labelledby="pills-edit-tab">
          <?php $attributes = array('name' => 'update_product', 'id' => 'update_product', 'autocomplete' => 'off', 'class' => 'form-hrm');?>
		  <?php $hidden = array('token' => $segment_id);?>
          <?php echo form_open('erp/estimates/update_estimate_product', $attributes, $hidden);?>
          <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="asset_name" class="control-label">
                      <?= lang('Inventory.xin_product_name');?>
                      <span class="text-danger">*</span> </label>
                    <input class="form-control" placeholder="<?= lang('Inventory.xin_product_name');?>" name="name" type="text" value="<?= $result['product_name'];?>">
                  </div>
                </div>

                <div class="col-md-4">
                      <div class="form-group">
                        <label for="asset_name" class="control-label">
                          <?= lang('Inventory.xin_model_code');?>
                          <span class="text-danger">*</span> </label>
                        <input class="form-control" placeholder="<?= lang('Inventory.xin_model_code');?>" name="model_code" type="text" value="<?= $result['model_code'];?>">
                      </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="first_name">
                      <?= lang('Dashboard.xin_estimate_brand_single');?>
                      <span class="text-danger">*</span> </label>
                    <select class="form-control" name="category" data-plugin="select_hrm" data-placeholder="<?= lang('Dashboard.xin_estimate_brand_single');?>">
                      <option value=""></option>
                      <?php foreach($icategory_info as $pd_category) {?>
                      <option value="<?= $pd_category['constants_id']?>" <?php if($result['category_id']==$pd_category['constants_id']):?> selected="selected"<?php endif;?>>
                      <?= $pd_category['category_name']?>
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
                          <option value="<?= $assets_category['constants_id']?>" <?php if($result['machine_type_id']==$assets_category['constants_id']):?> selected="selected"<?php endif;?>>
                          <?= $assets_category['category_name']?>
                          </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="product_qty">
                      <?= lang('Inventory.xin_product_qty_initial');?>
                    </label>
                    <input class="form-control" name="qty" type="number" value="<?= $result['product_qty'];?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="product_qty">
                      <?= lang('Inventory.xin_product_capacity');?> <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" placeholder="<?= lang('Inventory.xin_product_capacity');?>" name="capacity"  type="text" value="<?= $result['capacity'];?>">
                  </div>
                </div>
                
                
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="purchase_price">
                      <?= lang('Inventory.xin_product_purchase_price');?> <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" placeholder="<?= lang('Inventory.xin_product_purchase_price');?>" name="purchase_price" type="text" value="<?= $result['purchase_price'];?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="selling_price">
                      <?= lang('Inventory.xin_product_selling_price');?> <span class="text-danger">*</span>
                    </label>
                    <input class="form-control" placeholder="<?= lang('Inventory.xin_product_selling_price');?>" name="selling_price" type="text" value="<?= $result['retail_price'];?>">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="product_details">
                      <?= lang('Inventory.xin_product_details');?>
                    </label>
                    <textarea class="form-control editor" placeholder="<?= lang('Inventory.xin_product_details');?>" name="product_description" cols="30" rows="2"><?= $result['product_description'];?></textarea>
                  </div>
                </div>
              </div>
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">
            <?= lang('Inventory.xin_update_estimate');?>
            </button>
          </div>
          <?= form_close(); ?>
        </div>
        <?php } ?>
        
  </div>
</div>
</div>

<!-- [ trackgoal-detail-right ] end -->
</div>
