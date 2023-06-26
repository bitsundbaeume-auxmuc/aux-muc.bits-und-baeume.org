<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Routing\RouterInterface;

final class MenuBuilder {
    private $factory;
    private RouterInterface $router;

    /**
     * @param $factory
     */
    public function __construct(FactoryInterface $factory, RouterInterface $router)
    {
        $this->factory = $factory;
        $this->router = $router;
    }


    public function createMainMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root');

        $routes = $this->router->getRouteCollection()->all();

        foreach ($routes as $name => $route) {

            if ($route->getOption('_menu') == 'main') {
//                print_r($name); die();
                $title = $route->getOption('_menu_title') ?: $name;
                $weight = $route->getOption('_menu_weight') ?: 0;

                $menu->addChild($title, ['route' => $name, 'weight' => $weight]);
            }
        }

//        $menu->addChild('Home', ['route' => 'homepage']);
        return $menu;
    }
}