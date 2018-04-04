<?php

namespace SafeBox\Infrastructure\Repository\SafeBox;


use SafeBox\Application\Service\SafeBox\CommonPasswordRepositoryInterface;

class WebCommonPasswordRepository implements CommonPasswordRepositoryInterface
{

    private $url = 'https://github.com/danielmiessler/SecLists/raw/master/Passwords/Common-Credentials/10-million-password-list-top-10000.txt';

    function all(): array
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $this->url);

        $file = $response->getBody()->getContents();

        $passwords = explode("\n", $file);

        $clean = array_values(array_diff($passwords, ["null", "", null]));

        return $clean;
    }
}