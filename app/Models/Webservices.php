<?php

namespace App\Models;
use App\Models\Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webservices extends Model
{
    use HasFactory;

    public function get_All_details($tbl,$order_by)
	{
		$this->db->select('*');
		$this->db->from($tbl);
        $this->db->order_by($order_by);
		$query = $this->db->get();
        //echo $this->db->last_query();exit();
		return $query->result(); 
	}
	
public function get_payment_mode_details_with_mode($company_name_id) {
    $this->db->select('pmdm.*, pm.payment_mode');
    $this->db->from('payment_mode_details_master pmdm');
    $this->db->join('payment_mode pm', 'pm.payment_mode_id = pmdm.payment_mode_id', 'right');
    $this->db->where('pmdm.company_name_id', $company_name_id);
    $this->db->order_by('pmdm.position', 'ASC');
    $query = $this->db->get();

    return $query->result();
}


    
      public function get_All($tbl)
	{
		$this->db->select('*');
		$this->db->from($tbl);
        //$this->db->order_by($order_by);
		$query = $this->db->get();
     	//echo $this->db->last_query();exit();
		return $query->result(); 
	}
	
public function delete_by_condition($table, $condition) {
    return $this->db->where($condition)->delete($table);
}




public function select_by_values_updated($table, $conditions) {
    $this->db->where($conditions);
    $this->db->order_by('updated_at', 'DESC');
    return $this->db->get($table)->result_array();
}

public function get_updated_product_list()
{
    // Replace 'shop_invoice_product' with your actual table name
    $this->db->select('*'); // Adjust columns as needed
    $this->db->from('shop_invoice_product');
    $this->db->where('shop_invoice_id', 0); // Adjust condition if necessary
    $query = $this->db->get();
    return $query->result(); // Returns the updated list
}

    
    public function getAll_details($tbl,$where,$order_by)
	{
		$this->db->select('*');
		$this->db->from($tbl);
        $this->db->where($where);
        $this->db->order_by($order_by);
		$query = $this->db->get();
        //echo $this->db->last_query();exit();
		return $query->result(); 
	}

	public function getAll_details_where_in($tbl,$where,$where_in_id,$where_in,$order_by)
	{
		$this->db->select('*');
		$this->db->from($tbl);
        $this->db->where($where);
        $this->db->where_in($where_in_id, $where_in);
        $this->db->order_by($order_by);
		$query = $this->db->get();
        //echo $this->db->last_query();exit();
		return $query->result(); 
	}

	public function getAll_details_or_where($tbl,$where,$or_where,$order_by)
	{
		$this->db->select('*');
		$this->db->from($tbl);
        $this->db->where($where);
        $this->db->or_where($or_where);
        $this->db->order_by($order_by);
		$query = $this->db->get();
        //echo $this->db->last_query();exit();
		return $query->result(); 
	}

	public function getAll_details_by_like($tbl,$column_name,$where,$order_by)
	{
		$this->db->select('*');
		$this->db->from($tbl);
        $this->db->like($column_name, $where);
        $this->db->order_by($order_by);
		$query = $this->db->get();
        $this->db->last_query();//exit();
		return $query->result(); 
	}
    
    public function select_all_count($tbl,$array)
	{
		$this->db->select('count(*) AS counts');
		$this->db->from($tbl);
        $this->db->where($array);
		$query = $this->db->get();
		//echo $this->db->last_query();//exit();
		return $query->result(); 
	}

	public function select_by_values($tbl, $array)
	{
		$this->db->select('*');
		$this->db->from($tbl);
		$this->db->where($array);
		$query = $this->db->get();
// 		echo $this->db->last_query();//exit();
		return $query->result(); 
	}
	
	public function select_gst_by_status_new($tbl, $array)
{
    $this->db->select('*');
    $this->db->from($tbl);
    $this->db->where($array);
    $this->db->where('status', 1); // Fetch only active GST percentages
    $query = $this->db->get();
    return $query->result(); 
}


	public function delete_by_id($tbl, $where)
	{
		$this->db->where($where);
		$this->db->delete($tbl);
		//echo $this->db->last_query();//exit();
		return true;
	}

    public function getAll($tbl,$order_by)
	{
		$this->db->select('*');
		$this->db->from($tbl);
        $this->db->order_by($order_by);
		$query = $this->db->get();
		return $query->result(); 
	}

	public function update_by_id($tbl,$tbl_id, $id, $data)
	{
		$this->db->where($tbl_id, $id);
		$this->db->update($tbl, $data);
		//$query = $this->db->get(); die;
     //echo $this->db->last_query();exit();
		return true;
	}
	
// 	public function update_by_id1($tbl, $tbl_id, $id, $data)
// {
//     // print_r($data);
//     // die;
//     // Ensure $data is properly formatted
//     if (!is_array($data) || empty($data)) {
//         throw new Exception("Invalid data format. Expected an associative array.");
//     }

//     // Debugging: Log the query for review (optional)
//     $this->db->where($tbl_id, $id);
//     $this->db->update($tbl, $data);
//     // echo $this->db->last_query(); // Uncomment to debug the actual query

//     return $this->db->affected_rows() > 0;
// }


	// public function update($tbl, $data, $where)
	// {
	// 	$this->db->where($where);
	// 	$this->db->update($tbl, $data);
		
	// 	//$query = $this->db->get();
    //  	//echo $this->db->last_query();//exit();
	// 	return true;
	// }

	public function update_after_login($tbl,$where, $data)
	{
		$this->db->update($tbl, $data);
		$this->db->where($where);
		//$query = $this->db->get();
     	//echo $this->db->last_query();//exit();
		return true;
	}

	public function delete_by_conditions($table, $conditions)
{
    // Adding conditions to the query
    $this->db->where($conditions);

    // Deleting the records
    return $this->db->delete($table);
}


	public function select_sum_by_id($select,$tbl,$array)
	{
		$this->db->select($select);
		$this->db->from($tbl);
        $this->db->where($array);
		$query = $this->db->get();
		//echo $this->db->last_query();//exit();
		return $query->result(); 
	}

    public function select_count_by_id($tbl,$array)
	{
		$this->db->select('count(*) AS counts');
		$this->db->from($tbl);
        $this->db->where($array);
		$query = $this->db->get();
		//echo $this->db->last_query();//exit();
		return $query->result(); 
	}
    
    public function getAllOrderby($tbl,$where,$order_by)
	{
		$this->db->select('*');
		$this->db->from($tbl);
        $this->db->where($where);
        $this->db->order_by($order_by);
		$query = $this->db->get();
        //echo $this->db->last_query();exit();
		return $query->result(); 
	}

	public function delete_by_another_id($tbl, $id_name, $id)
	{
		$this->db->where($id_name, $id);
		$this->db->delete($tbl);
		//echo $this->db->last_query();exit();
		return true;
	}

	public function select_by_values_limit($tbl, $where, $order_by, $limit)
	{
		$this->db->select('*');
		$this->db->from($tbl);
		$this->db->where($where);
		$this->db->order_by($order_by);
		$this->db->limit($limit);
		$query = $this->db->get();
		//echo $this->db->last_query();//exit();
		return $query->result(); 
	}

	public function get_where_group_by($tbl,$where, $group_by)
	{
		$this->db->select('*');
		$this->db->from($tbl);
		$this->db->where($where);
		$this->db->group_by($group_by);
		$query = $this->db->get();
		//echo $this->db->last_query();//exit();
		return $query->result(); 
	} 

	public function get_all_product_details($tbl, $where, $order_by)
	{
		$this->db->select('service_category.service_category_id ,service_category.service_category,service_category.gst_per,`service_category`.`hsn_code`,service_details.*');
		$this->db->from($tbl);

		$this->db->where($where);

		$this->db->join('service_category', 'service_details.service_category_id  = service_category.service_category_id', 'inner');

		$this->db->order_by($order_by);
		$query = $this->db->get();
		//echo $this->db->last_query();//exit();
		return $query->result(); 
	}
	
public function get_all_product_details3($tbl, $where, $order_by)
{
    $this->db->select('
        payment_mode_details_master.payment_mode_details_master_id,
        payment_mode_details_master.service_category,
        payment_mode_details_master.position,
        payment_mode_details_master.payment_mode_id,
        payment_mode.payment_mode,
        service_details.*
    ');
    $this->db->from($tbl);
    $this->db->where($where);

    // Join with payment_mode_details_master
    $this->db->join(
        'payment_mode_details_master',
        'service_details.payment_mode_details_master = payment_mode_details_master.payment_mode_details_master_id',
        'inner'
    );

    // Join with payment_mode using payment_mode_id
    $this->db->join(
        'payment_mode',
        'payment_mode_details_master.payment_mode_id = payment_mode.payment_mode_id',
        'inner'
    );

    $this->db->order_by($order_by);
    $query = $this->db->get();
    
    // Uncomment to debug query
    // echo $this->db->last_query(); exit();
    
    return $query->result();
}

	public function get_all_product_stock_details($tbl, $where, $order_by)
	{
		$this->db->select('product_company.product_company,
product_category.product_category,
product_details.*');
		$this->db->from($tbl);

		$this->db->where($where);

		$this->db->join('product_category', 'product_details.product_category_id = product_category.product_category_id', 'inner');
		$this->db->join('product_company', 'product_details.product_company_id = product_company.product_company_id', 'inner');

		$this->db->order_by($order_by);
		$query = $this->db->get();
		//echo $this->db->last_query();//exit();
		return $query->result(); 
	}
	public function get_total_invoice_value_customer_details($select, $tbl, $where)
	{
		$this->db->select($select);
		$this->db->from($tbl);
		$this->db->where($where);
		$this->db->join('shop_invoice_product', 'shop_invoice.shop_invoice_id = shop_invoice_product.shop_invoice_id', 'inner');

		$query = $this->db->get();
		//echo $this->db->last_query();//exit();
		return $query->result();
	}
	

	public function getExecutiveNames($shop_invoice_id)
	{
		$this->db->select('service_executive_details.name');
		$this->db->from('service_executive_details');
		$this->db->join('shop_invoice_service_executive_details', 'shop_invoice_service_executive_details.service_executive_details_id = service_executive_details.service_executive_details_id');
		$this->db->where('shop_invoice_service_executive_details.shop_invoice_id', $shop_invoice_id);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$executive_names = array();
			foreach ($query->result() as $row) {
				$executive_names[] = $row->name;
			}
			return implode(', ', $executive_names); // Concatenate names with commas
		} else {
			return false; // No records found
		}
	}

	public function update_by_id_condition($tbl, $condition, $data)
	{
		$this->db->where($condition);
		$this->db->update($tbl, $data);

		// Check if the update was successful
		return $this->db->affected_rows() > 0;
	}
	
	public function update_company_logo($company_name_id , $data)
{
    // print_r($company_name_id);
    // die;
    $this->db->where('company_name_id ', $company_name_id );
    return $this->db->update('company_name', $data);
}

	public function update_company_logo1($user_id , $data1)
{
    // print_r($company_name_id);
    // die;
    $this->db->where('user_id ', $user_id );
    return $this->db->update('user', $data1);
}

public function get_logo_by_company($company_name_id)
{
    $this->db->where('company_name_id', $company_name_id);
    $query = $this->db->get('company_name');
    return $query->row_array();
}



    public function get_all_users()
{
    $this->db->select('user_id, logo');
    $this->db->from('user'); // Adjust the table name as per your database
    return $this->db->get()->result_array(); // Return result as an array
}


public function get_All_details_by_company($table, $company_name_id, $order_by = null) {
    $this->db->where('company_name_id', $company_name_id); // Add the filter
    if ($order_by) {
        $this->db->order_by($order_by);
    }
    $query = $this->db->get($table);
    return $query->result();
}

public function select_by_values_with_join($table, $conditions)
{
    $this->db->select('company_name.*, user.*');
	$this->db->from($table);
	$this->db->where($conditions);
	$this->db->join('user', 'company_name.company_name_id = user.company_name_id', 'inner');
	$query = $this->db->get();
// 	echo $this->db->last_query();exit();
	return $query->result(); 
}

public function select_latest_by_values($table, $conditions) {
    // Adding a direct where condition for better readability
    if (is_array($conditions)) {
        foreach ($conditions as $column => $value) {
            $this->db->where($column, $value);
        }
    } else {
        // Assuming the conditions are passed as a single value
        $this->db->where('company_name_id', $conditions);
    }

    // Keep the latest record
    $this->db->order_by('subcription_id', 'DESC'); 
    $this->db->limit(1);
    
    // Execute the query
    $query = $this->db->get($table);
    
    // Debugging output for generated SQL
    // echo $this->db->last_query();exit();
    
    return $query->row_array(); // Returns the latest row as an array
}


public function getAll_details_id($table, $where = array(), $order_by = '') {
    $this->db->from($table);
    if (!empty($where)) {
        $this->db->where($where);
    }
    if (!empty($order_by)) {
        $this->db->order_by($order_by);
    }
    $query = $this->db->get();
    return $query->result();
}
}
