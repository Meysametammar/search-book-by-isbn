<?php

namespace BookByISBN;

use GuzzleHttp\Client;

class OpacNlai
{

    private function search_in_opac($_iSBN)
    {
        $address = "http://opac.nlai.ir/opac-prod/search/bibliographicSimpleSearchProcess.do";

        $client = new Client();
        $response = $client->request(
            "GET",
            $address,
            [
                'query' => [
                    "simpleSearch.value" => $_iSBN,
                    "bibliographicLimitQueryBuilder.biblioDocType",
                    "simpleSearch.indexFieldId" => 221091,
                    "nliHolding",
                    "command" => "I",
                    "simpleSearch.tokenized" => true,
                    "classType" => 0,
                    "pageStatus" => 0,
                    "bibliographicLimitQueryBuilder.useDateRange" => null,
                    "bibliographicLimitQueryBuilder.year",
                    "documentType",
                    "attributes.locale" => "fa"
                ]
            ]
        );
        if ($response->getStatusCode() === 200) {

            return $response->getBody()->getContents();
        }
        return false;
    }


    private function get_address_of_first_result($_content)
    {
        preg_match("/command=FULL_VIEW&id=(.*)&pageStatus/", $_content, $BOOKPAGEID);
        $book_page_id = $BOOKPAGEID[1];
        $book_address = "http://opac.nlai.ir/opac-prod/bibliographic/" . $book_page_id;
        return $book_address;
    }

    private function extract_everything_about_book($_page_address)
    {
        $page_content = file_get_contents($_page_address);
        // var_dump($page_content);
        // preg_match_all("/<TR>\n<TD\swidth=20\%\sVALIGN=top\sALIGN=right>(.*)<\/TD>\n<TD\sVALIGN=top\salign=center\swidth=\%5>:<\/TD>\n<TD\sVALIGN=top\salign=right(.*)\n<\/TR>/", $page_content, $ALLOFROWS);
        preg_match("/<TR>(.*)<\/TR>/s", $page_content, $ALLOFROWS);
        preg_match_all("/<TD width=20% VALIGN=top ALIGN=right>(.*)<\/TD>/", $ALLOFROWS[0], $SUBJECTS);
        preg_match_all("/<TD VALIGN=top align=right width=75%>(.*)<\/TD>/", $ALLOFROWS[0], $CONTENTS);
        $arr = [];
        for ($i = 0; $i < sizeof($SUBJECTS[1]); $i++) {
            // echo html_entity_decode($SUBJECTS[1][$i], ENT_COMPAT, "UTF-8");
            echo preg_replace_callback('/&#([0-9a-fx]+);/mi', [$this, 'replace_num_entity'], $SUBJECTS[1][$i]);
            echo "\n";
        }
        // var_dump($CONTENTS);
    }

    public function get_book_detail($_iSBN)
    {
        $search_result_content = $this->search_in_opac($_iSBN);
        $first_search_address = $this->get_address_of_first_result($search_result_content);
        $book_detail = $this->extract_everything_about_book($first_search_address);
        return $book_detail;
    }
}
