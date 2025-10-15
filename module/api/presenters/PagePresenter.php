<?php

namespace app\module\api\presenters;

use app\module\admin\models\Page;
use app\module\api\controllers\BaseApiController;
use Yii;

class PagePresenter extends AbstractPresenter
{
    /**
     * If we get model as array (as a result of query)? we
     * need to have an object model fro ability to use relations. So we just will store such object in thi property
     * @var Page|null
     */
    protected $model_object;
    protected $for_mobile_app;

    /**
     * PagePresenter constructor.
     * @param $model
     * @param $for_mobile_app
     */
    public function __construct($model, $for_mobile_app)
    {
        parent::__construct($model);

        $this->model_class    = Page::class;
        $this->for_mobile_app = $for_mobile_app;

        if ($this->model_type === 'array') {
            $this->model_object = Page::findOne($this->page_id);
        }
    }


    /**
     * @return array
     */
    public function getResource(): array
    {
        return [
            'id'               => $this->page_id,
            'image'            => $this->getImage(),
            'top_banner'       => $this->getBanner(),
            'gallery'          => $this->getGallery(),
            'facebook'         => $this->facebook,
            'instagram'        => $this->instagram,
            'youtube'          => $this->youtube,
            'vk'               => $this->vk,
            'title'            => $this->title,
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keyword'     => $this->meta_keyword,
            'description1'     => $this->for_mobile_app ? html_entity_decode(strip_tags($this->description1)) : $this->description1,
            'description2'     => $this->description2,
            'footer_columns'   => $this->getFooter()
        ];
    }


    /**
     * @return mixed
     */
    public function getBanner()
    {
        if (isset($this->model_object)) {
            return $this->model_object->banner;
        } else {
            return $this->model->banner;
        }
    }


    /**
     * @return mixed
     */
    public function getGallery()
    {
        if (isset($this->model_object)) {
            return $this->model_object->gallery;
        } else {
            return $this->model->gallery;
        }
    }


    /**
     * @return string
     */
    public function getImage()
    {
        return isset($this->image) && $this->imageExists()
            ? BaseApiController::BASE_SITE_URL . 'image/page/' . $this->image
            : BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
    }


    /**
     * @return bool
     */
    protected function imageExists()
    {
        return file_exists(\Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR . 'page' . DIRECTORY_SEPARATOR . $this->image);
    }


    /**
     * @return mixed
     */
    protected function getFooter()
    {
        $columns = json_decode($this->footer_columns, true);
        $multilingual_columns = ['address', 'address_links'];

        foreach ($columns as &$column) {
            foreach ($column as $key => &$value) {
                if(in_array($key, $multilingual_columns, true)){
                    $new_value = $column[$key][$this->lang];
                    $column[$key] = $new_value;
                }
            }
        }

        return $columns;
    }
}