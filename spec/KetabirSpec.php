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

    public function it_can_get_book_picture_by_isbn()
    {
        $this->get_book_picture_by_isbn("9789643113445")->shouldContain("http://164.138.18.205/DataBase/BookImages");
        $this->get_book_picture_by_isbn("9789646235793")->shouldContain("http://164.138.18.205/DataBase/BookImages");
        $this->get_book_picture_by_isbn("9786002571755")->shouldContain("http://164.138.18.205/DataBase/BookImages");
    }
}
