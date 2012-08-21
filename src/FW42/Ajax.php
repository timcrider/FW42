<?php
/**
* Ajax data wrapper.
*
*/

/**
* Ajax data wrapper
*/
class FW42_Ajax {
	/**
	*
	*/
	protected $Status;
	
	/**
	*
	*/
	protected $Message;
	
	/**
	*
	*/
	protected $Data;
	
	/**
	*
	*/
	public function setStatus($status) {
		$this->Status = (bool)$status;
		return $this->Status;
	}

	/**
	*
	*/
	public function setMessage($message) {
		$this->Message = (string)$message;
		return $this->Message;
	}

	/**
	*
	*/
	public function setData($data) {
		$this->Data = $data;
		return $this->Data;
	}

	/**
	*
	*/
	public function getStatus() {
		return $this->Status;
	}

	/**
	*
	*/
	public function getMessage() {
		return $this->Message;
	}

	/**
	*
	*/
	public function getData() {
		return $this->Data;
	}
	
	/**
	*
	*/
	public function Package() {
		return json_encode(array(
			'status'  => $this->Status,
			'message' => $this->Message,
			'data'    => $this->Data
		
		));
	}
	
	/**
	*
	*/
	public function Render() {
		if (!headers_sent()) {
			http_response_code(200);
			header('Content-type: application/json');
		}

		print $this->Package();
		
		return;
	}
}
