<?php
namespace App\Models;

use CodeIgniter\Model;
	
class EstimatesModel extends Model {
 
    protected $table = 'ci_estimates';

    protected $primaryKey = 'estimates_id';
    
	// get all fields of table
    protected $allowedFields = [
		'estimates_id',
		'estimate_number',
		'company_id',
		'client_id',
		'estimate_month',
		'estimate_date',
		'estimate_due_date',
		'status',
		'payment_method',
		'created_at',
		'project_id',
		'added_by',
		'estimate_title',
		'estimate_attend_to',
		'estimate_module',
		'split_piping_charge',
		'split_margin_charge',
		'duct_piping_charge',
		'duct_margin_charge',
		'package_piping_charge',
		'package_margin_charge',
		'vav_box',
		'extra_pipe',
		'vrf_piping_charge',
		'vrf_margin_charge',
		'cassette_piping_charge',
		'cassette_margin_charge',
		'floor_piping_charge',
		'floor_margin_charge',
		'terms_conditions',
		'profit',
		'discount',
		'project_cost',
		'general_terms_conditions',
		'ref_no'
	];
	
	
	
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
	
}
?>