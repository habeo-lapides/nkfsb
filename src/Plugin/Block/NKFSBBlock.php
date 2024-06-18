<?php

namespace Drupal\nkfsb\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Provides a custom block for social sharing buttons.
 *
 * @Block(
 *   id = "nkfsb_block",
 *   admin_label = @Translation("NKFSB Block")
 * )
 */
class NKFSBBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Constructs a new NKFSBBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The current route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $current_route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->currentRouteMatch->getParameter('node');

    // Build action array for displayable icons.
    if ($node instanceof NodeInterface) {
      $actions = [
        'email',
        'print',
        'pinterest',
        'linkedin',
        'facebook',
        'tweet',
      ];

      $services = [];
      $item = [];

      // Node properties to send to block.
      $item['title'] = $node->get('title')->value;
      $item['page_url'] = $node->toUrl()->toString();
      $item['tweet'] = $node->get('field_tweet_text')->value;
      $item['field_tweet_hashtag'] = $node->get('field_tweet_hashtag')->value;

      foreach ($actions as $action) {
        if ($node->hasField('field_action_' . $action)) {
          $services[$action] = $node->get('field_action_' . $action)->value;
        }
      }

      return [
        '#theme' => 'nkfsb_block',
        '#content' => $this->t('Custom NKFSB Block Content'),
        '#service' => $services,
        '#item' => $item,
        // Add more variables here as needed.
        '#attached' => [
          'library' => [
            // Add your library here if needed.
          ],
        ],
      ];
    }
  }
}
