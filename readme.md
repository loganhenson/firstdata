image:: https://travis-ci.org/loganhenson/firstdata.svg?branch=1.0.0
    :alt: Build status
    :target: https://travis-ci.org/loganhenson/firstdata

## loganhenson/firstdata

## Install

```
composer require loganhenson/firstdata
```

[Transaction API Reference](https://firstdata.zendesk.com/entries/407571-First-Data-Global-Gateway-e4-Web-Service-API-Reference-Guide#4)

[Search API Reference](https://firstdata.zendesk.com/entries/407573-First-Data-Global-Gateway-e4-Web-Service-Transaction-Search-and-Reporting-API)

[Transaction List](https://globalgatewaye4.firstdata.com/transactions/list)

[Demo Transaction List](https://demo.globalgatewaye4.firstdata.com/transactions/list)

> Requires .env file (see .env.example)

## Purchase

```php
<?php

$Transaction = new Transaction(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'));
try{
	$response = $Transaction->Purchase('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120);
}catch(FirstDataException $e){
	echo $e->getMessage();
}

```

## Late Purchase

```php
<?php

$Transaction = new Transaction(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'));
try{
	$response = $Transaction->PreAuth('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120);
	$latePurchaseResponse = $Transaction->LatePurchase('Mastercard', 'Logan Henson', $response['transarmor_token'], '1216', 120)
}catch(FirstDataException $e){
	echo $e->getMessage();
}

```

## Refund

```php
<?php

$Transaction = new Transaction(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'));
try{
	$response = $Transaction->Refund('Mastercard', 'Logan Henson', $transarmor_token, '1216', 120);
}catch(FirstDataException $e){
	echo $e->getMessage();
}

```

## Changing Currency Type

> Can be used before _Purchase_ and _LatePurchase_

> 'EUR', 'GBP', 'CHF', 'CZK', 'DKK', 'JPY', 'ZAR', 'SEK', 'CAD'

```php
<?php

$Transaction = new Transaction(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'));
try{
    $Transaction->setCurrency('CAD');
	$response = $Transaction->Purchase('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120);
}catch(FirstDataException $e){
	echo $e->getMessage();
}

```

## Get All Transactions

```php

$Search = new Search(getenv('fd_username'), getenv('fd_password'));
try{
	$transactions = $Search->getTransactions();
}catch(FirstDataException $e){
	echo $e->getMessage();
}

```

## Test Data

>Visa	4111111111111111	
 Expiry Date: Any future date.

>Mastercard	5500000000000004	
 Expiry Date: Any future date.

>American Express	340000000000009
 Note: Amex is 15 characters	
 Expiry Date: Any future date.

>JCB	3566002020140006	
 Expiry Date: Any future date.

>Discover	6011000000000004	
 Expiry Date: Any future date.

>Diners Club	36438999960016	
 Expiry Date: Any future date.