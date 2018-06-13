<?php

class AdminProductController extends AdminBase
{
    public function actionIndex()
    {
        self::checkAdmin();

        $productList = Product::getProductList();

        require_once (ROOT.'/views/admin_product/index.php');
        return true;
    }

    public function actionCreate()
    {
        self::checkAdmin();

        $categoriesList = Category::getCategoriesListAdmin();

        if (isset($_POST['submit'])) {

            $options['name'] = $_POST['name'];
            $options['code'] = $_POST['code'];
            $options['price'] = $_POST['price'];
            $options['category_id'] = $_POST['category_id'];
            $options['brand'] = $_POST['brand'];
            $options['availability'] = $_POST['availability'];
            $options['description'] = $_POST['description'];
            $options['is_new'] = $_POST['is_new'];
            $options['is_recommended'] = $_POST['is_recommended'];
            $options['status'] = $_POST['status'];

            $errors = false;
            if (!isset($options['name']) || empty($options['name'])) {
                $errors[] = 'Заполните поля';
            }
            if ($errors == false) {

                $id = Product::createProduct($options);
                if ($id) {
                    if (is_uploaded_file($_FILES["image"]["tmp_name"])) {
                        move_uploaded_file($_FILES["image"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . "/upload/images/products/{$id}.jpg");
                    }
                };
                header("Location: /admin/product");
            }
        }
        require_once(ROOT . '/views/admin_product/create.php');
        return true;
    }

    public function actionUpdate($id)
    {
        self::checkAdmin();

        $categoriesList = Category::getCategoriesListAdmin();
        $product = Product::getProductById($id);
        if(isset($_POST['submit'])){
            $options['name'] = $_POST['name'];
            $options['code'] = $_POST['code'];
            $options['price'] = $_POST['price'];
            $options['category_id'] = $_POST['category_id'];
            $options['brand'] = $_POST['brand'];
            $options['availability'] = $_POST['availability'];
            $options['description'] = $_POST['description'];
            $options['is_new'] = $_POST['is_new'];
            $options['is_recommended'] = $_POST['is_recommended'];
            $options['status'] = $_POST['status'];

            if(Product::updateProductByID($id, $options)){
                if(is_uploaded_file($_FILES["image"]["tmp_name"])){
                    move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER['DOCUMENT_ROOT']."/upload/images/products/{$id}.jpg");
                }
            }
            header("Location: /admin/product");
        }
        require_once (ROOT.'/views/admin_product/update.php');
        return true;
    }

    public function actionDelete($id)
    {
        self::checkAdmin();
        if(isset($_POST['submit'])) {
            Product::deleteProductById($id);

            header("Location: /admin/product");
        }

        require_once (ROOT.'/views/admin_product/delete.php');

        return true;
    }
}