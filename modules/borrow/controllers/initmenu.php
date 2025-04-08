<?php

/**
 * @filesource modules/borrow/controllers/initmenu.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Borrow\Initmenu;

use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * Init Menus
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\KBase
{
    /**
     * ฟังก์ชั่นเริ่มต้นการทำงานของโมดูลที่ติดตั้ง
     * และจัดการเมนูของโมดูล
     *
     * @param Request                $request
     * @param \Index\Menu\Controller $menu
     * @param array                  $login
     */
    public static function execute(Request $request, $menu, $login)
    {
        if ($login) {

            $submenus = [];
            $submenus[] = array(
                'text' => '{LNG_Add Borrow}',
                'url' => 'index.php?module=borrow'
            );
            foreach (Language::get('BORROW_STATUS') as $type => $text) {
                $submenus[] = array(
                    'text' => $text,
                    'url' => 'index.php?module=borrow-setup&amp;status='.$type
                );
            }


            $menu->addTopLvlMenu('borrow', '{LNG_Borrow}  ', null, $submenus, 'member');
            $menu->addTopLvlMenu('inventory', '{LNG_Inventory}', 'index.php?module=borrow-inventory', null, 'borrow');

            // ตรวจสอบว่าเป็น admin หรือไม่
            if (!Login::isAdmin()) {  // ถ้าไม่ใช่ admin
                // เพิ่มเมนู Borrow
                            // สามารถอนุมัติได้
            if (Login::checkPermission($login, 'can_approve_borrow')) {
                foreach (Language::get('BORROW_STATUS') as $type => $text) {
                    $menu->add('report', $text, 'index.php?module=borrow-report&amp;status=' . $type, null, 'borrow0' . $type);
                }
            }
            if (Login::checkPermission($login, 'can_config')) {
                $menu->add('settings', '{LNG_Settings} {LNG_Borrow} ', 'index.php?module=borrow-settings', null, 'borrow');
            }
            }




        }
    }
}
