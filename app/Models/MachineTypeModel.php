<?php
namespace App\Models;

use CodeIgniter\Model;

class MachineTypeModel extends Model {

    protected $table = 'ci_machine_types';

    protected $primaryKey = 'machine_type_id';
    
    // Get all fields of the table
    protected $allowedFields = ['machine_type_id', 'machine_type', 'created_at', 'company_id'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
?>
