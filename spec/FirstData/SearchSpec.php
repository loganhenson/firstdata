<?php

namespace spec\FirstData;

use FirstData\Search;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin Search */
class SearchSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            getenv('fd_username'),
            getenv('fd_password'),
            true
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FirstData\Search');
    }

    function it_can_get_array_of_transactions_as_associative_array()
    {
        $this->getTransactions()->shouldBeArray();
    }
}
