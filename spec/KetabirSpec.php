<?php

namespace spec\BookByISBN;

use BookByISBN\Ketabir;
use PhpSpec\ObjectBehavior;

class KetabirSpec extends ObjectBehavior
{

    public function it_can_get_book_picture_by_isbn()
    {
        $ketabir = new Ketabir("964-2793-03-2");
        var_dump($ketabir->book_picture);
        var_dump($ketabir->book_address);
        var_dump($ketabir->book_detail);
        // $this->get_book_picture_by_isbn("964-2793-03-2")->shouldContain("http://164.138.18.205/DataBase/BookImages");
        // $this->get_book_picture_by_isbn("9789646235793")->shouldContain("http://164.138.18.205/DataBase/BookImages");
        // $this->get_book_picture_by_isbn("9786002571755")->shouldContain("http://164.138.18.205/DataBase/BookImages");
    }
}
