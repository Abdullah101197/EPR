<?php
namespace App\Models;

use CodeIgniter\Model;
	
class EstimatesProductsModel extends Model {
 
    protected $table = 'ci_estimates_products';

    protected $primaryKey = 'estimates_products_id';
    
	// get all fields of table
    protected $allowedFields = ['estimates_products_id','company_id','model_code','capacity','product_description','product_name','product_qty','machine_type_id','purchase_price','retail_price','category_id','added_by','created_at','status'];
	
	
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
	
}
?>