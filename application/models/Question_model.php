<?php
/**
 * Created by PhpStorm.
 * User: liuzhaopeng
 * Date: 2018-06-13
 * Time: 17:41
 */

class Question_model extends CI_Model
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
     * 结构化每个题目的选项和答案的关系，方便前端展示
     */
    public function structSelection($arrQues)
    {
        $result = array();
        foreach ($arrQues as $row){
            $answer = $row['answer'];
            $selection = array(
                array('selection'=>$row['selection_1'], 'answer' => ($answer == 1) ? true : false),
                array('selection'=>$row['selection_2'], 'answer' => ($answer == 2) ? true : false),
                array('selection'=>$row['selection_3'], 'answer' => ($answer == 3) ? true : false),
                array('selection'=>$row['selection_4'], 'answer' => ($answer == 4) ? true : false),
            );
            //新增组织好的
            $row['selection'] = $selection;
            unset($row['selection_1']);
            unset($row['selection_2']);
            unset($row['selection_3']);
            unset($row['selection_4']);
            //unset($row['answer']);
            $result[] = $row;
        }
        return $result;
    }

    /**
     * 随机获取一个题目
     */
    public function getRandQuestion($subjectId, $useId)
    {
        $sql  = "SELECT * FROM question ";
        $sql .= "WHERE subject_id = {$subjectId} AND ques_id >= ((SELECT MAX(ques_id) FROM question)-(SELECT MIN(ques_id) FROM question)) * RAND() + ";
        $sql .= "(SELECT MIN(ques_id) FROM question) LIMIT 1 ";
        $query = $this->db->query($sql);
        $arrQues = $query->result_array();
        $result = $this->structSelection($arrQues);
        return $result;
    }

    /**
     * 获取未答对的题目 = 未回答过的 + 回答错误的
     */
    public function getUnCorrectQuestion($subjectId, $useId)
    {
        $sql  = "SELECT q.* from question q ";
        $sql .= "LEFT JOIN user_question_record uqr ON q.ques_id = uqr.ques_id ";
        $sql .= "WHERE q.subject_id = {$subjectId} AND (uqr.is_right = 0 OR uqr.is_right IS NULL)  LIMIT 1 ";
        $query = $this->db->query($sql);
        $arrQues = $query->result_array();
        $result = $this->structSelection($arrQues);
        return $result;
    }
    /**
     * 获取该用户从未回答过的题目
     */
    public function getUnviewedQuestion($subjectId, $useId)
    {
        $sql  = "SELECT q.* from question q ";
        $sql .= "LEFT JOIN user_question_record uqr ON q.ques_id = uqr.ques_id ";
        $sql .= "WHERE q.subject_id = {$subjectId} AND uqr.is_right IS NULL  LIMIT 1 ";
        $query = $this->db->query($sql);
        $arrQues = $query->result_array();
        $result = $this->structSelection($arrQues);
        return $result;
    }

    /**
     * 添加问题
     * @param $arrQuesData
     * @return mixed
     */
    public function addQuestion($arrQuesData)
    {
        $data = array(
            'subject_id' => $arrQuesData['subject_id'],
            'subject_name' => $arrQuesData['subject_name'],
            'subject_level_id' => $arrQuesData['subject_level_id'],
            'subject_level_name' => $arrQuesData['subject_level_name'],
            'ques_type' => $arrQuesData['ques_type'],
            'ques_title' => $arrQuesData['ques_title'],
            'selection_1' => $arrQuesData['selection_1'],
            'selection_2' => $arrQuesData['selection_2'],
            'selection_3' => $arrQuesData['selection_3'],
            'selection_4' => $arrQuesData['selection_4'],
            'answer' => $arrQuesData['answer'],
            'score' => $arrQuesData['score'],
        );
        $this->db->insert('question', $data);
        $affectedRows = $this->db->affected_rows();
        return $affectedRows;
    }


}