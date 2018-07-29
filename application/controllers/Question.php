<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $data = array(
            "info" => 0,
            "data" => array(),
            "msg" => "this is question index!"
        );
        $this->echoJsonAndExit($data);
    }


    /**
     * 开局
     */
    public function startRound()
    {
        //0 预定义返回参数
        $result = array(
            'code' => '0',
            'msg' => '',
            'data' => ''
        );

        //1 获取参数
        $token = $this->params['token'];//token
        $roundId = $this->params['roundId']; //本局的id
        $subjectId = $this->params['subjectId']; //获取科目id
        $useId = $this->params['userId']; //获取当前用户id

        //2 业务逻辑处理
        $model = $this->load->model('Question_model', '', true);
        //从没答对和没出现过的题目中抽题
        $arrQuestion = $this->Question_model->getUnCorrectQuestion($subjectId, $useId);
        //var_dump('getUnCorrectQuestion', $arrQuestion);

        //3 组织返回数据
        $result['data'] = $arrQuestion;
        $this->echoJsonAndExit($result);
    }

    /**
     * 结束一局
     */
    public function endRound()
    {
        //0 预定义返回参数
        $result = array(
            'code' => '0',
            'msg' => '',
            'data' => ''
        );

        //1 获取参数
        $token = $this->params['token'];//token
        $roundId = $this->params['roundId']; //本局的id
        $subjectId = $this->params['subjectId']; //获取科目id
        $userId = $this->params['userId']; //获取当前用户id
        //2 业务逻辑处理
        //2.1汇总该局的相关信息
        $model = $this->load->model('UserQuestion_model', '', true);
        $roundRecord = $this->UserQuestion_model->getRoundScore($roundId);
        $roundScore =  $roundRecord['roundScore'];
        //2.2若该局打破单局记录，则更新最新的单局最高分
        $model = $this->load->model('User_model', '', true);
        $userInfo = $this->User_model->getBasicUserInfo($userId);

        if (empty($userInfo)){
            $this->User_model->addUserScore($userId, $roundScore, $allScore = 0);
        }elseif($roundScore > $userInfo['round_score']){
            $this->User_model->updateUserRoundScore($userId, $roundScore, $allScore = 0);
        }


        //3 组织返回数据
        $result['data'] = array(
            'roundId' => $roundId,
            'score' => $roundScore
        );
        $result['msg'] = '本局结束';
        $this->echoJsonAndExit($result);
    }

    /**
     * 保存答题结果并返回得分、奖励、下一题
     */
    public function saveAnswer()
    {
        //0 预定义返回参数
        $result = array(
            'code' => '0',
            'msg' => '',
            'data' => ''
        );

        //1 获取参数
        $token = $this->params['token'];//token
        $roundId = $this->params['roundId']; //本局的id
        $subjectId = $this->params['subjectId']; //获取科目id
        $quesNum = $this->params['quesNum']; //本轮的第几题，如果是刚开始则是1
        $quesId = $this->params['quesId']; //题目id
        $isRight = $this->params['isRight']; //是否答对
        $useId = $this->params['userId']; //获取当前用户id

        //2 业务逻辑处理

        /**判断是否答对，计算增加的得分和奖励**/
        //2.1计算连击次数，这个地方使用缓存比较好
        //获取本轮已答过的题的

        $linkHitTime = $this->getLinkHitTime($roundId);
        $linkHitTime = $linkHitTime + $isRight ? 1 : 0;
        //2.2得分，得分跟连击次数有关系
        $score = $this->calculateScore($linkHitTime);
        //2.3奖励，奖励跟连击次数有关系
        $reward = $this->calculateReward($roundId, $linkHitTime);

        //2.4 保存结果（答题记录、得分、奖励）
        $model = $this->load->model('UserQuestion_model', '', true);
        $saveResult = $this->UserQuestion_model->saveUserQuestion($roundId, $useId, $subjectId, $quesId, $isRight, $score);
        if (false == $saveResult){
            $result = array(
                'code' => '-1',
                'msg' => '保存答题结果失败！',
                'data' => ''
            );
            $this->echoJsonAndExit($result);
        }

        //2.3 获取新的题目
        $this->load->model('Question_model', '', true);
        $arrQuestion = $this->Question_model->getUnviewedQuestion($subjectId, $useId);

        //3 组织返回数据
        //3.1 新题目
        $result['data']['question'] = $arrQuestion;
        //3.2 得分
        $result['data']['score'] = $score;
        //3.3 奖励
        $result['data']['reward'] = $reward;
        $this->echoJsonAndExit($result);
    }

    /**
     * 获取连击次数
     * @param $roundId
     */
    public function getLinkHitTime($roundId)
    {
        $this->load->model('UserQuestion_model', '', true);
        $arrRecord = $this->UserQuestion_model->getRoundRecord($roundId);
        $linkHitTime = 0;
        foreach ($arrRecord as $record){
            if ($record['is_right'] == 1){
                $linkHitTime++;
            }else{
                //遇到一次不正确的就跳出循环
                break;
            }
        }
        return $linkHitTime;
    }

    /**
     * 计算得分
     * @param $linkHitTime
     * @return mixed
     */
    public function calculateScore($linkHitTime)
    {

        return $linkHitTime;
    }

    /**
     * 计算奖励
     * @param $roundId
     * @param $linkHitTime
     */
    public function calculateReward($roundId, $linkHitTime)
    {
        //1 预定义返回值
        $reward = array(
            'id' => 1,
            'type' => '1',
            'name' => '加时30秒',
            'img' => '',
            'showInNextQuestion' => 0, //是否在下一个题目展示，一般是随机奖才会在下一个题目展示
        );
        //2  逻辑处理
        //2.1 若3连击，则奖励30加时
        if ($linkHitTime == 3){
            $reward = array(
                'id' => 1,
                'type' => '1',
                'name' => '加时30秒',
                'img' => '',
                'showInNextQuestion' => 0, //是否在下一个题目展示，一般是随机奖才会在下一个题目展示
            );
        }

        return $reward;
    }

    /**
     * 根据科目ID获取题目
     * @param $subjectId
     */
    public function getQuestion()
    {
        //0 预定义返回参数
        $result = array(
            'code' => '0',
            'msg' => '',
            'data' => ''
        );

        //1 获取参数
        $roundId = $this->input->get('roundId');//本局的id
        $subjectId = $this->input->get('subjectId');//获取科目id
        $quesNum = $this->input->get('quesNum');//本轮的第几题，如果是刚开始则是1
        $useId = 1;//获取当前用户id

        //2 业务逻辑处理
        $model = $this->load->model('Question_model', '', true);
        $arrQuestion = $this->Question_model->getRandQuestion($subjectId, $useId);
        //var_dump('getRandQuestion', $arrQuestion);

        $arrQuestion = $this->Question_model->getUnCorrectQuestion($subjectId, $useId);
        //var_dump('getUnCorrectQuestion', $arrQuestion);

        $arrQuestion = $this->Question_model->getUnviewedQuestion($subjectId, $useId);
        //var_dump('getUnviewedQuestion', $arrQuestion);

        //3 组织返回数据
        $result['data'] = $arrQuestion;
        echo json_encode($result);
        exit();
    }


}
