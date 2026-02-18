<?php
class NewComplaintController
{
    private $complaintModel;

    public function __construct($complaintModel)
    {
        $this->complaintModel = $complaintModel;
    }

    public function create()
    {

        $typesResult = $this->complaintModel->getComplaintTypes();
        $types = $typesResult['ok'] ? $typesResult['types'] : [];

        $productsResult = $this->complaintModel->getProductTypes();
        $products = $productsResult['ok'] ? $productsResult['products'] : [];

        $errors = [];
        $old = [
            'complaintTypeId' => '',
            'productId' => '',
            'details' => ''
        ];

        require __DIR__ . '/../views/complaintForm/newComplaintForm.php';
    }
}
