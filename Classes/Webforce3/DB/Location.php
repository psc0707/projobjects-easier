<?php

namespace Classes\Webforce3\DB;

use Classes\Webforce3\Config\Config;
use Classes\Webforce3\Exceptions\InvalidSqlQueryException;

class Location extends DbObject {    
    /** @var Country */
    protected $country;    
    /** @var string */
    protected $name;

    function __construct($id = 0, $country=null, $name = '', $inserted = '') {        
        if (empty($country)) {
                $this->country = new Country();
        }
        else {
                $this->country = $country;
        }        
        $this->name = $name;

        parent::__construct($id, $inserted);
    }

    /**
     * @param int $id
     * @return DbObject
     */
    public static function get($id) {
        // TODO: Implement get() method.
        $sql = '
			SELECT loc_id, loc_name, country_cou_id
			FROM location
			WHERE loc_id = :id
			ORDER BY 2
		';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        if ($stmt->execute() === false) {
            throw new InvalidSqlQueryException($sql, $stmt);
        } else {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!empty($row)) {
                $currentObject = new Location(
                        $row['loc_id'],
                        new Country($row['country_cou_id']),
                        $row['loc_name']                        
                );
                return $currentObject;
            }
        }

        return false;
    }

    /**
     * @return DbObject[]
     */
    public static function getAll() {
        // TODO: Implement getAll() method.
        $returnList = array();

        $sql = '
			SELECT `loc_id`, `loc_name`,country_cou_id
			FROM location
			WHERE loc_id > 0
			ORDER BY 2
		';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        if ($stmt->execute() === false) {
            throw new InvalidSqlQueryException($sql, $stmt);
        } else {
            $allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($allDatas as $row) {
                $currentObject = new Location(
                        $row['loc_id'],
                        new Country($row['country_cou_id']),
                        $row['loc_name']                        
                );
                $returnList[] = $currentObject;
            }
        }

        return $returnList;
    }

    /**
     * @return array
     */
    public static function getAllForSelect() {
        $returnList = array();

        $sql = '
			SELECT `loc_id`, `loc_name`
			FROM location
			WHERE loc_id > 0
			ORDER BY 2
		';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        if ($stmt->execute() === false) {
            print_r($stmt->errorInfo());
        } else {
            $allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($allDatas as $row) {
                $returnList[$row['loc_id']] = $row['loc_name'];
            }
        }

        return $returnList;
    }

    /**
     * @return bool
     */
    public function saveDB() {
        // TODO: Implement saveDB() method.
        if ($this->id > 0) {
            $sql = '
				UPDATE location
				SET   loc_name = :name,	
                                country_cou_id = :couid
				WHERE loc_id = :id
			';
            $stmt = Config::getInstance()->getPDO()->prepare($sql);
            $stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->name);
            $stmt->bindValue(':couid',$this->country->id, \PDO::PARAM_INT);

            if ($stmt->execute() === false) {
                throw new InvalidSqlQueryException($sql, $stmt);
            } else {
                return true;
            }
        } else {
            $sql = '
				INSERT INTO location (loc_name,country_cou_id)
				VALUES (:name,:couid)
			';
            $stmt = Config::getInstance()->getPDO()->prepare($sql);            
            $stmt->bindValue(':name', $this->name);
            $stmt->bindValue(':couid',$this->country->id);

            if ($stmt->execute() === false) {
                throw new InvalidSqlQueryException($sql, $stmt);
            } else {
                $this->id = Config::getInstance()->getPDO()->lastInsertId();
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function deleteById($id) {
        // TODO: Implement deleteById() method.
        $sql = '
                    DELETE FROM location WHERE tra_id = :id
            ';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        if ($stmt->execute() === false) {
            print_r($stmt->errorInfo());
        } else {
            return true;
        }
        return false;
    }

    function getName() {
        return $this->name;
    }

}
