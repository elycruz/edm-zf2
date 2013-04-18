<?php
/**
 * Description of PostIndexService
 *
 * @author ElyDeLaCruz
 */
class Edm_Service_Internal_PostIndexService 
{
    /**
     * Database Data Helper
     * @var Edm_Db_DbDataHelper
     */
    protected $_dbDataHelper;
    protected $_index;
    
    
    public function __construct(Edm_Db_DbDataHelper $dbDataHelper) {
        $this->_dbDataHelper = $dbDataHelper;
        $this->buildInitialIndex();
    }
    
    public function buildInitialIndex(array $rslt = null) {
        if (!empty($rslt)) {
            $index = $this->getIndex('create');
            foreach ($rslt as $tuple) {
                $tuple = $this->_dbDataHelper->reverseEscapeTupleFromDb($tuple);
                $index->addDocument($this->getIndexDocFromTuple($tuple));
            }
        }
    }
    
    /**
     * Returns the Edm Indexes in an array
     * @param string $context
     * @return array with 2 Zend_Search_Lucene_Interface objects:
     *      one for edm-default and 
     *      one for edm-admin
     *      These are both specified by:
     *           EDM_ADMIN_INDEX_PATH and 
     *           EDM_DEFAULT_INDEX_PATH
     *           respectively
     */
    public function getIndex($context) 
    {
        if (empty($this->_index)) {
            switch($context) {
                case 'open' :
                    $output = Zend_Search_Lucene::open(EDM_DEFAULT_INDEX_PATH);
                    break;
                case 'create' : 
                default: 
                    $output = Zend_Search_Lucene::create(EDM_DEFAULT_INDEX_PATH);
                    break;
            }
            $this->_index = $output;
        }
        return $this->_index;
    }
    
    public function createIndexEntry($tuple) {
        $index = $this->getIndex('open');
        $index->addDocument($this->getIndexDocFromTuple($tuple));
    }

    public function readFromIndex($string) {
        $index = $this->getIndex('open');
        return $index->find($string);
    }

    public function updateIndexEntry($tuple) {
        $hits = $index->find('post_id:' . $tuple->post_id);
        foreach ($hits as $hit) {
            $index->delete($hit->id);
        }
        
        $this->createIndexEntry($tuple);
    }

    public function deleteIndexEntry($tuple) {
        
    }
    
    public function getIndexDocFromTuple($tuple) {
        //$tuple = (object) $tuple;
        $meta = '';
        $title = $tuple['title'];
        $content = $tuple['content'];
        foreach ($tuple as $key => $val) {
            if ($key == 'content' || $key == 'excerpt') {
                continue;
            }
            $meta .= '<meta name="'. $key .'" content="'. $val .'" />';
        }
        
        $htmlString = <<<HTML
<html>
    <head>
        <title>{$title}</title>
        {$meta}
    </head>
    <body>{$content}</body>
</html>
HTML;
        return Zend_Search_Lucene_Document_Html::loadHTML($htmlString, true);
    }
    
}