<?php
namespace src\repositories;

class RelationshipRepository {
    private $conn;

    public function __construct(\mysqli $conn) {
        $this->conn = $conn;
    }

    public function insert($parentId, $childId) {
        $stmt = $this->conn->prepare("INSERT INTO organization_relationships (parent_id, child_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $parentId, $childId);
        $stmt->execute();
        $stmt->close();
    }

    public function getParents($organizationId) {
        $sql = "SELECT o2.* FROM organization_relationships r
                JOIN organizations o2 ON r.parent_id = o2.id
                WHERE r.child_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $organizationId);
        $stmt->execute();
        $result = $stmt->get_result();

        $parents = [];
        while ($row = $result->fetch_assoc()) {
            $parents[] = $row;
        }

        $stmt->close();

        return $parents;
    }

    public function getDaughters($organizationId) {
        $sql = "SELECT o2.* FROM organization_relationships r
                JOIN organizations o2 ON r.child_id = o2.id
                WHERE r.parent_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $organizationId);
        $stmt->execute();
        $result = $stmt->get_result();

        $daughters = [];
        while ($row = $result->fetch_assoc()) {
            $daughters[] = $row;
        }

        $stmt->close();

        return $daughters;
    }

    public function getSisters($organizationId) {
        $parents = $this->getParents($organizationId);

        $sisterIds = [];
        foreach ($parents as $p) {
            $sql = "SELECT o2.* FROM organization_relationships r
                    JOIN organizations o2 ON r.child_id = o2.id
                    WHERE r.parent_id = ? AND o2.id != ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $p['id'], $organizationId);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $sisterIds[$row['id']] = $row;
            }

            $stmt->close();
        }

        return array_values($sisterIds);
    }
}
