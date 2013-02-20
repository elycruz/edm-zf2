<?php
/**
 * DirectoryCrawler.php
 * @author E. De La Cruz
 * @email elydelacruz_112@yahoo.com
 * @desc A simple class for generating directory structure 
 * in array, string, or xml format
 */
class Edm_Util_DirectoryCrawler {

    protected $_directory;
    protected $_iterator;
    protected $_output;
    protected $_recursive;
    protected $_pattern;

    /**
     * Sets the root directory to search from
     * @param String $value
     * @return default
     */
    public function setDirectory($value) {
        $this->_directory = $value;
    }

    /**
     * Sets whether to use recursion or not to search the directories within root directory
     * @param Boolean $value
     * @return default
     */
    public function setRecursive($value) {
        $this->_recursive = $value;
    }

    /**
     * Sets the regular expression pattern that needs to be matched
     * @param Regular Expression String $value
     * @return default
     */
    public function setPattern($value) {
        $this->_pattern = $value;
    }

    public function __construct($directory, $recursive = false, $pattern = null) 
    {
        if (empty($directory)) {
            throw new Exception('The directory value for '.
                    'Edm_Util_DirectoryCrawler cannot be null.');
        }
        
        $this->_directory = $directory;
        $this->_pattern = $pattern;
        $this->_recursive = $recursive;
        $this->_output = '';
    }

    public function __destruct() {
        
    }

    
    /**
     * Returns an xml representation of a directory as a string
     * ** NOTE ** We are manualy crawling through the directory
     * for better performance this should be implement with 
     * RecursiveDirectoryIterator and RecursiveIteratorIterator 
     * @return String
     */
    public function __toString() {
        // Initiate our output variable
        $this->_output =
                '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' . PHP_EOL
                . ' <directoryItems>' . PHP_EOL;

        if ($this->_recursive) {
            // Initiate our iterator
            $this->_iterator = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($this->_directory)
            );

            // Loop through it
            while ($this->_iterator->valid()) {
                if (empty($this->_pattern)) {
                    
                    // Get current item
                    $item = $this->_iterator->current();
                    
                    
                    if ($prevDir != $item->getPath()) {
                        $this->_output .= '  <directory name="' . $item->getPath() . '">' . PHP_EOL;
                        $prevDir = $item->getPath();
                        $dir_open = true;
                    }
                    $this->_output .= '  <file name="' . $item->getFilename() . '" />' . PHP_EOL;
                    $this->_iterator->next();

                    if ($this->_iterator->valid()) {
                        $item = $this->_iterator->current();
                        if ($prevDir != $item->getPath()) {
                            $this->_output .= '  </directory>' . PHP_EOL;
                        }
                    } else {
                        if ($dir_open) {
                            $this->_output .= '  </directory>' . PHP_EOL;
                        }
                    }
                } else {
                    /* $item = $this->_iterator->current();
                      if( $prevDir != $item->getPath() )
                      {
                      $this->_output .= '  <directory name="'. $item->getPath() .'">'. PHP_EOL;
                      $prevDir = $item->getPath();
                      $dir_open = true;
                      }
                      if( preg_match( $this->_pattern['file'], $item->getFilename() ) > 0 )
                      {
                      $this->_output .= '  <file name="'. $item->getFilename() .'" />'. PHP_EOL;
                      }
                      $this->_iterator->next();

                      if( $this->_iterator->valid() )
                      {
                      $item = $this->_iterator->current();
                      if( $prevDir != $item->getPath() )
                      {
                      $this->_output .= '  </directory>'. PHP_EOL;
                      }
                      }
                      else {
                      if( $dir_open ) {
                      $this->_output .= '  </directory>'. PHP_EOL;
                      }
                      } */
                }
            }
        } else {
            $this->_iterator = new DirectoryIterator($this->_directory);

            while ($this->_iterator->valid()) {
                $item = $this->_iterator->current();

                if (!$item->isDot()) {
                    if ($item->isFile()) {
                        $this->_output .= '<file name="' . $item->getFilename() . '" />' . PHP_EOL;
                    } else if ($item->isDir()) {
                        $this->_output .= '<directory name="' . $item->getFilename() . '" />' . PHP_EOL;
                    }
                }
                $this->_iterator->next();
            }
        }

        $this->_output .= ' </directoryItems>' . PHP_EOL;
        return $this->_output;
    }

    /**
     * Returns our iterator that is being used by this
     * @return Iterator
     */
    public function getIterator() 
    {
        // Initiate our iterator
        if (!empty($this->_recursive)) {
            $it = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($this->_directory)
            );
        }
        else {
            $it = new RecursiveDirectoryIterator($this->_directory);
        }
        
        return $this->_iterator = $it;
    }

}