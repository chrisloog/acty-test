<?php
namespace src\repositories;

class OrganizationRepository {
    private $conn;

    public function __construct(\mysqli $conn) {
        $this->conn = $conn;
    }

    public function findByName($organizationName) {
        $stmt = $this->conn->prepare("SELECT id, org_name FROM organizations WHERE org_name = ?");
        $stmt->bind_param("s", $organizationName);
        $stmt->execute();
        $res = $stmt->get_result();
        $org = $res->fetch_assoc();
        $stmt->close();
        return $org;
    }

    public function insert($organizationName) {
        $stmt = $this->conn->prepare("INSERT INTO organizations (org_name) VALUES (?)");
        $stmt->bind_param("s", $organizationName);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }
}
