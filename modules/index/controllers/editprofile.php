<?php
/**
 * @filesource modules/index/controllers/editprofile.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Editprofile;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=editprofile
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * แก้ไขข้อมูลส่วนตัวสมาชิก
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // ข้อความ title bar
    $this->title = Language::get('Editing your account');
    // เลือกเมนู
    $this->menu = 'member';
    // สมาชิก, ไม่ใช่สมาชิกตัวอย่าง
    if ($login = Login::notDemoMode(Login::isMember())) {
      // อ่านข้อมูลสมาชิก
      $user = \Index\Editprofile\Model::get($request->request('id', $login['id'])->toInt());
      if ($user && $user['id'] > 0 && ($login['id'] == $user['id'] || Login::isAdmin())) {
        // แสดงผล
        $section = Html::create('section', array(
            'class' => 'content_bg'
        ));
        // breadcrumbs
        $breadcrumbs = $section->add('div', array(
          'class' => 'breadcrumbs'
        ));
        $ul = $breadcrumbs->add('ul');
        $ul->appendChild('<li><span class="icon-user">{LNG_Users}</span></li>');
        $ul->appendChild('<li><a href="{BACKURL?module=member&id=0}">{LNG_Member list}</a></li>');
        $ul->appendChild('<li><span>{LNG_Edit}</span></li>');
        $section->add('header', array(
          'innerHTML' => '<h2 class="icon-profile">'.$this->title.'</h2>'
        ));
        if ($login['status'] == self::$cfg->student_status) {
          // แสดงฟอร์ม student
          $student = \School\User\Model::getForWrite($login['id']);
          if ($student) {
            $section->appendChild(createClass('School\Student\View')->render($request, $student, $login));
            return $section->render();
          }
        } else {
          // แสดงฟอร์ม editprifile
          $section->appendChild(createClass('Index\Editprofile\View')->render($user, $login));
          return $section->render();
        }
      }
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}