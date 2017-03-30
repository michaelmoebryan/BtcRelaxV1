<?php
class ExchangeRateskuna {

    public function ExchangeRateskuna() {
        
    }

    /*
      @return an instance of RestResponse */

    public static function btcuahByKuna() {
        $req = Self::httpGet("https://kuna.io/api/v2/tickers/btcuah");
        $json = json_decode($req); 
        if (json_last_error() === JSON_ERROR_NONE) { 
            if (is_numeric($json->ticker->last))
            {
                return $json->ticker->last;
            }
        } 
        throw  new Exception("Cannot get exchange rate!");
        
    }
    

    public static function httpGet($url, $post_vars = false) {
	header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
        header('Pragma: no-cache'); // HTTP 1.0.
        header('Expires: 0'); // Proxies.

        $post_contents = '';
	if ($post_vars) {
		if (is_array($post_vars)) {
			foreach($post_vars as $key => $val) {
				$post_contents .= ($post_contents ? '&' : '').urlencode($key).'='.urlencode($val);
			}
		} else {
			$post_contents = $post_vars;
		}
	}
	$uinf = \parse_url($url);
	$host = $uinf['host'];
	$path = $uinf['path'];
	$path .= (isset($uinf['query']) && $uinf['query']) ? ('?'.$uinf['query']) : '';
	$headers = array(
		($post_contents ? 'POST' : 'GET')." $path HTTP/1.1",
		"Host: $host",
	);
	if ($post_contents) {
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'Content-Length: '.strlen($post_contents);
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 600);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	if ($post_contents) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_contents);
	}
	$data = curl_exec($ch);
	if (curl_errno($ch)) {
		return false;
	}
	curl_close($ch);
	return $data;
    }

}


