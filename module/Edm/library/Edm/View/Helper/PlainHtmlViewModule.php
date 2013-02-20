<?php
/**
 * @author ElyDeLaCruz
 * @todo update to have hierarchical inheritance for rendering.
 */
class Edm_View_Helper_PlainHtmlViewModule extends
Edm_View_Helper_Abstract
{
    /**
     * Default options array
     * @var array
     */
    protected $_defaults = array(
        'html_class' => '',
        'html_id' => '',
        'htmlAttribs' => '',
        'beveled' => false,
        'useModuleTitle' => true,
        'showHeaderDiv' => true,
        'showBodyDiv' => true,
        'showContentDiv' => true,
        'showFooterDiv' => true,
        'headerContent' => '',
        'bodyContent' => '',
        'content' => '',
        'footerContent' => '',
        'headerTag' => 'h3',
        'headerHtml_class' => 'header',
        'bodyHtml_class' => 'body',
        'contentHtml_class' => 'content',
        'footerHtml_class' => 'footer',
        
        
    );
    
    /**
     * Renders a Plain Html Module
     * @param array $options
     * @param Zend_View_Abstract $view
     * @return string $output
     */
    public function plainHtmlViewModule(array $options,
            Zend_View_Abstract $view = null)
    {
        if (!empty($view)) {
            $this->setView($view);
        }
        
        // Set default options since the view helper is stored for later use
        // and keeps what ever values it has.
        $this->resetOptions();
        
        if (!empty($options['tuple'])) {
            // Set tuple to options
            $this->setOptions((array)$options['tuple']);
        }
        
        // Passed in options overide options set in our tuple
        $this->setOptions($options);
        
        if (!empty($this->userParams)) {
            $this->setOptionsFromUserParams($this->userParams);
        }

        // Html class
        if (!empty($this->html_class)) {
            // Add class attribute to the htmlAttribs array
            $this->htmlAttribs = is_array($this->htmlAttribs) ?
                    array_merge($this->htmlAttribs,
                        array('class' => $this->html_class)) :
                            array('class' => $this->html_class);
        }
        
        // Html id
        if (!empty($this->html_id)) {
            // Add id attribute to the htmlAttribs array
            $this->htmlAttribs = is_array($this->htmlAttribs) ?
                    array_merge($this->htmlAttribs, 
                            array('id' => $this->html_id)) :
                            array('id' => $this->html_id);
        }

        // Attributes compilation variable
        $htmlAttribs = '';
        
        // Check to see if we need to add any htmlAttribs to this html module
        if (is_array($this->htmlAttribs) &&
                count($this->htmlAttribs) > 0) {
            
            // Loop through htmlAttribs and turn them into html htmlAttribs
            foreach ($this->htmlAttribs as $key => $val) {
                $htmlAttribs .= ' '. strtolower($key) .'="'. $val .'"';
            }
        }
        
        // Set the htmlAttribs to be used in populating our html module
        $this->htmlAttribs = $htmlAttribs;
     
        // Module
        $output = '<div'. $this->htmlAttribs .'>';
        
        if (!empty($this->beveled)) {
            $output .= '<div class="bevel">';
        }
        
        // Header
        if (!empty($this->showHeaderDiv)) {
            
            // Use module title
            if (!empty($this->useModuleTitle)) {
                if (!empty($this->headerContent)) {
                    $tmp = $this->headerContent;
                    $this->headerContent = '<'. $this->headerTag .'>'. 
                            $this->title .'</'. $this->headerTag .'>'. $tmp;
                }
                else {
                    $this->headerContent = '<'. $this->headerTag .'>'. 
                            $this->title .'</'. $this->headerTag .'>';
                }
            }
            else if (empty($this->headerContent)) {
                $this->headerContent = '<'. $this->headerTag .'>' . 
                        $this->title . '</'. $this->headerTag .'>';
            }
            
            // Set header div
            $output .= '<div class="' . $this->headerHtml_class . '">' .
                   $this->headerContent . '</div>';
        }

        // Body Div Start
        if (!empty($this->showBodyDiv)) {
            $output .= '<div class="'. $this->bodyHtml_class .'">';

            // Body Content
            if (!empty($this->bodyContent)) {
                $output .= $this->bodyContent;
            } 
        }  
        
        // Content Div Start
        if (!empty($this->showContentDiv)) {
            $output .= '<div class="' . $this->contentHtml_class . '">';
        }
        
        // Content
        $output .= $this->content;
                
        // Content div close
        if (!empty($this->showContentDiv)) {
            $output .= '</div>';
        }

        // Body div close 
        if (!empty($this->showBodyDiv)) {
            $output .= '</div>';
        }

        // Footer div
        if (!empty($this->showFooterDiv)) {
            $output .= '<div class="' . $this->footerHtml_class .'">' .
                    $this->footerContent . '</div>';
        }

        if (!empty($this->beveled)) {
            $output .= '</div>';
        }
        
        // Close module div
        $output .= '</div><!--/.module-->';
        
        $this->appendHeadStuffToHead();
        
        // Return output to view
        return $output;

    } // end render    
    
    /**
     * Resets this view helper's options to the default values 
     * since it is stored and reused in different views/view scripts.
     * So calling this function from the top of our view helper function
     * ensures we have default values each time it is reused from within views
     * @return void 
     */
    public function resetOptions() {
        $this->setOptions($this->_defaults);
    }
    
    
    protected function appendHeadStuffToHead() {
        $view = Zend_Layout::getMvcInstance();
        $view = $view->getView();
        if (!empty($this->script0)) {
            for ($i = 0; $i < 10; $i += 1) {
                $scriptVar = 'script' . $i;
                $stylesheetVar = 'stylesheet' . $i;
                if (!empty($this->{$scriptVar})) {
                    $view->headScript()
                            ->appendFile($this->{$scriptVar});
                }
                if (!empty($this->{$stylesheetVar})) {
                    $view->headLink()
                            ->appendStylesheet($this->{$stylesheetVar});
                }
            } // for
        } // if
    } // end
}
