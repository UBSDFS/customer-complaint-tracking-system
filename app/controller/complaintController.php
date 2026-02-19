<?php
require_once __DIR__ . '/../model/complaintModel.php';

class ComplaintController
{
    private ComplaintModel $complaintModel;

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

        $productsResult = $this->complaintModel->getProductTypes();
        $products = $productsResult['ok'] ? $productsResult['products'] : [];

        $errors = [];
        $old = ['complaintTypeId' => '', 'details' => '', 'productId' => ''];

        require __DIR__ . '/../views/complaintForm/newComplaintForm.php';
    }

    // POST: handle submit
    public function store()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=newComplaint");
            exit;
        }

        $customer_id = (int)($_SESSION['user_id'] ?? 0);
        if ($customer_id <= 0) {
            header("Location: index.php?action=showLogin");
            exit;
        }


        $complaint_type_id = (int)($_POST['complaintTypeId'] ?? 0);
        $details = trim($_POST['details'] ?? '');
        $product_id = (int)($_POST["productId"] ?? 0);


        $errors = [];
        if ($complaint_type_id <= 0) $errors[] = "Please select a complaint type.";
        if ($details === '') $errors[] = "Description is required.";
        if ($product_id <= 0) $errors[] = 'Please select a product type.';




        $image_path = null;
        if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Image upload failed.";
            } else {
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
                        $image_path = '/customer-complaint-tracking-system/public/uploads/complaints/' . $filename;
                    }
                }
            }
        }

        if (!empty($errors)) {
            $typesResult = $this->complaintModel->getComplaintTypes();
            $types = $typesResult['ok'] ? $typesResult['types'] : [];

            $productsResult = $this->complaintModel->getProductTypes();
            $products = $productsResult['ok'] ? $productsResult['products'] : [];

            $old = ['complaintTypeId' => $complaint_type_id, 'details' => $details, 'productId' => $product_id];
            require __DIR__ . '/../views/complaintForm/newComplaintForm.php';
            return;
        }


        // $product_id = 1;

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

            $productsResult = $this->complaintModel->getProductTypes();
            $products = $productsResult['ok'] ? $productsResult['products'] : [];

            $errors[] = $result['error'];
            $old = ['complaintTypeId' => $complaint_type_id, 'details' => $details, 'productId' => $product_id];
            require __DIR__ . '/../views/complaintForm/newComplaintForm.php';
            return;
        }

        header("Location: index.php?action=dashboard");
        exit;
    }

    // GET: complaint details (customer/tech/admin)
    public function show()
    {
        $complaintId = isset($_GET['complaint_id']) ? (int)$_GET['complaint_id'] : 0;
        if ($complaintId <= 0) {
            http_response_code(400);
            echo "Invalid complaint id";
            return;
        }

        $result = $this->complaintModel->getComplaintById($complaintId);
        if (!$result['ok']) {
            http_response_code(404);
            echo $result['error'];
            return;
        }
        $complaint = $result['complaint'];

        $role = $_SESSION['role'] ?? null;
        $userId = (int)($_SESSION['user_id'] ?? 0);

        if (!$role || $userId <= 0) {
            header("Location: index.php?action=showLogin");
            exit;
        }

        if ($role === 'customer' && (int)$complaint['customer_id'] !== $userId) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        if ($role === 'technician' && (int)$complaint['tech_id'] !== $userId) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        require BASE_PATH . '/app/views/complaints/show.php';
    }

    // GET: tech/admin edit screen
    public function edit()
    {
        $role = $_SESSION['role'] ?? null;
        if ($role !== 'technician' && $role !== 'admin') {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        $complaintId = isset($_GET['complaint_id']) ? (int)$_GET['complaint_id'] : 0;
        if ($complaintId <= 0) {
            http_response_code(400);
            echo "Invalid id";
            return;
        }

        $result = $this->complaintModel->getComplaintById($complaintId);
        if (!$result['ok']) {
            http_response_code(404);
            echo $result['error']; // "Complaint not found."
            return;
        }

        $complaint = $result['complaint'];

        $userId = (int)($_SESSION['user_id'] ?? 0);


        if ($role === 'technician' && (int)$complaint['tech_id'] !== $userId) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        require BASE_PATH . '/app/views/complaints/edit.php';
    }


    // POST: update tech/admin fields
    public function update()
    {
        $role = $_SESSION['role'] ?? null;
        if ($role !== 'technician' && $role !== 'admin') {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $complaintId = (int)($_POST['complaint_id'] ?? 0);
        $technicianNotes = trim($_POST['technician_notes'] ?? '');
        $status = trim($_POST['status'] ?? 'open');
        $resolutionNotes = trim($_POST['resolution_notes'] ?? '');

        if ($complaintId <= 0) {
            http_response_code(400);
            echo "Invalid id";
            return;
        }


        $resolutionDate = null;
        if ($status === 'resolved') {
            if ($resolutionNotes === '') {
                $_SESSION['flash_error'] = "Resolution notes are required to resolve a complaint.";
                header("Location: index.php?action=editComplaint&complaint_id=" . $complaintId);
                exit;
            }
            $resolutionDate = date('Y-m-d');
        }

        $ok = $this->complaintModel->updateComplaintTechFields(
            $complaintId,
            $technicianNotes,
            $status,
            $resolutionDate,
            $resolutionNotes
        );

        if (!$ok) {
            http_response_code(500);
            echo "Update failed";
            return;
        }

        header("Location: index.php?action=techDashboard");
        exit;
    }

    // GET: view complaint details
    public function view()
    {
        $role = $_SESSION['role'] ?? null;
        $userId = (int)($_SESSION['user_id'] ?? 0);

        if (!$role || $userId <= 0) {
            header("Location: index.php?action=showLogin");
            exit;
        }


        $complaintId = (int)($_GET['complaint_id'] ?? 0);
        if ($complaintId <= 0) {
            http_response_code(400);
            echo "Invalid id";
            return;
        }

        $result = $this->complaintModel->getComplaintById($complaintId);
        if (!$result['ok']) {
            http_response_code(404);
            echo $result['error'];
            return;
        }

        $complaint = $result['complaint'];

        // Authorization
        if ($role === 'customer' && (int)($complaint['customer_id'] ?? 0) !== $userId) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        if ($role === 'technician' && (int)($complaint['tech_id'] ?? 0) !== $userId) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        // Render complaint details view
        $viewFile = BASE_PATH . '/app/views/complaintForm/complaintview.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View not found: " . htmlspecialchars($viewFile);
            return;
        }

        require $viewFile;
    }
    public function editCustomer()
    {
        $customer_id = (int)($_SESSION['user_id'] ?? 0);
        $role = $_SESSION['role'] ?? '';

        if ($role !== 'customer' || $customer_id <= 0) {
            header("Location: index.php?action=showLogin");
            exit;
        }

        $complaint_id = (int)($_GET['id'] ?? 0);

        if ($complaint_id <= 0) {
            http_response_code(400);
            echo "Invalid id";
            return;
        }


        $result = $this->complaintModel->getComplaintById($complaint_id);
        if (!$result['ok']) {
            http_response_code(404);
            echo $result['error'];
            return;
        }

        $complaint = $result['complaint'];

        //check who owns the complaint - only owner can edit
        if ((int)($complaint['customer_id'] ?? 0) !== $customer_id) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        // Prevent editing if complaint is resolved
        if (($complaint['status'] ?? '') === 'resolved') {
            http_response_code(403);
            echo "Cannot edit a resolved complaint.";
            return;
        }

        $typesResult = $this->complaintModel->getComplaintTypes();
        $types = $typesResult['ok'] ? $typesResult['types'] : [];

        $productsResult = $this->complaintModel->getProductTypes();
        $products = $productsResult['ok'] ? $productsResult['products'] : [];

        $errors = [];

        //pre-fill form with existing complaint data
        $old = [
            'complaintTypeId' => (int)($complaint['complaint_type_id'] ?? 0),
            'details'         => (string)($complaint['details'] ?? ''),
            'productId'       => (int)($complaint['product_id'] ?? 0),
            'imagePath'       => (string)($complaint['image_path'] ?? ''),
        ];

        require __DIR__ . '/../views/complaintForm/editComplaintForm.php';
    }
    public function updateCustomer()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=dashboard");
            exit;
        }

        $customer_id = (int)($_SESSION['user_id'] ?? 0);
        $role = $_SESSION['role'] ?? '';

        if ($role !== 'customer' || $customer_id <= 0) {
            header("Location: index.php?action=showLogin");
            exit;
        }

        $complaint_id = (int)($_POST['complaint_id'] ?? 0);
        if ($complaint_id <= 0) {
            http_response_code(400);
            echo "Invalid id";
            return;
        }

        // fetch existing complaint (ownership + old image path)
        $existingRes = $this->complaintModel->getComplaintById($complaint_id);
        if (!$existingRes['ok']) {
            http_response_code(404);
            echo $existingRes['error'];
            return;
        }

        $complaint = $existingRes['complaint'];

        if ((int)($complaint['customer_id'] ?? 0) !== $customer_id) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        if (($complaint['status'] ?? '') === 'resolved') {
            http_response_code(403);
            echo "Cannot edit a resolved complaint.";
            return;
        }

        $complaint_type_id = (int)($_POST['complaintTypeId'] ?? 0);
        $details = trim($_POST['details'] ?? '');
        $product_id = (int)($_POST["productId"] ?? 0);

        $errors = [];
        if ($complaint_type_id <= 0) $errors[] = "Please select a complaint type.";
        if ($details === '') $errors[] = "Description is required.";
        if ($product_id <= 0) $errors[] = 'Please select a product type.';

        // default: keep existing image
        $image_path = (string)($complaint['image_path'] ?? '');

        // replace only if new file uploaded
        if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Image upload failed.";
            } else {
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
                        $image_path = '/customer-complaint-tracking-system/public/uploads/complaints/' . $filename;
                    }
                }
            }
        }

        // re-render edit form on errors (same pattern as store())
        if (!empty($errors)) {
            $typesResult = $this->complaintModel->getComplaintTypes();
            $types = $typesResult['ok'] ? $typesResult['types'] : [];

            $productsResult = $this->complaintModel->getProductTypes();
            $products = $productsResult['ok'] ? $productsResult['products'] : [];

            $old = [
                'complaintTypeId' => $complaint_type_id,
                'details' => $details,
                'productId' => $product_id,
                'imagePath' => $image_path,
            ];

            require __DIR__ . '/../views/complaintForm/editComplaintForm.php';
            return;
        }

        // call model update
        $updateRes = $this->complaintModel->updateCustomerComplaint(
            $complaint_id,
            $complaint_type_id,
            $details,
            $product_id,
            $image_path
        );

        if (!$updateRes['ok']) {
            $typesResult = $this->complaintModel->getComplaintTypes();
            $types = $typesResult['ok'] ? $typesResult['types'] : [];

            $productsResult = $this->complaintModel->getProductTypes();
            $products = $productsResult['ok'] ? $productsResult['products'] : [];

            $errors[] = $updateRes['error'] ?? 'Update failed.';
            $old = [
                'complaintTypeId' => $complaint_type_id,
                'details' => $details,
                'productId' => $product_id,
                'imagePath' => $image_path,
            ];

            require __DIR__ . '/../views/complaintForm/editComplaintForm.php';
            return;
        }

        header("Location: index.php?action=dashboard");
        exit;
    }

    private function requireTech()
    {
        $role = $_SESSION['role'] ?? null;
        $userId = (int)($_SESSION['user_id'] ?? 0);

        if ($role !== 'tech' || $userId <= 0) {
            http_response_code(403);
            echo "Forbidden";
            exit;
        }
    }

    public function techUpdateComplaint()
    {
        $this->requireTech();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=techDashboard');
            exit;
        }

        $complaintId = (int)($_POST['complaint_id'] ?? 0);
        if ($complaintId <= 0) {
            $_SESSION['flash_error'] = 'Invalid complaint.';
            header('Location: index.php?action=techDashboard');
            exit;
        }

        $status = trim($_POST['status'] ?? '');
        $updateNote = trim($_POST['update_note'] ?? '');
        $resolutionSummary = trim($_POST['resolution_summary'] ?? '');


        $allowed = ['open', 'assigned', 'in_progress', 'resolved'];
        if (!in_array($status, $allowed, true)) {
            $_SESSION['flash_error'] = 'Invalid status value.';
            header("Location: index.php?action=techDashboard&complaint_id={$complaintId}");
            exit;
        }

        // If resolved, require resolution summary
        if ($status === 'resolved' && $resolutionSummary === '') {
            $_SESSION['flash_error'] = 'Resolution summary is required to resolve a complaint.';
            header("Location: index.php?action=techDashboard&complaint_id={$complaintId}");
            exit;
        }

        // Append update note if provided
        if ($updateNote !== '') {
            $appendRes = $this->complaintModel->appendDetails(
                $complaintId,
                $updateNote,
                'TECH'
            );

            if (!$appendRes['ok']) {
                $_SESSION['flash_error'] = $appendRes['error'] ?? 'Failed to append update note.';
                header("Location: index.php?action=techDashboard&complaint_id={$complaintId}");
                exit;
            }
        }

        // If status is resolved, append resolution summary and set resolution date; else just update status
        if ($status === 'resolved') {
            $appendRes2 = $this->complaintModel->appendDetails(
                $complaintId,
                "RESOLUTION: " . $resolutionSummary,
                'TECH'
            );

            if (!$appendRes2['ok']) {
                $_SESSION['flash_error'] = $appendRes2['error'] ?? 'Failed to append resolution summary.';
                header("Location: index.php?action=techDashboard&complaint_id={$complaintId}");
                exit;
            }

            // Updates status, sets resolution date, and saves resolution summary in a separate field
            $res = $this->complaintModel->resolveComplaint($complaintId);
        } else {
            // Just update status without changing resolution date or summary
            $res = $this->complaintModel->updateStatus($complaintId, $status);
        }

        if (!$res['ok']) {
            $_SESSION['flash_error'] = $res['error'] ?? 'Update failed.';
            header("Location: index.php?action=techDashboard&complaint_id={$complaintId}");
            exit;
        }

        $_SESSION['flash_success'] = 'Saved.';
        header("Location: index.php?action=techDashboard&complaint_id={$complaintId}");
        exit;
    }
}
