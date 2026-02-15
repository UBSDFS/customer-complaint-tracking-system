<?php
public function getComplaintTypes(): array
{
    $sql = "SELECT complaint_type_id, name FROM complaint_types ORDER BY complaint_type_id";
    $result = $this->db->query($sql);
    if (!$result) return ['ok' => false, 'error' => $this->db->error];

    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row;
    }

    return ['ok' => true, 'types' => $types];
}
