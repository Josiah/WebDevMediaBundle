<?php namespace WebDev\Bundle\MediaBundle\EventHandler;
use WebDev\Bundle\MediaBundle\Media\MediaOperator;

use Doctrine\Common\EventSubscriber;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

use WebDev\Bundle\MediaBundle\Media\EntityMediaRepository;

/**
 * Injects the media files for entites
 * 
 * @author Josiah <josiah@web-dev.com.au>
 */
class EntityMediaInjector
    implements
        EventSubscriber
{
    /**
     * Creates a new instance of this injector
     * 
     * @param string $entityType
     * @param string $property
     * @param MediaOperator $operator
     */
    public function __construct($entityType, MediaOperator $operator)
    {
        $this->entityType = $entityType;
        $this->operator = $operator;
    }
    
    /**
     * The entity type handled by this media injector
     * 
     * @var string
     */
    protected $entityType;
    
    /**
     * The media operator
     * 
     * @var \WebDev\Bundle\MediaBundle\Media\MediaOperator
     */
    protected $operator;

    /**
     * Gets the subscribed events for this event subscriber
     * 
     * @return multitype:string
     */
    public function getSubscribedEvents()
    {
        return array(Events::postLoad);
    }
    
    /**
     * 
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        $entityRepository = $event->getEntityManager()->getRepository($this->entityType);
        $entityClass = $entityRepository->getClassName();
        
        // Skip entities not observed by this media injector
        if(!($entity instanceof $entityClass)) return;
        
        $this->operator->loadMedia($entity);
    }
}
