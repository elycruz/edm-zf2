<?php

/**
 * @note Proto's shouldn't be nested more than one level deep.
 * @todo Consider adding primary key and foreign key fields to this proto 
 * to help ease the definition/building of sql select statements and other CRUD
 * operations in services.
 */

namespace Edm\Db\ResultSet\Proto;

use Zend\Config\Config,
    Edm\InputFilter\DefaultInputOptions,
    Edm\InputFilter\DefaultInputOptionsAware,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface;

/**
 * Abstract result set prototype.
 * @author ElyDeLaCruz
 */
abstract class AbstractProto extends \ArrayObject
    implements
    ProtoInterface,
    DefaultInputOptionsAware,
    InputFilterAwareInterface {

    /**
     * Default input options
     * @var \Edm\InputFilter\DefaultInputOptions
     */
    protected static $defaultInputOptions;
    
    /**
     * Valid keys allowed for proto.
     * @var array
     */
    protected $_allowedKeysForProto;
    
    /**
     * Not allowed for RDBMS updates.
     * @var array
     */
    protected $_notAllowedKeysForUpdate;

    /**
     * Keys that should be unset before exporting (toArray) array to db;
     * @var array
     */
    protected $_notAllowedKeysForInsert;

    /**
     * Proto names to use when calling to array to generate values.
     * @var array
     */
    protected $_subProtoGetters;

    /**
     * Input Filter.
     * @var \Zend\InputFilter\InputFilterInterface
     */
    protected $_inputFilter = null;

    /**
     * Key used when returning from `toArray` for form usage.
     * @var string
     */
    protected $_formKey;
    
    /**
     * A place to store current values in proto for use in comparisons and other
     * operations.  Allows user to call `storeSnapshot` to store it's current 
     * `allowedKeysForProto` values inside an array.
     * @var array
     */
    protected $_snapshotValues = [];
    
    /**
     * Same as `$_snapshotValues` but for proto's own self and it's sub protos.
     * @var array
     */
    protected $_nestedSnapshotValues = [];

    /**
     * Constructor
     * @param array $data
     * @param int $flags
     */
    public function __construct(array $data = null, $flags = 0) {
        parent::__construct([], $flags == 0 ? \ArrayObject::ARRAY_AS_PROPS : $flags);
        $this->exchangeArray(is_array($data) ? $data : []);
    }

    /**
     * @return string
     */
    public function getFormKey() {
        return $this->_formKey;
    }

    /**
     * Get models valid keys
     * @return array
     */
    public function getAllowedKeysForProto () {
        return $this->_allowedKeysForProto;
    }

    /**
     * Returns a list of fields not allowed for RDBMS update.
     * @return array
     */
    public function getNotAllowedKeysForUpdate () {
        return $this->_notAllowedKeysForUpdate;
    }

    /**
     * Returns a list of fields not allowed for database (should be keys not in defined database).
     * @return array
     */
    public function getNotAllowedKeysForInsert () {
        return $this->_notAllowedKeysForInsert;
    }

    /**
     * @return array
     */
    public function getSubProtoGetters() {
        return $this->_subProtoGetters;
    }

    /**
     * Returns model as array with only set values.
     * Any fields which do not have set values won't be returned in the array.
     * @param string $operation - Operation [Update,Insert,Db,Form].
     *  If set removes the 'notAllowedForUpdate', 'notAllowedForInsert',
     *  'notAllowedKeysForDb' keys from the exported array.
     * @return array
     */
    public function toArray ($operation = null) {
        $outArray = [];
        foreach ($this->_allowedKeysForProto as $key) {
            if (!$this->has($key)) {
                continue;
            }
            $outArray[$key] = $this->{$key};
        }

        // Filter Based on Operation here
        return $this->filterArrayBasedOnOp($outArray, $operation);
    }

    /**
     * @param string $operation
     * @return array
     */
    public function toNestedArray ($operation = null) {
        // Declare out array
        $outArray = [];

        // Get self out
        $selfOut = $this->toArray($operation);

        // For each in sub protos nest their arrays
        $this->forEachInSubProtos(function (AbstractProto $subProto) use (&$outArray, $operation) {
            $outArray[$subProto->getFormKey()] = $subProto->toArray($operation);
        });

        // Filter Based on Operation here
        $outArray = $this->filterArrayBasedOnOp($outArray, $operation);

        // Set self on out array
        $outArray[$this->getFormKey()] = $selfOut;

        // Return array
        return $outArray;
    }

    /**
     * @param array $array
     * @param string $operation
     * @return array
     * @throws \Exception
     */
    public function filterArrayBasedOnOp ($array, $operation = null) {
        // If operation is not set then return the unfiltered array
        if (!isset($operation)) {
            return $array;
        }

//        // Ensure operation is one of ours else throw an exception
//        if (   $operation !== AbstractProto::FOR_OPERATION_DB
//            || $operation !== AbstractProto::FOR_OPERATION_DB_INSERT
//            || $operation !== AbstractProto::FOR_OPERATION_DB_UPDATE
//            || $operation !== AbstractProto::FOR_OPERATION_DB_FORM
//            ) {
//            throw new \Exception('"' . $operation .'" is not one of the defined operations ' .
//                'for the `toArray` method of the `'. __CLASS__ . '` class.');
//        }

        // Get array keys to filter against
        $notAllowedForOp = $this->{'_notAllowedKeysFor' . $operation};

        // Return filtered array
        if (isset($notAllowedForOp)) {
            $retVal = array_filter($array, function ($key) use ($notAllowedForOp) {
                return !in_array($key, $notAllowedForOp);
            }, ARRAY_FILTER_USE_KEY);
        }
        else {
            $retVal = $array;
        }

        return $retVal;
    }

    /**
     * Check if key exists on object.
     * @param $key string
     * @return bool
     */
    public function has ($key) {
        return isset($this->{$key});
    }

    /**
     * @param array $input
     * @return array
     */
    public function exchangeArray ($input) {
        $oldArray = $this->toArray();
        $this->forEachInSubProtos(function ($subProto) use ($input){
            $this->setAllowedKeysOnProto($input, $subProto);
        });
        $this->setAllowedKeysOnProto($input);
        return $oldArray;
    }

    /**
     * @param array $input
     * @return array
     */
    public function exchangeNestedArray ($input) {
        $oldArray = $this->toNestedArray();
        $this->forEachInSubProtos(
            /**
             * @param AbstractProto $subProto
             */
            function (AbstractProto $subProto) use ($input) {
                $formKey = $subProto->getFormKey();
                if (isset($input[$formKey])) {
                    $this->setAllowedKeysOnProto($input[$formKey], $subProto);
                }
            });
        $this->setAllowedKeysOnProto($input[$this->getFormKey()]);
        return $oldArray;
    }

    /**
     * @param array $inputData
     * @param ProtoInterface $proto
     * @return ProtoInterface $proto
     */
    public function setAllowedKeysOnProto($inputData, $proto = null) {
        $proto = $proto === null ? $this : $proto;
        $validKeys = $proto->getAllowedKeysForProto();
        foreach ($validKeys as $key) {
            if (isset($inputData[$key])) {
                $proto->{$key} = $inputData[$key];
            }
        }
        return $proto;
    }

    /**
     * @param Config $options
     */
    public static function setDefaultInputOptions (Config $options) {
        self::$defaultInputOptions = $options;
    }

    /**
     * @return DefaultInputOptions
     */
    public static function getDefaultInputOptions () {
        if (empty(self::$defaultInputOptions)) {
            self::$defaultInputOptions = new DefaultInputOptions();
        }
        return self::$defaultInputOptions;
    }

    /**
     * Gets default shared input options
     * @param string $key
     * @param array $defaults
     * @return array
     */
    public static function getDefaultInputOptionsByKey ($key, array $defaults = null) {
        $retVal = null;
        $options = self::getDefaultInputOptions();

        // Get the offset for key
        if ($options->offsetExists($key)) {
            $retVal = $options->get($key);
            $output = array();
            if (!empty($retVal)) {
                if ($retVal->offsetExists('validators')) {
                    $output['validators'] = $retVal->validators->toArray();
                }
                if ($retVal->offsetExists('filters')) {
                    $output['filters'] = $retVal->filters->toArray();
                }
                $retVal = $output;
            }
        }

        // Merge return value with defaults if necessary
        if (!empty($defaults) && is_array($retVal)) {
            $retVal = array_replace($retVal, $defaults);
        }

        return $retVal;
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return \Zend\InputFilter\InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        $this->_inputFilter = $inputFilter;
        return $this;
    }

    /**
     * @return \Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter () {
        return $this->_inputFilter;
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function forEachInSubProtos (callable $callback) {
        $out = [];
        if (isset($this->_subProtoGetters) && is_array($this->_subProtoGetters)) {
            foreach ($this->_subProtoGetters as $getter) {
                $subProto = $this->{$getter}();
                call_user_func($callback, $subProto);
                $out[] = $subProto;
            }
        }
        return $out;
    }
    
    /**
     * Stores snapshot of own 'allowed' values.
     * @param string $operation - ['Update', 'Insert']
     * @see \Edm\Db\ResultSet\Proto\ProtoInterface constants
     * @return \Edm\Db\ResultSet\Proto\AbstractProto
     */
    public function storeSnapshot ($operation = null) {
        $this->_snapshotValues = $this->toArray($operation);
        return $this;
    }
    
    /**
     * Stores snapshot of own 'allowed' key-value pairs and it's sub proto
     * key-value pairs.
     * @param string $operation - ['Update', 'Insert']
     * @see \Edm\Db\ResultSet\Proto\ProtoInterface constants
     * @return \Edm\Db\ResultSet\Proto\AbstractProto
     */
    public function storeNestedSnapshot ($operation = null) {
        $this->_nestedSnapshotValues = $this->toNestedArray($operation);
        return $this;
    }
    
    public function getSnapshotValues() {
        return $this->_snapshotValues;
    }

    public function getNestedSnapshotValues() {
        return $this->_nestedSnapshotValues;
    }

}

