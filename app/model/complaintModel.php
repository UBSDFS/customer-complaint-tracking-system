<?php

class ComplaintModel
{
    private $db;

    //receive database connection
    public function __construct($databaseConnection)
    {
        $this->db = $databaseConnection;
    }
    public function createComplaint(
        int $customer_id,
        int $complaint_type_id,
        string $details,
        ?int $product_id = null,
        ?string $image_path = null
    ): array {
        if ($customer_id <= 0 || $complaint_type_id <= 0) {
            return ['ok' => false, 'error' => 'Invalid IDs provided.'];
        }

        $details = trim($details);
        if ($details === '') {
            return ['ok' => false, 'error' => 'Complaint details cannot be empty.'];
        }

        // Default status to open on creation
        $status = 'open';

        $sql = "INSERT INTO complaints (customer_id, product_id, complaint_type_id, details, image_path, status)
            VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['ok' => false, 'error' => $this->db->error];


        $stmt->bind_param("iiisss", $customer_id, $product_id, $complaint_type_id, $details, $image_path, $status);

        if (!$stmt->execute()) return ['ok' => false, 'error' => $stmt->error];

        return ['ok' => true, 'complaint_id' => $stmt->insert_id];
    }


    // Assign a tech to a complaint
    public function assignTech(int $complaint_id, int $tech_id): array
    {
        if ($complaint_id <= 0 || $tech_id <= 0) {
            return ['ok' => false, 'error' => 'Invalid complaint_id or tech_id.'];
        }


        $sql = "UPDATE complaints
            SET tech_id = ?,
                status = CASE
                    WHEN status = 'open' THEN 'assigned'
                    ELSE status
                END
            WHERE complaint_id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'error' => $this->db->error];
        }

        $stmt->bind_param("ii", $tech_id, $complaint_id);

        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => $stmt->error];
        }

        if ($stmt->affected_rows === 0) {

            return ['ok' => false, 'error' => 'No complaint updated (invalid complaint_id or no change).'];
        }

        return ['ok' => true];
    }


    public function updateStatus(int $complaint_id, string $status): array
    {
        if ($complaint_id <= 0) {
            return ['ok' => false, 'error' => 'Invalid complaint_id.'];
        }

        $allowed = ['open', 'assigned', 'in_progress', 'resolved'];

        if (!in_array($status, $allowed, true)) {
            return ['ok' => false, 'error' => 'Invalid status value.'];
        }


        if ($status === 'resolved') {
            return ['ok' => false, 'error' => "Use resolveComplaint() to mark a complaint as resolved."];
        }


        $sql = "UPDATE complaints
            SET status = ?,
                complaint_resolution_date = NULL
            WHERE complaint_id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'error' => $this->db->error];
        }

        $stmt->bind_param("si", $status, $complaint_id);

        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'error' => 'No complaint updated (invalid complaint_id or no change).'];
        }

        return ['ok' => true];
    }


    public function resolveComplaint(int $complaint_id, ?string $resolution_date = null): array
    {
        if ($complaint_id <= 0) {
            return ['ok' => false, 'error' => 'Invalid complaint_id.'];
        }

        // If a date is provided, it should be YYYY-MM-DD
        if ($resolution_date !== null) {
            $resolution_date = trim($resolution_date);
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $resolution_date)) {
                return ['ok' => false, 'error' => 'resolution_date must be in YYYY-MM-DD format or null.'];
            }

            $sql = "UPDATE complaints
                SET status = 'resolved',
                    complaint_resolution_date = ?
                WHERE complaint_id = ?";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return ['ok' => false, 'error' => $this->db->error];
            }

            $stmt->bind_param("si", $resolution_date, $complaint_id);
        } else {
            // If no date provided, set it to today
            $sql = "UPDATE complaints
                SET status = 'resolved',
                    complaint_resolution_date = CURDATE()
                WHERE complaint_id = ?";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return ['ok' => false, 'error' => $this->db->error];
            }

            $stmt->bind_param("i", $complaint_id);
        }

        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'error' => 'No complaint updated (invalid complaint_id or no change).'];
        }

        return ['ok' => true];
    }


    public function appendDetails(int $complaint_id, string $new_note, ?string $author_label = null): array
    {
        if ($complaint_id <= 0) {
            return ['ok' => false, 'error' => 'Invalid complaint_id.'];
        }

        $new_note = trim($new_note);
        if ($new_note === '') {
            return ['ok' => false, 'error' => 'New note cannot be empty.'];
        }

        // Optional label (ex: "Tech", "Admin", "Kyle", "Ulysses (Tech)")
        $author_label = $author_label !== null ? trim($author_label) : null;

        // Use a transaction so read + write are consistent
        if (!$this->db->begin_transaction()) {
            return ['ok' => false, 'error' => 'Failed to start transaction: ' . $this->db->error];
        }

        try {
            // Read current details
            $selectSql = "SELECT details FROM complaints WHERE complaint_id = ? FOR UPDATE";
            $selectStmt = $this->db->prepare($selectSql);
            if (!$selectStmt) {
                throw new Exception("Prepare failed (select): " . $this->db->error);
            }

            $selectStmt->bind_param("i", $complaint_id);

            if (!$selectStmt->execute()) {
                throw new Exception("Execute failed (select): " . $selectStmt->error);
            }

            $result = $selectStmt->get_result();
            if (!$result || $result->num_rows === 0) {
                throw new Exception("Complaint not found.");
            }

            $row = $result->fetch_assoc();
            $existing = (string)($row['details'] ?? '');


            $timestamp = date('Y-m-d H:i');
            $header = "--- UPDATE ({$timestamp})";
            if ($author_label !== null && $author_label !== '') {
                $header .= " [{$author_label}]";
            }
            $header .= " ---";

            $block = $header . "\n" . $new_note;


            $combined = trim($existing);
            if ($combined !== '') {
                $combined .= "\n\n" . $block;
            } else {
                $combined = $block;
            }


            $updateSql = "UPDATE complaints SET details = ? WHERE complaint_id = ?";
            $updateStmt = $this->db->prepare($updateSql);
            if (!$updateStmt) {
                throw new Exception("Prepare failed (update): " . $this->db->error);
            }

            $updateStmt->bind_param("si", $combined, $complaint_id);

            if (!$updateStmt->execute()) {
                throw new Exception("Execute failed (update): " . $updateStmt->error);
            }

            $this->db->commit();

            return ['ok' => true];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    // Should only be used by admins/techs to delete duplicate complaints
    public function deleteComplaint(int $complaint_id): array
    {
        if ($complaint_id <= 0) {
            return ['ok' => false, 'error' => 'Invalid complaint_id.'];
        }

        $sql = "DELETE FROM complaints WHERE complaint_id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'error' => $this->db->error];
        }

        $stmt->bind_param("i", $complaint_id);

        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => $stmt->error];
        }

        if ($stmt->affected_rows === 0) {
            return ['ok' => false, 'error' => 'No complaint deleted (not found).'];
        }

        return ['ok' => true];
    }

    // Get all complaints for a customer
    public function getComplaintsByCustomerId(int $customer_id): array
    {
        if ($customer_id <= 0) {
            return ['ok' => false, 'error' => 'Invalid customer_id.'];
        }

        $sql = "SELECT c.*, ct.name AS complaint_type_name
        FROM complaints c
        JOIN complaint_types ct ON c.complaint_type_id = ct.complaint_type_id
        WHERE c.customer_id = ?
        ORDER BY c.complaint_id DESC";


        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'error' => $this->db->error];
        }

        $stmt->bind_param("i", $customer_id);

        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => $stmt->error];
        }

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return ['ok' => false, 'error' => 'No complaints found for this customer.'];
        }

        $complaints = [];
        while ($row = $result->fetch_assoc()) {
            $complaints[] = $row;
        }

        return ['ok' => true, 'complaints' => $complaints];
    }

    // Get a single complaint by complaint_id
    public function getComplaintById(int $complaint_id): array
    {
        if ($complaint_id <= 0) {
            return ['ok' => false, 'error' => 'Invalid complaint_id.'];
        }

        $sql = "SELECT * FROM complaints WHERE complaint_id = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'error' => $this->db->error];
        }

        $stmt->bind_param("i", $complaint_id);

        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => $stmt->error];
        }

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return ['ok' => false, 'error' => 'Complaint not found.'];
        }

        $complaint = $result->fetch_assoc();
        return ['ok' => true, 'complaint' => $complaint];
    }

    // Get all complaints in the system
    public function getAllComplaints(): array
    {
        $sql = "SELECT * FROM complaints";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'error' => $this->db->error];
        }

        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => $stmt->error];
        }

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return ['ok' => false, 'error' => 'No complaints found in the system.'];
        }

        $complaints = [];
        while ($row = $result->fetch_assoc()) {
            $complaints[] = $row;
        }

        return ['ok' => true, 'complaints' => $complaints];
    }
    public function getComplaintTypes(): array
    {
        $sql = "SELECT complaint_type_id, name
                FROM complaint_types
                ORDER BY complaint_type_id";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['ok' => false, 'error' => $this->db->error];

        if (!$stmt->execute()) return ['ok' => false, 'error' => $stmt->error];

        $result = $stmt->get_result();
        $types = [];
        while ($row = $result->fetch_assoc()) {
            $types[] = $row;
        }

        return ['ok' => true, 'types' => $types];
    }
    public function getComplaintsAssignedToTech(int $techId): array
    {
        $sql = "SELECT * FROM complaints WHERE tech_id = ? ORDER BY complaint_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $techId);
        $stmt->execute();
        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function updateComplaintTechFields(
        int $complaintId,
        string $technicianNotes,
        string $status,
        ?string $resolutionDate,
        string $resolutionNotes
    ): bool {
        $sql = "UPDATE complaints
            SET technician_notes = ?,
                status = ?,
                complaint_resolution_date = ?,
                resolution_notes = ?
            WHERE complaint_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "ssssi",
            $technicianNotes,
            $status,
            $resolutionDate,
            $resolutionNotes,
            $complaintId
        );

        return $stmt->execute();
    }
    public function getUnassignedOpenComplaints(): array
    {
        $sql = "SELECT c.*, ct.name AS complaint_type_name
            FROM complaints c
            JOIN complaint_types ct ON c.complaint_type_id = ct.complaint_type_id
            WHERE c.tech_id IS NULL
              AND c.status IN ('open','assigned','in_progress')
            ORDER BY c.complaint_id DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['ok' => false, 'error' => $this->db->error];

        if (!$stmt->execute()) return ['ok' => false, 'error' => $stmt->error];

        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return ['ok' => true, 'complaints' => $rows];
    }
    public function getOpenComplaintsWithTech(): array
    {
        $sql = "SELECT
                c.complaint_id,
                c.status,
                c.complaint_resolution_date,
                c.details,
                c.image_path,
                c.customer_id,
                c.tech_id,
                ct.name AS complaint_type_name,

                -- customer name
                CONCAT(cp.first_name, ' ', cp.last_name) AS customer_name,

                -- tech name (nullable)
                CONCAT(ep.first_name, ' ', ep.last_name) AS tech_name

            FROM complaints c
            JOIN complaint_types ct ON c.complaint_type_id = ct.complaint_type_id
            JOIN customer_profiles cp ON c.customer_id = cp.user_id
            LEFT JOIN employee_profiles ep ON c.tech_id = ep.user_id

            WHERE c.status IN ('open','assigned','in_progress')
            ORDER BY c.complaint_id DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return ['ok' => false, 'error' => $this->db->error];

        if (!$stmt->execute()) return ['ok' => false, 'error' => $stmt->error];

        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return ['ok' => true, 'complaints' => $rows];
    }
}
