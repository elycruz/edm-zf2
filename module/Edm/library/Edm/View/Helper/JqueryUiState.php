<?php
/**
 * Description of JqueryUiState
 *
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_JqueryUiState extends Edm_View_Helper_Abstract
{
    /**
     * Returns the markup for a jquery ui template
     * to the user;  I.e. stores complex markup that represents error/highlight
     * ui states
     * @param <type> $name
     * @param <type> $options
     * @return string
     */
    public function jqueryUiState(array $options)
    {
        /**
         * Set valid key names for $this->searchParams and $options
         */
        $this->setValidKeyNames(array(
            'messages', 'uiState'
        ));

        /**
         * Validate key names
         */
        $this->validateKeyNames($options);

        /**
         * Set options
         */
        $this->setOptions($options);

        /**
         * If there are no messages bail
         */
        $messageCount = count($this->messages);
        if (empty($messageCount)) {
            return '';
        }

        /**
         * Turn meessages to markup
         */
        $messages = $this->_messagesToMarkup();
        
        /**
         * Set the output
         */
        $output = '';
        switch ($this->uiState) {
            case 'highlight':
                $output = <<<HTML
            <div class="ui-state-highlight ui-corner-all flash-message tl grid_16">
                <p class="m10px"><span class="ui-icon ui-icon-info"></span>
                {$messages}</p>
            </div>
HTML;
                break;
            case 'error':
                $output =  <<<HTML
            <div class="ui-state-error ui-corner-all flash-message tl grid_16">
                <p class="m10px"><span class="ui-icon ui-icon-alert fl"></span>
                    {$messages}</p>
            </div>
HTML;
                break;
            default:
                $output = <<<HTML
            <div class="ui-state-highlight ui-corner-all flash-message tl grid_16">
                <p class="m10px"><span class="ui-icon ui-icon-info fl"></span>
                    {$messages}</p>
            </div>
HTML;
                break;
        }
        return $output;
    }

    protected function _messagesToMarkup()
    {
        $output = '';
        if (count($this->messages) > 1) {
            $output = '<ul>';
            foreach($this->messages as $msg) {
               $output .= '<li>'. $msg .'</li>';
            }
            $output .= '</ul>';
        }
        else {
            $output = $this->messages[0];
        }
        return $output;
    }

}