<?php

namespace Classes\Webforce3\DB;

use Classes\Webforce3\Config\Config;
use Classes\Webforce3\Exceptions\InvalidSqlQueryException;

class Student extends DbObject {
	/** @var Session */
	protected $session;
	/** @var City */
	protected $city;
	/** @var string */
	protected $lname;
	/** @var string */
	protected $fname;
	/** @var string */
	protected $email;
	/** @var string */
	protected $birthdate;
	/** @var int */
	protected $friendliness;

	public function __construct($id=0, $session=null, $city=null, $lname='', $fname='', $email='', $birthdate='', $friendliness=0, $inserted='') {
		if (empty($session)) {
			$this->session = new Session();
		}
		else {
			$this->session = $session;
		}
		if (empty($city)) {
			$this->city = new City();
		}
		else {
			$this->city = $city;
		}
		$this->lname = $lname;
		$this->fname = $fname;
		$this->email = $email;
		$this->birthdate = $birthdate;
		$this->friendliness = $friendliness;

		parent::__construct($id, $inserted);
	}

	/**
	 * @param int $id
	 * @return bool|Student
	 * @throws InvalidSqlQueryException
	 */
	public static function get($id) {
		$sql = '
			SELECT stu_id, city_cit_id, session_ses_id, stu_lastname, stu_firstname, stu_birthdate, stu_email, stu_friendliness
			FROM student
			WHERE stu_id = :id
			ORDER BY stu_lastname ASC, stu_firstname ASC
		';
		$stmt = Config::getInstance()->getPDO()->prepare($sql);
		$stmt->bindValue(':id', $id, \PDO::PARAM_INT);

		if ($stmt->execute() === false) {
			throw new InvalidSqlQueryException($sql, $stmt);
		}
		else {
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
			if (!empty($row)) {
				$currentObject = new Student(
					$row['stu_id'],
					new Session($row['session_ses_id']),
					new City($row['city_cit_id']),
					$row['stu_lastname'],
					$row['stu_firstname'],
					$row['stu_email'],
					$row['stu_birthdate'],
					$row['stu_friendliness']
				);
				return $currentObject;
			}
		}

		return false;
	}

	/**
	 * @return DbObject[]
	 * @throws InvalidSqlQueryException
	 */
	public static function getAll() {
		$returnList = array();

		$sql = '
			SELECT stu_id, city_cit_id, session_ses_id, stu_lastname, stu_firstname, stu_birthdate, stu_email, stu_friendliness
			FROM student
			WHERE stu_id > 0
			ORDER BY stu_lastname ASC, stu_firstname ASC
		';
		$stmt = Config::getInstance()->getPDO()->prepare($sql);
		if ($stmt->execute() === false) {
			throw new InvalidSqlQueryException($sql, $stmt);
		}
		else {
			$allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($allDatas as $row) {
				$currentObject = new Student(
					$row['stu_id'],
					new Session($row['session_ses_id']),
					new City($row['city_cit_id']),
					$row['stu_lastname'],
					$row['stu_firstname'],
					$row['stu_email'],
					$row['stu_birthdate'],
					$row['stu_friendliness']
				);
				$returnList[] = $currentObject;
			}
		}

		return $returnList;
	}

	/**
	 * @return array
	 * @throws InvalidSqlQueryException
	 */
	public static function getAllForSelect() {
		$returnList = array();

		$sql = '
			SELECT stu_id, stu_lastname, stu_firstname
			FROM student
			WHERE stu_id > 0
			ORDER BY stu_lastname ASC, stu_firstname ASC
		';
		$stmt = Config::getInstance()->getPDO()->prepare($sql);
		if ($stmt->execute() === false) {
			throw new InvalidSqlQueryException($sql, $stmt);
		}
		else {
			$allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($allDatas as $row) {
				$returnList[$row['stu_id']] = $row['stu_lastname'].' '.$row['stu_firstname'];
			}
		}

		return $returnList;
	}

	/**
	 * @param int $sessionId
	 * @return DbObject[]
	 * @throws InvalidSqlQueryException
	 */
	public static function getFromSession($sessionId) {
		$returnList = array();

		$sql = '
			SELECT stu_id, city_cit_id, session_ses_id, stu_lastname, stu_firstname, stu_birthdate, stu_email, stu_friendliness
			FROM student
			WHERE stu_id > 0
			AND session_ses_id = :sessionId
			ORDER BY stu_lastname ASC, stu_firstname ASC
		';
		$stmt = Config::getInstance()->getPDO()->prepare($sql);
		$stmt->bindValue(':sessionId', $sessionId, \PDO::PARAM_INT);

		if ($stmt->execute() === false) {
			throw new InvalidSqlQueryException($sql, $stmt);
		}
		else {
			$allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach ($allDatas as $row) {
				$currentObject = new Student(
					$row['stu_id'],
					new Session($row['session_ses_id']),
					new City($row['city_cit_id']),
					$row['stu_lastname'],
					$row['stu_firstname'],
					$row['stu_email'],
					$row['stu_birthdate'],
					$row['stu_friendliness']
				);
				$returnList[] = $currentObject;
			}
		}

		return $returnList;
	}

	/**
	 * @return bool
	 * @throws InvalidSqlQueryException
	 */
	public function saveDB() {
		if ($this->id > 0) {
			$sql = '
				UPDATE student
				SET stu_lastname = :lname,
				stu_firstname = :fname,
				stu_email = :email,
				stu_birthdate = :birthdate,
				stu_friendliness = :friendliness,
				city_cit_id = :citId,
				session_ses_id = :sesId
				WHERE stu_id = :id
			';
			$stmt = Config::getInstance()->getPDO()->prepare($sql);
			$stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
			$stmt->bindValue(':birthdate', $this->birthdate, \PDO::PARAM_INT);
			$stmt->bindValue(':friendliness', $this->friendliness, \PDO::PARAM_INT);
			$stmt->bindValue(':citId', $this->city->id, \PDO::PARAM_INT);
			$stmt->bindValue(':sesId', $this->session->id, \PDO::PARAM_INT);
			$stmt->bindValue(':lname', $this->lname);
			$stmt->bindValue(':fname', $this->fname);
			$stmt->bindValue(':email', $this->email);

			if ($stmt->execute() === false) {
				throw new InvalidSqlQueryException($sql, $stmt);
			}
			else {
				return true;
			}
		}
		else {
			$sql = '
				INSERT INTO student (stu_lastname, stu_firstname, stu_email, stu_birthdate, stu_friendliness, city_cit_id, session_ses_id)
				VALUES (:lname, :fname, :email, :birthdate, :friendliness, :citId, :sesId)
			';
			$stmt = Config::getInstance()->getPDO()->prepare($sql);
			$stmt->bindValue(':birthdate', $this->birthdate, \PDO::PARAM_INT);
			$stmt->bindValue(':friendliness', $this->friendliness, \PDO::PARAM_INT);
			$stmt->bindValue(':citId', $this->city->id, \PDO::PARAM_INT);
			$stmt->bindValue(':sesId', $this->session->id, \PDO::PARAM_INT);
			$stmt->bindValue(':lname', $this->lname);
			$stmt->bindValue(':fname', $this->fname);
			$stmt->bindValue(':email', $this->email);

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
	 * @throws InvalidSqlQueryException
	 */
	public static function deleteById($id) {
		$sql = '
			DELETE FROM student WHERE stu_id = :id
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

	/**
	 * @return Session
	 */
	public function getSession() {
		return $this->session;
	}

	/**
	 * @return City
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @return string
	 */
	public function getLname() {
		return $this->lname;
	}

	/**
	 * @return string
	 */
	public function getFname() {
		return $this->fname;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return int
	 */
	public function getBirthdate() {
		return $this->birthdate;
	}

	/**
	 * @return int
	 */
	public function getFriendliness() {
		return $this->friendliness;
	}
}