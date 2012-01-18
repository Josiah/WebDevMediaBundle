<?php namespace WebDev\Bundle\MediaBundle\Twig;


use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use WebDev\Bundle\MediaBundle\Media\MediaManager;

use Twig_Extension;

class MediaBundleExtension extends Twig_Extension
{
    public function __construct(MediaManager $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }
    
    /**
     * The media manager
     * 
     * @var MediaManager
     */
    protected $mediaManager;
    
    /**
     * The url generator
     * 
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;
    
    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'media';
    }
    
    /**
     * {@inheritdoc}
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'media' => new \Twig_Function_Method($this,'getRelativePath'),
        );
    }
    
    /**
     * Gets the relative path to the file in the media repository
     * 
     * @param string $repository
     * @param object $object
     */
    public function getRelativePath($repositoryName, $object)
    {
        $repository = $this->mediaManager->getRepository($repositoryName);
        
        return $repository->getMediaOperator()->getRelativePath($object);
    }
}
