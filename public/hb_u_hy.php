<?php

class HuobiApi
{
    private $api       = 'api.hbdm.vn';
    public $api_method = '';
    public $req_method = '';
    public $access;
    public $secret;
    public $robot      = 'coin.mimaz.org';

    public function __construct($access_key, $secret_key)
    {
        $this->access = $access_key;
        $this->secret = $secret_key;
        date_default_timezone_set('Etc/GMT+0');
    }

    // 全仓u合约
    public function all_u_fuck($start, $up1, $down1, $direction, $beishu, $zhangshu)
    {
        $source           = 'api';
        $this->api_method = '/linear-swap-api/v1/swap_cross_order';
        $this->req_method = 'POST';
        $postdata         = [
            'contract_code' => 'BTC-USDT',
            'price'     => $start,
            'volume'     => $zhangshu,
            'direction'     => $direction,
            'offset'       => 'open',
            'lever_rate' => $beishu,
            'order_price_type' => 'opponent',
            'tp_trigger_price' => $up1,
            'tp_order_price' => $up1,
            'tp_order_price_type' => 'limit',
            'sl_trigger_price' => $down1,
            'sl_order_price' => $down1,
            'sl_order_price_type' => 'limit',
        ];
        $url              = $this->create_sign_url($postdata);
        $return           = $this->curl($url, $postdata);
        return json_decode($return);
    }

    // 查询最近的收益 u全仓
    public function select_user_profit()
    {
        $source           = 'api';
        $this->api_method = '/linear-swap-api/v1/swap_cross_user_settlement_records';
        $this->req_method = 'POST';
        $postdata         = [
            'margin_account' => 'USDT',
            'page_index'     => 1,
            'page_size'       => 10,
        ];
        $url              = $this->create_sign_url($postdata);
        $return           = $this->curl($url, $postdata);
        return json_decode($return);
    }

    /**
     * 类库方法
     */
    // 生成验签URL
    public function create_sign_url($append_param = [])
    {
        // 验签参数
        $param = [
            'AccessKeyId'      => $this->access,
            'SignatureMethod'  => 'HmacSHA256',
            'SignatureVersion' => 2,
            'Timestamp'        => date('Y-m-d\TH:i:s', time()),
        ];
        if ($append_param) {
            foreach ($append_param as $k => $ap) {
                $param[$k] = $ap;
            }
        }
        return 'https://' . $this->api . $this->api_method . '?' . $this->bind_param($param);
    }

    // 组合参数
    public function bind_param($param)
    {
        $u         = [];
        $sort_rank = [];
        foreach ($param as $k => $v) {
            $u[]         = $k . '=' . urlencode($v);
            $sort_rank[] = ord($k);
        }
        asort($u);
        $u[] = 'Signature=' . urlencode($this->create_sig($u));
        return implode('&', $u);
    }

    // 生成签名
    public function create_sig($param)
    {
        $sign_param_1 = $this->req_method . '\n' . $this->api . '\n' . $this->api_method . '\n' . implode('&', $param);
        $signature    = hash_hmac('sha256', $sign_param_1, $this->secret, true);
        return base64_encode($signature);
    }

    public function curl($url, $postdata = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($this->req_method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $output = curl_exec($ch);
        $info   = curl_getinfo($ch);
        curl_close($ch);
        return $output;
    }
}
