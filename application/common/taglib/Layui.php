<?php
/**
 * Created by PhpStorm.
 * User: xiaozhu
 * Date: 2019/7/9
 * Time: 12:15
 */

namespace app\common\taglib;

use think\template\TagLib;

class Layui extends TagLib
{
    protected $tags = [
        'button'    => ['attr'=>'event,text,class', 'close'=>0],
        'close'     => ['attr'=>'time,format', 'close'=>0],
        'open'      => ['attr'=>'name,type', 'close'=>1]
    ];

    public function tagClose($tag)
    {
        $format = empty($tag['format']) ? 'Y-m-d H:i:s' : $tag['format'];
        $time = empty($tag['time']) ? time() : $tag['time'];
        $parse = '<?php ';
        $parse .= 'echo date("'.$format.'",'.$time.');';
        $parse .= ' ?>';

        return $parse;
    }

    public function tagButton($tag)
    {
        $event = empty($tag['event']) ? 'event' : $tag['event'];
        $text = empty($tag['text']) ? ' 未知事件' : $tag['text'];
        $class = empty($tag['class']) ? '' : $tag['class'];
        $parse = "<?php ";
        $parse .= "echo '<button class=\"layui-btn layui-btn-sm {$class}\" lay-event=\"{$event}\">{$text}</button>'";
        $parse .= " ?>";

        return $parse;
    }
}