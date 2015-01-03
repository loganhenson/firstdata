<?php

namespace spec\FirstData;

use FirstData\Search;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin Search */
class SearchSpec extends ObjectBehavior
{
    function it_is_initializable(){
        $this->beConstructedWith(getenv('fd_username'), getenv('fd_password'), true);
        $this->shouldHaveType('FirstData\Search');
    }

    function it_can_get_array_of_transactions_as_associative_array(){
        $this->beConstructedWith(getenv('fd_username'), getenv('fd_password'), true);
        $this->getTransactions()->shouldBeArray();
    }

    function it_can_get_transactions_accurately(){
        $this->beConstructedWith(getenv('fd_username'), getenv('fd_password'), true);
        $this->getTransactions()[0]['Cardholder Name']->shouldBeLike('Logan Henson');
    }

    function it_should_throw_informative_error_exception(){
        $this->beConstructedWith("invalid", "invalid", true);
        $this->shouldThrow('\FirstData\FirstDataException')->during('getTransactions');
    }
}
