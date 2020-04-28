<?php

namespace spec\BookByISBN;

use BookByISBN\OpacNlai;
use PhpSpec\ObjectBehavior;

class OpacNlaiSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OpacNlai::class);
    }

    function it_can_get_book_detail_by_ISBN()
    {
        $this->get_book_detail("964-2793-03-2")->shouldBeArray();
    }
}
