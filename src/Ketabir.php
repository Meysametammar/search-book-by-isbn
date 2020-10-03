<?php

namespace BookByISBN;

use GuzzleHttp\Client;

class Ketabir
{
    /**
     * @var undefined
     */
    public $book_picture;
    /**
     * @var undefined
     */
    public $book_address;
    /**
     * @var undefined
     */
    public $book_detail;

    /**
     * @param mixed $_ISBN
     *
     * @return void
     */
    public function __construct($_ISBN)
    {
        $this->make_everything_ready($_ISBN);
    }

    /**
     * @param mixed $_ISBN
     *
     * @return void
     */
    private function make_my_dirty_header($_ISBN)
    {
        $search_page = new Client([
            'base_uri' => 'http://ketab.ir/Search.aspx',
        ]);
        $response = $search_page->request('GET');
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $content = $response->getBody()->getContents();
        // $content = file_get_contents("http://ketab.ir/Search.aspx", false);
        preg_match("/id=\"__VIEWSTATE\" value=\"(.*)\" \/>/", $content, $VIEWSTATE);
        if (!isset($VIEWSTATE[1])) {
            return false;
        }
        $view_state = $VIEWSTATE[1];
        preg_match(
            "/id=\"__VIEWSTATEGENERATOR\" value=\"(.*)\" \/>/",
            $content,
            $VIEWSTATEGENERATOR
        );
        if (!isset($VIEWSTATEGENERATOR[1])) {
            return false;
        }
        $view_state_generator = $VIEWSTATEGENERATOR[1];
        preg_match("/id=\"__EVENTVALIDATION\" value=\"(.*)\" \/>/", $content, $EVENTVALIDATION);
        if (!isset($EVENTVALIDATION[1])) {
            return false;
        }
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

    /**
     * @param mixed $_ISBN
     *
     * @return void
     */
    private function make_everything_ready($_ISBN)
    {
        $body = $this->make_my_dirty_header($_ISBN);
        $http_content = http_build_query($body);
        $client = new Client(['cookies' => true]);
        $response = $client->request('POST', 'http://ketab.ir/Search.aspx', [
            'form_params' => $body,
            'allow_redirects' => true,
            'headers' => [
                "content-type: application/x-www-form-urlencoded",
                "Content-Length: " . strlen($http_content),
            ],
        ]);
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $find_page_content = $response->getBody()->getContents();
        $this->give_me_picture($find_page_content);
        $this->give_book_detail($this->book_address);
    }

    /**
     * @param mixed $_page_url
     *
     * @return void
     */
    private function give_book_detail($_page_url)
    {
        $book_detail = [];
        $client = new Client([
            'cookies' => true,
        ]);
        $response = $client->get($_page_url, ['verify' => true]);
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $content = $response->getBody()->getContents();
        // var_dump($content);
        preg_match("/<span id=\"(.*)\" class=\"h4\">(.*)<\/span>/", $content, $TITLE);
        $book_detail["title"] = isset($TITLE[2]) ? $TITLE[2] : null;

        preg_match("/<span id=\"(.*)\" class=\"h4\">(.*)<\/span>/", $content, $TITLE);
        $book_detail["title"] = isset($TITLE[2]) ? $TITLE[2] : null;

        $this->book_detail = $book_detail;
        // var_dump($book_detail);
    }

    /**
     * @param mixed $_page_content
     * @param mixed $book_id
     *
     * @return void
     */
    private function give_me_picture($_page_content)
    {
        preg_match(
            "/href=\"\/bookview.aspx\?bookid=(.*)\"><img id=\"(.*)\" src=\"(.*)\" height=\"100\"/",
            $_page_content,
            $DATAPERRESULT
        );
        if (!isset($DATAPERRESULT[3])) {
            return false;
        }

        $this->book_address = "http://ketab.ir/bookview.aspx?bookid=" . $DATAPERRESULT[1];
        $this->book_picture = $DATAPERRESULT[3];
        return true;
    }
}
