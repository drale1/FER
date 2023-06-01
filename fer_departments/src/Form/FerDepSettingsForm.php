<?php

namespace Drupal\fer_departments\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Fer departments settings for this site.
 */
class FerDepSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fer_departments_fer_dep_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['fer_departments.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $url1 = empty($this->config('fer_departments.settings')->get('department_1'))
      ? 'finance' : preg_replace('/ /', '_', $this->config('fer_departments.settings')->get('department_1'));
    $url1 = strtolower($url1);
    $url2 = empty($this->config('fer_departments.settings')->get('department_2'))
      ? 'it' : preg_replace('/ /', '_', $this->config('fer_departments.settings')->get('department_2'));
    $url2 = strtolower($url2);
    $url3 = empty($this->config('fer_departments.settings')->get('department_3'))
      ? 'consulting' : preg_replace('/ /', '_', $this->config('fer_departments.settings')->get('department_3'));
    $url3 = strtolower($url3);
    $url4 = empty($this->config('fer_departments.settings')->get('department_4'))
      ? '' : preg_replace('/ /', '_', $this->config('fer_departments.settings')->get('department_4'));
    $url4 = strtolower($url4);
    $url5 = empty($this->config('fer_departments.settings')->get('department_5'))
      ? '' : preg_replace('/ /', '_', $this->config('fer_departments.settings')->get('department_5'));
    $url5 = strtolower($url5);
    $form['department_1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department 1'),
      '#default_value' => empty($this->config('fer_departments.settings')->get('department_1'))
        ? 'Finance' : $this->config('fer_departments.settings')->get('department_1'),
      '#description' => $this->t('Please use this link for registration form: <a href="@form-link" target="_blank">
        Form link</a>', ['@form-link' => '/registration/'.$url1])
    ];
    $form['department_2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department 2'),
      '#default_value' => empty($this->config('fer_departments.settings')->get('department_2'))
        ? 'IT' : $this->config('fer_departments.settings')->get('department_2'),
      '#description' => $this->t('Please use this link for registration form: <a href="@form-link" target="_blank">
        Form link</a>', ['@form-link' => '/registration/'.$url2])
    ];
    $form['department_3'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department 3'),
      '#default_value' => empty($this->config('fer_departments.settings')->get('department_3'))
        ? 'Consulting' : $this->config('fer_departments.settings')->get('department_3'),
      '#description' => $this->t('Please use this link for registration form: <a href="@form-link" target="_blank">
        Form link</a>', ['@form-link' => '/registration/'.$url3])
    ];
    $form['department_4'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department 4'),
      '#default_value' => $this->config('fer_departments.settings')->get('department_4'),
      '#description' => $this->t('Please use this link for registration form: <a href="@form-link" target="_blank">
        Form link</a>', ['@form-link' => '/registration/'.$url4])
    ];
    $form['department_5'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department 5'),
      '#default_value' => $this->config('fer_departments.settings')->get('department_5'),
      '#description' => $this->t('Please use this link for registration form: <a href="@form-link" target="_blank">
        Form link</a>', ['@form-link' => '/registration/'.$url5])
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    //deleting value from array for checking does it exists already
    $unset_dep1 = $this->config('fer_departments.settings')->getRawData();
    unset($unset_dep1['department_1']);
    $unset_dep2 = $this->config('fer_departments.settings')->getRawData();
    unset($unset_dep2['department_2']);
    $unset_dep3 = $this->config('fer_departments.settings')->getRawData();
    unset($unset_dep3['department_3']);
    $unset_dep4 = $this->config('fer_departments.settings')->getRawData();
    unset($unset_dep4['department_4']);
    $unset_dep5 = $this->config('fer_departments.settings')->getRawData();
    unset($unset_dep5['department_5']);

    //checking does value already exists in same array
    if (in_array($form_state->getValue('department_1'), $unset_dep1))
    {
      $form_state->setErrorByName('department_1',
        $this->t('This department already exists. Please enter another department'));
    }
    if (in_array($form_state->getValue('department_2'), $unset_dep2))
    {
      $form_state->setErrorByName('department_2',
        $this->t('This department already exists. Please enter another department'));
    }
    if (in_array($form_state->getValue('department_3'), $unset_dep3))
    {
      $form_state->setErrorByName('department_3',
        $this->t('This department already exists. Please enter another department'));
    }
    if (in_array($form_state->getValue('department_4'), $unset_dep4))
    {
      $form_state->setErrorByName('department_4',
        $this->t('This department already exists. Please enter another department'));
    }
    if (in_array($form_state->getValue('department_5'), $unset_dep5))
    {
      $form_state->setErrorByName('department_5',
        $this->t('This department already exists. Please enter another department'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('fer_departments.settings')
      ->set('department_1', $form_state->getValue('department_1'))
      ->set('department_2', $form_state->getValue('department_2'))
      ->set('department_3', $form_state->getValue('department_3'))
      ->set('department_4', $form_state->getValue('department_4'))
      ->set('department_5', $form_state->getValue('department_5'))
      ->save();
    //Clearing cache is MUST because file fer/src/EventSubscriber/FerRouteSubscriber.php is making new yml file
    drupal_flush_all_caches();
    parent::submitForm($form, $form_state);
  }

}
