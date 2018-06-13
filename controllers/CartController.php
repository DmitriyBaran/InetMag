<?php

class CartController
{

    public function actionAdd($id)
    {
        // Добавляем товар в корзину
        Cart::addProduct($id);

        // Возвращаем пользователя на страницу
        $referrer = $_SERVER['HTTP_REFERER'];
        header("Location: $referrer");
    }

    public function actionAddAjax($id)
    {
        echo Cart::addProduct($id);
        return true;
    }

    public function actionIndex()
    {
        $categories = array();
        $categories = Category::getCategoriesList();

        $productsInCart = false;

        // Получим данные из корзины
        $productsInCart = Cart::getProducts();

        if ($productsInCart) {
            // Получаем полную информацию о товарах для списка
            $productsIds = array_keys($productsInCart);
            $products = Product::getProdustsByIds($productsIds);

            // Получаем общую стоимость товаров
            $totalPrice = Cart::getTotalPrice($products);
        }

        require_once(ROOT . '/views/cart/index.php');

        return true;
    }

    public function actionDelete($id)
    {
        Cart::deleteProduct($id);

        header("Location: /cart");
    }

    public function actionCheckout()
    {
        $categories = array();
        $categories = Category::getCategoriesList();

        $result = false;

        if (isset($_POST['submit'])) {
            $userName = $_POST['userName'];
            $userPhone = $_POST['userPhone'];
            $userComment = $_POST['userComment'];

            $errors = false;
            if (!User::checkName($userName))
                $errors[] = 'Неправильное имя';
            if (!User::checkPhone($userPhone))
                $errors[] = 'Неправильный телефон';

            if ($errors == false) {
                $productsInCart = Cart::getProducts();
                if (User::isGuest()) {
                    $userId = false;
                } else {
                    $userId = User::checkLogged();
                }

                $result = Order::save($userName, $userPhone, $userComment, $userId, $productsInCart);
                if ($result) {
                    $adminEmail = 'dimabaran96@mail.ru';
                    $message = 'Заказ на сайте';
                    $subject = 'Новый заказ';
                    mail($adminEmail, $subject, $message);

                    Cart::clear();
                }
            } else {

                //Форма заполнена корректно? -Нет
                $productsInCart = Cart::getProducts();
                $productsIds = array_keys($productsInCart);
                $products = Product::getProdustsByIds($productsIds);
                $totalPrice = Cart::getTotalPrice($products);
                $totalQuantity = Cart::countItems();
            }
        } else {
            //Форма отправлена? -Нет
            $productsInCart = Cart::getProducts();

            //В корзине есть товары?
            if ($productsInCart == false) {
                //Если в корзине нет товаров, тогда отправляем на главную

                header("Location: /");
            } else {
                //В корзине есть товары, выводим общюю стоимость, количество товаров

                $productsIds = array_keys($productsInCart);
                $products = Product::getProdustsByIds($productsIds);
                $totalPrice = Cart::getTotalPrice($products);
                $totalQuantity = Cart::countItems();

                $userName = false;
                $userPhone = false;
                $userComment = false;

                //Проверка авторизации пользователя
                if (User::isGuest()) {
                    //значения для формы пустые
                } else {
                    //получаем информацию о пользоветеле из БД по id
                    $userId = User::checkLogged();
                    $user = User::getUserById($userId);

                    $userName = $user['name'];
                }

            }

        }



        require_once(ROOT . '/views/cart/checkout.php');


        return true;
    }
}
