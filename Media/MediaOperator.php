<?php namespace WebDev\Bundle\MediaBundle\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use WebDev\Conventional\StringTransformer;

use WebDev\Conventional\Resolver;
use \SplFileInfo;

class MediaOperator
{
    /**
     * Creates a new instance of the media operator
     * 
     * @param string $property of the object to look for media on
     * @param string $path to the file on the filesystem
     */
    public function __construct($property, $path)
    {
        $this->resolver = new Resolver();
        $this->property = $property;
        $this->pathTemplate = $path;
    }

    /**
     * The property of the object that is to contain the media for this type
     * 
     * @var string
     */
    protected $property;

    /**
     * The path template to the object
     * 
     * @var string
     */
    protected $pathTemplate;

    /**
     * The object property resolver
     * 
     * @var \WebDev\Conventional\Resolver
     */
    protected $resolver;

    /**
     * The root path for this media operator
     * 
     * @var string
     */
    protected $rootPath;

    /**
     * Sets the root path for this media operator
     * 
     * @param string $path
     */
    public function setRootPath($path)
    {
        $this->rootPath = $path;
    }

    /**
     * Gets the root path for this media operator
     * 
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * Gets the media on the object in the specified property
     * 
     * @param mixed $object
     * @return mixed the media object
     */
    public function getMedia($object)
    {
        return $this->resolver
                    ->get($object, $this->property);
    }

    /**
     * Gets the file info instance containing information about the media on the object
     * 
     * @param mixed $object
     * @return \SplFileInfo
     */
    public function getMediaInfo($object)
    {
        $file = $this->getMedia($object);
        if (is_string($file)) {
            return new SplFileInfo($file);
        } elseif ($file instanceof \SplFileInfo) {
            return $file;
        } elseif (is_object($file) && is_callable(array($file, '__toString'))) {
            return new SplFileInfo((string) $file);
        } else {
            return null;
        }
    }

    /**
     * Get the absolute path to the media file for the specified object 
     * 
     * @param mixed $object
     * @return string
     */
    public function getAbsolutePath($object)
    {
        return $this->getRootPath() . DIRECTORY_SEPARATOR . $this->getRelativePath($object);
    }

    /**
     * Get the relative path to the media file for the specified object
     * 
     * @param object $object
     * @return string
     */
    public function getRelativePath($object)
    {
        return (string) (new StringTransformer($this->pathTemplate, $object));
    }

    /**
     * Processes any change to the media in the object
     * 
     * @param mixed $object
     */
    public function processMediaChange($object)
    {
        $file = $this->getMediaInfo($object);
        $path = $this->getAbsolutePath($object);

        if (is_null($file)) {
            return;
        }

        // Process uploads
        if ($file->getRealPath() != realpath($path)) {
            if ($file instanceof UploadedFile) {
                $file->move(dirname($path), basename($path));
            } else {
                copy($file->getRealPath(), $path);
            }

            // Purge the derivatives
            foreach ($this->derivativePaths as $pathTemplate) {
                $path = (string) (new StringTransformer($pathTemplate, $object));
                foreach (glob($path) as $path) {
                    if (is_file($path) && is_writable($path)) {
                        unlink($path);
                    }
                }
            }
        }
    }

    /**
     * The paths that can contain a derivative of the media in this repository
     * 
     * @var array
     */
    protected $derivativePaths = array();

    /**
     * Adds a path that contains media derived from this repository
     * 
     * @param array $path
     */
    public function addDerivativePath($path)
    {
        foreach ((array) $path as $path) {
            $this->derivativePaths[] = $path;
        }
    }

    /**
     * Loads the media into the object
     * 
     * @param mixed $object
     */
    public function loadMedia($object)
    {
        $path = $this->getAbsolutePath($object);

        // Load the file into the object if it exists
        if (is_file($path)) {
            $file = new SplFileInfo($path);
            $this->resolver
                 ->set($object, $this->property, $file);
        }
    }
}
