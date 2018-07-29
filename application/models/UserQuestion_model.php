<?php
/**
 * Created by PhpStorm.
 * User: liuzhaopeng
 * Date: 2018-06-13
 * Time: 17:41
 */

class UserQuestion_model extends CI_Model
{

    /**
     * @param $subjectId
     */
    public function _getQuestion($subjectId,$quesNum,$useId)
    {

        $where = array(
            'subject_id' => $subjectId
        );

        $this->db->select('subject_name, ques_title');
        $this->db->where($where);
        $this->db->from('question');
        $this->db->limit(1,0);
        $query = $this->db->get();

        $query = $this->db->query("select * from question");


        foreach ($query->result() as $row)
        {
            var_dump($row);
        }

    }

    /**
     * 随机获取一个题目
     */
    public function saveUserQuestion($roundId, $useId, $subjectId, $ques_id, $is_right = 0, $score = 1)
    {
        $data = array(
            'round_id' => $roundId,
            'user_id' => $useId,
            'ques_id' => $ques_id,
            'is_right' => $is_right,
            'score' => $score,
            'add_time' => time(),
        );
        $result = $this->db->insert('user_question_record', $data);
        return $result;
    }

    /**
     * 根据round_id获取本局所有答题记录
     */
    public function getRoundRecord($roundId)
    {
        $sql = "SELECT * FROM user_question_record WHERE round_id = {$roundId} ORDER BY record_id DESC ";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }
    /**
     * 根据round_id获取本局得分
     */
    public function getRoundScore($roundId)
    {
        $sql = "SELECT SUM(score) as roundScore FROM user_question_record WHERE round_id = {$roundId} AND is_right = 1";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        return $result;
    }

}