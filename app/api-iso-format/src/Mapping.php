<?php
namespace ISO8583;

class Mapping 
{
	protected $mapping = [];

	public function __construct($mapping = null)
	{
        $mapping = $mapping === null ? implode(DIRECTORY_SEPARATOR, [__DIR__, 'Mapping.json']) : $mapping;
        if (!file_exists($mapping)) {
            throw new \Exception('Unknown mapping file: ' . $mapping);
        }

		$mappingJSON    = json_decode(file_get_contents($mapping), true);
        if ($mappingJSON === null) {
            throw new \Exception('Bad JSON mapping file: ' . $mapping);
        }

		foreach($mappingJSON as $field => $data) 
		{
			$this->setFieldData($field, $data);
		}
	}

	public function getFieldData($field)
	{
		 if (!isset($this->mapping[$field])) 
		 {
			 throw new \Exception('No field ' . $field . ' in mapping');
		 }

		 return $this->mapping[$field];
	}

	public function setFieldData($field, $data)
	{
		$this->mapping[$field] = $data;
	}
}
