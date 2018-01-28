<?php
namespace AppBundle\Exception;

class QuestionException extends \Exception
{
    /**
     * 
     * @param string $message
     * @param int $code
     */
    public function __construct($message=NULL, $code=0)
    {
        parent::__construct($message, $code);
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see Exception::__toString()
     */
    public function __toString()    
    {    
        return $this->message;    
    }
}