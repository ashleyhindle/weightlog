<?php
namespace WeightLog;

class WeightLog
{
	private $db;
	public function __construct(\Aura\Sql\ExtendedPdo $db)
	{
		$this->db = $db;
	}

	public function generateToken()
	{
		return bin2hex(openssl_random_pseudo_bytes(16));
	}

	public function getAllWeightsByToken($token)
	{
		$person = $this->getPersonByToken($token);
		if (empty($person)) {
			return [];
		}

		$weights = $this->db->fetchAssoc("SELECT * from weights WHERE personid=?", [$person['id']]);
		return $weights;
	}

	public function getPersonByToken($token)
	{
		return $this->db->fetchOne("SELECT * from persons WHERE token=?", [$token]);
	}

	public function setCurrentWeight($person, $weight)
	{
		return $this->db->perform("UPDATE persons SET currentweight=? WHERE id=?", [$weight, $person['id']]);
	}

	public function getPerson($telegramid, $firstname, $username)
	{
		$person = $this->db->fetchOne("SELECT * from persons WHERE telegramid=? AND firstname=? AND username=?",
			[$telegramid, $firstname, $username]
		);

		if (!empty($person)) {
			return $person;
		}

		$person = $this->addPerson($telegramid, $firstname, $username, $this->generateToken());
		return $person;
	}

	public function addPerson($telegramid, $firstname, $username, $token)
	{
		$result = $this->db->perform("INSERT INTO persons (timeadded, currentweight, telegramid, firstname, username, token) VALUES (:timeadded, :currentweight, :telegramid, :firstname, :username, :token)", 
			[
				'timeadded' => time(),
				'currentweight' => 0,
				'telegramid' => $telegramid,
				'firstname' => $firstname,
				'username' => $username,
				'token' => $token,
			]
		);

		if (empty($result)) {
			return false;
		}

		return $this->getPerson($telegramid, $firstname, $username);
	}
}
