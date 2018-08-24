<?php
use FormulaParser\FormulaParser;
require('../vendor/autoload.php');
$anekdots = array(
  'Колобок повесился😄😄',
  '- Мама, одевай меня быстрее! - Вовочка, куда же ты так торопишься? - Меня в садике ждут друзья! - И что же вы там делаете? - Дерёмся!😄😄',
  'Учительница:<br>
  - Прекрасно, Вовочка, домашнее задание выполнено без ошибок. А ты уверен, что твоему папе никто не помогал!?😄😄',
  "Учительница спрашивает Вовочку:
  – Вовочка, почему птицы летят на юг?
  – Потому что им трудно идти туда пешком.😄😄",
  "Учительница:
  – Вот муравей трудится целый день. Дети, а что происходит потом?
  Вовочка:
  – А потом какая-нибудь зараза возьмёт и раздавит!😄😄",
  "
  Встречаются два ежика. У одного забинтована лапка.
  – Что с тобой?
  – Ничего. Просто хотел почесаться.😄😄",
  "Бежит ежик по лесу, а навстречу ему заяц:
  – Ежик, а ежик, почему у тебя иголки такие жесткие?
  – Почему, почему! Не мылся я давно!😄😄",
  "Отец говорит сыну:
  – Придется мне тебя выпороть, хотя, можешь поверить, мне это неприятно.
  – В таком случае, кому ты хочешь доставить удовольствие?😄😄"
  );
$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Our web handlers

$app->get('/', function() use($app) {
  return "Hello world";
});
$app->post('/bot', function() use($app) {
  $data = json_decode(file_get_contents('php://input'));

  if(!$data)
    return "nioh";
  
  if( $data->secret !== getenv('VK_SECRET_TOKEN') && $data->type !== 'confirmation')
    return 'nioh';

  switch($data->type){
    case 'confirmation':
      return getenv('VK_CONFIRMATION_CODE');
      break;
      

    case 'message_new':
      $split = explode(" ", $data->object->text, 2);
      if ( $split[0] == '!Ку' || $split[0] == '!ку' || $split[0] == '!привет' || $split[0] == '!Привет'){
        $request_params = array(
          'user_id' => "{$data->object->from_id}",
          'message'=>'🎉Приветик🎉',
          'access_token' => '18d28ce6782d1c964c4bac21f4fd054378c65e739089d1bcae856947b32657436f5c2d06faa5179289e08',
          'v' => '5.80'
        );
      } elseif ( $split[0] == "!скажи" ){
        $request_params = array(
          'user_id' => "{$data->object->from_id}",
          'message'=> $split[1],
          'access_token' => '18d28ce6782d1c964c4bac21f4fd054378c65e739089d1bcae856947b32657436f5c2d06faa5179289e08',
          'v' => '5.80'
        );
      } elseif ( $split[0] == '!реши' ){
            $request_params = array(
              'user_id' => "{$data->object->from_id}",
              'access_token' => '18d28ce6782d1c964c4bac21f4fd054378c65e739089d1bcae856947b32657436f5c2d06faa5179289e08',
              'v' => '5.80'
            );
            $formula = $split[1];
            $precision = 2; // Number of digits after the decimal point

            try {
              $parser = new FormulaParser($formula, $precision);
              $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]
              $request_params['message'] = "🍀Ответ: " . $result[1];
            } catch (\Exception $e) {
              $request_params['message'] = "😱Тут я бессилен😱";
            }

      }
      else {
        $request_params = array(
          'user_id' => "{$data->object->from_id}",
          'message'=>'Добро пожаловать! <br> Вот мои команды: <br> ;-P !привет - бот скажет тебе привет😜<br>👏!скажи <фраза/текст> - бот повторит твою фразу👏<br>😎!реши <пример> - бот вычислит пример за тебя😎',
          'access_token' => '18d28ce6782d1c964c4bac21f4fd054378c65e739089d1bcae856947b32657436f5c2d06faa5179289e08',
          'v' => '5.80'
        );
      }

      file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
      return 'ok';

      break;
  }
  return "nioh";
});

$app->run();
/*elseif ( $data->object->text == '!анекдот' || $data->object->text == '!Анекдот' ){
        $rand = $anekdots[rand(0, count($anekdots)-1)];
        $request_params = array(
          'user_id' => "{$data->object->from_id}",
          'message'=> "$anekdots[0]",
          'access_token' => '18d28ce6782d1c964c4bac21f4fd054378c65e739089d1bcae856947b32657436f5c2d06faa5179289e08',
          'v' => '5.80'
        );
      } */