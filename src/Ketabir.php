<?php

namespace BookByISBN;

use GuzzleHttp\Client;

class Ketabir
{


    private function make_my_dirty_header($_ISBN)
    {
        $content = file_get_contents("http://ketab.ir/Search.aspx", false);
        preg_match("/id=\"__VIEWSTATE\" value=\"(.*)\" \/>/", $content, $VIEWSTATE);
        $view_state = $VIEWSTATE[1];
        preg_match("/id=\"__VIEWSTATEGENERATOR\" value=\"(.*)\" \/>/", $content, $VIEWSTATEGENERATOR);
        $view_state_generator = $VIEWSTATEGENERATOR[1];
        preg_match("/id=\"__EVENTVALIDATION\" value=\"(.*)\" \/>/", $content, $EVENTVALIDATION);
        $event_validation = $EVENTVALIDATION[1];

        $body = [
            "__VIEWSTATE" => $view_state,
            "__VIEWSTATEGENERATOR" => $view_state_generator,
            "__EVENTVALIDATION" => $event_validation,
            "ctl00\$SiteMenu\$Search\$DropDownFieldList" => "1",
            "ctl00\$SiteMenu\$Search\$DDLTypeSearch" => "1",
            "ctl00\$ContentPlaceHolder1\$TxtIsbn" => $_ISBN,
            "ctl00\$ContentPlaceHolder1\$drpDewey" => "-1",
            "ctl00\$ContentPlaceHolder1\$drpFromIssueYear" => "57",
            "ctl00\$ContentPlaceHolder1\$drpFromIssueMonth" => "01",
            "ctl00\$ContentPlaceHolder1\$drpFromIssueDay" => "01",
            "ctl00\$ContentPlaceHolder1\$drpToIssueYear" => "98",
            "ctl00\$ContentPlaceHolder1\$drpToIssueMonth" => "12",
            "ctl00\$ContentPlaceHolder1\$drpToIssueDay" => "30",
            "ctl00\$ContentPlaceHolder1\$drLanguage" => "0",
            "ctl00\$ContentPlaceHolder1\$DrSort" => "1",
            "ctl00\$ContentPlaceHolder1\$DrPageSize" => "100",
            "ctl00\$ContentPlaceHolder1\$BtnSearch" => "",
        ];
        return $body;
    }

    public function get_book_picture_by_isbn($_ISBN)
    {
        $body = $this->make_my_dirty_header($_ISBN);
        $http_content = http_build_query($body);
        $client = new Client(['cookies' => true]);
        $response = $client->request(
            'POST',
            'http://ketab.ir/Search.aspx',
            [
                'form_params' => $body,
                'allow_redirects' => true,
                'headers' => [
                    "content-type: application/x-www-form-urlencoded",
                    "Content-Length: " . strlen($http_content)
                ]
            ]
        );
        if ($response->getStatusCode() === 200) {
            $find_page_content = $response->getBody()->getContents();
            return $this->give_me_picture($find_page_content);
        }
        return false;
    }

    private function give_me_picture($_page_content)
    {
        preg_match("/href=\"\/bookview.aspx\?bookid=(.*)\"><img id=\"(.*)\" src=\"(.*)\" height=\"100\"/", $_page_content, $DATAPERRESULT);
        if (!isset($DATAPERRESULT[3]))
            return false;
        var_dump($DATAPERRESULT[3]);
        return $DATAPERRESULT[3];
    }
}
