<?php
class Bot {
    private $api="https://api.telegram.org/bot".BOT_TOKEN."/";
    public function sendMessage($chat_id,$text,$reply_markup=null){
        $data=['chat_id'=>$chat_id,'text'=>$text,'parse_mode'=>'HTML'];
        if($reply_markup)$data['reply_markup']=json_encode($reply_markup);
        file_get_contents($this->api."sendMessage?".http_build_query($data));
    }
    public function sendVoice($chat_id,$voice,$caption='',$reply_markup=null){
        $data=['chat_id'=>$chat_id,'voice'=>$voice,'caption'=>$caption,'parse_mode'=>'HTML'];
        if($reply_markup)$data['reply_markup']=json_encode($reply_markup);
        file_get_contents($this->api."sendVoice?".http_build_query($data));
    }
    public function getChatMember($chat_id,$user_id){
        $res=file_get_contents($this->api."getChatMember?chat_id={$chat_id}&user_id={$user_id}");
        return json_decode($res,true);
    }
    public function answerCallbackQuery($id,$text=''){
        file_get_contents($this->api."answerCallbackQuery?".http_build_query([
            'callback_query_id'=>$id,'text'=>$text,'show_alert'=>false
        ]));
    }
}
?>