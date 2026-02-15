<?php

class ComplaintController
{
    private $complaintModel;

    public function __construct($db)
    {
        require_once __DIR__ . '/../model/complaintModel.php';
        $this->complaintModel = new ComplaintModel($db);
    }

    // GET: show form
    public function create()
    {
        $typesResult = $this->complaintModel->getComplaintTypes();
        $types = $typesResult['ok'] ? $typesResult['types'] : [];

        $errors = [];
        $old = ['complaintTypeId' => '', 'details' => ''];

        require __DIR__ . '/../views/complaintForm/newComplaintForm.php';
    }

    // POST: handle submit
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=newComplaint");
            exit;
        }

        // customer_id comes from session
        $customer_id = (int)($_SESSION['user_id'] ?? 0);
        if ($customer_id <= 0) {
            header("Location: index.php?action=showLogin");
            exit;
        }

        $complaint_type_id = (int)($_POST['complaintTypeId'] ?? 0);
        $details = trim($_POST['details'] ?? '');

        $errors = [];
        if ($complaint_type_id <= 0) $errors[] = "Please select a complaint type.";
        if ($details === '') $errors[] = "Description is required.";


        $image_path = null;
        if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Image upload failed.";
            } else {
                // basic validation: ensure it's an image
                $tmp = $_FILES['image']['tmp_name'];
                $info = @getimagesize($tmp);
                if ($info === false) {
                    $errors[] = "Uploaded file is not a valid image.";
                } else {
                    $uploadDir = __DIR__ . '/../../public/uploads/complaints';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $safeExt = strtolower(preg_replace('/[^a-z0-9]/', '', $ext));
                    if ($safeExt === '') $safeExt = 'jpg';

                    $filename = 'c_' . $customer_id . '_' . time() . '.' . $safeExt;
                    $dest = $uploadDir . '/' . $filename;

                    if (!move_uploaded_file($tmp, $dest)) {
                        $errors[] = "Could not save uploaded image.";
                    } else {
                        // store web path in DB
                        $image_path = '/customer-complaint-tracking-system/public/uploads/complaints/' . $filename;
                    }
                }
            }
        }

        if (!empty($errors)) {
            $typesResult = $this->complaintModel->getComplaintTypes();
            $types = $typesResult['ok'] ? $typesResult['types'] : [];

            $old = ['complaintTypeId' => $complaint_type_id, 'details' => $details];
            require __DIR__ . '/../views/complaintForm/newComplaintForm.php';
            return;
        }


        $product_id = 1;

        $result = $this->complaintModel->createComplaint(
            $customer_id,
            $complaint_type_id,
            $details,
            $product_id,
            $image_path
        );

        if (!$result['ok']) {
            $typesResult = $this->complaintModel->getComplaintTypes();
            $types = $typesResult['ok'] ? $typesResult['types'] : [];

            $errors[] = $result['error'];
            $old = ['complaintTypeId' => $complaint_type_id, 'details' => $details];
            require __DIR__ . '/../views/complaintForm/newComplaintForm.php';
            return;
        }


        header("Location: index.php?action=dashboard");
        exit;
    }
}
