<?php
$useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36";

if(isset($_GET['url'])) {
    $link = $_GET['url'];
    if(isFind($link, 'stock.adobe.com')) {
        $cookie = 'cookies.txt';
        if(!file_exists($cookie)) {
            die;
        }
        $url = urldecode($link);
        if(strpos($url, "?") !== false) {
    		if(strpos($url, "&asset_id=") !== false) {
    			$explo = explode("&asset_id=", $url);
    			$imageid = $explo[1];
    		}
    		elseif(strpos($url, "?asset_id=") !== false) {
    			$explo = explode("?asset_id=", $url);
    			$imageid = end(explode("/",$explo[0]));
    		}
    		elseif(strpos($url, "?k=")!==false){
    			$explo = explode("?k=", $url);
    			$imageid = $explo[1];
    		}
    		else {
    			$explo = explode("?", $url);
    			$explo = explode("/", $explo[0]);
    			$imageid = end($explo);
    		}
        }
        else {
    		$explo = explode("/", $url);
    		$imageid = end($explo);
        }
    	$geturl = "https://stock.adobe.com/Ajax/MediaData/".$imageid."?full=1";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $geturl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $headers = [
            'Referer: https://stock.adobe.com',
            'User-Agent: '.$useragent,
            'upgrade-insecure-requests: 1'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                                                                                            
        $rs = curl_exec($ch);
        $vid_data = $rs;
        curl_close($ch);
        $json = json_decode($rs);
        $is_standard = $json->is_standard;
        $is_premium = $json->is_premium;
        $is_video = $json->is_video;
        if($is_premium) {
            die;
        }
        $url = "https://stock.adobe.com/id/".$imageid;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $headers = [
            'Referer: https://stock.adobe.com/',
            'User-Agent: '.$useragent,
            'Connection: keep-alive'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                                                                                            
        $rs = curl_exec($ch);
        curl_close($ch);
        $html = new DOMDocument();
        @$html->loadHTML($rs);
        $balance = 0;
        $xpath = new DOMXPath($html);
        $els = $xpath->query("//span[@data-t='quota-images-standard' or @data-t='quota-cct-pro-unlimited-label'] or @data-t='quota-by-license-badge-text-1']");
        if($els->length > 0) {
            $el = $els->item(0);
            $balance = $el->nodeValue;
        }
        $start = '<script type="application/json" id="js-page-config">';
        $end = '</script>';
        $getjs = getBetweenString($rs, $start, $end);
        $json = json_decode($getjs);
        $csrf = $json->stockPortal->reduxState->portal->page->csrfToken;
        $xRequestId = $json->stockPortal->reduxState->portal->page->xRequestId;
		
        if($is_video) {
            $vid_data = json_decode($vid_data, true);
            unset($vid_data["keywords"]);
            $_data = array('id' => 'quotaConfirm', 'asset_details' => $vid_data, 'use_credits' => false, 'ims_profile' => array('userId' => '74801D4E6572C2130A495E27@AdobeID', 'countryCode' => 'DE'), 'license_id' => 3, 'content_id' => $imageid, 'team_profile_type' => 'personal', 'is_signed_in' => true, 'member_id' => 227641201, 'member_type' => 'stk', 'test_and_target' => array('is_member' => true, 'has_subscription' => true, 'country_code' => 'UK', 'content_type' => 'Video', 'price_tier' => 'none', 'user_type' => 'stk', 'state_name' => 'quotaConfirm'));
            $vid_data = json_encode($_data);
            
            $url = 'https://stock.adobe.com/de/Ajax/Checkout/4';
        	$ch = curl_init($url);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $vid_data);
        	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            $headers = [
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'User-Agent: '.$useragent,
                'X-Checkout-State-Id: quotaConfirm',
                'X-Core-Checkout: 1',
                'X-Csrf-Token: '.$csrf,
                'X-Current-Page-Url: https://stock.adobe.com/'.$imageid,
                'X-Request-Id: '.$xRequestId,
                'X-Requested-With: XMLHttpRequest'
            ];
        	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        	$content = curl_exec($ch);
        	curl_close($ch);

            $requrl = "https://stock.adobe.com/de/Ajax/GetLicenseDownloadUrl/$imageid/3";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $requrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $headers = [
                'referer: '.$url,
                'User-Agent: '.$useragent,
                'x-csrf-token: '.$csrf,
                'x-request-id: '.$xRequestId,
                'x-requested-with: XMLHttpRequest',
                'sec-fetch-site: same-origin',
                'sec-fetch-mode: cors',
                'sec-fetch-dest: empty',
                'origin: https://stock.adobe.com',
                'Connection: keep-alive',
                'content-length: 0'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $content = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($content);
            $download_url = $data->download_url;
            $format = $data->format;
            $filename = "AdobeStock_$imageid.$format";
            downloadFile($download_url, $cookie, $headers, $filename);
            header('Content-Type: application/json');
            $rsArray = array('status' => true, 'imageid' => $imageid, 'download' => $download_url, 'name' => $filename);
            echo json_encode($rsArray);
            die;
        }
        else {
            $requrl = 'https://stock.adobe.com/de/Ajax/GetDownload/'.$imageid.'/1';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $requrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $headers = [
                'referer: '.$url,
                'User-Agent: '.$useragent,
                'x-csrf-token: '.$csrf,
                'x-request-id: '.$xRequestId,
                'x-requested-with: XMLHttpRequest',
                'sec-fetch-site: same-origin',
                'sec-fetch-mode: cors',
                'sec-fetch-dest: empty',
                'origin: https://stock.adobe.com',
                'Connection: keep-alive',
                'content-length: 0'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $content = curl_exec($ch);
            curl_close($ch);
            $jsons = json_decode($content);
            if(!empty($jsons->download_url)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $jsons->download_url);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_NOBODY, 1);
                $headers = [
                    'Referer: '.$url,
                    'User-Agent: '.$useragent,
                    'Connection: keep-alive'
                ];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                                                                                            
                $rs = curl_exec($ch);
                $download_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
            }
        }
        if(!empty($download_url) && strpos($download_url, "amazonaws.com") !== false) {
            if(!$is_video) {
                $explo = explode("filename%3D", $download_url);
                if(strpos($explo[1], "&") !== false) {
                    $expl = explode("&", $explo[1]);
                    $filename = str_replace("%22", "", $expl[0]);
                }
                else {
                    $filename = str_replace("%22", "", $explo[1]);
                }
                if(strpos($download_url, 'filename%3D') === false) {
                    $filename = basename(trim(strtok($download_url, '?')));
                }
                header('Content-Type: application/json');
                $rsArray = array('status' => true, 'imageid' => $imageid, 'name' => $filename, 'download' => $download_url);
                echo json_encode($rsArray);
            }
            die;
        }
        else
        {
            header('Content-Type: application/json');
            $rsArray = array('status' => false, 'imageid' => $imageid, 'msg' => 'error', 'details' => $jsons);
            echo json_encode($rsArray);
            die;
        }
        
    }
}

function isFind($string, $find) {
    $pos = stripos($string, $find);
    if($pos === false) {
        return false;
    }
    return true;
}
function getBetweenString($string, $start, $end) {
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
function downloadFile($url, $cookie, $headers, $filename) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $data = curl_exec($curl);
    curl_close($ch);
    file_put_contents($filename, $data);
    return $data;
}
