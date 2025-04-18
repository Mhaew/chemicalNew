<?php
/**
 * @filesource modules/borrow/views/order.php
 *
 * @copyright 2016 Goragod.com
 * @license https://www.kotchasan.com/license/
 *
 * @see https://www.kotchasan.com/
 */

namespace Borrow\Order;

use Kotchasan\Html;
use Kotchasan\Language;

/**
 * module=borrow-order
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * ฟอร์ม รายละเอียดการยืมพัสดุ
     *
     * @param object $index
     *
     * @return string
     */
    public function render($index)
    {
        $form = Html::create('form', array(
            'id' => 'order_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/borrow/model/order/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Details of} {LNG_Borrower}',
            'titleClass' => 'icon-profile'
        ));
        // borrower
        $groups = $fieldset->add('groups');
        $groups->add('text', array(
            'id' => 'borrower',
            'labelClass' => 'g-input icon-customer',
            'itemClass' => 'width20',
            'label' => '{LNG_Borrower}',
            'title' => '{LNG_Borrower}',
            'value' => $index->borrower,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups->add('number', array(
            'id' => 'id_card',
            'labelClass' => 'g-input icon-number',
            'itemClass' => 'width20',
            'label' => 'รหัสประจำตัว',
            'title' => 'รหัสประจำตัว',
            'value' => $index->id_card,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups->add('text', array(
            'id' => 'major',
            'labelClass' => 'g-input icon-profile',
            'itemClass' => 'width20',
            'label' => 'สาขาวิชา',
            'title' => 'สาขาวิชา',
            'value' => $index->major,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups->add('number', array(
            'id' => 'phone',
            'labelClass' => 'g-input icon-phone',
            'itemClass' => 'width20',
            'label' => 'เบอร์ติดต่อ',
            'title' => 'เบอร์ติดต่อ',
            'value' => $index->phone,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups = $fieldset->add('groups');
        $groups->add('text', array(
            'id' => 'address',
            'labelClass' => 'g-input icon-address',
            'itemClass' => 'width40',
            'label' => 'ที่อยู่',
            'title' => 'ที่อยู่',
            'value' => $index->address,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups->add('text', array(
            'id' => 'province',
            'labelClass' => 'g-input icon-location',
            'itemClass' => 'width20',
            'label' => 'จังหวัด',
            'title' => 'จังหวัด',
            'value' => $index->province,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups->add('text', array(
            'id' => 'country',
            'labelClass' => 'g-input icon-world',
            'itemClass' => 'width20',
            'label' => '{LNG_Country}',
            'title' => '{LNG_Country}',
            'datalist' => \Kotchasan\Country::all(),
            'value' => $index->country,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups->add('text', array(
            'id' => 'zipcode',
            'labelClass' => 'g-input icon-number',
            'itemClass' => 'width40',
            'label' => 'รหัสไปรษณีย์',
            'title' => 'รหัสไปรษณีย์',
            'value' => $index->zipcode,
            'autofocus' => true,
            'readonly' => true
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Details of} อาจารย์ที่ปรึกษาและผู้สอน',
            'titleClass' => 'icon-profile'
        ));
        $groups = $fieldset->add('groups');
        $groups->add('text', array(
            'id' => 'p_name',
            'labelClass' => 'g-input icon-customer',
            'itemClass' => 'width40',
            'label' => 'อาจารย์ที่ปรึกษา',
            'title' => 'อาจารย์ที่ปรึกษา',
            'value' => $index->p_name,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups->add('text', array(
            'id' => 'p_phone',
            'labelClass' => 'g-input icon-phone',
            'itemClass' => 'width40',
            'label' => 'เบอร์ติดต่อ',
            'title' => 'เบอร์ติดต่อ',
            'value' => $index->p_phone,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups = $fieldset->add('groups');
        $groups->add('text', array(
            'id' => 'techer',
            'labelClass' => 'g-input icon-customer',
            'itemClass' => 'width40',
            'label' => 'อาจารย์ผู้สอน',
            'title' => 'อาจารย์ผู้สอน',
            'value' => $index->techer,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups->add('text', array(
            'id' => 'techerMajors',
            'labelClass' => 'g-input icon-profile',
            'itemClass' => 'width40',
            'label' => 'สาขา',
            'title' => 'สาขา',
            'value' => $index->techerMajors,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups->add('text', array(
            'id' => 'useFor',
            'labelClass' => 'g-input icon-menus',
            'itemClass' => 'width40',
            'label' => 'จุดประสงค์ที่เบิก',
            'title' => 'จุดประสงค์ที่เบิก',
            'value' => $index->useFor,
            'autofocus' => true,
            'readonly' => true
        ));
        $groups = $fieldset->add('groups');
        $groups->add('date', array(
            'id' => 'use_date',
            'labelClass' => 'g-input icon-calendar',
            'itemClass' => 'width50',
            'label' => 'วันที่ต้องการใช้',
            'value' => $index->use_date,
            'readonly' => true
        ));
        
        // borrower_id
        $fieldset->add('hidden', array(
            'id' => 'borrower_id',
            'value' => $index->borrower_id
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Transaction details}',
            'titleClass' => 'icon-cart'
        ));
        $groups = $fieldset->add('groups');
        // borrow_no
        $groups->add('text', array(
            'id' => 'borrow_no',
            'labelClass' => 'g-input icon-number',
            'itemClass' => 'width50',
            'label' => '{LNG_Transaction No.}',
            'placeholder' => '{LNG_Leave empty for generate auto}',
            'value' => $index->borrow_no,
            'readonly' => true
        ));
        // transaction_date
        $groups->add('date', array(
            'id' => 'transaction_date',
            'labelClass' => 'g-input icon-calendar',
            'itemClass' => 'width50',
            'label' => '{LNG_Transaction date}',
            'value' => $index->transaction_date,
            'readonly' => true
        ));
        $groups = $fieldset->add('groups');
        // borrow_date
        $groups->add('date', array(
            'id' => 'borrow_date',
            'labelClass' => 'g-input icon-calendar',
            'itemClass' => 'width50',
            'label' => 'วันที่ส่งมอบ',
            'value' => $index->borrow_date
        ));

        // return_date

        $borrow_status = Language::get('BORROW_STATUS');
        $table = '<table class="fullwidth data border"><thead><tr>';
        $table .= '<th>{LNG_Detail}</th>';
        $table .= '<th>{LNG_Quantity}</th>';
        $table .= '<th>{LNG_Delivery}</th>';
        $table .= '<th>{LNG_Status}</th>';
        $table .= '<th>หมายเหตุ</th>';
        $table .= '<th colspan="3"></th>';
        $table .= '</tr></thead><tbody id=tb_products>';
        foreach (\Borrow\Order\Model::items($index->id) as $item) {
            $table .= '<tr>';
            $table .= '<td><a id="product_no_'.$item['product_no'].'">'.$item['topic'].' ('.$item['product_no'].')</a></td>';
            $table .= '<td class="center">'.$item['num_requests'].'</td>';
            $table .= '<td class="center" id="amount_'.$item['id'].'">'.$item['amount'].'</td>';
            $table .= '<td class="center"><span class="term'.$item['status'].'" id="status_'.$item['id'].'">'.$borrow_status[$item['status']].'</span></td>';
            $table .= '<td class="center" id="detail_'.$item['id'].'">'.$item['detail'].'</td>';
            if ($item['status'] != 1 && $item['status'] != 3 && $item['status'] != 4) {
                $table .= '<td class="center"><a id=delivery_'.$item['borrow_id'].'_'.$item['id'].' class="button icon-outbox green">{LNG_Delivery}</a></td>';
                $table .= '<td class="center"><a id=return_'.$item['borrow_id'].'_'.$item['id'].' class="button icon-inbox blue">{LNG_Return}</a></td>';
                $table .= '<td class="center"><a id=status_'.$item['borrow_id'].'_'.$item['id'].' class="button icon-star0 red">{LNG_Status update}</a></td>';
            }
            $table .= '</tr>';
        }
        $table .= '</tbody>';
        $table .= '</table>';
        $fieldset->add('div', array(
            'class' => 'item',
            'innerHTML' => $table
        ));
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit right'
        ));
        if (self::$cfg->noreply_email != '') {
            $fieldset->add('checkbox', array(
                'id' => 'send_mail',
                'labelClass' => 'inline-block middle',
                'label' => '&nbsp;{LNG_Email the relevant person}',
                'value' => 1
            ));
        }
        // submit
        $fieldset->add('submit', array(
            'class' => 'button ok large',
            'id' => 'order_submit',
            'value' => '{LNG_Save}'
        ));
        // borrow_id
        $fieldset->add('hidden', array(
            'id' => 'borrow_id',
            'value' => $index->id
        ));
        // Javascript
        $form->script('initBorrowOrder();');
        // คืนค่า HTML
        return $form->render();
    }
}
