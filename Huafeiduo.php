<?php

class Huafeiduo{
	private $API_KEY = '',
		$SECRET_KEY = '';
	private $instance;

	public function __construct($akey,$skey){
		$this->API_KEY = $akey;
		$this->SECRET_KEY = $skey;

		$this->instance = curl_init();
		curl_setopt($this->instance, CURLOPT_TIMEOUT, 30);
		curl_setopt($this->instance, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->instance, CURLOPT_FOLLOWLOCATION, true);    //支持抓取302/301跳转后的页面内容
	}

	public function accountbalance(){
		$params =['api_key'=>$this->API_KEY];
		$sign = $this->sign($params);
		$url = "http://api.huafeiduo.com/gateway.cgi?mod=account.balance&api_key={$this->API_KEY}&sign=$sign";
		return $this->get($url);
	}

	public function orderphoneget($orderid,$sporderid = null){
		$params = [
			'order_id' => $orderid,'api_key'=>$this->API_KEY
		];
		$sign = $this->sign($params);
		$params['sign'] = $sign;
		$pstr = '';
		foreach($params as $key => $value){
			$pstr .= "&{$key}={$value}";
		}

		$url = "http://api.huafeiduo.com/gateway.cgi?mod=order.phone.get$pstr";
		return $this->get($url);
		/*
		 * {
			status: "success",
			data: {
				sp_order_id: "1438052375",
				mobile_num: "18647787979",
				price: "1.3000",
				create_time: "1438052392",
				last_status_change_time: "1438052433",
				status: "success",
				card_worth: "1",
				order_id: "2015072810595217990"
				}
			}
		 */
	}

	public function orderphonelist($starttime,$endtime,$status = 'success',$order_id = null,$page = null){
		$params = ['api_key'=>$this->API_KEY];
		if($starttime){
			$params['start_time'] = $starttime;
			$params['end_time'] = $endtime;
		}
		$params['status'] = $status;
		if($order_id) $params['order_id'] = $order_id;
		if($page) $params['page'] = $page;
		$sign = $this->sign($params);
		$params['sign'] = $sign;
		$pstr = '';
		foreach($params as $key => $value){
			$pstr .= "&{$key}={$value}";
		}

		$url = "http://api.huafeiduo.com/gateway.cgi?mod=order.phone.list$pstr";
		return $this->get($url);
	}

	public function orderphonecheck($phone_number,$card_worth){
		$params = [
			'card_worth' => $card_worth,'phone_number' =>$phone_number,'api_key'=>$this->API_KEY
		];
		$sign = $this->sign($params);
		$params['sign'] = $sign;
		$pstr = '';
		foreach($params as $key => $value){
			$pstr .= "&{$key}={$value}";
		}
		$url = "http://api.huafeiduo.com/gateway.cgi?mod=order.phone.check$pstr";
		return $this->get($url);
	}

	public function orderphonestatus($sp_order_id){
		$params = [
			'sp_order_id' => $sp_order_id,'api_key'=>$this->API_KEY
		];
		$sign = $this->sign($params);
		$params['sign'] = $sign;
		$pstr = '';
		foreach($params as $key => $value){
			$pstr .= "&{$key}={$value}";
		}
		$url = "http://api.huafeiduo.com/gateway.cgi?mod=order.phone.status$pstr";
		return $this->get($url);
	}

	public function orderphonesubmit($phone_number,$card_worth,$sp_order_id,$remark = null,$notifyurl = null){
		$params = [
			'card_worth' => $card_worth,'phone_number' =>$phone_number,'sp_order_id'=>$sp_order_id,'api_key'=>$this->API_KEY
		];
		if($remark) $params['remark'] = $remark;
		if($notifyurl) $params['notify_url'] = $notifyurl;
		$sign = $this->sign($params);
		$params['sign'] = $sign;
		$pstr = '';
		foreach($params as $key => $value){
			$pstr .= "&{$key}={$value}";
		}
		$url = "http://api.huafeiduo.com/gateway.cgi?mod=order.phone.submit{$pstr}";
		return $this->get($url);
	}

	private function sign($params){
		ksort($params);     /// 按键名升序对数组进行排序
		$paramString = '';
		foreach($params as $key => $value){
			$paramString .= "{$key}{$value}";
		}

		$sign = md5($paramString . $this->SECRET_KEY);
		return strtolower($sign);
	}

	private function get($url) {
		if (!$this->instance)	return;
		curl_setopt($this->instance, CURLOPT_URL, $url);
		curl_setopt($this->instance, CURLOPT_HTTPGET, true);
		return $this->excute();
	}

	private function post($url, $params) {
		if (!$this->instance)	return;
		curl_setopt($this->instance, CURLOPT_URL, $url);
		curl_setopt($this->instance, CURLOPT_POST, true);
		curl_setopt($this->instance, CURLOPT_POSTFIELDS, $params);
		return $this->excute();
	}

	private function excute() {
		$result = curl_exec($this->instance);
		return $result;
	}
}