<?php namespace FirstData;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use League\Csv\Reader;
use SplFileObject;

class Search{

    private $url;
    private $request;
    private $username;
    private $password;
    private $client;

    function __construct($username, $password, $test_mode = false){

        $this->username = $username;
        $this->password = $password;

        if(!$test_mode)
            $this->url = 'https://api.globalgatewaye4.firstdata.com/transaction/search';
        else
            $this->url = 'https://api.demo.globalgatewaye4.firstdata.com/transaction/search';

        $this->client = new Client([
            'base_url' => $this->url,
            'defaults' => [
                'auth'  => [$username, $password]
            ]
        ]);

    }

    public function getTransactions(){

        return $this->send();

    }

    private function send(){

        try{
            $response = $this->client->get(null, ['body' => $this->request]);
        }catch(ClientException $e){
            throw new FirstDataException($e->getResponse()->getBody());
        }

        $inputCsv = Reader::createFromString($response->getBody());
        $inputCsv->setDelimiter(',');
        $inputCsv->setEncodingFrom('ISO-8859-15');
        $inputCsv->setFlags(SplFileObject::DROP_NEW_LINE|SplFileObject::READ_AHEAD|SplFileObject::SKIP_EMPTY);

        return $inputCsv->fetchAssoc();

    }

}