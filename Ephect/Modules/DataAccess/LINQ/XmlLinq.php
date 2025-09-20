<?php
	/**
	 * User: Milan Gallas
	 * Date: 23.10.2016
	 * Time: 0:03
	 */

	namespace Ephect\Modules\DataAccess\LINQ;


	class XmlLinq extends  Linq
	{

		/**
		 * load xml string and transform to array
		 * @param string|array $object
		 * @return $this
		 */
		public function from(string|array $object): self
		{
			$this->array = $this->parse($object);
			return $this;
		}

        /**
         * return only elements that combine both fields
         *
         * @param string|array $array
         * @return $this
         */
		public function innerJoin(string|array $array): self
		{
			$this->secondaryArray = $this->parse($array);
			$this->joinType = "inner";
			return $this;
		}

		/**
		 * the first collection will list all entries, even those which do not connect with other collections
		 *
		 * @param $array
		 * @return $this
		 */
		public function leftJoin($array): self
		{
			$this->secondaryArray = $this->parse($array);
			$this->joinType = "left";
			return $this;
		}

		/**
		 * @param $source
		 * @return mixed
		 */
		protected function parse($source): mixed
		{
			if($source instanceof \SimpleXMLElement){
				return $this->simpleXmlElement2Array($source);
			}
			return $this->xml2array($source);
		}

        /**
         * @param string $xmlString
         * @return mixed
         */
		protected function xml2array(string $xmlString): mixed
		{
			$xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			return $array[array_keys($array)[0]];
		}

		/**
		 * parse SimleXmlElement object to array
		 * @param $xmlElement
		 * @return mixed
		 */
		protected function simpleXmlElement2Array($xmlElement): mixed
        {
			$array = (array)json_decode(json_encode($xmlElement));
			$key = array_keys($array)[0];
			return $array[$key];
		}

	}