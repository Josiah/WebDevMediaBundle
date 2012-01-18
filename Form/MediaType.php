<?php namespace WebDev\Bundle\MediaBundle\Form;

use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Form\AbstractType;

/**
 * A form type to bind data into fields from a media repository
 * 
 * @author Josiah <josiah@web-dev.com.au>
 */
class MediaType extends AbstractType 
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('file','file')
            ->add('delete','checkbox',array('label'=>'Remove file'))
        ;
    }
    
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::getParent()
     */
    public function getParent()
    {
        return 'form';
    }
    
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'media';
    }
}
