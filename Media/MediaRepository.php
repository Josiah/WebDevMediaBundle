<?php namespace WebDev\Bundle\MediaBundle\Media;

/**
 * Media Repository
 * 
 * Represents a collection of media items that can be retrieved by a unique identifier
 * 
 * @author Josiah <josiah@web-dev.com.au>
 */
interface MediaRepository
{
    /**
     * Gets the name of this media repository
     * 
     * @return string
     */
    public function getName();
    
    /**
     * Finds a media file by its unique identifier
     * 
     * @param mixed $identifier
     * @return \SplFileInfo
     */
    public function find($identifier);
    
    /**
     * Indicates whether this media repository can process the specified object
     * 
     * @param object $object
     * @return bool TRUE if this media repository can process the object; FALSE otherwise
     */
    public function canProcess($object);
    
    /**
     * Processes the media associated with the object using this media repository
     * 
     * @param object $object
     */
    public function process($object);
    
    /**
     * Gets the operator for this media repository
     * 
     * @return MediaOperator 
     */
    public function getMediaOperator();
}
