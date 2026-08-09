<?php
/**
 * API Backend សម្រាប់គ្រប់គ្រងបុគ្គលិកយោធា (PHP POST / GET)
 * RESTful AJAX Endpoint for Military Personnel Management
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$db = getDBConnection();
$action = $_REQUEST['action'] ?? '';

$response = [
    'success' => false,
    'message' => 'សកម្មភាពមិនត្រឹមត្រូវ (Invalid Action)',
    'data' => null
];

try {
    switch ($action) {

        // 1. ទាញយកបញ្ជីបុគ្គលិកយោធាទាំងអស់ (ជាមួយ Search & Filter)
        case 'fetch_all':
            $search = trim($_GET['search'] ?? '');
            $rankFilter = trim($_GET['rank'] ?? '');
            $unitFilter = trim($_GET['unit'] ?? '');
            $statusFilter = trim($_GET['marital_status'] ?? '');

            $sql = "SELECT * FROM military_personnel WHERE 1=1";
            $params = [];

            if ($search !== '') {
                $sql .= " AND (name_khmer LIKE :search OR name_latin LIKE :search OR id_card LIKE :search OR phone LIKE :search OR position LIKE :search)";
                $params[':search'] = "%{$search}%";
            }
            if ($rankFilter !== '') {
                $sql .= " AND rank = :rank";
                $params[':rank'] = $rankFilter;
            }
            if ($unitFilter !== '') {
                $sql .= " AND unit = :unit";
                $params[':unit'] = $unitFilter;
            }
            if ($statusFilter !== '') {
                $sql .= " AND marital_status = :marital_status";
                $params[':marital_status'] = $statusFilter;
            }

            $sql .= " ORDER BY id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $personnel = $stmt->fetchAll();

            $response = [
                'success' => true,
                'count' => count($personnel),
                'data' => $personnel
            ];
            break;

        // 2. ទាញយកទិន្នន័យសង្ខេប (Statistics)
        case 'get_stats':
            $totalStmt = $db->query("SELECT COUNT(*) as total FROM military_personnel");
            $total = $totalStmt->fetch()['total'];

            $rankStmt = $db->query("SELECT rank, COUNT(*) as count FROM military_personnel WHERE rank IS NOT NULL AND rank != '' GROUP BY rank");
            $ranks = $rankStmt->fetchAll();

            $unitStmt = $db->query("SELECT unit, COUNT(*) as count FROM military_personnel WHERE unit IS NOT NULL AND unit != '' GROUP BY unit");
            $units = $unitStmt->fetchAll();

            $response = [
                'success' => true,
                'data' => [
                    'total' => $total,
                    'ranks' => $ranks,
                    'units' => $units
                ]
            ];
            break;

        // 3. បន្ថែមបុគ្គលិកយោធាថ្មី (Add Personnel)
        case 'add':
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $id_card = trim($input['id_card'] ?? '');
            $name_khmer = trim($input['name_khmer'] ?? '');

            if ($name_khmer === '') {
                throw new Exception('សូមបញ្ចូល "ឈ្មោះខ្មែរ" របស់បុគ្គលិកយោធា!');
            }
            if ($id_card === '') {
                // ប្រសិនបើអត្តលេខទទេ ស្វ័យប្រវត្តបង្កើតអត្តលេខ
                $id_card = 'AF-' . str_pad(mt_rand(10000, 99999), 6, '0', STR_PAD_LEFT);
            }

            // ពិនិត្យមើលអត្តលេខជាន់គ្នា
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM military_personnel WHERE id_card = :id_card");
            $checkStmt->execute([':id_card' => $id_card]);
            if ($checkStmt->fetchColumn() > 0) {
                throw new Exception("អត្តលេខ '{$id_card}' មានក្នុងប្រព័ន្ធរួចហើយ!");
            }

            $sql = "INSERT INTO military_personnel (
                id_card, name_khmer, name_latin, gender, dob, rank, position, unit,
                place_of_birth, current_address, enlistment_date, framework_date,
                marital_status, phone, notes
            ) VALUES (
                :id_card, :name_khmer, :name_latin, :gender, :dob, :rank, :position, :unit,
                :place_of_birth, :current_address, :enlistment_date, :framework_date,
                :marital_status, :phone, :notes
            )";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_card' => $id_card,
                ':name_khmer' => $name_khmer,
                ':name_latin' => trim($input['name_latin'] ?? ''),
                ':gender' => trim($input['gender'] ?? 'ប្រុស'),
                ':dob' => !empty($input['dob']) ? $input['dob'] : null,
                ':rank' => trim($input['rank'] ?? ''),
                ':position' => trim($input['position'] ?? ''),
                ':unit' => trim($input['unit'] ?? ''),
                ':place_of_birth' => trim($input['place_of_birth'] ?? ''),
                ':current_address' => trim($input['current_address'] ?? ''),
                ':enlistment_date' => !empty($input['enlistment_date']) ? $input['enlistment_date'] : null,
                ':framework_date' => !empty($input['framework_date']) ? $input['framework_date'] : null,
                ':marital_status' => trim($input['marital_status'] ?? 'នៅលីវ'),
                ':phone' => trim($input['phone'] ?? ''),
                ':notes' => trim($input['notes'] ?? '')
            ]);

            $response = [
                'success' => true,
                'message' => 'បានរក្សាទុកទិន្នន័យបុគ្គលិកយោធាជោគជ័យ!',
                'id' => $db->lastInsertId()
            ];
            break;

        // 4. កែប្រែទិន្នន័យបុគ្គលិកយោធា (Edit Personnel)
        case 'edit':
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = (int)($input['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID បុគ្គលិកមិនត្រឹមត្រូវ!');
            }

            $id_card = trim($input['id_card'] ?? '');
            $name_khmer = trim($input['name_khmer'] ?? '');

            if ($name_khmer === '') {
                throw new Exception('សូមបញ្ចូល "ឈ្មោះខ្មែរ"!');
            }

            $sql = "UPDATE military_personnel SET
                id_card = :id_card,
                name_khmer = :name_khmer,
                name_latin = :name_latin,
                gender = :gender,
                dob = :dob,
                rank = :rank,
                position = :position,
                unit = :unit,
                place_of_birth = :place_of_birth,
                current_address = :current_address,
                enlistment_date = :enlistment_date,
                framework_date = :framework_date,
                marital_status = :marital_status,
                phone = :phone,
                notes = :notes
                WHERE id = :id";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_card' => $id_card,
                ':name_khmer' => $name_khmer,
                ':name_latin' => trim($input['name_latin'] ?? ''),
                ':gender' => trim($input['gender'] ?? 'ប្រុស'),
                ':dob' => !empty($input['dob']) ? $input['dob'] : null,
                ':rank' => trim($input['rank'] ?? ''),
                ':position' => trim($input['position'] ?? ''),
                ':unit' => trim($input['unit'] ?? ''),
                ':place_of_birth' => trim($input['place_of_birth'] ?? ''),
                ':current_address' => trim($input['current_address'] ?? ''),
                ':enlistment_date' => !empty($input['enlistment_date']) ? $input['enlistment_date'] : null,
                ':framework_date' => !empty($input['framework_date']) ? $input['framework_date'] : null,
                ':marital_status' => trim($input['marital_status'] ?? 'នៅលីវ'),
                ':phone' => trim($input['phone'] ?? ''),
                ':notes' => trim($input['notes'] ?? ''),
                ':id' => $id
            ]);

            $response = [
                'success' => true,
                'message' => 'បានកែប្រែទិន្នន័យបុគ្គលិកយោធាជោគជ័យ!'
            ];
            break;

        // 5. លុបទិន្នន័យបុគ្គលិក (Delete Personnel)
        case 'delete':
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = (int)($input['id'] ?? $_GET['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID បុគ្គលិកមិនត្រឹមត្រូវ!');
            }

            $stmt = $db->prepare("DELETE FROM military_personnel WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $response = [
                'success' => true,
                'message' => 'បានលុបទិន្នន័យបុគ្គលិកយោធាជោគជ័យ!'
            ];
            break;

        // 6. នាំចូលទិន្នន័យជាកញ្ចប់ពី Excel (Bulk Import Excel Data)
        case 'import_batch':
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);

            if (empty($input) || !is_array($input)) {
                throw new Exception('គ្មានទិន្នន័យសម្រាប់ នាំចូល ទេ!');
            }

            $successCount = 0;
            $updatedCount = 0;
            $errorCount = 0;

            $db->beginTransaction();

            $sqlCheck = "SELECT id FROM military_personnel WHERE id_card = :id_card";
            $stmtCheck = $db->prepare($sqlCheck);

            $sqlInsert = "INSERT INTO military_personnel (
                id_card, name_khmer, name_latin, gender, dob, rank, position, unit,
                place_of_birth, current_address, enlistment_date, framework_date,
                marital_status, phone, notes
            ) VALUES (
                :id_card, :name_khmer, :name_latin, :gender, :dob, :rank, :position, :unit,
                :place_of_birth, :current_address, :enlistment_date, :framework_date,
                :marital_status, :phone, :notes
            )";
            $stmtInsert = $db->prepare($sqlInsert);

            $sqlUpdate = "UPDATE military_personnel SET
                name_khmer = :name_khmer, name_latin = :name_latin, gender = :gender, dob = :dob,
                rank = :rank, position = :position, unit = :unit, place_of_birth = :place_of_birth,
                current_address = :current_address, enlistment_date = :enlistment_date,
                framework_date = :framework_date, marital_status = :marital_status,
                phone = :phone, notes = :notes
                WHERE id_card = :id_card";
            $stmtUpdate = $db->prepare($sqlUpdate);

            foreach ($input as $row) {
                $name_khmer = trim($row['name_khmer'] ?? $row['ឈ្មោះខ្មែរ'] ?? '');
                if ($name_khmer === '') {
                    $errorCount++;
                    continue;
                }

                $id_card = trim($row['id_card'] ?? $row['អត្តលេខ'] ?? '');
                if ($id_card === '') {
                    $id_card = 'AF-' . str_pad(mt_rand(10000, 99999), 6, '0', STR_PAD_LEFT);
                }

                $gender = trim($row['gender'] ?? $row['ភេទ'] ?? 'ប្រុស');
                $name_latin = trim($row['name_latin'] ?? $row['ឈ្មោះឡាតាំង'] ?? '');
                $dob = !empty($row['dob'] ?? $row['ថ្ងៃខែឆ្នាំកំណើត']) ? trim($row['dob'] ?? $row['ថ្ងៃខែឆ្នាំកំណើត']) : null;
                $rank = trim($row['rank'] ?? $row['ឋានន្តរស័ក្តិ'] ?? '');
                $position = trim($row['position'] ?? $row['មុខតំណែង'] ?? '');
                $unit = trim($row['unit'] ?? $row['អង្គភាព'] ?? '');
                $place_of_birth = trim($row['place_of_birth'] ?? $row['ទីកន្លែងកំណើត'] ?? '');
                $current_address = trim($row['current_address'] ?? $row['ទីលំនៅបច្ចុប្បន្ន'] ?? '');
                $enlistment_date = !empty($row['enlistment_date'] ?? $row['ថ្ងៃខែឆ្នាំចូលបម្រើការងារ'] ?? $row['ថ្ងៃខែឆ្នាំចូលបម្រើ']) ? trim($row['enlistment_date'] ?? $row['ថ្ងៃខែឆ្នាំចូលបម្រើការងារ'] ?? $row['ថ្ងៃខែឆ្នាំចូលបម្រើ']) : null;
                $framework_date = !empty($row['framework_date'] ?? $row['ថ្ងៃខែឆ្នាំចូលក្របខ័ណ្ឌ'] ?? $row['ថ្ងៃចូលក្របខ័ណ្ឌ']) ? trim($row['framework_date'] ?? $row['ថ្ងៃខែឆ្នាំចូលក្របខ័ណ្ឌ'] ?? $row['ថ្ងៃចូលក្របខ័ណ្ឌ']) : null;
                $marital_status = trim($row['marital_status'] ?? $row['ស្ថានភាពរស់នៅ'] ?? $row['ស្ថានភាព'] ?? 'នៅលីវ');
                $phone = trim($row['phone'] ?? $row['លេខទូរស័ព្ទ'] ?? '');
                $notes = trim($row['notes'] ?? $row['ផ្សេងៗ'] ?? '');

                // Check existing
                $stmtCheck->execute([':id_card' => $id_card]);
                $existingId = $stmtCheck->fetchColumn();

                $params = [
                    ':id_card' => $id_card,
                    ':name_khmer' => $name_khmer,
                    ':name_latin' => $name_latin,
                    ':gender' => $gender,
                    ':dob' => $dob,
                    ':rank' => $rank,
                    ':position' => $position,
                    ':unit' => $unit,
                    ':place_of_birth' => $place_of_birth,
                    ':current_address' => $current_address,
                    ':enlistment_date' => $enlistment_date,
                    ':framework_date' => $framework_date,
                    ':marital_status' => $marital_status,
                    ':phone' => $phone,
                    ':notes' => $notes
                ];

                if ($existingId) {
                    $stmtUpdate->execute($params);
                    $updatedCount++;
                } else {
                    $stmtInsert->execute($params);
                    $successCount++;
                }
            }

            $db->commit();

            $response = [
                'success' => true,
                'message' => "នាំចូលទិន្នន័យពី Excel បានជោគជ័យ! (បន្ថែមថ្មី: {$successCount}, ធ្វើបច្ចុប្បន្នភាព: {$updatedCount})",
                'imported' => $successCount + $updatedCount
            ];
            break;

        default:
            $response['message'] = "សកម្មភាព '{$action}' មិនត្រូវបានគាំទ្រទេ!";
            break;
    }
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $response = [
        'success' => false,
        'message' => 'កំហុស (Error): ' . $e->getMessage()
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
