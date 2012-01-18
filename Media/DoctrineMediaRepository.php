<?php namespace WebDev\Bundle\MediaBundle\Media;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

use WebDev\Bundle\MediaBundle\Media\AbstractMediaRepository;

class DoctrineMediaRepository extends AbstractMediaRepository
{
    /**
     * Creates a new instance and injects the right services
     * 
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager, $className, MediaOperator $mediaOperator)
    {
        parent::__construct($mediaOperator);
        $this->objectManager = $objectManager;
        $this->objectRepository = $objectManager->getRepository($className);
    }
    
    /**
     * The object manager
     * 
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;
    
    /**
     * The object repository
     * 
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $objectRepository;
    
    /**
     * Gets the class name of the object in this media repository
     * 
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectRepository->getClassName();
    }
    
    /**
     * (non-PHPdoc)
     * @see WebDev\Bundle\MediaBundle\Media.MediaRepository::find()
     */
    public function find($id)
    {
       $object = $this->objectRepository->find($id);
       return $this->generatePath($object);
    }

    /**
     * (non-PHPdoc)
     * @see WebDev\Bundle\MediaBundle\Media.MediaRepository::canProcess()
     */
    public function canProcess($object)
    {
        $class = $this->getObjectClass();
        return $object instanceof $class;
    }
}
