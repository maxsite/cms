<?php

	function get_download($url){
		
		$ret = false;
		
		if( function_exists('curl_init') ){
			if( $curl = curl_init() ){
				
				if( curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false) ){
					if( curl_setopt($curl,CURLOPT_URL,$url) ){
						if( curl_setopt($curl,CURLOPT_RETURNTRANSFER,true) ){
							if( curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,30) ){
								if( curl_setopt($curl,CURLOPT_HEADER,false) ){
									if( curl_setopt($curl,CURLOPT_ENCODING,"gzip,deflate") ){
									
										$ret = curl_exec($curl);									
										curl_close($curl);
									}
								}
							}
						}
					}
				}
			}
		}
		else{
			$u = parse_url($url);
			
			if( $fp = @fsockopen($u['host'],!empty($u['port']) ? $u['port'] : 80 ) ){
				
			    $headers = 'GET '.  $u['path'] . '?' . $u['query'] .' HTTP/1.0'. "\r\n";
			    $headers .= 'Host: '. $u['host'] ."\r\n";
			    $headers .= 'Connection: Close' . "\r\n\r\n";
				
			    fwrite($fp, $headers);
			    $ret = '';
					
				while( !feof($fp) ){
					$ret .= fgets($fp,1024);
				}
				
				$ret = substr($ret,strpos($ret,"\r\n\r\n") + 4);
				
				fclose($fp);
			}
		}
		
		return $ret;
	}


?>