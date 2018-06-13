<?php


class BlogController
{

    public function actionIndex()
    {
        $blogList = array();
        $blogList = Blog::getBlogList();

        require_once(ROOT . '/views/blog/index.php');

        return true;
    }

    public function actionView($id)
    {
        if ($id) {
            $newsItem = Blog::getNewsItemById($id);
            require_once(ROOT . '/views/blog/view.php');
        }

        return true;
    }

}
