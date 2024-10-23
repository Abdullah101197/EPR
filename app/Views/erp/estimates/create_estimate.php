<?php
use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\ProjectsModel;
use App\Models\ConstantsModel;
use App\Models\EstimatesProductsModel;
use App\Models\MachineTypeModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$ProjectsModel = new ProjectsModel();
$ConstantsModel = new ConstantsModel();
$EstimatesProductsModel = new EstimatesProductsModel();
$MachineTypeModel = new MachineTypeModel();


$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = erp_company_settings();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();
$clients =  $UsersModel->where('user_type','customer')->findAll();
$estimate_categories =  $ConstantsModel->where('type','estimate_category')->findAll();
$estimate_brands =  $ConstantsModel->where('type','estimate_brand')->findAll();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$employees = $UsersModel->where('user_type', 'staff')->findAll();
if($user_info['user_type'] == 'staff'){
	$machine_types = $MachineTypeModel->where('company_id', $user_info['company_id'])->findAll();
} else {

  $machine_types = $MachineTypeModel->where('company_id', $usession['sup_user_id'])->findAll();
}
if($user_info['user_type'] == 'staff'){
	$projects = $ProjectsModel->where('company_id',$user_info['company_id'])->orderBy('project_id', 'ASC')->findAll();
	$tax_types = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type','tax_type')->findAll();
//   $estimates_products = $EstimatesProductsModel->where('company_id', $user_info['company_id'])->findAll();

} else {
	$projects = $ProjectsModel->where('company_id',$usession['sup_user_id'])->orderBy('project_id', 'ASC')->findAll();
	$tax_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type','tax_type')->findAll();
//   $estimates_products = $EstimatesProductsModel->where('company_id', $usession['sup_user_id'])->findAll();

}
$xin_system = erp_company_settings();
?>
<?php
// Create Invoice Page
?>
<?php $get_animate = '';?>

<div class="row <?php echo $get_animate;?>">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header with-elements"> <span class="card-header-title mr-2"><strong>
        <?= lang('Main.xin_create_new_estimate');?>
        </strong></span> </div>
        <div class="card-body" aria-expanded="true">
    <div class="row m-b-1">
        <div class="col-md-12">
            <?php 
            
            $attributes = array('name' => 'create_invoice', 'id' => 'xin-form', 'autocomplete' => 'off', 'class' => 'form');?>
            <?php $hidden = array('user_id' => 0);?>
            <?php echo form_open('erp/estimates/create_new_estimate', $attributes, $hidden);?>
            <?php $inv_info = generate_random_employeeid();?>

            <div class="bg-white">
                <div class="box-block">
                    <!-- Top Fields (Estimate Number, Client, Estimate Date, Estimate Due Date) -->
                    <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="estimate_number">
                                <?= lang('Dashboard.xin_estimate_title');?> <span class="text-danger">*</span>
                            </label>
                            <input id="estimate-title" class="form-control" name="estimate_title" type="text" >
                        </div>
                    </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estimate_number">
                                    <?= lang('Main.xin_estimate_number');?> <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" name="estimate_number" type="text" value="<?= $inv_info;?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estimate_number">
                                        Reference No <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" name="ref_no" type="text" value="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="client-dropdown">
                                    Client <span class="text-danger">*</span>
                                </label>
                                <select id="client-dropdown" class="form-control" data-plugin="select_hrm" name="client" data-placeholder="Select Client">
                                    <?php foreach($clients as $client) {?>
                                    <option value="<?php echo $client['user_id']?>" data-contact="<?php echo $client['contact_number']?>">
                                        <?php echo $client['first_name']?> <?php echo $client['last_name']?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="contact_number">
                                    Contact Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="contact_number" disabled class="form-control" name="contact_number">
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var clientDropdown = document.getElementById('client-dropdown');
                            var contactNumberInput = document.getElementById('contact_number');

                            function updateContactNumber() {
                                var selectedOption = clientDropdown.options[clientDropdown.selectedIndex];
                                var contactNumber = selectedOption.getAttribute('data-contact');
                                contactNumberInput.value = contactNumber;
                            }

                            updateContactNumber();

                            clientDropdown.addEventListener('change', updateContactNumber);
                        });
                        </script>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estimate_number">
                                    <?= lang('Dashboard.xin_estimate_atten');?> <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" name="estimate_attend_to" data-plugin="select_hrm" data-placeholder="<?= lang('Main.xin_addedby');?>">
                                <?php 
                                    foreach ($employees as $key => $employee) { ?>
                                        <option value="<?= $employee['user_id'] ?>">
                                        <?= $employee['username'] ?>
                                    </option>
                                <?php    }
                                ?>  
                                
                                    
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estimate_date">
                                    <?= lang('Main.xin_estimate_date');?> <span class="text-danger">*</span>
                                </label>
                                <input class="form-control date" name="estimate_date" type="text" value="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estimate_due_date">
                                    <?= lang('Main.xin_estimate_due_date');?> <span class="text-danger">*</span>
                                </label>
                                <input class="form-control date" name="estimate_due_date" type="text" value="">
                            </div>
                        </div>
                        <?php if( $user_info['company_id'] == $usession['sup_user_id'] ){?>
               
               <div class="col-md-3">
                 <div class="form-group">
                   <label for="added_by" class="control-label">
                     <?= lang('Main.xin_addedby');?>
                   </label>
                   <select class="form-control" name="added_by" data-plugin="select_hrm" data-placeholder="<?= lang('Main.xin_addedby');?>">
                   <?php 
                     foreach ($employees as $key => $employee) { ?>
                        <option value="<?= $employee['user_id'] ?>">
                           <?= $employee['username'] ?>
                       </option>
                 <?php    }
                   ?>  
                  
                     
                   </select>
                 </div>
               </div>
               <?php
 
               }else{ ?>
                 <div class="col-md-4">
                 <div class="form-group">
                   <label for="added_by" class="control-label">
                     <?= lang('Main.xin_addedby');?>
                   </label>
                   <select disabled class="form-control"  data-plugin="select_hrm" data-placeholder="<?= lang('Main.xin_addedby');?>">
                   <?php 
                     foreach ($employees as $key => $employee) { ?>
                        <option value="<?= $employee['user_id'] ?>" <?php if($employee['user_id']==$usession['sup_user_id']):?> selected="selected"<?php endif;?>>
                           <?= $employee['username'] ?>
                       </option>
                 <?php    }
                   ?>  
                  
                     
                   </select>
                   <input type="hidden" name="added_by" value="<?= $usession['sup_user_id'] ?>">
              <input type="hidden" name="user_id" value="<?= $usession['sup_user_id'] ?>">
                 
                 </div>
               </div>
             <?php  } ?> 
 
             <div class="col-12">
                <div class="form-group">
                <label for="added_by" class="control-label">
                    Terms and Conditions <span class="text-danger">*</span>
                </label>
                <textarea class="form-control editor" placeholder="Terms and Conditions" name="terms_conditions" cols="30" rows="2"></textarea>
                </div>
            </div>
             <div class="col-12">
                <div class="form-group">
                <label for="added_by" class="control-label">
                    General Terms and Conditions <span class="text-danger">*</span>
                </label>
                <textarea class="form-control editor" placeholder="General Terms and Conditions" name="general_terms_conditions" cols="30" rows="2"></textarea>
                </div>
            </div>
                    </div>         
                    
                    <!-- Dynamic Modules for Items -->
                    <div id="module-list">
                        <!-- Modules will be added here dynamically -->
                    </div>
                    <div class="form-group overflow-hidden">
                        <div class="col-xs-12">
                            <button type="button" class="btn btn-sm btn-primary" id="add-module">
                                <?= lang('Invoices.xin_title_add_module');?>
                            </button>
                        </div>
                    </div>
                    <hr>
                  
                    <div id="split-unit" class="machine-type-section" style="display: none;">
                        <h3>SPLIT UNIT</h3>
                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="split-piping-rate">
                                    Piping Charge (Nos)<span class="text-danger">*</span>
                                </label>
                                <input id="split-piping-rate" value="0.00" class="form-control" name="split_piping_charge" type="number" step="any">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="split-margin-split">
                                    Margin Split<span class="text-danger">*</span>
                                </label>
                                <input id="split-margin-split" value="0.00" class="form-control" name="split_margin_charge" type="number" step="any">
                            </div>
                        </div>
                        </div>
                        
                        <hr>
                    </div>
                    

                    
                    <div id="ductable-unit" class="machine-type-section" style="display: none;">
                        <h3>DUCTABLE UNIT</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duct-piping-rate">
                                        Piping Charge(Nos)<span class="text-danger">*</span>
                                    </label>
                                    <input id="duct-piping-rate" value="0.00" class="form-control" name="duct_piping_charge" type="number" step="any">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duct-margin-split">
                                        Margin Duct<span class="text-danger">*</span>
                                    </label>
                                    <input id="duct-margin-split" value="0.00" class="form-control" name="duct_margin_charge" type="number" step="any">
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    

                    
                    <div id="package-units" class="machine-type-section" style="display: none;">
                        <h3>PACKAGE UNIT</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="package-piping-rate">
                                        Ducting Charge(TR)<span class="text-danger">*</span>
                                    </label>
                                    <input id="package-piping-rate" value="0.00" class="form-control" name="package_piping_charge" type="number" step="any">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="package-margin-split">
                                        Margin Duct<span class="text-danger">*</span>
                                    </label>
                                    <input id="package-margin-split" value="0.00" class="form-control" name="package_margin_charge" type="number" step="any">
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                  

                    
                    <div id="vrf-concealed-duct" class="machine-type-section" style="display: none;">
                        <h3>VRF CONCEALED DUCT</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vrf-piping-rate">
                                        Ducting Charge(TR)<span class="text-danger">*</span>
                                    </label>
                                    <input id="vrf-piping-rate" value="0.00" class="form-control" name="vrf_piping_charge" type="number" step="any">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vrf-margin-split">
                                        Margin Duct<span class="text-danger">*</span>
                                    </label>
                                    <input id="vrf-margin-split" value="0.00" class="form-control" name="vrf_margin_charge" type="number" step="any">
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                   

                    
                    <div id="cassette-unit" class="machine-type-section" style="display: none;">
                        <h3>CASSETTE UNIT</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cassette-piping-rate">
                                        Piping Charge (Nos)<span class="text-danger">*</span>
                                    </label>
                                    <input id="cassette-piping-rate" value="0.00" class="form-control" name="cassette_piping_charge" type="number" step="any">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cassette-margin-split">
                                        Margin Split<span class="text-danger">*</span>
                                    </label>
                                    <input id="cassette-margin-split" value="0.00" class="form-control" name="cassette_margin_charge" type="number" step="any">
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    

                    
                    <div id="floor-stand" class="machine-type-section" style="display: none;">
                        <h3>FLOOR STAND</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor-piping-rate">
                                        Piping Charge (Nos)<span class="text-danger">*</span>
                                    </label>
                                    <input id="floor-piping-rate" value="0.00" class="form-control" name="floor_piping_charge" type="number" step="any">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor-margin-split">
                                        Margin Split<span class="text-danger">*</span>
                                    </label>
                                    <input id="floor-margin-split" value="0.00" class="form-control" name="floor_margin_charge" type="number" step="any">
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estimate_number">
                                    VAV Box <span class="text-danger">*</span>
                                </label>
                                <input id="vav-box" value="0.00" class="form-control" name="vav_box" type="number" step="any">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estimate_number">
                                    Extra Pipe<span class="text-danger">*</span>
                                </label>
                                <input id="extra-pipe" value="0.00" class="form-control" name="extra_pipe" type="number" step="any">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="discountInput"><strong>Discount:</strong></label>
                                <input name="discount" type="number" value="0.00" class="form-control" id="discountInput" placeholder="0.00" step="any">
                            </div>
                        </div>
                    </div>
                    <div id="overall-totals"></div>
                    <div id="table-data"></div>
                    <!-- Other invoice-related fields -->
                </div>
            </div>
        </div>
    </div>
</div>
 
    
      <div class="card-footer text-right">
        <button type="submit" name="invoice_submit" class="btn btn-primary pull-right my-1" style="margin-right: 5px;">
        <?= lang('Main.xin_create_new_estimate');?>
        </button>
      </div>
      <?php echo form_close(); ?> </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to add a new module
        document.getElementById('add-module').addEventListener('click', function() {
            addModule();
        });

        function addModule() {
            var moduleIndex = document.querySelectorAll('.module').length;

            var moduleTemplate = `
            <div class="module">
                <input type="hidden" name="module_index[]" value="${moduleIndex}">
                <div class="module-header">
                    <div class="form-group">
                        <label for="module_name">Module Name</label>
                        <input type="text" class="form-control module-name-input" name="module_name[]" placeholder="Enter module name">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-module">
                        <span class="fa fa-trash"></span> Remove Module
                    </button>
                </div>
                <br><br>
                <div class="module-body">
                    <div class="ci-item-values">
                        <div class="item-list">
                            <!-- Items will be added here dynamically -->
                        </div>
                        <div class="form-group overflow-hidden1">
                            <div class="col-xs-12">
                                <button type="button" class="btn btn-sm btn-primary add-item">
                                    <?= lang('Invoices.xin_title_add_item');?>
                                </button>
                            </div>
                        </div>
                         <div class="custom-item-list">
                            <!-- Items will be added here dynamically -->
                        </div>
                        <div class="form-group overflow-hidden1">
                            <div class="col-xs-12">
                                <button type="button" class="btn btn-sm btn-primary add-custom-item">
                                    Add Custom Item
                                </button>
                            </div>
                        </div>
                        <div class="module-totals">
                            <div class="totals-list">
                                <!-- Totals will be shown here -->
                            </div>
                        </div>
                        <div class="module-totals">
                            <div class="custom-totals-list">
                                <!-- Totals will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>`;

            var moduleList = document.getElementById('module-list');
            var div = document.createElement('div');
            div.innerHTML = moduleTemplate;

            var moduleElement = div.firstElementChild;
            moduleList.appendChild(moduleElement);

            moduleElement.querySelector('.add-item').addEventListener('click', function() {
                addItem(moduleElement.querySelector('.item-list'), moduleElement, moduleIndex);
            });
            moduleElement.querySelector('.add-custom-item').addEventListener('click', function() {
                addCustomItem(moduleElement.querySelector('.custom-item-list'), moduleElement, moduleIndex);
            });
            moduleElement.querySelector('.remove-module').addEventListener('click', function() {
                moduleList.removeChild(moduleElement);
                updateOverallTotals();
            });
        }

        function addCustomItem(itemList, moduleElement, moduleIndex) {
            var labels = itemList.children.length === 0;

            var itemTemplate = `
                <div class="row custom-item-row mt-2 mb-2">
                    <input type="hidden" name="custom_item_module_index[]" value="${moduleIndex}">
                    <div class="form-group mb-1 col-sm-12 col-md-2">
                        ${labels ? '<label for="name">Name</label>' : ''}
                        <input type="text" class="form-control" name="custom_name[]" placeholder="Name">
                    </div>
                    <div class="form-group mb-1 col-sm-12 col-md-3">
                        ${labels ? '<label for="description">Description</label>' : ''}
                        <input type="text" class="form-control" name="custom_description[]" placeholder="Description">
                    </div>
                    <div class="form-group mb-1 col-sm-12 col-md-2">
                        ${labels ? '<label for="qty">Qty</label>' : ''}
                        <input type="number" min="1" class="form-control" name="custom_qty[]" value="1">
                    </div>
                    <div class="form-group mb-1 col-sm-12 col-md-2">
                        ${labels ? '<label for="price">Price</label>' : ''}
                        <input type="number" class="form-control" min="1" name="custom_price[]" value="0" placeholder="Price">
                    </div>
                    <div class="form-group mb-1 col-sm-12 col-md-2">
                        ${labels ? '<label for="total_price">Total Price</label>' : ''}
                        <input type="number" readonly class="form-control" name="custom_total_price[]" value="0" placeholder="Total Price">
                    </div>
                    <div class="form-group col-sm-12 col-md-1 text-xs-center mt-2">
                        <button type="button" class="btn icon-btn btn-sm btn-outline-danger remove-item">
                            <span class="fa fa-trash"></span>
                        </button>
                    </div>
                </div>`;

            var div = document.createElement('div');
            div.innerHTML = itemTemplate;

            var itemElement = div.firstElementChild;
            itemList.appendChild(itemElement);

            // Remove item event
            itemElement.querySelector('.remove-item').addEventListener('click', function() {
                itemList.removeChild(itemElement);
                updateModuleCustomTotals(moduleElement);
            });

            // Update total price when quantity or price is changed
            itemElement.querySelectorAll('input[name="custom_qty[]"], input[name="custom_price[]"]').forEach(input => {
                input.addEventListener('input', function() {
                    updateItemTotalPrice(itemElement);
                    updateModuleCustomTotals(moduleElement);
                });
            });

            // Initialize the total price for the new item
            updateItemTotalPrice(itemElement);
            updateModuleCustomTotals(moduleElement);
        }
        function addItem(itemList, moduleElement, moduleIndex) {
            var labels = itemList.children.length === 0;

            var itemTemplate = `
            <div class="row item-row">
                <input type="hidden" name="item_module_index[]" value="${moduleIndex}">
                <div class="form-group mb-1 col-sm-12 col-md-1">
                    ${labels ? '<label for="qty_hrs"><?= lang('Invoices.xin_title_location');?></label>' : ''}
                    <input type="text" class="form-control qty_hrs" name="location[]" placeholder="Location">
                </div>
                <div class="form-group mb-1 col-sm-12 col-md-1">
                    ${labels ? '<label for="qty_hrs"><?= lang('Invoices.xin_title_area');?></label>' : ''}
                    <input type="text" class="form-control qty_hrs" name="area[]" placeholder="Area">
                </div>
                <div class="form-group mb-1 col-sm-12 col-md-2">
                    ${labels ? '<label for="model_code"><?= lang('Dashboard.xin_brand');?> <span class="text-danger">*</span></label>' : ''}
                    <select class="form-control model-brand-select" data-plugin="select_hrm" data-placeholder="Select Brand" name="brand[]">
                        <option value="">Choose</option>
                        <?php foreach($estimate_brands as $estimates_product) {?>
                        <option value="<?php echo $estimates_product['constants_id']?>"><?php echo $estimates_product['category_name']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group mb-1 col-sm-12 col-md-2">
                    ${labels ? '<label for="model_code"><?= lang('Invoices.xin_title_model');?> <span class="text-danger">*</span></label>' : ''}
                    <select class="form-control model-code-select" data-plugin="select_hrm" data-placeholder="Select Model" name="model_code[]">
                        <option value="">Choose</option>
                    </select>
                </div>
                <div class="skin skin-flat form-group mb-1 col-sm-12 col-md-1">
                    ${labels ? '<label for="unit_price">Capacity</label>' : ''}
                    <input class="form-control unit_price" type="text" name="capacity[]" value="0" readonly/>
                </div>
                <div class="form-group mb-1 col-sm-12 col-md-1">
                    ${labels ? '<label for="qty_hrs"><?= lang('Invoices.xin_title_description');?></label>' : ''}
                    <input type="text" class="form-control qty_hrs" name="description[]" value="" readonly>
                    <input type="hidden" class="form-control qty_hrs" name="price[]" value="" readonly>
                </div>
                <div class="form-group mb-1 col-sm-12 col-md-1">
                    ${labels ? '<label for="qty_hrs"><?= lang('Invoices.xin_title_qty_hrs');?></label>' : ''}
                    <input type="text" class="form-control qty_hrs" name="qty[]" value="1">
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        ${labels ? '<label for="first_name"><?= lang('Inventory.xin_machine_type');?> <span class="text-danger">*</span></label>' : ''}
                        <input type="text" class="form-control machine-type-select" name="machine_type_name[]" value="" readonly>
                        <input type="hidden" class="form-control machine-type-id" name="machine_type_id[]" value="">
                    </div>
                </div>
                <div class="form-group col-sm-12 col-md-1 text-xs-center mt-2">
                    <button type="button" class="btn icon-btn btn-sm btn-outline-danger remove-item">
                        <span class="fa fa-trash"></span>
                    </button>
                </div>
            </div>`;

            var div = document.createElement('div');
            div.innerHTML = itemTemplate;

            var itemElement = div.firstElementChild;
            itemList.appendChild(itemElement);

            // Initialize select2 for the newly added elements
            $(itemElement).find('.model-brand-select').select2({
                placeholder: $(itemElement).find('.model-brand-select').data('placeholder')
            });

            $(itemElement).find('.model-code-select').select2({
                placeholder: $(itemElement).find('.model-code-select').data('placeholder')
            });

            // Attach event listeners
            $(itemElement).find('.model-brand-select').on('select2:select', function(e) {
                var brandId = e.params.data.id;
                fetchModelCodesByBrand(brandId, itemElement);
            });

            $(itemElement).find('.model-code-select').on('select2:select', function(e) {
                var productId = e.params.data.id;
                fetchProductDetails(productId, itemElement);
            });

            itemElement.querySelector('.remove-item').addEventListener('click', function() {
                itemList.removeChild(itemElement);
                updateModuleTotals(moduleElement);
            });

            itemElement.querySelector('input[name="capacity[]"]').addEventListener('input', function() {
                updateModuleTotals(moduleElement);
            });

            itemElement.querySelector('input[name="qty[]"]').addEventListener('input', function() {
                updateModuleTotals(moduleElement);
            });

            itemElement.querySelector('.machine-type-select').addEventListener('change', function() {
                updateModuleTotals(moduleElement);
            });

            updateModuleTotals(moduleElement);
        }

        function fetchProductDetails(productId, itemElement) {
            fetch('estimate-product-details/' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        var capacityInput = itemElement.querySelector('input[name="capacity[]"]');
                        var descriptionInput = itemElement.querySelector('input[name="description[]"]');
                        var priceInput = itemElement.querySelector('input[name="price[]"]');
                        var machineTypeIdInput = itemElement.querySelector('input[name="machine_type_id[]"]');
                        var machineTypeNameInput = itemElement.querySelector('input[name="machine_type_name[]"]');
                        
                        if (capacityInput) {
                            capacityInput.value = data.capacity || '';
                        }
                        if (descriptionInput) {
                            descriptionInput.value = data.product_description || '';
                        }
                        if (priceInput) {
                            priceInput.value = data.retail_price || '';
                        }
                        if (machineTypeIdInput) {
                            machineTypeIdInput.value = data.machine_type_id || '';
                        }
                        if (machineTypeNameInput) {
                            machineTypeNameInput.value = data.machine_type_name || '';
                        } else {
                            console.error('Machine type name input element not found');
                        }

                        updateModuleTotals(itemElement.closest('.module'));
                    } else {
                        console.error('No data received');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function fetchModelCodesByBrand(brandId, itemElement) {
            fetch('fetch-model-codes-by-brand/' + brandId)
                .then(response => response.json())
                .then(data => {
                    var modelCodeSelect = itemElement.querySelector('select[name="model_code[]"]');
                    modelCodeSelect.innerHTML = '<option value="">Choose</option>';
                    data.forEach(function(model) {
                        var option = document.createElement('option');
                        option.value = model.estimates_products_id;
                        option.textContent = model.model_code;
                        option.dataset.sellingPrice = model.retail_price;
                        modelCodeSelect.appendChild(option);
                    });
                })

                .catch(error => console.error('Error:', error));
        }
        

        function updateModuleTotals(moduleElement) {
            var totals = {};
            var capacityByMachineType = {};
            var qtyByMachineType = {};

            var items = moduleElement.querySelectorAll('.item-row');

            items.forEach(item => {
                var machineTypeName = item.querySelector('.machine-type-select').value;
                var capacity = parseFloat(item.querySelector('input[name="capacity[]"]').value) || 0;
                var qty = parseFloat(item.querySelector('input[name="qty[]"]').value) || 0;

                // Check if machineTypeName is not empty
                if (machineTypeName) {
                    if (!capacityByMachineType[machineTypeName]) {
                        capacityByMachineType[machineTypeName] = 0;
                        qtyByMachineType[machineTypeName] = 0;
                    }

                    capacityByMachineType[machineTypeName] += capacity;
                    qtyByMachineType[machineTypeName] += qty;
                }
            });

            var totalsList = moduleElement.querySelector('.totals-list');
            totalsList.innerHTML = '';

            for (var machineType in capacityByMachineType) {
                if (capacityByMachineType[machineType] > 0 || qtyByMachineType[machineType] > 0) {
                    var totalCapacity = capacityByMachineType[machineType];
                    var totalQty = qtyByMachineType[machineType];
                    totalsList.innerHTML += `
                    <div>
                        <strong>Machine Type: ${machineType}</strong><br>
                        Total Capacity: ${totalCapacity}<br>
                        Total Quantity: ${totalQty}<br><br>
                    </div>`;
                }
            }

            updateOverallTotals();
        }

        function updateItemTotalPrice(itemElement) {
            var qty = parseFloat(itemElement.querySelector('input[name="custom_qty[]"]').value) || 0;
            var price = parseFloat(itemElement.querySelector('input[name="custom_price[]"]').value) || 0;
            var totalPrice = qty * price;
            itemElement.querySelector('input[name="custom_total_price[]"]').value = totalPrice.toFixed(2);  // Round to two decimals
        }

        function updateModuleCustomTotals(moduleElement) {
            var totalQty = 0;
            var totalPrice = 0;

            var items = moduleElement.querySelectorAll('.custom-item-row');

            items.forEach(item => {
                var qty = parseFloat(item.querySelector('input[name="custom_qty[]"]').value) || 0;
                var price = parseFloat(item.querySelector('input[name="custom_price[]"]').value) || 0;
                var itemTotalPrice = qty * price;

                totalQty += qty;
                totalPrice += itemTotalPrice;
            });

            // Update the display of total quantities and prices
            var totalsList = moduleElement.querySelector('.custom-totals-list');
            totalsList.innerHTML = `
                <div>
                    <strong>Total Quantity: </strong> ${totalQty}<br>
                    <strong>Total Price: </strong> ${totalPrice.toFixed(2)}<br>
                </div>`;

            updateOverallTotals();
        }

        function generateHTMLTable(overallTotals, estimateTitle) {
            // Add the estimate title to the top of the table
            var tableHTML = estimateTitle ? `<h3>${estimateTitle}</h3>` : '';
            var totalValueCostAllMachines = 0;
            var totalProjectCost = 0;
            var totalMarginSplit = 0;

            for (var machineType in overallTotals) {
                var totalAmount = 0; // Initialize total amount for this machine type
                var totalCapacity = 0; // Initialize total capacity for this machine type
                var totalQty = 0; // Initialize total quantity for Piping Charge calculation

                // Aggregate model codes
                var modelsMap = {};

                overallTotals[machineType].models.forEach(function(model) {
                    var amount = model.sellingPrice * model.qty;
                    var modelTotalCapacity = model.capacity * model.qty; // Correctly calculate model total capacity

                    if (!modelsMap[model.modelCode]) {
                        modelsMap[model.modelCode] = {
                            modelCode: model.modelCode,
                            qty: 0,
                            sellingPrice: model.sellingPrice,
                            capacity: model.capacity
                        };
                    }

                    modelsMap[model.modelCode].qty += model.qty;
                    totalAmount += amount;

                    // Only add capacity for normal items (skip custom items if they have no capacity)
                    if (model.capacity) {
                        totalCapacity += modelTotalCapacity;
                    }

                    // Update quantity based on machine type and capacity
                    if (machineType === 'DUCTABLE UNIT' || machineType === 'PACKAGE UNITS' || machineType === 'VRF CONCEALED DUCT') {
                        totalQty += model.capacity ? modelTotalCapacity : 0; // Add total capacity only if applicable
                    } else {
                        totalQty += model.qty;
                    }
                });

                // Convert modelsMap to an array for rendering the table
                var aggregatedModels = Object.values(modelsMap);

                tableHTML += 
                `<h4>Machine Type: ${machineType}</h4>
                <table border="1" cellpadding="5" cellspacing="0" style="width: 100%;"> <!-- Added inline style -->
                    <thead>
                        <tr>
                            <th>Model Code</th>
                            <th>QTY</th>
                            <th>Rate (Selling Price)</th>
                            <th>Sub Amount</th>
                            <th>Capacity (TR)</th>
                            <th>Total Capacity (TR)</th>
                        </tr>
                    </thead>
                    <tbody>`;

                aggregatedModels.forEach(function(model) {
                    var amount = model.sellingPrice * model.qty;
                    var modelTotalCapacity = model.capacity * model.qty; // Correctly calculate model total capacity

                    tableHTML += 
                    `<tr>
                        <td>${model.modelCode}</td>
                        <td>${model.qty}</td>
                        <td>${model.sellingPrice.toFixed(2)}</td>
                        <td>${amount.toFixed(2)}</td>
                        <td>${model.capacity ? model.capacity.toFixed(2) : '-'}</td> <!-- Show '-' for custom items without capacity -->
                        <td>${model.capacity ? modelTotalCapacity.toFixed(2) : '-'}</td> <!-- Show '-' for custom items without capacity -->
                    </tr>`;
                });

                // Add the cost split row
                if (machineType == 'SPLIT UNIT') {
                    var slpitLabel = 'Cost Split'
                    var pipingLabel = 'Piping Charge(Nos)'
                    var totalCastLabel = 'Total Cost'
                    var marginLable = 'Margin Split'
                    var valueLable = 'Value Split'
                    var rateUnitLable = 'Rate each Unit'
                } else if(machineType == 'DUCTABLE UNIT'){
                    var slpitLabel = 'Machines Cost'
                    var pipingLabel = 'Ducting Charge(TR)'
                    var totalCastLabel = 'Total Cost DX'
                    var marginLable = 'Margin Duct'
                    var valueLable = 'Value Duct'
                    var rateUnitLable = 'DX TR Value'
                }
                 else if(machineType == 'PACKAGE UNITS'){
                    var slpitLabel = 'Machines Cost'
                    var pipingLabel = 'Ducting Charge(TR)'
                    var totalCastLabel = 'Total Cost DX'
                    var marginLable = 'Margin Duct'
                    var valueLable = 'Value Duct'
                    var rateUnitLable = 'DX TR Value'
                } 
                 else if(machineType == 'VRF CONCEALED DUCT'){
                    var slpitLabel = 'Machines Cost'
                    var pipingLabel = 'Ducting Charge(TR)'
                    var totalCastLabel = 'Total Cost DX'
                    var marginLable = 'Margin Duct'
                    var valueLable = 'Value Duct'
                    var rateUnitLable = 'DX TR Value'
                } 
                 else if(machineType == 'CASSETTE UNIT'){
                    var slpitLabel = 'Cost Split'
                    var pipingLabel = 'Piping Charge(Nos)'
                    var totalCastLabel = 'Total Cost DX'
                    var marginLable = 'Margin Split'
                    var valueLable = 'Value Split'
                    var rateUnitLable = 'Rate each Unit'
                } 
                 else if(machineType == 'FLOOR STAND'){
                    var slpitLabel = 'Cost Split'
                    var pipingLabel = 'Piping Charge(Nos)'
                    var totalCastLabel = 'Total Cost'
                    var marginLable = 'Margin Split'
                    var valueLable = 'Value Split'
                    var rateUnitLable = 'Rate each Unit'
                } 
                else {
                    var slpitLabel = '';
                    var pipingLabel = ''
                    var totalCastLabel = ''
                    var marginLable = ''
                    var valueLable = ''
                    var rateUnitLable = ''
                }
                tableHTML += 
                    `<tr>
                        <td colspan="3"><strong>${slpitLabel}</strong></td>
                        <td><strong>${totalAmount.toFixed(2)}</strong></td>
                        <td colspan="2"><strong class="d-flex justify-content-end">${totalCapacity.toFixed(2)}</strong></td>
                    </tr>`;

                // Add the Piping Charge row
                if (machineType == 'SPLIT UNIT') {
                    var pipingChargeRate = document.getElementById('split-piping-rate').value.trim() || 0;
                } else if(machineType == 'DUCTABLE UNIT'){
                    var pipingChargeRate = document.getElementById('duct-piping-rate').value.trim() || 0;
                } 
                else if(machineType == 'PACKAGE UNITS'){
                    var pipingChargeRate = document.getElementById('package-piping-rate').value.trim() || 0;
                } 
                else if(machineType == 'VRF CONCEALED DUCT'){
                    var pipingChargeRate = document.getElementById('vrf-piping-rate').value.trim() || 0;
                } 
                else if(machineType == 'CASSETTE UNIT'){
                    var pipingChargeRate = document.getElementById('cassette-piping-rate').value.trim() || 0;
                } 
                else if(machineType == 'FLOOR STAND'){
                    var pipingChargeRate = document.getElementById('floor-piping-rate').value.trim() || 0;
                } 
                
                else {
                    var pipingChargeRate = 0;
                }
                var pipingChargeAmount = totalQty * pipingChargeRate;

                if (machineType == 'DUCTABLE UNIT' || machineType == 'PACKAGE UNITS' || machineType == 'VRF CONCEALED DUCT') {
                    var totalQtyLabel = totalQty.toFixed(2);
                } else {
                    var totalQtyLabel = totalQty;
                }
                tableHTML += 
                    `<tr>
                        <td><strong>${pipingLabel}</strong></td>
                        <td>${totalQtyLabel}</td>
                        <td>${pipingChargeRate}</td>
                        <td>${pipingChargeAmount.toFixed(2)}</td>
                        <td colspan="2"></td> <!-- Empty cells to align with original table structure -->
                    </tr>`;

                // Add the Total Total Cost row
                var totalCost = totalAmount + pipingChargeAmount;
                totalProjectCost += totalCost;
                tableHTML += 
                    `<tr>
                        <td><strong>${totalCastLabel}</strong></td>
                        <td colspan="3"><strong class="d-flex justify-content-end">${totalCost.toFixed(2)}</strong></td>
                        <td colspan="2"></td> <!-- Empty cells to align with original table structure -->
                    </tr>`;

                // Add the Margin Split row
                if (machineType == 'SPLIT UNIT') {
                    var marginSplit = document.getElementById('split-margin-split').value.trim() || 0;
                } else if(machineType == 'DUCTABLE UNIT'){
                    var marginSplit = document.getElementById('duct-margin-split').value.trim() || 0;
                } 
                else if(machineType == 'PACKAGE UNITS'){
                    var marginSplit = document.getElementById('package-margin-split').value.trim() || 0;
                }
                else if(machineType == 'VRF CONCEALED DUCT'){
                    var marginSplit = document.getElementById('vrf-margin-split').value.trim() || 0;
                }
                else if(machineType == 'CASSETTE UNIT'){
                    var marginSplit = document.getElementById('cassette-margin-split').value.trim() || 0;
                }
                else if(machineType == 'FLOOR STAND'){
                    var marginSplit = document.getElementById('floor-margin-split').value.trim() || 0;
                }
                 else {
                    var marginSplit = 0;
                }

                var marginCost = totalCost * (0.01 * marginSplit);
                totalMarginSplit += marginCost;
                tableHTML += 
                    `<tr>
                        <td><strong>${marginLable}</strong></td>
                        <td>${marginSplit}</td>
                        <td colspan="2"><strong class="d-flex justify-content-center">${marginCost.toFixed(2)}</strong></td>
                        <td colspan="2"></td> <!-- Empty cells to align with original table structure -->
                    </tr>`;

                // Add the Value Split row
                var valueCost = totalCost + marginCost;
                totalValueCostAllMachines += valueCost;
                tableHTML += 
                    `<tr>
                        <td><strong>${valueLable}</strong></td>
                        <td colspan="3"><strong class="d-flex justify-content-center">${valueCost.toFixed(2)}</strong></td>
                        <td colspan="2"></td> <!-- Empty cells to align with original table structure -->
                    </tr>`;

                // Add Rate each unit row
                if (machineType == 'SPLIT UNIT') {
                    var eachCost = (marginCost +  pipingChargeAmount) / totalQty;
                } else if(machineType == 'DUCTABLE UNIT'){
                    var eachCost = valueCost / totalQty;
                }
                 else if(machineType == 'PACKAGE UNITS'){
                    var eachCost = valueCost / totalQty;
                }
                 else if(machineType == 'VRF CONCEALED DUCT'){
                    var eachCost = valueCost / totalQty;
                }
                 else if(machineType == 'CASSETTE UNIT'){
                    var eachCost = (marginCost +  pipingChargeAmount) / totalQty;
                }
                 else if(machineType == 'FLOOR STAND'){
                    var eachCost = (marginCost +  pipingChargeAmount) / totalQty;
                }
                 else {
                    var eachCost = 0;
                }
                

                tableHTML += 
                    `<tr>
                        <td><strong>${rateUnitLable}</strong></td>
                        <td colspan="3"><strong class="d-flex justify-content-center">${eachCost.toFixed(2)}</strong></td>
                        <td colspan="2"></td> <!-- Empty cells to align with original table structure -->
                    </tr>`;

                tableHTML += 
                    `</tbody>
                </table><br>`;
            }
            var extraPipe = parseFloat(document.getElementById('extra-pipe').value.trim()) || 0;
            var vavBox = parseFloat(document.getElementById('vav-box').value.trim()) || 0;
            var vavBoxMultiple = vavBox;
            var discount = parseFloat(document.getElementById('discountInput').value.trim()) || 0;
            var salesCommissionAmount = (totalValueCostAllMachines + extraPipe) * 0.01;
            var totalValue = totalValueCostAllMachines + salesCommissionAmount + extraPipe + vavBox;
            var projectCost = totalProjectCost + vavBoxMultiple;
            var benefitTotal = totalMarginSplit - discount;

            // Build the table HTML
            var tableHTML = `
                <div style="width: 100%; margin-top: 20px;">
                    <h4>Overall Calculations</h4>
                    <table border="1" cellpadding="6" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td><strong class="d-flex justify-content-center">Sales Commission</strong></td>
                            <td><strong class="d-flex justify-content-center">${salesCommissionAmount.toFixed(2)}</strong></td>
                        </tr>
                        <tr>
                            <td><strong class="d-flex justify-content-center">VAV Box</strong></td>
                            <td><strong class="d-flex justify-content-center">${vavBox}</strong></td>
                        </tr>
                        <tr>
                            <td><strong class="d-flex justify-content-center">Extra Pipe</strong></td>
                            <td><strong class="d-flex justify-content-center">${extraPipe}</strong></td>
                        </tr>
                        <tr>
                            <td><strong class="d-flex justify-content-center">TOTAL VALUE</strong></td>
                            <td><strong class="d-flex justify-content-center">${(+totalValue).toFixed(2)}</strong></td>
                        </tr>
                        <tr>
                            <td><strong class="d-flex justify-content-center">Cost Project</strong></td>
                            <td><strong class="d-flex justify-content-center"><input type="hidden" value="${projectCost.toFixed(2)}" name="project_cost">${projectCost.toFixed(2)}</strong></td>
                        </tr>
                        <tr>
                            <td><strong class="d-flex justify-content-center">Discount</strong></td>
                            <td><strong class="d-flex justify-content-center">${discount.toFixed(2)}</strong></td>
                        </tr>
                        <tr>
                            <td><strong class="d-flex justify-content-center">Benefit</strong></td>
                            <td><strong class="d-flex justify-content-center"><input type="hidden" value="${benefitTotal.toFixed(2)}" name="profit"> ${benefitTotal.toFixed(2)}</strong></td>
                        </tr>
                    </table>
                </div>`;

            var totalsContainer = document.getElementById('table-data');
            totalsContainer.innerHTML = tableHTML;
        }







        function updateOverallTotals() {
            var modules = document.querySelectorAll('.module');
            var overallTotals = {};

            // First, hide all machine-type sections
            var allMachineTypeSections = document.querySelectorAll('.machine-type-section');
            allMachineTypeSections.forEach(section => {
                section.style.display = 'none'; // Hide all sections
            });

            modules.forEach(module => {
                var items = module.querySelectorAll('.item-row');

                items.forEach(item => {
                    var modelCodeSelect = item.querySelector('.model-code-select');
                    var machineTypeName = item.querySelector('.machine-type-select').value;
                    var capacity = parseFloat(item.querySelector('input[name="capacity[]"]').value) || 0;
                    var qty = parseFloat(item.querySelector('input[name="qty[]"]').value) || 0;
                    var modelCode = modelCodeSelect.options[modelCodeSelect.selectedIndex].textContent;
                    var sellingPrice = parseFloat(modelCodeSelect.options[modelCodeSelect.selectedIndex].dataset.sellingPrice) || 0;

                    // Check if machineTypeName is not empty
                    if (machineTypeName) {
                        if (!overallTotals[machineTypeName]) {
                            overallTotals[machineTypeName] = { capacity: 0, qty: 0, models: [] };
                        }

                        overallTotals[machineTypeName].capacity += capacity;
                        overallTotals[machineTypeName].qty += qty;
                        overallTotals[machineTypeName].models.push({
                            modelCode: modelCode,
                            qty: qty,
                            sellingPrice: sellingPrice,
                            capacity: capacity
                        });

                        // Show the corresponding machine-type section
                        var machineTypeId = machineTypeName.toLowerCase().replace(/ /g, '-');
                        var machineTypeSection = document.getElementById(machineTypeId);
                        if (machineTypeSection) {
                            machineTypeSection.style.display = 'block'; // Show the relevant section
                        }
                    }
                });

                // Inside updateOverallTotals function, after processing other models
                var customItems = document.querySelectorAll('.custom-item-row');
                customItems.forEach(item => {
                    var machineType = 'VRF CONCEALED DUCT'; // As you mentioned the items should be added to VRF CONCEALED DUCT
                    var customName = item.querySelector('input[name="custom_name[]"]').value.trim();
                    var customDescription = item.querySelector('input[name="custom_description[]"]').value.trim();
                    var customQty = parseFloat(item.querySelector('input[name="custom_qty[]"]').value) || 0;
                    var customPrice = parseFloat(item.querySelector('input[name="custom_price[]"]').value) || 0;
                    var customTotalPrice = customQty * customPrice;

                    if (!overallTotals[machineType]) {
                        overallTotals[machineType] = { capacity: 0, qty: 0, models: [] };
                    }

                    // Add the custom item to the machine type's models array
                    overallTotals[machineType].models.push({
                        modelCode: customName,
                        qty: customQty,
                        sellingPrice: customPrice,
                        capacity: 0 // Custom items have no capacity
                    });
                });

            });

            var estimateTitle = document.getElementById('estimate-title').value.trim();
            var totalsList = document.getElementById('overall-totals');
            totalsList.innerHTML = '';

            totalsList.innerHTML += '<hr><h3>Overall Totals</h3>';

            // Clear existing hidden inputs
            document.querySelectorAll('.overall-totals-hidden').forEach(input => input.remove());

            // Display overall totals and create hidden inputs
            for (var machineType in overallTotals) {
                if (overallTotals[machineType].capacity > 0 || overallTotals[machineType].qty > 0) {
                    var totalCapacity = overallTotals[machineType].capacity;
                    var totalQty = overallTotals[machineType].qty;

                    totalsList.innerHTML += `
                        <div>
                            <strong>Machine Type: ${machineType}</strong><br>
                            Total Capacity: ${totalCapacity}<br>
                            Total Quantity: ${totalQty}<br><br>
                        </div>`;

                    // Create hidden inputs for storing overall totals
                    var hiddenCapacityInput = document.createElement('input');
                    hiddenCapacityInput.type = 'hidden';
                    hiddenCapacityInput.name = `overall_totals[${machineType}][capacity]`;
                    hiddenCapacityInput.value = totalCapacity;
                    hiddenCapacityInput.classList.add('overall-totals-hidden');

                    var hiddenQtyInput = document.createElement('input');
                    hiddenQtyInput.type = 'hidden';
                    hiddenQtyInput.name = `overall_totals[${machineType}][qty]`;
                    hiddenQtyInput.value = totalQty;
                    hiddenQtyInput.classList.add('overall-totals-hidden');

                    totalsList.appendChild(hiddenCapacityInput);
                    totalsList.appendChild(hiddenQtyInput);
                }
            }

            // Generate HTML table for detailed view
            generateHTMLTable(overallTotals, estimateTitle);
        }


        document.getElementById('estimate-title').addEventListener('input', updateOverallTotals);

        document.getElementById('split-piping-rate').addEventListener('input', updateOverallTotals);
        document.getElementById('split-margin-split').addEventListener('input', updateOverallTotals);

        document.getElementById('duct-piping-rate').addEventListener('input', updateOverallTotals);
        document.getElementById('duct-margin-split').addEventListener('input', updateOverallTotals);

        document.getElementById('package-piping-rate').addEventListener('input', updateOverallTotals);
        document.getElementById('package-margin-split').addEventListener('input', updateOverallTotals);

        document.getElementById('vrf-piping-rate').addEventListener('input', updateOverallTotals);
        document.getElementById('vrf-margin-split').addEventListener('input', updateOverallTotals);

        document.getElementById('cassette-piping-rate').addEventListener('input', updateOverallTotals);
        document.getElementById('cassette-margin-split').addEventListener('input', updateOverallTotals);

        document.getElementById('floor-piping-rate').addEventListener('input', updateOverallTotals);
        document.getElementById('floor-margin-split').addEventListener('input', updateOverallTotals);

        document.getElementById('extra-pipe').addEventListener('input', updateOverallTotals);
        document.getElementById('vav-box').addEventListener('input', updateOverallTotals);
        document.getElementById('discountInput').addEventListener('input', updateOverallTotals);

        
    });




$(document).ready(function() {	
	/* create an invoice */
	$("#xin-form").submit(function(e){
	/*Form Submit*/
	e.preventDefault();
		var obj = $(this), action = obj.attr('name');
		$('.save').prop('disabled', true);
		
		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize()+"&is_ajax=1&type=add_record&form="+action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != '') {
					toastr.error(JSON.error);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					$('.save').prop('disabled', false);
					Ladda.stopAll();
				} else {
					toastr.success(JSON.result);
					$('.save').prop('disabled', false);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();
					window.location = main_url+'/estimates-list';
				}
			}
		});
	});	
	
});








</script>
