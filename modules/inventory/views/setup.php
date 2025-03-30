<?php

/**
 * @filesource modules/inventory/views/setup.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Inventory\Setup;

use Kotchasan\DataTable;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=inventory-setup
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
        $filters = [];
        $params = [];
        $params['stock_condition'] = $request->request('stock_condition')->toInt();
        setcookie('stock_condition', $params['stock_condition'], time() + 2592000, '/', HOST, HTTPS, true);

        $this->category = \Inventory\Category\Model::init(false, true, false);
        foreach ($this->category->items() as $key => $label) {
            if ($key != 'unit') {
                $params[$key] = $request->request($key)->topic();
                $filters[] = [
                    'name' => $key,
                    'text' => $label,
                    'options' => [0 => '{LNG_all items}'] + $this->category->toSelect($key),
                    'value' => $params[$key]
                ];
            }
        }
        // URL สำหรับส่งให้ตาราง
        $uri = $request->createUriWithGlobals(WEB_URL . 'index.php');
        // ตาราง
        $table = new DataTable(array(
            'uri' => $uri,
            'model' => \Inventory\Setup\Model::toDataTable($params),  // ส่ง params ที่มีเงื่อนไขการค้นหาด้วย
            'perPage' => $request->cookie('inventorySetup_perPage', 30)->toInt(),
            'sort' => $request->cookie('inventorySetup_sort', 'id desc')->toString(),
            'onRow' => array($this, 'onRow'),
            'hideColumns' => array('unit'),
            'searchColumns' => ['topic', 'product_no', 'mj'],
            /* ตั้งค่าการกระทำของของตัวเลือกต่างๆ ด้านล่างตาราง ซึ่งจะใช้ร่วมกับการขีดถูกเลือกแถว */
            'action' => 'index.php/inventory/model/setup/action',
            'actionCallback' => 'dataTableActionCallback',
            'actions' => [
                [
                    'id' => 'action',
                    'class' => 'ok',
                    'text' => '{LNG_With selected}',
                    'options' => [
                        'delete' => '{LNG_Delete}'
                    ]
                ]
            ],
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
                // เพิ่มการกรอง stock_condition
                array(
                    'name' => 'stock_condition',
                    'text' => 'จำนวนคงเหลือ', // ค้นหาข้อมูลที่ stock ต่ำกว่า 40%
                    'options' => array(
                        0 => 'ทั้งหมด', // ค้นหาทุกสถานะ
                        1 => 'เหลือน้อยกว่า 40%'  // ค้นหาที่ stock น้อยกว่า 40%
                    ),
                    'value' => isset($params['stock_condition']) ? $params['stock_condition'] : 0
                )

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
                'mj' => array(
                    'text' => 'สาขาวิชา',
                    'class' => 'center',
                    'sort' => 'mj'
                ),
                'stock' => array(
                    'text' => '{LNG_Stock}',
                    'class' => 'center',
                    'sort' => 'stock'
                ),
                'size' => array(
                    'text' => 'ขนาดบรรจุ',
                    'class' => 'center',
                    'sort' => 'size'
                ),
                'inuse' => array(
                    'text' => 'ต้องการใช้',
                    'class' => 'center notext',
                    'sort' => 'inuse'
                )
            ),
            'cols' => array(
                'topic' => array(
                    'class' => 'topic'
                ),
                'product_no' => array(
                    'class' => 'nowrap'
                ),
                'mj' => array(
                    'class' => 'center nowrap'
                ),
                'stock' => array(
                    'class' => 'center nowrap'
                ),
                'size' => array(  // เพิ่มการแสดงผลของขนาดสินค้า
                    'class' => 'center nowrap'
                ),
                'inuse' => array(
                    'class' => 'center'
                )
            ),
            'buttons' => array(
                array(
                    'class' => 'icon-edit button green',
                    'href' => $uri->createBackUri(array('module' => 'inventory-write', 'tab' => 'product', 'id' => ':id')),
                    'text' => '{LNG_Edit}'
                )
            ),
            'addNew' => array(
                'class' => 'float_button icon-new',
                'href' => $uri->createBackUri(array('module' => 'inventory-write', 'id' => 0)),
                'title' => '{LNG_Add} {LNG_Equipment}'
            )
        ));

        // save cookie
        setcookie('inventorySetup_perPage', $table->perPage, time() + 2592000, '/', HOST, HTTPS, true);
        setcookie('inventorySetup_sort', $table->sort, time() + 2592000, '/', HOST, HTTPS, true);


        // คืนค่า HTML
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
        $item['product_no'] = '<img style="max-width:none" src="data:image/png;base64,' . base64_encode(\Kotchasan\Barcode::create($item['product_no'], 50, 9)->toPng()) . '">';
        $item['topic'] = '<span class=two_lines title="' . $item['topic'] . '">' . $item['topic'] . '</span>';

        foreach ($this->category->items() as $key => $label) {
            if (isset($item[$label])) {
                $item[$label] = $this->category->get($key, $item[$label]);
            }
        }

        $item['inuse'] = '<a id=inuse_' . $item['id'] . ' class="icon-valid ' . ($item['inuse'] == 0 ? 'disabled' : 'access') . '" title="' . $this->inventory_status[$item['inuse']] . '"></a>';
        $thumb = is_file(ROOT_PATH . DATA_FOLDER . 'inventory/' . $item['id'] . self::$cfg->stored_img_type) ? WEB_URL . DATA_FOLDER . 'inventory/' . $item['id'] . self::$cfg->stored_img_type : WEB_URL . 'skin/img/noicon.png';
        $item['id'] = '<img src="' . $thumb . '" style="max-height:50px;max-width:50px" alt=thumbnail>';

        $item['size'] = $item['size'] . ' ' . $item['unit'];
        // Debug output
        error_log('Stock: ' . $item['stock'] . ' Size: ' . $item['size']);

        // ตรวจสอบ stock < 0.4 * size และแสดงผลเป็นสีแดง
        if (isset($item['stock'], $item['size']) && $item['stock'] < 0.4 * $item['size']) {
            $item['stock'] = '<span style="color:red;">' . $item['stock'] . ' ' . $item['unit'] . '</span>';
        } else {
            $item['stock'] .= ' ' . $item['unit'];
        }



        return $item;
    }
}
