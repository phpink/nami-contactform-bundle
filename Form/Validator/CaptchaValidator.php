<?php namespace PhpInk\Nami\ContactFormBundle\Form\Validator;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Captcha validator
 */
class CaptchaValidator
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * Session key to store the code
     */
    private $key;

    /**
     * Error message text for non-matching submissions
     */
    private $invalidMessage;

    /**
     * Translator
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     * @param SessionInterface    $session
     * @param string              $key
     * @param string              $invalidMessage
     */
    public function __construct(TranslatorInterface $translator, SessionInterface $session, $key, $invalidMessage)
    {
        $this->translator       = $translator;
        $this->session          = $session;
        $this->key              = $key;
        $this->invalidMessage   = $invalidMessage;
    }

    /**
     * @param FormEvent $event
     */
    public function validate(FormEvent $event)
    {
        $form = $event->getForm();
        $userSum = (int) $form->getData()['captcha'];
        if (!$userSum || $userSum !== $this->getExpectedSum()) {
            $form->addError(
                new FormError(
                    $this->translator->trans(
                        $this->invalidMessage, array(), 'validators'
                    )
                )
            );
        }
        $this->session->remove($this->key);
    }

    /**
     * Retrieve the expected CAPTCHA sum
     *
     * @return mixed|null
     */
    protected function getExpectedSum()
    {
        return $this->session->get($this->key, null);
    }
}
