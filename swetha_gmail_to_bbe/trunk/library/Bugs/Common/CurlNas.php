<?php

class Bugs_Common_CurlNas
{
	public static function download_file($nas_ip, $bbe_prod_request_action, $bbe_prod_request_controller, $params = array(), $filename = "", $bbe_prod_version = DEFAULT_BBE_PROD_VERSION)
	{
		$nas_ip = (!strstr($nas_ip, "http://")) ? "http://" . $nas_ip : $nas_ip;
		
		$url_params = array_merge($params, array(
			"bbe_prod_version" => $bbe_prod_version,
			"bbe_prod_request_action" => $bbe_prod_request_action,
			"bbe_prod_request_controller" => $bbe_prod_request_controller,
		));
		
		$download_url = $nas_ip . "/eebrowser/bbe_prod/index.php?ts=" . md5(microtime() . rand(1, 10000)) . "&" . http_build_query($url_params);
		$tmp_zip_PATH = BASE_PATH . "/tmp/downloads/";
		
		if(!file_exists($tmp_zip_PATH))
		{
			mkdir($tmp_zip_PATH, 0777, true);
		}
		
		$complete_tmp_zip_filename = $tmp_zip_PATH . strtolower(Bugs_Auth_Session::getIdentity()->nickname) . "." . date("Ymd_his") . "." . md5(rand(1,1000)) . "_" . $filename;
		$cnt = self::download_file_by_chunks($download_url, $complete_tmp_zip_filename);
		
		return $complete_tmp_zip_filename;
	}
	
	
	/**
	 * Copy remote file over HTTP one small chunk at a time.
	 *
	 * @param $infile The full URL to the remote file
	 * @param $outfile The path where to save the file
	 */
	public static function download_file_by_chunks($infile, $outfile, $chunk_size_MB = 5)
	{
		$chunksize = $chunk_size_MB * (1024 * 1024); // 10 Megs
	
		/**
		 * parse_url breaks a part a URL into it's parts, i.e. host, path,
		 * query string, etc.
		 */
		$parts = parse_url($infile);
		
		$port = isset($parts['port']) ? $parts['port'] : 80;
		
		$i_handle = fsockopen($parts['host'], $port, $errstr, $errcode, 5);
		$o_handle = fopen($outfile, 'wb');
	
		if ($i_handle == false || $o_handle == false) {
			return false;
		}
	
		if (!empty($parts['query'])) {
			$parts['path'] .= '?' . $parts['query'];
		}
	
		/**
		 * Send the request to the server for the file
		 */
		$request = "GET {$parts['path']} HTTP/1.1\r\n";
		$request .= "Host: {$parts['host']}\r\n";
		$request .= "User-Agent: Mozilla/5.0\r\n";
		$request .= "Keep-Alive: 115\r\n";
		$request .= "Connection: keep-alive\r\n\r\n";
		fwrite($i_handle, $request);
	
		/**
		 * Now read the headers from the remote server. We'll need
		 * to get the content length.
		 */
		$headers = array();
		while(!feof($i_handle)) {
			$line = fgets($i_handle);
			if ($line == "\r\n") break;
			$headers[] = $line;
		}
	
		/**
		 * Look for the Content-Length header, and get the size
		 * of the remote file.
		 */
		$length = 0;
		foreach($headers as $header) {
			if (stripos($header, 'Content-Length:') === 0) {
				$length = (int)str_replace('Content-Length: ', '', $header);
				break;
			}
		}
	
		/**
		 * Start reading in the remote file, and writing it to the
		 * local file one chunk at a time.
		 */
		$cnt = 0;
		while(!feof($i_handle)) {
			$buf = '';
			$buf = fread($i_handle, $chunksize);
			$bytes = fwrite($o_handle, $buf);
			if ($bytes == false) {
				return false;
			}
			$cnt += $bytes;
	
			/**
			 * We're done reading when we've reached the conent length
			 */
			if ($cnt >= $length) break;
		}
	
		fclose($i_handle);
		fclose($o_handle);
		
		return $cnt;
	}
	
	
}