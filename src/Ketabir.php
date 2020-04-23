<?php

namespace BookByISBN;

class Ketabir
{
    public function get_book_address_by_isbn($argument1)
    {
        $opts = array(
            'http' => array(
                'method' => "POST",
                'header' => "content-type: application/x-www-form-urlencoded"
            )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $file = file_get_contents('http://ketab.ir/Search.aspx', false, $context);
    }
}
