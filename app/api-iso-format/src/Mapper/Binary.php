<?php
namespace ISO8583\Mapper;

class Binary extends AbstractMapper
{
	public function pack($message)
	{
        // $packed = bin2hex($message);
        $packed = $message;

        if ($this->getVariableLength() > 0) {
            $packed = sprintf('%0' . $this->getVariableLength() . 'd', strlen($packed)) . $packed;
        }

        return $packed;
	}

	public function unpack(&$message)
	{
        if ($this->getVariableLength() > 0) {
            // $length = (int)hex2bin(substr($message, 0, $this->getVariableLength() * 2));
            $length = (int)hex2bin(substr($message, 0, $this->getVariableLength()));
        } else {
            $length = $this->getLength();
        }

        // $parsed = substr($message, 0, $length * 2);
        // $message = substr($message, $length * 2);
        $parsed = substr($message, 0, $length);
        $message = substr($message, $length);
        
        return $parsed;
	}
}
