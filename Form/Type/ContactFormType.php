<?php namespace PhpInk\Nami\ContactFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{
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
     * @param TranslatorInterface $translator
     * @param array               $options
     */
    public function __construct(TranslatorInterface $translator, $options = array())
    {
        $this->session    = $options['session'];
        $this->translator = $translator;
        $this->options    = $options;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subject', 'text', array(
            'attr' => array(
                'placeholder' => 'Subject'
            )
        ));
        $builder->add('name', 'text', array(
            'attr' => array(
                'placeholder' => 'Your name',
                'pattern'     => '.{2,}' //minlength
            )
        ));
        $builder->add('email', 'email', array(
            'attr' => array(
                'placeholder' => 'Your email'
            )
        ));
        $builder->add('company', 'text', array(
            'attr' => array(
                'placeholder' => 'Your company'
            ),
            'required' => false
        ));
//        $builder->add('captcha',
//            CaptchaType::class,
//            array_merge($this->options, array(
//                'invalid_message' => $this->translator->trans(
//                    'Antispam value is incorrect.'
//                ),
//                'session' => $this->session,
//            ))
//        );
        $builder->add('message', 'textarea', array(
            'attr' => array(
                'placeholder' => 'Your message',
                'class' => 'textarea'
            )
        ));
        $builder->add('submit', 'submit');
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return array
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $collectionConstraint = new Collection(array(
            'name' => array(
                new NotBlank(array('message' => 'Le nom ne doit pas être vide.')),
                new Length(array('min' => 2))
            ),
            'email' => array(
                new NotBlank(array('message' => 'L\'email ne doit pas être vide.')),
                new Email(array('message' => 'L\'email est invalide.'))
            ),
            'subject' => array(
                new NotBlank(array('message' => 'Le sujet ne doit pas être vide.')),
                new Length(array('min' => 3))
            ),
            'company' => array(),
            'message' => array(
                new NotBlank(array('message' => 'Le message ne doit pas être vide.')),
                new Length(array('min' => 5))
            )
        ));

        $resolver->setDefaults(array(
            'constraints' => $collectionConstraint
        ));
    }

    public function getName()
    {
        return 'contact';
    }
}
