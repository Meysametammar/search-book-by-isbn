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
        if (!isset($BOOKPAGEID[1])) {
            return false;
        }
        $book_page_id = $BOOKPAGEID[1];
        $book_address = "http://opac.nlai.ir/opac-prod/bibliographic/" . $book_page_id;
        return $book_address;
    }

    private function extract_everything_about_book($_page_address)
    {
        if (!is_string($_page_address)) {
            return false;
        }
        $page_content = file_get_contents($_page_address);
        preg_match("/<TR>(.*)<\/TR>/s", $page_content, $ALLOFROWS);
        if (!isset($ALLOFROWS[0])) {
            return false;
        }
        preg_match_all("/<TD width=20% VALIGN=top ALIGN=right>(.*)<\/TD>/", $ALLOFROWS[0], $SUBJECTS);
        preg_match_all("/<TD VALIGN=top align=right width=75%>(.*)<\/TD>/", $ALLOFROWS[0], $CONTENTS);
        $arr = [];
        if (!isset($SUBJECTS[1]) || !isset($CONTENTS[1])) {
            return false;
        }
        for ($i = 0; $i < sizeof($SUBJECTS[1]); $i++) {
            $tmp_arr = [
                "title" => html_entity_decode($SUBJECTS[1][$i]),
                "content" => html_entity_decode($CONTENTS[1][$i])
            ];
            array_push($arr, $tmp_arr);
        }
        return $arr;
    }

    public function get_book_detail($_iSBN)
    {
        $search_result_content = $this->search_in_opac($_iSBN);
        $first_search_address = $this->get_address_of_first_result($search_result_content);
        $book_detail = $this->extract_everything_about_book($first_search_address);
        return $book_detail;
    }
}
