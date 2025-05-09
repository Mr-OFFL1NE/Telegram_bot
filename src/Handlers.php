<?php
class Handlers {
    private static function checkForceJoin($bot,$user_id){
        foreach(FORCE_CHANNELS as $ch){
            $m=$bot->getChatMember($ch,$user_id);
            if(!$m['ok']||in_array($m['result']['status'],['left','kicked'])){
                return $ch;
            }
        }
        return true;
    }
    public static function onMessage($bot,$msg){
        $chat_id=$msg['chat']['id'];
        $user_id=$msg['from']['id'];
        $text=$msg['text']??'';
        $is_group=in_array($msg['chat']['type'],['group','supergroup']);
        $pdo=Db::get();
        $stmt=$pdo->prepare("INSERT INTO users (telegram_id,username,first_name)
            VALUES (?,?,?) ON DUPLICATE KEY UPDATE username=?,first_name=?");
        $stmt->execute([
            $user_id,$msg['from']['username']??'',
            $msg['from']['first_name']??'',
            $msg['from']['username']??'',
            $msg['from']['first_name']??''
        ]);
        if(!$is_group){
            $cj=self::checkForceJoin($bot,$user_id);
            if($cj!==true){ $bot->sendMessage($chat_id,"لطفاً ابتدا در کانال {$cj} عضو شوید."); return;}
        }
        if($text === '/start'){
            $kb=['inline_keyboard'=>[
                [['text'=>"🎲 حقیقت",'callback_data'=>'truth']],
                [['text'=>"🔥 جرأت",'callback_data'=>'dare']],
                [['text'=>"➕ ارسال سوال",'callback_data'=>'add_q']],
                [['text'=>"🔄 صوتی/متنی",'callback_data'=>'toggle_mode']]
            ]];
            $bot->sendMessage($chat_id,"سلام! به ربات جرأت یا حقیقت خوش آمدی.",$kb);
        }
    }
    public static function onCallback($bot,$cb){
        $data=$cb['data'];
        $chat_id=$cb['message']['chat']['id'];
        $user_id=$cb['from']['id'];
        $cb_id=$cb['id'];

        $pdo=Db::get();
        $res=$pdo->prepare("SELECT key_value FROM settings WHERE key_name=?");
        $res->execute(["mode_$user_id"]);
        $row=$res->fetch();
        $mode=$row?$row['key_value']:'text';

        switch($data){
            case 'truth': case 'dare':
                $stmt=$pdo->prepare("SELECT * FROM questions WHERE type=? AND status='approved' ORDER BY RAND() LIMIT 1");
                $stmt->execute([$data]);
                $q=$stmt->fetch(PDO::FETCH_ASSOC);
                if(!$q){ $bot->answerCallbackQuery($cb_id,"فعلاً سوالی موجود نیست."); return;}
                if($mode==='voice' && $q['voice_file_id']){
                    $bot->sendVoice($chat_id,$q['voice_file_id'],$q['text']);
                } else {
                    $bot->sendMessage($chat_id,$q['text']);
                }
                break;
            case 'add_q':
                $bot->sendMessage($chat_id,"نوع سوال را انتخاب کنید:",[
                    'inline_keyboard'=>[
                        [['text'=>'🎯 حقیقت','callback_data'=>'start_add_truth']],
                        [['text'=>'💥 جرأت','callback_data'=>'start_add_dare']]
                    ]
                ]);
                break;
            case 'toggle_mode':
                $new = $mode==='text'?'voice':'text';
                $stmt=$pdo->prepare("REPLACE INTO settings (key_name,key_value) VALUES (?,?)");
                $stmt->execute(["mode_$user_id",$new]);
                $bot->answerCallbackQuery($cb_id,"حالت جدید: $new");
                break;
        }
    }
}
?>