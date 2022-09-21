<?php

namespace Drupal\docassemble_embed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
* Provides a 'Docassemble' Block.
*
* @Block(
*   id = "docassemble_block",
*   admin_label = @Translation("Docassemble interview block"),
*   category = @Translation("Forms"),
* )
*/
class Docassemble extends BlockBase implements BlockPluginInterface {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $global_config = \Drupal::config('docassemble_embed.settings');
        $url = rtrim(trim($global_config->get('docassemble_embed.server')), '/');
        $config = $this->getConfiguration();
        if (!empty($config['docassemble_block_interview'])) {
            $interview = $config['docassemble_block_interview'];
        }
        else{
            $interview = "docassemble.demo:data/questions/questions.yml";
        }
        if (!empty($config['docassemble_block_style'])) {
            $style = $config['docassemble_block_style'];
        }
        else{
            $style = "border-style: solid; border-width: 1px; border-color: #aaa; width: 100%; min-height: 95vh;";
        }
        if ($config['docassemble_block_mode'] == 'iframe'){
            return array(
                '#type' => 'inline_template',
                '#template' => '<iframe style="{{ style }}" src="{{ url }}/interview?i={{ interview }}"></iframe>',
                '#context' => array(
                    'url' => $url,
                    'style' => $style,
                    'interview' => $interview,
                ),
            );
        }
        else {
            if ($config['docassemble_block_wide']){
                $wide = ' dawide';
            }
            else{
                $wide = '';
            }
            if ($config['docassemble_block_hide']){
                $hide = ' dahide-navbar';
            }
            else{
                $hide = '';
            }
            return array(
                '#type' => 'inline_template',
                '#template' => '<div id="dablock" class="dajs-embedded{{ hide }}{{ wide }}" style="{{ style }}"></div>',
                '#context' => array(
                    'hide' => $hide,
                    'wide' => $wide,
                    'style' => $style,
                ),
                '#attached' => array(
                    'library' => array(
                        'fontawesome/fontawesome.svg',
                        'jquery',
                        'docassemble_embed/docassemble-jquery',
                        'docassemble_embed/docassemble-assets',
                        'docassemble_embed/docassemble-interview',
                    ),
                    'drupalSettings' => array('docassembleUrl' => $url . '/interview?i=' . $interview . '&js_target=dablock'),
                ),
            );
        }
    }

    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);
        $default_config = \Drupal::config('docassemble_embed.settings');
        $config = $this->getConfiguration();

        $form['docassemble_block_interview'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Docassemble interview'),
            '#description' => $this->t('E.g., docassemble.demo:data/questions/questions.yml'),
            '#default_value' => isset($config['docassemble_block_interview']) ? $config['docassemble_block_interview'] : $default_config->get('docassemble_embed.interview'),
        ];
        $form['docassemble_block_style'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Style of div'),
            '#description' => $this->t('How should the div be styled?'),
            '#default_value' => isset($config['docassemble_block_style']) ? $config['docassemble_block_style'] : $default_config->get('docassemble_embed.style'),
        ];
        $form['docassemble_block_mode'] = array(
            '#type' => 'radios',
            '#title' => $this->t('Type of embedding'),
            '#default_value' => isset($config['docassemble_block_mode']) ? $config['docassemble_block_mode'] : $default_config->get('docassemble_embed.mode'),
            '#description' => $this->t('Only choose direct embedding if you know what you are doing.'),
            '#options' => array(
                'iframe' => $this->t('Iframe'),
                'div' => $this->t('Direct embedding'),
            ),
        );
        $form['docassemble_block_wide'] = array(
            '#type' => 'checkbox',
            '#title' => t('Interview should fill the width of the container, as if on a small screen (recommended unless the content area is full width)'),
            '#default_value' => (isset($config['docassemble_block_wide']) ? $config['docassemble_block_wide'] : $default_config->get('docassemble_embed.wide')) ? TRUE : FALSE,
            '#return_value' => "checked",
            '#states' => array(
                'visible' => array(
                    ':input[name="settings[docassemble_block_mode]"]' => array('value' => 'div')
                ),
            ),
        );
        $form['docassemble_block_hide'] = array(
            '#type' => 'checkbox',
            '#title' => t('Hide the navigation bar in the interview (recommended; also use the "question back button" feature in your interview)'),
            '#default_value' => (isset($config['docassemble_block_hide']) ? $config['docassemble_block_hide'] : $default_config->get('docassemble_embed.hide')) ? TRUE : FALSE,
            '#return_value' => "checked",
            '#states' => array(
                'visible' => array(
                    ':input[name="settings[docassemble_block_mode]"]' => array('value' => 'div')
                ),
            ),
        );
        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->setConfigurationValue('docassemble_block_interview', $form_state->getValue('docassemble_block_interview'));
        $this->setConfigurationValue('docassemble_block_style', $form_state->getValue('docassemble_block_style'));
        $this->setConfigurationValue('docassemble_block_mode', $form_state->getValue('docassemble_block_mode'));
        $this->setConfigurationValue('docassemble_block_wide', $form_state->getValue('docassemble_block_wide'));
        $this->setConfigurationValue('docassemble_block_hide', $form_state->getValue('docassemble_block_hide'));
    }
}
