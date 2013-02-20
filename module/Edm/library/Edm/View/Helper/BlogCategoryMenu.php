<?php
/**
 * @author ElyDeLaCruz
 * @todo update to have hierarchical inheritance for rendering.
 */
class Edm_View_Helper_BlogCategoryMenu extends
Edm_View_Helper_Abstract
{
    protected $_output = '';
    
    /**
     * Will be set when output is passed as one 
     * of the keys in the options array.
     * @param string $value 
     * @return Edm_View_Helper_PlainHtmlModule
     */
    public function setOutput($value) {
        $this->_output .= (string) $value;
        return $this;
    }
    
    /**
     * Renders a Plain Html Module
     * @param array $options
     * @param Zend_View_Abstract $view
     * @return string $output
     */
    public function blogCategoryMenu(array $options,
            Zend_View_Abstract $view = null)
    {
        // Set valid key names here.  Options that you would like overridable
        // should be listed within this array.
        $this->setValidKeyNames(array('tuple', 'attributes'));

        // Validate the option key names
        $this->validateKeyNames($options);
        
        // Set tuple to options
        $this->setOptions((array)$options['tuple']);
        
        // Passed in options overide options set in our tuple
        $this->setOptions($options);

        // Html class
        if (!empty($this->html_class)) {
            // Add class attribute to the attributes array
            $this->attributes = is_array($this->attributes) ?
                    array_merge($this->attributes,
                        array('class' => $this->html_class)) :
                            array('class' => $this->html_class);
        }
        // Html id
        if (!empty($this->html_id)) {
            // Add id attribute to the attributes array
            $this->attributes = is_array($this->attributes) ?
                    array_merge($this->attributes, 
                            array('id' => $this->html_id)) :
                            array('id' => $this->html_id);
        }

        // Attributes compilation variable
        $attributes = '';
        
        // Check to see if we need to add any attributes to this html module
        if (is_array($this->attributes) &&
                count($this->attributes) > 0) {
            
            // Loop through attributes and turn them into html attributes
            foreach ($this->attributes as $key => $val) {
                $attributes .= ' '. strtolower($key) .'="'. $val .'"';
            }
        }
        
        // Set the attributes to be used in populating our html module
        $this->attributes = $attributes;
     
        // Module
        $output = $this->_output;
        $output .= '<div'. $this->attributes .'>';
           
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
        
        // BEGIN BLOG CATEGORIES
        // Content
        $termTaxServ = Edm_Service_Internal_TermTaxonomyService::getInstance();
        $categories = $termTaxServ
                            ->getDescendantsByAlias('blog', 'post-category');
        $pages = array();
        
        // Loop through categories and 
        // create a page for each one
        foreach($categories as $category) {
            $pages[] = array(
                'label' => $category->name,
                'privilege' => 'index',
                'resource' => 'blog',
                'route' => 'front-end-blog',
                'title' => $category->name .' link',
                'params' => array(
                    'categoryAlias' => $category->alias
                )
            );
        }
        
        // Get zend nav object
        $nav = new Zend_Navigation($pages);
        
        // Render menu
        $output .=
                $this->view
                ->navigation()
                ->findHelper('EdmMenu')
                ->setMinDepth(0)
                ->setMaxDepth(3)
                ->setUlClass('vert-menu-fl sub-menu')
                ->renderMenu($nav);
        
        $output .= $this->content;
        // END BLOG CATEGORIES
        
                
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

        // Close module div
        $output .= '</div><!--/.module-->';

        // Return output to view
        return $output;

    } // end render   
    
    
}
