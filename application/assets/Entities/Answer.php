<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/29/14
 * Time: 2:16 PM
 */

namespace application\assets\Entities;


class Answer {

    private $id;
    private $user;
    private $question;
    private $title;
    private $answer;
    private $rating;
    private $modified_date;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param \DateTime $modified_date
     */
    public function setModifiedDate(\DateTime $modified_date)
    {
        $this->modified_date = $modified_date;
    }

    /**
     * @return mixed
     */
    public function getModifiedDate()
    {
        return $this->modified_date;
    }

    /**
     * @param Question $question
     */
    public function setQuestion(Question $question)
    {
        $this->question = $question;
    }

    /**
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getDateTwig(){
        return $this->modified_date->format('Y/m/d G:i:s');
    }
    public function ratingUp(){
        $rating = explode('|',$this->rating);
        return $rating[0];
    }
    public function ratingDown(){
        $rating = explode('|',$this->rating);
        return $rating[1];
    }

} 