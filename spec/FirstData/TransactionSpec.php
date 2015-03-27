<?php

namespace spec\FirstData;

use FirstData\Transaction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin Transaction */
class TransactionSpec extends ObjectBehavior
{

    function it_is_initializable(){
        $this->beConstructedWith(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $this->shouldHaveType('FirstData\Transaction');
    }

    function it_can_purchase(){
        $this->beConstructedWith(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $this->Purchase('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120)['bank_resp_code']->shouldBeLike("100");
    }

    function it_can_purchase_and_refund(){
        $Transaction = new Transaction(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $transarmor_token = $Transaction->Purchase('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120)['transarmor_token'];

        $this->beConstructedWith(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $this->Refund('Mastercard', 'Logan Henson', $transarmor_token, '1216', 120)['bank_resp_code']->shouldBeLike("100");
    }

    function it_should_throw_informative_error_exception(){
        $this->beConstructedWith(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $this->shouldThrow('\FirstData\FirstDataException')->during('Refund', ['Mastercard', 'Logan Henson', '', '1216', 120]);
    }

    function it_can_pre_authorize(){
        $this->beConstructedWith(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $this->PreAuth('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120)['transarmor_token']->shouldBeString();
    }

    function it_can_late_purchase(){
        $Transaction = new Transaction(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $transarmor_token = $Transaction->PreAuth('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120)['transarmor_token'];

        $this->beConstructedWith(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $this->LatePurchase('Mastercard', 'Logan Henson', $transarmor_token, '1216', 120)['bank_resp_code']->shouldBeLike("100");
    }

    function it_can_change_currency(){
        $this->beConstructedWith(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $this->setCurrency('CAD');
        $this->Purchase('Mastercard', 'Logan Henson', 5500000000000004, '1216', 200)['currency_code']->shouldBeLike('CAD');
    }

    function it_throws_invalid_currency_code(){
        $this->beConstructedWith(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $this->shouldThrow('\FirstData\FirstDataException')->during('setCurrency', ["EURO"]);
    }

    function it_throws_on_invalid_credit_card_number(){
        $this->beConstructedWith(getenv('fd_gateway_id'), getenv('fd_gateway_password'), getenv('fd_key_id'), getenv('fd_key'), true);
        $this->shouldThrow('\FirstData\FirstDataException')->during('Purchase', ['Mastercard', 'Logan Henson', 5500000000400004, '1216', 120]);
    }

}