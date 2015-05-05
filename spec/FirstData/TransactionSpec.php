<?php

namespace spec\FirstData;

use FirstData\Transaction;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin Transaction */
class TransactionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            getenv('fd_gateway_id'),
            getenv('fd_gateway_password'),
            getenv('fd_key_id'),
            getenv('fd_key'),
            true
        );
    }

    function it_is_initializable(){
        $this->shouldHaveType('FirstData\Transaction');
    }

    function it_can_purchase()
    {
        $this->Purchase('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120)['bank_resp_code']->shouldBeLike("100");
    }

    function it_can_purchase_and_refund()
    {
        $transarmor_token = $this->Purchase('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120)['transarmor_token'];
        $this->Refund('Mastercard', 'Logan Henson', $transarmor_token, '1216', 120)['bank_resp_code']->shouldBeLike("100");
    }

    function it_should_throw_informative_error_exception()
    {
        $this->shouldThrow('\FirstData\FirstDataException')->during('Refund', ['Mastercard', 'Logan Henson', '', '1216', 120]);
    }

    function it_can_pre_authorize()
    {
        $this->PreAuth('Mastercard', 'Logan Henson', 5500000000000004, '1216', 120)['transarmor_token']->shouldBeString();
    }

    function it_can_late_purchase()
    {
        $transarmor_token = $this->PreAuth('American Express', 'Nathan R Mickler', '340000000000009', '1219', '0')['transarmor_token'];
        $this->LatePurchase('American Express', 'Logan Henson', $transarmor_token, '1216', 120)['bank_resp_code']->shouldBeLike("100");
    }

    function it_can_change_currency()
    {
        $this->setCurrency('CAD');
        $this->Purchase('Mastercard', 'Logan Henson', 5500000000000004, '1216', 200)['currency_code']->shouldBeLike('CAD');
    }

    function it_throws_invalid_currency_code()
    {
        $this->shouldThrow('\FirstData\FirstDataException')->during('setCurrency', ["EURO"]);
    }

    function it_can_handle_numbers_instead_of_strings()
    {
        $this->purchase('Mastercard', 'Logan Henson', 5500000000000004, 1216, 200)['bank_resp_code']->shouldBeLike("100");
    }

    function it_can_handle_null_byte_strings()
    {
        $this->purchase('Mastercard', 'Logan Henson', chr(0). '5500000000000004' . chr(0), 1216, 200)['bank_resp_code']->shouldBeLike("100");
    }
}