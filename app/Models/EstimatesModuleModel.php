<?php
namespace App\Models;

use CodeIgniter\Model;
	
class EstimatesModuleModel extends Model {
 
    protected $table = 'ci_estimates_module';

    protected $primaryKey = 'module_id';
    
	// get all fields of table
    protected $allowedFields = ['module_id', 'estimate_id', 'module_name', 'created_at'];
	
	
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
	
}
?>