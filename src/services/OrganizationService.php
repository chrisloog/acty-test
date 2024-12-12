<?php
namespace src\services;

use src\repositories\OrganizationRepository;
use src\repositories\RelationshipRepository;

class OrganizationService {
    private $organizationRepository;
    private $relationshipRepository;

    public function __construct(OrganizationRepository $organizationRepository, RelationshipRepository $relationshipRepository) {
        $this->organizationRepository = $organizationRepository;
        $this->relationshipRepository = $relationshipRepository;
    }

    public function getOrganizationId($organizationName) {
        $organization = $this->organizationRepository->findByName($organizationName);

        if ($organization) {
            return $organization['id'];
        }

        return $this->organizationRepository->insert($organizationName);
    }

    public function insertOrganizationHierarchy($data, $parentId = null) {
        $organizationName = $data["org_name"];
        $organizationId = $this->getOrganizationId($organizationName);

        if ($parentId !== null) {
            $this->relationshipRepository->insert($parentId, $organizationId);
        }

        if (!empty($data['daughters'])) {
            foreach ($data['daughters'] as $daughter) {
                $this->insertOrganizationHierarchy($daughter, $organizationId);
            }
        }

        return $organizationId;
    }

    public function getParents($organizationId) {
        return $this->relationshipRepository->getParents($organizationId);
    }

    public function getDaughters($organizationId) {
        return $this->relationshipRepository->getDaughters($organizationId);
    }

    public function getSisters($organizationId) {
        return $this->relationshipRepository->getSisters($organizationId);
    }
}
