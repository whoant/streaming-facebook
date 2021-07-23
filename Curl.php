<?php


class MultiCurl{
	private $curl = NULL,
	$active = NULL,
	$data = array(),
	$arrayCurl = NULL;


	public function _set($array){
		$this->arrayCurl = $array;
		$this->curl = curl_multi_init();

		foreach ($this->arrayCurl as $key => $value) {
			curl_multi_add_handle($this->curl, $value);
		}

		do {
			$status = curl_multi_exec($this->curl, $active);
		} while ($active && $status == CURLM_OK);

	}

	public function _getData(){
		return @$this->data;
	}

	public function _run(){
		foreach ($this->arrayCurl as $key => $value) {
			
			$header_size = curl_getinfo($value, CURLINFO_HEADER_SIZE);
			$response = curl_multi_getcontent($value);

			$this->data[] = [
				'data' => substr($response, $header_size),
				'code' => curl_getinfo($value, CURLINFO_HTTP_CODE),
				'header_code' => curl_getinfo($value),
				'redirect' => curl_getinfo($value, CURLINFO_EFFECTIVE_URL),
			];
			curl_multi_remove_handle($this->curl, $value);
			curl_close($value);
		}
		curl_multi_close($this->curl);
		// $this->_clear();
	}
	public function __destruct() {
		$this->arrayCurl = NULL;
		$this->curl = NULL;
		$this->active = NULL;
	}
}

class Curl{
	private $url = NULL,
	$timeoutconnect = NULL,
	$timeoutcurl = NULL,
	$post = NULL,
	$cookie = NULL,
	$useragent = NULL,
	$transfer = true,
	$user = NULL,
	$pass = NULL,
	$path_cookie = NULL,
	$header = NULL,
	$data = NULL,
	$resHeader = NULL,
	$code = NULL,
	$proxy = NULL,
	$redirect = NULL,
	$limit = NULL;

	public function _setURL($url = NULL) // Set Url Curl
	{
		$this->url = $url;
	}
	public function _setProxy($arrProxy = NULL) // Set Url Curl
	{
		$this->proxy = $arrProxy;
	}
	public function _setTimeOutConnect($time = 60) // Set Time Out Curl
	{
		$this->timeoutconnect = $time;
	}
	public function _setTimeOutCurl($time = 300) // Set Time Out Curl
	{
		$this->timeoutcurl = $time;
	}
	public function _setPostData($data = NULL) // Set Post Data
	{
		if(NULL !== $data){
			$this->post = $data;
		}
	}
	public function _setUserAgent($useragent = NULL) // Set User Agent
	{
		$this->useragent = $useragent;
	}
	public function _setTransfer($transfer = true) // Set Return Transfer
	{
		$this->transfer = $transfer;
	}
	public function _setCook($cookie = NULL) // Set Cookie
	{
		$this->cookie = $cookie;
	}
	public function _setAuth($user = NULL, $pass = NULL) // Set Auth
	{
		if(NULL !== $user AND NULL !== $pass){
			$this->user = $user;
			$this->pass = $pass;
		}
	}
	public function _pathCook($path = NULL) // Set Path Save And Load Cookie
	{
		$this->path_cookie = $path;
	}
	public function _setHeader($header = NULL) // Set Header
	{
		if(NULL !== $header AND is_array($header) == true){
			$this->header = $header;
		}
	}


	public function _getData() // Get Data
	{
		return @$this->data;
	}
	public function _getCode() // Get Code
	{
		return @$this->code;
	}

	public function _getRedirect() // Get Code
	{
		return @$this->redirect;
	}
	public function _getHeader_code() // Get Code
	{
		return @$this->header_code;
	}

	public function _getSetCookie(){
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $this->resHeader, $matches);
		return join("; ", $matches[1]);
		
	}

	public function _clear() // Clear Data
	{
		$this->url = NULL;
		$this->timeoutconnect = NULL;
		$this->timeoutcurl = NULL;
		$this->post = NULL;
		$this->cookie = NULL;
		$this->useragent = NULL;
		$this->transfer = true;
		$this->user = NULL;
		$this->pass = NULL;
		$this->path_cookie = NULL;
		$this->header = NULL;
		$this->proxy = NULL;
		$this->limit = NULL;
	}
	public function _run($type = 'normal') // Run Curl
	{
		try{
			if(NULL == $this->url){
				throw new \Exceptions('không có thông tin URL!');
			}
		}
		catch( \Exceptions $e){
			die($e->eMessageCurl());
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		@curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		if(NULL !== $this->timeoutconnect){
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeoutconnect); // Thời Gian Kết Nối
		}
		if(NULL !== $this->timeoutcurl){
			curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeoutcurl); // Thời Gian Lấy Dữ Liệu
		}
		if(NULL !== $this->post){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->post);
		}
		if(is_array($this->proxy)){
			curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTPS);
			curl_setopt($curl, CURLOPT_PROXY, $this->proxy['proxy']);
			curl_setopt($curl, CURLOPT_PROXYPORT, $this->proxy['port']);

			if(isset($this->proxy['userpass'])){
				curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->proxy['userpass']);
			}

		}
		$this->useragent = (NULL !== $this->useragent) ? $this->useragent : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36';
		curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
		$array_true_false = array(TRUE, FALSE);
		$this->transfer = (in_array($this->transfer, $array_true_false) == false) ? TRUE : $this->transfer;
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, $this->transfer);
		$array_header = array();
		if(NULL !== $this->header AND is_array($this->header) == true){
			$array_header = $this->header;
		}
		if(NULL !== $this->cookie){
			$this->header = array();
			$this->header[] = 'Connection: keep-alive';
			$this->header[] = 'Keep-Alive: 300';
			$this->header[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
			$this->header[] = 'Accept-Language: en-us,en;q=0.5';
			$this->header[] = 'Expect:';
			$this->header = array_merge($array_header, $this->header);
			curl_setopt($curl, CURLOPT_COOKIE, $this->cookie);
		}
		if(NULL !== $this->user AND NULL !== $this->pass){
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST|CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $this->user.':'.$this->pass);
		}
		if(NULL !== $this->path_cookie){
			curl_setopt($curl, CURLOPT_COOKIEFILE, $this->path_cookie);
			curl_setopt($curl, CURLOPT_COOKIEJAR, $this->path_cookie);

		}
		
		curl_setopt($curl, CURLOPT_HEADER, true);
		
		// curl_setopt($ch, CURLOPT_HEADERFUNCTION, "curlResponseHeaderCallback");
		if($this->header){
			curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
		}
		if ($type == 'multi') return $curl;
		
		$response = curl_exec($curl);

		$this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->redirect = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$this->resHeader = substr($response, 0, $header_size);
		$this->data = substr($response, $header_size);

		$this->header_code = curl_getinfo($curl);
		curl_close($curl);
		$this->_clear();
		
	}
}
?>
