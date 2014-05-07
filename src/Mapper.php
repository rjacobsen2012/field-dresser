<?php

use Factories\MapperFactory;
use Helpers\ConfigHelper;
use Handlers\ResponseHandler;

class Mapper
{

    public $dbconfig;

    public $mapper;

    /**
     * @param      $type
     * @param      $host
     * @param      $user
     * @param      $password
     * @param      $database
     * @param null $port
     * @param null $socket
     */
    public function setDbConfig($type, $host, $user, $password, $database, $port = null, $socket = null)
    {

        $config = new ConfigHelper;
        $config->setConfig($type, $host, $user, $password, $database, $port, $socket);
        $this->dbconfig = $config;

    }

    public function getDbConfig()
    {
        return $this->dbconfig->getConfig();
    }

    public function getMapperFactory($name = null)
    {

        return new MapperFactory($name, $this->dbconfig);

    }

    public function validateDbConnection()
    {

        /** @var \Factories\MapperFactory $mapperFactory */
        $mapperFactory = $this->getMapperFactory();
        /** @var \Contracts\ServicesInterface $mapper */
        $mapper = $mapperFactory->build();

        if (! $mapper) {
            if ($mapperFactory->getError()) {
                throw new \Exception($mapperFactory->getError());
            } else {
                throw new \Exception("An unknown error occured while trying to test the database connection.");
            }
        }

        if (! $mapper->validateDbConnection()) {
            throw new \Exception("Could not validate the database connection.");
        }

        return true;

    }

    public function validateModel($model)
    {

        /** @var \Factories\MapperFactory $mapperFactory */
        $mapperFactory = $this->getMapperFactory($model);
        if (! $mapperFactory->isValidModel()) {
            if ($mapperFactory->getError()) {
                throw new \Exception($mapperFactory->getError());
            } else {
                throw new \Exception("An unknown error occured while trying to load the model.");
            }
        }

        return true;
    }

    public function getFields($name)
    {

        /** @var \Factories\MapperFactory $mapperFactory */
        $mapperFactory = $this->getMapperFactory($name);
        /** @var \Contracts\ServicesInterface $mapper */
        $mapper = $mapperFactory->build();

        if (!$mapper) {
            if ($mapperFactory->getError()) {
                throw new \Exception($mapperFactory->getError());
            } else {
                throw new \Exception("An unknown error occured while trying to retrieve the fields.");
            }
        }

        $mapper->setDefaults();

        return $mapper->getTableProperties();

    }

    public function getInfo($name)
    {

        /** @var \Factories\MapperFactory $mapperFactory */
        $mapperFactory = $this->getMapperFactory($name);
        /** @var \Contracts\ServicesInterface $mapper */
        $mapper = $mapperFactory->build();

        if (!$mapper) {
            if ($mapperFactory->getError()) {
                throw new \Exception($mapperFactory->getError());
            } else {
                throw new \Exception("An unknown error occured while trying to retrieve the model or database info.");
            }
        }

        $mapper->setDefaults();

        return $mapper->getModelTableInfo();

    }

}