<?php

/**
 * @filesource modules/inventory/views/write.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Inventory\Write;

use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=inventory-write&tab=product
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * ฟอร์มเพิ่ม/แก้ไข Inventory
     *
     * @param Request $request
     * @param object $product
     *
     * @return string
     */
    public function render(Request $request, $product)
    {
        $form = Html::create('form', array(
            'id' => 'product',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/inventory/model/write/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true
        ));
        $fieldset = $form->add('fieldset', array(
            'titleClass' => 'icon-write',
            'title' => '{LNG_Details of} {LNG_Equipment}'
        ));
        $groups = $fieldset->add('groups');
        if ($product->id == 0) {
            // product_no
            $groups->add('text', array(
                'id' => 'product_no',
                'labelClass' => 'g-input icon-barcode',
                'itemClass' => 'width20',
                'label' => '{LNG_Serial/Registration No.}',
                'placeholder' => 'ตัวอย่าง abc-123 *ต้องมี -',
                'value' => isset($product->product_no) ? $product->product_no : ''
            ));
        }
        // topic
        $groups->add('text', array(
            'id' => 'topic',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'width30',
            'label' => 'ชื่อสารเคมี (Chemical name)',
            'placeholder' => 'ชื่อของ {LNG_Equipment}',
            'value' => isset($product->topic) ? $product->topic : ''
        ));
        $groups->add('text', array(
            'id' => 'cheme_no',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'width20',
            'label' => 'CAS no.',
            'placeholder' => '',
            'value' => isset($product->cheme_no) ? $product->cheme_no : ''
        ));
        foreach (Language::get('INVENTORY_METAS', []) as $key => $label) {
            if ($key == 'detail') {
                $fieldset->add('textarea', array(
                    'id' => $key,
                    'labelClass' => 'g-input icon-file',
                    'itemClass' => 'item',
                    'label' => $label,
                    'placeholder' => 'ประเภทความเป็นอันตราย',
                    'rows' => 3,
                    'value' => isset($product->{$key}) ? $product->{$key} : ''
                ));
            } else {
                $fieldset->add('text', array(
                    'id' => $key,
                    'labelClass' => 'g-input icon-edit',
                    'itemClass' => 'item',
                    'label' => $label,
                    'value' => isset($product->{$key}) ? $product->{$key} : ''
                ));
            }
        }
        // category
        $category = \Inventory\Category\Model::init(false);
        $n = 0;
        foreach ($category->items() as $key => $label) {
            if ($key !== 'unit') {
                if ($n % 2 == 0) {
                    $groups = $fieldset->add('groups');
                }
                $n++;
                $groups->add('text', array(
                    'id' => $key,
                    'labelClass' => 'g-input icon-menus',
                    'itemClass' => 'width30',
                    'label' => $label,
                    'datalist' => $category->toSelect($key),
                    'value' => isset($product->{$key}) ? $product->{$key} : '',
                    'text' => ''
                ));
            }
        }
        $groups->add('text', array(
            'id' => 'seller',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'width30',
            'label' => 'ผู้ขาย',
            'placeholder' => '',
            'value' => isset($product->seller) ? $product->seller : ''
        ));
        // เพิ่มแถวใหม่
        $groups = $fieldset->add('groups');
        $groups->add('text', array(
            'id' => 'sds',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'width15',
            'label' => 'Color',
            'placeholder' => '',
            'value' => isset($product->sds) ? $product->sds : ''
        ));
        $groups->add('text', array(
            'id' => 'un_class',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'width15',
            'label' => 'UN Class',
            'placeholder' => '',
            'value' => isset($product->un_class) ? $product->un_class : ''
        ));
        $groups->add('text', array(
            'id' => 'grade',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'width10',
            'label' => 'เกรด',
            'placeholder' => '',
            'value' => isset($product->grade) ? $product->grade : ''
        ));
        $groups->add('select', array(
            'id' => 'mj',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'width20',
            'label' => 'สาขาวิชา',
            'options' => Language::get('MAJORS'),
            'placeholder' => '',
            'value' => isset($product->mj) ? $product->mj : ''
        ));
        if ($product->id == 0) {
            $groups = $fieldset->add('groups');
            $groups->add('number', array(
                'id' => 'size',
                'labelClass' => 'g-input icon-number',
                'itemClass' => 'width20',
                'label' => '{LNG_size}',
                'value' => isset($product->size) ? $product->size : 0
            ));
            // stock
            $groups->add('number', array(
                'id' => 'stock',
                'labelClass' => 'g-input icon-number',
                'itemClass' => 'width20',
                'label' => '{LNG_Stock}',
                'value' => isset($product->stock) ? $product->stock : 0
            ));
            // unit
            $groups->add('text', array(
                'id' => 'unit',
                'labelClass' => 'g-input icon-edit',
                'itemClass' => 'width20',
                'label' => '{LNG_Unit}',
                'datalist' => $category->toSelect('unit'),
                'value' => isset($product->unit) ? $product->unit : '',
                'text' => ''
            ));
        }
        $groups->add('date', array(
            'id' => 'exp',
            'labelClass' => 'g-input icon-edit',
            'itemClass' => 'width20',
            'label' => 'วันที่รับเข้า Lab',
            'placeholder' => '',
            'value' => isset($product->exp) ? $product->exp : ''
        ));

        // picture
        if (is_file(ROOT_PATH.DATA_FOLDER.'inventory/'.$product->id.self::$cfg->stored_img_type)) {
            $img = WEB_URL.DATA_FOLDER.'inventory/'.$product->id.self::$cfg->stored_img_type.'?'.time();
        } else {
            $img = WEB_URL.'skin/img/noicon.png';
        }
        $fieldset->add('file', [
            'id' => 'picture',
            'labelClass' => 'g-input icon-upload',
            'itemClass' => 'item',
            'label' => '{LNG_Image}',
            'comment' => '{LNG_Browse image uploaded, type :type} ({LNG_resized automatically})',
            'dataPreview' => 'imgPicture',
            'previewSrc' => $img,
            'accept' => self::$cfg->inventory_img_typies
        ]);
        // inuse
        $fieldset->add('select', [
            'id' => 'inuse',
            'labelClass' => 'g-input icon-valid',
            'itemClass' => 'item',
            'label' => '{LNG_Status}',
            'options' => Language::get('INVENTORY_STATUS'),
            'value' => $product->inuse
        ]);
        $fieldset = $form->add('fieldset', [
            'class' => 'submit'
        ]);
        // submit
        $fieldset->add('submit', [
            'class' => 'button save large icon-save',
            'value' => '{LNG_Save}'
        ]);
        // id
        $fieldset->add('hidden', [
            'id' => 'id',
            'value' => $product->id
        ]);
        \Gcms\Controller::$view->setContentsAfter([
            '/:type/' => implode(', ', self::$cfg->inventory_img_typies)
        ]);
        if ($product->id == 0) {
            // Javascript
            $form->script('barcodeEnabled(["product_no"]);');
        }
        // คืนค่า HTML
        return $form->render();
    }
}