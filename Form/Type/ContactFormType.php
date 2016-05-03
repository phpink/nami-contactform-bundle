<?php namespace PhpInk\Nami\ContactFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->session    = $options['session'];
        $this->translator = $options['translator'];
        
        $builder->add('subject', TextType::class, array(
            'label' => 'form.label.subject',
            'translation_domain' => 'NamiContactFormBundle'
        ));
        $builder->add('name', TextType::class, array(
            'label' => 'form.label.name',
            'translation_domain' => 'NamiContactFormBundle',
            'attr' => array(
                'pattern'     => '.{2,}' //minlength
            )
        ));
        $builder->add('email', EmailType::class, array(
            'label' => 'form.label.email',
            'translation_domain' => 'NamiContactFormBundle',
        ));
        $builder->add('company', TextType::class, array(
            'label' => 'form.label.company',
            'required' => false,
            'translation_domain' => 'NamiContactFormBundle'
        ));
        $builder->add('captcha',
            CaptchaType::class,
            array_merge($options, array(
                'invalid_message' => $this->translator->trans(
                    'form.error.captcha', [], 'NamiContactFormBundle'
                ),
                'session' => $this->session,
                'translator' => $this->translator,
            ))
        );
        $builder->add('message', TextareaType::class, array(
            'label' => 'form.label.message',
            'attr' => array(
                'class' => 'textarea'
            ),
            'translation_domain' => 'NamiContactFormBundle'
        ));
        $builder->add('submit', SubmitType::class, array(
            'label' => 'form.label.submit',
            'translation_domain' => 'NamiContactFormBundle',
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return array
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $collectionConstraint = new Collection(array(
            'name' => array(
                new NotBlank(array('message' => 'form.error.name_empty')),
                new Length(array('min' => 2))
            ),
            'email' => array(
                new NotBlank(array('message' => 'form.error.email_empty')),
                new Email(array('message' => 'form.error.email_invalid'))
            ),
            'subject' => array(
                new NotBlank(array('message' => 'form.error.subject_empty')),
                new Length(array('min' => 3))
            ),
            'company' => array(),
            'message' => array(
                new NotBlank(array('message' => 'form.error.message_empty')),
                new Length(array('min' => 5))
            )
        ));

        $resolver->setDefaults(array(
            'constraints' => $collectionConstraint
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['session', 'translator']);
        $resolver->addAllowedTypes('session', SessionInterface::class);
        $resolver->addAllowedTypes('translator', TranslatorInterface::class);
    }

    public function getBlockPrefix()
    {
        return 'nami_contactform_contact';
    }
}
