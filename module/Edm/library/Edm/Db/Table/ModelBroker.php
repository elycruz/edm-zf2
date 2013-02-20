<?php

/**
 * A simple model broker which stores models
 * and lazy loads them if they're not already instantiated.
 * @todo refactor this class to use the simple broker interface
 */
class Edm_Db_Table_ModelBroker
{
    /**
     * Std class to hold models
     * @var stdClass
     */
    protected static $_models = null;

    /**
     * Adds a model object
     * @param Edm_Db_AbstractTable $model
     * @return void
     */
    public static function addModel(
            Edm_Db_AbstractTable $model, $alias)
    {
        $models = self::getStack();
        $alias = self::normalizeModelName($alias);
        if (!isset($models->{$alias})) {
            $models->{$alias} = $model;
        }
        return;
    }

    /**
     * resetModels()
     * @return void
     */
    public static function resetModels()
    {
        self::$_models = null;
        return;
    }

    /**
     * Is a particular model loaded in the broker?
     * @param  string $name
     * @return boolean
     */
    public static function hasModel($name)
    {
        $models = self::getStack();
        $name = self::normalizeModelName($name);
        return isset($models->{$name});
    }

    /**
     * Remove a particular model from the broker
     * @param  string $name
     * @return boolean
     */
    public static function removeModel($name)
    {
        $models = self::getStack();
        $name = self::normalizeModelName($name);

        if (isset($models->{$name})) {
            unset($models->{$name});
            return true;
        }
        
        return false;
    }

    /**
     * Lazy load the priority models and return it
     * @return Edm_Db_Table__ModelBroker_PriorityStack
     */
    public static function getStack()
    {
        if (empty(self::$_models)) {
            self::$_models = new stdClass();
        }

        return self::$_models;
    }

    /**
     * getModel() - get model by name
     *
     * @param  string $name
     * @return Edm_Db_AbstractTable
     */
    public static function getModel($name)
    {
        $models = self::getStack();
        $name = self::normalizeModelName($name);

        if (!isset($models->{$name}) 
            && self::modelClassExists($name)) {
            self::_loadModelByName($name);
        }

        return $models->{$name};
    }

    /**
     * Takes a normalized alias name and fetches the model for it
     * @param type $name
     * @return Edm_Db_AbstractTable 
     */
    protected static function _loadModelByName($name)
    {
        $model = '';
        $className = 'Model_' . $name;

        try {
            $model = new $className();
        }
        catch (Exception $e) {
            throw new Exception('Instantion/retrieval of model name "'.
                    $className .'" failed.  Error received:'. $e->message);
        }
        if (!$model instanceof Edm_Db_AbstractTable) {
            throw new Exception('Model is not an instance of Edm_Db_AbstractTable.');
        }
        
        self::getStack()->{$name} = $model;
        
        return $model;
    }

    /**
     * Takes a table alias name and converts it to a class name; I.e.,
     *      hello-world (model) to HelloWorld
     * @param string $name
     * @return string
     */
    public static function normalizeModelName($name) {
        if (is_string($name)) {
            $name = str_replace(array('-', '_'), ' ', $name);
            $name = ucwords($name);
            $name = str_replace(' ', '', $name);
            return $name;
        }
        throw new Exception('Only strings are allowed for the db table '.
                'broker\'s  normalize name function.');
    }
    
    public static function modelClassExists($aliasName) {
        $aliasName = self::normalizeModelName($aliasName);
        try {
            return self::_loadModelByName($aliasName);
        }
        catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Retrieve model by name as object property
     * @param  string $name
     * @return Edm_Db_AbstractTable
     */
    public function __get($name)
    {
        return self::getModel($name);
    }
}
