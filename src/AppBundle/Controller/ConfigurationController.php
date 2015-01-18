<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationController extends Controller
{
    /**
     * @Route("/configuration", name="configuration")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $emails = $em->getRepository('AppBundle:Email')->findAll();
        $configData = $this->get('app_config')->getConfigData();
        return $this->render('AppBundle:Main:configuration.html.twig', array('emails' => $emails, 'configData' =>$configData));
    }

    /**
     * @Route("/emaildelete/{id}", name="email_delete")
     */
    public function deleteEmailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Email')->find($id);
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('configuration'));
    }

    /**
     * @Route("/updateemail", name="email_update")
     */
    public function updateEmailAction(Request $request)
    {
        $id = $request->request->get('id');
        $email = $request->request->get('email');

        $em = $this->getDoctrine()->getManager();

        $emailEntity = $em->getRepository('AppBundle:Email')->find($id);

        if (!$emailEntity) {
            throw $this->createNotFoundException('Unable to find Email entity.');
        }

        $emailEntity->setEmail($email);
        $em->flush();

        return new Response('{ "success": true }', 200, array('Content-Type'=>'application/json'));
    }

    /**
     * @Route("/createemail", name="email_create")
     */
    public function createEmailAction(Request $request)
    {
        $email = $request->request->get('email');
        $em = $this->getDoctrine()->getManager();
        $emailEntity = new Email();
        $emailEntity->setEmail($email);
        $em->persist($emailEntity);
        $em->flush();

        return new Response('{ "success": true }', 200, array('Content-Type'=>'application/json'));
    }

    /**
     * @Route("/updateconfig", name="config_update")
     */
    public function updateConfigAction(Request $request)
    {
        $tempLimit = $request->request->get('tempLimit');
        $lightLimit = $request->request->get('lightLimit');
        $notifyEmail = $request->request->get('notifyEmail');
        $notifyTwitter = $request->request->get('notifyTwitter');
        $this->get('app_config')->setConfigValue('temperature_limit',$tempLimit);
        $this->get('app_config')->setConfigValue('light_limit',$lightLimit);
        $this->get('app_config')->setConfigValue('email_notify',$notifyEmail);
        $this->get('app_config')->setConfigValue('twitter_notify',$notifyTwitter);
        return new Response('{ "success": true }', 200, array('Content-Type'=>'application/json'));
    }

}