<?php

class ComplaintsController
{
    private $complaintModel;

    public function __construct($db)
    {
        require_once __DIR__ . '/../model/complaintModel.php';
        $this->complaintModel = new ComplaintModel($db);
    }

    // 
    public function createComplaint($customer_id, $product_id, $complaint_type_id, $details, $image_path = null)
    {
        $queryResult = $this->complaintModel->createComplaint($customer_id, $product_id, $complaint_type_id, $details, $image_path);
        if ($queryResult['ok']) {
            echo "Complaint created with ID: " . $queryResult['complaint_id'];
        } else {
            echo "Error creating complaint: " . $queryResult['error'];
        }
    }

    public function getAllComplaints()
    {
        // Implementation for retrieving all complaints
        $queryResult = $this->complaintModel->getAllComplaints();
        if ($queryResult['ok']) {
            return $queryResult['complaints'];
        } else {
            echo "Error retrieving complaints: " . $queryResult['error'];
            return [];
        }
    }

    public function updateComplaintStatus($complaint_id, $status)
    {
        $queryResult = $this->complaintModel->updateStatus($complaint_id, $status);
        if ($queryResult['ok']) {
            echo "Complaint status updated successfully.";
        } else {
            echo "Error updating complaint status: " . $queryResult['error'];
        }
    }

    public function assignTechToComplaint($complaint_id, $tech_id)
    {
        $queryResult = $this->complaintModel->assignTech($complaint_id, $tech_id);
        if ($queryResult['ok']) {
            echo "Tech assigned to complaint successfully.";
        } else {
            echo "Error assigning tech to complaint: " . $queryResult['error'];
        }
    }

    public function resolveComplaint($complaint_id, $resolution_date = null)
    {
        $queryResult = $this->complaintModel->resolveComplaint($complaint_id, $resolution_date);
        if ($queryResult['ok']) {
            echo "Complaint resolved successfully.";
        } else {
            echo "Error resolving complaint: " . $queryResult['error'];
        }
    }
    
    public function appendToComplaintDetails($complaint_id, $new_note, $author_label = null)
    {
        $queryResult = $this->complaintModel->appendDetails($complaint_id, $new_note, $author_label);
        if ($queryResult['ok']) {
            echo "Note appended to complaint details successfully.";
        } else {
            echo "Error appending note to complaint details: " . $queryResult['error'];
        }
    }
}
