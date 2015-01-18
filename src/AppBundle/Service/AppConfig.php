<?php

namespace AppBundle\Service;

class AppConfig {

    protected $em;

    public function __construct($entityManager) {
        $this->em = $entityManager;
    }

    public function getConfigData()
    {
        return array(
            'temperatureLimit'=>$this->getConfigValue("temperature_limit"),
            'lightLimit'=>$this->getConfigValue("light_limit"),
            'emailNotify'=>$this->getConfigValue("email_notify"),
            'twitterNotify'=>$this->getConfigValue("twitter_notify"),
        );
    }

    public function getConfigValue($configOption)
    {
        $sql = 'SELECT `option_value` FROM `config` WHERE `option_name` = ' . '"'. $configOption . '"';
        $stmt = $this->em
            ->getConnection()
            ->prepare($sql);
        $stmt->execute();
        $value = $stmt->fetchAll()[0]['option_value'];
        return $value;
    }

    public function setConfigValue($optionName, $value)
    {
        $sql = 'UPDATE `config` SET  `option_value` ='  . $value . ' WHERE  `config`.`option_name` ='. '"'. $optionName . '"';
        $stmt = $this->em
            ->getConnection()
            ->prepare($sql);
        $stmt->execute();
    }

}