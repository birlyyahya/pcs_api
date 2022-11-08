<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
require APPPATH . '/libraries/Firebase/JWT/JWT.php';
use \Firebase\JWT\JWT;

class Api_pcs extends REST_Controller {

    private $secret_key = "hahahahahah";

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_admin');
    }

	public function index_get()
	{
        $data = $this->M_admin->getData();
        $result = array (
            "success" => true,
            "message" => "data ditemukan!",
            "data" => $data
        );


        echo json_encode($result);
	}

    public function index_post()
    {
        
        $data = array(
            'email' => $this->input->post('email'),
            'password' => $this->input->post('password'),
            'nama' => $this->input->post('nama')
        );

        $insert = $this->M_admin->insertData($data);

        if($insert){
            $this->response($data,200);
        }else {
            $this->response($data,502);
        }

    }

    public function admin_delete()
    {
        $id = $this->delete("id");
        $result = $this->M_admin->deleteAdmin($id);

        if (empty($result)) {
            $data_json = array(
                "success" => true,
                "message" => "Id Tidak Valid",
                "data" => null 
            );
            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        };
        $data_json = array(
            "succcess" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "admin" => $result
            )
        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }
    public function admin_put()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        //validasi
        // $validation_message = [];
        
        // if ($this->put("email")=="") {
        //     array_push($validation_message,"Email tidak boleh kosong");
        // }
        // if ($this->put("email")!="" && !filter_var($this->put("email"),FILTER_VALIDATE_EMAIL)) {
        //     array_push($validation_message,"format email tidak valid");
        // }
        // if ($this->put("password")=="") {
        //     array_push($validation_message,"Password tidak boleh kosong");
        // }
        // if ($this->put("nama")=="") {
        //     array_push($validation_message,"Nama tidak boleh kosong");
        // }
        // if(count($validation_message)>0){
        //     $data_json = array(
        //         "succcess" => false,
        //         "message" => "data tidak valid",
        //         "data" => $validation_message
        //     );
        //     $this->response($data_json,REST_Controller::HTTP_OK);
        //     $this->output->_display();
        //     exit();
        // }
        $data = array(
            "email" => $this->put("email"),
            "password" => md5($this->put("password")),
            "nama" => $this->put("nama")
        );
        $id = $this->put("id");

        $result = $this->M_admin->updateAdmin($data,$id);

        $data_json = array(
            "success" => true,
            "message" => "update berhasil",
            "data" => array(
                "admin" => $result
            )
        );
        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function cekToken()
    {
        try{
            $token = $this->input->get_request_header('Authorization');

            if (!empty($token)) {
                $token = explode('',$token)[1];
            }

            $token_decode = JWT::decode($token,$this->secret_key,array('HS256'));
        }
        catch(Exception $e){
            $data_json= array(
                "success" => false,
                "message" => "Token Tidak Valid",
                "error_code" => 1204,
                "data" => null
            );
            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
    }

    public function login_post()
    {
        $data = array(
            "email" => $this->input->post("email"),
            "password" => md5($this->input->post("password"))
        );

        $result = $this->M_admin->cekLoginAdmin($data);

        if (empty($result)) {

            $data_json= array(
                "success" => false,
                "message" => "Token Tidak Valid",
                "error_code" => 1204,
                "data" => null
            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }else {
            $date = new Datetime();

            $payload['id'] = $result['id'];
            $payload['email'] = $result['email'];
            $payload['iat'] = $date->getTimestamp();
            $payload['exp'] = $date->getTimestamp() + 3600;

            $data_json= array(
                "success" => true,
                "message" => "Otentikasi Berhasil",
                "data" => array(
                    "admin" => $result,
                    "token" => JWT::encode($payload,$this->secret_key)
                )
            );

        }
        
    }


}
