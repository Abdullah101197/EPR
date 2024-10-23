<?php

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\AssetsModel;
use App\Models\OfficialdocumentsModel;
use App\Models\ConstantsModel;
use App\Models\Moduleattributes;
use App\Models\Moduleattributesval;
use App\Models\Moduleattributesvalsel;
use App\Models\DepartmentModel;


$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$UsersModel = new UsersModel();		
$AssetsModel = new AssetsModel();	
$OfficialdocumentsModel = new OfficialdocumentsModel();	
$SystemModel = new SystemModel();
$ConstantsModel = new ConstantsModel();
$Moduleattributes = new Moduleattributes();
$Moduleattributesval = new Moduleattributesval();
$Moduleattributesvalsel = new Moduleattributesvalsel();
$DepartmentModel = new DepartmentModel();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if($user_info['user_type'] == 'staff'){
	$category_info = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type','assets_category')->findAll();
	$brand_info = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type','assets_brand')->findAll();
	$count_module_attributes = $Moduleattributes->where('company_id',$user_info['company_id'])->where('module_id',2)->orderBy('custom_field_id', 'ASC')->countAllResults();
	$module_attributes = $Moduleattributes->where('company_id',$user_info['company_id'])->where('module_id',2)->orderBy('custom_field_id', 'ASC')->findAll();
} else {
	$category_info = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type','assets_category')->findAll();
	$brand_info = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type','assets_brand')->findAll();
	$count_module_attributes = $Moduleattributes->where('company_id',$usession['sup_user_id'])->where('module_id',2)->orderBy('custom_field_id', 'ASC')->countAllResults();
	$module_attributes = $Moduleattributes->where('company_id',$usession['sup_user_id'])->where('module_id',2)->orderBy('custom_field_id', 'ASC')->findAll();
}



$xin_system = erp_company_settings();

$segment_id = $request->uri->getSegment(3);
$document_id = udecode($segment_id);

$result = $OfficialdocumentsModel->where('document_id', $document_id)->first();	
$department = $DepartmentModel->where('department_id', $result['department_id'])->first();	

?>

<div class="row">
  <div class="col-lg-4">
    <div class="card hdd-right-inner">
      <div class="card-header">
        <h5>
          <?= lang('Employees.xin_document_view');?>
        </h5>
      </div>
      <div class="card-body task-details">
        <table class="table">
          <tbody>
            <tr>
              <td><i class="fas fa-adjust m-r-5"></i> <?php echo lang('Employees.xin_document_name');?>:</td>
              <td class="text-right"><span class="float-right">
                <?= $result['license_name'];?>
                </span></td>
            </tr>
            <tr>
              <td><i class="far fa-calendar-alt m-r-5"></i> <?php echo lang('Dashboard.xin_category');?>:</td>
              <td class="text-right"> <?= ($result['document_category'] == 0 ) ? 'System' : 'Official' ;?></td>
            </tr>
            
            <tr>
              <td><i class="far fa-calendar-alt m-r-5"></i> <?php echo lang('Main.xin_created_at');?>:</td>
              <td class="text-right"><?= set_date_format($result['created_at']);?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="bg-light card mb-2">
      <div class="card-body">
        <ul class="nav nav-pills mb-0">
          <li class="nav-item m-r-5"> <a href="#pills-overview" data-toggle="tab" aria-expanded="false" class="">
            <button type="button" class="btn btn-shadow btn-secondary text-uppercase">
            <?= lang('Main.xin_overview');?>
            </button>
            </a> </li>
          <?php if(in_array('asset3',staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
          <li class="nav-item m-r-5"> <a href="#pills-edit" data-toggle="tab" aria-expanded="false" class="">
            <button type="button" class="btn btn-shadow btn-secondary text-uppercase">
            <?= lang('Main.xin_edit');?>
            </button>
            </a> </li>
          <li class="nav-item m-r-5"> <a href="#pills-image" data-toggle="tab" aria-expanded="false" class="">
            <button type="button" class="btn btn-shadow btn-secondary text-uppercase">
            <?= lang('Employees.xin_document_file');?>
            </button>
            </a> </li>
          <?php } ?>
        </ul>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5><i class="feather icon-lock mr-1"></i>
          <?= lang('Employees.xin_document_name');?>
          :
          <?= $result['license_name'];?>
        </h5>
      </div>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table m-b-0 f-14 b-solid requid-table">
                <tbody class="text-muted">
                  <tr>
                    <td><?php echo lang('Employees.xin_company');?></td>
                    <td><?= $result['company_id'];?></td>
                  </tr>
                  <tr>
                    <td><?php echo lang('Employees.xin_document_type');?></td>
                    <td><?= $result['document_type'] ?></td>
                  </tr>
                  <tr>
                    <td><?php echo lang('Employees.xin_department_name');?></td>
                    <td><?= $department['department_name'] ?></td>
                  </tr>
                  <tr>
                    <td><?= lang('Employees.xin_document_doe');?></td>
                    <td><?= $result['expiry_date'];?></td>
                  </tr>
                 
                </tbody>
              </table>
            </div>

          </div>
        </div>

        <?php if(in_array('asset3',staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
        <div class="tab-pane fade" id="pills-edit" role="tabpanel" aria-labelledby="pills-edit-tab">
          <?php $attributes = array('name' => 'update_asset', 'id' => 'update_asset', 'autocomplete' => 'off', 'class'=>'m-b-1');?>
          <?php $hidden = array('_method' => 'EDIT', 'token' => $segment_id);?>
          <?= form_open_multipart('erp/documents/update_official_document', $attributes, $hidden);?>
          <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div id="accordion">
                 
                  <div class="card-body">
                    <div class="row">
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="license_name"><?= lang('Employees.xin_license_name');?> <span class="text-danger">*</span></label>
                        <input class="form-control" placeholder="<?= lang('Employees.xin_license_name');?>" value="<?= $result['license_name'] ?>" name="license_name" type="text">
                      </div>
                    </div> 
                    <div class="col-md-6">
                      <label for="title" class="control-label">
                              <?= lang('Employees.xin_document_category');?>
                              <span class="text-danger">*</span></label>
                        <select class="form-control" name="document_category" data-plugin="select_hrm" data-placeholder="<?= lang('Asset.xin_brand');?>">
                                      <option value="0">System</option>
                                      
                                      <option value="1">Official</option>
                                      
                        </select>
                      </div>
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="title" class="control-label">
                            <?= lang('Employees.xin_document_type');?>
                            <span class="text-danger">*</span></label>
                          <input class="form-control" placeholder="<?= lang('Employees.xin_document_eg_payslip_etc');?>" value="<?= $result['document_type'] ?>"  name="document_type" type="text">
                        </div>
                      </div> 
                      <input type="hidden" name="type" value="edit_record">
                    <div class="col-md-6">
                        <div class="form-group">
                          <label for="expiry_date"><?= lang('Employees.xin_document_doe');?> <span class="text-danger">*</span></label>
                          <div class="input-group">
                          <input class="form-control date" placeholder="<?= lang('Employees.xin_document_doe');?>" value="<?= $result['expiry_date'] ?>" name="expiry_date" type="text">
                          <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                        </div>
                        </div>
                      </div>
                        
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="license_number"><?= lang('Employees.xin_license_number');?> <span class="text-danger">*</span></label>
                            <input class="form-control" placeholder="<?= lang('Employees.xin_license_number');?>" value="<?= $result['license_no'] ?>" name="license_number" type="text">
                          </div>
                        </div>
                      </div>
                  </div>
                  <div class="card-footer text-right">
                    <button type="reset" class="btn btn-light" href="#add_form" data-toggle="collapse" aria-expanded="false"><?= lang('Main.xin_reset');?></button>
                    &nbsp;
                    <button type="submit" class="btn btn-primary"><?= lang('Main.xin_save');?></button>
                  </div>
                </div>
              </div>
            </div>
    
          </div>
          </div>
          <?= form_close(); ?>
        </div>
        <div class="tab-pane fade" id="pills-image" role="tabpanel" aria-labelledby="pills-image-tab">
          <div class="card-body pb-2">
            <div class="box-body">
              <?php $attributes = array('name' => 'edit_image', 'id' => 'edit_image', 'autocomplete' => 'off');?>
              <?php $hidden = array('token' => $segment_id);?>
              <?= form_open('erp/assets/update_asset_image', $attributes, $hidden);?>
              <div class="form-body">
              <div class="row">
                  <div class="col-md-12">
                  	<img src="<?= base_url('public/uploads/documents').'/'.$result['document_file'];?>" class="d-block ui-w-50 rounded-circle" width="50" height="50" />
                  </div>
               </div>   
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="logo">
                        <?= lang('Employees.xin_document_file');?>
                        <span class="text-danger">*</span> </label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="asset_image">
                        <label class="custom-file-label">
                          <?= lang('Main.xin_choose_file');?>
                        </label>
                        <small>
                        <?= lang('Main.xin_company_file_type');?>
                        </small> </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer text-right">
            <button  type="submit" class="btn btn-primary">
            <?= lang('Employees.xin_update_pic');?>
            </button>
          </div>
          <?= form_close(); ?>
      </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
