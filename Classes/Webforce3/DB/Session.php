<?php

namespace Classes\Webforce3\DB;

use Classes\Webforce3\Config\Config;
use Classes\Webforce3\Exceptions\InvalidSqlQueryException;

class Session extends DbObject {
    /** @var string */
    protected $startdate;
    /** @var string */
    protected $enddate;
    /** @var int */    
    protected $number;
    /** @var Location */
    protected $location;
    /** @var Training */
    protected $training;
    
    function __construct($id=0, $startdate='', $enddate='', $number=0, $location=null, $training=null, $inserted='') {
        if (empty($location)) {
                $this->location = new Location();
        }
        else {
                $this->location = $location;
        }
        if (empty($training)) {
                $this->training = new Training();
        }
        else {
                $this->training = $training;
        }
        
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->number = $number;
        
        //print_r( $inserted);
        parent::__construct($id, $inserted);
    }

    /**
	 * @param int $id
	 * @return DbObject
	 */
	public static function get($id) {
		// TODO: Implement get() method.
        $sql = '
                SELECT ses_id, ses_start_date, ses_end_date,ses_number,
                location_loc_id, training_tra_id
                FROM session
                LEFT OUTER JOIN training ON training.tra_id = session.training_tra_id
                LEFT OUTER JOIN location ON location.loc_id = session.location_loc_id
                WHERE ses_id =:id
                ORDER BY ses_start_date ASC            
		';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        if ($stmt->execute() === false) {
            throw new InvalidSqlQueryException($sql, $stmt);
        } else {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!empty($row)) {
                $currentObject = new Session(
                            $row['ses_id'],                            
                            $row['ses_start_date'],
                            $row['ses_end_date'],                            
                            $row['ses_number'],                            
                            new Location($row['location_loc_id']),
                            new Training($row['training_tra_id'])
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
                SELECT ses_id, tra_name, ses_start_date, ses_end_date, ses_number , loc_name
                ,location_loc_id, training_tra_id
                FROM session
                LEFT OUTER JOIN training ON training.tra_id = session.training_tra_id
                LEFT OUTER JOIN location ON location.loc_id = session.location_loc_id
                WHERE ses_id > 0
                ORDER BY ses_start_date ASC                                
		';
		$stmt = Config::getInstance()->getPDO()->prepare($sql);
		if ($stmt->execute() === false) {
			throw new InvalidSqlQueryException($sql, $stmt);
		}
		else {
			$allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($allDatas as $row) {
                                $currentObject = new Session(
                                $row['ses_id'],
                                $row['tra_name'],
                                $row['ses_start_date'],
                                $row['ses_end_date'],
                                $row['loc_name'],                                                
                                new Location($row['location_loc_id']),
                                new Training($row['training_tra_id'])
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
			SELECT ses_id, tra_name, ses_start_date, ses_end_date, ses_number, loc_name
			FROM session
			LEFT OUTER JOIN training ON training.tra_id = session.training_tra_id
			LEFT OUTER JOIN location ON location.loc_id = session.location_loc_id
			WHERE ses_id > 0
			ORDER BY ses_start_date ASC
		';
		$stmt = Config::getInstance()->getPDO()->prepare($sql);
		if ($stmt->execute() === false) {
			print_r($stmt->errorInfo());
		}
		else {
			$allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($allDatas as $row) {
				$returnList[$row['ses_id']] = '['.$row['ses_start_date'].' > '.$row['ses_end_date'].'] '.$row['tra_name'].' - '.$row['loc_name'];
			}
		}

		return $returnList;
	}

	/**
	 * @param int $sessionId
	 * @return DbObject[]
	 */
	public static function getFromSession($sessionId) {
		// TODO: Implement getFromTraining() method.
		$returnList = array();

		$sql = '                       
			SELECT ses_id, tra_name, ses_start_date, ses_end_date,ses_number, loc_name
			FROM session
			LEFT OUTER JOIN training ON training.tra_id = session.training_tra_id
			LEFT OUTER JOIN location ON location.loc_id = session.location_loc_id
			WHERE ses_id > 0
                        AND ses_id = :sessionId
			ORDER BY ses_start_date ASC                        
			
		';
		$stmt = Config::getInstance()->getPDO()->prepare($sql);
		$stmt->bindValue(':sessionId', $sessionId, \PDO::PARAM_INT);

		if ($stmt->execute() === false) {
			throw new InvalidSqlQueryException($sql, $stmt);
		}
		else {
			$allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($allDatas as $row) {
                                $currentObject = new Session(
                                $row['ses_id'],
                                $row['tra_name'],
                                $row['ses_start_date'],
                                $row['ses_end_date'],
                                $row['ses_number'],
                                $row['loc_name'],                                                
                                new Location($row['location_loc_id']),
                                new Training($row['training_tra_id'])
                                );
				$returnList[] = $currentObject;
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
				UPDATE session
				SET ses_start_date = :startdate,
				ses_end_date = :enddate,
				ses_number = :number                                
				WHERE ses_id = :id
			';
			$stmt = Config::getInstance()->getPDO()->prepare($sql);
			$stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
			$stmt->bindValue(':startdate', $this->startdate);
			$stmt->bindValue(':enddate', $this->enddate);
			$stmt->bindValue(':number', $this->number);

			if ($stmt->execute() === false) {
				throw new InvalidSqlQueryException($sql, $stmt);
			}
			else {
				return true;
			}
		}
		else {
			$sql = '
				INSERT INTO session (ses_start_date, ses_end_date, ses_number, location_loc_id, training_tra_id)
				VALUES (:startdate, :number, :email, :locid, :traid)
			';
			$stmt = Config::getInstance()->getPDO()->prepare($sql);
			$stmt->bindValue(':startdate', $this->startdate);
			$stmt->bindValue(':enddate', $this->enddate);
			$stmt->bindValue(':number', $this->number, \PDO::PARAM_INT);			
			$stmt->bindValue(':locid', $this->location->id, \PDO::PARAM_INT);			
			$stmt->bindValue(':traid', $this->training->id, \PDO::PARAM_INT);			
			
			if ($stmt->execute() === false) {
				throw new InvalidSqlQueryException($sql, $stmt);
			}
			else {
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
			DELETE FROM session WHERE ses_id = :id
		';
		$stmt = Config::getInstance()->getPDO()->prepare($sql);
		$stmt->bindValue(':id', $id, \PDO::PARAM_INT);

		if ($stmt->execute() === false) {
			print_r($stmt->errorInfo());
		}
		else {
			return true;
		}
		return false;            
	}
        
        function getStartdate() {
            return $this->startdate;
        }

        function getEnddate() {
            return $this->enddate;
        }

        function getNumber() {
            return $this->number;
        }


}