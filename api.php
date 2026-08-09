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

function savePhotoIfBase64($photoStr, $prefix, $recordId) {
    if (empty($photoStr)) return '';
    if (strpos($photoStr, 'data:image') === 0) {
        try {
            $parts = explode(';base64,', $photoStr);
            $ext = 'jpg';
            if (strpos($parts[0], 'png') !== false) $ext = 'png';
            else if (strpos($parts[0], 'webp') !== false) $ext = 'webp';
            $photosDir = __DIR__ . '/photos';
            if (!is_dir($photosDir)) mkdir($photosDir, 0777, true);
            $filename = "{$prefix}_" . ($recordId ?: 'new') . "_" . time() . ".{$ext}";
            $filepath = $photosDir . '/' . $filename;
            file_put_contents($filepath, base64_decode($parts[1]));
            return "photos/{$filename}";
        } catch (Exception $e) {
            return $photoStr;
        }
    }
    return $photoStr;
}

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

            $sql .= " ORDER BY id ASC";

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

            $surname = trim($input['surname'] ?? '');
            $given_name = trim($input['given_name'] ?? '');
            $name_khmer = trim($input['name_khmer'] ?? '');
            if (!$name_khmer && ($surname || $given_name)) {
                $name_khmer = trim("{$surname} {$given_name}");
            }

            $id_card = trim($input['id_card'] ?? '');
            if ($name_khmer === '') {
                throw new Exception('សូមបញ្ចូល "ឈ្មោះខ្មែរ" ឬ "គោត្តនាម/នាម"!');
            }

            $photo = savePhotoIfBase64($input['photo'] ?? '', 'officer', null);
            $family_photo = savePhotoIfBase64($input['family_photo'] ?? '', 'family', null);

            $sql = "INSERT INTO military_personnel (
                manual_id, rank, surname, given_name, name_khmer, name_latin, gender, id_card, position,
                unit_group, unit, rank_date, position_date, dob, enlistment_date, framework_date,
                education_level, study_local, study_abroad, children_count, black_card_expiry, blue_card_expiry,
                pob_village, pob_commune, pob_district, pob_province, place_of_birth,
                addr_house, addr_group, addr_village, addr_commune, addr_district, addr_province, current_address,
                marital_status, phone, notes, photo, family_photo, family_name
            ) VALUES (
                :manual_id, :rank, :surname, :given_name, :name_khmer, :name_latin, :gender, :id_card, :position,
                :unit_group, :unit, :rank_date, :position_date, :dob, :enlistment_date, :framework_date,
                :education_level, :study_local, :study_abroad, :children_count, :black_card_expiry, :blue_card_expiry,
                :pob_village, :pob_commune, :pob_district, :pob_province, :place_of_birth,
                :addr_house, :addr_group, :addr_village, :addr_commune, :addr_district, :addr_province, :current_address,
                :marital_status, :phone, :notes, :photo, :family_photo, :family_name
            )";

            $pob_v = trim($input['pob_village'] ?? '');
            $pob_c = trim($input['pob_commune'] ?? '');
            $pob_d = trim($input['pob_district'] ?? '');
            $pob_p = trim($input['pob_province'] ?? '');
            $place_of_birth = trim($input['place_of_birth'] ?? '');
            if (empty($place_of_birth)) {
                $place_of_birth = implode(' - ', array_filter([$pob_v, $pob_c, $pob_d, $pob_p]));
            }

            $addr_h = trim($input['addr_house'] ?? '');
            $addr_g = trim($input['addr_group'] ?? '');
            $addr_v = trim($input['addr_village'] ?? '');
            $addr_c = trim($input['addr_commune'] ?? '');
            $addr_d = trim($input['addr_district'] ?? '');
            $addr_p = trim($input['addr_province'] ?? '');
            $current_address = trim($input['current_address'] ?? '');
            if (empty($current_address)) {
                $current_address = implode(' - ', array_filter([$addr_h, $addr_g, $addr_v, $addr_c, $addr_d, $addr_p]));
            }

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':manual_id' => trim($input['manual_id'] ?? ''),
                ':rank' => trim($input['rank'] ?? ''),
                ':surname' => $surname,
                ':given_name' => $given_name,
                ':name_khmer' => $name_khmer,
                ':name_latin' => trim($input['name_latin'] ?? ''),
                ':gender' => trim($input['gender'] ?? 'ប្រុស'),
                ':id_card' => $id_card,
                ':position' => trim($input['position'] ?? ''),
                ':unit_group' => trim($input['unit_group'] ?? ''),
                ':unit' => trim($input['unit'] ?? ''),
                ':rank_date' => !empty($input['rank_date']) ? $input['rank_date'] : null,
                ':position_date' => !empty($input['position_date']) ? $input['position_date'] : null,
                ':dob' => !empty($input['dob']) ? $input['dob'] : null,
                ':enlistment_date' => !empty($input['enlistment_date']) ? $input['enlistment_date'] : null,
                ':framework_date' => !empty($input['framework_date']) ? $input['framework_date'] : null,
                ':education_level' => trim($input['education_level'] ?? ''),
                ':study_local' => trim($input['study_local'] ?? ''),
                ':study_abroad' => trim($input['study_abroad'] ?? ''),
                ':children_count' => trim($input['children_count'] ?? ''),
                ':black_card_expiry' => !empty($input['black_card_expiry']) ? $input['black_card_expiry'] : null,
                ':blue_card_expiry' => !empty($input['blue_card_expiry']) ? $input['blue_card_expiry'] : null,
                ':pob_village' => $pob_v,
                ':pob_commune' => $pob_c,
                ':pob_district' => $pob_d,
                ':pob_province' => $pob_p,
                ':place_of_birth' => $place_of_birth,
                ':addr_house' => $addr_h,
                ':addr_group' => $addr_g,
                ':addr_village' => $addr_v,
                ':addr_commune' => $addr_c,
                ':addr_district' => $addr_d,
                ':addr_province' => $addr_p,
                ':current_address' => $current_address,
                ':marital_status' => trim($input['marital_status'] ?? 'នៅលីវ'),
                ':phone' => trim($input['phone'] ?? ''),
                ':notes' => trim($input['notes'] ?? ''),
                ':photo' => $photo,
                ':family_photo' => $family_photo,
                ':family_name' => trim($input['family_name'] ?? '')
            ]);

            $newId = $db->lastInsertId();
            $response = [
                'success' => true,
                'message' => 'បានរក្សាទុកទិន្នន័យបុគ្គលិកយោធាជោគជ័យ!',
                'id' => $newId
            ];
            break;

        // 4. កែប្រែទិន្នន័យបុគ្គលិកយោធា (Edit Personnel)
        case 'edit':
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = (int)($input['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception('ID បុគ្គលិកមិនត្រឹមត្រូវ!');
            }

            $surname = trim($input['surname'] ?? '');
            $given_name = trim($input['given_name'] ?? '');
            $name_khmer = trim($input['name_khmer'] ?? '');
            if (!$name_khmer && ($surname || $given_name)) {
                $name_khmer = trim("{$surname} {$given_name}");
            }

            $photo = savePhotoIfBase64($input['photo'] ?? '', 'officer', $id);
            $family_photo = savePhotoIfBase64($input['family_photo'] ?? '', 'family', $id);

            $sql = "UPDATE military_personnel SET
                manual_id = :manual_id,
                rank = :rank,
                surname = :surname,
                given_name = :given_name,
                name_khmer = :name_khmer,
                name_latin = :name_latin,
                gender = :gender,
                id_card = :id_card,
                position = :position,
                unit_group = :unit_group,
                unit = :unit,
                rank_date = :rank_date,
                position_date = :position_date,
                dob = :dob,
                enlistment_date = :enlistment_date,
                framework_date = :framework_date,
                education_level = :education_level,
                study_local = :study_local,
                study_abroad = :study_abroad,
                children_count = :children_count,
                black_card_expiry = :black_card_expiry,
                blue_card_expiry = :blue_card_expiry,
                pob_village = :pob_village,
                pob_commune = :pob_commune,
                pob_district = :pob_district,
                pob_province = :pob_province,
                place_of_birth = :place_of_birth,
                addr_house = :addr_house,
                addr_group = :addr_group,
                addr_village = :addr_village,
                addr_commune = :addr_commune,
                addr_district = :addr_district,
                addr_province = :addr_province,
                current_address = :current_address,
                marital_status = :marital_status,
                phone = :phone,
                notes = :notes,
                photo = :photo,
                family_photo = :family_photo,
                family_name = :family_name
                WHERE id = :id";

            $pob_v = trim($input['pob_village'] ?? '');
            $pob_c = trim($input['pob_commune'] ?? '');
            $pob_d = trim($input['pob_district'] ?? '');
            $pob_p = trim($input['pob_province'] ?? '');
            $place_of_birth = trim($input['place_of_birth'] ?? '');
            if (empty($place_of_birth)) {
                $place_of_birth = implode(' - ', array_filter([$pob_v, $pob_c, $pob_d, $pob_p]));
            }

            $addr_h = trim($input['addr_house'] ?? '');
            $addr_g = trim($input['addr_group'] ?? '');
            $addr_v = trim($input['addr_village'] ?? '');
            $addr_c = trim($input['addr_commune'] ?? '');
            $addr_d = trim($input['addr_district'] ?? '');
            $addr_p = trim($input['addr_province'] ?? '');
            $current_address = trim($input['current_address'] ?? '');
            if (empty($current_address)) {
                $current_address = implode(' - ', array_filter([$addr_h, $addr_g, $addr_v, $addr_c, $addr_d, $addr_p]));
            }

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':manual_id' => trim($input['manual_id'] ?? ''),
                ':rank' => trim($input['rank'] ?? ''),
                ':surname' => $surname,
                ':given_name' => $given_name,
                ':name_khmer' => $name_khmer,
                ':name_latin' => trim($input['name_latin'] ?? ''),
                ':gender' => trim($input['gender'] ?? 'ប្រុស'),
                ':id_card' => trim($input['id_card'] ?? ''),
                ':position' => trim($input['position'] ?? ''),
                ':unit_group' => trim($input['unit_group'] ?? ''),
                ':unit' => trim($input['unit'] ?? ''),
                ':rank_date' => !empty($input['rank_date']) ? $input['rank_date'] : null,
                ':position_date' => !empty($input['position_date']) ? $input['position_date'] : null,
                ':dob' => !empty($input['dob']) ? $input['dob'] : null,
                ':enlistment_date' => !empty($input['enlistment_date']) ? $input['enlistment_date'] : null,
                ':framework_date' => !empty($input['framework_date']) ? $input['framework_date'] : null,
                ':education_level' => trim($input['education_level'] ?? ''),
                ':study_local' => trim($input['study_local'] ?? ''),
                ':study_abroad' => trim($input['study_abroad'] ?? ''),
                ':children_count' => trim($input['children_count'] ?? ''),
                ':black_card_expiry' => !empty($input['black_card_expiry']) ? $input['black_card_expiry'] : null,
                ':blue_card_expiry' => !empty($input['blue_card_expiry']) ? $input['blue_card_expiry'] : null,
                ':pob_village' => $pob_v,
                ':pob_commune' => $pob_c,
                ':pob_district' => $pob_d,
                ':pob_province' => $pob_p,
                ':place_of_birth' => $place_of_birth,
                ':addr_house' => $addr_h,
                ':addr_group' => $addr_g,
                ':addr_village' => $addr_v,
                ':addr_commune' => $addr_c,
                ':addr_district' => $addr_d,
                ':addr_province' => $addr_p,
                ':current_address' => $current_address,
                ':marital_status' => trim($input['marital_status'] ?? 'នៅលីវ'),
                ':phone' => trim($input['phone'] ?? ''),
                ':notes' => trim($input['notes'] ?? ''),
                ':photo' => $photo,
                ':family_photo' => $family_photo,
                ':family_name' => trim($input['family_name'] ?? ''),
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
