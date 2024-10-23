<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the TimeHRM License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.timehrm.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to timehrm.official@gmail.com so we can send you a copy immediately.
 *
 * @author   TimeHRM
 * @author-email  timehrm.official@gmail.com
 * @copyright  Copyright © timehrm.com All Rights Reserved
 */

namespace App\Controllers\Erp;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\I18n\Time;

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\ProjectsModel;
use App\Models\EstimatesModel;
use App\Models\EstimatesProductsModel;
use App\Models\MachineTypeModel;
use App\Models\InvoicesModel;
use App\Models\InvoiceitemsModel;
use App\Models\EstimatesitemsModel;
use App\Models\EstimatesModuleModel;
use App\Models\MainModel;
use App\Models\ConstantsModel;
use App\Models\WarehouseModel;
use App\Models\Moduleattributes;
use App\Models\Moduleattributesval;
use App\Models\Moduleattributesvalsel;
use CodeIgniter\HTTP\Request;

class Estimates extends BaseController
{

	//project_estimates
	public function project_estimates()
	{

		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('estimate2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Dashboard.xin_estimates') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'estimates';
		$data['breadcrumbs'] = lang('Dashboard.xin_estimates');

		$data['subview'] = view('erp/estimates/estimate_project_list', $data);
		return view('erp/layout/layout_main', $data); //page load


	}

	public function create_estimate()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		//$SuperroleModel = new SuperroleModel();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('estimate3', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Main.xin_create_new_estimate') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'create_estimates';
		$data['breadcrumbs'] = lang('Main.xin_create_new_estimate');

		$data['subview'] = view('erp/estimates/create_estimate', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function fetchBrandsModelCode($brandId)
	{
		$EstimatesProductsModel = new EstimatesProductsModel();

		// Fetch model codes where 'category_id' matches the passed $brandId
		$modelCodes = $EstimatesProductsModel->where('category_id', $brandId)->findAll();

		if (!empty($modelCodes)) {
			// Return JSON response
			return $this->response->setJSON($modelCodes);
		} else {
			// Handle case where no model codes are found for the brand
			return $this->response->setJSON(['error' => 'No model codes found for this brand'])->setStatusCode(404);
		}
	}


	public function estimate_details()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		//$SuperroleModel = new SuperroleModel();
		$usession = $session->get('sup_username');
		$EstimatesModel = new EstimatesModel();
		$request = \Config\Services::request();
		$ifield_id = udecode($request->uri->getSegment(3));


		$isegment_val = $EstimatesModel->where('estimates_id', $ifield_id)->first();

		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff' && $user_info['user_type'] != 'customer') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'customer') {
			if (!in_array('estimate2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Main.xin_view_estimate') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'estimate_details';
		$data['breadcrumbs'] = lang('Main.xin_view_estimate');

		$data['subview'] = view('erp/estimates/estimate_details', $data);

		return view('erp/layout/layout_main', $data); //page load
	}

	public function getProductDetails()
	{
		$request = \Config\Services::request();
		$productId = $request->uri->getSegment(3);

		// Load the product model
		$productModel = new EstimatesProductsModel();
		$product = $productModel->where('estimates_products_id', $productId)->first();

		if ($product) {
			// Load the machine type model
			$machineTypeModel = new ConstantsModel();
			// Fetch machine type name based on machine_type_id from product
			$machineType = $machineTypeModel->where('constants_id', $product['machine_type_id'])->first();

			// Add machine_type_name to product data
			if ($machineType) {
				$product['machine_type_name'] = $machineType['category_name'];
			} else {
				$product['machine_type_name'] = null; // or a default value if needed
			}
		}

		return $this->response->setJSON($product);
	}


	public function product_estimates()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('product1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Inventory.xin_products') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'estimates_products';
		$data['breadcrumbs'] = lang('Inventory.xin_products');
		$data['subview'] = view('erp/estimates/estimate_product_list', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	public function product_estimates_category()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('product_category1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Dashboard.xin_category') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'cons_estimates_category';
		$data['breadcrumbs'] = lang('Dashboard.xin_category');

		$data['subview'] = view('erp/constants/key_estimate_category', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	public function product_estimates_brand()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('product_category1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Dashboard.xin_category') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'cons_estimates_brand';
		$data['breadcrumbs'] = lang('Dashboard.xin_category');

		$data['subview'] = view('erp/constants/key_estimate_brand', $data);
		return view('erp/layout/layout_main', $data); //page load

	}

	public function estimate_product_list()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('erp/login'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$EstimatesProductsModel = new EstimatesProductsModel();
		$ConstantsModel = new ConstantsModel();
		$WarehouseModel = new WarehouseModel();
		$xin_system = erp_company_settings();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if ($user_info['user_type'] == 'staff') {
			$get_data = $EstimatesProductsModel->where('company_id', $user_info['company_id'])->orderBy('estimates_products_id', 'ASC')->findAll();
		} else {
			$get_data = $EstimatesProductsModel->where('company_id', $usession['sup_user_id'])->orderBy('estimates_products_id', 'ASC')->findAll();
		}

		$data = array();

		foreach ($get_data as $r) {

			$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url() . 'erp/estimate-product-view/' . uencode($r['estimates_products_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><span class="fa fa-arrow-circle-right"></span></button></a></span>';
			if (in_array('product4', staff_role_resource()) || $user_info['user_type'] == 'company') { //delete
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['estimates_products_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			// category
			$category_info = $ConstantsModel->where('constants_id', $r['category_id'])->first();

			if ($category_info) {
				$category_name = $category_info['category_name'];
			} else {
				$category_name = '--';
			}

			// machine type
			$machine_type_info = $ConstantsModel->where('constants_id', $r['machine_type_id'])->first();

			if ($machine_type_info) {
				$machine_type_name = $machine_type_info['category_name'];
			} else {
				$machine_type_name = '--';
			}
			// warehouse


			// purchase price
			$purchase_price = number_to_currency($r['purchase_price'], $xin_system['default_currency'], null, 2);
			// selling price
			$retail_price = number_to_currency($r['retail_price'], $xin_system['default_currency'], null, 2);
			// product rating

			$iproduct = '
			<p class="m-0 d-inline-block align-middle font-16">
				<a href="#!" class="text-body">' . $r['product_name'] . '</a>
				<br>
			</p>';
			// check out of stock and expired products


			$created_at = set_date_format($r['created_at']) . '<br>';
			$combhr = $edit . $delete;
			$ivisitor_name = '
				' . $iproduct . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>';
			$model_code = $r['model_code'];
			$data[] = array(
				$ivisitor_name,
				$model_code,
				$category_name,
				$machine_type_name,
				$r['product_qty'],
				$r['capacity'],
				$purchase_price,
				$retail_price,
				$created_at
			);
		}
		$output = array(
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	public function estimate_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('erp/login'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$EstimatesModel = new EstimatesModel();
		$ConstantsModel = new ConstantsModel();
		$WarehouseModel = new WarehouseModel();
		$xin_system = erp_company_settings();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if ($user_info['user_type'] == 'staff') {
			$get_data = $EstimatesModel->where('company_id', $user_info['company_id'])->orderBy('estimates_id', 'ASC')->findAll();
		} else {
			$get_data = $EstimatesModel->where('company_id', $usession['sup_user_id'])->orderBy('estimates_id', 'ASC')->findAll();
		}


		$data = array();

		foreach ($get_data as $r) {

			$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url() . 'erp/estimate-view/' . uencode($r['estimates_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><span class="fa fa-arrow-circle-right"></span></button></a></span>';
			if (in_array('product4', staff_role_resource()) || $user_info['user_type'] == 'company') { //delete
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['estimates_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}
			// category
			$category_info = $ConstantsModel->where('constants_id', $r['category_id'])->first();
			if ($category_info) {
				$category_name = $category_info['category_name'];
			} else {
				$category_name = '--';
			}
			// warehouse


			// purchase price
			$purchase_price = number_to_currency($r['purchase_price'], $xin_system['default_currency'], null, 2);
			// selling price
			$retail_price = number_to_currency($r['retail_price'], $xin_system['default_currency'], null, 2);
			// product rating

			$iproduct = '
			<p class="m-0 d-inline-block align-middle font-16">
				<a href="#!" class="text-body">' . $r['product_name'] . '</a>
				<br>
			</p>';
			// check out of stock and expired products


			$created_at = set_date_format($r['created_at']) . '<br>';
			$combhr = $edit . $delete;
			$ivisitor_name = '
				' . $iproduct . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>';
			$model_code = $r['model_code'];
			$data[] = array(
				$ivisitor_name,
				$model_code,
				$category_name,
				$r['product_qty'],
				$purchase_price,
				$retail_price,
				$created_at
			);
		}
		$output = array(
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	//create_estimate
	public function add_estimate_product()
	{
		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
	
		if ($this->request->getPost('type') === 'add_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'name' => [
					'rules'  => 'required',
					'errors' => [
						'required' =>  lang('Main.xin_error_field_text')
					]
				],
				'category' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],


				'qty' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],

				'purchase_price' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'selling_price' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"name" => $validation->getError('name'),
					"category" => $validation->getError('category'),
					"model_code" => $validation->getError('model_code'),
					"qty" => $validation->getError('qty'),
					"purchase_price" => $validation->getError('purchase_price'),
					"selling_price" => $validation->getError('selling_price'),
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				// upload file

			
			
				$name = $this->request->getPost('name', FILTER_SANITIZE_STRING);
				$category = $this->request->getPost('category', FILTER_SANITIZE_STRING);
				$model_code = $this->request->getPost('model_code', FILTER_SANITIZE_STRING);
				$capacity = $this->request->getPost('capacity', FILTER_SANITIZE_STRING);
				$qty = $this->request->getPost('qty', FILTER_SANITIZE_STRING);
				$purchase_price = $this->request->getPost('purchase_price', FILTER_SANITIZE_STRING);
				$selling_price = $this->request->getPost('selling_price', FILTER_SANITIZE_STRING);
				$product_description = $this->request->getPost('product_description', FILTER_SANITIZE_STRING);
				$machine_type_id = $this->request->getPost('machine_type_id', FILTER_SANITIZE_STRING);
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$staff_id = $usession['sup_user_id'];
					$company_id = $user_info['company_id'];
				} else {
					$staff_id = $usession['sup_user_id'];
					$company_id = $usession['sup_user_id'];
				}

				$data = [
					'product_name' => $name,
					'product_qty'  => $qty,
					'company_id'  => $company_id,
					'model_code'  => $model_code,
					'category_id'  => $category,
					'capacity'     => $capacity,
					'purchase_price'  => $purchase_price,
					'retail_price'  => $selling_price,
					'product_description'  => trim($product_description),
					'machine_type_id' => $machine_type_id,
					'added_by'  => $usession['sup_user_id'],
					'created_at' => date('d-m-Y h:i:s'),
					'status'  => 1,
				];

				$EstimatesProductsModel = new EstimatesProductsModel();
				$result = $EstimatesProductsModel->insert($data);

				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_product_added_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
				$this->output($Return);
				exit;
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}
	//estimate_details

	public function estimate_product_view()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$EstimatesProductsModel = new EstimatesProductsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$request = \Config\Services::request();
		$ifield_id = udecode($request->uri->getSegment(3));
		$isegment_val = $EstimatesProductsModel->where('estimates_products_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('product1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Inventory.xin_product_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'product_details';
		$data['breadcrumbs'] = lang('Inventory.xin_product_details');

		$data['subview'] = view('erp/estimates/estimate_product_view', $data);

		return view('erp/layout/layout_main', $data); //page load
	}

	public function update_estimate_product()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'edit_record') {

			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'name' => [
					'rules'  => 'required',
					'errors' => [
						'required' =>  lang('Main.xin_error_field_text')
					]
				],

				'qty' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],

				'purchase_price' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'selling_price' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"name" => $validation->getError('name'),
					"category" => $validation->getError('category'),
					"model_code" => $validation->getError('model_code'),
					"qty" => $validation->getError('qty'),
					"capacity" => $validation->getError('capacity'),
					"purchase_price" => $validation->getError('purchase_price'),
					"selling_price" => $validation->getError('selling_price'),
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$name = $this->request->getPost('name', FILTER_SANITIZE_STRING);
				$category = $this->request->getPost('category', FILTER_SANITIZE_STRING);
				$model_code = $this->request->getPost('model_code', FILTER_SANITIZE_STRING);
				$qty = $this->request->getPost('qty', FILTER_SANITIZE_STRING);
				$capacity = $this->request->getPost('capacity', FILTER_SANITIZE_STRING);
				$purchase_price = $this->request->getPost('purchase_price', FILTER_SANITIZE_STRING);
				$selling_price = $this->request->getPost('selling_price', FILTER_SANITIZE_STRING);
				$machine_type_id = $this->request->getPost('machine_type_id', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$product_description = $this->request->getPost('product_description', FILTER_SANITIZE_STRING);
				$UsersModel = new UsersModel();
				$MainModel = new MainModel();


				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'product_name' => $name,
					'product_qty'  => $qty,
					'capacity'  => $capacity,
					'company_id'  => $company_id,
					'model_code'  => $model_code,
					'category_id'  => $category,
					'purchase_price'  => $purchase_price,
					'retail_price'  => $selling_price,
					'product_description'  => trim($product_description),
					'machine_type_id' => $machine_type_id,
					'added_by'  => $usession['sup_user_id'],
					'created_at' => date('d-m-Y h:i:s'),
					'status'  => 1,
				];

				$EstimatesProductsModel = new EstimatesProductsModel();
				$result = $EstimatesProductsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_product_updated_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
				$this->output($Return);
				exit;
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}

	//edit_estimate
	public function edit_estimate()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		//$SuperroleModel = new SuperroleModel();
		$usession = $session->get('sup_username');
		$EstimatesModel = new EstimatesModel();
		$request = \Config\Services::request();
		$ifield_id = udecode($request->uri->getSegment(3));
		$isegment_val = $EstimatesModel->where('estimates_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('estimate4', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Main.xin_edit_estimate') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'create_estimates';
		$data['breadcrumbs'] = lang('Main.xin_edit_estimate');

		$data['subview'] = view('erp/estimates/edit_estimate', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	//view_project_estimate
	public function view_project_estimate()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		//$SuperroleModel = new SuperroleModel();
		$usession = $session->get('sup_username');
		$EstimatesModel = new EstimatesModel();
		$request = \Config\Services::request();
		$ifield_id = udecode($request->uri->getSegment(3));
		$isegment_val = $EstimatesModel->where('estimates_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Main.xin_print_estimate') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoice_details';
		$data['breadcrumbs'] = lang('Main.xin_print_estimate');

		$data['subview'] = view('erp/estimates/view_project_estimate', $data);
		return view('erp/layout/pre_layout_main', $data); //page load
	}
	// |||add record|||
	public function create_new_estimate()
	{
		// Turn on full error reporting
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

		// Set validation rules
		$rules = [
			'estimate_title' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'estimate_attend_to' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'estimate_number' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'ref_no' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'estimate_date' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'estimate_due_date' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
		];

		// Validate the input
		if (!$this->validate($rules)) {
			// Display validation errors
			$errors = $validation->getErrors();
			foreach ($errors as $key => $error) {
				$Return['error'] = $error;
				$this->output($Return);
				return;  // Stop execution if an error is found
			}
		}

		
		if (!isset($_POST['item_module_index']) || !is_array($_POST['item_module_index'])) {
			$Return['error'] = 'You must add at least one item to the estimate.';
			$this->output($Return);
			return;
		}

		$modules = [];
		foreach ($_POST['item_module_index'] as $index => $moduleIndex) {
			// Ensure the module exists before adding items to it
			if (!isset($modules[$moduleIndex])) {
				$modules[$moduleIndex] = [
					'module_name' => $_POST['module_name'][$moduleIndex],
					'items' => [],
					'custom_items' => []
				];
			}
			
			// Add the standard items
			$modules[$moduleIndex]['items'][] = [
				'location' => $_POST['location'][$index],
				'area' => $_POST['area'][$index],
				'brand' => $_POST['brand'][$index],
				'model_code' => $_POST['model_code'][$index],
				'capacity' => $_POST['capacity'][$index],
				'description' => $_POST['description'][$index],
				'qty' => $_POST['qty'][$index],
				'price' => $_POST['price'][$index],
				'machine_type_id' => $_POST['machine_type_id'][$index],
			];
		}

		// Check if custom items exist and add them to their respective modules
		if (!empty($_POST['custom_item_module_index'])) {
			foreach ($_POST['custom_item_module_index'] as $index => $moduleIndex) {
				// Ensure the module exists before adding custom items to it
				if (!isset($modules[$moduleIndex])) {
					$modules[$moduleIndex] = [
						'module_name' => $_POST['module_name'][$moduleIndex],
						'items' => [],
						'custom_items' => []
					];
				}

				// Add the custom items
				$modules[$moduleIndex]['custom_items'][] = [
					'name' => $_POST['custom_name'][$index],
					'description' => $_POST['custom_description'][$index],
					'qty' => $_POST['custom_qty'][$index],
					'price' => $_POST['custom_price'][$index],
					'total_price' => $_POST['custom_total_price'][$index],
				];
			}
		}

		// Handle overall totals
		$overallTotals = [];
		if (isset($_POST['overall_totals'])) {
			foreach ($_POST['overall_totals'] as $machineType => $totals) {
				$overallTotals[$machineType] = [
					'capacity' => $totals['capacity'],
					'qty' => $totals['qty'],
				];
			}
		}

		$modules['overall_totals'] = $overallTotals;

		
		// Proceed with processing the data
		$estimate_title = $_POST['estimate_title'];
		$estimate_attend_to = $_POST['estimate_attend_to'];
		$invoice_number = $_POST['estimate_number'];
		$ref_no = $_POST['ref_no'];
		$client_id = $_POST['client'];
		$invoice_date = $_POST['estimate_date'];
		$invoice_due_date = $_POST['estimate_due_date'];
		$added_by = $_POST['added_by'];
		$contactNumber = $_POST['contact_number'] ?? "";
		$terms_conditions = $_POST['terms_conditions'] ?? "";
		$general_terms_conditions = $_POST['general_terms_conditions'] ?? "";

		$split_piping_charge = $_POST['split_piping_charge'] ?? 0;
		$split_margin_charge = $_POST['split_margin_charge'] ?? 0;
		$duct_piping_charge = $_POST['duct_piping_charge'] ?? 0;
		$duct_margin_charge = $_POST['duct_margin_charge'] ?? 0;
		$package_piping_charge = $_POST['package_piping_charge'] ?? 0;
		$package_margin_charge = $_POST['package_margin_charge'] ?? 0;
		$vrf_piping_charge = $_POST['vrf_piping_charge'] ?? 0;
		$vrf_margin_charge = $_POST['vrf_margin_charge'] ?? 0;
		$cassette_piping_charge = $_POST['cassette_piping_charge'] ?? 0;
		$cassette_margin_charge = $_POST['cassette_margin_charge'] ?? 0;
		$floor_piping_charge = $_POST['floor_piping_charge'] ?? 0;
		$floor_margin_charge = $_POST['floor_margin_charge'] ?? 0;
		$vav_box = $_POST['vav_box'] ?? 0;
		$extra_pipe = $_POST['extra_pipe'] ?? 0;
		$profit = $_POST['profit'] ?? 0;
		$discount = $_POST['discount'] ?? 0;
		$project_cost = $_POST['project_cost'] ?? 0;

		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

		// invoice month
		$dd1 = explode('-', $invoice_date);
		$inv_mnth = $dd1[0] . '-' . $dd1[1];

		$data = [
			'estimate_number'  => $invoice_number,
			'ref_no'  => $ref_no,
			'company_id' => $company_id,
			'client_id' => $client_id,
			'estimate_date'  => $invoice_date,
			'estimate_month'  => $inv_mnth,
			'estimate_due_date'  => $invoice_due_date,
			'status'  => 0,
			'payment_method'  => 0,
			'contact_number' => $contactNumber,
			'created_at' => time(),
			'added_by' => $added_by,
			'estimate_title' => $estimate_title,
			'estimate_attend_to' => $estimate_attend_to,
			'split_piping_charge' => $split_piping_charge,
			'split_margin_charge' => $split_margin_charge,
			'duct_piping_charge' => $duct_piping_charge,
			'duct_margin_charge' => $duct_margin_charge,
			'package_piping_charge' => $package_piping_charge,
			'package_margin_charge' => $package_margin_charge,
			'vrf_piping_charge' => $vrf_piping_charge,
			'vrf_margin_charge' => $vrf_margin_charge,
			'cassette_piping_charge' => $cassette_piping_charge,
			'cassette_margin_charge' => $cassette_margin_charge,
			'floor_piping_charge' => $floor_piping_charge,
			'floor_margin_charge' => $floor_margin_charge,
			'vav_box' => $vav_box,
			'extra_pipe' => $extra_pipe,
			'profit' => $profit,
			'discount' => $discount,
			'project_cost' => $project_cost,
			'estimate_module' => json_encode($modules),
			'terms_conditions' => $terms_conditions,
			'general_terms_conditions' => $general_terms_conditions
		];
		// var_dump($data);exit;

		$EstimatesModel = new \App\Models\EstimatesModel();
		$result = $EstimatesModel->insert($data);
		$estimate_id = $EstimatesModel->insertID();

		if ($result) {
			
			$Return['result'] = lang('Success.ci_estimate_created__msg');
			$this->output($Return);
			exit;
		} else {
			// Log or display database errors
			$Return['error'] = lang('Main.xin_error_msg');
			$Return['error'] .= $EstimatesModel->errors();
		}
	
	}
	public function update_estimate()
	{
		// Turn on full error reporting
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

		// Set validation rules
		$rules = [
			'estimate_title' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'estimate_attend_to' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'estimate_number' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'ref_no' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'estimate_date' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'estimate_due_date' => [
				'rules'  => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
		];

		// Validate the input
		if (!$this->validate($rules)) {
			// Display validation errors
			$errors = $validation->getErrors();
			foreach ($errors as $key => $error) {
				$Return['error'] = $error;
				$this->output($Return);
				return;  // Stop execution if an error is found
			}
		}

		// var_dump($_POST);exit;
		if (!isset($_POST['item_module_index']) || !is_array($_POST['item_module_index'])) {
			$Return['error'] = 'You must add at least one item to the estimate.';
			$this->output($Return);
			return;
		}

		$modules = [];
		foreach ($_POST['item_module_index'] as $index => $moduleIndex) {
			// Ensure the module exists before adding items to it
			if (!isset($modules[$moduleIndex])) {
				$modules[$moduleIndex] = [
					'module_name' => $_POST['module_name'][$moduleIndex],
					'items' => [],
					'custom_items' => []
				];
			}
			
			// Add the standard items
			$modules[$moduleIndex]['items'][] = [
				'location' => $_POST['location'][$index],
				'area' => $_POST['area'][$index],
				'brand' => $_POST['brand'][$index],
				'model_code' => $_POST['model_code'][$index],
				'capacity' => $_POST['capacity'][$index],
				'description' => $_POST['description'][$index],
				'qty' => $_POST['qty'][$index],
				'price' => $_POST['price'][$index],
				'machine_type_id' => $_POST['machine_type_id'][$index],
			];
		}

		// Check if custom items exist and add them to their respective modules
		if (!empty($_POST['custom_item_module_index'])) {
			foreach ($_POST['custom_item_module_index'] as $index => $moduleIndex) {
				// Ensure the module exists before adding custom items to it
				if (!isset($modules[$moduleIndex])) {
					$modules[$moduleIndex] = [
						'module_name' => $_POST['module_name'][$moduleIndex],
						'items' => [],
						'custom_items' => []
					];
				}

				// Add the custom items
				$modules[$moduleIndex]['custom_items'][] = [
					'name' => $_POST['custom_name'][$index],
					'description' => $_POST['custom_description'][$index],
					'qty' => $_POST['custom_qty'][$index],
					'price' => $_POST['custom_price'][$index],
					'total_price' => $_POST['custom_total_price'][$index],
				];
			}
		}
		$overallTotals = [];
		if (isset($_POST['overall_totals'])) {
			foreach ($_POST['overall_totals'] as $machineType => $totals) {
				$overallTotals[$machineType] = [
					'capacity' => $totals['capacity'] ?? 0,
					'qty' => $totals['qty'] ?? 0,
				];
			}
		}

		$modules['overall_totals'] = $overallTotals;

	
		// Proceed with processing the data
		$estimate_id = $_POST['estimates_id'];
		$estimate_title = $_POST['estimate_title'] ?? '';
		$estimate_attend_to = $_POST['estimate_attend_to'] ?? '';
		$invoice_number = $_POST['estimate_number'] ?? '';
		$ref_no = $_POST['ref_no'] ?? '';
		$client_id = $_POST['client'] ?? '';
		$invoice_date = $_POST['estimate_date'] ?? '';
		$invoice_due_date = $_POST['estimate_due_date'] ?? '';
		$added_by = $_POST['added_by'] ?? '';
		$contactNumber = $_POST['contact_number'] ?? '';
		$terms_conditions = $_POST['terms_conditions'] ?? "";
		$general_terms_conditions = $_POST['general_terms_conditions'] ?? "";

		$split_piping_charge = $_POST['split_piping_charge'] ?? 0;
		$split_margin_charge = $_POST['split_margin_charge'] ?? 0;
		$duct_piping_charge = $_POST['duct_piping_charge'] ?? 0;
		$duct_margin_charge = $_POST['duct_margin_charge'] ?? 0;
		$package_piping_charge = $_POST['package_piping_charge'] ?? 0;
		$package_margin_charge = $_POST['package_margin_charge'] ?? 0;
		$vrf_piping_charge = $_POST['vrf_piping_charge'] ?? 0;
		$vrf_margin_charge = $_POST['vrf_margin_charge'] ?? 0;
		$cassette_piping_charge = $_POST['cassette_piping_charge'] ?? 0;
		$cassette_margin_charge = $_POST['cassette_margin_charge'] ?? 0;
		$floor_piping_charge = $_POST['floor_piping_charge'] ?? 0;
		$floor_margin_charge = $_POST['floor_margin_charge'] ?? 0;
		$vav_box = $_POST['vav_box'] ?? 0;
		$extra_pipe = $_POST['extra_pipe'] ?? 0;
		$profit = $_POST['profit'] ?? 0;
		$discount = $_POST['discount'] ?? 0;
		$project_cost = $_POST['project_cost'] ?? 0;

		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

		// invoice month
		$dd1 = explode('-', $invoice_date);
		$inv_mnth = $dd1[0] . '-' . $dd1[1];

		$data = [
			'estimate_number'  => $invoice_number,
			'ref_no'  => $ref_no,
			'company_id' => $company_id,
			'client_id' => $client_id,
			'estimate_date'  => $invoice_date,
			'estimate_month'  => $inv_mnth,
			'estimate_due_date'  => $invoice_due_date,
			'status'  => 0,
			'payment_method'  => 0,
			'contact_number' => $contactNumber,
			'created_at' => time(),
			'added_by' => $added_by,
			'estimate_title' => $estimate_title,
			'estimate_attend_to' => $estimate_attend_to,
			'split_piping_charge' => $split_piping_charge,
			'split_margin_charge' => $split_margin_charge,
			'duct_piping_charge' => $duct_piping_charge,
			'duct_margin_charge' => $duct_margin_charge,
			'package_piping_charge' => $package_piping_charge,
			'package_margin_charge' => $package_margin_charge,
			'vrf_piping_charge' => $vrf_piping_charge,
			'vrf_margin_charge' => $vrf_margin_charge,
			'cassette_piping_charge' => $cassette_piping_charge,
			'cassette_margin_charge' => $cassette_margin_charge,
			'floor_piping_charge' => $floor_piping_charge,
			'floor_margin_charge' => $floor_margin_charge,
			'vav_box' => $vav_box,
			'extra_pipe' => $extra_pipe,
			'profit' => $profit,
			'discount' => $discount,
			'project_cost' => $project_cost,
			'estimate_module' => json_encode($modules),
			'terms_conditions' => $terms_conditions,
			'general_terms_conditions' => $general_terms_conditions
		];

		$EstimatesModel = new \App\Models\EstimatesModel();
		$result = $EstimatesModel->update($estimate_id, $data);

		if ($result) {
			$Return['result'] = lang('Success.ci_estimate_updated_msg');
			
			$this->output($Return);
			exit;
		} else {
			// Log or display database errors
			$Return['error'] = lang('Main.xin_error_msg');
			$Return['error'] .= $EstimatesModel->errors();
		}
	
	}



	// |||update record|||

	///estimates_calendar
	public function estimates_calendar()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('invoice_calendar', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Dashboard.xin_quote_calendar') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'estimates';
		$data['breadcrumbs'] = lang('Dashboard.xin_quote_calendar');

		$data['subview'] = view('erp/estimates/calendar_estimates', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	public function client_invoice_calendar()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_invoice_calendar') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoices';
		$data['breadcrumbs'] = lang('Dashboard.xin_invoice_calendar');

		$data['subview'] = view('erp/invoices/calendar_client_invoices', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	// delete record
	public function delete_estimate_items()
	{
		var_dump($this->request->getVar('record_id'));
		die;

		if ($this->request->getVar('record_id')) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$Return['csrf_hash'] = csrf_hash();
			$EstimatesitemsModel = new EstimatesitemsModel();
			$result = $EstimatesitemsModel->where('estimates_id', $record_id)->delete($record_id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_estimate_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
		}
	}
	public function estimate_approve()
	{
		$request = \Config\Services::request();
		$estimateId = $request->uri->getSegment(4);
		
		$EstimatesModel = new EstimatesModel();

		$estimate = $EstimatesModel->where('estimates_id', $estimateId)->first();

		if ($estimate) {
			$EstimatesModel->update($estimateId, ['status' => 1]);

			return $this->response->setJSON(['status' => 'success', 'message' => 'Estimate approved successfully.']);
		} else {
			return $this->response->setJSON(['status' => 'error', 'message' => 'Estimate not found.'], 404);
		}
	}

	// read record
	public function read_estimate_data()
	{
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('erp/login'));
		}
		$id = $request->getGet('field_id');
		$data = [
			'field_id' => $id,
		];
		if ($session->has('sup_username')) {
			return view('erp/estimates/update_estimate', $data);
		} else {
			return redirect()->to(site_url('erp/login'));
		}
	}
	// |||update cancel_estimate_record|||
	public function cancel_estimate_record()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'cancel_estimate_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			if ($user_info['user_type'] == 'staff') {
				$company_id = $user_info['company_id'];
			} else {
				$company_id = $usession['sup_user_id'];
			}
			$data = [
				'status'  => 2
			];
			$EstimatesModel = new EstimatesModel();
			$result = $EstimatesModel->update($id, $data);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_estimate_cancelled_success_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
			exit;
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}
	// |||update convert_estimate_record|||
	public function convert_estimate_record()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'convert_estimate_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$UsersModel = new UsersModel();
			$EstimatesModel = new EstimatesModel();
			$EstimatesitemsModel = new EstimatesitemsModel();
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$result = $EstimatesModel->where('estimates_id', $id)->first();

			$data = [
				'invoice_number'  => $result['estimate_number'],
				'company_id' => $result['company_id'],
				'client_id' => $result['client_id'],
				'project_id'  => $result['project_id'],
				'invoice_month'  => $result['estimate_month'],
				'invoice_date'  => $result['estimate_date'],
				'invoice_due_date'  => $result['estimate_due_date'],
				'sub_total_amount'  => $result['sub_total_amount'],
				'discount_type'  => $result['discount_type'],
				'discount_figure'  => $result['discount_figure'],
				'total_tax'  => $result['total_tax'],
				'tax_type'  => $result['tax_type'],
				'total_discount'  => $result['total_discount'],
				'grand_total'  => $result['grand_total'],
				'status'  => 0,
				'payment_method'  => 0,
				'invoice_note'  => $result['estimate_note'],
				'created_at' => date('d-m-Y h:i:s')
			];
			$InvoicesModel = new InvoicesModel();
			$result = $InvoicesModel->insert($data);
			$invoice_id = $InvoicesModel->insertID();
			$invoice_items = $EstimatesitemsModel->where('estimates_id', $id)->findAll();
			foreach ($invoice_items as $item) {
				$data2 = array(
					'invoice_id' => $invoice_id,
					'project_id' => $item['project_id'],
					'item_name' => $item['item_name'],
					'item_qty' => $item['item_qty'],
					'item_unit_price' => $item['item_unit_price'],
					'item_sub_total' => $item['item_sub_total'],
					'created_at' => date('d-m-Y H:i:s')
				);
				$InvoiceitemsModel = new InvoiceitemsModel();
				$InvoiceitemsModel->insert($data2);
			}
			$data3 = [
				'status'  => 1
			];
			$EstimatesModel = new EstimatesModel();
			$result = $EstimatesModel->update($id, $data3);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_estimate_convert_to_invoice_success_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
			exit;
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}
	// delete record
	public function delete_estimate_product()
	{

		if ($this->request->getPost('type') == 'delete_record') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$EstimatesProductsModel = new EstimatesProductsModel();
			$result = $EstimatesProductsModel->where('estimates_products_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_product_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
		}
	}
}
