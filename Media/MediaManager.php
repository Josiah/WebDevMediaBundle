<?php namespace WebDev\Bundle\MediaBundle\Media;

use Imagine\Image\ImagineInterface;

use Imagine\Filter\Transformation;

use Exception;

/**
 * Responsible for managing the media that is bound to entities
 * 
 * @author Josiah <josiah@web-dev.com.au>
 */
class MediaManager
{
    /**
     * Creates a new instance and injects services
     * 
     * @param ImagineInterface $imagine
     */
    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }
    
    /**
     * Imagine image transformer
     * 
     * @var ImagineInterface
     */
    protected $imagine;
    
    /**
     * The root path where images should be transformed
     * 
     * @var string
     */
    protected $transformationPath;
    
    /**
     * Gets the root path where images are transformed
     * 
     * @return string
     */
    public function getTransformationPath()
    {
        return $this->transformationPath;
    }
    
    /**
     * Sets the root path where images are transformed
     * 
     * @param string $path
     */
    public function setTransformationPath($path)
    {
        $this->transformationPath = $path;
        return $this;
    }
    
    /**
     * The media repositories (for originals)
     * 
     * @var array
     */
    protected $repositories;
    
    /**
     * Makes a repository available to this media manager
     * 
     * @param MediaRepository $repository
     * @return \WebDev\Bundle\MediaBundle\Media\MediaManager
     */
    public function addRepository(MediaRepository $repository)
    {
        $this->repositories[$repository->getName()] = $repository;
        return $this;
    }
    
    /**
     * Removes a repository from this media manager
     * 
     * @param MediaRepository|string $repository
     * @return \WebDev\Bundle\MediaBundle\Media\MediaManager
     */
    public function removeRepository($repository)
    {
        if(is_string($repository))
        {
            unset($this->repositories[$repository]);
        }
        elseif($repository instanceof MediaRepository)
        {
            unset($this->repositories[$repository->getName()]);
        }
        
        return $this; 
    }
    
    /**
     * Gets a repository from this media manager
     * 
     * @param string $repositoryName
     * @throws Exception if the repository can't be found in this media manager
     * @return \WebDev\Bundle\MediaBundle\Media\MediaRepository 
     */
    public function getRepository($repositoryName)
    {
        if(isset($this->repositories[$repositoryName]))
        {
            return $this->repositories[$repositoryName];
        }
        else 
        {
            throw new Exception("No repository in this media manager named '{$repositoryName}'.");
        }
    }
    
    /**
     * The possible transformations that can be made by this media manager
     * 
     * @var array
     */
    protected $transformations;
    
    /**
     * Adds a transformation to this media manager
     * 
     * @param string $name
     * @param Transformation $transformation
     * @return \WebDev\Bundle\MediaBundle\Media\MediaRepository
     */
    public function addTransformation($name, Transformation $transformation)
    {
        $this->transformations[$name] = $transformation;
        return $this;
    }
    
    /**
     * Gets the transformation identified by the specified transformation name
     * 
     * @param string $name
     * @throws Exception when the transformation cannot be found
     * @return Transformation
     */
    public function getTransformation($name)
    {
       if(isset($this->transformations[$name]))
       {
           return $this->transformations[$name];
       }
       else
       {
           throw new Exception("No transformation in this media manager named '{$name}'");
       }
    }
    
    /**
     * Performs the specified transformation on the media file
     * 
     * @param string $repositoryName
     * @param mixed $id
     * @param string $transformationName
     * @param string $relativePath
     */
    public function transform($repositoryName, $id, $transformationName, $relativePath)
    {
        $repository = $this->getRepository($repositoryName);
        $file = $repository->find($id);
        
        $absolutePath = $this->getTransformationPath().$relativePath;
        
        $this->getTransformation($transformationName)
            ->apply($this->imagine->open($file->getRealPath()))
            ->save($absolutePath);
        
        return new SplFileInfo($absolutePath);
    }
    
    /**
     * Invokes the correct repositories to process the media on the specified object
     * 
     * @param object $object
     */
    public function process($object)
    {
        foreach($this->repositories as $repository)
        {
            if($repository->canProcess($object))
            {
                $repository->process($object);
            }
        }
    }
}