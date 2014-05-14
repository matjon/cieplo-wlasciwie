<?php
namespace Kraken\WarmBundle\Command;

use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchWeatherDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kraken:weather:fetch')
            ->setDescription('Fetches list of cities and weather data for each city')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cities = array();
        $citiesFile = dirname(__DIR__) . '/DataFixtures/cities.json';
        if (file_exists($citiesFile)) {
            $cities = json_decode(file_get_contents($citiesFile), true);
        }

        $client = new Client();
        $client->setHeader('User-Agent', 'Opera/9.80 (X11; Linux x86_64) Presto/2.12.388 Version/12.15');
        $data = array();

        $url ='http://weather.sg.msn.com/sitemap.aspx?wealocations=Poland';
        $crawler = $client->request('GET', $url);

        $that = $this;

        $nodeValues = $crawler->filter('.linklist1 li a')->each(function ($node, $i) use (&$that, &$data, &$client, &$output, &$cities) {
            echo $node->nodeValue.PHP_EOL;
            $regionUrl = 'http://weather.sg.msn.com/'.$node->getAttribute('href');
            $crawler = $client->request('GET', $regionUrl);

            $nodeValues = $crawler->filter('.t3 .td2 a')->each(function ($node, $i) use (&$that, &$output, &$cities) {
                echo "miasto: ".$node->nodeValue.PHP_EOL;

                $parts = explode(',', $node->nodeValue);
                $cityName = trim($parts[0]);

                $stuff = explode('wc:', $node->getAttribute('href'));
                $substuff = explode('&q', $stuff[1]);
                $cityId = $substuff[0];

                try {
                    $cityData = $that->fetchCityData($cityName);

                    $output->writeln(sprintf(
                        '<info>weather:fetch</info> fetching city data - %s: %s',
                        $cityId, $cityData['name']
                    ));

                    try {
                        $output->writeln(sprintf(
                            '<info>weather:fetch</info> fetching weather data: %s',
                            $cityId
                        ));

                        $temperatures = $that->fetchAverageTemperatures($cityId);
                        file_put_contents(dirname(__DIR__) . '/DataFixtures/weather/'.$cityId.'.json', json_encode($temperatures));
                    } catch (\Exception $e) {
                        $cityData['error'] = 'incomplete weather data';
                        $output->writeln(sprintf(
                            '<error>weather:fetch</error> Error fetching weather! <comment>%s</comment>',
                            $e->getMessage()
                        ));
                    }

                } catch (\Exception $e) {
                    $output->writeln(sprintf(
                        '<error>weather:fetch</error> %s Oh crap! <comment>%s</comment>',
                        $cityId, $e->getMessage()
                    ));
                    $cityData['error'] = 'crap';
                }
                $cities[$cityId] = $cityData;

            });

        });

        file_put_contents(dirname(__DIR__) . '/DataFixtures/cities.json', json_encode($cities));
    }

    protected function fetchCityData($cityName)
    {
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

            $name = str_replace('Gmina ', '', $name);

            $data = array(
                'name' => $name,
                'lat' => $location['lat'],
                'lon' => $location['lng'],
            );
        } else {
            throw new \Exception('Cannot find location in Google response');
        }

        return $data;
    }

    protected function fetchAverageTemperatures($cityId)
    {
        $cityUrl = 'http://weather.sg.msn.com/daily_averages.aspx?wealocations=wc:'.$cityId;

        $client = new Client();
        $client->setHeader('User-Agent', 'Opera/9.80 (X11; Linux x86_64) Presto/2.12.388 Version/12.15');
        $data = array();
        $min = 0;

        //TODO check if there's any data, not only N/A's
        // cities without data: Rybnik, Sanok, Kwidzyn, Elbląg

        for ($month = 1; $month <= 12; $month++) {
            $crawler = $client->request('GET', $cityUrl.'&weai='.$month);

            $nodeValues = $crawler->filter('.t3 tr td')->each(function ($node, $i) use (&$data, $month, &$min) {
                if ($i % 5 == 1) {
                    $stuff = explode('low: ', $node->nodeValue);
                    if (isset($stuff[1])) {
                        $avgMin = (int) $stuff[1];
                        $min = $avgMin;
                    }
                }

                if ($i % 5 == 2) {
                    $stuff = explode('hi: ', $node->nodeValue);
                    if (isset($stuff[1]) && !stristr($stuff[1], 'NA')) {
                        $avgHi = (int) $stuff[1];
                        if ($min !== 'NA' && $avgHi !== 'NA') {
                            $data[$month][((int) ($i/5)+1)] = ($min + $avgHi)/2;
                        } else {
                            $data[$month][((int) ($i/5)+1)] = (int) ($avgHi*0.75);
                        }
                        $min = 0;
                    } else {
                        $data[$month][((int) ($i/5)+1)] = $min + abs((int) ($min/2));
                    }
                }
            });
        }

        return $data;
    }
}
