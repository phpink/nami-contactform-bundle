<?php namespace PhpInk\Nami\ContactFormBundle\Form\Type;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

use PhpInk\Nami\ContactFormBundle\Form\Validator\CaptchaValidator;

/**
 * Captcha type
 */
class CaptchaType extends AbstractType
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var array
     */
    protected $captchaSum;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Options
     * @var array
     */
    private $options = array();

    /**
     * @param SessionInterface    $session
     * @param TranslatorInterface $translator
     * @param array               $options
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator, $options = array())
    {
        $this->session      = $session;
        $this->translator  = $translator;
        $this->options      = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $validator = new CaptchaValidator(
            $this->translator,
            $this->session,
            sprintf('captcha_%s', $builder->getForm()->getName()),
            $options['invalid_message']
        );
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            array($validator, 'validate')
        );

        $this->captchaSum = array(rand(1,9), rand(1,9));
        $builder->add('captcha', 'text', array(
            'attr' => array(
                'placeholder' => sprintf(
                    'Combien font %d + %d ? (antispam)',
                    $this->captchaSum[0],
                    $this->captchaSum[1]
                )
            ),
            'data' => ''
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $sessionKey = sprintf('captcha_%s', $form->getName());
        $this->session->set(
            $sessionKey,
            $this->captchaSum[0] +
            $this->captchaSum[1]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->options['mapped'] = false;
        $this->options['compound'] = true;
        $resolver->setDefaults($this->options);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'captcha';
    }
}
