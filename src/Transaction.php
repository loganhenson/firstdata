<?php namespace FirstData;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Transaction{

    private $url;
    private $version;
    private $request;
    private $gateway_id;
    private $password;
    private $key;
    private $date;
    private $client;

    function __construct($gateway_id, $password, $key_id, $key, $test_mode = false){

        $this->gateway_id = $gateway_id;
        $this->password = $password;
        $this->key_id = $key_id;
        $this->key = $key;
        $this->date = gmdate('Y-m-d\TH:i:s') . 'Z';

        $this->version = '/transaction/v14';

        if(!$test_mode)
            $this->url = 'https://api.globalgatewaye4.firstdata.com' . $this->version;
        else
            $this->url = 'https://api.demo.globalgatewaye4.firstdata.com' . $this->version;

        $this->client = new Client([
            'base_url' => $this->url,
            'defaults' => [
                'headers'  => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-GGe4-Date' => $this->date
                ]
            ]
        ]);

        $this->initialize();

    }

    private function initialize(){
        $this->request = array(
            "gateway_id" => $this->gateway_id,
            "password" => $this->password
        );
    }

    private function setCreditCardNumber($credit_card_number){
        $this->request = array_merge($this->request, array(
            'cc_number' => $credit_card_number
        ));
    }

    private function setCreditCardExpiry($credit_card_expiry){
        $this->request = array_merge($this->request, array(
            'cc_expiry' => $credit_card_expiry
        ));
    }

    private function setCardholderName($cardholder_name){
        $this->request = array_merge($this->request, array(
            'cardholder_name' => $cardholder_name
        ));
    }

    private function setCreditCardType($credit_card_type){
        $this->request = array_merge($this->request, array(
            'credit_card_type' => $credit_card_type
        ));
    }

    private function setAmount($amount){
        $this->request = array_merge($this->request, array(
            'amount' => $amount
        ));
    }

    private function setTransactionType($type){
        $this->request = array_merge($this->request, array(
            'transaction_type' => $type
        ));
    }

    private function setTransarmorToken($transarmor_token){
        $this->request = array_merge($this->request, array(
            'transarmor_token' => $transarmor_token
        ));
    }

    private function setCVDCode($cvd_code){
        $this->request = array_merge($this->request, array(
            'cvd_presence_ind' => 1,
            'cvd_code' => $cvd_code
        ));
    }

    public function Purchase($credit_card_type, $cardholderName, $credit_card_number, $credit_card_expiry, $cvd_code, $amount){

        $this->setTransactionType("00");
        $this->setCreditCardType($credit_card_type);
        $this->setCardholderName($cardholderName);
        $this->setCreditCardNumber($credit_card_number);
        $this->setCreditCardExpiry($credit_card_expiry);
        $this->setCVDCode($cvd_code);
        $this->setAmount($amount);

        return $this->send();

    }

    public function LatePurchase($credit_card_type, $cardholderName, $transarmor_token, $credit_card_expiry, $cvd_code, $amount){

        $this->setTransactionType("00");
        $this->setCreditCardType($credit_card_type);
        $this->setCardholderName($cardholderName);
        $this->setTransarmorToken($transarmor_token);
        $this->setCreditCardExpiry($credit_card_expiry);
        $this->setCVDCode($cvd_code);
        $this->setAmount($amount);

        return $this->send();

    }

    public function PreAuth($credit_card_type, $cardholderName, $credit_card_number, $credit_card_expiry, $cvd_code){

        $this->setTransactionType("01");
        $this->setCreditCardType($credit_card_type);
        $this->setCardholderName($cardholderName);
        $this->setCreditCardNumber($credit_card_number);
        $this->setCreditCardExpiry($credit_card_expiry);
        $this->setCVDCode($cvd_code);
        $this->setAmount(0);

        return $this->send();

    }

    public function Refund($credit_card_type, $cardholderName, $transarmor_token, $credit_card_expiry, $cvd_code, $amount){

        $this->setTransactionType("04");
        $this->setCreditCardType($credit_card_type);
        $this->setCardholderName($cardholderName);
        $this->setTransarmorToken($transarmor_token);
        $this->setCreditCardExpiry($credit_card_expiry);
        $this->setCVDCode($cvd_code);
        $this->setAmount($amount);

        return $this->send();

    }

    public function setCurrency($currency){

        if(!in_array($currency, array(
            'EUR',
            'GBP',
            'CHF',
            'CZK',
            'DKK',
            'JPY',
            'ZAR',
            'SEK',
            'CAD'
        ))){
            throw new FirstDataException('Invalid Currency Code');
        }

        $this->request = array_merge($this->request, array(
            'currency_code' => $currency
        ));

    }

    private function send(){

        try{
            //all values must be strings and separated by newline character
            //also trim to remove null chars
            $content = array_map('trim', array_map('strval', $this->request));
            $hash_data_parts = ['POST', 'application/json', sha1(json_encode($content)), $this->date, $this->version];
            $hash_data = implode("\n", $hash_data_parts);
            $hmac = base64_encode(hash_hmac('sha1', $hash_data, $this->key, true));

            $response = $this->client->post(null, ['body' => json_encode($content),
                'headers' => [
                    'X-GGe4-Content-SHA1' => sha1(json_encode($content)),
                    'Authorization' => 'GGE4_API ' . $this->key_id . ':' . $hmac
                ]
            ]);

            /**
             * Check bank message
             */
            $bank_message = $response->json()['bank_message'];
            if($bank_message !== 'Approved') {
                throw new FirstDataException($bank_message);
            }

        }catch(ClientException $e){
            throw new FirstDataException($e->getResponse()->getBody());
        }catch(ServerException $e){
            throw new FirstDataException('Error processing request at First Data');
        }

        $this->initialize();

        return $response->json();

    }

}