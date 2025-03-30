<?php

/**
 * @filesource modules/borrow/models/home.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Borrow\Home;

use Gcms\Login;
use Kotchasan\Database\Sql;

/**
 * โมเดลสำหรับอ่านข้อมูลแสดงในหน้า  Home
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{
    /**
     * อ่านรายการจองวันนี้.
     *
     * @return object
     */
    public static function get($login)
    {
        // รอตรวจสอบ
        $q0 = static::createQuery()
            ->select(Sql::COUNT())
            ->from('borrow W')
            ->join('borrow_items S', 'INNER', array('S.borrow_id', 'W.id'))
            ->where(array(
                array('W.borrower_id', $login['id']),
                array('S.status', 0),
                
            ));


        // ไม่อนุมัติ
        // ครบกำหนดคืน
        $q1 = static::createQuery()
            ->select(Sql::COUNT())
            ->from('borrow W')
            ->join('borrow_items S', 'INNER', array('S.borrow_id', 'W.id'))
            ->where(array(
                array('W.borrower_id', $login['id']),
                array('S.status', 1)

            ));
        // อนุมัติ/ใช้งานอยู่
        $q2 = static::createQuery()
            ->select(Sql::COUNT())
            ->from('borrow W')
            ->join('borrow_items S', 'INNER', array('S.borrow_id', 'W.id'))
            ->where(array(
                array('W.borrower_id', $login['id']),
                array('S.status', 2)
            ))
            ->andWhere(array(
                array(Sql::DATEDIFF('W.return_date', date('Y-m-d')), '>', 0),
                Sql::ISNULL('W.return_date')
            ), 'OR');
        $q4 = static::createQuery()
            ->select(Sql::COUNT())
            ->from('borrow W')
            ->join('borrow_items S', 'INNER', ['S.borrow_id', 'W.id'])
            ->where([
                ['W.borrower_id', $login['id']],
                ['S.status', 4]
            ]);
        if (Login::checkPermission($login, 'can_approve_borrow')) {
            // รายการรอตรวจสอบทั้งหมด
            $q3 = static::createQuery()
                ->select(Sql::COUNT())
                ->from('borrow W')
                ->join('borrow_items S', 'INNER', array('S.borrow_id', 'W.id'))
                ->where(array('S.status', 0));
            $q5 = static::createQuery()
                ->select(Sql::COUNT())
                ->from('borrow W')
                ->join('borrow_items S', 'INNER', array('S.borrow_id', 'W.id'))
                ->where(array('S.status', 2));
            $q6 = static::createQuery()
                ->select(Sql::COUNT())
                ->from('borrow W')
                ->join('borrow_items S', 'INNER', array('S.borrow_id', 'W.id'))
                ->where(array('S.status', 1));
            $q7 = static::createQuery()
                ->select(Sql::COUNT())
                ->from('borrow W')
                ->join('borrow_items S', 'INNER', array('S.borrow_id', 'W.id'))
                ->where(array('S.status', 4));
            $q8 = static::createQuery()
                ->select(Sql::COUNT())
                ->from('user U')
                ->where(array('U.active', 0));

            return static::createQuery()->cacheOn()->first(array($q0, 'pending'), array($q1, 'returned'), array($q2, 'confirmed'), array($q3, 'allpending'), array($q5, 'allconfirmed'), array($q6, 'allreturned'), array($q7, 'alldelivered'), array($q8, 'unactive'));
        } else {
            return static::createQuery()->cacheOn()->first(array($q0, 'pending'), array($q1, 'returned'), array($q2, 'confirmed'), array($q4, 'delivered'));
        }
    }
}
