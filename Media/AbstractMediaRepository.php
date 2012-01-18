<?php namespace WebDev\Bundle\MediaBundle\Media;

/**
 * Abstract base media repository class
 * 
 * @author Josiah <josiah@web-dev.com.au>
 */
use WebDev\Conventional\StringTransformer;

abstract class AbstractMediaRepository
    implements
        MediaRepository
{
    /**
     * Creates a new instance of the abstract media repository
     * 
     * @param MediaOperator $mediaOperator
     */
    public function __construct(MediaOperator $mediaOperator)
    {
        $this->mediaOperator = $mediaOperator;
    }
    
    /**
     * The media operator that manages the files attached to objects
     * 
     * @var MediaOperator
     */
    protected $mediaOperator;
    
    /**
     * Gets the media operator for this media repository
     * 
     * @return \WebDev\Bundle\MediaBundle\Media\MediaOperator
     */
    public function getMediaOperator()
    {
        return $this->mediaOperator;
    }
    
    /**
     * The name given to this media repository
     * 
     * @var string
     */
    protected $name;
    
    /**
     * (non-PHPdoc)
     * @see WebDev\Bundle\MediaBundle\Media.MediaRepository::getName()
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the name of this repository
     * 
     * @param string $name
     * @return \WebDev\Bundle\MediaBundle\Media\AbstractMediaRepository
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * The absolute path template used in this media repository
     * 
     * @var string
     */
    protected $pathTemplate;
    
    /**
     * Gets the path template for this media repository
     * 
     * @return string
     */
    public function getPathTemplate()
    {
        return $this->pathTemplate;
    }
    
    /**
     * Sets the path template used in this media repository
     * 
     * @param string $pathTemplate
     * @return \WebDev\Bundle\MediaBundle\Media\AbstractMediaRepository
     */
    public function setPathTemplate($pathTemplate)
    {
        $this->pathTemplate = $pathTemplate;
        return $this;
    }
    
    /**
     * Generate a path from the path template and the specified input data
     * 
     * @param string $input
     * @return string
     */
    public function generatePath($input)
    {
        $transform = new StringTransformer($this->getPathTemplate(), $input);
        
        return $transform();
    }
    
    /**
     * (non-PHPdoc)
     * @see WebDev\Bundle\MediaBundle\Media.MediaRepository::process()
     */
    public function process($object)
    {
        $this->mediaOperator->processMediaChange($object);
    }
}
