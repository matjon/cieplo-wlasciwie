<?php

namespace Kraken\WarmBundle\Calculator;

use Goutte\Client;

class WeatherDataFetcher
{
    public function fetchCityData($code)
    {
        $client = new Client();
        $client->setHeader('User-Agent', 'Opera/9.80 (X11; Linux x86_64) Presto/2.12.388 Version/12.15');
        $data = array();

        $url ='http://weather.sg.msn.com/sitemap.aspx?wealocations=Poland';
        $crawler = $client->request('GET', $url);

        $nodeValues = $crawler->filter('.linklist1 li a')->each(function ($node, $i) use (&$data, &$client, $code) {
echo $node->nodeValue.PHP_EOL;
            $regionUrl = 'http://weather.sg.msn.com/'.$node->getAttribute('href');
            $crawler = $client->request('GET', $regionUrl);

            $nodeValues = $crawler->filter('.t3 .td2 a')->each(function ($node, $i){
echo $node->nodeValue.PHP_EOL;
$stuff = explode('wc:', $node->getAttribute('href'));
$substuff = explode('&q', $stuff[1]);
$cityId = $substuff[0];

$cityUrl = 'http://weather.sg.msn.com/daily_averages.aspx?wealocations=wc:'.$cityId;

die(print_r($substuff[0]));
            });

            $crap = substr($node->nodeValue, 0, stripos($node->nodeValue, 'if ('));
            $wtf = explode('for ', $crap);
            $dafq = explode(',', $wtf[1]);
            $cityName = $dafq[0];

            if (!$cityName) {
                throw new \Exception(sprintf('City name on weather.com page not found: %s', $crap));
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($cityName).',+Poland&sensor=false',
                CURLOPT_USERAGENT => 'Kraken Invader'
            ));

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if (!isset($response['results'][0])) {
                  throw new \Exception('Skipped, as this does not look like a city to Google: '.$cityName);
            }

            if ($response['results'][0]['address_components'][0]['types'][0] != 'locality') {
                  throw new \Exception('Skipped, as this is not a city: '.$cityName);
            }

            $location = $response['results'][0]['geometry']['location'];
            if ($location) {

                $name = isset($response['results'][0]['address_components'][1]['long_name'])
                    ? $response['results'][0]['address_components'][1]['long_name']
                    : $response['results'][0]['address_components'][0]['long_name'];

                if (!$name) {
                    throw new \Exception('Cannot find city name in Google response');
                }

                $data = array(
                    'name' => $name,
                    'lat' => $location['lat'],
                    'lon' => $location['lng'],
                );
            } else {
                throw new \Exception('Cannot find location in Google response');
            }
        });

        if (empty($data)) {
            throw new \Exception('City page seems to be anihilated');
        }

        return $data;
    }

    public function fetchAverageTemperatures($code)
    {
        $client = new Client();
        $data = array();

        for ($month = 1; $month <= 12; $month++) {
            $crawler = $client->request('GET', 'http://www.weather.com/weather/wxclimatology/daily/'.$code.'?climoMonth='.$month);

            $nodeValues = $crawler->filter('table.Basic table tr td.lapAvgDataRow')->each(function ($node, $i) use (&$data, $month) {
                if ($i % 8 == 5) {
                    if ($node->nodeValue == 'N/A') {
                        throw new \Exception('Incomplete weather data');
                    }
                    $data[$month][((int) ($i/8)+1)] = $this->toCelsius((int) $node->nodeValue);
                }
            });
        }

        return $data;
    }

    protected function toCelsius($fahrenheit)
    {
        return 5/9*($fahrenheit-32);
    }
}
