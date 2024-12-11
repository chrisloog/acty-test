<?php
include 'db.php';

require_once __DIR__ . '/src/Repositories/OrganizationRepository.php';
require_once __DIR__ . '/src/Repositories/RelationshipRepository.php';
require_once __DIR__ . '/src/Services/OrganizationService.php';

use App\Repositories\OrganizationRepository;
use App\Repositories\RelationshipRepository;
use App\Services\OrganizationService;

if (!isset($conn)) {
    die("Database connection not established. Please check db.php.");
}

header("Content-Type: application/json");

$organizationRepository = new OrganizationRepository($conn);
$relationshipRepository = new RelationshipRepository($conn);
$organizationService = new OrganizationService($organizationRepository, $relationshipRepository);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'POST':
        if (!isset($input['org_name'])) {
            echo json_encode(["message" => "Missing organization name"]);
            break;
        }

        $organizationService->insertOrganizationHierarchy($input);

        echo json_encode(["message" => "Data inserted successfully"]);
        break;

    case 'GET':
        if (isset($_GET['org_name'])) {
            $organizationName = $_GET['org_name'];
            $organization = $organizationRepository->findByName($organizationName);

            if (!$organization) {
                echo json_encode(["message" => "Organization not found"]);
                break;
            }

            $parents = $organizationService->getParents($organization['id']);
            $daughters = $organizationService->getDaughters($organization['id']);
            $sisters = $organizationService->getSisters($organization['id']);

            $relationships = [];

            foreach ($parents as $p) {
                $relationships[] = [
                    "relationship_type" => "parent",
                    "org_name" => $p["org_name"]
                ];
            }

            foreach ($sisters as $s) {
                $relationships[] = [
                    "relationship_type" => "sister",
                    "org_name" => $s["org_name"]
                ];
            }

            foreach ($daughters as $d) {
                $relationships[] = [
                    "relationship_type" => "daughter",
                    "org_name" => $d["org_name"]
                ];
            }

            echo json_encode($relationships);

        } else {
            echo json_encode(["message" => "Missing organization name"]);
        }

        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}
