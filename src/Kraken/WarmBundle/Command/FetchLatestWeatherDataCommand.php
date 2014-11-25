<?php
namespace Kraken\WarmBundle\Command;

use Goutte\Client;
use Kraken\WarmBundle\Entity\City;
use Kraken\WarmBundle\Entity\Temperature;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchLatestWeatherDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kraken:weather:fetch_latest')
            ->setDescription('Fetches weather data for each city for previous winter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $cities = array();
        $citiesFile = dirname(__DIR__) . '/DataFixtures/cities.json';
        if (file_exists($citiesFile)) {
            $cities = json_decode(file_get_contents($citiesFile), true);
        }

        $rawFreemeteoCities = file_get_contents(dirname(__DIR__) . '/DataFixtures/freemeteo_cities.txt');
        $freemeteoCities = explode(PHP_EOL, $rawFreemeteoCities);

        $client = new Client();
        $client->setHeader('User-Agent', 'Opera/9.80 (X11; Linux x86_64) Presto/2.12.388 Version/12.15');

        $matchedCities = array();
        foreach ($freemeteoCities as $city) {
            $cityStuff = explode(':', $city);

            foreach ($cities as $id => $details) {
                if (isset($details['name']) && isset($cityStuff[1]) && $details['name'] == $cityStuff[1]) {
                    $matchedCities[$cityStuff[1]] = $cityStuff[0];

                    $cityEntity = $em->getRepository('KrakenWarmBundle:City')->findOneBy(array('name' => $details['name']));

                    if (!$cityEntity) {
                        $cityEntity = new City();
                        $cityEntity->setName($details['name']);
                        $cityEntity->setLatitude($details['lat']);
                        $cityEntity->setLongitude($details['lon']);
                        $em->persist($cityEntity);
                        $em->flush();
                    }

                    break;
                }
            }
        }

        $months = array(
            array(9, 2013),
            array(10, 2013),
            array(11, 2013),
            array(12, 2013),
            array(1, 2014),
            array(2, 2014),
            array(3, 2014),
            array(4, 2014)
        );

        foreach ($matchedCities as $cityName => $cityId) {
            $cityEntity = $em->getRepository('KrakenWarmBundle:City')->findOneBy(array('name' => $cityName));

            if (!$cityEntity) {
                throw new \RuntimeException(sprintf('Nie ma takiego miasta %s', $cityName));
            }

            $temperatureEntities = $em->createQueryBuilder('t')
                ->select('count(t.id)')
                ->from('KrakenWarmBundle:Temperature', 't')
                ->where('t.city = :city')
                ->setParameter('city', $cityEntity)
                ->getQuery()
                ->getSingleScalarResult();

            if ($temperatureEntities >= 28 * count($months)) {
                echo $cityName. ' ma już wszystko'.PHP_EOL;
                continue;
            } else {
                echo $cityName. ' ma tylko '.$temperatureEntities.PHP_EOL;
            }

            sleep(rand(5,15));
            $landPointInfoResponse = $client->getClient()->get(sprintf('http://freemeteo.pl/Services/GeoLocation/LandPointInfo?ID=%s', $cityId));
            $landPointInfo = $landPointInfoResponse->json();

            foreach ($landPointInfo[0]['Stations'] as $station) {
                $data = array();
                foreach ($months as $m => $month) {
                    $rowsThisMonth = 0;
                    $temperatureEntity = $em->getRepository('KrakenWarmBundle:Temperature')->findOneBy(array('city' => $cityEntity, 'month' => $month[0]));
                    
                    if ($temperatureEntity) {
                        echo $cityEntity->getName().' już ma miesiąc '.$month[0].PHP_EOL;
                        continue;
                    }

                    sleep(rand(5, 15));
                    $dataUrl = sprintf(
                        'http://freemeteo.pl/pogoda/%s/historia/miesieczna-historia/?gid=%s&station=%s&month=%s&year=%s&language=polish&country=poland', 
                        $landPointInfo[0]['FriendlyUrl'],
                        $cityId,
                        $station['StationID'],
                        $month[0],
                        $month[1]
                    );
                    echo $dataUrl.PHP_EOL;

                    $crawler = $client->request('GET', $dataUrl);

                    $row = array();
                    $crawler->filter('.monthly-history td')->each(function ($node, $i) use (&$row, &$data, &$rowsThisMonth) {
                        //echo $i.': '.$node->text().PHP_EOL;

                        if ($i % 10 >= 0 && $i % 10 < 2) {
                            $row[] = $node->text(); 
                        }

                        if ($i % 10 == 2) {
                            $data[$row[0]] = ((double)$row[1] + (double)$node->text()) / 2;
                            $rowsThisMonth++;
                            $row = array();
                        }
                    });

                    if ($rowsThisMonth < 25) {
                        echo 'tylko '.$rowsThisMonth.' pomiarów'.PHP_EOL;
                        continue(2); // not enough data, try another station
                    }

                    foreach ($data as $date => $temperature) {
                        $day = explode('-', $date);
                        $t = new Temperature();
                        $t->setType('yearly');
                        $t->setCity($cityEntity);
                        $t->setDay($day[2]);
                        $t->setMonth($day[1]);
                        $t->setValue($temperature);

                        $em->persist($t);
                    }

                    $em->flush();
                }
            }
        }
    }
}
