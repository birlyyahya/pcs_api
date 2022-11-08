<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_admin extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function getData()
	{
		$this->load->database();
        $data = $this->db->get('admin');
        return $data->result_array();
	}
	
	function insertData($data)
	{
		$this->load->database();
		$insert = $this->db->insert('admin',$data);
		return $insert;
	}
	function deleteAdmin($id)
	{
		$result = $this->db->get_where('admin',array('id'=>$id));

		$this->db->where('id',$id);
		$this->db->delete('admin');

		return $result->row_array();

	}
	function updateAdmin($data,$id)
	{
		$this->db->get_where('admin', array('id'=>$id));
		return  $this->db->update($result,$data);

	}

}

