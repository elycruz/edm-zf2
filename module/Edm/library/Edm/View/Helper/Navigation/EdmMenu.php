<?php
/**
 * @todo redux render menu function here
 * @author ElyDeLaCruz
 */
class Edm_View_Helper_Navigation_EdmMenu
    extends Zend_View_Helper_Navigation_Menu
{
    public function  edmMenu(Zend_Navigation_Container $container = null) {
        return parent::menu($container);
    }

    /**
     * Renders a normal menu (called from {@link renderMenu()})
     *
     * @param  Zend_Navigation_Container $container   container to render
     * @param  string                    $ulClass     CSS class for first UL
     * @param  string                    $indent      initial indentation
     * @param  int|null                  $minDepth    minimum depth
     * @param  int|null                  $maxDepth    maximum depth
     * @param  bool                      $onlyActive  render only active branch?
     * @return string
     */
    protected function _renderMenu(Zend_Navigation_Container $container,
                                   $ulClass,
                                   $indent,
                                   $minDepth,
                                   $maxDepth,
                                   $onlyActive)
    {
//        $this->_renderUlMenu($container,
//                $ulClass,
//                $indent,
//                $minDepth,
//                $maxDepth,
//                $onlyActive);
        $html = '';

        // find deepest active
        if ($found = $this->findActive($container, $minDepth, $maxDepth)) {
            $foundPage = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
                            RecursiveIteratorIterator::SELF_FIRST);

        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        $i = $j = 0;
        
        $count = count($container);
        
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibilty
                $j = 0;
                continue;
            } else if ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } else if ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages() ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);

            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $depth ==  0) {
                    $ulClass = ' class="' . $ulClass . '"';
                } else {
                    $ulClass = '';
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . self::EOL;
            } else if ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . self::EOL;
                    $html .= $ind . '</ul>' . self::EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            }

            // render li tag and page
            // Changing this to allow styling if 'first' or 'last' element
            // If li is last or first add the 'first' || 'last' css class
            
            $liClass = '';
            
//            if ($j >= ($count - 1) * $depth) {
//                $liClass .= 'last';
//            }
            
            if ($j == 0) {
               $liClass .= 'first'; 
            }
            
            if ($isActive == true) {
                if ($liClass == '') {
                    $liClass .= 'active';
                }
                else {
                    $liClass .= ' active';
                }
            }
            
            if ($liClass !== '') {
                $liClass = ' class="'. $liClass .'"';
            }
            
            $html .= $myIndent . '    <li' . $liClass . '>' . self::EOL
                   . $myIndent . '        ' . $this->htmlify($page) . self::EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
            $j += 1;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth+1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i-1);
                $html .= $myIndent . '    </li>' . self::EOL
                       . $myIndent . '</ul>' . self::EOL;
            }
            $html = rtrim($html, self::EOL);
        }

        return $html;
    }


    /**
     * Edited the `Zend_View_Helper_Navigation_Menu` htmlify function to
     * add a `span` element around the `label`'s text
     * Original phpdoc from `Zend_View_Helper_Navigation_Menu`:
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link Zend_View_Helper_Navigation_Abstract::htmlify()}.
     *
     * @param  Zend_Navigation_Page $page  page to generate HTML for
     * @return string                      HTML string for the given page
     */
    public function  htmlify(Zend_Navigation_Page $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // translate label and title?
        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass()
        );

        // does page have a href?
        if ($href = $page->getHref()) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        return '<' . $element . $this->_htmlAttribs($attribs) . '><span>'
             . $this->view->escape($label)
             . '</span></' . $element . '>';
    }
    
    protected function _renderUlMenu($container,
                $ulClass = null,
                $indent = null,
                $minDepth = null,
                $maxDepth = null,
                $onlyActive = null) {
        
        // Start Ul element
        $out = '<ul';
        
        // Ul class
        if ($ulClass) {
            $out .= ' class="' . $ulClass .'"';
        }
        
        // Close Ul opening tag
        $out .= '>';
        
        // Loop container Pages
        foreach($container->getPages() as $page) {
            $out .= '<li><a href="'. $page->controller .'"><span>' . 
                    $page->label . '</span></a>';
                    
            if ($page->hasPages()) {
                $this->_renderUlMenu($page);
            }
            
            $out .= '</li>';
        }
        
        $out .= '</ul>';
        echo $out;
        exit();
    }
    
}
