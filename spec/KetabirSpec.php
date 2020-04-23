<?php

namespace spec\BookByISBN;

use BookByISBN\Ketabir;
use PhpSpec\ObjectBehavior;

class KetabirSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Ketabir::class);
    }

    public function it_can_get_book_address_by_isbn()
    {
        $this->get_book_address_by_isbn("9789643113445")->shouldBe("http://ketab.ir/bookview.aspx?bookid=2453934");
    }
}
