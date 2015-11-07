<?php

    class Scrape {
        
        /** @var User Agent para enganar os servidores se passando pelo facebook scraping */
        const USER_AGENT = 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)';

        static function getContent($Url) {
            $parse = parse_url($Url);
            if (!empty($parse['host'])) {
                
                $options = [
                    CURLOPT_RETURNTRANSFER => true, // return web page
                    CURLOPT_HEADER => false, // don't return headers
                    CURLOPT_FOLLOWLOCATION => true, // follow redirects
                    CURLOPT_ENCODING => "", // handle all encodings
                    CURLOPT_USERAGENT => self::USER_AGENT, // who am i
                    CURLOPT_AUTOREFERER => false, // set referer on redirect
                    CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
                    CURLOPT_TIMEOUT => 120, // timeout on response
                    CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_BINARYTRANSFER => true,
                    CURLOPT_HTTPHEADER => [ 'Host: ' . $parse['host']],
                    CURLOPT_URL => $Url,
                    CURLOPT_REFERER => $Url,
                ];

                $ch = curl_init();

                curl_setopt_array($ch, $options);

                $content = curl_exec($ch);
                $err = curl_errno($ch);
                $errmsg = curl_error($ch);
                $header = curl_getinfo($ch);

                curl_close($ch);

                $header['errno'] = $err;
                $header['errmsg'] = $errmsg;
                $header['content'] = preg_replace(['/<script.*?<\/script>/si', '/<style.*?<\/style>/si'], null, $content);

                return $header;
            } else {
                return null;
            }
        }

    }
    