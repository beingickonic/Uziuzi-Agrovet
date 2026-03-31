<?php
// 🏢 Safaricom Daraja API Configuration
// Replace with your real credentials from Safaricom Developer Portal
define('MPESA_CONSUMER_KEY', 'YOUR_CONSUMER_KEY'); 
define('MPESA_CONSUMER_SECRET', 'YOUR_CONSUMER_SECRET');
define('MPESA_SHORTCODE', '174379'); // Default Sandbox Shortcode
define('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');

class MpesaHandler {
    
    // 🔑 Step 1: Generate Access Token
    public function getAccessToken() {
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode(MPESA_CONSUMER_KEY . ':' . MPESA_CONSUMER_SECRET)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $result = json_decode($response);
        return $result->access_token ?? null;
    }

    // 📱 Step 2: Trigger STK Push (Prompt)
    public function stkPush($phone, $amount, $reference) {
        $token = $this->getAccessToken();
        if(!$token) return ['status' => 'error', 'msg' => 'Failed to generate token'];

        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $timestamp = date('YmdHis');
        $password = base64_encode(MPESA_SHORTCODE . MPESA_PASSKEY . $timestamp);
        
        // Format phone: 2547XXXXXXXX
        $phone = preg_replace('/^0/', '254', $phone);

        $curl_post_data = array(
            'BusinessShortCode' => MPESA_SHORTCODE,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => round($amount),
            'PartyA' => $phone,
            'PartyB' => MPESA_SHORTCODE,
            'PhoneNumber' => $phone,
            'CallBackURL' => 'https://yourdomain.com/mpesa_callback.php', 
            'AccountReference' => 'Uziuzi Agrovet',
            'TransactionDesc' => 'Payment for ' . $reference
        );

        $data_string = json_encode($curl_post_data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ['ResponseCode' => '99', 'CustomerMessage' => 'cURL Error: ' . $err];
        } else {
            return json_decode($response);
        }
    }
}
?>
