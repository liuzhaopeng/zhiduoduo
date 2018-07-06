/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50719
Source Host           : 127.0.0.1:3306
Source Database       : zhiduoduo

Target Server Type    : MYSQL
Target Server Version : 50719
File Encoding         : 65001

Date: 2018-06-30 13:22:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `question`
-- ----------------------------
DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `ques_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '题目id，主键',
  `subject_id` int(11) NOT NULL COMMENT '学科id',
  `subject_name` char(50) NOT NULL DEFAULT '' COMMENT '科目名称',
  `subject_level_id` int(11) NOT NULL COMMENT '题目难度等级，难度等级是跟着学科定的',
  `subject_level_name` char(50) NOT NULL DEFAULT '' COMMENT '科目难度名称',
  `ques_type` char(20) NOT NULL DEFAULT '1' COMMENT '题目类型：1单选题，2多选题',
  `ques_title` varchar(500) NOT NULL DEFAULT '' COMMENT '题目标题',
  `selection_1` varchar(200) NOT NULL,
  `selection_2` varchar(200) NOT NULL,
  `selection_3` varchar(200) NOT NULL,
  `selection_4` varchar(200) NOT NULL,
  `answer` char(50) NOT NULL DEFAULT '' COMMENT '正确答案，多选时用逗号分隔',
  `score` int(11) DEFAULT '0' COMMENT '题目对应的分数',
  PRIMARY KEY (`ques_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of question
-- ----------------------------
INSERT INTO `question` VALUES ('1', '3', '测试类型', '1', '难度一', '1', '第一题', 'aa', 'bb', 'cc', 'dd', '2', '1');
INSERT INTO `question` VALUES ('2', '3', '测试类型', '1', '难度一', '1', '第二题', '1', '2', '3', '4', '3', '1');
INSERT INTO `question` VALUES ('3', '3', '测试类型', '1', '难度一', '1', '第三题', 'a', 'b', 'c', 'd', '2', '1');
INSERT INTO `question` VALUES ('4', '3', '测试类型', '1', '难度一', '1', '第四题', 'a', 'b', 'c', 'd', '1', '1');
INSERT INTO `question` VALUES ('5', '3', '测试类型', '1', '难度一', '1', '第五题', 'd', 'f', 'g', 'h', '2', '1');

-- ----------------------------
-- Table structure for `subject_level`
-- ----------------------------
DROP TABLE IF EXISTS `subject_level`;
CREATE TABLE `subject_level` (
  `subject_level_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_level_name` char(50) NOT NULL DEFAULT '' COMMENT '科目等级名称',
  `subject_id` int(11) NOT NULL COMMENT '科目id，冗余字段',
  `subject_name` char(50) NOT NULL DEFAULT '' COMMENT '科目名称，冗余字段',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已删除',
  PRIMARY KEY (`subject_level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of subject_level
-- ----------------------------

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------

-- ----------------------------
-- Table structure for `user_question_record`
-- ----------------------------
DROP TABLE IF EXISTS `user_question_record`;
CREATE TABLE `user_question_record` (
  `record_id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL DEFAULT '0' COMMENT '本局的id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `ques_id` int(11) NOT NULL COMMENT '题目id',
  `is_right` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0表示没有答对1表示答对',
  `score` tinyint(4) NOT NULL DEFAULT '0' COMMENT '本题得分',
  `add_time` int(11) NOT NULL DEFAULT '0',
  `add_time_format` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '答题时间点',
  PRIMARY KEY (`record_id`),
  UNIQUE KEY `index_unique` (`round_id`,`user_id`,`ques_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='用户答题记录表';

-- ----------------------------
-- Records of user_question_record
-- ----------------------------
INSERT INTO `user_question_record` VALUES ('21', '1', '1', '1', '1', '1', '1530244974', '2018-06-29 12:02:54');
INSERT INTO `user_question_record` VALUES ('23', '1', '1', '2', '1', '1', '1530252046', '2018-06-29 14:00:46');
INSERT INTO `user_question_record` VALUES ('24', '1', '1', '3', '1', '1', '1530252103', '2018-06-29 14:01:43');
INSERT INTO `user_question_record` VALUES ('25', '1', '1', '4', '1', '1', '1530252111', '2018-06-29 14:01:51');
INSERT INTO `user_question_record` VALUES ('26', '1', '1', '5', '1', '1', '1530252119', '2018-06-29 14:01:59');

-- ----------------------------
-- Table structure for `user_subject_level`
-- ----------------------------
DROP TABLE IF EXISTS `user_subject_level`;
CREATE TABLE `user_subject_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `subject_level_id` int(11) NOT NULL COMMENT '用户当前在该科目的难度等级id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户和科目难度的关系表，用来表示用户可以所处的科目难度水平，可以用来推荐对应难度系数的题目，是可以不断升级的';

-- ----------------------------
-- Records of user_subject_level
-- ----------------------------
