<?php
namespace App\Models;

use CodeIgniter\Model;
	
class EstimatesitemsModel extends Model {
 
    protected $table = 'ci_estimates_items';

    protected $primaryKey = 'estimate_item_id';
    
	// get all fields of table
    protected $allowedFields = ['estimate_item_id','estimate_id','location','area','capacity','qty_hrs','description','model_code','estimate_module_id','created_at'];
	
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
	
}
?>