<?php

namespace Drupal\fer\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Provides a FER form.
 */
class FerVisitorsForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The RouteMatch
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * The form constructor.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $routeMatch
   *   The RouteMatch
   */
  public function __construct(Connection $connection, CurrentRouteMatch $routeMatch)
  {
    $this->connection = $connection;
    $this->routeMatch = $routeMatch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fer_fer_visitors';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['name'] = [
      '#title' => t('Your name'),
      '#type' => 'textfield',
      '#size' => 15,
      '#description' => t('Please insert your name'),
      '#placeholder' => t('Insert your name'),
      '#required' => TRUE,
    ];

    $form['one_plus'] = [
      '#title' => t('One plus'),
      '#type' => 'select',
      '#options' => [
        0 => $this->t('Yes'),
        1 => $this->t('No')
      ],
      '#description' => t('Do you come with somebody else'),
      '#placeholder' => t('YES or NO'),
      '#required' => TRUE,
    ];

    $form['children'] = [
      '#title' => t('Amount of children'),
      '#type' => 'number',
      '#size' => 2,
      '#description' => t('Please insert amount of your children'),
      '#placeholder' => t('Insert amount of your children'),
      '#required' => TRUE,
    ];

    $form['vegetarians'] = [
      '#title' => t('Amount of vegetarians'),
      '#type' => 'number',
      '#size' => 2,
      '#description' => t('Please insert amount of vegetarians'),
      '#placeholder' => t('Insert amount of vegetarians'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#title' => t('Your email address'),
      '#type' => 'email',
      '#size' => 15,
      '#description' => t('Please insert your email address'),
      '#placeholder' => t('Insert your email address'),
      '#required' => TRUE,
    ];

//    kint($this->account);die();

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->cleanValues()->getValues();
//    kint($values);die();
    if (mb_strlen($form_state->getValue('name')) < 3) {
      $form_state->setErrorByName('name', $this->t('First name should be at least 3 characters.'));
    }
    $one_plus = $form_state->getValue('one_plus');
    $children = $form_state->getValue('children');
    if ($one_plus == '0')
    {

      $total = 1 + $children + 1;
    } else
    {
      $total = 1 + $children;
    }

    $number_of_vegetarians = (int)$form_state->getValue('vegetarians');

    if ($number_of_vegetarians > $total) {
      $form_state->setErrorByName('vegetarians', t('Total amount of vegetarians must not be higher than total amount of invited people'));
    }

    $email = $form_state->getValue('email');
    if ($email == !\Drupal::service('email.validator')->isValid($email)) {
      $form_state->setErrorByName('email', t('This email is not correct', ['%mail' => $email]));
    }
    //get all emails from SQL table
    $query = $this->connection->select('node__field_email', 'n');
    $query->fields('n', ['entity_id','field_email_value']);
    $all_emails = $query->execute()->fetchAllKeyed();

    if (in_array($email, $all_emails))
    {
      $form_state->setErrorByName('email', t('You are already registered, because this email already exists', ['%mail' => $email]));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //On submiting form, it creates new node on content type registration
    $node = Node::create(['type' => 'registration']);
    $node->set('title', $form_state->getValue('name'));
    $node->set('field_one_plus', $form_state->getValue('one_plus'));
    $node->set('field_children', $form_state->getValue('children'));
    $node->set('field_vegetarians', $form_state->getValue('vegetarians'));
    $node->set('field_email', $form_state->getValue('email'));
    $node->set('field_department', $this->routeMatch->getCurrentRouteMatch()->getRouteObject()->getDefault('_title'));
    $node->save();
    $this->messenger()->addStatus($this->t('Your form has been sent.'));
    $form_state->setRedirect('<front>');
  }

}
