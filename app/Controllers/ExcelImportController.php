<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\I18n\Time;
use App\Models\TimesheetModel;
use App\Models\UsersModel;

class ExcelImportController extends Controller
{
  
    public function import()
    {
        var_dump('ok');die;
        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            // Get the temporary file path
            $filePath = $file->getTempName();

            // Load the Excel file using PhpSpreadsheet
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($filePath);

            // Get the active sheet data
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            // Get session and user information
            $session = \Config\Services::session();
            $usession = $session->get('sup_username');	
            $UsersModel = new UsersModel();
            $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
            
            // Determine company and employee IDs based on user type
            if ($user_info['user_type'] == 'staff') {
                $company_id = $user_info['company_id'];
                $employee_id = $usession['sup_user_id'];
            } else {
                $company_id = $usession['sup_user_id'];
                $employee_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);
            }

            // Initialize TimesheetModel
            $TimesheetModel = new TimesheetModel();

            // Loop through sheet data and insert into database
            foreach ($sheetData as $index => $row) {
                // Skip header row
                if ($index == 0) continue;

                // Prepare data for insertion
                $data = [
                    'company_id' => $company_id,
                    'employee_id'  => $employee_id,
                    'attendance_date'  => $this->transformDate($row[0]),
                    'clock_in'  => $this->transformTime($row[2]),
                    'clock_in_ip_address' => 1, // Example IP address, you may need to adjust this
                    'clock_out'  =>$this->transformTime($row[3]),
                    'clock_out_ip_address'  => 1, // Example IP address, you may need to adjust this
                    'clock_in_out'  => 0,
                    'clock_in_latitude' => 1, // Example latitude, you may need to adjust this
                    'clock_in_longitude'  => 1, // Example longitude, you may need to adjust this
                    'clock_out_latitude'  => 1, // Example latitude, you may need to adjust this
                    'clock_out_longitude'  => 1, // Example longitude, you may need to adjust this
                    'time_late'  => $this->transformTime($row[2]), // Example time transformation, you may need to adjust this
                    'early_leaving'  => $this->transformTime($row[3]), // Example time transformation, you may need to adjust this
                    'overtime' => $this->transformTime($row[3]), // Example time transformation, you may need to adjust this
                    'total_work'  => $this->transformTime($row[4]), // Example time transformation, you may need to adjust this
                    'total_rest'  => 0, // Example value, you may need to adjust this
                    'attendance_status'  => 'Present', // Example status, you may need to adjust this
                ];

                // Insert data into database
                $result = $TimesheetModel->insert($data);	
            }

            // Redirect after successful import
            return redirect()->to('/excel-import')->with('message', 'Data imported successfully.');
        }

    }

    private function transformDate($date)
    {
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
            return \DateTime::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        }
        return null;
    }

    private function transformTime($time)
    {
        if (empty($time)) {
            return null;
        }
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return \DateTime::createFromFormat('H:i', $time)->format('H:i:s');
        }
        return null;
    }
}
