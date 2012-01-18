<?php namespace WebDev\Bundle\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WebDevMediaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        if(array_key_exists('root_path',$config))
        {
            $container->setParameter('webdev_media.root_path', $config['root_path']);
        }
        
        $this->loadRepositories($config, $container);
        $this->loadTransformations($config, $container);
    }
    
    public function loadRepositories(array $config, ContainerBuilder $container)
    {
        if(!array_key_exists('repositories',$config)) return;
        
        $repositories = $config['repositories'];
        foreach($repositories as $name => $repositoryConfig)
        {
            if(isset($repositoryConfig['entity']))
            {
                // Create the entity media operator
                $operatorDefinitionID = "media.operators.{$name}";
                $operatorDefinition = new Definition('%webdev_media.operator.class%',array(
                    $repositoryConfig['property'],
                    $repositoryConfig['path'],
                ));
                $operatorDefinition->addMethodCall('setRootPath', array('%webdev_media.root_path%'));
                $container->setDefinition($operatorDefinitionID, $operatorDefinition);
                
                // Create the media repository
                $definitionID = "media.repositories.{$name}";
                $definition = $container->setDefinition($definitionID, new DefinitionDecorator("media.doctrine_repository_template"));
                $definition->setArguments(array(
                    new Reference(sprintf("doctrine.orm.%s_entity_manager",$repositoryConfig['entity_manager'])),
                    $repositoryConfig['entity'],
                    new Reference($operatorDefinitionID)));
                $definition->addMethodCall('setName',array($name));
                $definition->addMethodCall('setPathTemplate',array($repositoryConfig['path']));
                
                // Add the repository to the media manager
                $container->getDefinition('media.manager')->addMethodCall('addRepository',array(new Reference($definitionID)));
                
                // Create the entity media injector listener
                $injectorDefinition = new Definition('%webdev_media.entity_repository.injector.class%',array(
                    $repositoryConfig['entity'],
                    new Reference($operatorDefinitionID),
                ));
                $injectorDefinition->addTag('doctrine.event_subscriber',array('connection' => $repositoryConfig['entity_manager']));
                $container->setDefinition("{$definitionID}.injector", $injectorDefinition);
            }
        }
    }
    
    public function loadTransformations(array $config, ContainerBuilder $container)
    {
        if(!array_key_exists('transformations',$config)) return;
        
        $transformations = $config['transformations'];
        foreach($transformations as $name => $transformation)
        {
            $transformationDefinitionID = "media.transformations.{$name}";
            $transformationDefinition = new Definition('%webdev_media.transformation.class%',array($transformation));
            $transformationDefinition->setFactoryService("media.transformation_factory");
            $transformationDefinition->setFactoryMethod("createTransformation");
            $container->setDefinition($transformationDefinitionID, $transformationDefinition);
            $container->getDefinition('media.manager')
                ->addMethodCall('addTransformation',array($name,new Reference($transformationDefinitionID)));
        }
    }
}
