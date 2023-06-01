<?php

namespace Drupal\fer\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
//use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * Route subscriber.
 */
class FerRouteSubscriber extends RouteSubscriberBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a FerRouteSubscriber object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    $path_extensions = $this->configFactory->get('fer_departments.settings')->getRawData();

    foreach ($path_extensions as $key => $value)
    {
      $url = preg_replace('/ /', '_', $value);
      $url = strtolower($url);
      $route = new Route('/registration/'.$url,
        [
          '_title' => $value,
          '_form' => 'Drupal\fer\Form\FerVisitorsForm',
          'type' => $key,
        ],
        [
          '_permission' => 'access content'
        ],
      );
      $collection->add('fer.route_subscriber' . $key, $route);
    }
  }

  /**
   * {@inheritdoc}
   */
/*  public static function getSubscribedEvents() {
    $events = parent::getSubscribedEvents();

    // Use a lower priority than \Drupal\views\EventSubscriber\RouteSubscriber
    // to ensure the requirement will be added to its routes.
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -300];

    return $events;
  }*/

}
