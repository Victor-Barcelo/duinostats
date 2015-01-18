<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Light;
use AppBundle\Entity\Temperature;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Ob\HighchartsBundle\Highcharts\Highchart;

class MainController extends Controller
{
    private $apiKey = "qwerty";
    /**
     * @Route("/", name="main")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Main:plots.html.twig', array('tempChart' => $this->getTempChart(), 'lightChart' => $this->getLightChart()));
    }

    /**
     * @Route("/gettemps", name="getTemps")
     */
    public function getTemperaturesAction()
    {
        $tempsArray = $this->getTemperaturesArray();
        $return=array("success"=>true, "data"=>$tempsArray);
        $return=json_encode($return);
        return new Response($return, 200, array('Content-Type'=>'application/json'));
    }

    /**
     * @Route("/getlights", name="getLights")
     */
    public function getLightsAction()
    {
        $lightsArray = $this->getlightsArray();
        $return=array("success"=>true, "data"=>$lightsArray);
        $return=json_encode($return);
        return new Response($return, 200, array('Content-Type'=>'application/json'));
    }

    private function getTempChart()
    {
        $unfilteredDataArray = $this->getTemperaturesArray();
        $data = array();
        foreach ($unfilteredDataArray as $dataArray)
        {
            array_push($data, array($dataArray['time'] *1000 , $dataArray['temperature']));
        }
        $ob = new Highchart();
        $ob->chart->renderTo('tempChart');
        $ob->chart->type('spline');
        $ob->plotOptions->pie(array(
            'allowPointSelect'  => true,
            'cursor'    => 'pointer',
            'dataLabels'    => array('enabled' => false),
            'showInLegend'  => false
        ));
        $ob->lang->shortMonths(array("Ene" , "Feb" , "Mar" , "Abr" , "May" , "Jun" , "Jul" , "Ago" , "Sep" , "Oct" , "Nov" , "Dec"));
        $ob->lang->weekdays(array("Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"));
        $ob->title->text('Sensor: LM35');
        $ob->xAxis->title(array('text'  => "Tiempo"));
        $ob->xAxis->type('datetime');
        $ob->yAxis->title(array('text'  => "Temperatura"));
        $tempLimit = $this->get('app_config')->getConfigValue('temperature_limit');
        $ob->yAxis->plotLines(array(array('value'  => $tempLimit, 'color'  => "red", 'dashStyle'=> "shortdash", 'width'  => 2, 'label'  => array( 'text'=>"Umbral de alerta (" . $tempLimit .")"))));
        $ob->series(array(array('name' => 'Temperatura', 'data' => $data)));
        return $ob;
    }

    private function getTemperaturesArray(){
        $repo = $this->getDoctrine()->getRepository('AppBundle:Temperature');
        $temps = $repo->findAll();
        $tempsArray = array();
        foreach ($temps as $temp)
        {
            array_push($tempsArray, array('id' => $temp->getId(),'temperature' => $temp->getTemperature(), 'time' => $temp->getTime()));
        }
        return $tempsArray;
    }

    private function getLightChart()
    {
        $unfilteredDataArray = $this->getLightsArray();
        $data = array();
        foreach ($unfilteredDataArray as $dataArray)
        {
            array_push($data, array($dataArray['time'] *1000 , $dataArray['light']));
        }
        $ob = new Highchart();
        $ob->chart->renderTo('lightChart');
        $ob->chart->type('spline');
        $ob->plotOptions->pie(array(
            'allowPointSelect'  => true,
            'cursor'    => 'pointer',
            'dataLabels'    => array('enabled' => false),
            'showInLegend'  => false
        ));
        $ob->lang->shortMonths(array("Ene" , "Feb" , "Mar" , "Abr" , "May" , "Jun" , "Jul" , "Ago" , "Sep" , "Oct" , "Nov" , "Dec"));
        $ob->lang->weekdays(array("Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"));
        $ob->title->text('Sensor: Fotoresistor genérico');
        $ob->xAxis->title(array('text'  => "Tiempo"));
        $ob->xAxis->type('datetime');
        $ob->yAxis->title(array('text'  => "Luminosidad"));
        $lightLimit = $this->get('app_config')->getConfigValue('light_limit');
        $ob->yAxis->plotLines(array(array('value'  => $lightLimit, 'color'  => "red", 'dashStyle'=> "shortdash", 'width'  => 2, 'label'  => array( 'text'=>"Umbral de alerta (" . $lightLimit .")"))));
        $ob->series(array(array('name' => 'Luminosidad', 'data' => $data)));
        return $ob;
    }

    private function getLightsArray(){
        $repo = $this->getDoctrine()->getRepository('AppBundle:Light');
        $lights = $repo->findAll();
        $lightsArray = array();
        foreach ($lights as $light)
        {
            array_push($lightsArray, array('id' => $light->getId(),'light' => $light->getLight(), 'time' => $light->getTime()));
        }
        return $lightsArray;
    }

    /**
     * @Route("/insertsensordata", name="insertSensorData")
     */
    public function insertSensorDataAction(Request $request)
    {
        if(strcmp($request->request->get('key'), $this->apiKey) !== 0)
        {
            return new Response('{success: false}', 200, array('Content-Type'=>'application/json'));
        }
        $tempValue = $request->request->get('temperature');
        $lightValue = $request->request->get('light');

        $em = $this->getDoctrine()->getManager();
        $temp = new Temperature();
        $temp->setTemperature($tempValue);
        $temp->setTime(time());
        $em->persist($temp);
        $light = new Light();
        $light->setLight($lightValue);
        $light->setTime(time());
        $em->persist($light);
        $em->flush();

        $time = date("Y-m-d H:m:s",time());
        if($tempValue > $this->get('app_config')->getConfigValue('temperature_limit'))
        {
            $msg = '['. $time. '] - Alerta, alta temperatura!: ' . $tempValue;
            $this->notifySensorAlerts($msg);
        }
        if($lightValue > $this->get('app_config')->getConfigValue('light_limit'))
        {
            $msg = '['. $time. '] - Alerta, alta luminosidad!: ' . $lightValue;
            $this->notifySensorAlerts($msg);
        }
        return new Response('{success: true, temperature: '. $tempValue .', light: ' . $lightValue . '}', 200, array('Content-Type'=>'application/json'));
    }

    private function notifySensorAlerts($msg)
    {
        if($this->get('app_config')->getConfigValue('email_notify') == "1") $this->emailAlerts($msg);
        if($this->get('app_config')->getConfigValue('twitter_notify') == "1") $this->tweetAlert($msg);
        return new Response('{ "success": true }', 200, array('Content-Type'=>'application/json'));
    }

    private function emailAlerts($msg)
    {
        $emails = $this->getEmails();
        foreach ($emails as $email)
        {
            $message = \Swift_Message::newInstance()
                ->setSubject('Alerta!')
                ->setFrom('pistats@none.com')
                ->setTo($email)
                ->setBody($msg);
            $this->get('mailer')->send($message);
        }
    }

    private function tweetAlert($msg)
    {
        $twitter = $this->get('endroid.twitter');
        $twitter->query('statuses/update', 'POST', 'json', array( 'status' => $msg ));
    }

    private function getEmails()
    {
        $em = $this->getDoctrine()->getManager();
        $usersToNotify = $em->getRepository('AppBundle:Email')->findAll();
        $emails = array();
        foreach ($usersToNotify as $user) {
            array_push($emails, $user->getEmail());
        }
        return $emails;
    }

    /**
     * @Route("/test", name="test")
     */
    public function testAction()
    {
        return new Response('{ "success": true }', 200, array('Content-Type'=>'application/json'));
    }
}