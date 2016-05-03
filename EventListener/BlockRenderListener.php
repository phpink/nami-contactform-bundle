<?php

namespace PhpInk\Nami\ContactFormBundle\EventListener;

use PhpInk\Nami\ContactFormBundle\Form\Type\ContactFormType;
use PhpInk\Nami\CoreBundle\Event\BlockRenderEvent;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class BlockRenderListener
{
    /**
     * @var string
     */
    protected $mailTo;
    protected $host;
    
    /**
     * View marker when mail is sent
     * @var bool
     */
    private $mailSent = false;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var - 
     */
    protected $twig;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @param string $mailTo
     */
    public function __construct($mailTo, $host, $formFactory, $translator, $twig, $mailer)
    {
        $this->mailTo = $mailTo;
        $this->host = $host;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
        $this->twig = $twig;
        $this->mailer = $mailer;
    }
    
    public function onBlockRender(BlockRenderEvent $event)
    {
        if ($event->getBlock()->getType() === 'ContactForm') {
            $session = $event->getRequest()->getSession();
            if (!$session) {
                $session = new Session();
                $session->start();
                $event->getRequest()->setSession($session);
            }
            $event->getBlock()->setContent(
                $this->twig->render(
                    '@NamiContactFormBundle/Resources/views/mail.html.twig',
                    array(
                        'form' => $this->getForm($event)->createView(),
                        'mailSent' => $this->mailSent
                    ),
                    [
                        'session' => $session,
                        'translator' => $this->translator
                    ]
                )
            );
            return $this;
        }
    }

    /**
     * Build the contact form
     * @param BlockRenderEvent $event
     * @return \Symfony\Component\Form\Form
     */
    private function getForm(BlockRenderEvent $event)
    {
        $form = $this->formFactory->create(
            ContactFormType::class, null, [
                'session' => $event->getRequest()->getSession(),
                'translator' => $this->translator
            ]
        );

        // Submit the form data
        if ($event->getRequest()->isMethod('post')) {
            $form->handleRequest($event->getRequest());

            // If the submitted data is valid
            if ($form->isValid()) {
                $this->sendMail($event, $form);
            }
        }
        return $form;
    }

    /**
     * Send the contact mail when form has been validated
     * @param BlockRenderEvent $event
     * @param FormInterface $form
     */
    private function sendMail(BlockRenderEvent $event, $form)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($form->get('subject')->getData())
            ->setFrom($form->get('email')->getData())
            ->setTo($this->mailTo)
            ->setBody(
                $this->twig->render(
                    '@NamiContactFormBundle/Resources/views/mail.html.twig',
                    array(
                        'host' => $this->host,
                        'ip' => $event->getRequest()->getClientIp(),
                        'browser' => $event->getRequest()->headers->get('user-agent'),
                        'subject' => $form->get('subject')->getData(),
                        'name' => $form->get('name')->getData(),
                        'email' => $form->get('email')->getData(),
                        'company' => $form->get('company')->getData(),
                        'message' => $form->get('message')->getData()
                    )
                )
            );
        $this->mailer->send($message);
        $this->mailSent = 'Votre message a bien été envoyé. Merci';
    }
}
