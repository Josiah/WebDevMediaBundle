<?php namespace WebDev\Bundle\MediaBundle\Media;

/**
 * A media file that can be used in media bundle operations
 * 
 * @author Josiah <josiah@web-dev.com.au>
 */
interface MediaFile
{
    /**
     * Gets the identifier for this media file
     * 
     * @return string
     */
    public function getIdentifier();
    
    /**
     * Gets the repository for this media file
     * 
     * @return string
     */
    public function getRepository();
    
    /**
     * Gets the real path to this media file
     * 
     * @return string
     */
    public function getRealPath();
}
