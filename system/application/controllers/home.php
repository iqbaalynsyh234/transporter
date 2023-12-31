<?php
include "base.php";

class Home extends Base {

	function Home()
	{
		parent::Base();

		$this->load->helper("common");
		$this->load->model("smsmodel");
	}

	function index()
	{
		if ($this->sess)
		{
			redirect(base_url()."trackers/");
		}

		if ($this->config->item("login"))
		{
			$servername = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "lacak-mobil.com";
			$urls = parse_url($this->config->item("login"));
			if ($urls['host'] != $servername)
			{
				redirect($this->config->item("login"));
			}
		}

		if($this->config->item("iscustompage"))
		{
			$params['content'] = $this->load->view("globalpage/member/login", false, true);
		}
		else
		{
			$params['content'] = $this->load->view("member/login", false, true);
		}
		$this->load->view('template', $params);
	}

	function config()
	{
		if ($this->sess->user_type != 1)
		{
			redirect(base_url());
		}

		$q = $this->db->get("config");
		$rows = $q->result();

		for($i=0; $i < count($rows); $i++)
		{
			$settings[$rows[$i]->config_name] = $rows[$i]->config_value;
		}

		$params['settings'] = isset($settings) ? $settings : false;

		$callback['title'] = $this->lang->line("lconfiguration");
		$callback['html'] = $this->load->view("app/form", $params, true);

		echo json_encode($callback);
	}

	function saveconfig()
	{
		if ($this->sess->user_type != 1)
		{
			redirect(base_url());
		}

		$this->db->where_in("config_name", array_keys($_POST));
		$this->db->delete("config");

		foreach($_POST as $key=>$val)
		{
			unset($insert);

			if ($key == "bypasspassword")
			{
				$val = md5($val);
			}

			$insert['config_name'] = $key;
			$insert['config_value'] = $val;
			$insert['config_lastmodified'] = date("Y-m-d H:i:s");
			$insert['config_lastmodifier'] = $this->sess->user_id;

			$this->db->insert("config", $insert);
		}

		$callback['error'] = false;
		echo json_encode($callback);
	}

	function contactus($id=0)
	{
		$this->db->where("vehicle_id", $id);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
			exit;
		}

		$row = $q->row();

		$params['vehicle'] = $row;
		$callback['html'] = $this->load->view("app/expired_contactus", $params, true);
		$callback['title'] = $this->lang->line("lcontact_us");

		echo json_encode($callback);
	}

	function savecontactus()
	{
		$subject = isset($_POST['subject']) ? trim($_POST['subject']) : "";
		$message = isset($_POST['message']) ? trim($_POST['message']) : "";

		if (strlen($subject) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_subject');

			echo json_encode($callback);

			return;
		}

		if (strlen($message) == 0)
		{
			$callback['error'] = true;
			$callback['message'] = $this->lang->line('lempty_message');

			echo json_encode($callback);

			return;
		}

		unset($insert);

		$insert['contactus_subject'] = $subject;
		$insert['contactus_message'] = $message;
		$insert['contactus_creator'] = $this->sess->user_id;
		$insert['contactus_created'] = date("Y-m-d H:i:s");
		$insert['contactus_status'] = 1;

		$this->db->insert('contactus', $insert);

		sendlocalhost($subject, $message);

		$callback['error'] = false;
		$callback['message'] = $this->lang->line('lcontactus_success');

		echo json_encode($callback);
	}

	function login($token, $redirect="trackers", $additional="")
	{
		$uniqid = uniqid();
		$this->db->where("'".$uniqid."'='".$uniqid."'", null, false);
		$this->db->where("session_id", $token);
		$this->db->join("user", "session_user = user_id");
		$this->db->join("agent", "agent_id = user_agent", "left outer");
		$q = $this->db->get("session");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$row = $q->row();

		if ($this->config->item("SMS_WELCOME"))
		{
			$this->smsmodel->welcome($row);
		}

		if ($this->config->item("ADDSESSION_SERVICE"))
		{
			$params['p'] = "adilahsoft".date("Ymd");
			$params['u'] = $row->user_login;

			curl_post_async($this->config->item("ADDSESSION_SERVICE"), $params);
		}

		unset($row->agent_sms_1_expired);
		unset($row->agent_sms_n_expired);

		$this->session->set_flashdata('showannounce', 1);
		$this->session->set_userdata($this->config->item('session_name'), serialize($row));

		unset($insert);

		$insert['log_created'] = date("Y-m-d H:i:s");
		$insert['log_creator'] = $row->user_id;
		$insert['log_type'] = "login";
		$insert['log_ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
		$insert['log_data'] = $row->user_login;
		$insert['log_target'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";

		$this->db->insert("log", $insert);

		$this->db->where("session_referer <>", "payment");
		$this->db->where("session_id", $token);
		$this->db->delete("session");

		if ($redirect)
		{
			if ($redirect == "invoice")
			{
				redirect(base_url().$redirect."/bayar/".$additional);
			}
			else
			{
				redirect(base_url().$redirect."/");
			}
		}
		else
		{
			redirect(base_url()."trackers/");
		}
	}

	function appendlog($log)
	{
		$log = isset($_POST['log']) ? trim($_POST['log']) :  "";
		if (! $log) return;

		$pathlog = $this->config->item("MYSQL_PATH_LOG");
		$prefixlog = $this->config->item("MYSQL_PREFIX_LOG");

		if (! $pathlog) return;

		if (! is_dir($pathlog))
		{
			@mkdir($pathlog);
		}

		if (! in_array($pathlog[strlen($pathlog)-1], array("/", "\\")))
		{
			$logfile = sprintf('%s\\%s%s.log', $pathlog, $prefixlog, date("Ymd"));
		}
		else
		{
			$logfile = sprintf('%s%s%s.log', $pathlog, $prefixlog, date("Ymd"));
		}

		$fout = @fopen($logfile, "a");
		if (! $fout) return;
		$retry = 1;
		$exit = false;
		while(! @flock($fout, LOCK_EX))
		{
			if ($retry > 100)
			{
				fclose($fout);
				$exit = true;
			}

			$retry++;
		}

		if ($exit) return;

		fwrite($fout, "[".date("H:i:s")."] : ".$log."\r\n");
		flock($fout, LOCK_UN);
		fclose($fout);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
