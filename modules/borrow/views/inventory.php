<?php

/**
 * @filesource modules/borrow/views/inventory.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Borrow\Inventory;

use Kotchasan\DataTable;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=borrow-inventory
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * @var object
     */
    private $category;
    /**
     * @var array
     */
    private $inventory_status;

    /**
     * ตาราง Inventory
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        $this->inventory_status = Language::get('INVENTORY_STATUS');
        $params = array(
            'category_id' => $request->request('category_id')->toInt(),
            'type_id' => $request->request('type_id')->toInt(),
            'model_id' => $request->request('model_id')->toInt(),
            'stock_condition' => $request->request('stock_condition')->toInt() // เพิ่มเงื่อนไขการค้นหาของ stock
        );
        $this->category = \Inventory\Category\Model::init(false);

        $uri = $request->createUriWithGlobals(WEB_URL . 'index.php');

        // ตาราง
        $table = new DataTable(array(
            'uri' => $uri,
            'model' => \Borrow\Inventory\Model::toDataTable($params),
            'perPage' => $request->cookie('borrow_inventory_perPage', 30)->toInt(),
            'sort' => $request->cookie('borrow_inventory_sort', 'id desc')->toString(),
            'onRow' => array($this, 'onRow'),
            'hideColumns' => array('unit'),
            'searchColumns' => array('product_no', 'topic', 'mj'),
            'action' => 'index.php/borrow/model/inventory/action',
            'actionCallback' => 'dataTableActionCallback',
            'filters' => array(
                array(
                    'name' => 'category_id',
                    'text' => '{LNG_Category}',
                    'options' => array(0 => '{LNG_all items}') + $this->category->toSelect('category_id'),
                    'value' => $params['category_id']
                ),
                array(
                    'name' => 'type_id',
                    'text' => '{LNG_Type}',
                    'options' => array(0 => '{LNG_all items}') + $this->category->toSelect('type_id'),
                    'value' => $params['type_id']
                ),
                array(
                    'name' => 'model_id',
                    'text' => '{LNG_Model}',
                    'options' => array(0 => '{LNG_all items}') + $this->category->toSelect('model_id'),
                    'value' => $params['model_id']
                ),
                // เพิ่มเงื่อนไขสำหรับ stock_condition
            ),

            'headers' => array(
                'id' => array(
                    'text' => '{LNG_Image}',
                    'sort' => 'id'
                ),
                'topic' => array(
                    'text' => '{LNG_Equipment}',
                    'sort' => 'topic'
                ),
                'product_no' => array(
                    'text' => '{LNG_Serial/Registration No.}',
                    'sort' => 'product_no'
                ),
                'category_id' => array(
                    'text' => 'สถานะ',
                    'class' => 'center',
                    'sort' => 'category_id'
                ),
                'type_id' => array(
                    'text' => '{LNG_Type}',
                    'class' => 'center',
                    'sort' => 'type_id'
                ),
                'mj' => array(
                    'text' => 'สาขาวิชา',
                    'class' => 'center',
                    'sort' => 'mj'
                ),
                'model_id' => array(
                    'text' => '{LNG_Model}',
                    'class' => 'center',
                    'sort' => 'model_id'
                ),
                'stock' => array(
                    'text' => '{LNG_Stock}',
                    'class' => 'center',
                    'sort' => 'stock'
                )
            ),
            'cols' => array(
                'topic' => array(
                    'class' => 'topic'
                ),
                'product_no' => array(
                    'class' => 'nowrap'
                ),
                'category_id' => array(
                    'class' => 'center nowrap'
                ),
                'type_id' => array(
                    'class' => 'center nowrap'
                ),
                'mj' => array(
                    'class' => 'center nowrap'
                ),
                'model_id' => array(
                    'class' => 'center nowrap'
                ),
                'stock' => array(
                    'class' => 'center'
                )
            ),
            'buttons' => array(
                'detail' => array(
                    'class' => 'icon-info button orange',
                    'id' => ':product_no',
                    'text' => '{LNG_Detail}'
                )
            )
        ));

        setcookie('borrow_inventory_perPage', $table->perPage, time() + 2592000, '/', HOST, HTTPS, true);
        setcookie('borrow_inventory_sort', $table->sort, time() + 2592000, '/', HOST, HTTPS, true);

        return $table->render();
    }


    /**
     * จัดรูปแบบการแสดงผลในแต่ละแถว
     *
     * @param array  $item ข้อมูลแถว
     * @param int    $o    ID ของข้อมูล
     * @param object $prop กำหนด properties ของ TR
     *
     * @return array คืนค่า $item กลับไป
     */
    public function onRow($item, $o, $prop)
    {
        // สร้าง Barcode สำหรับสินค้า
        $item['product_no'] = '<img style="max-width:none" src="data:image/png;base64,' . base64_encode(\Kotchasan\Barcode::create($item['product_no'], 40, 9)->toPng()) . '">';
        // แสดงหัวข้อสินค้า
        $item['topic'] = '<span class=two_lines>' . $item['topic'] . '</span>';
        // แสดงหมวดหมู่, ประเภท, และรุ่น
        $item['category_id'] = $this->category->get('category_id', $item['category_id']);
        $item['type_id'] = $this->category->get('type_id', $item['type_id']);
        $item['model_id'] = $this->category->get('model_id', $item['model_id']);
        $item['mj'];
        // เพิ่มหน่วยให้กับ stock
        $item['stock'] .= ' ' . $item['unit'];
        // สร้าง path สำหรับภาพ thumbnail
        $thumb = is_file(ROOT_PATH.DATA_FOLDER.'inventory/'.$item['id'].self::$cfg->stored_img_type) ? WEB_URL.DATA_FOLDER.'inventory/'.$item['id'].self::$cfg->stored_img_type : WEB_URL.'skin/img/noicon.png';
        // สร้างแสดงภาพ thumbnail
        $item['id'] = '<img src="'.$thumb.'" style="max-height:50px;max-width:50px" alt=thumbnail>';
        return $item;
    }
}
