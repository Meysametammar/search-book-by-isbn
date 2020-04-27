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
        # code...
    }

    public function get_book_detail($_iSBN)
    {
        $search_result = $this->search_in_opac($_iSBN);
    }
}
