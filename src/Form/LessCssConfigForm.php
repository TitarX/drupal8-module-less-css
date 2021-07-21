<?php

namespace Drupal\less_css\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class LessCssConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('less_css.settings');

    $form['#method'] = 'post';

    $form['regenerate-checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Регенировать файлы CSS'),
      '#description' => $this->t(
          'Установите флажок, если требуется регенерировать файлы CSS при каждом запросе страниц.'
        )
        . '<div>'
        . '<strong>'
        . $this->t(
          'При включении этой опции, следующие модули будут отключены:'
        )
        . '</strong>'
        . '<ul>'
        . '<li>Internal Page Cache (page_cache);</li>'
        . '<li>Internal Dynamic Page Cache (dynamic_page_cache).</li>'
        . '</ul>'
        . '</div>',
      '#default_value' => $config->get('regenerate'),
    ];

    $form['compress-checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Сжимать файлы CSS'),
      '#description' => $this->t(
        'Установите флажок, если файлы CSS должны быть минимизированы.'
      ),
      '#default_value' => $config->get('compress'),
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Сохранить',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'less_css_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['less_css.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $regenerate_value = $form_state->getValue('regenerate-checkbox');
      $compress_value = $form_state->getValue('compress-checkbox');

      if ($regenerate_value) {
        $this->cacheModulesControl('page_cache');
        $this->cacheModulesControl('dynamic_page_cache');
      }

      $this->config('less_css.settings')
        ->set('regenerate', $regenerate_value)
        ->set('compress', $compress_value)
        ->save();

      $status_message = $this->t('Настройки успешно сохранены');
      drupal_set_message($status_message, 'status');
    } catch (Exception $e) {
      $error_message = $e->getMessage();
      drupal_set_message($error_message, 'error');
    }
  }

  private function cacheModulesControl($module_name) {
    $moduleHandler = \Drupal::service('module_handler');
    if ($moduleHandler->moduleExists($module_name)) {
      \Drupal::service('module_installer')->uninstall([$module_name]);
    }
  }

}
