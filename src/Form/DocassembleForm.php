<?php

namespace Drupal\docassemble_embed\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class DocassembleForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'docassemble_embed_form';
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form = parent::buildForm($form, $form_state);
        $config = $this->config('docassemble_embed.settings');
        $form['url'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Docassemble server'),
            '#default_value' => $config->get('docassemble_embed.server'),
            '#description' => $this->t('E.g., https://demo.docassemble.org'),
        );
        $form['style'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Default style'),
            '#default_value' => $config->get('docassemble_embed.style'),
            '#description' => $this->t('Default style for embedded interviews'),
        );
        $form['bootstrap'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Bootstrap CSS URL, if using direct embedding'),
            '#default_value' => $config->get('docassemble_embed.bootstrap'),
            '#description' => $this->t('Use a relative URL beginning with / for a file on the Docassemble server, or use an absolute URL, or leave blank if you are providing Bootstrap through the host web site'),
        );
        return parent::buildForm($form, $form_state);
    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {

    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->config('docassemble_embed.settings')
             ->set('docassemble_embed.server', $form_state->getValue('url'))
             ->set('docassemble_embed.style', $form_state->getValue('style'))
             ->set('docassemble_embed.bootstrap', $form_state->getValue('bootstrap'))
             ->save();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
            return [
                'docassemble_embed.settings',
            ];
    }

}